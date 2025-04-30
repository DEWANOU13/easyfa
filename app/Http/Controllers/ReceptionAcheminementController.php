<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Acheminer;
use App\Models\Acheminement;
use Illuminate\Http\Request;
use App\Models\receptionAppro;
use App\Models\StockHistories;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ReceptionAcheminement;
use App\Models\ReceptionnerAcheminement;
use Illuminate\Support\Facades\Validator;
use App\Exports\ReceptionAcheminementPeriodeExport;
use App\Exports\ReceptionAcheminementActionPrintExport;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ReceptionAcheminementController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-reception-acheminenment');
        $site_id = session()->get('site_id');
        $listeReception = ReceptionAcheminement::join('agences', 'reception_acheminements.Id_Agence', 'agences.id')
        ->join('users', 'reception_acheminements.Id_Utilisateur', 'users.id')
        ->select('reception_acheminements.id', 'reception_acheminements.Date_Reception', 'reception_acheminements.Reference_Reception', 'reception_acheminements.Observations', 'users.name', 'agences.NomAgence')
        ->where('reception_acheminements.Id_Agence', $site_id)
        ->get();


        $receptionner_acheminements = DB::table('receptionner_acheminements')
        ->join('acheminers', 'receptionner_acheminements.Id_Acheminer', '=', 'acheminers.id')
        ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
        ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
        ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
        ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
        ->select('receptionner_acheminements.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
        ->where('receptionner_acheminements.Id_Reception_Acheminement', '=', 0)
        ->get();

        $annees = ReceptionnerAcheminement::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');


        return view('page.acheminement.receptionner.receptionner',
            ['listeReception' => $listeReception,
            'receptionner_acheminements' => $receptionner_acheminements,
            'annees' => $annees,
            ]
        );
    }
    public function create()
    {
        $this->authorize('receptionner-acheminement');
        $site_id = session()->get('site_id');
        $agences = DB::table('agences')
            ->join('acheminements', 'acheminements.Id_Agence_Source', 'agences.id')
            ->distinct()
            ->where('acheminements.Id_Agence_Destination', $site_id)
            ->select('agences.id', 'agences.NomAgence')
            ->get();


        // $approvisionnements = DB::table('approvisionnements')
        //     ->where('approvisionnements.Id_Agence_Destination', $site_id)
        //     ->get();

        $acheminements = DB::table('acheminements')
            ->join('acheminers', 'acheminers.Id_Acheminement', '=', 'acheminements.id')
            ->where('acheminements.Id_Agence_Destination', '=', $site_id)
            ->wherenot('acheminements.Statut_acheminement', 'RECEPTION_TERMINEE')

            ->select(
                'acheminements.id',
                DB::raw('SUM(acheminers.Qte_acheminee) as Qte_acheminee'),
                DB::raw('MAX(acheminements.Reference_acheminement) as Reference_acheminement'),
                DB::raw('MAX(acheminements.Id_Agence_Source) as Id_Agence_Source')
            )
            ->groupBy('acheminements.id')
            ->orderBy('acheminements.id', 'desc')
            ->get();

        $acheminers = DB::table('acheminers')
            ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('acheminers.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            // ->where('approvisionners.Id_Approvisionnement', '=', $id)
            ->get();

            $receptionners = ReceptionnerAcheminement::all();

            // dd($receptionners);
        return view('page.acheminement.receptionner.nouveau',
            [
                'agences' => $agences,
                'acheminements' => $acheminements,
                'acheminers' => $acheminers,
                'receptionners' => $receptionners,
            ]
        );
    }
    public function getAcheminement(Request $request)
    {



        $data_approv = $request->input('category_id');

        $magasin_formate = explode('-', $data_approv, 2);
        $id_apporv = trim($magasin_formate[0]);
       // dd($id_apporv);

        $approvs = DB::table('acheminers')
            ->join('stocks', 'acheminers.Id_Stock', 'stocks.id')
            ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            ->join('produits', 'stocks.Id_Produit', 'produits.id')
            ->where('acheminers.Id_Acheminement', '=', $id_apporv)
            ->whereColumn('acheminers.Qte_Receptionnee', '!=', 'acheminers.Qte_acheminee')

            ->select('acheminers.*', 'magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'produits.id as produits_id',)
            ->get();

            $annees = ReceptionAcheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');



        // dd($approvs);

        return response()->json($approvs);
    }
    public function store(Request $request)
    {
        $this->authorize('receptionner-acheminement');
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
        $lastReference = ReceptionAcheminement::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            //   $lastReference = Reglement::orderBy('id', 'desc')->first();
            $lastReference = ReceptionAcheminement::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Reception, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        if (is_array(getIdAgenceByUser())) {
            $site_id = 1;
        } else {
            $site_id = $request->session()->get('site_id');
        }

        //variable de creation entree produit
        $reference_reglement = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
        $date_reglement =  Carbon::now();
        // $fournisseur = $request->input('fournisseur');
        $client = $request->input('client');
        $observation = $request->input('observation');


        $reception_approv = new ReceptionAcheminement();
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


            $stock = Stock::find($stock_id);
            $produit_id = $stock->Id_Produit;

            $stock_destination = Stock::where('Id_Produit', '=', $produit_id)->where('Id_Magasin', '=', $magasin_destination_id)->first();

            if(empty($stock_destination)){
                $stock_destination = new Stock();
                $stock_destination->Id_Produit = $produit_id;
                $stock_destination->Id_Magasin = $magasin_destination_id;
                $stock_destination->Qte_stockee = 0;
                $stock_destination->Prix_Achat_Net = 0;
                $stock_destination->Enregistrer_par = auth()->user()->id;
                $stock_destination->save();
            }

            $sommeAppro = DB::table('acheminers')
            ->join('acheminements', 'acheminers.Id_Acheminement', 'acheminements.id')
            ->where('acheminers.Id_Acheminement', '=', $approv_id)
            ->sum('Qte_acheminee');


        $sommeRecept = DB::table('receptionner_acheminements')
            ->join('acheminers', 'receptionner_acheminements.Id_Acheminer', 'acheminers.id')
            ->join('acheminements', 'acheminers.Id_Acheminement', 'acheminements.id')
            ->where('acheminers.Id_Acheminement', '=', $approv_id)
            ->sum('receptionner_acheminements.Qte_Receptionnee');


        // dd($sommeAppro, $sommeRecept);

        $somme_total = $sommeRecept + $value['montant'];

        if ($sommeAppro > $somme_total) {
            $statut_appro = 'PARTIELLEMENT RECEPTIONNEE';
        } else {
            $statut_appro = 'RECEPTION_TERMINEE';
        }
            $approvisionner = Acheminer::find($acheminer_id);



            $new_quantity = $stock_destination->Qte_stockee + $value['montant'];



            if ($stock_destination) {
                $stock_destination->Qte_stockee = $new_quantity;
                $stock_destination->update();

                $approvisionner->Qte_Receptionnee += $value['montant'];
                $approvisionner->update();
            }

            $Qte_Receptionnee_total += $approvisionner->Qte_Receptionnee;
            $qte_appro_total += $approvisionner->Qte_acheminee;

            $approv_exist = Acheminement::find($approv_id);

            $approv_exist->Statut_acheminement = $statut_appro;
            $approv_exist->update();

            $detail_reglement = new ReceptionnerAcheminement();
            $detail_reglement->Id_Reception_Acheminement = $reception_approv->id;
            $detail_reglement->Id_Acheminer = $acheminer_id;
            $detail_reglement->Id_Magasin = $magasin_destination_id;
            $detail_reglement->Qte_Receptionnee = $value['montant'];
            $detail_reglement->save();

            $date_entree =  Carbon::now();
            $historique_entree_produit = new StockHistories();
            $historique_entree_produit->Date = $date_entree;
            $historique_entree_produit->agence_id = $site_id;
            $historique_entree_produit->Motif = 'Entree de produit par une réception d\'acheminement';
            $historique_entree_produit->Justificatif = $reception_approv->Reference_Reception;
            $historique_entree_produit->operation = 'ENTREE';
            $historique_entree_produit->type_operation = 'ENTREE';
            $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
            $historique_entree_produit->Id_Produit = $stock_destination->Id_Produit;
            $historique_entree_produit->Id_Magasin = $stock_destination->Id_Magasin;
            $historique_entree_produit->Quantite = $value['montant'];
            $historique_entree_produit->save();
        }
       /*  $qte_receptionne_global = $Qte_Receptionnee_total;
        $qte_appro_global = $qte_appro_total;

       // dd($qte_receptionne_global, $qte_appro_global);

        if($qte_receptionne_global == $qte_appro_global)
            {
                $acheminement = Acheminement::find($approv_id);
                $acheminement->Statut_acheminement = "RECEPTION_TERMINEE";
                $acheminement->update();

            }
 */
        // dd($reste_a_payer);
        return to_route('reception_acheminement')->with('success', 'La réceptionn a bien été ajouté');
    }
    public function getReceptionLigne($id)
    {

        try {
            $annees = Acheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

            $site_id = session()->get('site_id');
            $listeReception = ReceptionAcheminement::join('agences', 'reception_acheminements.Id_Agence', 'agences.id')
            ->join('users', 'reception_acheminements.Id_Utilisateur', 'users.id')
            ->select('reception_acheminements.id', 'reception_acheminements.Date_Reception', 'reception_acheminements.Reference_Reception', 'reception_acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_acheminements.Id_Agence', $site_id)
            ->get();

            $annees = ReceptionnerAcheminement::selectRaw('YEAR(created_at) as annee');


            $receptionner_acheminements = DB::table('receptionner_acheminements')
                ->join('acheminers', 'receptionner_acheminements.Id_Acheminer', '=', 'acheminers.id')
                ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('receptionner_acheminements.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle','acheminers.Qte_acheminee')
                ->where('receptionner_acheminements.Id_Reception_Acheminement', '=', $id)
                ->get();

            //dd($receptionner_acheminements);

            return view(
                'page.acheminement.receptionner.receptionner',
                [
                    'listeReception' => $listeReception,
                    'receptionner_acheminements' => $receptionner_acheminements,
                    'annees' => $annees,
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

            $reception = ReceptionAcheminement::join('agences', 'reception_acheminements.Id_Agence', 'agences.id')
            ->join('users', 'reception_acheminements.Id_Utilisateur', 'users.id')
            ->select('reception_acheminements.id', 'reception_acheminements.Date_Reception', 'reception_acheminements.Reference_Reception', 'reception_acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_acheminements.id', $id_reception)
            ->get();


            if ($reception->isEmpty()) {
                return redirect()->back()->with('warning', 'Reception non trouvé');
            }

            $recetionner =  DB::table('receptionner_acheminements')
            ->join('acheminers', 'receptionner_acheminements.Id_Acheminer', '=', 'acheminers.id')
            ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('receptionner_acheminements.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            ->where('receptionner_acheminements.Id_Reception_Acheminement', '=', $id_reception)
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
                $htmlContent = view('page.acheminement.receptionner.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'Reception_Acheminement';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if($reponse === 'exporter'){
                $prefixe = 'Reception_Acheminement';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ReceptionAcheminementActionPrintExport($data), $nom_excel);
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

            $query = DB::table('reception_acheminements')
                ->join('agences', 'reception_acheminements.Id_Agence', 'agences.id')
                ->join('users', 'reception_acheminements.Id_Utilisateur', 'users.id')
                ->whereBetween('reception_acheminements.Date_Reception', [$date_debut_periode, $date_fin_periode])
                ->select('reception_acheminements.*','users.name', 'agences.NomAgence');

            if($agence === 'Tous') {

                $liste_reception = $query->get();
                if (count($liste_reception) > 0) {
                    $get_request = $liste_reception;
                } else {
                    return to_route('reception_acheminement')->with('error', 'Aucunes données trouvées!');
                }
            }
            if($agence !== 'Tous') {
                $liste_reception = $query->where('reception_acheminements.Id_Agence', $agence)->get();

                if (count($liste_reception) > 0) {
                    $get_request = $liste_reception;
                } else {
                    return to_route('reception_acheminement')->with('error', 'Aucunes données trouvées!');
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
                return Excel::download(new ReceptionAcheminementPeriodeExport($data), 'reception_acheminements_'.$date_et_heure.'.xlsx');
            }

       /*   } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }

    public function filterReceptionAche(Request $request)
    {
        $this->authorize('consulter-reglement');
        $site_id = session()->get('site_id');
        // try{
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $annee = $request->query('annee');

        // Vérification de l'utilisateur et de l'agence
        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');


        $query = ReceptionAcheminement::join('agences', 'reception_acheminements.Id_Agence', 'agences.id')
        ->join('users', 'reception_acheminements.Id_Utilisateur', 'users.id')
        ->select('reception_acheminements.id', 'reception_acheminements.Date_Reception', 'reception_acheminements.Reference_Reception', 'reception_acheminements.Observations', 'users.name', 'agences.NomAgence')
        ->where('reception_acheminements.Id_Agence', $site_id);


        if ($annee) {
            $query->whereYear('reception_acheminements.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('reception_acheminements.created_at', $month);
            $query->whereYear('reception_acheminements.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('reception_acheminements.created_at', '>=', $startDate)
                ->where('reception_acheminements.created_at', '<=', $endDate);
        }

        $listeReception = $query->get();


        // $detail_reglements = DB::table('detail_reglements')
        //     ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
        //     ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
        //     ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
        //     ->where('detail_reglements.Id_Reglement', '=', 0)
        //     ->get();


        $listeAgence = Agence::all();
        $clients = Client::all();
        // $mode_paiements = LibelleTypeOperation::all();
        $annees = ReceptionAcheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        // dd($reglements, $detail_reglements);

        return view('page.acheminement.receptionner.receptionner',  [
            'listeReception' => $listeReception,
            // 'detail_reglements' => $detail_reglements,
            'clients' => $clients,
            // 'mode_paiements' => $mode_paiements,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

}
