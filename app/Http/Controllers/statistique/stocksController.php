<?php

namespace App\Http\Controllers\statistique;

use App\Exports\StatistiqueFIcheStockConsolideExport;
use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Exports\StatistiqueStockMagasinExport;
use App\Exports\StatistiqueStockConsolideExport;
use App\Models\Categorie;
use App\Models\CategorieProduit;
use App\Models\StockHistories;
use League\Csv\Query\Row;

class stocksController extends Controller
{

    public function listestock()
    {
        $this->authorize('statistique-stock');

        $site_id = session()->get('site_id');

        if ($site_id == 1) {
            $magasin = Magasin::all();
        } else {
            $magasin = Magasin::where('agence_id', $site_id)->get();
        }

        $produit = Produit::all();
        $categorie = CategorieProduit::all();
        return view('page.statistique.stock.stock',
            [
                'magasin' => $magasin,
                'produit' => $produit,
                'categorie' => $categorie,

            ]

        );
    }

    public function statistiqueStockMagasin(Request $request)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'magasin' => 'required',
            'categorie' => 'required',
            'produit_' => 'required',
        ]);

        $dateDebut = Carbon::parse($data['dateDebut'])->format('Y-m-d H:m:s');
        $dateFin = Carbon::parse($data['dateFin'])->format('Y-m-d H:m:s');

        $dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $magasin = $data['magasin'];
        $produit = $data['produit_'];
        $categorie = $data['categorie'];

        // dd($magasin, $categorie, $produit);

        $query = DB::table('stock_histories')
            ->join('produits', 'stock_histories.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stock_histories.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
            ->select(
                'produits.id as produit_id',
                'produits.Reference as reference',
                'produits.Designation as designation',
                'categorie_produits.Libelle as categorie',
                'magasins.id as magasin_id',
                'magasins.NomMagasin as nom_magasin',
                DB::raw("MAX(unite_comptages.Libelle) as unite"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'ENTREE' THEN stock_histories.Quantite ELSE 0 END) as total_entree"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'SORTIE' THEN stock_histories.Quantite ELSE 0 END) as total_sortie"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'TRANSFERT' THEN stock_histories.Quantite ELSE 0 END) as total_transfert"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'FACTURE_V' THEN stock_histories.Quantite ELSE 0 END) as total_facture_fv"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'FACTURE_A' THEN stock_histories.Quantite ELSE 0 END) as total_facture_fa"),
                DB::raw("SUM(CASE WHEN stock_histories.type_operation = 'FACTURE_INVALIDEE' THEN stock_histories.Quantite ELSE 0 END) as total_facture_in"),
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'magasins.id');


        if ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes') {
            $query->where('stock_histories.Id_Magasin', $magasin)->where('stock_histories.Id_Produit', $produit)->where('produits.Id_Categorie', $categorie);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes') {
            $query->where('stock_histories.Id_Produit', '=',  $produit)->where('produits.Id_Categorie', '=', $categorie);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $query->where('stock_histories.Id_Magasin', $magasin)->where('produits.Id_Categorie', $categorie);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes') {
            $query->where('stock_histories.Id_Magasin', $magasin)->where('stock_histories.Id_Produit', $produit);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes') {
            $query->where('stock_histories.Id_Magasin', $magasin);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes') {
            $query->where('stock_histories.Id_Produit', $produit);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } elseif ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $query->where('produits.Id_Categorie', $categorie);
            $infoMagasin = Magasin::where('id', $magasin)->first();
            $infoProduit = Produit::where('id', $produit)->first();
            $infoCategoire = CategorieProduit::where('id', $categorie)->first();
        } else {
            $infoMagasin = Magasin::where('id', 0)->first();
            $infoProduit = Produit::where('id', 0)->first();
            $infoCategoire = CategorieProduit::where('id', 0)->first();
        }
        $resultats = $query->get();
        /* foreach ($resultats as $vente) {
            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit',  $vente->produit_id)
                ->where('Id_Magasin', $vente->magasin_id)
                ->where('Date', '<=', $date_veille_debut)
                ->orderBy('Date', 'asc') // On parcourt les opérations dans l'ordre chronologique
                ->get();


            // Initialisation du solde
            $solde_initial = 0;

            // Parcourir les opérations pour calculer le solde
            foreach ($operations_anterieures as $operation) {
                if ($operation->operation === 'IMPORTATION_STOCK') {
                    // Réinitialiser le solde à la quantité de l'importation de stock
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'INVENTAIRE') {
                    // Réinitialiser le solde à la quantité spécifiée par l'inventaire
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'ENTREE') {
                    // Ajouter la quantité pour les entrées
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->operation === 'SORTIE') {
                    // Soustraire la quantité pour les sorties
                    $solde_initial -= $operation->Quantite;
                }
            }
            $vente->SoldeInitial = $solde_initial;
            //dd($vente->SoldeInitial);
        } */


        $listeStock = $resultats->groupBy('categorie');
        //dd($listeStock);


        return response()->json([
            'listeStock' => $listeStock,
            'infoMagasin' => $infoMagasin,
            'dateDebut' => $dateDebut,
            'produit' => $infoProduit,
            'categorie' => $infoCategoire,
            'dateFin' => $dateFin
        ]);
    }
    public function statistiqueStockConsolide(Request $request)
    {
        // dd($request);

        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'magasin' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $magasin = $data['magasin'];
        //$dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $produit = $data['produit'];

        $listeStock = Stock::join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
            ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->select(
                'produits.Reference',
                'produits.Designation',
                'magasins.NomMagasin',
                'categorie_produits.Libelle as Categorie_produit',
                'unite_comptages.Libelle as Unite',
                DB::raw('SUM(stocks.Qte_stockee) as Qte_stockee'),
                DB::raw('AVG(stocks.Prix_Achat_Net) as Prix_Achat_Net')
            )
            ->whereBetween('stocks.created_at', [$dateDebut, $dateFin])
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'unite_comptages.Libelle', 'magasins.NomMagasin');

        if ($produit !== 'Tous') {
            $listeStock->where('stocks.Id_Produit', $produit);
            $infoProduit = Produit::find($produit);
        } else {
            $infoProduit = 'Tous';
        }
        if ($magasin !== 'Tous') {
            $listeStock->where('stocks.Id_Magasin', $magasin);
            $infoMagasin = Magasin::find($magasin);
        } else {
            $infoMagasin = 'Tous';
        }

        $listeStock = $listeStock->get()->groupBy('Categorie_produit');
        // dd($listeStock);

        return response()->json([
            'listeStock' => $listeStock,
            'infoMagasin' => $infoMagasin,
            'infoProduit' => $infoProduit,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }

    public function ficheStockConsolide(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'magasin' => 'required',
            'produit' => 'required',
        ]);


        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        // $dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $magasin = $data['magasin'];
        $produit = $data['produit'];

        $mouvementStock = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
            ->join('magasins', 'magasins.id', '=', 'stock_histories.Id_Magasin')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            // ->select('stock_histories.*', 'magasins.NomMagasin')
            // ->where('stock_histories.Id_Produit', $produit);
            ->select(
                'produits.Reference',
                'stock_histories.type_operation',
                'produits.Designation',
                'magasins.NomMagasin',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('MAX(stock_histories.Date) as Date'),
                DB::raw('MAX(stock_histories.Motif) as Motif'),
                DB::raw('MAX(stock_histories.Justificatif) as Justificatif'),
                DB::raw('MAX(stock_histories.operation) as operation'),
                DB::raw('MAX(stock_histories.type_operation) as type_operation'),
                DB::raw('MAX(stock_histories.Quantite) as Quantite'),
                // DB::raw('AVG(stocks.Prix_Achat_Net) as Prix_Achat_Net')
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stock_histories.type_operation', 'magasins.NomMagasin');


        if ($magasin !== 'Tous') {
            $mouvementStock->where('stock_histories.Id_Magasin', $magasin);
            $infoMagasin = Magasin::find($magasin);
        } else {
            $infoMagasin = 'Tous';
        }
        if ($produit !== 'Tous') {
            $mouvementStock->where('stock_histories.Id_Produit', $produit);
            $infoProduit = Produit::find($produit);
        } else {
            $infoProduit = 'Tous';
        }

        $mouvementStock = $mouvementStock->get();



        //dd($mouvementStock);



        return response()->json([
            'mouvementStock' => $mouvementStock,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoMagasin' => $infoMagasin,
            'infoProduit' => $infoProduit
        ]);
    }

    public function export_excel_stat_stock_magasin(Request $request)
    {
        try {
            $data = $request->validate([
                'tableStatMagasinData' => 'required|array',
                'infoMagasin' => '',
                'dateDebut' => '',
                'dateFin' => '',
            ]);
            // dd($data);
            $user = Auth::user()->name;
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new StatistiqueStockMagasinExport($data, $user, $texteEntetePied);

            $fileName = 'statistique_stock_magasin' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function export_stat_stock_magasin_Pdf(Request $request)
    {
        // dd($request);
        // try {
        $data = $request->validate([
            'tableStatMagasinData' => 'required|array',
            'infoMagasin' => '',
            'dateDebut' => '',
            // 'dateFin' => '',
        ]);

        Carbon::setLocale('fr');

        $NomMagasin = $data['infoMagasin']['NomMagasin'] ?? '';

        $dateImpression = Carbon::now()->translatedFormat('d F Y');
        $user = Auth::user()->name;

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $html = view('page.statistique.stock.document.stat_stock_magasinPDF', [
            'data' => $data,
            'dateDebut' => $data['dateDebut'],
            // 'dateFin' => $data['dateFin'],

            'imageEntetePied' => $imageEntetePied,
            'NomMagasin' => $NomMagasin,
            'dateImpression' => $dateImpression,
            'user' => $user

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        return $dompdf->stream('statistique_global_facture_', ["Attachment" => false]);
        // } catch (Exception $e) {
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function export_excel_stat_stock_consolide(Request $request)
    {
        // dd($request);
        try {

            $data = $request->validate([
                'tableStatStockConsolideData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoMagasin' => '',
                'infoProduit' => '',
            ]);
            $user = Auth::user()->name;
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new StatistiqueStockConsolideExport($data, $user, $texteEntetePied);

            $fileName = 'statistique_stock_consolide' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export_stat_stock_consolide_pdf(Request $request)
    {
        try {
            // dd($request);
            $data = $request->validate([
                'tableStatStockConsolideData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoProduit' => '',
                'infoMagasin' => '',
            ]);


            Carbon::setLocale('fr');


            $dateDebut = Carbon::parse($data['dateDebut'])->translatedFormat('d F Y');
            $dateFin = Carbon::parse($data['dateFin'])->translatedFormat('d F Y');

            $NomMagasin = $data['infoMagasin']['NomMagasin'] ?? 'Tous';
            $NomProduit = $data['infoProduit']['Designation'] ?? 'Tous';

            $dateImpression = Carbon::now()->translatedFormat('d F Y');
            $user = Auth::user()->name;

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.stock.document.stat_stock_consolidePDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'NomMagasin' => $NomMagasin,
                'NomProduit' => $NomProduit,
                'dateImpression' => $dateImpression,
                'user' => $user

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('statistique_global_facture_', ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function export_excel_fiche_stock_consolide(Request $request)
    {
        try {

            $data = $request->validate([
                'tableFicheStockConsolideData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoProduit' => '',
                'infoMagasin' => '',
            ]);
            $user = Auth::user()->name;

            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new StatistiqueFIcheStockConsolideExport($data, $user, $texteEntetePied);

            $fileName = 'fiche_stock_consolide' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export_fiche_stock_consolide_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableFicheStockConsolideData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoProduit' => '',
                'infoMagasin' => '',
            ]);

            // dd($data);
            Carbon::setLocale('fr');

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.stock.document.fiche_stock_consolidePDF', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoProduit' => $data['infoProduit'],
                'infoMagasin' => $data['infoMagasin'],

            ])->render();

            // Options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));

            // Initialiser Dompdf
            $dompdf = new Dompdf($options);

            // Charger le HTML
            $dompdf->loadHtml($html);

            // (Optionnel) Configuration du format de papier et de l'orientation
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Télécharger le PDF
            return $dompdf->stream('fiche_stock_consolide', ["Attachment" => false]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }


    public function getProductsByCategory($categoryId)
    {
        // dd($categoryId);

        if ($categoryId !== 'Toutes') {

            // $categorie_formate = explode('-', $categoryId, 3);
            // $id_categorie = trim($categorie_formate[1]);
            // $id_magasin = trim($categorie_formate[0]);
            // dd('ici(----');
            // Filtrer les produits en fonction de la catégorie
            $products = Produit::where('Id_Categorie', $categoryId)->get();

            // $products = DB::table('produits')
            //     ->join('stocks', 'stocks.Id_Produit', 'produits.id')
            //     ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            //     ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            //     ->select(
            //         'stocks.Id_Produit',
            //         DB::raw('MAX(produits.Reference) as Reference'),
            //         DB::raw('MAX(produits.Designation) as Designation'),
            //     )
            //     ->where('agences.id', '=', getIdAgenceByUser())
            //     ->where('produits.Id_Categorie', '=', $categoryId)
            //     // ->where('stocks.Id_Magasin', '=', $id_magasin)
            //     ->groupBy('stocks.Id_Produit')
            //     ->get();
        } else {
            $products = [];
        }

        return response()->json($products);
    }
}
