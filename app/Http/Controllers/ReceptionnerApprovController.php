<?php

namespace App\Http\Controllers;

use App\Exports\ReceptionApprovActionPrintExport;
use App\Exports\ReceptionApprovPrintExport;
use App\Models\Agence;
use App\Models\Approvisionnement;
use App\Models\Approvisionner;
use App\Models\Client;
use App\Models\Image;
use App\Models\PrefixeReference;
use App\Models\receptionAppro;
use App\Models\receptionnerAppro;
use App\Models\Stock;
use App\Models\StockHistories;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use DragonCode\Contracts\Cashier\Auth\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ReceptionnerApprovController extends Controller
{

    public function index()
    {
        $this->authorize('consulter-liste-reception-approvisionnement');
        $site_id = session()->get('site_id');
        $receptions = DB::table('reception_appros')
            ->join('agences', 'reception_appros.Id_Agence', 'agences.id')
            ->join('users', 'reception_appros.Id_Utilisateur', 'users.id')
            ->select('users.name', 'agences.NomAgence', 'reception_appros.*')
            ->where('reception_appros.Id_Agence', '=', $site_id)
            ->orderBy('reception_appros.id', 'desc')
            ->get();

        $annees = receptionAppro::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $agence = Agence::find($site_id);

        if ($agence->id === 1) {
            return to_route('approvisionner')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
        }

        return view(
            'page.approvisionnement.receptionner.receptionner',
            [
                'receptions' => $receptions,
                'annees' => $annees
            ]
        );
    }

    public function create()
    {
        $this->authorize('receptionner-approvisionnement');
        $site_id = session()->get('site_id');
        $agences = DB::table('agences')
            ->join('approvisionnements', 'approvisionnements.Id_Agence_Source', 'agences.id')
            ->distinct()
            ->where('approvisionnements.Id_Agence_Destination', $site_id)
            ->select('agences.id', 'agences.NomAgence')
            ->get();

        // $approvisionnements = DB::table('approvisionnements')
        //     ->where('approvisionnements.Id_Agence_Destination', $site_id)
        //     ->get();

        $approvisionnements = DB::table('approvisionnements')
            ->join('approvisionners', 'approvisionners.Id_Approvisionnement', '=', 'approvisionnements.id')
            ->where('approvisionnements.Id_Agence_Destination', '=', $site_id)
            ->wherenot('approvisionnements.Statut_appro', 'RECEPTION_TERMINEE')
            ->select(
                'approvisionnements.id',
                DB::raw('SUM(approvisionners.Qte_Approvisionnee) as Qte_Approvisionnee'),
                DB::raw('MAX(approvisionnements.Reference_Approvisionnement) as Reference_Approvisionnement'),
                DB::raw('MAX(approvisionnements.Id_Agence_Source) as Id_Agence_Source')
            )
            ->groupBy('approvisionnements.id')
            ->orderBy('approvisionnements.id', 'desc')
            ->get();

        $approvisionners = DB::table('approvisionners')
            ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('approvisionners.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            // ->where('approvisionners.Id_Approvisionnement', '=', $id)
            ->get();

        $receptionners = receptionnerAppro::all();


        $agence = Agence::find($site_id);

        if ($agence->id === 1) {
            return to_route('approvisionner')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
        }

        // dd($approvisionnements);
        return view('page.approvisionnement.receptionner.nouveau',
            [
                'agences' => $agences,
                'approvisionnements' => $approvisionnements,
                'approvisionners' => $approvisionners,
                'receptionners' => $receptionners,
            ]
        );
    }

    public function store(Request $request)
    {
        $this->authorize('receptionner-approvisionnement');
        $validator = Validator::make(
            $request->all(),
            [
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
        $lastReference = receptionAppro::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            //   $lastReference = Reglement::orderBy('id', 'desc')->first();
            $lastReference = receptionAppro::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
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

        // dd($magasin_source, $magasin_destination);

        //   dd($site_id);
        $reception_approv = new receptionAppro();
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

            $stock = Stock::find($stock_id);
            $produit_id = $stock->Id_Produit;

            $stock_destination = Stock::where('Id_Produit', '=', $produit_id)->where('Id_Magasin', '=', $magasin_destination_id)->first();

            if (empty($stock_destination)) {
                $stock_destination = new Stock();
                $stock_destination->Id_Produit = $produit_id;
                $stock_destination->Id_Magasin = $magasin_destination_id;
                $stock_destination->Qte_stockee = 0;
                $stock_destination->Prix_Achat_Net = 0;
                $stock_destination->Enregistrer_par = auth()->user()->id;
                $stock_destination->save();
            }



            $sommeAppro = DB::table('approvisionners')
                ->join('approvisionnements', 'approvisionners.Id_Approvisionnement', 'approvisionnements.id')
                ->where('approvisionners.Id_Approvisionnement', '=', $approv_id)
                ->sum('Qte_Approvisionnee');


            $sommeRecept = DB::table('receptionner_appros')
                ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', 'approvisionners.id')
                ->join('approvisionnements', 'approvisionners.Id_Approvisionnement', 'approvisionnements.id')
                ->where('approvisionners.Id_Approvisionnement', '=', $approv_id)
                ->sum('receptionner_appros.Qte_Receptionnee');


            // dd($sommeAppro, $sommeRecept);

            $somme_total = $sommeRecept + $value['montant'];

            if ($sommeAppro > $somme_total) {
                $statut_appro = 'PARTIELLEMENT RECEPTIONNEE';
            } else {
                $statut_appro = 'RECEPTION_TERMINEE';
            }



            // dd($stock_destination);
            $approvisionner = Approvisionner::find($approvisionner_id);

            $new_quantity_approv = $approvisionner->Qte_Receptionnee + $value['montant'];

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

            $approv_exist = Approvisionnement::find($approv_id);

            $approv_exist->Statut_appro = $statut_appro;
            $approv_exist->update();

            $detail_reglement = new receptionnerAppro();
            $detail_reglement->Id_Reception = $reception_approv->id;
            $detail_reglement->Id_Approvisionner = $approvisionner->id;
            $detail_reglement->Id_Magasin = $magasin_destination_id;
            $detail_reglement->Qte_Receptionnee = $value['montant'];
            $detail_reglement->save();

            $date_entree =  Carbon::now();
            $historique_entree_produit = new StockHistories();
            $historique_entree_produit->Date = $date_entree;
            $historique_entree_produit->agence_id = $site_id;
            $historique_entree_produit->Motif = 'Entree de produit par une réception d\'approvisionnement';
            $historique_entree_produit->Justificatif = $reception_approv->Reference_Reception;
            $historique_entree_produit->operation = 'ENTREE';
            $historique_entree_produit->type_operation = 'ENTREE';
            $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
            $historique_entree_produit->Id_Produit = $stock_destination->Id_Produit;
            $historique_entree_produit->Id_Magasin = $stock_destination->Id_Magasin;
            $historique_entree_produit->Quantite = $value['montant'];
            $historique_entree_produit->save();
        }

        // $qte_receptionne_global = $Qte_Receptionnee_total;
        // $qte_appro_global = $qte_appro_total;

        // // dd($qte_receptionne_global, $qte_appro_global);

        // if ($qte_receptionne_global == $qte_appro_global) {
        //     $approv = Approvisionnement::find($approv_id);
        //     $approv->Statut_appro = "RECEPTION_TERMINEE";
        //     $approv->update();
        // }


        // dd($reste_a_payer);
        return to_route('reception_approvisionnement')->with('success', 'La réceptionn a bien été ajouté');
    }

    public function getApprov(Request $request)
    {


        $data_approv = $request->input('category_id');

        $magasin_formate = explode('-', $data_approv, 2);
        $id_apporv = trim($magasin_formate[0]);

        $approvs = DB::table('approvisionners')
            ->join('stocks', 'approvisionners.Id_Stock', 'stocks.id')
            ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            ->join('produits', 'stocks.Id_Produit', 'produits.id')
            ->where('approvisionners.Id_Approvisionnement', '=', $id_apporv)
            ->whereColumn('approvisionners.Qte_Receptionnee', '!=', 'approvisionners.Qte_Approvisionnee')

            ->select('approvisionners.*', 'magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'produits.id as produits_id',)
            ->get();

        // dd($approvs);

        return response()->json($approvs);
    }

    public function getReceptionnerApprov($id)
    {


        // try {
        $annees = Approvisionnement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');


        $site_id = session()->get('site_id');
        $receptions = DB::table('reception_appros')
            ->join('agences', 'reception_appros.Id_Agence', 'agences.id')
            ->join('users', 'reception_appros.Id_Utilisateur', 'users.id')
            ->select('users.name', 'agences.NomAgence', 'reception_appros.*')
            ->where('reception_appros.Id_Agence', '=', $site_id)
            ->orderBy('reception_appros.id', 'desc')
            ->get();


        $receptionners = DB::table('receptionner_appros')
            ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
            ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('receptionner_appros.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle', 'approvisionners.Qte_Approvisionnee')
            ->where('receptionner_appros.Id_Reception', '=', $id)
            ->get();


        return view(
            'page.approvisionnement.receptionner.receptionner',
            [
                'receptionners' => $receptionners,
                'receptions' => $receptions,
            ]
        );
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function ReceptionApprovImprimerAction(Request $request)
    {
        $id_approv = $request->input('id_approv');
        $reponse = $request->input('reponse');
        if (empty($id_approv)) {
            return redirect()->back()->with('warning', 'ID de transfert non fourni');
        }

        $receptions = DB::table('reception_appros')
            ->join('agences', 'reception_appros.Id_Agence', 'agences.id')
            ->join('users', 'reception_appros.Id_Utilisateur', 'users.id')
            ->select('reception_appros.id', 'reception_appros.Date_Reception', 'reception_appros.Reference_Reception', 'reception_appros.Observations', 'users.name', 'agences.NomAgence')
            ->where('reception_appros.id', $id_approv)
            ->get();


        if ($receptions->isEmpty()) {
            return redirect()->back()->with('warning', 'approvisionnement non trouvé');
        }

        $ligne_receptionners = DB::table('receptionner_appros')
            ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
            ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('receptionner_appros.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            ->where('receptionner_appros.Id_Reception', '=', $id_approv)
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
            $htmlContent = view('page.approvisionnement.receptionner.imprimer.imprimer-action', $data)->render();

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

            return Excel::download(new ReceptionApprovActionPrintExport($data), $nom_excel);
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


        $query_magasin = DB::table('receptionner_appros')
            ->join('magasins', 'receptionner_appros.Id_Magasin', '=', 'magasins.id')
            ->join('reception_appros', 'receptionner_appros.Id_Reception', '=', 'reception_appros.id')
            ->select(
                'receptionner_appros.Id_Magasin',
                'magasins.NomMagasin',
                DB::raw('MAX(receptionner_appros.created_at) as created_at')
            )
            ->where('reception_appros.Id_Agence', '=', $site_id)
            ->groupBy('receptionner_appros.Id_Magasin', 'magasins.NomMagasin');



        $query_receptionner = DB::table('receptionner_appros')
            ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
            ->join('reception_appros', 'receptionner_appros.Id_Reception', '=', 'reception_appros.id')
            ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->where('reception_appros.Id_Agence', '=', $site_id)
            ->select('receptionner_appros.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            ->whereBetween('receptionner_appros.created_at', [$date_debut_periode, $date_fin_periode]);

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
                ->where('produits.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) >= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {

            $data_magasins = $query_magasin->get();
            $data_receptions = $query_receptionner
                ->where('categorie_produits.id', '=', $categorie)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('produits.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('produits.id', '=', $produit)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } elseif ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes') {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('produits.id', '=', $produit)
                ->where('categorie_produits.id', '=', $categorie)
                ->get();

            if ((count($data_magasins) || count($data_receptions)) <= 0) {
                return redirect()->back()->with('error', 'Aucune données trouvées..');
            }
        } else {

            $data_magasins = $query_magasin->where('magasins.id', $magasin)->get();
            $data_receptions = $query_receptionner
                ->where('receptionner_appros.Id_Magasin', '=', $magasin)
                ->where('produits.id', '=', $produit)
                ->where('categorie_produits.id', '=', $categorie)
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
            $htmlContent = view('page.approvisionnement.receptionner.imprimer.imprimer', $data)->render();

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

            return Excel::download(new ReceptionApprovPrintExport($data), $nom_excel);
        }
    }

    public function getProductsByCategoryReception(Request $request)
    {

        $categorie_id = $request->input('category_id');

        if ($categorie_id === 'Toutes') {
            $produits =  DB::table('receptionner_appros')
                ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
                ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('produits.id', 'produits.Reference', 'produits.Designation')
                ->distinct()
                ->get();
        } else {
            $produits =  DB::table('receptionner_appros')
                ->join('approvisionners', 'receptionner_appros.Id_Approvisionner', '=', 'approvisionners.id')
                ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('produits.id', 'produits.Reference', 'produits.Designation')
                ->where('categorie_produits.id', '=', $categorie_id)
                ->distinct()
                ->get();
        }

        return response()->json($produits);
    }


    public function filterReceptionAppro(Request $request)
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
        $user = FacadesAuth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');


        $query =  DB::table('reception_appros')
            ->join('agences', 'reception_appros.Id_Agence', 'agences.id')
            ->join('users', 'reception_appros.Id_Utilisateur', 'users.id')
            ->select('users.name', 'agences.NomAgence', 'reception_appros.*')
            ->where('reception_appros.Id_Agence', '=', $site_id)
            ->orderBy('reception_appros.id', 'desc');


        if ($annee) {
            $query->whereYear('reception_appros.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('reception_appros.created_at', $month);
            $query->whereYear('reception_appros.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('reception_appros.created_at', '>=', $startDate)
                ->where('reception_appros.created_at', '<=', $endDate);
        }

        $reception_appros = $query->get();


        // $detail_reglements = DB::table('detail_reglements')
        //     ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
        //     ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
        //     ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
        //     ->where('detail_reglements.Id_Reglement', '=', 0)
        //     ->get();


        $listeAgence = Agence::all();
        $clients = Client::all();
        // $mode_paiements = LibelleTypeOperation::all();
        $annees = receptionAppro::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        // dd($reglements, $detail_reglements);

        return view('page.approvisionnement.receptionner.receptionner',  [
            'receptions' => $reception_appros,
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
