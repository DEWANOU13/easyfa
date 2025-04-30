<?php

namespace App\Http\Controllers\statistique;

use Exception;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\StockHistories;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Exports\ReleverSortieExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
class releveSortieController extends Controller
{
    public function releveSortie()
    {
        $this->authorize('releve-sortie-produit');
        try {
            $categorie = CategorieProduit::all();

            // $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();

            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
            //$listeAgence = Agence::all();
            return view('page.statistique.relever.relever_sortie',
                [
                    'categorie' => $categorie,
                    'listeAgence' => $listeAgence
                ]

            );
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function releverSortiereq(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'categorie' => 'required',
            'agence' => 'required',
        ]);


       $agenceId = $request->input('agence');
       $categorieId = $request->input('categorie');
       $dateDebut = $request->input('dateDebut');
       $dateFin = $request->input('dateFin');

       DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

/*        $query = DB::table('produits')
    ->selectRaw("
        produits.Reference,
        produits.Designation,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 2 THEN stock_histories.Quantite ELSE 0 END), 0) AS Lundi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 3 THEN stock_histories.Quantite ELSE 0 END), 0) AS Mardi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 4 THEN stock_histories.Quantite ELSE 0 END), 0) AS Mercredi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 5 THEN stock_histories.Quantite ELSE 0 END), 0) AS Jeudi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 6 THEN stock_histories.Quantite ELSE 0 END), 0) AS Vendredi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 7 THEN stock_histories.Quantite ELSE 0 END), 0) AS Samedi,
        COALESCE(SUM(CASE WHEN DAYOFWEEK(stock_histories.Date) = 1 THEN stock_histories.Quantite ELSE 0 END), 0) AS Dimanche,
        COALESCE(SUM(stock_histories.Quantite), 0) AS SortieHebdo,
        COALESCE(ROUND(AVG(stock_histories.Quantite), 2), 0) AS SortieMoyenneParJour
    ")
    ->leftJoin('stock_histories', function ($join) use ($dateDebut, $dateFin, $agenceId) {
        $join->on('stock_histories.Id_Produit', '=', 'produits.id')
             ->whereIn('stock_histories.type_operation', ['SORTIE', 'FACTURE_V','FACTURE_A'])
             ->whereBetween('stock_histories.Date', [$dateDebut, $dateFin]);
        if ($agenceId) {
            $join->where('stock_histories.agence_id', $agenceId);
        }
    })
    ->when($categorieId && $categorieId !== 'Tous', function ($q) use ($categorieId) {
        // Appliquer le filtre de catégorie uniquement si `categorieId` n'est pas 'Tous'
        return $q->where('produits.Id_Categorie', $categorieId);
    })
    ->groupBy('produits.id', 'produits.Reference', 'produits.Designation')
    ->get(); */
    $query = DB::table('produits')
    ->selectRaw("
        produits.Reference,
        produits.Designation,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 2 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Lundi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 3 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Mardi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 4 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Mercredi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 5 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Jeudi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 6 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Vendredi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 7 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Samedi,
        COALESCE(SUM(CASE
            WHEN DAYOFWEEK(stock_histories.Date) = 1 THEN
                CASE
                    WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
                    WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
                    ELSE 0
                END
            ELSE 0 END), 0) AS Dimanche,
        COALESCE(SUM(CASE
            WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
            WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
            ELSE 0 END), 0) AS SortieHebdo,
        COALESCE(ROUND(AVG(CASE
            WHEN stock_histories.type_operation IN ('SORTIE', 'FACTURE_V') THEN stock_histories.Quantite
            WHEN stock_histories.type_operation IN ('FACTURE_A', 'FACTURE_INVALIDEE') THEN -stock_histories.Quantite
            ELSE 0 END), 2), 0) AS SortieMoyenneParJour
    ")
    ->leftJoin('stock_histories', function ($join) use ($dateDebut, $dateFin, $agenceId) {
        $join->on('stock_histories.Id_Produit', '=', 'produits.id')
             ->whereBetween('stock_histories.Date', [$dateDebut, $dateFin]);
        if ($agenceId !== 'Tous') {
            $join->where('stock_histories.agence_id', $agenceId);
        }
    })
    ->when($categorieId && $categorieId !== 'Tous', function ($q) use ($categorieId) {
        // Appliquer le filtre de catégorie uniquement si `categorieId` n'est pas 'Tous'
        return $q->where('produits.Id_Categorie', $categorieId);
    })
    ->groupBy('produits.id', 'produits.Reference', 'produits.Designation');



    $results = $query->get();

    if ($agenceId !== 'Tous') {
        $infoAgence = Agence::where('id', $agenceId)->first();
    } else {
        $infoAgence = Agence::where('id', 0)->first();
    }

    if ($categorieId !== 'Tous') {
        $infoCategorie = CategorieProduit::where('id', $categorieId)->first();
    } else {
        $infoCategorie = CategorieProduit::where('id', 0)->first();
    }
    $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();


        return response()->json([
            'listeReleverSortie' => $results,
            'imageEntetePied' => $imageEntetePied,
            'infoAgence' => $infoAgence,
            'infoCategorie' => $infoCategorie,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);




    // Retourner les données au format JSON
    //return response()->json(['data' => $results]);

    }
    public function export_excel_relever_sortie(Request $request)
    {
        try {
            $data = $request->validate([
                'tableReleverSortieData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoCategorie' => '',
                'infoAgence' => '',
            ]);
            //dd($data);
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new ReleverSortieExport($data, $texteEntetePied);

            $fileName = 'Relever_sortie' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export_relever_sortie_pdf(Request $request)
    {
        // try {
            $data = $request->validate([
                'tableReleverSortieData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoCategorie' => '',
                'infoAgence' => '',
            ]);



        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.statistique.relever.imprimer.relever_sortiePdf', [
            'data' => $data,
            'dateDebut' => $data['dateDebut'],
            'dateFin' => $data['dateFin'],
            'infoCategorie' => $data['infoCategorie'],
            'infoAgence' => $data['infoAgence'],

            'imageEntetePied' => $imageEntetePied,

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Liste_reglement_periode', ["Attachment" => false]);

        /*  } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }
}
