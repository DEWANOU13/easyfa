<?php

namespace App\Http\Controllers\statistique;

use App\Exports\AchatCumuleParAgenceExport;
use App\Exports\AchatCumuleParCategorieExport;
use App\Exports\AchatCumuleParFournisseurExport;
use App\Exports\AchatCumuleParMoisExport;
use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\CategorieProduit;
use App\Models\Fournisseur;
use App\Models\Image;
use App\Models\Produit;
use App\Models\Stock;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class statistiqueController extends Controller
{
    // En fonction du type de statistique; vous mettez ceci en debut de vos fonctions

    // $this->authorize('statistique-achat');
    // $this->authorize('statistique-vente');
    // $this->authorize('statistique-stock');
    // $this->authorize('statistique-marge');

    public function listestatistiques(Request $request)
    {
        $this->authorize('statistique-achat');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DB::table('stocks');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Achats cumulés par mois
        $achat_cumule_par_mois = $query->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(Prix_Achat_Net) as Prix_Achat_Net'))
            ->groupBy('year', 'month')
            ->get();

        $categorie = CategorieProduit::all();
        $fournisseur = Fournisseur::all();
        // $agences = Agence::all();
        $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
        return view('page.statistique.achat.statistique',
            [
                'categorie' => $categorie,
                'fournisseur' => $fournisseur,
                'agences' => $agences,
                'achat_cumule_par_mois'=>  $achat_cumule_par_mois,
                // 'AchatsParCategorie'=> $AchatsParCategorie,
            ]);
    }

    public function achatCumuleParMois(Request $request){
        $this->authorize('statistique-achat');
        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');


        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


        $achat_cumule_par_mois = DB::table('entrer_produits')
        // ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(Prix_Achat_Net) as Prix_Achat_Net')
        )
        // ->whereBetween('entree_produits.Date_Produit', [$date_debut_periode, $date_fin_periode])
        ->groupBy('year', 'month')
        // ->orderBy( 'month', 'desc')
        ->get();

        // dd($achat_cumule_par_mois);
        $categorie = CategorieProduit::all();
        $fournisseur = Fournisseur::all();
        $produit = Produit::all();
        return view('page.statistique.achat.statistique', [
            'achat_cumule_par_mois' => $achat_cumule_par_mois,
            'categorie' => $categorie,
                'fournisseur' => $fournisseur,
                'produit'=> $produit,
                'filterMois' => true
        ]);



    }

    public function achatCumuleParCategorie(Request $request)
    {
        $this->authorize('statistique-achat');
        // Récupération des dates et de la catégorie de la requête
        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $categorie = $request->input('categorie');

        // Conversion des dates au format approprié
        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        // Requête pour récupérer les achats cumulés par catégorie et par mois
        $achat_cumule_par_categorie = DB::table('entrer_produits')
            ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select(
                'categorie_produits.Libelle as categorie',
                DB::raw('MAX(entrer_produits.created_at) as created_at'),
                // DB::raw('MONTH(entrer_produits.created_at) as month'),
                DB::raw('SUM(entrer_produits.Prix_Achat_Net) as Prix_Achat_Net')
            )
            // ->whereBetween('entree_produits.Date_Entree', [$date_debut_periode, $date_fin_periode])
            // ->whereBetween('entrer_produits.created_at', [$date_debut_periode, $date_fin_periode])
            ->where('categorie_produits.id', '=', $categorie)
            ->groupBy('categorie')
            // ->orderBy('year', 'month')
            ->get();

            // dd($achat_cumule_par_categorie);

        // Récupération des catégories, fournisseurs et produits
        $categories = CategorieProduit::all();
        $fournisseurs = Fournisseur::all();
        $agences = Agence::all();

        // Retourne la vue avec les données récupérées
        return view('page.statistique.achat.statistique', [
            'achat_cumule_par_categorie' => $achat_cumule_par_categorie,
            'categorie' => $categories,
            'fournisseur' => $fournisseurs,
            'agences' => $agences,
            'filterCategorie' => true
        ]);
    }

    public function achatCumuleParFournisseur(Request $request)
    {
        $this->authorize('statistique-achat');
        // Récupération des dates et de la catégorie de la requête
        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $fournisseur_id = $request->input('fournisseur');

        // Conversion des dates au format approprié
        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        // Requête pour récupérer les achats cumulés par catégorie et par mois
        $achat_cumule_par_fournisseur = DB::table('entrer_produits')
            ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
            ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select(
                // DB::raw('MONTH(entrer_produits.created_at) as month'),
                'entree_produits.Id_Fournisseur as Id_Fournisseur',
                DB::raw('MAX(fournisseurs.DenominationSociale) as DenominationSociale'),
                DB::raw('SUM(entrer_produits.Prix_Achat_Net) as Prix_Achat_Net')
            )
            ->where('fournisseurs.id', '=', $fournisseur_id)
            ->groupBy('Id_Fournisseur')
            // ->orderBy('year', 'month')
            ->get();

            // dd($achat_cumule_par_fournisseur);

        // Récupération des catégories, fournisseurs et produits
        $categories = CategorieProduit::all();
        $fournisseurs = Fournisseur::all();
        $agences = Agence::all();

        // Retourne la vue avec les données récupérées
        return view('page.statistique.achat.statistique', [
            'achat_cumule_par_fournisseur' => $achat_cumule_par_fournisseur,
            'categorie' => $categories,
            'fournisseur' => $fournisseurs,
            'agences' => $agences,
            'filterFournisseur' => true
        ]);
    }

    public function achatCumuleParAgence(Request $request)
    {
        $this->authorize('statistique-achat');
        // Récupération des dates et de la catégorie de la requête
        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $agence = $request->input('agence');

        // Conversion des dates au format approprié
        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        // Requête pour récupérer les achats cumulés par catégorie et par mois
        $achat_cumule_par_agence = DB::table('entrer_produits')
            ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
            ->select(
                'entree_produits.Id_Agence as Id_Agence',
                'entrer_produits.Id_Produit as Id_Produit',
                DB::raw('MAX(agences.NomAgence) as NomAgence'),
                DB::raw('MAX(produits.Designation) as Designation'),
                DB::raw('SUM(entrer_produits.Prix_Achat_Net) as Prix_Achat_Net'),
                DB::raw('SUM(entrer_produits.Qte_Entree) as Qte_Entree')
            )
            // ->whereBetween('entree_produits.Date_Entree', [$date_debut_periode, $date_fin_periode])
            // ->whereBetween('entrer_produits.created_at', [$date_debut_periode, $date_fin_periode])
            // ->orderBy('year', 'month')
            ->where('agences.id', '=', $agence)
            ->groupBy('Id_Agence', 'Id_Produit')
            ->get();

            // dd($achat_cumule_par_agence);

        // Récupération des catégories, fournisseurs et produits
        $categories = CategorieProduit::all();
        $fournisseurs = Fournisseur::all();
        $agences = Agence::all();

        // Retourne la vue avec les données récupérées
        return view('page.statistique.achat.statistique', [
            'achat_cumule_par_agence' => $achat_cumule_par_agence,
            'categorie' => $categories,
            'fournisseur' => $fournisseurs,
            'agences' => $agences,
            'filterAgence' => true
        ]);
    }

    public function imprimerAchatCumuleParMois(Request $request){

        // dd($request);

        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $action = $request->input('action');

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.statistique.achat.imprimer.imprimer-achat-cumule-par-mois', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
        ])->render();

        if($action === 'imprimer'){
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ACM';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if($action === 'exporter'){

            $prefixe = 'ACM';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            return Excel::download(new AchatCumuleParMoisExport($all_data, $texteEntetePied), $nom_excel);
        }


    }

    public function imprimerAchatCumuleParCategorie(Request $request){
            // dd($request);

        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $action = $request->input('action');

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.statistique.achat.imprimer.imprimer-achat-cumule-par-categorie', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
        ])->render();

        if($action === 'imprimer'){
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ACM';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if($action === 'exporter'){

            $prefixe = 'ACM';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            return Excel::download(new AchatCumuleParMoisExport($all_data, $texteEntetePied), $nom_excel);
        }
    }

    public function imprimerAchatCumuleParFournisseur(Request $request){
        // dd($request);

        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $action = $request->input('action');

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.statistique.achat.imprimer.imprimer-achat-cumule-par-fournisseur', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
        ])->render();

        if($action === 'imprimer'){
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ACF';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if($action === 'exporter'){

            $prefixe = 'ACF';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            return Excel::download(new AchatCumuleParFournisseurExport($all_data, $texteEntetePied), $nom_excel);
        }
    }

    public function imprimerAchatCumuleParAgence(Request $request){
        // dd($request);

        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $action = $request->input('action');

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.statistique.achat.imprimer.imprimer-achat-cumule-par-agence', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
        ])->render();

        if($action === 'imprimer'){
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ACA';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if($action === 'exporter'){

            $prefixe = 'ACA';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            return Excel::download(new AchatCumuleParAgenceExport($all_data, $texteEntetePied), $nom_excel);
        }
    }


}
