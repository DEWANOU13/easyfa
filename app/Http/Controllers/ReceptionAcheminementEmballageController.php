<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use Illuminate\Http\Request;
use App\Models\StockEmballage;
use App\Models\PrefixeReference;
use App\Models\AcheminerEmballage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AcheminementEmballage;
use App\Models\ReceptionAchemEmballage;
use App\Models\StockEmballageHistories;
use App\Models\ReceptionnerAcheminement;
use Illuminate\Support\Facades\Validator;
use App\Models\ReceptionnerAchemEmballage;
use App\Exports\ReceptionAchemEmballagePeriodeExport;
use App\Exports\ReceptionAchemEmballageActionPrintExport;


class ReceptionAcheminementEmballageController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-reception-acheminenment-emballage');

        $annees = ReceptionAchemEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

        $site_id = session()->get('site_id');
        $listeReception = ReceptionAchemEmballage::join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
        ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
        ->select('reception_achem_emballages.id', 'reception_achem_emballages.Date_Reception', 'reception_achem_emballages.Reference_Reception', 'reception_achem_emballages.Observations', 'users.name', 'agences.NomAgence')
        ->where('reception_achem_emballages.Id_Agence', $site_id)
        ->whereMonth('reception_achem_emballages.created_at', $currentMonth)
        ->whereYear('reception_achem_emballages.created_at', $currentYear)
        ->get();


        $receptionner_acheminements = DB::table('receptionner_achem_emballages')
        ->join('acheminer_emballages', 'receptionner_achem_emballages.Id_Acheminer', '=', 'acheminer_emballages.id')
        ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
        ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
        ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
        ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
        ->select('receptionner_achem_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
        ->where('receptionner_achem_emballages.Id_Reception_Acheminement', '=', 0)

        ->get();


        return view('page.acheminement_emballage.receptionner.receptionner',
            ['listeReception' => $listeReception,
            'receptionner_acheminements' => $receptionner_acheminements,
            'annees' => $annees

            ]
        );
    }
    public function filterReceptionAchminementEmballage(Request $request)
    {

        try {
            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            // Création de la requête initiale
            if (is_array($Agence_id)) {
                $query = ReceptionAchemEmballage::join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
                ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
                ->select('reception_achem_emballages.id', 'reception_achem_emballages.Date_Reception', 'reception_achem_emballages.Reference_Reception', 'reception_achem_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->where('reception_achem_emballages.Id_Agence', $Agence_id);
            } else {
                $query = ReceptionAchemEmballage::join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
                ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
                ->select('reception_achem_emballages.id', 'reception_achem_emballages.Date_Reception', 'reception_achem_emballages.Reference_Reception', 'reception_achem_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->where('reception_achem_emballages.Id_Agence', $Agence_id);
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('reception_achem_emballages.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('reception_achem_emballages.created_at', $month);
                $query->whereYear('reception_achem_emballages.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('reception_achem_emballages.created_at', '>=', $startDate)
                      ->where('reception_achem_emballages.created_at', '<=', $endDate);
            }


            // Exécuter la requête
            $listeReception = $query->get();

            $receptionner_acheminements = DB::table('receptionner_achem_emballages')
            ->join('acheminer_emballages', 'receptionner_achem_emballages.Id_Acheminer', '=', 'acheminer_emballages.id')
            ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('receptionner_achem_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            ->where('receptionner_achem_emballages.Id_Reception_Acheminement', '=', 0)

            ->get();
            $annees = ReceptionAchemEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');



            return view('page.acheminement_emballage.receptionner.receptionner',
            ['listeReception' => $listeReception,
            'receptionner_acheminements' => $receptionner_acheminements,
            'annees' => $annees
            ]
        );

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function create()
    {
        $this->authorize('receptionner-acheminement-emballage');
        $site_id = session()->get('site_id');
        $agences = DB::table('agences')
            ->join('acheminement_emballages', 'acheminement_emballages.Id_Agence_Source', 'agences.id')
            ->distinct()
            ->where('acheminement_emballages.Id_Agence_Destination', $site_id)
            ->select('agences.id', 'agences.NomAgence')
            ->get();


        // $approvisionnements = DB::table('approvisionnements')
        //     ->where('approvisionnements.Id_Agence_Destination', $site_id)
        //     ->get();

        $acheminements = DB::table('acheminement_emballages')
            ->join('acheminer_emballages', 'acheminer_emballages.Id_Acheminement', '=', 'acheminement_emballages.id')
            ->where('acheminement_emballages.Id_Agence_Destination', '=', $site_id)
            ->wherenot('acheminement_emballages.Statut_acheminement', 'RECEPTION_TERMINEE')

            ->select(
                'acheminement_emballages.id',
                DB::raw('SUM(acheminer_emballages.Qte_acheminee) as Qte_acheminee'),
                DB::raw('MAX(acheminement_emballages.Reference_acheminement) as Reference_acheminement'),
                DB::raw('MAX(acheminement_emballages.Id_Agence_Source) as Id_Agence_Source')
            )
            ->groupBy('acheminement_emballages.id')
            ->orderBy('acheminement_emballages.id', 'desc')
            ->get();

        $acheminers = DB::table('acheminer_emballages')
            ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('acheminer_emballages.*', 'emballages.Nom_emballage as Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            // ->where('approvisionners.Id_Approvisionnement', '=', $id)
            ->get();

            $receptionners = ReceptionnerAchemEmballage::all();

            // dd($receptionners);
        return view('page.acheminement_emballage.receptionner.nouveau',
            [
                'agences' => $agences,
                'acheminements' => $acheminements,
                'acheminers' => $acheminers,
                'receptionners' => $receptionners,
            ]
        );
    }
    public function getAcheminementemballage(Request $request)
    {


        $data_approv = $request->input('category_id');

        $magasin_formate = explode('-', $data_approv, 2);
        $id_apporv = trim($magasin_formate[0]);
       // dd($id_apporv);

        $approvs = DB::table('acheminer_emballages')
            ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', 'stock_emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')

            ->where('acheminer_emballages.Id_Acheminement', '=', $id_apporv)
            ->whereColumn('acheminer_emballages.Qte_Receptionnee', '!=', 'acheminer_emballages.Qte_acheminee')

            ->select('acheminer_emballages.*', 'magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'emballages.id as produits_id',)
            ->get();


        return response()->json($approvs);
    }

    public function store(Request $request)
    {
        $this->authorize('receptionner-acheminement-emballage');
        // dd($request);

        $validator = Validator::make(
            $request->all(),
            [
                // 'fournisseur' => 'required',
                // 'fournisseur' => 'required',
                'client' => 'required',
                'observation' => 'required',
                // 'magasin_destination' => 'required',
                'inputs.*.num_facture' => 'required',
                //  'inputs.*.designation' => 'required',
                'inputs.*.mode_reglement' => 'required',
                // 'inputs.*.quantity' => 'required',
                'inputs.*.montant' => 'required',
            ],
            [
                // 'fournisseur' => 'Fournisseur requis',
                'client' => 'le clien est requis',
                'observation' => 'Observations requise',
                'inputs.*.num_facture' => "Numéro de facture est requise",
                // 'inputs.*.designation' => "designation(s) requise(s)",
                'inputs.*.mode_reglement' => "Le mode paiement est requis",
                'inputs.*.montant' => "Le montant est requis",
                // 'inputs.*.quantity' => "quantite(s) requise(s)",
                // 'inputs.*.quantity_transfer' => "Quantite à transférer requise(s)",
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Obtenez le dernier chiffre de l'année actuelle
        $lastDigitOfYear = substr(Carbon::now()->year, -2);
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->reception_acheminement ?? '';

        // Obtenez le dernier numéro de référence enregistré
        //   $lastReference = Reglement::count();
        $lastReference = ReceptionAchemEmballage::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            //   $lastReference = Reglement::orderBy('id', 'desc')->first();
            $lastReference = ReceptionAchemEmballage::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Reception, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        if (is_array(getIdAgenceByUser())) {
            $site_id = 1;
        } else {
            $site_id = $request->session()->get('site_id');
        }

        //variable de creation entree produit
        $reference_reglement = "{$site_id}/{$lastDigitOfYear}/REAEMB/{$incrementedReferenceNumber}";
        $date_reglement =  Carbon::now();
        // $fournisseur = $request->input('fournisseur');
        $client = $request->input('client');
        $observation = $request->input('observation');


        $reception_approv = new ReceptionAchemEmballage();
        $reception_approv->Date_Reception = $date_reglement;
        $reception_approv->Reference_Reception = $reference_reglement;
        $reception_approv->Observations = $observation;
        $reception_approv->Id_Agence = $site_id;
        $reception_approv->Id_Utilisateur = auth()->user()->id;
        // $reception_approv->Id_Fournisseur = $fournisseur;
       $reception_approv->save();

        $Qte_Receptionnee_total = 0;
        $qte_appro_total = 0;

        foreach ($request->inputs as $value) {

            $approv_formate = explode('-', $value['num_facture'], 2);
            $magasin_formate = explode('-', $value['mode_reglement'], 2);
            $produit_formate = explode('|', $value['produit'], 3);
            $approv_id = trim($approv_formate[0]);
            $magasin_destination_id = trim($magasin_formate[0]);
            $acheminer_id = trim($produit_formate[0]);
            $stock_id = trim($produit_formate[1]);
           //dd($magasin_destination_id);


            $stock = StockEmballage::find($stock_id);
            //dd($stock);
            $produit_id = $stock->Id_Emballage;

            $stock_destination = StockEmballage::where('Id_Emballage', '=', $produit_id)->where('Id_Magasin', '=', $magasin_destination_id)->first();

            if(empty($stock_destination)){
                $stock_destination = new StockEmballage();
                $stock_destination->Id_Emballage = $produit_id;
                $stock_destination->Id_Magasin = $magasin_destination_id;
                $stock_destination->Qte_stockee = 0;
                $stock_destination->Prix_Achat_Net = 0;
                $stock_destination->Enregistrer_par = auth()->user()->id;
                $stock_destination->save();
            }


            $sommeAppro = DB::table('acheminer_emballages')
            ->join('acheminement_emballages', 'acheminer_emballages.Id_Acheminement', 'acheminement_emballages.id')
            ->where('acheminer_emballages.Id_Acheminement', '=', $approv_id)
            ->sum('Qte_acheminee');


        $sommeRecept = DB::table('receptionner_achem_emballages')
            ->join('acheminer_emballages', 'receptionner_achem_emballages.Id_Acheminer', 'acheminer_emballages.id')
            ->join('acheminement_emballages', 'acheminer_emballages.Id_Acheminement', 'acheminement_emballages.id')
            ->where('acheminer_emballages.Id_Acheminement', '=', $approv_id)
            ->sum('receptionner_achem_emballages.Qte_Receptionnee');


        // dd($sommeAppro, $sommeRecept);

        $somme_total = $sommeRecept + $value['montant'];

        if ($sommeAppro > $somme_total) {
            $statut_appro = 'PARTIELLEMENT RECEPTIONNEE';
        } else {
            $statut_appro = 'RECEPTION_TERMINEE';
        }
            $approvisionner = AcheminerEmballage::find($acheminer_id);

            $new_quantity = $stock_destination->Qte_stockee + $value['montant'];



            if ($stock_destination) {
                $stock_destination->Qte_stockee = $new_quantity;
                $stock_destination->update();

                $approvisionner->Qte_Receptionnee += $value['montant'];
                $approvisionner->update();
            }

            $Qte_Receptionnee_total += $approvisionner->Qte_Receptionnee;
            $qte_appro_total += $approvisionner->Qte_acheminee;

            $approv_exist = AcheminementEmballage::find($approv_id);

            $approv_exist->Statut_acheminement = $statut_appro;
            $approv_exist->update();

            $detail_reglement = new ReceptionnerAchemEmballage();
            $detail_reglement->Id_Reception_Acheminement = $reception_approv->id;
            $detail_reglement->Id_Acheminer = $acheminer_id;
            $detail_reglement->Id_Magasin = $magasin_destination_id;
            $detail_reglement->Qte_Receptionnee = $value['montant'];
            $detail_reglement->save();

            $date_entree =  Carbon::now();
            $historique_entree_produit = new StockEmballageHistories();
            $historique_entree_produit->Date = $date_entree;
            $historique_entree_produit->agence_id = $site_id;
            $historique_entree_produit->Motif = "Entree d'emballage par une réception d'acheminement";
            $historique_entree_produit->Justificatif = $reception_approv->Reference_Reception;
            $historique_entree_produit->operation = 'ENTREE';
            $historique_entree_produit->type_operation = 'ENTREE';
            $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
            $historique_entree_produit->Id_Emballage = $stock_destination->Id_Emballage;
            $historique_entree_produit->Id_Magasin = $stock_destination->Id_Magasin;
            $historique_entree_produit->Quantite = $value['montant'];
            $historique_entree_produit->save();
        }
 /*        $qte_receptionne_global = $Qte_Receptionnee_total;
        $qte_appro_global = $qte_appro_total;

       // dd($qte_receptionne_global, $qte_appro_global);

        if($qte_receptionne_global == $qte_appro_global)
            {
                $acheminement = AcheminementEmballage::find($approv_id);
                $acheminement->Statut_acheminement = "RECEPTION_TERMINEE";
                $acheminement->update();

            } */

        // dd($reste_a_payer);
        return to_route('reception_acheminement_emballage')->with('success', 'La réceptionn a bien été ajouté');
    }
    public function getReceptionLigne($id)
    {

        try {
            $annees = AcheminementEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $site_id = session()->get('site_id');
            $listeReception = ReceptionAchemEmballage::join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
            ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
            ->select('reception_achem_emballages.id', 'reception_achem_emballages.Date_Reception', 'reception_achem_emballages.Reference_Reception', 'reception_achem_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_achem_emballages.Id_Agence', $site_id)
            ->whereMonth('reception_achem_emballages.created_at', $currentMonth)
            ->whereYear('reception_achem_emballages.created_at', $currentYear)
            ->get();





            $receptionner_acheminements = DB::table('receptionner_achem_emballages')
                ->join('acheminer_emballages', 'receptionner_achem_emballages.Id_Acheminer', '=', 'acheminer_emballages.id')
                ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('receptionner_achem_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle','acheminer_emballages.Qte_acheminee')
                ->where('receptionner_achem_emballages.Id_Reception_Acheminement', '=', $id)

                ->get();

            //dd($receptionner_acheminements);

            return view(
                'page.acheminement.receptionner.receptionner',
                [
                    'listeReception' => $listeReception,
                    'receptionner_acheminements' => $receptionner_acheminements,
                    'annees' => $annees
                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function reception_imprimer_action(Request $request)
    {
        try {
            $id_reception = $request->input('id_reception');

            $reponse = $request->input('reponse');
            if (empty($id_reception)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $reception = ReceptionAchemEmballage::join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
            ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
            ->select('reception_achem_emballages.id', 'reception_achem_emballages.Date_Reception', 'reception_achem_emballages.Reference_Reception', 'reception_achem_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_achem_emballages.id', $id_reception)
            ->get();


            if ($reception->isEmpty()) {
                return redirect()->back()->with('warning', 'Reception non trouvé');
            }

            $recetionner =  DB::table('receptionner_achem_emballages')
            ->join('acheminer_emballages', 'receptionner_achem_emballages.Id_Acheminer', '=', 'acheminer_emballages.id')

            ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('receptionner_achem_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            ->where('receptionner_achem_emballages.Id_Reception_Acheminement',  '=', $id_reception)
            ->get();



            // dd($approvisionnement, $approvisionners );
            if ($recetionner->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucun produit receptionné trouvé');
            }




            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $data = [
                'texteEntetePied' => $texteEntetePied,
                'imageEntetePied' => $imageEntetePied,
                'reception' => $reception,
                'recetionner' => $recetionner,
            ];

            if($reponse === 'imprimer'){
                $htmlContent = view('page.acheminement_emballage.receptionner.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'Reception_Acheminement_Emballage';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if($reponse === 'exporter'){
                $prefixe = 'Reception_Acheminement_Emballage';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ReceptionAchemEmballageActionPrintExport($data), $nom_excel);
            }



            } catch (Exception $e) {
                // Redirection avec message d'erreur
                return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
            }
    }

    public function imprimer_reception(Request $request)
    {
        $this->authorize('imprimer-liste-transfert');
        $site_id = session()->get('site_id');
       // try {

            $debut_periode = $request->input('date_debut_periode');
            $fin_periode = $request->input('date_fin_periode');
            $agence = $request->input('agence');
            $submit = $request->input('submit');

            $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
            $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

            $query = DB::table('reception_achem_emballages')
                ->join('agences', 'reception_achem_emballages.Id_Agence', 'agences.id')
                ->join('users', 'reception_achem_emballages.Id_Utilisateur', 'users.id')
                ->whereBetween('reception_achem_emballages.Date_Reception', [$date_debut_periode, $date_fin_periode])
                ->select('reception_achem_emballages.*','users.name', 'agences.NomAgence');

            if($agence === 'Tous') {

                $liste_reception = $query->get();
                if (count($liste_reception) > 0) {
                    $get_request = $liste_reception;
                } else {
                    return to_route('reception_acheminement_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }
            if($agence !== 'Tous') {
                $liste_reception = $query->where('reception_achem_emballages.Id_Agence', $agence)->get();

                if (count($liste_reception) > 0) {
                    $get_request = $liste_reception;
                } else {
                    return to_route('reception_acheminement_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }
            if ($agence !== 'Tous') {
                $infoAgence = Agence::where('id', $agence)->first();
            } else {
                $infoAgence = Agence::where('id', 0)->first();
            }



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data = [
                'texteEntetePied' => $texteEntetePied,
                'debut_periode' => $debut_periode,
                'fin_periode' => $fin_periode,
                'getAcheminements' => $get_request,
                'infoAgence' => $infoAgence,
                'imageEntetePied' => $imageEntetePied
            ];

            $date_et_heure = date('Ymd_His');

            if($submit == 'PDF'){



                $options = new Options();
                $options->set('chroot', realpath(''));
                $dompdf = new Dompdf($options);


                $htmlContent = view('page.acheminement.receptionner.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('Reception_acheminements_'.$date_et_heure, array("Attachment" => false));
            }
            if($submit == 'EXCEL'){
                return Excel::download(new ReceptionAchemEmballagePeriodeExport($data), 'reception_acheminements_'.$date_et_heure.'.xlsx');
            }

       /*   } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }
}
