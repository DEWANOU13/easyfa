<?php

namespace App\Http\Controllers\emballage;

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
use App\Models\Emballage;
use App\Models\Transferer;
use Illuminate\Http\Request;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\SortieProduit;
use App\Models\SortirProduit;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Models\EntreeEmballage;
use App\Models\SortieEmballage;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\CategorieProduit;


use App\Models\PrefixeReference;
use App\Models\TransfertProduit;
use App\Models\CategorieEmballage;
use App\Models\TransfertEmballage;
use Illuminate\Support\Facades\DB;
use App\Models\EntreLigneEmballage;
use App\Http\Controllers\Controller;
use App\Models\SortieLigneEmballage;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransfertPeriodeExport;
use App\Models\StockEmballageHistories;
use App\Models\TransfertLigneEmballage;
use Illuminate\Support\Facades\Validator;
use App\Exports\TransfertEmballagePeriodeExport;

class TransfertEmballageController extends Controller
{
    public function  index(Request $request)
    {
        $this->authorize('consulter-transfert-produit');
        try {
            $annees = TransfertEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $site_id = session()->get('site_id');
            // $transfert_produits = TransfertProduit::all();
            $transfert_produits = DB::table('transfert_emballages')
                ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'transfert_emballages.Id_Agence', '=', 'agences.id')
                ->join('magasins', 'transfert_emballages.Id_Magasin_Source', '=', 'magasins.id')
                ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', '=', 'magasin_destination.id')
                // ->join('agences as agences_source', 'magasins.agence_id', '=', 'agences_source.id')
                ->join('agences as agences_destination', 'magasin_destination.agence_id', '=', 'agences_destination.id')
                ->select('magasins.NomMagasin as NomMagasinSource', 'magasin_destination.NomMagasin as NomMagasinDestination', 'transfert_emballages.*', 'users.name', 'agences.NomAgence', 'agences_destination.NomAgence as NomAgenceDestination')
                ->where(function ($query) use ($site_id) {
                    $query->where('agences_destination.id', $site_id)
                        ->orWhere('agences.id', $site_id);
                })
                ->whereMonth('transfert_emballages.created_at', $currentMonth)
                ->whereYear('transfert_emballages.created_at', $currentYear)
                ->orderBy('transfert_emballages.id', 'desc')
                ->get();

            // dd($transfert_produits);
            $transferers = DB::table('transfert_ligne_emballages')
                ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->select('transfert_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation')
                ->where('transfert_ligne_emballages.Id_Transfert_Emballage', '=', 0)
                ->get();

            $produits = Emballage::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();
            $site_id = $request->session()->get('site_id');

            // dd($site_id);

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            // dd($transfert_produits);


            return view(
                'page.emballage.transfert_emballage.transfert',
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
            $stock_produits = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
                ->where('stock_emballages.Qte_stockee', '>', 0)
                ->get();

            $site_id = session()->get('site_id');



            $magasin_source = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $magasin_destination = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')->orderBy('created_at', 'desc')->get();


            $listeAgence = Agence::all();


            return view('page.emballage.transfert_emballage.nouveau', [
                'stock_produits' => $stock_produits,
                // 'magasins' => $magasins,
                'magasin_source' => $magasin_source,
                'magasin_destination' => $magasin_destination,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }


    public function store(Request $request)
    {
        $this->authorize('effectuer-transfert-produit');

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'magasin_source' => 'required',
                    'magasin_destination' => 'required',
                    'inputs.*.produit' => 'required',
                    'inputs.*.magasin' => 'required',
                    'inputs.*.quantity_transfer' => 'required',
                ],
                [
                    'magasin_source' => 'Magasin source required',
                    'magasin_destination' => 'Magasin destination required',
                    'observation' => 'Observations requis',
                    'inputs.*.produit' => "produit(s) requis",
                    'inputs.*.magasin' => "magasin(s) requis",
                    'inputs.*.quantity_transfer' => "Quantite à transférer requise(s)",
                ]

            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            $lastReference = TransfertEmballage::count();



            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = TransfertEmballage::orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Transfert, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            $reference_transfert = "1/{$lastDigitOfYear}/TEMB/{$incrementedReferenceNumber}";
            $date_transfert =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $magasin_source = $request->input('magasin_source');
            $magasin_destination = $request->input('magasin_destination');

            $site_destination = DB::table('magasins')
                ->join('agences', 'agences.id', '=', 'magasins.agence_id')
                ->where('magasins.id', $magasin_destination)
                ->select('magasins.agence_id') // or specify 'agences.id' if you only need the ID
                ->first();

            $site_destination_id = $site_destination->agence_id;
            $site_id = session()->get('site_id');


            $transfert_produit = new TransfertEmballage();
            $transfert_produit->Date_Transfert = $date_transfert;
            $transfert_produit->Id_Utilisateur = auth()->user()->id;
            $transfert_produit->Reference_Transfert = $reference_transfert;
            $transfert_produit->Observations = $observation;
            $transfert_produit->Id_Magasin_Source = $magasin_source;
            $transfert_produit->Id_Agence = $site_id;
            $transfert_produit->Id_Agence_Destination = $site_destination_id;
            $transfert_produit->Id_Magasin_Destination = $magasin_destination;
            $transfert_produit->save();

            $entree_produit = new EntreeEmballage();
            $entree_produit->Date_Entree = $date_transfert;
            $entree_produit->Id_Utilisateur = auth()->user()->id;
            $entree_produit->Reference_Entree = $reference_transfert;
            $entree_produit->Observations = $observation;
            $entree_produit->Id_Agence = $site_destination_id;
            $entree_produit->Id_Agence_source = $site_id;
            $entree_produit->save();

            $sortie_produit = new SortieEmballage();
            $sortie_produit->Date_Sortie = $date_transfert;
            $sortie_produit->Id_Utilisateur = auth()->user()->id;
            $sortie_produit->Reference_Sortie = $reference_transfert;
            $sortie_produit->Observations = $observation;
            $sortie_produit->Id_Agence = $site_id;
            $sortie_produit->save();

            foreach ($request->inputs as $value) {


                $produit_formate = explode(' ', $value['produit'], 2);
                $reference_produit = $produit_formate[1];
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode(' ', $produit_formate[1], 2);
                // $magasin_formate = explode('-', $value['magasin'], 2);
                // $id_magasin = trim($magasin_formate[0]);
                // dd($reference_produit);

                $produit = Emballage::where('Reference', '=', $reference_produit_formate[0])->first();

                $stock_destination = StockEmballage::where('Id_Emballage', $produit->id)->where('Id_magasin', $magasin_destination)->first();

                //By Maxime
                $stock_source = StockEmballage::where('Id_Emballage', $produit->id)->where('Id_magasin', $magasin_source)->first();
                $quantite_stockee_source = $stock_source->Qte_stockee - $value['quantity_transfer'];
                $prix_achat_source = $stock_source->Prix_Achat_Net;
                $stock_source->Qte_stockee = $quantite_stockee_source;
                $stock_source->update();

                // Concerne le magasin destination
                $entrer_produit = new EntreLigneEmballage();
                $entrer_produit->Id_Entree_Emballage = $entree_produit->id;
                $entrer_produit->Id_Emballage = $produit->id;
                $entrer_produit->Id_Magasin = $magasin_destination;
                $entrer_produit->Qte_Entree = $value['quantity_transfer'];
                $entrer_produit->Prix_Achat_Net = $prix_achat_source;
                $entrer_produit->save();


                // L'historik
                $historique_entree_produit = new StockEmballageHistories();
                $historique_entree_produit->Date = $date_transfert;
                $historique_entree_produit->agence_id = $site_destination_id;
                $historique_entree_produit->Motif = 'Entrée produit par transfert';
                $historique_entree_produit->Justificatif = $reference_transfert;
                $historique_entree_produit->operation = 'ENTREE EMBALLAGE';
                $historique_entree_produit->type_operation = 'TRANSFERT EMBALLAGE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Emballage = $produit->id;
                $historique_entree_produit->Id_Magasin = $magasin_destination;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];;
                $historique_entree_produit->save();

                // Concerne le magasin source
                $sortir_produit = new SortieLigneEmballage();
                $sortir_produit->Id_Sortie_Emballage = $sortie_produit->id;
                $sortir_produit->Id_Stock_Emballage = $stock_source->id;
                $sortir_produit->Qte_Sortie = $value['quantity_transfer'];
                $sortir_produit->save();

                $stock = StockEmballage::find($stock_source->id);

                //L'historik
                $historique_sortie_produit = new StockEmballageHistories();
                $historique_sortie_produit->Date = $date_transfert;
                $historique_sortie_produit->agence_id = $site_id;
                $historique_sortie_produit->Motif = 'Sortie produit par transfert';
                $historique_sortie_produit->Justificatif = $reference_transfert;
                $historique_sortie_produit->operation = 'SORTIE EMBALLAGE';
                $historique_sortie_produit->type_operation = 'TRANSFERT EMBALLAGE';
                $historique_sortie_produit->Id_Utilisateur = auth()->user()->id;
                $historique_sortie_produit->Id_Emballage = $stock->Id_Emballage;
                $historique_sortie_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_sortie_produit->Quantite = $value['quantity_transfer'];;
                $historique_sortie_produit->save();

                if ($stock_destination !== null) {

                    // dd($stock_source, $magasin_destination);
                    $calcul_nouveau_prix_achat_destination = (($prix_achat_source * $value['quantity_transfer']) + ($stock_destination->Prix_Achat_Net * $stock_destination->Qte_stockee)) / ($stock_destination->Qte_stockee + $value['quantity_transfer']);
                    //dd($calcul_nouveau_prix_achat_destination);
                    $quantite_stockee_destination = $stock_destination->Qte_stockee + $value['quantity_transfer'];
                    $stock_destination->Qte_stockee = $quantite_stockee_destination;
                    $stock_destination->Prix_Achat_Net = $calcul_nouveau_prix_achat_destination;
                    $stock_destination->update();

                    $transferer = new TransfertLigneEmballage();
                    $transferer->Id_Emballage = $produit->id;
                    $transferer->Id_Transfert_Emballage = $transfert_produit->id;
                    $transferer->Qte_Transferee = $value['quantity_transfer'];
                    $transferer->save();

                    // dd('quantite_stockee_source', $quantite_stockee_source, 'quantite_stockee_destination', $quantite_stockee_destination);
                } else {

                    // $prix_achat_net_destination = $stock_source->Prix_Achat_Net;
                    $stocker_destination = new StockEmballage();
                    $stocker_destination->Id_Emballage  = $produit->id;
                    $stocker_destination->Id_Magasin  = $magasin_destination;
                    $stocker_destination->Qte_stockee = $value['quantity_transfer'];
                    $stocker_destination->Prix_Achat_Net = $prix_achat_source;
                    $stocker_destination->save();

                    $transferer = new TransfertLigneEmballage();
                    $transferer->Id_Emballage = $produit->id;
                    $transferer->Id_Transfert_Emballage = $transfert_produit->id;
                    $transferer->Qte_Transferee = $value['quantity_transfer'];
                   $transferer->save();

                }
            }
            return to_route('transfert_emballage')->with('success', 'Le transfert a bien été effectué');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite.");
        }
    }

    public function getTransfertEmballage(Request $request, $id)
    {
        // dd('idid');
        $this->authorize('consulter-transfert-produit');
        try {

            $annees = TransfertEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
            $transfert_produits = TransfertEmballage::all();

            $produits = Emballage::orderBy('created_at', 'desc')->get();
            $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();
            $site_id = $request->session()->get('site_id');
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $transferers = DB::table('transfert_ligne_emballages')
                ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->select('transfert_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation')
                ->where('transfert_ligne_emballages.Id_Transfert_Emballage', '=', $id)
                ->get();

            return view(
                'page.emballage.transfert_emballage.transfert',
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
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }

    }

    public function filterTransfertEmballage(Request $request)
    {
        $this->authorize('consulter-transfert-produit');
        try {
            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $year = date('Y'); // Ou une autre année si nécessaire
            $annee = $request->query('annee');

            $user = Auth::user();
            $user_connecterId = $user->id;

            $query = DB::table('transfert_emballages')
                ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'transfert_emballages.Id_Agence', '=', 'agences.id')
                ->join('magasins', 'transfert_emballages.Id_Magasin_Source', '=', 'magasins.id')
                ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', '=', 'magasin_destination.id')
                ->select('magasins.NomMagasin as NomMagasinSource', 'magasin_destination.NomMagasin as NomMagasinDestination', 'transfert_emballages.*', 'users.name', 'agences.NomAgence')
                ->orderBy('transfert_emballages.id', 'desc');

            if ($month) {
                $query->whereMonth('transfert_emballages.created_at', $month)
                    ->whereYear('transfert_emballages.created_at', $year);
            }

            if ($startDate) {
                $query->whereDate('transfert_emballages.created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('transfert_emballages.created_at', '<=', $endDate);
            }

            if ($annee) {
                $query->whereYear('transfert_emballages.created_at', $annee);
            }

            $transfert_produits = $query->get();

            // Formater les données pour qu'elles soient bien interprétées en JSON
            $transfert_produits = $transfert_produits->map(function ($transfert_produit) {
                return [
                    'id' => $transfert_produit->id,
                    'Date_Transfert' => $transfert_produit->Date_Transfert,
                    'NomAgence' => $transfert_produit->NomAgence,
                    'Reference_Transfert' => $transfert_produit->Reference_Transfert,
                    'NomMagasinDestination' => $transfert_produit->NomMagasinDestination,
                    'NomMagasinSource' => $transfert_produit->NomMagasinSource,
                    'Observations' => $transfert_produit->Observations,
                    'name' => $transfert_produit->name,
                ];
            });

            // dd($entree_produits);
            return response()->json($transfert_produits);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite .");
        }
    }

    public function ImprimerTransfertEmballage(Request $request)
    {
        $this->authorize('imprimer-liste-transfert');
       // try {
            $id_transfert = $request->input('id_transfert');
            if (empty($id_transfert)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $transfert_produits = DB::table('transfert_emballages')
                ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('magasins as magasins_source', 'transfert_emballages.Id_Magasin_Source', '=', 'magasins_source.id')
                ->join('magasins as magasins_destination', 'transfert_emballages.Id_Magasin_Destination', '=', 'magasins_destination.id')
                ->join('agences', 'transfert_emballages.Id_Agence', '=', 'agences.id')
                ->join('agences as agence_destinations', 'transfert_emballages.Id_Agence_Destination', '=', 'agence_destinations.id')
                ->where('transfert_emballages.id', '=', $id_transfert)
                ->select('transfert_emballages.*', 'magasins_source.NomMagasin as NomMagasinSource', 'magasins_destination.NomMagasin as NomMagasinDestination', 'users.name', 'agences.NomAgence as NomAgenceSource', 'agence_destinations.NomAgence as NomAgenceDestination')
                ->get();


            if ($transfert_produits->isEmpty()) {
                return redirect()->back()->with('warning', 'Transfert non trouvé');
            }

            $transferers = DB::table('transfert_ligne_emballages')
                ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->select('transfert_ligne_emballages.Qte_transferee', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle')
                ->where('transfert_ligne_emballages.Id_Transfert_Emballage', '=', $id_transfert)
                ->get();

            // dd($transferers, $transfert_produits );
            if ($transferers->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucun emballage transféré trouvé');
            }




        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.produit.transfert_emballage.imprimer.imprimer-action', [
            'imageEntetePied' => $imageEntetePied,
            'transfert_produits' => $transfert_produits,
            'transferers' => $transferers,
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
        $prefixe = 'TRANSFERT_EMBALLAGE';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);

       /*  } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        } */
    }

    public function transfertEmballagePeriode(Request $request)
    {

        // dd('nds');
        $this->authorize('imprimer-liste-transfert');
       // try {
            $debut_periode = $request->input('date_debut_periode');
            $fin_periode = $request->input('date_fin_periode');
            $magasin_source = $request->input('magasin_source');
            $magasin_destination = $request->input('magasin_destination');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');
            $submit = $request->input('submit');

            $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
            $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


            $query_transfert_produit = DB::table('transfert_emballages')
            ->join('transfert_ligne_emballages', 'transfert_ligne_emballages.Id_Transfert_Emballage', 'transfert_emballages.id')
            ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
            ->join('magasins as magasin_source', 'transfert_emballages.Id_Magasin_Source', 'magasin_source.id')
            ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', 'magasin_destination.id')
            ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', 'emballages.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
            ->select(
                'transfert_ligne_emballages.Id_Transfert_Emballage',
                DB::raw('MAX(transfert_emballages.Date_Transfert) as Date_Transfert'),
                DB::raw('MAX(transfert_emballages.Reference_Transfert) as Reference_Transfert'),
                DB::raw('MAX(transfert_emballages.Observations) as Observations'),
                DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                DB::raw('MAX(users.name) as name'),
            )
            ->groupBy('transfert_ligne_emballages.Id_Transfert_Emballage');

            $query = DB::table('transfert_emballages')
            ->join('transfert_ligne_emballages', 'transfert_ligne_emballages.Id_Transfert_Emballage', 'transfert_emballages.id')
            ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
            ->whereBetween('transfert_emballages.Date_Transfert', [$date_debut_periode, $date_fin_periode])
            ->select('emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'transfert_ligne_emballages.*');



            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = $query_transfert_produit->get();
                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = $query_transfert_produit->where('categorie_emballages.id', '=', $categorie)->get();
                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = $query_transfert_produit->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();

                $all_transferers = $query->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {

                $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)->get();


                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                $transfert_produit = DB::table('transfert_emballages')
                    ->join('transfert_ligne_emballages', 'transfert_ligne_emballages.Id_Transfert_Emballage', 'transfert_emballages.id')
                    ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_emballages.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', 'emballages.id')
                    ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                    // ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    // ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)
                    ->where('categorie_emballages.id', '=', $categorie)
                    // ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)
                    ->select(
                        'transfert_ligne_emballages.Id_Transfert_Emballage',
                        DB::raw('MAX(transfert_emballages.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_emballages.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_emballages.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transfert_ligne_emballages.Id_Transfert_Emballage')
                    ->get();


                $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)
                ->where('categorie_emballages.id', '=', $categorie)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                    $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)
                    ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                    $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('categorie_emballages.id', '=', $categorie)->get();


                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_emballages')
                    ->join('transfert_ligne_emballages', 'transfert_ligne_emballages.Id_Transfert_Emballage', 'transfert_emballages.id')
                    ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_emballages.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', 'emballages.id')
                    ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                    // ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)

                    // ->where('categorie_emballages.id', '=', $categorie)
                    // ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)
                    ->select(
                        'transfert_ligne_emballages.Id_Transfert_Emballage',
                        DB::raw('MAX(transfert_emballages.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_emballages.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_emballages.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transfert_ligne_emballages.Id_Transfert_Emballage')
                    ->get();

                    $transfert_produit = $query_transfert_produit->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {

                    $transfert_produit = $query_transfert_produit->where('categorie_emballages.id', '=', $categorie)
                    ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)
                ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {

                $transfert_produit = DB::table('transfert_emballages')
                    ->join('transfert_ligne_emballages', 'transfert_ligne_emballages.Id_Transfert_Emballage', 'transfert_emballages.id')
                    ->join('users', 'transfert_emballages.Id_Utilisateur', '=', 'users.id')
                    ->join('magasins as magasin_source', 'transfert_emballages.Id_Magasin_Source', 'magasin_source.id')
                    ->join('magasins as magasin_destination', 'transfert_emballages.Id_Magasin_Destination', 'magasin_destination.id')
                    ->join('emballages', 'transfert_ligne_emballages.Id_Emballage', 'emballages.id')
                    ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                    // ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)

                    ->select(
                        'transfert_ligne_emballages.Id_Transfert_Emballage',
                        DB::raw('MAX(transfert_emballages.Date_Transfert) as Date_Transfert'),
                        DB::raw('MAX(transfert_emballages.Reference_Transfert) as Reference_Transfert'),
                        DB::raw('MAX(transfert_emballages.Observations) as Observations'),
                        DB::raw('MAX(magasin_source.NomMagasin) as NomMagasinSource'),
                        DB::raw('MAX(magasin_destination.NomMagasin) as NomMagasinDestination'),
                        DB::raw('MAX(users.name) as name'),
                    )
                    ->groupBy('transfert_ligne_emballages.Id_Transfert_Emballage')
                    ->get();

                    $transfert_produit = $query_transfert_produit->where('categorie_emballages.id', '=', $categorie)
                    ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)
                    ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)
                ->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {

                    $transfert_produit = $query_transfert_produit->where('categorie_emballages.id', '=', $categorie)
                    ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('categorie_emballages.id', '=', $categorie)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {

                    $transfert_produit = $query_transfert_produit->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)
                    ->where('transfert_emballages.Id_Magasin_Destination', '=', $magasin_destination)
                    ->where('transfert_emballages.Id_Magasin_Source', '=', $magasin_source)->get();

                if (count($transfert_produit) <= 0) {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
                }

                $all_transferers = $query->where('transfert_ligne_emballages.Id_Emballage', '=', $produit)->get();
                // dd($transfert_produit, $all_transferers);
                if (count($all_transferers) > 0) {
                    $get_request = $all_transferers;
                } else {
                    return to_route('transfert_emballage')->with('error', 'Aucunes données trouvées!');
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


            $data = [
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


                $htmlContent = view('page.emballage.transfert_emballage.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('TRANSFERT_EMBALLAGE_'.$date_et_heure, array("Attachment" => false));
            }
            if($submit == 'EXCEL'){
                return Excel::download(new TransfertEmballagePeriodeExport($data), 'TRANSFERT_'.$date_et_heure.'.xlsx');
            }

   /*       } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }



    public function transfertEmballageImprimer(Request $request)
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
                ->select('transferers.Qte_transferee', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle')
                ->where('transferers.Id_Transfert_Produit', '=', $id_transfert)
                ->get();

            // dd($transferers, $transfert_produits );
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
        ];

        if($reponse === 'imprimer'){
            $htmlContent = view('page.emballage.entree_emballage.imprimer.imprimer-action', $data)->render();

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
