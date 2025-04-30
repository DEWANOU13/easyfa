<?php

namespace App\Http\Controllers\emballage;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Client;
use App\Models\ConsignationEntree;
use App\Models\Fournisseur;
use App\Models\LigneConsignationEntree;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConsignationEntreeController extends Controller
{
    public function consignation()
    {
        accessEmballage();
        $this->authorize('consignation');

        $annees = ConsignationEntree::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        $Agence_id = session()->get('site_id');

        $query = ConsignationEntree::join('users', 'consignation_entrees.user_id', '=', 'users.id')
            ->join('fournisseurs', 'consignation_entrees.fournisseur_id', '=', 'fournisseurs.id')
            ->join('agences', 'consignation_entrees.agence_id', '=', 'agences.id')
            ->select('consignation_entrees.*', 'users.name', 'fournisseurs.DenominationSociale as Denomination_sociale')
            ->where('consignation_entrees.agence_id', $Agence_id)
            ->orderBy('consignation_entrees.created_at', 'desc');

        $query_agence = Agence::find($Agence_id);

        if ($query_agence->NomAgence == 'Siège') {

            $listeConsignation = $query->get();
        } else {
            // dd($Agence_id);
            $listeConsignation = $query->where('consignation_entrees.agence_id', '=', $Agence_id)->get();
        }


        // $detailConsignation = LigneConsignationEntree::join('consignation_entrees', 'ligne_consignation_entrees.consignation_id', '=', 'consignation_entrees.id')
        //     ->join('emballages', 'ligne_consignation_entrees.emballage_id', '=', 'emballages.id')
        //     ->join('produits', 'ligne_consignation_entrees.produit_id', '=', 'produits.id')
        //     ->select('ligne_consignation_entrees.*', 'emballages.Nom_emballage', 'produits.Designation')
        //     ->orderBy('created_at', 'desc')
        //     ->where('consignations.id', 0)
        //     ->get();

        // $listefactureDeconsignation = ::join('entree_pr', 'lien_facture_consignations.facture_id', '=', 'factures.id')
        //     ->join('consignations', 'lien_facture_consignations.consignation_id', '=', 'consignations.id')
        //     ->select('lien_facture_consignations.*', 'factures.Reference_facture', 'factures.Statut_facture', 'factures.Date_facture')
        //     ->where('consignations.id',)
        //     ->get();

        return view('page.emballage.consignation_entree.consignation_entree',  [
            'annees' => $annees,
            'listeConsignation' => $listeConsignation,
            // 'detailConsignation' => $detailConsignation,
            // 'listefactureDeconsignation' => $listefactureDeconsignation

        ]);
    }

    public function getDetailConsignation($id)
    {
        accessEmballage();
        $this->authorize('consignation');

        $annees = ConsignationEntree::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        $Agence_id = session()->get('site_id');


        $listeConsignation = ConsignationEntree::join('users', 'consignation_entrees.user_id', '=', 'users.id')
            ->join('fournisseurs', 'consignation_entrees.fournisseur_id', '=', 'fournisseurs.id')
            ->join('agences', 'consignation_entrees.agence_id', '=', 'agences.id')
            ->select('consignation_entrees.*', 'users.name', 'fournisseurs.DenominationSociale as Denomination_sociale')
            ->where('consignation_entrees.agence_id', $Agence_id)
            ->orderBy('consignation_entrees.created_at', 'desc')
            ->get();


        $detailConsignation = LigneConsignationEntree::join('consignation_entrees', 'ligne_consignation_entrees.consignation_id', '=', 'consignation_entrees.id')
            ->join('emballages', 'ligne_consignation_entrees.emballage_id', '=', 'emballages.id')
            ->join('stock_emballages', 'ligne_consignation_entrees.stock_emballage_id', '=', 'stock_emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->select('ligne_consignation_entrees.*', 'emballages.Nom_emballage', 'magasins.NomMagasin')
            ->orderBy('created_at', 'desc')
            ->where('consignation_entrees.id', $id)
            ->get();
        // $listefactureDeconsignation = LienFactureConsignation::join('factures', 'lien_facture_consignations.facture_id', '=', 'factures.id')
        //     ->join('consignations', 'lien_facture_consignations.consignation_id', '=', 'consignations.id')
        //     ->select('lien_facture_consignations.*', 'factures.Reference_facture', 'factures.Statut_facture', 'factures.Date_facture')
        //     ->where('consignations.id', $id)
        //     ->get();


        return view('page.emballage.consignation_entree.consignation_entree',  [
            'annees' => $annees,
            'listeConsignation' => $listeConsignation,
            'detailConsignation' => $detailConsignation,
            // 'listefactureDeconsignation' => $listefactureDeconsignation


        ]);
    }

    public function showFormRegleConsignation()
    {
        // accessEmballage();
        // $this->authorize('consignation');

        $clients = Fournisseur::all();
        $site_id = session()->get('site_id');

        $stock_emballages = DB::table('stock_emballages')
            ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
            ->join('agences', 'magasins.agence_id', 'agences.id')
            ->select('stock_emballages.*', 'emballages.Nom_Emballage', 'magasins.NomMagasin')
            ->where('magasins.agence_id', '=', $site_id)
            ->get();

        $quantite_deja_receptionnee = LigneConsignationEntree::all();

        // dd($stock_emballages);

        return view('page.emballage.consignation_entree.nouveau_entree', [
            'clients' => $clients,
            'stock_emballages' => $stock_emballages,
            'quantite_deja_receptionnee' => $quantite_deja_receptionnee,
        ]);
    }

    public function getConsignationDuFournisseur($clientId)
    {
        accessEmballage();
        $this->authorize('consignation');

        // Récupérez les factures liées à ce client
        $consignation = ConsignationEntree::where('fournisseur_id', $clientId)
            ->wherein('statut', ['EN_COURS', 'PARTIELLEMENT DECONSIGNE'])
            ->get();



        return response()->json($consignation);
    }

    public function getLigneConsignationSelectionnee($consignationId)
    {
        accessEmballage();
        $this->authorize('consignation');

        $ligneConsignation = LigneConsignationEntree::join('consignation_entrees', 'ligne_consignation_entrees.consignation_id', '=', 'consignation_entrees.id')
            ->join('emballages', 'ligne_consignation_entrees.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignation_entrees.produit_id', '=', 'produits.id')
            ->select('ligne_consignation_entrees.*', 'emballages.Nom_emballage', 'produits.Designation')
            ->where('ligne_consignation_entrees.consignation_id', $consignationId)
            ->get();


        return response()->json([
            'ligneConsignation' => $ligneConsignation,
        ]);
    }

    public function storeDeconsignation(Request $request)
    {
        accessEmballage();
        $this->authorize('consignation');

        // dd($request);

        $validator = Validator::make(
            $request->all(),
            [
                'client_id' => 'required',
                'consignation_id' => 'required',
                'statut' => 'required',

                'num_facture.*' => 'required',
                'montant.*' => 'nullable|numeric',

            ],
            [
                'client_id' => 'le client est requis',
                'consignation_id' => 'La consignation est requise',
                'num_facture.*' => 'emballage est require',
                'montant.*' => 'quantite est require',
                'statut' => 'statut est require',
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $fournisseur_id = $request->input('client_id');
        $consignation_id = $request->input('consignation_id');

        $consignation = ConsignationEntree::findorfail($consignation_id);

        // dd($consignation);

        foreach ($request->inputs as $value) {

            $emballage_formate = explode('|', $value['num_facture'], 3);
            $stock_emballage_id = trim($emballage_formate[0]);

            $stock_emballage = StockEmballage::find($stock_emballage_id);
            $emballage_id = $stock_emballage->Id_Emballage;

            if ($stock_emballage) {
                $new_quantite = $stock_emballage->Qte_stockee - $value['montant'];

                $stock_emballage->Qte_stockee = $new_quantite;
                $stock_emballage->update();
            }

            $ligne_consignation_entree = new LigneConsignationEntree();
            $ligne_consignation_entree->emballage_id = $emballage_id;
            $ligne_consignation_entree->consignation_id = $consignation_id;
            $ligne_consignation_entree->Qte = $value['montant'];
            $ligne_consignation_entree->stock_emballage_id = $stock_emballage_id;
            $ligne_consignation_entree->save();

            $historique_sortie_emballage = new StockEmballageHistories();
            $historique_sortie_emballage->Date = date('Y-m-d H:i:s');
            $historique_sortie_emballage->agence_id = $consignation->agence_id;
            $historique_sortie_emballage->Motif = "Sortie d'emballage sur une déconsignation";
            $historique_sortie_emballage->Justificatif = $consignation->ref_entree;
            $historique_sortie_emballage->operation = 'SORTIE';
            $historique_sortie_emballage->type_operation = 'DECONSIGNATION ENTREE';
            $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
            $historique_sortie_emballage->Id_Emballage = $stock_emballage->Id_Emballage;
            $historique_sortie_emballage->Id_Magasin = $stock_emballage->Id_Magasin;
            $historique_sortie_emballage->Quantite = $value['montant'];
            $historique_sortie_emballage->save();
        }

        // dd($request->consignation_id);

        $sommeEntree = DB::table('entrer_produits')
            ->where('Id_Entree_Produit', '=', $consignation->entree_id)
            ->sum('Qte_Entree');

        $sommeLigneConsignation = DB::table('ligne_consignation_entrees')
            ->join('consignation_entrees', 'ligne_consignation_entrees.consignation_id', 'consignation_entrees.id')
            ->where('ligne_consignation_entrees.consignation_id', '=', $request->consignation_id)
            ->sum('Qte');

        // $sommeFacturee = DB::table('ligne_consignation_entrees')
        //     ->join('consignation_entrees', 'ligne_consignation_entrees.consignation_id', 'consignation_entrees.id')
        //     ->where('ligne_consignation_entrees.consignation_id', '=', $request->consignation_id)
        //     ->sum('facturee');

        // dd($sommeEntree, $sommeLigneConsignation);

        // // foreach($ligne_consignations as $ligne_consignation){
        /*     if ($sommeEntree !=  $sommeLigneConsignation) {
            // dd('1');
            $statut_consignation = 'PARTIELLEMENT DECONSIGNE';
        } else {
            // dd('2');
            $statut_consignation = 'DECONSIGNATION TERMINEE';
        } */

        $statut_consignation = $request->statut;
        $consignation->statut = $statut_consignation;
        $consignation->update();


        return to_route('consignation_entree')->with('Déconsignation effectué avec succes');
    }

    public function filterConsignationEntree(Request $request)
    {
        $this->authorize('consulter-factures');

        try {
            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            // $query = ConsignationEntree::join('users', 'consignation_entrees.user_id', '=', 'users.id')
            //     ->join('fournisseurs', 'consignation_entrees.fournisseur_id', '=', 'fournisseurs.id')
            //     ->join('agences', 'consignation_entrees.agence_id', '=', 'agences.id')
            //     ->select('consignation_entrees.*', 'users.name', 'fournisseurs.DenominationSociale as Denomination_sociale', 'agences.NomAgence')
            //     // ->where('consignation_entrees.agence_id', $Agence_id)
            //     ->orderBy('consignation_entrees.created_at', 'desc');

            // Création de la requête initiale
            if (is_array($Agence_id)) {
                $query = ConsignationEntree::join('users', 'consignation_entrees.user_id', '=', 'users.id')
                            ->join('fournisseurs', 'consignation_entrees.fournisseur_id', '=', 'fournisseurs.id')
                            ->join('agences', 'consignation_entrees.agence_id', '=', 'agences.id')
                            ->select('consignation_entrees.*', 'users.name', 'fournisseurs.DenominationSociale as Denomination_sociale', 'agences.NomAgence')
                            // ->where('consignation_entrees.agence_id', $Agence_id)
                            ->orderBy('consignation_entrees.created_at', 'desc');
            } else {
                $query =  ConsignationEntree::join('users', 'consignation_entrees.user_id', '=', 'users.id')
                ->join('fournisseurs', 'consignation_entrees.fournisseur_id', '=', 'fournisseurs.id')
                ->join('agences', 'consignation_entrees.agence_id', '=', 'agences.id')
                ->select('consignation_entrees.*', 'users.name', 'fournisseurs.DenominationSociale as Denomination_sociale', 'agences.NomAgence')
                // ->where('consignation_entrees.agence_id', $Agence_id)
                ->orderBy('consignation_entrees.created_at', 'desc');
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('consignation_entrees.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('consignation_entrees.created_at', $month);
                $query->whereYear('consignation_entrees.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('consignation_entrees.created_at', '>=', $startDate)
                    ->where('consignation_entrees.created_at', '<=', $endDate);
            }


            $query_agence = Agence::find($Agence_id);

            if ($query_agence->NomAgence === 'Siège') {
                $listeConsignation = $query->get();
            } else {
                $listeConsignation = $query->where('consignation_entrees.agence_id', '=', $Agence_id);
            }

            // Exécuter la requête
            $listeConsignation = $query->get();


            $listeClient = Client::all();
            $listeAgence = Agence::all();
            $annees = ConsignationEntree::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view('page.emballage.consignation_entree.consignation_entree',  [
                'annees' => $annees,
                'listeConsignation' => $listeConsignation,
                'listeClient' => $listeClient,
                'listeAgence' => $listeAgence


            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
}
