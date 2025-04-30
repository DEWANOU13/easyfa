<?php

namespace App\Http\Controllers\accueil;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Stock;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CategorieClient;
use App\Models\DetailReglement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class dashboardController extends Controller
{
    // Méthode pour afficher le dashboard principal
    public function eleHmentsdashbord()
    {

        $user = Auth::user();
        $loginHistories = $user->loginHistories;
        // dd($loginHistories);

        $Agence_id = session()->get('site_id');

        // dd($Agence_id);

        //Calcul des totaux

        $TotalClient = Client::all()->count();
        $TotalFournisseur = Fournisseur::all()->count();
        $TotalProduit = Produit::all()->count();
        $Totalmagasin = Magasin::all()->count();
        $Totalusers = User::all()->count();
        $TotalCategorieProduit = CategorieProduit::all()->count();
        $totalCategorieClient = CategorieClient::all()->count();


        //Top des 10 meilleurs clients du mois

        $topClients = Client::select('clients.Denomination_sociale', DB::raw('SUM(factures.Net_a_payer) as chiffre_affaires'))
            ->join('factures', 'clients.id', '=', 'factures.client_id')
            ->whereMonth('factures.created_at', '=', date('m'))
            ->where(function ($query) {
                $query->where('factures.Statut_facture', 'NORMALISEE')
                    ->orwhere('factures.Statut_facture', 'EN COURS DE REGLEMENT')
                    ->orWhere('factures.Statut_facture', 'SOLDE');
            })
            ->where(function ($query) {
                $query->where('factures.Code_type_facture', 'FV')
                    ->orWhere('factures.Code_type_facture', 'EV');
            })
            ->where('factures.agence_id', '=', $Agence_id)
            ->groupBy('clients.Denomination_sociale')
            ->orderByDesc('chiffre_affaires')
            ->limit(10)
            ->get();

        // dd($topClients);

        // Top des 10 produits les plus vendus du mois

        $topProduits = DB::table('lignefactures')
            ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
            ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->select('produits.Reference', 'produits.Designation', DB::raw('COUNT(stocks.Id_Produit) as total_sales'))
            ->whereYear('lignefactures.created_at', '=', now()->year)
            ->whereMonth('lignefactures.created_at', '=', now()->month)
            ->where('factures.agence_id', '=', $Agence_id)
            ->groupBy('produits.Reference', 'produits.Designation')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();


        // Récupérer toutes les factures
        $Factures = Facture::all();

        $nbreFacture = Facture::where('agence_id', '=', $Agence_id)
        ->where(function ($query) {
            $query->where('Code_type_facture', '=', 'EV')
                  ->orWhere('Code_type_facture', '=', 'FV');
        })
        ->whereNot('Statut_facture', '=', 'EN COURS')
        ->whereNot('Statut_facture', '=', 'ANNULEE')
        ->count();


        $nbreAvoir = Facture::where('agence_id', '=', $Agence_id)
        ->where(function ($query) {
            $query->where('Code_type_facture', '=', 'EA')
                  ->orWhere('Code_type_facture', '=', 'FA');
        })
        ->where('Statut_facture', '=', 'NORMALISEE')
        ->count();

        $nbreProformas = Facture::where('agence_id', '=', $Agence_id)
            ->where('Code_type_facture', '=', 'PR')
            ->count();

        $nbrFactureEncourdereglement = Facture::where('agence_id', '=', $Agence_id)
        ->where(function ($query) {
            $query->where('Code_type_facture', '=', 'EV')
                  ->orWhere('Code_type_facture', '=', 'FV');
        })
        ->where('Statut_facture', '=', 'EN COURS DE REGLEMENT')
        ->count();

        $nbrFacturesolde = Facture::where('agence_id', '=', $Agence_id)
        ->where(function ($query) {
            $query->where('Code_type_facture', '=', 'EV')
                  ->orWhere('Code_type_facture', '=', 'FV');
        })
        ->where('Statut_facture', '=', 'SOLDE')
        ->count();

        $sommeMontantRegler = DetailReglement::join('reglements', 'reglements.id', '=', 'detail_reglements.Id_Reglement')
                ->where('reglements.Id_Agence', '=', $Agence_id)
                ->where('detail_reglements.Statut_Reglement', '=', 1)
                ->sum('detail_reglements.Montant_Regle');


                $sommeMoisEnCours = DetailReglement::join('reglements', 'reglements.id', '=', 'detail_reglements.Id_Reglement')
                    ->where('reglements.Id_Agence', '=', $Agence_id)
                    ->where('detail_reglements.Statut_Reglement', '=', 1)
                    ->whereMonth('detail_reglements.created_at', '=', Carbon::now()->month) // Filtre sur le mois actuel
                    ->whereYear('detail_reglements.created_at', '=', Carbon::now()->year)   // Filtre sur l'année actuelle
                    ->sum('detail_reglements.Montant_Regle');






        //dd($nbreProformas);

        // Initialiser les totaux
        $totalProformas = 0;
        $totalMontantProformas = 0;
        $totalFactures = 0;
        $totalMontantFactures = 0;
        $totalAvoirs = 0;
        $totalMontantAvoirs = 0;
        $totalMontantFacturesV = 0;
        $totalMontantFacN = 0;

        // Parcourir les factures pour calculer les totaux
        foreach ($Factures as $facture) {
            if ($facture->agence_id === $Agence_id) {
                if ($facture->Code_type_facture === 'PR') {
                    $totalProformas++;
                    $totalMontantProformas += $facture->Net_a_payer;
                } elseif ($facture->Code_type_facture === 'FA' || $facture->Code_type_facture === 'EV') {
                    $totalAvoirs++;
                    $totalMontantAvoirs += $facture->Net_a_payer;
                } else {
                    $totalFactures++;
                    $totalMontantFactures += $facture->Net_a_payer;
                    if ($facture->Statut_facture === 'NORMALISEE' || $facture->Statut_facture === 'EN COURS DE REGLEMENT') {
                        $totalMontantFacN += $facture->Net_a_payer;
                    }
                }
            }
        }
        $totalMontantFacturesV += $totalMontantFactures - $totalMontantAvoirs;

        //Diagramme des règlements
        // Récupérer les règlements avec leurs détails de la base de données
        $reglements = DB::table('reglements')
            ->join('detail_reglements', 'reglements.id', '=', 'detail_reglements.Id_Reglement')
            ->select(DB::raw('DATE(reglements.Date_Reglement) as dateR'), DB::raw('TIME(reglements.Date_Reglement) as heureR'), DB::raw('SUM(detail_reglements.Montant_Regle) as total_montant_reglement'))
            ->where('Statut_Reglement', 1)
            ->groupBy('dateR', 'heureR')
            ->get();

        $totalMontantReglements = 0;
        $pourcentageReglements = 0;
        // Parcourir les règlements pour calculer les totaux
        foreach ($reglements as $reglement) {
            $totalMontantReglements += $reglement->total_montant_reglement;
        }
        if ($totalMontantFacturesV != 0) {
            $pourcentageReglements += round(($totalMontantReglements / $totalMontantFacturesV) * 100, 2);
        }
        // Manipuler les données pour les préparer au graphique
        $datesR = $reglements->pluck('dateR');
        $heuresR = $reglements->pluck('heureR');
        $totalReglementsParDate = $reglements->pluck('total_montant_reglement');

        // Récupérer les données de la base de données en effectuant une jointure entre les tables
        $entrees = DB::table('entree_produits')
            ->join('entrer_produits', 'entree_produits.id', '=', 'entrer_produits.Id_Entree_Produit')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->select(DB::raw('DATE(entree_produits.Date_Entree) as date_Entree'), DB::raw('TIME(entree_produits.Date_Entree) as heure_Entree'), DB::raw('SUM(entrer_produits.Qte_Entree) as total_quantite'), 'produits.Designation')
            ->groupBy('date_Entree', 'heure_Entree', 'produits.Designation')
            ->where('entree_produits.Id_Agence', $Agence_id)
            ->get();
        // dd($entrees);

        $sorties = DB::table('sortie_produits')
            ->join('sortir_produits', 'sortie_produits.id', '=', 'sortir_produits.Id_Sortie_Produit')
            ->join('produits', 'sortir_produits.Id_Sortie_Produit', '=', 'produits.id')
            ->select(DB::raw('DATE(sortie_produits.Date_Sortie) as date_Sortie'), DB::raw('TIME(sortie_produits.Date_Sortie) as heure_Sortie'), DB::raw('SUM(sortir_produits.Qte_Sortie) as total_quantite_sortie'), 'produits.Designation')
            ->groupBy('date_Sortie', 'heure_Sortie', 'produits.Designation')
            ->where('sortie_produits.Id_Agence', $Agence_id)

            ->get();

        $stocks = DB::table('stocks')
            // ->join('sortir_produits', 'sortie_produits.id', '=', 'sortir_produits.Id_Sortie_Produit')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->select('produits.Designation', DB::raw('SUM(stocks.Qte_stockee) as total_en_stock'), 'stocks.Prix_Achat_Net')
            ->groupBy('stocks.Prix_Achat_Net', 'produits.Designation')
            ->where('magasins.agence_id', $Agence_id)

            ->get();
        $TotalStocks = 0;
        // Parcourir les règlements pour calculer les totaux
        foreach ($stocks as $stock) {
            $TotalStocks += $stock->total_en_stock;
        }

        // Manipuler les données pour les préparer au graphique
        $dates = $entrees->pluck('date_Entree');
        $totalesEntrees = $entrees->SUM('total_quantite');
        //dd($totalesEntrees);
        $nomProduits = $entrees->pluck('Designation');

        $datesSorties = $sorties->pluck('date_Sortie');
        $totalesSorties = $sorties->SUM('total_quantite_sortie');
        $nomProduitsSortie = $sorties->pluck('Designation');

        $produitStock = $stocks->pluck('Designation');
        $quantiteEnStock = $stocks->pluck('total_en_stock');
        $prixAchatNet = $stocks->pluck('Prix_Achat_Net');

        // Fusionner les deux ensembles de dates en un seul ensemble de dates uniques
        $datesCombinees = $dates->merge($datesSorties)->unique()->sort();
        $userWidgets = Auth::user()->widgets->pluck('id');



        return view('page.accueil.home.index', [

            'userWidgets' => $userWidgets,

            //Historique des connexions
            'loginHistories' => $loginHistories,
            //Top meilleurs clients

            'topClients' => $topClients,

            //Top meilleurs produits

            'topProduits' => $topProduits,

            // Les totaux
            'totalCategorieClient' => $totalCategorieClient,

            'TotalClient' => $TotalClient,
            'TotalFournisseur' => $TotalFournisseur,
            'TotalProduit' => $TotalProduit,
            'Totalmagasin' => $Totalmagasin,
            'Totalusers' => $Totalusers,
            'TotalCategorieProduit' => $TotalCategorieProduit,

            // Proformas et factures
            'nbreFacture' => $nbreFacture,
            'nbreProformas' => $nbreProformas,
            'nbrFacturesolde' => $nbrFacturesolde,
            'nbrFactureEncourdereglement' => $nbrFactureEncourdereglement,
            'nbreAvoir' => $nbreAvoir,
            'sommeMontantRegler' => $sommeMontantRegler,
            'sommeMoisEnCours' => $sommeMoisEnCours,

            'totalProformats' => $totalProformas,
            'totalMontantProformats' => $totalMontantProformas,
            'totalFactures' => $totalFactures,
            'totalMontantFactures' => $totalMontantFactures,
            'totalMontantFacturesV' => $totalMontantFacturesV,
            'totalMontantAvoirs' => $totalMontantAvoirs,
            'totalAvoirs' => $totalAvoirs,
            'totalMontantFacN' => $totalMontantFacN,

            //Retour données règlements
            'datesR' => $datesR,
            'heuresR' => $heuresR,
            'totalReglementsParDate' => $totalReglementsParDate,
            'totalMontantReglements' => $totalMontantReglements,
            'pourcentageReglements' => $pourcentageReglements,

            'datesCombinees' => $datesCombinees,

            //Retour données Entrées
            'dates' => $dates,
            'totalesEntrees' => $totalesEntrees,
            'designations' => $nomProduits,

            //Retour données Sorties
            'totalesSorties' => $totalesSorties,
            'nomProduitsSortie' => $nomProduitsSortie,
            'datesSorties' => $datesSorties,

            //Retour données en Storks
            'produitStock' => $produitStock,
            'quantiteEnStock' => $quantiteEnStock,
            'TotalStocks' => $TotalStocks,
            'prixAchatNet' => $prixAchatNet
        ]);
    }

    public function importation()
    {
        $this->authorize('importation');
        return view('page.parametre_administration.importation.importation');
    }

    public function getUserWidgets()
    {
        $widgets = Auth::user()->widgets;
        return response()->json(['widgets' => $widgets]);
    }
}
