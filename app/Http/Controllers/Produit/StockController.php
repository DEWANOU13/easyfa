<?php

namespace App\Http\Controllers\Produit;

use App\Exports\HistoriqueStockExport;
use App\Exports\ListeStockExport;
use App\Exports\ListeStockExportCategorie;
use App\Http\Controllers\Controller;
use App\Imports\StockImport;
use App\Models\Agence;
use App\Models\AgenceUser;
use App\Models\CategorieProduit;
use App\Models\ChoixSeuil;
use App\Models\Image;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\SeuilStock;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

use function Laravel\Prompts\select;

class StockController extends Controller
{
    // retourne la vue d'inventaire
    public function  index(Request $request)
    {
        $this->authorize('consulter-stock-produit');
        try {
            $site_id = session()->get('site_id');

            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $categories = CategorieProduit::orderBy('id', 'desc')->get();
            $agences = Agence::orderBy('id', 'desc')->whereIn('id', $agenceIds)->get();
            $produits = Produit::orderBy('id', 'desc')->where('Statut', 'ACTIF')->where('Type', 'PRODUIT')->get();
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->whereIn('agences.id', $agenceIds)
                ->where('agences.id', $site_id)
                ->select('magasins.*', 'agences.NomAgence')
                ->get();

            $magasin_ids = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->orderBy('magasins.created_at', 'desc')
                ->pluck('magasins.id');

            $query = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->leftJoin('emballages', 'produits.Emballage_id', '=', 'emballages.id')
                ->leftJoin('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->whereIn('magasins.id', $magasin_ids)
                ->select('stocks.*', 'produits.Reference','produits.type_emballage', 'produits.Designation','emballages.Nom_emballage','categorie_emballages.Libelle as Libelle_Categorie_Emballage', 'categorie_produits.Libelle as Libelle_Categorie', 'magasins.NomMagasin', 'agences.NomAgence', 'unite_comptages.Libelle as Libelle_Unite_Comptage')
                // ->select('stocks.*', 'produits.Reference','produits.type_emballage', 'produits.Designation', 'categorie_produits.Libelle as Libelle_Categorie', 'magasins.NomMagasin', 'agences.NomAgence', 'unite_comptages.Libelle as Libelle_Unite_Comptage')
                ->orderBy('produits.Reference', 'asc');

                $agence = Agence::find($site_id);

                if($agence->NomAgence === 'Siège'){
                    $stocke_produits = $query->get();
                }else{
                    $stocke_produits = $query->where('agences.id', '=', $site_id)->get();

                }

                // dd($stocke_produits);

            $historiques_stock = DB::table('stock_histories')
                ->join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
                ->join('agences', 'agences.id', '=', 'stock_histories.agence_id')
                ->join('magasins', 'magasins.id', '=', 'stock_histories.Id_Magasin')
                ->whereIn('magasins.id', $magasin_ids)
                ->select('stock_histories.Date', 'produits.Designation', 'agences.NomAgence', 'magasins.NomMagasin', 'stock_histories.Motif', 'stock_histories.Justificatif', 'stock_histories.Quantite', 'stock_histories.operation')
                ->get();

            $import_stocks = DB::table('import_stocks')
                ->join('users', 'import_stocks.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stocks.Id_Agence', '=', 'agences.id')
                ->select('import_stocks.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stocks.id', 'desc')
                ->get();

            // dd($import_stocks);


                $typeSeuil = ChoixSeuil::where('libelle', 'CATEGORIE')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_cat = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->join('seuil_stocks', 'produits.Id_Categorie', '=', 'seuil_stocks.id_categorie_produit')
                    ->whereColumn('stocks.Qte_stockee', '<=', 'seuil_stocks.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'categorie_produits.Libelle',
                        'stocks.Qte_stockee',
                        'seuil_stocks.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_cat = '';
                }
                $typeSeuil = ChoixSeuil::where('libelle', 'PRODUIT')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_produit = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('seuil_stock_produits', 'produits.id', '=', 'seuil_stock_produits.id_produit')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->whereColumn('stocks.Qte_stockee', '<', 'seuil_stock_produits.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'stocks.Qte_stockee',
                        'seuil_stock_produits.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_produit = '';
                }







            $solde_initial = null;
            $date_veille_debut = null;

            return view('page.produit.stock.stock', [
                'stock_produits' => $stocke_produits,
                'historiques_stock' => $historiques_stock,
                'solde_initial' => $solde_initial,
                'date_veille_debut' => $date_veille_debut,
                'import_stocks' => $import_stocks,
                'agence' => $agences,
                'categorie' => $categories,
                'produit' => $produits,
                'magasin' => $magasins,
                'dp' => '',
                'df' => '',
                'ag' => Agence::find(0),
                'mg' => Magasin::find(0),
                'cat' => CategorieProduit::find(0),
                'prod' => Produit::find(0),
                'seuil_stock_by_cat' => $seuil_stock_by_cat,
                'seuil_stock_by_produit' => $seuil_stock_by_produit
            ])->with('tabToShow', false);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function filterStockProduit(Request $request)
    {
        $this->authorize('consulter-stock-produit');
        try {
            $site_id = session()->get('site_id');

            // Récupération des valeurs du formulaire
            $agence = $request->input('agence');
            $magasin = $request->input('magasin');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');

            // Construction de la requête
            $query = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as Libelle_Categorie', 'magasins.NomMagasin', 'agences.NomAgence', 'unite_comptages.Libelle as Libelle_Unite_Comptage')
                ->where('agences.id', $site_id); // Ajouter la condition pour le site_id ici

            // Ajouter les conditions WHERE en fonction des valeurs fournies
            if ($agence) {
                $query->where('agences.id', $agence);
            }

            if ($magasin) {
                $query->where('magasins.id', $magasin);
            }

            if ($categorie && $categorie !== 'Toutes') {
                $query->where('categorie_produits.id', $categorie);
            }

            if ($produit && $produit !== 'Tous') {
                $query->where('produits.id', $produit);
            }

            // Exécuter la requête
            $stocke_produits = $query->get();
            // dd($stocke_produits); // Vérifiez les données récupérées

            // Récupérer les stocks importés
            $import_stocks = DB::table('import_stocks')
                ->join('users', 'import_stocks.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stocks.Id_Agence', '=', 'agences.id')
                ->select('import_stocks.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stocks.id', 'desc')
                ->get();

            // Récupérer les agences, catégories, produits et magasins
            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $agences = Agence::whereIn('id', $agenceIds)->orderBy('id', 'desc')->get();
            $categories = CategorieProduit::orderBy('id', 'desc')->get();
            $produits = Produit::where('Statut', 'ACTIF')->where('Type', 'PRODUIT')->orderBy('id', 'desc')->get();
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->whereIn('agences.id', $agenceIds)
                ->where('agences.id', $site_id)
                ->select('magasins.*', 'agences.NomAgence')
                ->get();

                $typeSeuil = ChoixSeuil::where('libelle', 'CATEGORIE')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_cat = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->join('seuil_stocks', 'produits.Id_Categorie', '=', 'seuil_stocks.id_categorie_produit')
                    ->whereColumn('stocks.Qte_stockee', '<=', 'seuil_stocks.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'categorie_produits.Libelle',
                        'stocks.Qte_stockee',
                        'seuil_stocks.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_cat = '';
                }
                $typeSeuil = ChoixSeuil::where('libelle', 'PRODUIT')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_produit = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('seuil_stock_produits', 'produits.id', '=', 'seuil_stock_produits.id_produit')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->whereColumn('stocks.Qte_stockee', '<', 'seuil_stock_produits.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'stocks.Qte_stockee',
                        'seuil_stock_produits.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_produit = '';
                }

            return view('page.produit.stock.stock', [
                'stock_produits' => $stocke_produits,
                'import_stocks' => $import_stocks,
                'produit' => $produits,
                'magasin' => $magasins,
                'agence' => $agences,
                'categorie' => $categories,
                'dp' => '',
                'df' => '',
                'solde_initial' => '',
                'date_veille_debut' => '',
                'ag' => $agence ? Agence::find($agence) : '',
                'mg' => $magasin ? Magasin::find($magasin) : '',
                'cat' => $categorie !== 'Toutes' ? CategorieProduit::find($categorie) : '',
                'prod' => $produit !== 'Tous' ? Produit::find($produit) : '',
                'seuil_stock_by_cat' => $seuil_stock_by_cat,
                'seuil_stock_by_produit' => $seuil_stock_by_produit
            ])->with('tabToShow', false, '');
        } catch (\Exception $e) {
            return redirect()->route('page.stock.stock')->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function filterStockProduitHistorique(Request $request)
    {
        $this->authorize('consulter-stock-produit');
        try {
            $site_id = session()->get('site_id');

            $date_fin_periode = $request->input('date_fin_periode');
            $date_debut_periode = $request->input('date_debut_periode');
            $agence = $request->input('agence');
            $magasin = $request->input('magasin');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');

            $dp = $date_debut_periode;
            $df = $date_fin_periode;
            $ag = $agence;
            $mg = $magasin;
            $cat = $categorie;
            $prod = $produit;
            // dd('$magasin', $magasin, '$agence', $site_id);

            // Validation des dates
            if ($date_debut_periode && $date_fin_periode) {
                $date_debut_periode = Carbon::createFromFormat('Y-m-d\TH:i',  $dp)->format('Y-m-d 00:00:00');
                $date_fin_periode =  Carbon::createFromFormat('Y-m-d\TH:i', $df)->format('Y-m-d 23:59:59');
                $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dp . ' -1 day')); // Date de la veille
            } else {
                return redirect()->route('page.stock.stock')->with('error', 'Veuillez sélectionner les dates de début et de fin.')->with('tabToShow', 'historique_stock');
            }

            // if ($agence && $magasin) {
            //     return redirect()->route('page.stock.stock')->with('error', 'Sélectionnez une agence ou un magasin.');
            // }

            if (!$produit || !$magasin) {
                return redirect()->route('page.stock.stock')->with('error', 'Sélectionnez ou moins un produit et un magasin.')->with('tabToShow', 'historique_stock');
            }


            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit', $produit)
                ->where('Id_Magasin', $magasin)
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

            // S'assurer qu'il y a un solde, sinon on initialise à 0
            if (is_null($solde_initial)) {
                $solde_initial = 0;
            }

            $query_historiques_stock_filter = DB::table('stock_histories')
                ->join('produits', 'stock_histories.Id_Produit' , '=', 'produits.id')
                ->join('agences',  'stock_histories.agence_id', '=','agences.id')
                ->join('magasins', 'stock_histories.Id_Magasin', '=', 'magasins.id');
                // ->where('stock_histories.agence_id', '=', $site_id);

            // if ($agence) {
            //     $query_historiques_stock_filter->where('agences.id', $agence);
            // }
            if ($magasin) {
                $query_historiques_stock_filter->where('stock_histories.Id_Magasin', '=', $magasin);
            }
            if ($produit) {
                $query_historiques_stock_filter->where('stock_histories.Id_Produit', '=', $produit);
            }
            if ($date_debut_periode && $date_fin_periode) {
                $query_historiques_stock_filter->whereBetween('stock_histories.Date', [$date_debut_periode, $date_fin_periode]);
            }

            $historiques_stock = $query_historiques_stock_filter->select(
                'stock_histories.Date',
                'produits.Designation',
                'agences.NomAgence',
                'magasins.NomMagasin',
                'stock_histories.Motif',
                'stock_histories.Justificatif',
                'stock_histories.Quantite',
                'stock_histories.operation',
                'stock_histories.created_at'
            );

            $historiques_stock = $query_historiques_stock_filter->get();

            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $categories = CategorieProduit::orderBy('id', 'desc')->get();
            $agences = Agence::orderBy('id', 'desc')->whereIn('id', $agenceIds)->get();
            $produits = Produit::orderBy('id', 'desc')->where('Statut', 'ACTIF')->where('Type', 'PRODUIT')->get();
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->whereIn('agences.id', $agenceIds)
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->get();


            $magasin_ids = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->orderBy('magasins.created_at', 'desc')
                ->pluck('magasins.id');

            $query = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                // ->whereIn('magasins.id', $magasin_ids)
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as Libelle_Categorie', 'magasins.NomMagasin', 'agences.NomAgence', 'unite_comptages.Libelle as Libelle_Unite_Comptage')
                ->orderBy('stocks.id', 'desc');

                $query_agence = Agence::find($site_id);


                if($query_agence->NomAgence === 'Siège'){
                    $stocke_produits = $query->get();
                }else{
                    $stocke_produits = $query->where('agences.id', '=', $site_id)->get();
                }


            $import_stocks = DB::table('import_stocks')
                ->join('users', 'import_stocks.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stocks.Id_Agence', '=', 'agences.id')
                ->select('import_stocks.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stocks.id', 'desc')
                ->get();

            // dd($historiques_stock);


            $typeSeuil = ChoixSeuil::where('libelle', 'CATEGORIE')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_cat = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->join('seuil_stocks', 'produits.Id_Categorie', '=', 'seuil_stocks.id_categorie_produit')
                    ->whereColumn('stocks.Qte_stockee', '<=', 'seuil_stocks.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'categorie_produits.Libelle',
                        'stocks.Qte_stockee',
                        'seuil_stocks.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_cat = '';
                }
                $typeSeuil = ChoixSeuil::where('libelle', 'PRODUIT')->first();
                if($typeSeuil != null){

                    $seuil_stock_by_produit = Stock::join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('seuil_stock_produits', 'produits.id', '=', 'seuil_stock_produits.id_produit')
                    ->join('magasins', 'magasins.id', '=', 'stocks.Id_Magasin')
                    ->whereColumn('stocks.Qte_stockee', '<', 'seuil_stock_produits.seuil_stock') // Comparaison correcte entre colonnes
                    ->select(
                        'produits.Reference',
                        'produits.Designation',
                        'stocks.Qte_stockee',
                        'seuil_stock_produits.seuil_stock'
                    )
                    ->where('magasins.agence_id', $site_id)
                    ->where('produits.statut', 'ACTIF')
                    ->whereIn('produits.Type', ['PRODUIT','produit'])
                    ->get();
                }else{

                    $seuil_stock_by_produit = '';
                }



            return view('page.produit.stock.stock', [
                'solde_initial' => $solde_initial,
                'date_veille_debut' => $date_veille_debut,
                'historiques_stock' => $historiques_stock,
                'agence' => $agences,
                'import_stocks' => $import_stocks,
                'categorie' => $categories,
                'produit' => $produits,
                'magasin' => $magasins,
                'dp' => $dp,
                'df' => $df,
                'ag' => Agence::find($ag),
                'mg' => Magasin::find($mg),
                'cat' => CategorieProduit::find($cat),
                'prod' => Produit::find($prod),
                'stock_produits' => $stocke_produits,
                'seuil_stock_by_cat' => $seuil_stock_by_cat,
                'seuil_stock_by_produit' => $seuil_stock_by_produit
            ])->with('tabToShow', 'historique_stock');
        } catch (\Exception $e) {
            return redirect()->route('page.stock.stock')->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function imprimerStock(Request $request)
    {
        // dd($request);
        $this->authorize('consulter-stock-produit');
        $reponse = $request->input('reponse');
        try {

            $user = Auth::user();
            $Agence_id = session()->get('site_id');

            $get_stocks = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_categorie', 'categorie_produits.id')
                ->join('emballages', 'produits.Emballage_id', 'emballages.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', 'unite_comptages.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'produits.Id_Categorie', 'produits.Emballage_id', 'categorie_produits.Libelle', 'unite_comptages.Libelle as Libelle_Comptage', 'magasins.NomMagasin')
                ->where('magasins.agence_id', $Agence_id)
                ->where('produits.Type', 'PRODUIT')
                ->orderBy('produits.Reference', 'asc')
                ->get();



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data_categorie_stock = DB::table('stocks')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('categorie_produits', 'produits.Id_categorie', 'categorie_produits.id')
            ->join('emballages', 'produits.Emballage_id', 'emballages.id')
            ->select( 'produits.Emballage_id', 'emballages.Nom_emballage as Libelle')
            ->where('produits.Type', 'PRODUIT')
            ->distinct()
            ->get();




            if ($reponse === 'imprimer') {
                $htmlContent = view('page.produit.stock.imprimer.imprimer', [
                    'imageEntetePied' => $imageEntetePied,
                    'getStock' => $get_stocks,
                    'authUserName' => auth()->user()->name
                ])->render();

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
                $prefixe = 'stock_imprimer';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if ($reponse === 'imprimerCategorie') {

                $htmlContent = view('page.produit.stock.imprimer.imprimer_categorie', [
                    'imageEntetePied' => $imageEntetePied,
                    'getStock' => $get_stocks,
                    'data_categorie_stock' => $data_categorie_stock,
                    'authUserName' => auth()->user()->name
                ])->render();

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
                $prefixe = 'stock_imprimer';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if ($reponse === 'exporter') {

                $data = [
                    'texteEntetePied' => $texteEntetePied,
                    'getStock' => $get_stocks,
                    'reponse' => $reponse

                ];

                // dd($htmlContent);
                // dd('faire une exportation');

                $prefixe = 'stock_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ListeStockExport($data), $nom_excel);
            }
            if ($reponse === 'exporterCategorie') {

                $data = [
                    'texteEntetePied' => $texteEntetePied,
                    'getStock' => $get_stocks,
                    'reponse' => $reponse,
                    'data_categorie_stock' => $data_categorie_stock,
                ];

                // dd($data);
                // dd('faire une exportation');

                $prefixe = 'stock_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ListeStockExportCategorie($data), $nom_excel);
            }
            if ($reponse === 'formatImportation') {

                $get_stocks = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_categorie', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', 'unite_comptages.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'unite_comptages.Libelle as Libelle_Comptage', 'magasins.NomMagasin')
                ->where('magasins.agence_id', $Agence_id)
                ->where('produits.Type', 'PRODUIT')
                ->where('produits.Statut', '<>', 'INACTIF')
                ->orderBy('produits.Reference', 'asc')
                ->get();

                $data = [
                    'texteEntetePied' => $texteEntetePied,
                    'getStock' => $get_stocks,
                    'reponse' => $reponse
                ];

                // dd($htmlContent);
                // dd('faire une exportation');

                $prefixe = 'modele_stock_produits test';
                $nom_excel = $prefixe . '.xlsx';

                return Excel::download(new ListeStockExport($data), $nom_excel);
            }

            // return view('page.produit.stock.imprimer.imprimer', $data);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function imprimerStockHistorique(Request $request)
    {
        $this->authorize('consulter-stock-produit');
        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();


        // dd($all_data);
        $htmlContent = view('page.produit.stock.imprimer.imprimer-historique', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
        ])->render();

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
        $prefixe = 'Historique_stock_du';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);
    }

    public function exporterStockHistorique(Request $request)
    {
        $this->authorize('consulter-stock-produit');
        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
        $data = [
            'all_data' => $all_data,
            'texteEntetePied' => $texteEntetePied,
        ];

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // $htmlContent = [
        //   'imageEntetePied' => $imageEntetePied,
        //     'data' => $all_data,
        // ];

        // dd($htmlContent);
        // dd('faire une exportation');

        $prefixe = 'historique_stock_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new HistoriqueStockExport($data), $nom_excel);
    }

    public function import(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Lancer l'importation du fichier Excel
            Excel::import(new StockImport, $request->file('file'));

                return to_route('page.stock.stock')->with('success', 'Importation réussie.');

        } catch (Exception $e) {
            // Attraper l'exception levée dans StockImport et afficher un message d'erreur
            return redirect()->back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }
    }


    public function getDetailsImportStock(Request $request)
    {
        // dd($request);

        $id = $request->input('id');

        $details_import_stocks = DB::table('detail_import_stocks')
            ->join('produits', 'detail_import_stocks.Id_Produit', '=', 'produits.id')
            ->join('emballages', 'produits.Emballage_id', '=', 'emballages.id')
            ->join('magasins', 'detail_import_stocks.Id_Magasin', '=', 'magasins.id')
            ->select('detail_import_stocks.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'emballages.Nom_emballage')
            ->where('detail_import_stocks.Id_Import_Stock', '=', $id)
            ->get();

        // dd($details_import_stocks );

        return view('page.produit.stock.stock', [
            'details_import_stocks' => $details_import_stocks,
            'agence' => [],
            'historiques_stock' => [],
            'import_stocks' => [],
            'solde_initial' => '',
            'date_veille_debut' => '',
            'categorie' => [],
            'produit' => [],
            'magasin' => [],
            'dp' => '',
            'df' => '',
            'ag' => '',
            'mg' => '',
            'cat' => '',
            'prod' => '',
            'stock_produits' => [],
        ])->with('tabToShow', 'historique_import_stock');

        // return redirect()->route('page.stock.stock')->with('tabToShow', 'historique_import_stock');
    }

    public function getProductsByCategory($categoryId) {
        if ($categoryId === 'Toutes') {
            // dd('ici');
            // Récupérer tous les produits si "Toutes" est sélectionné
            $products = Produit::all();
        } else {
            // dd('ici(----');
            // Filtrer les produits en fonction de la catégorie
            $products = Produit::where('Id_Categorie', $categoryId)->get();
        }

        return response()->json($products);
    }

}
