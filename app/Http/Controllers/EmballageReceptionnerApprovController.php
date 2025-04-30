<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use Illuminate\Http\Request;
use App\Models\ApproEmballage;
use App\Models\StockEmballage;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use App\Models\LigneApproEmballage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ReceptionApproEmballage;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Validator;
use App\Models\LigneReceptionApproEmballage;
use App\Exports\EmballageReceptionApprovPrintExport;
use App\Exports\EmballageReceptionApprovActionPrintExport;

class EmballageReceptionnerApprovController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-reception-approvisionnement-emballage');

        $annees = ReceptionApproEmballage::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $site_id = session()->get('site_id');
        $receptions = DB::table('reception_appro_emballages')
            ->join('agences', 'reception_appro_emballages.Id_Agence', 'agences.id')
            ->join('users', 'reception_appro_emballages.Id_Utilisateur', 'users.id')
            ->select('users.name', 'agences.NomAgence', 'reception_appro_emballages.*')
            ->where('reception_appro_emballages.Id_Agence', '=', $site_id)
            ->orderBy('reception_appro_emballages.id', 'desc')
            ->whereMonth('reception_appro_emballages.created_at', $currentMonth)
            ->whereYear('reception_appro_emballages.created_at', $currentYear)
            ->get();


        return view('page.approvisionnement_emballage.receptionner_emballage.receptionner',
            [
                'receptions' => $receptions,
                'annees' => $annees
            ]
        );
    }
    public function filterReceptionApproEmballage(Request $request)
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
                $query =  DB::table('reception_appro_emballages')
                ->join('agences', 'reception_appro_emballages.Id_Agence', 'agences.id')
                ->join('users', 'reception_appro_emballages.Id_Utilisateur', 'users.id')
                ->select('users.name', 'agences.NomAgence', 'reception_appro_emballages.*')
                ->where('reception_appro_emballages.Id_Agence', '=', $Agence_id)
                ->orderBy('reception_appro_emballages.id', 'desc');
            } else {
                $query =  DB::table('reception_appro_emballages')
                ->join('agences', 'reception_appro_emballages.Id_Agence', 'agences.id')
                ->join('users', 'reception_appro_emballages.Id_Utilisateur', 'users.id')
                ->select('users.name', 'agences.NomAgence', 'reception_appro_emballages.*')
                ->where('reception_appro_emballages.Id_Agence', '=', $Agence_id)
                ->orderBy('reception_appro_emballages.id', 'desc');
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('reception_appro_emballages.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('reception_appro_emballages.created_at', $month);
                $query->whereYear('reception_appro_emballages.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('reception_appro_emballages.created_at', '>=', $startDate)
                      ->where('reception_appro_emballages.created_at', '<=', $endDate);
            }


            // Exécuter la requête
            $receptions = $query->get();
            $annees = ReceptionApproEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');



            return view('page.approvisionnement_emballage.receptionner_emballage.receptionner',
            [
                'receptions' => $receptions,
                'annees' => $annees
            ]
        );

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('receptionner-approvisionnement-emballage');
        $site_id = session()->get('site_id');
        $agences = DB::table('agences')
            ->join('appro_emballages', 'appro_emballages.Id_Agence_Source', 'agences.id')
            ->distinct()
            ->where('appro_emballages.Id_Agence_Destination', $site_id)
            ->select('agences.id', 'agences.NomAgence')
            ->get();

        // $appro_emballages = DB::table('appro_emballages')
        //     ->where('appro_emballages.Id_Agence_Destination', $site_id)
        //     ->get();

        $approvisionnements = DB::table('appro_emballages')
            ->join('ligne_appro_emballages', 'ligne_appro_emballages.Id_Appro_Emballage', '=', 'appro_emballages.id')
            ->where('appro_emballages.Id_Agence_Destination', '=', $site_id)
            ->wherenot('appro_emballages.Statut_appro', 'RECEPTION_TERMINEE')
            ->select(
                'appro_emballages.id',
                DB::raw('SUM(ligne_appro_emballages.Qte_Approvisionnee) as Qte_Approvisionnee'),
                DB::raw('MAX(appro_emballages.Reference_Appro_Emballage) as Reference_Approvisionnement'),
                DB::raw('MAX(appro_emballages.Id_Agence_Source) as Id_Agence_Source')
            )
            ->groupBy('appro_emballages.id')
            ->orderBy('appro_emballages.id', 'desc')
            ->get();

        $approvisionners = DB::table('ligne_appro_emballages')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('ligne_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            // ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $id)
            ->get();

        $receptionners = LigneReceptionApproEmballage::all();

        // dd($appro_emballages);
        return view('page.approvisionnement_emballage.receptionner_emballage.nouveau',
            [
                'agences' => $agences,
                'approvisionnements' => $approvisionnements,
                'approvisionners' => $approvisionners,
                'receptionners' => $receptionners,
            ]
        );
    }


    public function getApprovEmballage(Request $request)
    {


        $data_approv = $request->input('category_id');

        $magasin_formate = explode('-', $data_approv, 2);
        $id_apporv = trim($magasin_formate[0]);

        $approvs = DB::table('ligne_appro_emballages')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', 'stock_emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
            ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $id_apporv)
            ->whereColumn('ligne_appro_emballages.Qte_Receptionnee', '!=', 'ligne_appro_emballages.Qte_Approvisionnee')
            ->select('ligne_appro_emballages.*', 'magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'emballages.id as emballages_id',)
            ->get();

       // dd($approvs);

        return response()->json($approvs);
    }

    public function store(Request $request)
    {
        $this->authorize('receptionner-approvisionnement-emballage');
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
        $prefix = $prefixe->reception_approvisionnement ?? '';

        // Obtenez le dernier numéro de référence enregistré
        //   $lastReference = Reglement::count();
        $lastReference = ReceptionApproEmballage::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            //   $lastReference = Reglement::orderBy('id', 'desc')->first();
            $lastReference = ReceptionApproEmballage::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Reception, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        if (is_array(getIdAgenceByUser())) {
            $site_id = 1;
        } else {
            $site_id = $request->session()->get('site_id');
        }

        //variable de creation entree produit
        $reference_reglement = "{$site_id}/{$lastDigitOfYear}/REEMB/{$incrementedReferenceNumber}";
        $date_reglement =  Carbon::now();
        // $fournisseur = $request->input('fournisseur');
        $client = $request->input('client');
        $observation = $request->input('observation');

        // dd($magasin_source, $magasin_destination);

        //   dd($site_id);
        $reception_approv = new ReceptionApproEmballage();
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
            $magasin_destination_id = $magasin_formate[0];
            $approvisionner_id = trim($produit_formate[0]);
            $stock_id = trim($produit_formate[1]);

            $stock = StockEmballage::find($stock_id);
            $emballage_id = $stock->Id_Emballage;

            $stock_destination = StockEmballage::where('Id_Emballage', '=', $emballage_id)->where('Id_Magasin', '=', $magasin_destination_id)->first();

            if (empty($stock_destination)) {
                $stock_destination = new StockEmballage();
                $stock_destination->Id_Emballage = $emballage_id;
                $stock_destination->Id_Magasin = $magasin_destination_id;
                $stock_destination->Qte_stockee = 0;
                $stock_destination->Prix_Achat_Net = 0;
                $stock_destination->Enregistrer_par = auth()->user()->id;
                $stock_destination->save();
            }
            // dd($stock_destination);
            $approvisionner = LigneApproEmballage::find($approvisionner_id);
            $new_quantity_approv = $approvisionner->Qte_Receptionnee + $value['montant'];

            $sommeAppro = DB::table('ligne_appro_emballages')
                ->join('appro_emballages', 'ligne_appro_emballages.Id_Appro_Emballage', 'appro_emballages.id')
                ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $approv_id)
                ->sum('Qte_Approvisionnee');


            $sommeRecept = DB::table('ligne_reception_appro_emballages')
                ->join('ligne_appro_emballages', 'ligne_reception_appro_emballages.Id_Ligne_Appro_Emballage', 'ligne_appro_emballages.id')
                ->join('appro_emballages', 'ligne_appro_emballages.Id_Appro_Emballage', 'appro_emballages.id')
                ->where('ligne_appro_emballages.Id_Appro_Emballage', '=', $approv_id)
                ->sum('ligne_reception_appro_emballages.Qte_Receptionnee');

            // dd($sommeAppro, $sommeRecept);

            $somme_total = $sommeRecept + $value['montant'];

            if ($sommeAppro > $somme_total) {
                $statut_appro = 'PARTIELLEMENT RECEPTIONNEE';
            } else {
                $statut_appro = 'RECEPTION_TERMINEE';
            }

            // dd($approvisionner->Qte_Approvisionnee, $new_quantity_approv);

            $new_quantity = $stock_destination->Qte_stockee + $value['montant'];

            if ($stock_destination) {
                $stock_destination->Qte_stockee = $new_quantity;
                $stock_destination->update();

                $approvisionner->Qte_Receptionnee = $new_quantity_approv;
                $approvisionner->update();
            }

            $Qte_Receptionnee_total += $approvisionner->Qte_Receptionnee;
            $qte_appro_total += $approvisionner->Qte_Approvisionnee;

            $approv_exist = ApproEmballage::find($approv_id);
            $approv_exist->Statut_appro = $statut_appro;

            $approv_exist->update();

            $detail_reglement = new LigneReceptionApproEmballage();
            $detail_reglement->Id_Reception_Emballage = $reception_approv->id;
            $detail_reglement->Id_Ligne_Appro_Emballage = $approvisionner->id;
            $detail_reglement->Id_Magasin = $magasin_destination_id;
            $detail_reglement->Qte_Receptionnee = $value['montant'];
            $detail_reglement->save();

            $date_entree =  Carbon::now();
            $historique_entree_produit = new StockEmballageHistories();
            $historique_entree_produit->Date = $date_entree;
            $historique_entree_produit->agence_id = $site_id;
            $historique_entree_produit->Motif = 'Entree de produit par une réception d\'approvisionnement';
            $historique_entree_produit->Justificatif = $reception_approv->Reference_Reception;
            $historique_entree_produit->operation = 'ENTREE';
            $historique_entree_produit->type_operation = 'ENTREE';
            $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
            $historique_entree_produit->Id_Emballage = $stock_destination->Id_Produit;
            $historique_entree_produit->Id_Magasin = $stock_destination->Id_Magasin;
            $historique_entree_produit->Quantite = $value['montant'];
            $historique_entree_produit->save();
        }

        // $qte_receptionne_global = $Qte_Receptionnee_total;
        // $qte_appro_global = $qte_appro_total;

        // dd($qte_receptionne_global, $qte_appro_global);

        // if ($qte_receptionne_global == $qte_appro_global) {
        //     $approv = ApproEmballage::find($approv_id);
        //     $approv->Statut_appro = "RECEPTION_TERMINEE";
        //     $approv->update();
        // }


        // dd($reste_a_payer);
        return to_route('reception_approvisionnement_emballage')->with('success', 'La réceptionn a bien été ajouté');
    }

    public function getReceptionnerApprov($id)
    {
        // try {
        $annees = ReceptionApproEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;


        $site_id = session()->get('site_id');

        $receptions = DB::table('reception_appro_emballages')
            ->join('agences', 'reception_appro_emballages.Id_Agence', 'agences.id')
            ->join('users', 'reception_appro_emballages.Id_Utilisateur', 'users.id')
            ->select('users.name', 'agences.NomAgence', 'reception_appro_emballages.*')
            ->where('reception_appro_emballages.Id_Agence', '=', $site_id)
            ->whereMonth('reception_appro_emballages.Date_Reception', $currentMonth)
            ->whereYear('reception_appro_emballages.Date_Reception', $currentYear)
            ->orderBy('reception_appro_emballages.id', 'desc')
            ->get();

        $receptionners = DB::table('ligne_reception_appro_emballages')
            ->join('ligne_appro_emballages', 'ligne_reception_appro_emballages.Id_Ligne_Appro_Emballage', '=', 'ligne_appro_emballages.id')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('ligne_reception_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle', 'ligne_appro_emballages.Qte_Approvisionnee')
            ->where('ligne_reception_appro_emballages.Id_Reception_Emballage', '=', $id)
            ->get();

        return view(
            'page.approvisionnement_emballage.receptionner_emballage.receptionner',
            [
                'receptionners' => $receptionners,
                'receptions' => $receptions,
                'annees' => $annees
            ]
        );
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function ReceptionApprovImprimerAction(Request $request)
    {
        // try {
        $id_approv = $request->input('id_approv');
        $reponse = $request->input('reponse');
        if (empty($id_approv)) {
            return redirect()->back()->with('warning', 'ID de transfert non fourni');
        }

        $receptions = DB::table('reception_appro_emballages')
            ->join('agences', 'reception_appro_emballages.Id_Agence', 'agences.id')
            ->join('users', 'reception_appro_emballages.Id_Utilisateur', 'users.id')
            ->select('reception_appro_emballages.id', 'reception_appro_emballages.Date_Reception', 'reception_appro_emballages.Reference_Reception', 'reception_appro_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_appro_emballages.id', $id_approv)
            ->get();


        if ($receptions->isEmpty()) {
            return redirect()->back()->with('warning', 'approvisionnement non trouvé');
        }


        $ligne_receptionners = DB::table('ligne_reception_appro_emballages')
            ->join('ligne_appro_emballages', 'ligne_reception_appro_emballages.Id_Ligne_Appro_Emballage', '=', 'ligne_appro_emballages.id')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('ligne_reception_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            ->where('ligne_reception_appro_emballages.Id_Reception_Emballage', '=', $id_approv)
            ->get();

        // dd($receptions, $ligne_receptionners );

        if ($ligne_receptionners->isEmpty()) {
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
            'receptions' => $receptions,
            'ligne_receptions' => $ligne_receptionners,
        ];

        if ($reponse === 'imprimer') {
            $htmlContent = view('page.approvisionnement_emballage.receptionner_emballage.imprimer.imprimer-action', $data)->render();

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
            $prefixe = 'reception_approv';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if ($reponse === 'exporter') {
            $prefixe = 'reception_approv_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new EmballageReceptionApprovActionPrintExport($data), $nom_excel);
        }



        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function ReceptionApprovOngletImpression(Request $request)
    {


        $site_id = session()->get('site_id');

        $reponse = $request->input('submit');

        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $magasin = $request->input('agence');
        $categorie = $request->input('categorie');
        $produit = $request->input('produit');

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


        $query_magasin = DB::table('ligne_reception_appro_emballages')
            ->join('magasins', 'ligne_reception_appro_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('reception_appro_emballages', 'ligne_reception_appro_emballages.Id_Reception_Emballage', '=', 'reception_appro_emballages.id')
            ->select(
                'ligne_reception_appro_emballages.Id_Magasin',
                'magasins.NomMagasin',
                DB::raw('MAX(ligne_reception_appro_emballages.created_at) as created_at')
            )
            ->where('reception_appro_emballages.Id_Agence', '=', $site_id)
            ->groupBy('ligne_reception_appro_emballages.Id_Magasin', 'magasins.NomMagasin');



        $query_receptionner = DB::table('ligne_reception_appro_emballages')
            ->join('ligne_appro_emballages', 'ligne_reception_appro_emballages.Id_Ligne_Appro_Emballage', '=', 'ligne_appro_emballages.id')
            ->join('reception_appro_emballages', 'ligne_reception_appro_emballages.Id_Reception_Emballage', '=', 'reception_appro_emballages.id')
            ->join('stock_emballages', 'ligne_appro_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->where('reception_appro_emballages.Id_Agence', '=', $site_id)
            ->select('ligne_reception_appro_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            ->whereBetween('ligne_reception_appro_emballages.created_at', [$date_debut_periode, $date_fin_periode]);

        // dd($query_magasin, $query_receptionner);

        if ($magasin === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes') {
            $data_magasins = $query_magasin->get();
            $data_receptions = $query_receptionner->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes') {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_magasins = $query_magasin->get();
            $data_receptions = $query_receptionner
                ->where('emballages.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) >= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {

            $data_magasins = $query_magasin->get();
            $data_receptions = $query_receptionner
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes') {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('emballages.id', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } else {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $data = [
            'texteEntetePied' => $texteEntetePied,
            'imageEntetePied' => $imageEntetePied,
            'data_magasins' => $data_magasins,
            'data_receptions' => $data_receptions,
        ];

        if ($reponse === 'PDF') {
            $htmlContent = view('page.approvisionnement_emballage.receptionner_emballage.imprimer.imprimer', $data)->render();

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
            $prefixe = 'reception_approv';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if ($reponse === 'EXCEL') {
            $prefixe = 'reception_approv_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new EmballageReceptionApprovPrintExport($data), $nom_excel);
        }
    }


    public function getProductsByCategoryReception(Request $request)
    {

        $categorie_id = $request->input('category_id');

        if ($categorie_id === 'Toutes') {
            $emballages =  DB::table('receptionner_appros')
                ->join('ligne_appro_emballages', 'receptionner_appros.Id_Approvisionner', '=', 'ligne_appro_emballages.id')
                ->join('stocks', 'ligne_appro_emballages.Id_Stock', '=', 'stocks.id')
                ->join('emballages', 'stocks.Id_Produit', '=', 'emballages.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('emballages.id', 'emballages.Reference', 'emballages.Designation')
                ->distinct()
                ->get();
        } else {
            $emballages =  DB::table('receptionner_appros')
                ->join('ligne_appro_emballages', 'receptionner_appros.Id_Approvisionner', '=', 'ligne_appro_emballages.id')
                ->join('stocks', 'ligne_appro_emballages.Id_Stock', '=', 'stocks.id')
                ->join('emballages', 'stocks.Id_Produit', '=', 'emballages.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('emballages.id', 'emballages.Reference', 'emballages.Designation')
                ->where('categorie_emballages.id', '=', $categorie_id)
                ->distinct()
                ->get();
        }
        return response()->json($emballages);
    }
}
