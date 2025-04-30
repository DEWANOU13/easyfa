<?php

namespace App\Http\Controllers\statistique;

use Exception;
use App\Models\User;
use App\Models\Agence;
use App\Models\Produit;
use App\Models\AgenceUser;
use Illuminate\Http\Request;
use App\Models\StockHistories;
use App\Models\groupeCategorie;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Lignefacture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class pointVenteController extends Controller
{
    public function pointVente()
    {
        $this->authorize('voir-point-de-vente');

        try {
            $produit = Produit::all();
            $categorie = CategorieProduit::all();
            $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();
            $listeGroupeCat = groupeCategorie::all();
            $utilisateur = User::wherenot('id',1)->wherenot('id',2)->get();

            $site_id = session()->get('site_id');
            $agenceConnect = Agence::where('id', $site_id)->first();

            $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

            return view('page.statistique.point.point',
                [
                    'agences' => $agences,
                    'produit' => $produit,
                    'categorie' => $categorie,
                    'listeAgence' => $listeAgence,
                    'listeGroupeCat' => $listeGroupeCat,
                    'utilisateur' => $utilisateur,
                    'agenceConnect' => $agenceConnect
                ]
            );
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }






/*     public function pointVentePeriode1(Request $request)
{
    $this->authorize('voir-point-de-vente');
    DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

    $data = $request->validate([
        'dateDebut' => 'required|date',
        'dateFin' => 'required|date|after_or_equal:dateDebut',
        'agence' => 'required',
        'utilisateur' => 'required',
    ]);

    $dateDebut = $data['dateDebut'];
    $dateFin = $data['dateFin'];
    $agence = $data['agence'];
    $utilisateur = $data['utilisateur'];

    // Récupération des données pour les ventes (FACTURE_V) et les achats (FACTURE_A)
    $pointVente = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
        ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
        ->join('association_groupe_categories', 'association_groupe_categories.categorie_produit_id', '=', 'produits.Id_Categorie')
        ->join('groupe_categories', 'groupe_categories.id', '=', 'association_groupe_categories.groupe_categorie_id')
        ->select(
            'groupe_categories.libelle as libelle_groupe',
            'groupe_categories.id as groupe_id',
            DB::raw('SUM(CASE WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Prix_vente ELSE 0 END) as total_V'),
            DB::raw('SUM(CASE WHEN stock_histories.type_operation = "FACTURE_A" THEN stock_histories.Prix_vente ELSE 0 END) as total_A'),
            DB::raw('SUM(CASE WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN stock_histories.Prix_vente ELSE 0 END) as total_inv'),
            DB::raw('DATE(stock_histories.created_at) as date_jour')
        )
        ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
        ->where('stock_histories.agence_id', $agence)
        ->where('stock_histories.Id_Utilisateur', $utilisateur)
        ->groupBy('groupe_categories.id', 'groupe_categories.libelle', DB::raw('DATE(stock_histories.created_at)'))
        ->get()
        ->map(function($item) {
            // Calculer la différence entre les ventes et les achats
            $item->total_vente = $item->total_V - $item->total_A - $item->total_inv;
            return $item;
        });

       // dd($pointVente);

    // Récupération des groupes de catégories pour les en-têtes
    $listeGroupeCat = GroupeCategorie::all();
    $utilisateur = User::find($utilisateur);

    return response()->json([
        'pointVente' => $pointVente,
        'dateDebut' => $dateDebut,
        'dateFin' => $dateFin,
        'listeGroupeCat' => $listeGroupeCat,
        'utilisateur' => $utilisateur
    ]);
}
public function pointVentePeriode2(Request $request)
{
    $this->authorize('voir-point-de-vente');
    DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

    $data = $request->validate([
        'dateDebut' => 'required|date',
        'dateFin' => 'required|date|after_or_equal:dateDebut',
        'agence' => 'required',
        'utilisateur' => 'required',
    ]);

    $dateDebut = $data['dateDebut'];
    $dateFin = $data['dateFin'];
    $agence = $data['agence'];
    $utilisateur = $data['utilisateur'];

    // Récupération des données avec multiplication Prix_revient * Qte
    $pointVente = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
        ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
        ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
        ->join('association_groupe_categories', 'association_groupe_categories.categorie_produit_id', '=', 'produits.Id_Categorie')
        ->join('groupe_categories', 'groupe_categories.id', '=', 'association_groupe_categories.groupe_categorie_id')
        ->select(
            'groupe_categories.libelle as libelle_groupe',
            'groupe_categories.id as groupe_id',
            DB::raw('SUM(lignefactures.Prix_revient * lignefactures.Qte) as total_vente'),
            DB::raw('DATE(factures.created_at) as date_jour')
        )
        ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
        ->where('factures.agence_id', $agence)
        ->where('factures.user_id', $utilisateur)
        // Filtrer par Code_type_facture (FV ou EV)
        ->whereIn('factures.Code_type_facture', ['FV', 'EV'])
        // Exclure les statuts ANNULEE et EN COURS
        ->whereNotIn('factures.Statut_facture', ['ANNULEE', 'EN COURS', 'INVALIDEE'])
        ->groupBy('groupe_categories.id', 'groupe_categories.libelle', DB::raw('DATE(factures.created_at)'))
        ->get();

    // Vérification du résultat
    //dd($pointVente);
       // Récupération des groupes de catégories pour les en-têtes
       $listeGroupeCat = GroupeCategorie::all();
       $utilisateur = User::find($utilisateur);

       return response()->json([
           'pointVente' => $pointVente,
           'dateDebut' => $dateDebut,
           'dateFin' => $dateFin,
           'listeGroupeCat' => $listeGroupeCat,
           'utilisateur' => $utilisateur
       ]);
} */

public function pointVentePeriode(Request $request)
{
    $this->authorize('voir-point-de-vente');
    DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

    $data = $request->validate([
        'dateDebut' => 'required|date',
        'dateFin' => 'required|date|after_or_equal:dateDebut',
        'agence' => 'required',
        'utilisateur' => 'required',
    ]);

    $dateDebut = $data['dateDebut'];
    $dateFin = $data['dateFin'];
    $agence = $data['agence'];
    $utilisateur = $data['utilisateur'];

    // Récupération des données avec multiplication Prix_revient * Qte sans regroupement par date
    $pointVente = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
        ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
        ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
        ->join('association_groupe_categories', 'association_groupe_categories.categorie_produit_id', '=', 'produits.Id_Categorie')
        ->join('groupe_categories', 'groupe_categories.id', '=', 'association_groupe_categories.groupe_categorie_id')
        ->select(
            'groupe_categories.libelle as libelle_groupe',
            'groupe_categories.id as groupe_id',
            DB::raw('SUM(lignefactures.Prix_revient * lignefactures.Qte) as total_vente')
        )
        ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
        ->where('factures.agence_id', $agence)
        ->where('factures.user_id', $utilisateur)
        // Filtrer par Code_type_facture (FV ou EV)
        ->whereIn('factures.Code_type_facture', ['FV', 'EV'])
        // Exclure les statuts ANNULEE, EN COURS, et INVALIDEE
        ->whereNotIn('factures.Statut_facture', ['ANNULEE', 'EN COURS', 'INVALIDEE'])
        ->groupBy('groupe_categories.id', 'groupe_categories.libelle') // Supprimer le groupement par date
        ->get();

    // Récupération des groupes de catégories pour les en-têtes
    $listeGroupeCat = GroupeCategorie::all();
    $utilisateur = User::find($utilisateur);

    $dateDebut = Carbon::parse($dateDebut)->translatedFormat('d/m/Y - H:i:s');
    $dateFin = Carbon::parse($dateFin)->translatedFormat('d/m/Y - H:i:s');

    return response()->json([
        'pointVente' => $pointVente,
        'dateDebut' => $dateDebut,
        'dateFin' => $dateFin,
        'listeGroupeCat' => $listeGroupeCat,
        'utilisateur' => $utilisateur
    ]);
}




}
