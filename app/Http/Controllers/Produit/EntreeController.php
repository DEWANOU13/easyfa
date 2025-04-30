<?php

namespace App\Http\Controllers\Produit;

use App\Exports\EntreeActionPrintExport;
use App\Exports\EntreeExport;
use App\Exports\ImportationProduitEntreeExport;
use App\Http\Controllers\Controller;
use App\Imports\EntreeImport;
use App\Imports\StockImport;
use App\Models\Agence;
use App\Models\CategorieProduit;
use App\Models\ConsignationEntree;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\Fournisseur;
use App\Models\StockHistories;
use App\Models\Image;
use App\Models\LigneConsignationEntree;
use App\Models\Magasin;
use App\Models\PrefixeReference;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;

class EntreeController extends Controller
{
    // retourne la vue d'entree
    public function  index(Request $request)
    {
        $this->authorize('consulter-entree-produit');
        try {

            $site_id = session()->get('site_id');

            $magasin_ids = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->orderBy('magasins.created_at', 'desc')
                ->pluck('magasins.id');


            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->entre_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // $entree_produits = DB::table('entree_produits')
            //     ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            //     ->join('agences', 'entree_produits.Id_Agence_source', '=', 'agences.id')
            //     ->join('users', 'entree_produits.Id_Utilisateur', '=', 'users.id')
            //     ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
            //     // ->join('utilisateur', 'entree_produits.Id_Utilisateur', '=', 'utilisateurs.id')
            //     ->select('entree_produits.*', 'fournisseurs.DenominationSociale as DenominationSociale', 'users.name', 'agences.NomAgence')
            //     ->whereMonth('entree_produits.created_at', $currentMonth)
            //     ->whereYear('entree_produits.created_at', $currentYear)
            //     ->where('agences.id', $site_id)
            //     ->orderBy('entree_produits.id', 'desc')
            //     ->get();



            $query = DB::table('entree_produits')
                ->leftJoin('fournisseurs', function ($join) {
                    $join->on('entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                        ->whereNotNull('entree_produits.Id_Fournisseur');
                })
                ->leftJoin('agences as source_agences', function ($join) {
                    $join->on('entree_produits.Id_Agence_source', '=', 'source_agences.id')
                        ->whereNull('entree_produits.Id_Fournisseur');
                })
                ->join('users', 'entree_produits.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
                ->select(
                    'entree_produits.*',
                    DB::raw('COALESCE(fournisseurs.DenominationSociale, source_agences.NomAgence) as DenominationSociale'),
                    'users.name',
                    'agences.NomAgence'
                )
                ->whereMonth('entree_produits.created_at', $currentMonth)
                ->whereYear('entree_produits.created_at', $currentYear)
                ->orderBy('entree_produits.id', 'desc');

            $agence = Agence::find($site_id);

            if ($agence->NomAgence === 'Siège') {
                $entree_produits = $query->get();
            } else {
                $entree_produits = $query->where('agences.id', $site_id)->get();
            }


            $entrer_produits = DB::table('entrer_produits')
                ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'entrer_produits.Id_Magasin', '=', 'magasins.id')
                ->select('entrer_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
                ->where('entrer_produits.Id_Entree_Produit', '=', 0)
                // ->whereIn('magasins.id', $magasin_ids)
                ->get();

            $annees = EntreeProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $produits = Produit::orderBy('created_at', 'desc')->get();
            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();


            $agences = DB::table('agences')
                ->leftJoin('magasins', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->orderBy('created_at', 'desc');



            if ($site_id == "1") {
                $agences =  $agences = Agence::all();
            }else{
                $agences = Agence::where('id', '=', $site_id)->get();
            }



            return view('page.produit.entree.entree', [
                'entree_produits' => $entree_produits,
                'entrer_produits' => $entrer_produits,
                'produits' => $produits,
                'fournisseurs' => $fournisseurs,
                'agences' => $agences,
                'categories' => $categories,
                'annees' => $annees
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    //retourne la vue d'ajout une nouvelle entree
    public function  Create(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->entre_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $produits = Produit::where('Type', 'PRODUIT')->where('Statut', '=', 'ACTIF')->pluck('Designation', 'Reference');
            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->where('Statut_fournisseur', '=', '1')->get();

            if (is_array(getIdAgenceByUser())) {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')->get();
            } else {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')
                    ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
            }

            $listeAgence = Agence::all();



            // $magasins = Magasin::
            return view('page.produit.entree.nouveau', [
                'produits' => $produits,
                'fournisseurs' => $fournisseurs,
                'magasins' => $magasins,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');
        // try {
        // dd($request);

        $validator = Validator::make(
            $request->all(),
            [
                'fournisseur' => 'required',
                'observation' => 'required',
                'inputs.*.produit' => 'required',
                //  'inputs.*.designation' => 'required',
                'inputs.*.magasin' => 'required',
                'inputs.*.quantity' => 'required',
                'inputs.*.prix_achat' => 'required',
            ],
            [
                'fournisseur' => 'Fournisseur requis',
                'observation' => 'Observations requis',
                'inputs.*.produit' => "produit(s) requis",
                // 'inputs.*.designation' => "designation(s) requise(s)",
                'inputs.*.magasin' => "magasin(s) requis",
                'inputs.*.quantity' => "quantite(s) requise(s)",
                'inputs.*.prix_achat' => "prix achat(s) requis",
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Obtenez le dernier chiffre de l'année actuelle
        $lastDigitOfYear = substr(Carbon::now()->year, -2);
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->entre_produit ?? '';

        // // Obtenez le dernier numéro de référence enregistré
        // $lastReference = EntreeProduit::count();
        // Obtenez le dernier numéro de référence enregistré
        $lastReference = EntreeProduit::where('Id_Agence', '=', session()->get('site_id'))->count();


        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            // $lastReference = EntreeProduit::orderBy('id', 'desc')->first();
            $lastReference = EntreeProduit::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Entree, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        $site_id = session()->get('site_id');



        // dd($site_id);
        //variable de creation entree produit
        $reference_entree = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
        $date_entree =  Carbon::now();
        $fournisseur = $request->input('fournisseur');
        $observation = $request->input('observation');
        $reponse_consignation = $request->input('entree_consignation');


        // dd(auth()->user()->id);

        $entree_produit = new EntreeProduit();
        $entree_produit->Date_Entree = $date_entree;
        $entree_produit->Id_Utilisateur = auth()->user()->id;
        $entree_produit->Reference_Entree = $reference_entree;
        $entree_produit->Observations = $observation;
        $entree_produit->Id_Agence = $site_id;
        $entree_produit->Id_Fournisseur = $fournisseur;
        $entree_produit->save();


        $entree_id = $entree_produit->id;

        if ($reponse_consignation == 1) {

            $save_consignation = new ConsignationEntree();
            $save_consignation->ref_entree = $reference_entree;
            $save_consignation->entree_id = $entree_id;
            $save_consignation->fournisseur_id = $fournisseur;
            $save_consignation->statut = 'EN_COURS';
            $save_consignation->user_id = auth()->user()->id;
            $save_consignation->agence_id = $site_id;
            $save_consignation->save();

            $consignation_id = $save_consignation->id;
        }


        foreach ($request->inputs as $value) {

            $produit = Produit::where('Reference', '=', $value['produit'])->first();

            $magasin_formate = explode('-', $value['magasin'], 2);
            $id_magasin = trim($magasin_formate[0]);
            // dd(trim($id_magasin[0]));
            $entrer_produit = new EntrerProduit();
            $entrer_produit->Id_Entree_Produit = $entree_produit->id;
            $entrer_produit->Id_Produit = $produit->id;
            $entrer_produit->Id_Magasin = $id_magasin;
            $entrer_produit->Qte_Entree = $value['quantity'];
            $entrer_produit->Prix_Achat_Net = $value['prix_achat'];
            $entrer_produit->save();


            //Historiq stock coté entrée
            $historique_entree_produit = new StockHistories();
            $historique_entree_produit->Date = $date_entree;
            $historique_entree_produit->agence_id = $site_id;
            $historique_entree_produit->Motif = $observation;
            $historique_entree_produit->Justificatif = $reference_entree;
            $historique_entree_produit->operation = 'ENTREE';
            $historique_entree_produit->type_operation = 'ENTREE';
            $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
            $historique_entree_produit->Id_Produit = $produit->id;
            $historique_entree_produit->Id_Magasin = $id_magasin;
            $historique_entree_produit->Quantite = $value['quantity'];
            $historique_entree_produit->save();

            $stock = Stock::where('Id_Produit', '=', $produit->id)->where('Id_Magasin', '=', $id_magasin)->first();

            $stock_emballage = StockEmballage::where('Id_Magasin', '=', $id_magasin)->where('Id_Emballage', '=', $produit->Emballage_id)->first();

            if ($stock) {
                $quantite_stockee = $stock->Qte_stockee + $value['quantity'];
                $prix_achat_net = (($stock->Prix_Achat_Net * $stock->Qte_stockee) + ($value['prix_achat'] * $value['quantity'])) / ($stock->Qte_stockee + $value['quantity']);
                $stock->Qte_stockee = $quantite_stockee;
                $stock->Prix_Achat_Net = $prix_achat_net;
                $stock->update();
            } else {
                $stocker_produit = new Stock();
                $stocker_produit->Id_Produit = $produit->id;
                $stocker_produit->Id_Magasin = $id_magasin;
                $stocker_produit->Qte_stockee = $value['quantity'];
                $stocker_produit->Prix_Achat_Net = $value['prix_achat'];
                $stocker_produit->save();
            }
            $produitId = $produit->id;

            if ($produit->type_emballage == 'EMBALLAGE_RECUPERABLE') {

                // dd('EMBALLAGE_RECUPERABLE consi...');

                $Produitemballage = Produit::where('id', $produitId)->first();
                $idEmballage = $Produitemballage->Emballage_id;
                if ($idEmballage != null) {
                    $stockEmballage = StockEmballage::where('Id_Emballage', $idEmballage)->where('Id_Magasin', $id_magasin)->first();
                    if (empty($stockEmballage)) {
                        $stockEmballage = new StockEmballage();
                        $stockEmballage->Id_Emballage = $idEmballage;
                        $stockEmballage->Id_Magasin = $id_magasin;
                        $stockEmballage->Qte_stockee = 0;
                        $stockEmballage->Prix_Achat_Net = 0;
                        $stockEmballage->Enregistrer_par = auth()->user()->id;
                        $stockEmballage->save();
                    }


                    // dd('repponse consi...', $reponse_consignation, $stockEmballage->id);

                    if ($reponse_consignation == 0) {
                        $stockEmballage->Qte_stockee  -= $value['quantity'];
                        $stockEmballage->update();


                        //Historiq stock emballage
                        $historique_sortie_emballage = new StockEmballageHistories();
                        $historique_sortie_emballage->Date = Carbon::now();
                        $historique_sortie_emballage->agence_id = $site_id;
                        $historique_sortie_emballage->Motif = "Sortie d'emballage sur une entrée de produit ayant un emballage";
                        $historique_sortie_emballage->Justificatif = $reference_entree;
                        $historique_sortie_emballage->operation = 'SORTIE';
                        $historique_sortie_emballage->type_operation = 'SORTIE';
                        $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                        $historique_sortie_emballage->Id_Emballage = $idEmballage;
                        $historique_sortie_emballage->Id_Magasin = $id_magasin;
                        $historique_sortie_emballage->Quantite = $value['quantity'];
                        $historique_sortie_emballage->save();
                    }

                    // if ($reponse_consignation == 1) {
                    //         //  dd('ligne entree consi...');
                    //     $save_ligne_consignation = new LigneConsignationEntree();
                    //     $save_ligne_consignation->consignation_id = $consignation_id;
                    //     $save_ligne_consignation->emballage_id = $idEmballage;
                    //     $save_ligne_consignation->produit_id = $produitId;
                    //     $save_ligne_consignation->Qte = $value['quantity'];
                    //     $save_ligne_consignation->restituee = 0;
                    //     $save_ligne_consignation->facturee = 0;
                    //     $save_ligne_consignation->stock_emballage_id = $stockEmballage->id;
                    //     $save_ligne_consignation->stock_id = $stock->id;
                    //     $save_ligne_consignation->save();
                    // }
                }
            }
        }
        return to_route('page.entree.entree')->with('success', 'L\'entree a bien été ajoutée');
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function getEntrerProduit($id)
    {
        $this->authorize('consulter-entree-produit');
        $site_id = session()->get('site_id');
        try {
            $query = DB::table('entree_produits')
                ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_produits.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
                // ->join('utilisateur', 'entree_produits.Id_Utilisateur', '=', 'utilisateurs.id')
                ->select('entree_produits.*', 'fournisseurs.DenominationSociale as DenominationSociale', 'users.name', 'agences.NomAgence')
                ->orderBy('entree_produits.id', 'desc');


            $agence = Agence::find($site_id);

            if ($agence->NomAgence === 'Siège') {
                $entree_produits = $query->get();
            } else {
                $entree_produits = $query->where('agences.id', $site_id)->get();
            }

            // dd($entree_produits);

            // $entrer_produits = EntrerProduit::where('Id_Entree_Produit', '=', $id)->get();
            $entrer_produits = DB::table('entrer_produits')
                ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'entrer_produits.Id_Magasin', '=', 'magasins.id')
                ->select('entrer_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
                ->where('entrer_produits.Id_Entree_Produit', '=', $id)
                ->orderBy('entrer_produits.id', 'desc')
                ->get();

            $produits = Produit::orderBy('created_at', 'desc')->get();
            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
            $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();
            $annees = EntreeProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            // dd($entrer_produits);
            // Utilisez la méthode with() pour passer des données et des messages à la vue
            return view('page.produit.entree.entree', [
                'entree_produits' => $entree_produits,
                'entrer_produits' => $entrer_produits,
                'produits' => $produits,
                'fournisseurs' => $fournisseurs,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ])
                ->with('success', $entrer_produits->count() . ' produit(s) entré(s) trouvé(s)');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }


        // return Redirect::route('page.entree.entree')->with(
        //          [
        //             'entrer_produits' => $entrer_produits,
        //             'entree_produits' => $entree_produits
        //         ]
        // );
    }
    public function filterEntree(Request $request)
    {
        $this->authorize('consulter-entree-produit');
        //try{
        $site_id = session()->get('site_id');

        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $annee = $request->query('annee');

        // Vérification de l'utilisateur et de l'agence
        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');

        $query = DB::table('entree_produits')
            ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('users', 'entree_produits.Id_Utilisateur', '=', 'users.id')
            ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
            // ->join('utilisateur', 'entree_produits.Id_Utilisateur', '=', 'utilisateurs.id')
            ->select('entree_produits.*', 'fournisseurs.DenominationSociale as DenominationSociale', 'users.name', 'agences.NomAgence')
            // ->where('agences.id', $site_id)
            ->orderBy('entree_produits.id', 'desc');


        $query_agence = Agence::find($site_id);

        $entrer_produits = DB::table('entrer_produits')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'entrer_produits.Id_Magasin', '=', 'magasins.id')
            ->select('entrer_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
            ->where('entrer_produits.Id_Entree_Produit', '=', 0)
            ->get();

        $produits = Produit::orderBy('created_at', 'desc')->get();

        $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('created_at', 'desc')->get();
        $categories = CategorieProduit::orderBy('created_at', 'desc')->get();

        if (is_array(getIdAgenceByUser())) {
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')->get();
        } else {
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
        }




        if ($annee) {
            $query->whereYear('entree_produits.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('entree_produits.created_at', $month);
            $query->whereYear('entree_produits.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('entree_produits.created_at', '>=', $startDate)
                ->where('entree_produits.created_at', '<=', $endDate);
        }

        $entree_produits = $query->get();


        if ($query_agence->NomAgence === 'Siège') {
            $entree_produits = $query->get();
        } else {
            $entree_produits = $query->where('agences.id', '=', $site_id)->get();
        }



        $magasin_ids = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->where('agences.id', '=', $site_id)
            ->orderBy('magasins.created_at', 'desc')
            ->pluck('magasins.id');

        $entrer_produits = DB::table('entrer_produits')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'entrer_produits.Id_Magasin', '=', 'magasins.id')
            ->select('entrer_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
            ->where('entrer_produits.Id_Entree_Produit', '=', 0)
            // ->whereIn('magasins.id', $magasin_ids)
            ->get();

        $annees = EntreeProduit::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $produits = Produit::orderBy('created_at', 'desc')->get();
        $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('created_at', 'desc')->get();
        $categories = CategorieProduit::orderBy('created_at', 'desc')->get();


        $magasins = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select('magasins.*', 'agences.NomAgence')
            ->whereIn('magasins.id', $magasin_ids)
            ->orderBy('created_at', 'desc')->get();




        return view('page.produit.entree.entree', [
            'entree_produits' => $entree_produits,
            'entrer_produits' => $entrer_produits,
            'produits' => $produits,
            'fournisseurs' => $fournisseurs,
            'magasins' => $magasins,
            'categories' => $categories,
            'annees' => $annees
        ]);
        /* }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

    public function imprimerEntree(Request $request)
    {
        $this->authorize('imprimer-liste-entrees-produits');
        // try {
        // dd($request);

        $site_id = session()->get('site_id');

        $reponse = $request->input('response');

        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $magasin = $request->input('magasin');
        $fournisseur = $request->input('fournisseur');
        $categorie = $request->input('categorie');
        $produit = $request->input('produit');

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


        // requete de fournisseur
        $query_fournisseur = DB::table('fournisseurs')
            ->join('entree_produits', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->whereBetween('entree_produits.Date_Entree', [$date_debut_periode, $date_fin_periode])
            ->select('fournisseurs.id', 'fournisseurs.DenominationSociale') // Sélectionner des colonnes spécifiques
            ->distinct();


        // requete de fournisseur
        // $query_magasin = DB::table('magasins')
        //     ->join('entree_produits', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
        //     ->join('entree_produits', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
        //     // ->whereBetween('entree_produits.Date_Entree', [$date_debut_periode, $date_fin_periode])
        //     ->select('fournisseurs.id', 'fournisseurs.DenominationSociale') // Sélectionner des colonnes spécifiques
        //     ->distinct();


        $query_entrer_produit = DB::table('entrer_produits')
            ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
            ->join('produits', 'entrer_produits.Id_Produit', '=', 'produits.id')
            ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
            ->join('magasins', 'entrer_produits.Id_Magasin', '=', 'magasins.id')
            // ->where('entree_produits.Id_Agence', '=', $site_id)
            ->select(
                'entrer_produits.Id_Entree_Produit',
                'entrer_produits.Id_Magasin',
                'magasins.NomMagasin',
                'entree_produits.Reference_Entree',
                DB::raw('SUM(entrer_produits.Qte_Entree) as total_quantity'), // Somme des quantités
                DB::raw('SUM(entrer_produits.Prix_Achat_Net) as total_prix'), // Somme des prix
                DB::raw('MAX(entrer_produits.Id_Entree_Produit) as Id_Entree_Produit'),
                DB::raw('MAX(fournisseurs.DenominationSociale) as DenominationSociale'),
                DB::raw('MAX(entrer_produits.Id_Produit) as Id_Produit'),
                DB::raw('MAX(entree_produits.Id_Fournisseur) as Id_Fournisseur'),
                DB::raw('MAX(agences.NomAgence) as NomAgence'),
                // DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                // DB::raw('MAX(magasins.id) as magasin_id'),
            )
            ->whereBetween('entrer_produits.created_at', [$date_debut_periode, $date_fin_periode])
            ->groupBy('entrer_produits.Id_Entree_Produit', 'entrer_produits.Id_Magasin', 'magasins.NomMagasin',);


        $query_ligne_entree_produit = DB::table('entrer_produits')
            ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', '=', 'entree_produits.id')
            ->join('produits', 'entrer_produits.Id_Entree_Produit', '=', 'produits.id')
            ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
            ->join('magasins', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'entrer_produits.Id_Produit',
                DB::raw('SUM(entrer_produits.Qte_Entree) as total_quantity'), // Somme des quantités
                DB::raw('SUM(entrer_produits.Prix_Achat_Net) as total_prix'), // Somme des prix
                DB::raw('MAX(entrer_produits.Id_Entree_Produit) as Id_Entree_Produit'),
                DB::raw('MAX(fournisseurs.DenominationSociale) as DenominationSociale'),
                DB::raw('MAX(agences.NomAgence) as NomAgence')
            )
            ->groupBy('entrer_produits.Id_Produit')->get();


        //  dd($query_fournisseur, $query_entrer_produit, $query_ligne_entree_produit);


        if ($fournisseur === 'Tous') {
            $get_fournisseur = $query_fournisseur->get();
        } else {
            $get_fournisseur = $query_fournisseur->where('fournisseurs.id', '=', $fournisseur)->get();
        }

        //  if($magasin === 'Tous'){
        //     $get_magasin = $query_magasin->get();
        //  }else{
        //     $get_magasin = $query_magasin->where('magasins.id', '=', $fourniseur)->get();
        //  }


        if ($magasin === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            // dd('premier');

            $get_entree_produit = $query_entrer_produit->get();

            // dd($get_fournisseur_entree_produits, $get_entree_produits, $all_entree, $get_magasin);
            if (count($get_entree_produit) <= 0) {
                return to_route('page.entree.entree')->with('error', 'Aucunes données trouvées!');
            }
        }

        // dd('oizo', $magasin);

        if ($magasin !== 'Toutes' && $fournisseur === 'Tous') {
            $get_entree_produit = $query_entrer_produit->where('entree_produits.Id_Agence', '=', $magasin)->get();
        }elseif ($magasin === 'Toutes' && $fournisseur !== 'Tous') {
            $get_entree_produit = $query_entrer_produit->where('entree_produits.Id_Fournisseur', '=', $magasin)->get();
        }elseif ($magasin !== 'Toutes' && $fournisseur !== 'Tous') {
            $get_entree_produit = $query_entrer_produit->where('entree_produits.Id_Agence', '=', $magasin)->where('entrer_produits.Id_Magasin', '=', $magasin)->get();
        }else{
            $get_entree_produit = $query_entrer_produit->get();
        }

        if (count($get_fournisseur) <= 0) {
            return to_route('page.entree.entree')->with('error', 'Aucunes données trouvées!');
        }

        if (count($get_entree_produit) <= 0) {
            return to_route('page.entree.entree')->with('error', 'Aucunes données trouvées!');
        }

        // dd($get_entree_produit);

        if ($fournisseur !== 'Tous') {
            $fournisseur = Fournisseur::find($fournisseur);
        }
        if ($magasin !== 'Toutes') {
            $magasin = Agence::find($magasin);
        }

    //  dd('oizo', $magasin);
        // if ($produit !== 'Tous') {
        //     $produit = Produit::find($produit);
        // }
        // if ($categorie !== 'Toutes') {
        //     $categorie = CategorieProduit::find($categorie);
        // }
        // dd($magasin, $fournisseur, $produit, $categorie);
        // dd($debut_periode, $fin_periode);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        // dd($imageEntetePied,);
        $data = [
            'imageEntetePied' => $imageEntetePied,
            'texteEntetePied' => $texteEntetePied,
            'debut_periode' => $debut_periode,
            'fin_periode' => $fin_periode,
            'fournisseur' => $fournisseur,
            'produit' => $produit,
            'magasin' => $magasin,
            'categorie' => $categorie,
            'getEntree' => $get_entree_produit,
            'getFournisseur' => $get_fournisseur,
            // 'getMagasin' => $get_magasin,
            // 'getFournisseurEntreeProduits' => $get_fournisseur_entree_produits
        ];


        // dd($data);

        if ($reponse === 'imprimer') {
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            $htmlContent = view('page.produit.entree.imprimer.imprimer', $data)->render();

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ENTREE';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }
        if ($reponse === 'exporter') {

            $prefixe = 'entree_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new EntreeExport($data), $nom_excel);
        }

        if ($reponse === 'afficher') {
            // dd('ici', $data);
            return view('page.produit.entree.entree', $data);
        }



        // return view('page.produit.entree.imprimer.imprimer', $data);

        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function storeFournisseur(Request $request)
    {
        $this->authorize('creer-fournisseur');
        try {
            $request->validate([
                'DenominationSociale' => ['required'],
                'AdresseFournisseur' => [''],
                'TelephoneFixe' => [''],
                'TelephoneMobile' => [''],
                'AdresseMail' => [''],
                'Pays' => [''],
                'NumeroIfu' => [''],
                'Statut_fournisseur' => ['required'],
            ]);

            $existingClient = Fournisseur::where('DenominationSociale', $request->DenominationSociale)->first();
            if ($existingClient) {
                return response()->json([
                    'success' => false,
                ]);
            }
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');


            $fourniseurs = new Fournisseur();
            $fourniseurs->DenominationSociale = $request->DenominationSociale;
            $fourniseurs->AdresseFournisseur = $request->AdresseFournisseur;
            $fourniseurs->TelephoneFixe = $request->TelephoneFixe;
            $fourniseurs->TelephoneMobile = $request->TelephoneMobile;
            $fourniseurs->AdresseMail = $request->AdresseMail;
            $fourniseurs->Pays = $request->Pays;
            $fourniseurs->NumeroIfu = $request->NumeroIfu;
            $fourniseurs->Statut_fournisseur = $request->Statut_fournisseur;
            $fourniseurs->user_id = $user_connecterId;
            $fourniseurs->save();

            return response()->json([
                'success' => true,
                'newCategoryId' => $fourniseurs->id,
                'newCategoryName' => $fourniseurs->DenominationSociale,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function storeMagasinentree(Request $request)
    {
        //$this->authorize('creer-fournisseur');
        try {
            $request->validate([
                'NomMagasin' => ['required'],
                'agence_id' => ['required'],
                'Statut_Magasin' => ['required'],
            ]);

            $existingMagasin = Magasin::where('NomMagasin', $request->NomMagasin)
                ->where('agence_id', $request->agence_id)
                ->first();

            if ($existingMagasin) {
                return back()->with('error', "Un magasin avec le même nom existe déjà dans l'agence.");
            }
            $user_connecterId = auth()->user()->id;
            $magasin = new Magasin();
            $magasin->NomMagasin = $request->NomMagasin;
            $magasin->agence_id = $request->agence_id;
            $magasin->Statut_Magasin = $request->Statut_Magasin;
            $magasin->Enregistrer_par = $user_connecterId;
            $magasin->save();

            return response()->json([
                'success' => true,
                'newMagasinId' => $magasin->id,
                'newMagasinName' => $magasin->NomMagasin,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function entreeImprimer(Request $request)
    {
        // dd($request);
        $this->authorize('imprimer-liste-entrees-produits');
        try {
            // dd($request);

            $id_entree = $request->input('id_entree');
            $reponse = $request->input('reponse');

            //  dd($id_entree);

            //  $entree_produits = EntreeProduit::where('id', '=', $id_sortie)->get();
            $entree_produits = DB::table('entree_produits')
                ->join('agences', 'entree_produits.Id_Agence', '=', 'agences.id')
                ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_produits.Id_Utilisateur', '=', 'users.id')
                ->select('entree_produits.*', 'agences.NomAgence', 'users.name', 'fournisseurs.DenominationSociale')
                ->where('entree_produits.id', '=', $id_entree)
                ->get();
            // $transferers = Transferer::where('transferers.Id_Transfert_Produit', '=', $id_transfert)->get();
            $entrer_produits = DB::table('entrer_produits')
                ->join('entree_produits', 'entrer_produits.Id_Entree_Produit', 'entree_produits.id')
                ->join('magasins', 'entrer_produits.Id_Magasin', 'magasins.id')
                ->join('produits', 'entrer_produits.Id_Produit', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                ->select('entrer_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'magasins.NomMagasin')
                ->where('entrer_produits.Id_Entree_Produit', '=', $id_entree)
                ->get();

            //  dd($entree_produits, $entrer_produits);
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();



            $data =  [
                'entree_produits' => $entree_produits,
                'entrer_produits' => $entrer_produits,
                'texteEntetePied' => $texteEntetePied,
                'imageEntetePied' => $imageEntetePied,
            ];
            if ($reponse === 'imprimer') {

                // dd($reponse);

                $htmlContent = view('page.produit.entree.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'ENTREE';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            } else {

                $prefixe = 'entree_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new EntreeActionPrintExport($data), $nom_excel);
            }

            // $pdf = Pdf::loadView('', $data);
            // return $pdf->download($nom_pdf);

            //  return view('page.produit.entree.imprimer.imprimer-action', $data);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        // dd('bien ici', $request->all());
        $this->authorize('effectuer-entrer-produit');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'fournisseur' => 'required',
            'entree_consignation' => '',
        ]);

        $fournisseur = $request->input('fournisseur');
        $entree_consignation = $request->input('entree_consignation');


        try {
            // Lancer l'importation du fichier Excel
            Excel::import(new EntreeImport($fournisseur, $entree_consignation), $request->file('file'));

            return redirect()->back()->with('success', 'Importation réussie.');
        } catch (Exception $e) {
            // Attraper l'exception levée dans StockImport et afficher un message d'erreur
            return redirect()->back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }
    }

    public function entreeImporter(Request $request)
    {

        // $produits = Produit::where('Type', '=', 'PRODUIT')->get();

        $produits = DB::table('produits')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('produits.*', 'categorie_produits.Libelle')
            ->where('produits.Type', '=', 'PRODUIT')
            ->get();

        $prefixe = 'importation_produit';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new ImportationProduitEntreeExport($produits), $nom_excel);
    }
}
