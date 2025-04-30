<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Emballage;
use Illuminate\Http\Request;
use App\Models\ApproEmballage;
use App\Models\StockEmballage;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use App\Models\LigneApproEmballage;
use App\Models\NotificationApprovs;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Validator;
use App\Exports\ApprovEmballagePrintExport;
use App\Models\NotificationApprovEmballages;
use App\Exports\ApprovEmballageActionPrintExport;
use App\Models\NotificationAchemi;
use App\Models\NotificationAchemiEmballage;

class EmballageApprovisionnementController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-approvisionnement-emballage');

        $annees = ApproEmballage::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $Agence_id = session()->get('site_id');


        $appro_emballages = DB::table('appro_emballages')
            ->join('agences', 'appro_emballages.Id_Agence_Destination', 'agences.id')
            ->join('users', 'appro_emballages.Id_Utilisateur', 'users.id')
            ->select('appro_emballages.id', 'appro_emballages.Date_Appro', 'appro_emballages.Statut_appro', 'appro_emballages.Reference_Appro_Emballage', 'appro_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->whereMonth('appro_emballages.Date_Appro', $currentMonth)
            ->whereYear('appro_emballages.Date_Appro', $currentYear)
            ->where('appro_emballages.Id_Agence_Source', $Agence_id)
            ->get();

        $site_id = session()->get('site_id');

        return view('page.approvisionnement_emballage.approvisionner_emballage.approvisionner',
            [
                'appro_emballages' => $appro_emballages,
                'annees' => $annees,
            ]
        );
    }

    public function filterAproEmballage(Request $request)
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
                $query =  DB::table('appro_emballages')
                        ->join('agences', 'appro_emballages.Id_Agence_Destination', 'agences.id')
                        ->join('users', 'appro_emballages.Id_Utilisateur', 'users.id')
                        ->select('appro_emballages.id', 'appro_emballages.Date_Appro', 'appro_emballages.Statut_appro', 'appro_emballages.Reference_Appro_Emballage', 'appro_emballages.Observations', 'users.name', 'agences.NomAgence')
                        ->where('appro_emballages.Id_Agence_Source', $Agence_id)
                        ;

            } else {
                $query =  DB::table('appro_emballages')
                ->join('agences', 'appro_emballages.Id_Agence_Destination', 'agences.id')
                ->join('users', 'appro_emballages.Id_Utilisateur', 'users.id')
                ->select('appro_emballages.id', 'appro_emballages.Date_Appro', 'appro_emballages.Statut_appro', 'appro_emballages.Reference_Appro_Emballage', 'appro_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->where('appro_emballages.Id_Agence_Source', $Agence_id);

            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('appro_emballages.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('appro_emballages.created_at', $month);
                $query->whereYear('appro_emballages.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('appro_emballages.created_at', '>=', $startDate)
                      ->where('appro_emballages.created_at', '<=', $endDate);
            }

            // Exécuter la requête
            $appro_emballages = $query->get();
            $annees = ApproEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');



                return view('page.approvisionnement_emballage.approvisionner_emballage.approvisionner',
                [
                    'appro_emballages' => $appro_emballages,
                    'annees' => $annees
                ]
            );

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function  create(Request $request)
    {
        $this->authorize('effectuer-approvisionnement-emballage');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            $site_id = session()->get('site_id');
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $emballages = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'magasins.agence_id')
                ->where('stock_emballages.Qte_stockee', '>', 0)

                ->where('magasins.agence_id', '=', $site_id)
                ->get();

                // dd($stock_produits);



            $agence_source = Agence::where('NomAgence', '=', 'Siège')->orderBy('id', 'desc')->get();
            $agence_destination = Agence::where('NomAgence', '<>', 'Siège')->orderBy('id', 'desc')->get();
            // $agences = Agence::orderBy('id', 'desc')->get();


            $agence = Agence::find($site_id);

            if($agence->id !== 1){
                return to_route('reception_approvisionnement_emballage')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
            }

            return view('page.approvisionnement_emballage.approvisionner_emballage.nouveau', [
                'stock_produits' => $stock_produits,
                'agence_source' => $agence_source,
                'agence_destination' => $agence_destination
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        $this->authorize('effectuer-approvisionnement-emballage');
        // dd($request);
        // try {
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
            $lastReference = ApproEmballage::where('Id_Agence_Source', '=', session()->get('site_id'))->count();
            $site_id = session()->get('site_id');
            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = TransfertProduit::orderBy('id', 'desc')->first();
                $lastReference = ApproEmballage::where('Id_Agence_Source', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Appro_Emballage, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->appro_emballages ?? '';
            //variable de creation entree produit
            $reference_approvisionnement = "{$site_id}/{$lastDigitOfYear}/APEMB/{$incrementedReferenceNumber}";
            $date_approvisionnement =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $id_agence_source = $request->input('magasin_source');
            $id_agence_destination = $request->input('magasin_destination');

            // dd($id_agence_destination, $id_agence_source);


            $appro_emballage = new ApproEmballage();
            $appro_emballage->Date_Appro = $date_approvisionnement;
            $appro_emballage->Id_Utilisateur = auth()->user()->id;
            $appro_emballage->Reference_Appro_Emballage = $reference_approvisionnement;
            $appro_emballage->Observations = $observation;
            $appro_emballage->Statut_appro = 'EN COURS';
            $appro_emballage->Id_Agence_Source = $id_agence_source;
            $appro_emballage->Id_Agence_Destination = $id_agence_destination;
            $appro_emballage->Enregistrer_par = auth()->user()->id;
            $appro_emballage->save();

            $quantite_total_approv = 0;


            foreach ($request->inputs as $value) {

                $produit_formate = explode(' ', $value['produit'], 2);
                $reference_produit = $produit_formate[1];
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode(' ', $produit_formate[1], 2);
                // $magasin_formate = explode('-', $value['magasin'], 2);
                // $id_magasin = trim($magasin_formate[0]);
                // dd($id_stock);

                $produit = Emballage::where('Reference', '=', $reference_produit_formate[0])->first();
                // dd($produit);

                $stock = StockEmballage::find($id_stock);
                // dd($stock);

                // dd($stock_source->id, $stock_destination->id);
                $new_quantite_stock = $stock->Qte_stockee - $value['quantity_transfer'];
                $stock->Qte_stockee = $new_quantite_stock;
                $stock->update();

                // Concerne le magasin destination
                $approvisionner = new LigneApproEmballage();
                $approvisionner->Id_Appro_Emballage  = $appro_emballage->id;
                $approvisionner->Id_Stock_Emballage  = $stock->id;
                $approvisionner->Prix_Revient = $stock->Prix_Achat_Net;
                $approvisionner->Qte_Approvisionnee = $value['quantity_transfer'];
                $approvisionner->Qte_Receptionnee = 0;
                $approvisionner->save();

                // L'historik
                $historique_entree_produit = new StockEmballageHistories();
                $historique_entree_produit->Date = $date_approvisionnement;
                $historique_entree_produit->agence_id = $id_agence_source;
                $historique_entree_produit->Motif = 'Sortie produit par appro_emballage';
                $historique_entree_produit->Justificatif = $reference_approvisionnement;
                $historique_entree_produit->operation = 'SORTIE';
                $historique_entree_produit->type_operation = 'SORTIE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Emballage = $stock->Id_Emballage;
                $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];
                $historique_entree_produit->save();

                $quantite_total_approv += $value['quantity_transfer'];
            }

            $notification_approv_emballage = new NotificationApprovEmballages();
            $notification_approv_emballage->Id_Appro_Emballage = $appro_emballage->id;
            $notification_approv_emballage->Id_Agence_Source = $id_agence_source;
            $notification_approv_emballage->Id_Agence_Destination = $id_agence_destination;
            $notification_approv_emballage->Qte_Approvisionnee = $quantite_total_approv;
            $notification_approv_emballage->Motif = 'Approvisionnement';
            $notification_approv_emballage->save();

            return to_route('approvisionner_emballage')->with('success', 'L\' appro_emballage a bien été effectué');
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function getApprovisionnerEmballage(Request $request, $id)
    {

        // try {
            $annees = ApproEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
                $Agence_id = session()->get('site_id');


            $appro_emballages = DB::table('appro_emballages')
                ->join('agences', 'appro_emballages.Id_Agence_Destination', 'agences.id')
                ->join('users', 'appro_emballages.Id_Utilisateur', 'users.id')
                ->select('appro_emballages.id', 'appro_emballages.Date_Appro', 'appro_emballages.Statut_appro', 'appro_emballages.Reference_Appro_Emballage', 'appro_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->where('appro_emballages.Id_Agence_Source', $Agence_id)
                ->get();

            $ligne_appro_emballages = DB::table('ligne_appro_emballages')
                ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('ligne_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
                ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $id)
                ->get();
            // dd($ligne_appro_emballages);

            return view(
                'page.approvisionnement_emballage.approvisionner_emballage.approvisionner',
                [
                    'ligne_appro_emballages' => $ligne_appro_emballages,
                    'appro_emballages' => $appro_emballages,
                    'annees' => $annees
                ]
            );
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function ApprovisinnementImprimerActionEmballage(Request $request)
    {
        try {
            $id_approv = $request->input('id_approv');
            $reponse = $request->input('reponse');
            if (empty($id_approv)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $appro_emballages = DB::table('appro_emballages')
                ->join('agences', 'appro_emballages.Id_Agence_Destination', 'agences.id')
                ->join('users', 'appro_emballages.Id_Utilisateur', 'users.id')
                ->select('appro_emballages.id', 'appro_emballages.Date_Appro', 'appro_emballages.Statut_appro', 'appro_emballages.Reference_Appro_Emballage', 'appro_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->where('appro_emballages.id', $id_approv)
                ->get();


            if ($appro_emballages->isEmpty()) {
                return redirect()->back()->with('warning', 'appro_emballages non trouvé');
            }

            $ligne_appro_emballages = DB::table('ligne_appro_emballages')
                ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('ligne_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
                ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $id_approv)
                ->get();



            // dd($appro_emballages, $ligne_appro_emballages );
            if ($ligne_appro_emballages->isEmpty()) {
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
                'appro_emballages' => $appro_emballages,
                'ligne_appro_emballages' => $ligne_appro_emballages,
            ];

            if ($reponse === 'imprimer') {
                $htmlContent = view('page.approvisionnement_emballage.approvisionner_emballage.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'appro_emballage';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if ($reponse === 'exporter') {
                $prefixe = 'approv_emballage_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ApprovEmballageActionPrintExport($data), $nom_excel);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function ReceptionApprovOngletImpressionEmballage(Request $request)
    {


        $site_id = session()->get('site_id');

        $reponse = $request->input('submit');

        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $agence = $request->input('agence');
        $categorie = $request->input('categorie');
        $produit = $request->input('produit');

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        // $query_agence = DB::table('appro_emballages')
        //     ->join('agences', 'appro_emballages.Id_Agence_Destination', '=', 'agences.id')
        //     ->join('users', 'appro_emballages.Id_Utilisateur', '=', 'users.id')
        //     ->select('appro_emballages.Id_Agence_Destination', 'agences.NomAgence', 'appro_emballages.Date_Appro')
        //     ->distinct();

        $query_agence = DB::table('appro_emballages')
            ->join('agences', 'appro_emballages.Id_Agence_Destination', '=', 'agences.id')
            ->join('users', 'appro_emballages.Id_Utilisateur', '=', 'users.id')
            ->select(
                'appro_emballages.Id_Agence_Destination',
                'agences.NomAgence',
                DB::raw('MAX(appro_emballages.Date_Appro) as Date_Appro')
            )
            ->where('appro_emballages.Id_Agence_Source', '=', $site_id)
            ->groupBy('appro_emballages.Id_Agence_Destination', 'agences.NomAgence');




        $query_approvisionners = DB::table('ligne_appro_emballages')
            ->join('appro_emballages', 'ligne_appro_emballages.Id_Appro_Emballage', '=', 'appro_emballages.id')
            ->join('agences', 'appro_emballages.Id_Agence_Destination', '=', 'agences.id')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            // ->where('magasins.agence_id', '=', $site_id)
            ->select('ligne_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle', 'appro_emballages.Id_Agence_Destination')
            ->whereBetween('ligne_appro_emballages.created_at', [$date_debut_periode, $date_fin_periode]);

        // dd($query_magasin, $query_receptionner);


        if ($agence === 'Toutes' && $produit === 'Tous' && $categorie === 'Toutes') {
            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence !== 'Toutes' && $produit === 'Tous' && $categorie === 'Toutes') {

            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('agences.id', '=', $agence)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence === 'Toutes' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners
                ->where('emballages.id', '=', $produit)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence === 'Toutes' && $produit === 'Tous' && $categorie !== 'Toutes') {

            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence !== 'Toutes' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('emballages.id', '=', $produit)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence !== 'Toutes' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence === 'Toutes' && $produit !== 'Tous' && $categorie !== 'Toutes') {

            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_emballages.id', '=', $categorie)
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } else {

            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_emballages.id', '=', $categorie)
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $data = [
            'texteEntetePied' => $texteEntetePied,
            'imageEntetePied' => $imageEntetePied,
            'data_agences' => $data_agences,
            'data_approvisionners' => $data_approvisionners,
        ];

        if ($reponse === 'PDF') {
            $htmlContent = view('page.approvisionnement_emballage.approvisionner_emballage.imprimer.imprimer', $data)->render();

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
            $prefixe = 'approv';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if ($reponse === 'EXCEL') {
            $prefixe = 'approv_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new ApprovEmballagePrintExport($data), $nom_excel);
        }
    }


    public function getProductsByCategoryApprovEmballage(Request $request)
    {

        $categorie_id = $request->input('category_id');


        if ($categorie_id === 'Toutes') {
            $emballages =  DB::table('ligne_appro_emballages')
                ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('emballages.id', 'emballages.Reference', 'emballages.Nom_Emballage as Designation')
                ->distinct()
                ->get();
        } else {
            $emballages =  DB::table('ligne_appro_emballages')
                ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('emballages.id', 'emballages.Reference', 'emballages.Nom_Emballage as Designation')
                ->where('categorie_emballages.id', '=', $categorie_id)
                ->distinct()
                ->get();
        }

        return response()->json($emballages);
    }

    public function changeStatutnotificationApprovEnb($id)
    {
        $approvisionnement = NotificationApprovEmballages::find($id);
        $approvisionnement->Statut = 0;
        $approvisionnement->save();
        return redirect()->back();
    }
    public function changeStatutnotificationAchemi($id)
    {
        $approvisionnement = NotificationAchemi::find($id);
        $approvisionnement->Statut = 0;
        $approvisionnement->save();
        return redirect()->back();
    }
    public function changeStatutnotificationAchemiEmb($id)
    {
        $approvisionnement = NotificationAchemiEmballage::find($id);
        $approvisionnement->Statut = 0;
        $approvisionnement->save();
        return redirect()->back();
    }
    public function changeStatutnotificationApprov($id)
    {
        $approvisionnement = NotificationApprovs::find($id);
        $approvisionnement->Statut = 0;
        $approvisionnement->save();
        return redirect()->back();
    }
}
