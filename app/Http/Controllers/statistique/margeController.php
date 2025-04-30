<?php

namespace App\Http\Controllers\statistique;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Lignefacture;
use Illuminate\Http\Request;
use App\Models\StockHistories;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Exports\MargeParJourExport;
use App\Exports\MargeParMoisExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MargeParClientExport;
use App\Exports\MargeParProduitExport;
use App\Models\Agence;

class margeController extends Controller
{
    //
    public function listemarge()
    {
        $this->authorize('statistique-marge');

        try {
            $client = Client::all();
            $produit = Produit::all();
            $categorie = CategorieProduit::all();
            $fournisseur = Fournisseur::all();
            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();


            return view(
                'page.statistique.marge.marge',
                [
                    'client' => $client,
                    'produit' => $produit,
                    'categorie' => $categorie,
                    'fournisseur' => $fournisseur,
                    'listeAgence' => $listeAgence


                ]

            );
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function margeParProduit(Request $request)
    {

        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
       // $dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $produit = $data['produit'];
        $agence = $data['agence'];

        $listeVente = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->select(
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('SUM(stock_histories.Quantite) as Quantite'),
                DB::raw('SUM(stock_histories.Prix_vente) as Prix_vente'),
                DB::raw('SUM(stock_histories.Prix_achat) as Prix_achat')
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->where('stock_histories.type_operation', 'FACTURE_V')
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle');

        if ($produit !== 'Tous') {
            $listeVente->where('stock_histories.Id_Produit', $produit);
        }
        if ($agence !== 'Toutes') {
            $listeVente->where('stock_histories.agence_id', $agence);
        }

        $listeVente = $listeVente->get()->groupBy('Categorie_produit');
        $infoProduit = Produit::where('id', $produit)->first();
        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoProduit' => $infoProduit,
            'infoAgence' => $infoAgence
        ]);
    }
    public function export_excel_marge_par_produit(Request $request)
    {
        $data = $request->validate([
            'tableMargeParProduitData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoProduit' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new MargeParProduitExport($data, $texteEntetePied);

            $fileName = 'Marge_par_produit' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
    }
    public function export_marge_par_produit_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableMargeParProduitData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoProduit' => '',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.marge.document.marge_par_produitPDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoProduit' => $data['infoProduit'],
                'infoAgence' => $data['infoAgence'],

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Marge_par_produit'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function margeParJour(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $agence = $data['agence'];
        //$dateFinH = Carbon::parse($data['dateFin'])->endOfDay();

        $listeVente = StockHistories::select(
                DB::raw('DATE(stock_histories.created_at) as Date'),
                DB::raw('SUM(stock_histories.Quantite) as Quantite'),
                DB::raw('SUM(stock_histories.Prix_vente) as Prix_vente'),
                DB::raw('SUM(stock_histories.Prix_achat) as Prix_achat')
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->where('stock_histories.type_operation', 'FACTURE_V')
            ->groupBy(DB::raw('DATE(stock_histories.created_at)'));

            if ($agence !== 'Toutes') {
                $listeVente->where('stock_histories.agence_id', $agence);
            }
            $listeVente = $listeVente->get();

        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;


        //dd($listeVente);

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoAgence' => $infoAgence
        ]);
    }
    public function export_excel_marge_par_jour(Request $request)
    {
        try {

        $data = $request->validate([
            'tableMargeParJourData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoAgence' => '',
        ]);


            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
            $export = new MargeParJourExport($data, $texteEntetePied);

            $fileName = 'Marge_par_jour' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function export_marge_par_jour_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableMargeParJourData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.marge.document.marge_par_jourPDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoAgence' => $data['infoAgence'],


            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Marge_par_jour'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function margeParMois(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date_format:Y-m',
            'dateFin' => 'required|date_format:Y-m|after_or_equal:dateDebut',
            'agence' => 'required',
        ]);

        $dateDebut = Carbon::parse($data['dateDebut'] . '-01')->startOfMonth();
        $dateFin = Carbon::parse($data['dateFin'] . '-01')->endOfMonth();
        $agence = $data['agence'];

        $listeVente = StockHistories::select(
                DB::raw('DATE_FORMAT(stock_histories.created_at, "%Y-%m") as Date'),
                DB::raw('SUM(stock_histories.Quantite) as Quantite'),
                DB::raw('SUM(stock_histories.Prix_vente) as Prix_vente'),
                DB::raw('SUM(stock_histories.Prix_achat) as Prix_achat')
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->where('stock_histories.type_operation', 'FACTURE_V')
            ->groupBy(DB::raw('DATE_FORMAT(stock_histories.created_at, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(stock_histories.created_at, "%Y-%m")'));

            if ($agence !== 'Toutes') {
                $listeVente->where('stock_histories.agence_id', $agence);
            }
            $listeVente = $listeVente->get();

        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $data['dateDebut'],
            'dateFin' => $data['dateFin'],
            'infoAgence' => $infoAgence
        ]);
    }
    public function export_excel_marge_par_mois(Request $request)
    {
        try {
        //dd($request);
        $data = $request->validate([
            'tableMargeParMoisData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoAgence' => '',
        ]);

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new MargeParMoisExport($data, $texteEntetePied);

            $fileName = 'Marge_par_mois' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function export_marge_par_mois_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableMargeParMoisData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.marge.document.marge_par_MoisPDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoAgence' => $data['infoAgence'],


            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Marge_par_mois'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function margeParClient(Request $request)
    {
       // dd($request);
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'client' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        //$dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $client = $data['client'];
        $agence = $data['agence'];


        $listeVente = StockHistories::join('factures', 'stock_histories.Justificatif', '=', 'factures.Reference_facture')
        ->join('clients', 'factures.client_id', '=', 'clients.id')
        ->select(
            'clients.id',
            'clients.Denomination_sociale',
            DB::raw('SUM(stock_histories.Prix_vente) as Prix_vente'),
            DB::raw('SUM(stock_histories.Prix_achat) as Prix_achat')
        )
        ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
        ->where('stock_histories.type_operation', 'FACTURE_V')
        ->groupBy(
            'clients.id',               // Group by sur clients.id
            'clients.Denomination_sociale' // Ajout de Denomination_sociale au GROUP BY
        );

            if ($client !== 'Tous') {
                $listeVente->where('clients.id', $client);
            }
            if ($agence !== 'Toutes') {
                $listeVente->where('stock_histories.agence_id', $agence);
            }


            $listeVente = $listeVente->get();
            $infoClient = Produit::where('id', $client)->first();
            $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;




        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoClient' => $infoClient,
            'infoAgence' => $infoAgence
        ]);
    }
    public function export_excel_marge_par_client(Request $request)
    {
        try {
        //dd($request);
        $data = $request->validate([
            'tableMargeParClientData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoClient' => '',
            'infoAgence' => '',
        ]);

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new MargeParClientExport($data, $texteEntetePied);

            $fileName = 'Marge_par_client' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function export_marge_par_client_pdf(Request $request)
    {

        try {
            $data = $request->validate([
                'tableMargeParClientData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoClient' => '',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.marge.document.marge_par_clientPDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoClient' => $data['infoClient'],
                'infoAgence' => $data['infoAgence'],


            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Marge_par_client'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

       }
       catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }




}

