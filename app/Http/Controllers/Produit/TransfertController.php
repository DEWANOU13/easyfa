<?php

namespace App\Http\Controllers\Produit;

use App\Exports\TransfertActionPrintExport;
use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Transferer;
use Illuminate\Http\Request;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\SortieProduit;
use App\Models\SortirProduit;
use App\Models\StockHistories;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use App\Models\TransfertProduit;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;


use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransfertPeriodeExport;
use Illuminate\Support\Facades\Validator;

class TransfertController extends Controller
{
    // retourne la vue de transfert
    public function  index(Request $request)
    {
        $this->authorize('consulter-transfert-produit');
        try {
            $annees = TransfertProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $site_id = session()->get('site_id');
            // $transfert_produits = TransfertProduit::all();
            $query = DB::table('transfert_produits')
                ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'transfert_produits.Id_Agence', '=', 'agences.id')
                ->join('magasins', 'transfert_produits.Id_Magasin_Source', '=', 'magasins.id')
                ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', '=', 'magasin_destination.id')
                // ->join('agences as agences_source', 'magasins.agence_id', '=', 'agences_source.id')
                ->join('agences as agences_destination', 'magasin_destination.agence_id', '=', 'agences_destination.id')
                ->select('magasins.NomMagasin as NomMagasinSource', 'magasin_destination.NomMagasin as NomMagasinDestination', 'transfert_produits.*', 'users.name', 'agences.NomAgence', 'agences_destination.NomAgence as NomAgenceDestination')
                ->whereMonth('transfert_produits.created_at', $currentMonth)
                ->whereYear('transfert_produits.created_at', $currentYear)
                ->orderBy('transfert_produits.id', 'desc');


                $query_agence = Agence::find($site_id);


                if($query_agence->NomAgence === 'Siège'){
                    $transfert_produits = $query->get();
                }else{
                    $transfert_produits = $query->where('agences.id', '=', $site_id)->get();
                }

            // dd($transfert_produits);
            $transferers = DB::table('transferers')
                ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                ->select('transferers.*', 'produits.Reference', 'produits.Designation')
                ->where('transferers.Id_Transfert_Produit', '=', 0)
                ->get();

            $produits = Produit::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();
            $site_id = $request->session()->get('site_id');

            // dd($site_id);

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            // dd($transfert_produits);


            return view('page.produit.transfert.transfert',
                [
                    'transfert_produits' => $transfert_produits,
                    'transferers' => $transferers,
                    'magasins' => $magasins,
                    'categories' => $categories,
                    'produits' => $produits,

                    'annees' => $annees,

                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }
    public function filterTransfert(Request $request)
    {
        $this->authorize('consulter-transfert-produit');
        try {
            $site_id =session()->get('site_id');
            $annees = TransfertProduit::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            $query = DB::table('transfert_produits')
                ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'transfert_produits.Id_Agence', '=', 'agences.id')
                ->join('magasins', 'transfert_produits.Id_Magasin_Source', '=', 'magasins.id')
                ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', '=', 'magasin_destination.id')
                ->select('magasins.NomMagasin as NomMagasinSource', 'magasin_destination.NomMagasin as NomMagasinDestination', 'transfert_produits.*', 'users.name', 'agences.NomAgence')
                // ->where('agences.id', $site_id)
                ->orderBy('transfert_produits.id', 'desc');

                $query_agence = Agence::find($site_id);

                if ($annee) {
                    $query->whereYear('transfert_produits.created_at', $annee);
                }

                if ($month && !$startDate && !$endDate) {
                    // Si seul le mois est fourni, appliquer le filtre par mois et année
                    $query->whereMonth('transfert_produits.created_at', $month);
                    $query->whereYear('transfert_produits.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
                }

                if ($startDate && $endDate && !$month && !$annee) {
                    // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                    $query->where('transfert_produits.created_at', '>=', $startDate)
                          ->where('transfert_produits.created_at', '<=', $endDate);
                }


            if($query_agence->NomAgence === 'Siège'){
                $transfert_produits = $query->get();
            }else{
                $transfert_produits = $query->where('agences.id', '=', $site_id)->get();
            }



            $transferers = DB::table('transferers')
                ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                ->select('transferers.*', 'produits.Reference', 'produits.Designation')
                ->where('transferers.Id_Transfert_Produit', '=', 0)
                ->get();

            $produits = Produit::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();
            $site_id = $request->session()->get('site_id');

            // dd($site_id);

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            // dd($transfert_produits);


            return view(
                'page.produit.transfert.transfert',
                [
                    'transfert_produits' => $transfert_produits,
                    'transferers' => $transferers,
                    'magasins' => $magasins,
                    'categories' => $categories,
                    'produits' => $produits,

                    'annees' => $annees,

                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    //retourne la vue d'ajout une nouvelle entree
    public function  create(Request $request)
    {
        $this->authorize('effectuer-transfert-produit');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stocks')
                ->join('produits', 'stocks.Id_produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
                ->where('stocks.Qte_stockee', '>', 0)
                // ->where('produits.Statut', '=', 'PRODUIT')
                ->get();

            $site_id = session()->get('site_id');



            $magasin_source = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $magasin_destination = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')->orderBy('created_at', 'desc')
                ->where('magasins.agence_id', '<>', $site_id)->get();

            // dd($magasin_source, $magasin_destination);



            // if (is_array($site_id)) {
            //     $magasins = DB::table('magasins')
            //     ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            //     ->select('magasins.*', 'agences.NomAgence')
            //     ->whereIn('agence_id', $site_id)->orderBy('created_at', 'desc')->get();
            // }else{
            //     $magasins = DB::table('magasins')
            //     ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            //     ->select('magasins.*', 'agences.NomAgence')
            //     ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();
            // }

            $listeAgence = Agence::all();


            // dd($stock_produits);
            return view('page.produit.transfert.nouveau', [
                'stock_produits' => $stock_produits,
                // 'magasins' => $magasins,
                'magasin_source' => $magasin_source,
                'magasin_destination' => $magasin_destination,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-transfert-produit');
        // dd($request);
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    // 'fournisseur' => 'required',
                    'magasin_source' => 'required',
                    'magasin_destination' => 'required',
                    'inputs.*.produit' => 'required',
                    //  'inputs.*.designation' => 'required',
                    'inputs.*.magasin' => 'required',
                    // 'inputs.*.quantity' => 'required',
                    'inputs.*.quantity_transfer' => 'required',
                ],
                [
                    // 'fournisseur' => 'Fournisseur requis',
                    'magasin_source' => 'Magasin source required',
                    'magasin_destination' => 'Magasin destination required',
                    'observation' => 'Observations requis',
                    'inputs.*.produit' => "produit(s) requis",
                    // 'inputs.*.designation' => "designation(s) requise(s)",
                    'inputs.*.magasin' => "magasin(s) requis",
                    // 'inputs.*.quantity' => "quantite(s) requise(s)",
                    'inputs.*.quantity_transfer' => "Quantite à transférer requise(s)",
                ]

            );

            if ($validator->fails()) {
                // Si la validation échoue, retournez à la page précédente avec les erreurs
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            // $lastReference = TransfertProduit::count();
            $lastReference = TransfertProduit::where('Id_Agence','=' ,session()->get('site_id'))->count();

            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = TransfertProduit::orderBy('id', 'desc')->first();
                $lastReference = TransfertProduit::where('Id_Agence','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Transfert, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            //variable de creation entree produit
            $reference_transfert = "1/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
            $date_transfert =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $magasin_source = $request->input('magasin_source');
            $magasin_destination = $request->input('magasin_destination');

            // dd($magasin_source, $magasin_destination);

            $site_destination = DB::table('magasins')
                ->join('agences', 'agences.id', '=', 'magasins.agence_id')
                ->where('magasins.id', $magasin_destination)
                ->select('magasins.agence_id') // or specify 'agences.id' if you only need the ID
                ->first();
            //  dd($site_destination->agence_id);



            $site_destination_id = $site_destination->agence_id;
            // dd($site_destination_id);
            $site_id = session()->get('site_id');

            $transfert_produit = new TransfertProduit();
            $transfert_produit->Date_Transfert = $date_transfert;
            $transfert_produit->Id_Utilisateur = auth()->user()->id;
            $transfert_produit->Reference_Transfert = $reference_transfert;
            $transfert_produit->Observations = $observation;
            $transfert_produit->Id_Magasin_Source = $magasin_source;
            $transfert_produit->Id_Agence = $site_id;
            $transfert_produit->Id_Agence_Destination = $site_destination_id;
            $transfert_produit->Id_Magasin_Destination = $magasin_destination;
            $transfert_produit->Enregistrer_par = auth()->user()->id;
            $transfert_produit->save();


            $entree_produit = new EntreeProduit();
            $entree_produit->Date_Entree = $date_transfert;
            $entree_produit->Id_Utilisateur = auth()->user()->id;
            $entree_produit->Reference_Entree = $reference_transfert;
            $entree_produit->Observations = $observation;
            $entree_produit->Id_Agence = $site_destination_id;
            $entree_produit->Id_Agence_source = $site_id;
            // $entree_produit->Id_Fournisseur = $fournisseur;
            $entree_produit->save();


            $sortie_produit = new SortieProduit();
            $sortie_produit->Date_Sortie = $date_transfert;
            $sortie_produit->Id_Utilisateur = auth()->user()->id;
            $sortie_produit->Reference_Sortie = $reference_transfert;
            $sortie_produit->Observations = $observation;
            $sortie_produit->Id_Agence = $site_id;
            // $entree_produit->Id_Fournisseur = $fournisseur;
            $sortie_produit->save();



            foreach ($request->inputs as $value) {

                $produit_formate = explode(' ', $value['produit'], 2);
                $reference_produit = $produit_formate[1];
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode(' ', $produit_formate[1], 2);
                // $magasin_formate = explode('-', $value['magasin'], 2);
                // $id_magasin = trim($magasin_formate[0]);
                // dd($reference_produit);

                $produit = Produit::where('Reference', '=', $reference_produit_formate[0])->first();
                // dd($produit);

                $stock_destination = Stock::where('Id_produit', $produit->id)->where('Id_magasin', $magasin_destination)->first();
                // dd($stock_destination);


                //By Maxime
                $stock_source = Stock::where('Id_produit', $produit->id)->where('Id_magasin', $magasin_source)->first();
                // dd($stock_source->id, $stock_destination->id);
                $quantite_stockee_source = $stock_source->Qte_stockee - $value['quantity_transfer'];
                $prix_achat_source = $stock_source->Prix_Achat_Net;
                $stock_source->Qte_stockee = $quantite_stockee_source;
                $stock_source->update();

                // Concerne le magasin destination

                $entrer_produit = new EntrerProduit();
                $entrer_produit->Id_Entree_Produit = $entree_produit->id;
                $entrer_produit->Id_Produit = $produit->id;
                $entrer_produit->Id_Magasin = $magasin_destination;
                $entrer_produit->Qte_Entree = $value['quantity_transfer'];
                $entrer_produit->Prix_Achat_Net = $prix_achat_source;
                $entrer_produit->save();


                // L'historik
                $historique_entree_produit = new StockHistories();
                $historique_entree_produit->Date = $date_transfert;
                $historique_entree_produit->agence_id = $site_destination_id;
                $historique_entree_produit->Motif = 'Entrée produit par transfert';
                $historique_entree_produit->Justificatif = $reference_transfert;
                $historique_entree_produit->operation = 'ENTREE';
                $historique_entree_produit->type_operation = 'TRANSFERT';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Produit = $produit->id;
                $historique_entree_produit->Id_Magasin = $magasin_destination;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];;
                $historique_entree_produit->save();


                // Concerne le magasin source

                $sortir_produit = new SortirProduit();
                $sortir_produit->Id_Sortie_Produit = $sortie_produit->id;
                $sortir_produit->Id_Stock = $stock_source->id;
                $sortir_produit->Qte_Sortie = $value['quantity_transfer'];
                $sortir_produit->save();

                $stock = Stock::find($stock_source->id);


                //L'historik
                $historique_sortie_produit = new StockHistories();
                $historique_sortie_produit->Date = $date_transfert;
                $historique_sortie_produit->agence_id = $site_id;
                $historique_sortie_produit->Motif = 'Sortie produit par transfert';
                $historique_sortie_produit->Justificatif = $reference_transfert;
                $historique_sortie_produit->operation = 'SORTIE';
                $historique_sortie_produit->type_operation = 'TRANSFERT';
                $historique_sortie_produit->Id_Utilisateur = auth()->user()->id;
                $historique_sortie_produit->Id_Produit = $stock->Id_Produit;
                $historique_sortie_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_sortie_produit->Quantite = $value['quantity_transfer'];;
                $historique_sortie_produit->save();



                //By Maxime


                if ($stock_destination !== null) {
                    // $stock_source = Stock::where('Id_produit', $produit->id)->where('Id_magasin', $magasin_source)->first();
                    // // dd($stock_source->id, $stock_destination->id);
                    // $quantite_stockee_source = $stock_source->Qte_stockee - $value['quantity_transfer'];
                    // $prix_achat_source = $stock_source->Prix_Achat_Net;
                    // $stock_source->Qte_stockee = $quantite_stockee_source;
                    // $stock_source->update();

                    // Concerne le magasin source
                    // $sortir_produit = new SortirProduit();
                    // // $entrer_produit->Id_Sortie_Produit = $sortie_produit->id;
                    // $sortir_produit->Id_Stock = $stock_source->id;
                    // $sortir_produit->Qte_Sortie = $value['quantity_transfer'];
                    // $sortir_produit->save();

                    // $prix_achat_net_destination = $stock_source->Prix_Achat_Net;
                    // // Concerne le magasin de destination
                    // $entrer_produit = new EntrerProduit();
                    // // $entrer_produit->Id_Sortie_Produit = $sortie_produit->id;
                    // $entrer_produit->Id_Produit = $produit->id;
                    // $entrer_produit->Id_Magasin = $magasin_destination;
                    // $entrer_produit->Qte_Entree = $value['quantity_transfer'];
                    // $entrer_produit->Prix_Achat_Net = $prix_achat_net_destination;
                    // $entrer_produit->save();

                    // dd($stock_source, $magasin_destination);
                    $calcul_nouveau_prix_achat_destination = (($prix_achat_source * $value['quantity_transfer']) + ($stock_destination->Prix_Achat_Net * $stock_destination->Qte_stockee)) / ($stock_destination->Qte_stockee + $value['quantity_transfer']);
                    $quantite_stockee_destination = $stock_destination->Qte_stockee + $value['quantity_transfer'];
                    $stock_destination->Qte_stockee = $quantite_stockee_destination;
                    $stock_destination->Prix_Achat_Net = $calcul_nouveau_prix_achat_destination;
                    $stock_destination->update();

                    $transferer = new Transferer();
                    $transferer->Id_Produit = $produit->id;
                    $transferer->Id_Transfert_Produit = $transfert_produit->id;
                    $transferer->Qte_Transferee = $value['quantity_transfer'];
                    $transferer->save();



                    // dd('quantite_stockee_source', $quantite_stockee_source, 'quantite_stockee_destination', $quantite_stockee_destination);
                } else {
                    // dd('update, et ajout');
                    // $stock_source = Stock::where('Id_produit', $produit->id)->where('Id_magasin', $magasin_source)->first();
                    // $quantite_stockee_source = $stock_source->Qte_stockee - $value['quantity_transfer'];
                    // $prix_achat_source = $stock_source->Prix_Achat_Net;
                    // $stock_source->Qte_stockee = $quantite_stockee_source;
                    // $stock_source->Prix_Achat_Net = $stock_source->Prix_Achat_Net;
                    // $stock_source->update();


                    // $prix_achat_net_destination = $stock_source->Prix_Achat_Net;
                    $stocker_destination = new Stock();
                    $stocker_destination->Id_produit  = $produit->id;
                    $stocker_destination->Id_Magasin  = $magasin_destination;
                    $stocker_destination->Qte_stockee = $value['quantity_transfer'];
                    $stocker_destination->Prix_Achat_Net = $prix_achat_source;
                    // dd($prix_achat_net_destination * $value['quantity_transfer']);
                    $stocker_destination->save();

                    // // Concerne le magasin source
                    // $sortir_produit = new SortirProduit();
                    // // $entrer_produit->Id_Sortie_Produit = $sortie_produit->id;
                    // $sortir_produit->Id_Stock = $stock_source->id;
                    // $sortir_produit->Qte_Sortie = $value['quantity_transfer'];
                    // $sortir_produit->save();

                    // $prix_achat_net_destination = $stock_source->Prix_Achat_Net;
                    // // Concerne le magasin de destination
                    // $entrer_produit = new EntrerProduit();
                    // // $entrer_produit->Id_Sortie_Produit = $sortie_produit->id;
                    // $entrer_produit->Id_Produit = $produit->id;
                    // $entrer_produit->Id_Magasin = $magasin_destination;
                    // $entrer_produit->Qte_Entree = $value['quantity_transfer'];
                    // $entrer_produit->Prix_Achat_Net = $prix_achat_net_destination;
                    // $entrer_produit->save();

                    $transferer = new Transferer();
                    $transferer->Id_Produit = $produit->id;
                    $transferer->Id_Transfert_Produit = $transfert_produit->id;
                    $transferer->Qte_Transferee = $value['quantity_transfer'];
                    $transferer->save();

                    // dd('quantite_stockee_source', $quantite_stockee_source, 'quantite_stockee_destination', $stocker_destination);
                }
            }
            return to_route('page.transfert.transfert')->with('success', 'Le transfert a bien été effectué');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getTransfertProduit(Request $request, $id)
    {
        // dd($id);
        $this->authorize('consulter-transfert-produit');
        try {
            $annees = TransfertProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
            $transfert_produits = TransfertProduit::all();
            // $transfert_produits = DB::table('transfert_produits')
            // ->join('magasins', 'transfert_produits.Id_Masagin_Source','=', 'magasins.id')
            // // ->join('magasins as magasin_destination', 'transfert_produits.Id_Masagin_Source','=', 'magasins.id')
            // ->select('magasins.NomMagasin as NomMagasin', 'transfert_produits.*')
            // ->get();
            // dd($transfert_produits);
            // $transferer = Transferer::where('Id_Transfert_Produit', '=', $id)->get();

            $produits = Produit::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();
            $site_id = $request->session()->get('site_id');
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $transferers = DB::table('transferers')
                ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                ->select('transferers.*', 'produits.Reference', 'produits.Designation')
                ->where('transferers.Id_Transfert_Produit', '=', $id)
                ->get();
            // dd($transferer);
            // dd($sortir_produits);

            return view(
                'page.produit.transfert.transfert',
                [
                    'transfert_produits' => $transfert_produits,
                    'transferers' => $transferers,
                    'magasins' => $magasins,
                    'categories' => $categories,
                    'produits' => $produits,
                    'annees' => $annees
                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }



        // ->with('success', $sortir_produits->count() . ' produit(s) entré(s) trouvé(s)');
        // return Redirect::route('page.entree.entree')->with(
        //          [
        //             'entrer_produits' => $entrer_produits,
        //             'entree_produits' => $entree_produits
        //         ]
        // );
    }

    public function imprimerTransfert(Request $request)
    {
        $this->authorize('imprimer-liste-transfert');
        $site_id = session()->get('site_id');
        try {
            $debut_periode = $request->input('date_debut_periode');
            $fin_periode = $request->input('date_fin_periode');
            $magasin_source = $request->input('magasin_source');
            $magasin_destination = $request->input('magasin_destination');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');
            $submit = $request->input('submit');

            $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
            $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {


                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                // dd($transfert_produit);

                $query = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }



                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();
                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    // ->where('transferers.Id_Produit', '=', $produit)
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    // ->where('transferers.Id_Produit', '=', $produit)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    // ->where('transferers.Id_Produit', '=', $produit)
                    // ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    // ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    // ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    // ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_produits')
                    ->join('transferers', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_produits.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_produits.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('produits', 'transferers.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->where('transfert_produits.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_produits.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_produits.Id_Agence', '=', $site_id)
                    ->select(
                        'transferers.Id_Transfert_Produit',
                        DB::raw('MAX(transfert_produits.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_produits.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_produits.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transferers.Id_Transfert_Produit')
                    ->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }

                $query = DB::table('transferers')
                    ->join('transfert_produits', 'transferers.Id_Transfert_Produit', 'transfert_produits.id')
                    ->join('produits', 'transferers.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('transferers.Id_Produit', '=', $produit)
                    ->whereBetween('transfert_produits.Date_Transfert', [$date_debut_periode, $date_fin_periode])
                    ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'transferers.*');

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('page.transfert.transfert')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous') {
                $magasin_source = Magasin::find($magasin_source);
            }
            if ($magasin_destination !== 'Tous') {
                $magasin_destination = Magasin::find($magasin_destination);
            }
            if ($produit !== 'Tous') {
                $produit = Produit::find($produit);
            }
            if ($categorie !== 'Toutes') {
                $categorie = CategorieProduit::find($categorie);
            }

            // dd($magasin_source, $magasin_destination, $categorie, $produit);
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data = [
                'texteEntetePied' => $texteEntetePied,
                'debut_periode' => $debut_periode,
                'fin_periode' => $fin_periode,
                'produit' => $produit,
                'magasin_source' => $magasin_source,
                'magasin_destination' => $magasin_destination,
                'categorie' => $categorie,
                'transfert_produits' => $transfert_produit,
                'getTransferer' => $get_request,
                'imageEntetePied' => $imageEntetePied
            ];

            $prefixe = 'TRANSFERT';
            $date_et_heure = date('Ymd_His');

            if($submit == 'PDF'){



                $options = new Options();
                $options->set('chroot', realpath(''));
                $dompdf = new Dompdf($options);


                $htmlContent = view('page.produit.transfert.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('TRANSFERT_'.$date_et_heure, array("Attachment" => false));
            }
            if($submit == 'EXCEL'){
                return Excel::download(new TransfertPeriodeExport($data), 'TRANSFERT_'.$date_et_heure.'.xlsx');
            }

         } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function transfertImprimer(Request $request)
    {
        // dd($request);
        $this->authorize('imprimer-liste-transfert');
        try {
            $id_transfert = $request->input('id_transfert');
            $reponse = $request->input('reponse');

            if (empty($id_transfert)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $transfert_produits = DB::table('transfert_produits')
                ->join('users', 'transfert_produits.Id_Utilisateur', '=', 'users.id')
                ->join('magasins as magasins_source', 'transfert_produits.Id_Magasin_Source', '=', 'magasins_source.id')
                ->join('magasins as magasins_destination', 'transfert_produits.Id_Magasin_Destination', '=', 'magasins_destination.id')
                ->join('agences', 'transfert_produits.Id_Agence', '=', 'agences.id')
                ->join('agences as agence_destinations', 'transfert_produits.Id_Agence_Destination', '=', 'agence_destinations.id')
                ->where('transfert_produits.id', '=', $id_transfert)
                ->select('transfert_produits.*', 'magasins_source.NomMagasin as NomMagasinSource', 'magasins_destination.NomMagasin as NomMagasinDestination', 'users.name', 'agences.NomAgence as NomAgenceSource', 'agence_destinations.NomAgence as NomAgenceDestination')
                ->get();




                if ($transfert_produits->isEmpty()) {
                    return redirect()->back()->with('warning', 'Transfert non trouvé');
            }

            $transferers = DB::table('transferers')
                ->join('produits', 'transferers.Id_Produit', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                ->select('transferers.Qte_transferee', 'produits.Id_Categorie', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle')
                ->where('transferers.Id_Transfert_Produit', '=', $id_transfert)
                ->get();

                $data_categorie_transfert = DB::table('transferers')
                ->join('produits', 'transferers.Id_Produit', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                ->select('produits.Id_Categorie', 'categorie_produits.Libelle')
                ->where('transferers.Id_Transfert_Produit', '=', $id_transfert)
                ->distinct()
                ->get();
            // dd($transferers, $data_categorie_transfert );
            if ($transferers->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucun produit transféré trouvé');
            }


            // dd($imageEntetePied );

            // if (!$imageEntetePied) {
            //     return redirect()->back()->with('warning', 'Image d\'entête/pied non trouvée');
            // }


        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $data = [
            'texteEntetePied' => $texteEntetePied,
            'imageEntetePied' => $imageEntetePied,
            'transfert_produits' => $transfert_produits,
            'transferers' => $transferers,
            'data_categorie_transfert' => $data_categorie_transfert,
        ];

        if($reponse === 'imprimer'){
            $htmlContent = view('page.produit.transfert.imprimer.imprimer-action', $data)->render();

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
            $prefixe = 'TRANS';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if($reponse === 'exporter'){
            $prefixe = 'transfert_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new TransfertActionPrintExport($data), $nom_excel);
        }



        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


}
