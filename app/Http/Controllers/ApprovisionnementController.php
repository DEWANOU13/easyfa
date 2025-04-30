<?php

namespace App\Http\Controllers;

use App\Exports\ApprovActionPrintExport;
use App\Exports\ApprovPrintExport;
use App\Models\Agence;
use App\Models\Approvisionnement;
use App\Models\Approvisionner;
use App\Models\Client;
use App\Models\EntreeProduit;
use App\Models\Image;
use App\Models\NotificationApprovs;
use App\Models\PrefixeReference;
use App\Models\Produit;
use App\Models\SortieProduit;
use App\Models\Stock;
use App\Models\StockHistories;
use App\Models\Transferer;
use App\Models\TransfertProduit;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ApprovisionnementController extends Controller
{
    //

    public function index()
    {
        $this->authorize('consulter-liste-approvisionnement');
        $approvisionnements = DB::table('approvisionnements')
            ->join('agences', 'approvisionnements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'approvisionnements.Id_Utilisateur', 'users.id')
            ->select('approvisionnements.id', 'approvisionnements.Date_Appro', 'approvisionnements.Statut_appro', 'approvisionnements.Reference_Approvisionnement', 'approvisionnements.Observations', 'users.name', 'agences.NomAgence')
            ->get();

        $annees = Approvisionnement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $site_id = session()->get('site_id');

        $agence = Agence::find($site_id);

        if ($agence->id !== 1) {
            return to_route('reception_approvisionnement')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
        }

        return view(
            'page.approvisionnement.approvisionner.approvisionner',
            [
                'approvisionnements' => $approvisionnements,
                'annees' => $annees,
            ]
        );
    }

    public function  create(Request $request)
    {
        $this->authorize('effectuer-approvisionnement');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            $site_id = session()->get('site_id');
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stocks')
                ->join('produits', 'stocks.Id_produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'magasins.agence_id')
                ->where('stocks.Qte_stockee', '>', 0)

                ->where('magasins.agence_id', '=', $site_id)
                ->get();

            // dd($stock_produits);

            $agence_source = Agence::where('NomAgence', '=', 'Siège')->orderBy('id', 'desc')->get();
            $agence_destination = Agence::where('NomAgence', '<>', 'Siège')->orderBy('id', 'desc')->get();
            // $agences = Agence::orderBy('id', 'desc')->get();


            $agence = Agence::find($site_id);

            if ($agence->id !== 1) {
                return to_route('reception_approvisionnement')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
            }

            return view('page.approvisionnement.approvisionner.nouveau', [
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
        $this->authorize('effectuer-approvisionnement');
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
            $lastReference = Approvisionnement::where('Id_Agence_Source', '=', session()->get('site_id'))->count();
            $site_id = session()->get('site_id');
            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = TransfertProduit::orderBy('id', 'desc')->first();
                $lastReference = Approvisionnement::where('Id_Agence_Source', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Approvisionnement, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->approvisionnement ?? '';
            //variable de creation entree produit
            $reference_approvisionnement = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
            $date_approvisionnement =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $id_agence_source = $request->input('magasin_source');
            $id_agence_destination = $request->input('magasin_destination');

            // dd($id_agence_destination, $id_agence_source);

            $approvisionnement = new Approvisionnement();
            $approvisionnement->Date_Appro = $date_approvisionnement;
            $approvisionnement->Id_Utilisateur = auth()->user()->id;
            $approvisionnement->Reference_Approvisionnement = $reference_approvisionnement;
            $approvisionnement->Observations = $observation;
            $approvisionnement->Statut_appro = 'EN COURS';
            $approvisionnement->Id_Agence_Source = $id_agence_source;
            $approvisionnement->Id_Agence_Destination = $id_agence_destination;
            $approvisionnement->Enregistrer_par = auth()->user()->id;
            $approvisionnement-> create();
            $id_agence_destination->save();

            $quantite_total_approv = 0;


            foreach ($request->inputs as $value) {

                $produit_formate = explode(' ', $value['produit'], 2);
                $reference_produit = $produit_formate[1];
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode(' ', $produit_formate[1], 2);
                // $magasin_formate = explode('-', $value['magasin'], 2);
                // $id_magasin = trim($magasin_formate[0]);
                // dd($id_stock);

                $produit = Produit::where('Reference', '=', $reference_produit_formate[0])->first();
                // dd($produit);

                $stock = Stock::find($id_stock);
                // dd($stock);

                // dd($stock_source->id, $stock_destination->id);
                $new_quantite_stock = $stock->Qte_stockee - $value['quantity_transfer'];
                $stock->Qte_stockee = $new_quantite_stock;
                $stock->update();

                // Concerne le magasin destination

                $approvisionner = new Approvisionner();
                $approvisionner->Id_Approvisionnement  = $approvisionnement->id;
                $approvisionner->Id_Stock  = $stock->id;
                $approvisionner->Prix_Revient = $stock->Prix_Achat_Net;
                $approvisionner->Qte_Approvisionnee = $value['quantity_transfer'];
                $approvisionner->Qte_Receptionnee = 0;
                $approvisionner->save();

                // L'historik
                $historique_entree_produit = new StockHistories();
                $historique_entree_produit->Date = $date_approvisionnement;
                $historique_entree_produit->agence_id = $id_agence_source;
                $historique_entree_produit->Motif = 'Sortie produit par approvisionnement';
                $historique_entree_produit->Justificatif = $reference_approvisionnement;
                $historique_entree_produit->operation = 'SORTIE';
                $historique_entree_produit->type_operation = 'SORTIE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Produit = $stock->Id_Produit;
                $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];
                $historique_entree_produit->save();

                $quantite_total_approv += $value['quantity_transfer'];
            }

            $notification_approv = new NotificationApprovs();
            $notification_approv->Id_Approvisionnement = $approvisionnement->id;
            $notification_approv->Id_Agence_Source = $id_agence_source;
            $notification_approv->Id_Agence_Destination = $id_agence_destination;
            $notification_approv->Qte_Approvisionnee = $quantite_total_approv;
            $notification_approv->Motif = 'Approvisionnement';
            $notification_approv->save();


            return to_route('approvisionner')->with('success', 'L\' approvisionnement a bien été effectué');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getApprovisionner(Request $request, $id)
    {

        try {
            $annees = Approvisionnement::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $approvisionnements = DB::table('approvisionnements')
                ->join('agences', 'approvisionnements.Id_Agence_Destination', 'agences.id')
                ->join('users', 'approvisionnements.Id_Utilisateur', 'users.id')
                ->select('approvisionnements.id', 'approvisionnements.Date_Appro', 'approvisionnements.Statut_appro', 'approvisionnements.Reference_Approvisionnement', 'approvisionnements.Observations', 'users.name', 'agences.NomAgence')
                ->get();

            $annees = Approvisionnement::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $approvisionners = DB::table('approvisionners')
                ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('approvisionners.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
                ->where('approvisionners.Id_Approvisionnement', '=', $id)
                ->get();
            // dd($approvisionners);

            return view(
                'page.approvisionnement.approvisionner.approvisionner',
                [
                    'approvisionners' => $approvisionners,
                    'approvisionnements' => $approvisionnements,
                    'annees' => $annees,
                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function ApprovisinnementImprimerAction(Request $request)
    {
        try {
            $id_approv = $request->input('id_approv');
            $reponse = $request->input('reponse');
            if (empty($id_approv)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $approvisionnement = DB::table('approvisionnements')
                ->join('agences', 'approvisionnements.Id_Agence_Destination', 'agences.id')
                ->join('users', 'approvisionnements.Id_Utilisateur', 'users.id')
                ->select('approvisionnements.id', 'approvisionnements.Date_Appro', 'approvisionnements.Statut_appro', 'approvisionnements.Reference_Approvisionnement', 'approvisionnements.Observations', 'users.name', 'agences.NomAgence')
                ->where('approvisionnements.id', $id_approv)
                ->get();


            if ($approvisionnement->isEmpty()) {
                return redirect()->back()->with('warning', 'approvisionnement non trouvé');
            }

            $approvisionners = DB::table('approvisionners')
                ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('approvisionners.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
                ->where('approvisionners.Id_Approvisionnement', '=', $id_approv)
                ->get();

            // dd($approvisionnement, $approvisionners );
            if ($approvisionners->isEmpty()) {
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
                'approvisionnement' => $approvisionnement,
                'approvisionners' => $approvisionners,
            ];

            if ($reponse === 'imprimer') {
                $htmlContent = view('page.approvisionnement.approvisionner.imprimer.imprimer-action', $data)->render();

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

            if ($reponse === 'exporter') {
                $prefixe = 'approv_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ApprovActionPrintExport($data), $nom_excel);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function ReceptionApprovOngletImpression(Request $request)
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

        // $query_agence = DB::table('approvisionnements')
        //     ->join('agences', 'approvisionnements.Id_Agence_Destination', '=', 'agences.id')
        //     ->join('users', 'approvisionnements.Id_Utilisateur', '=', 'users.id')
        //     ->select('approvisionnements.Id_Agence_Destination', 'agences.NomAgence', 'approvisionnements.Date_Appro')
        //     ->distinct();

        $query_agence = DB::table('approvisionnements')
            ->join('agences', 'approvisionnements.Id_Agence_Destination', '=', 'agences.id')
            ->join('users', 'approvisionnements.Id_Utilisateur', '=', 'users.id')
            ->select(
                'approvisionnements.Id_Agence_Destination',
                'agences.NomAgence',
                DB::raw('MAX(approvisionnements.Date_Appro) as Date_Appro')
            )
            ->where('approvisionnements.Id_Agence_Source', '=', $site_id)
            ->groupBy('approvisionnements.Id_Agence_Destination', 'agences.NomAgence');




        $query_approvisionners = DB::table('approvisionners')
            ->join('approvisionnements', 'approvisionners.Id_Approvisionnement', '=', 'approvisionnements.id')
            ->join('agences', 'approvisionnements.Id_Agence_Destination', '=', 'agences.id')
            ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            // ->where('magasins.agence_id', '=', $site_id)
            ->select('approvisionners.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle', 'approvisionnements.Id_Agence_Destination')
            ->whereBetween('approvisionners.created_at', [$date_debut_periode, $date_fin_periode]);

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
                ->where('produits.id', '=', $produit)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence === 'Toutes' && $produit === 'Tous' && $categorie !== 'Toutes') {

            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_produits.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence !== 'Toutes' && $produit !== 'Tous' && $categorie === 'Toutes') {

            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('produits.id', '=', $produit)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence !== 'Toutes' && $produit === 'Tous' && $categorie !== 'Toutes') {
            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_produits.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } elseif ($agence === 'Toutes' && $produit !== 'Tous' && $categorie !== 'Toutes') {

            $data_agences = $query_agence->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_produits.id', '=', $categorie)
                ->where('categorie_produits.id', '=', $categorie)
                ->get();

            if ((count($data_agences) || count($data_approvisionners)) <= 0) {
                return redirect()->back()->with('error', 'Requête non éfectuée.');
            }
        } else {

            $data_agences = $query_agence->where('agences.id', $agence)->get();
            $data_approvisionners = $query_approvisionners
                ->where('categorie_produits.id', '=', $categorie)
                ->where('categorie_produits.id', '=', $categorie)
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
            $htmlContent = view('page.approvisionnement.approvisionner.imprimer.imprimer', $data)->render();

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

            return Excel::download(new ApprovPrintExport($data), $nom_excel);
        }
    }


    public function getProductsByCategoryApprov(Request $request)
    {

        $categorie_id = $request->input('category_id');


        if ($categorie_id === 'Toutes') {
            $produits =  DB::table('approvisionners')
                ->join('stocks', 'approvisionners.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('produits.id', 'produits.Reference', 'produits.Designation')
                ->distinct()
                ->get();
        } else {
            $produits =  DB::table('approvisionners')
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


    public function filterApprovisionnement(Request $request)
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


        $query = DB::table('approvisionnements')
            ->join('agences', 'approvisionnements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'approvisionnements.Id_Utilisateur', 'users.id')
            ->select('approvisionnements.id', 'approvisionnements.Date_Appro', 'approvisionnements.Statut_appro', 'approvisionnements.Reference_Approvisionnement', 'approvisionnements.Observations', 'users.name', 'agences.NomAgence');



        if ($annee) {
            $query->whereYear('approvisionnements.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('approvisionnements.created_at', $month);
            $query->whereYear('approvisionnements.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('approvisionnements.created_at', '>=', $startDate)
                ->where('approvisionnements.created_at', '<=', $endDate);
        }

        $approvisionnements = $query->get();

        $agence = Agence::find($site_id);

        if ($agence->id !== 1) {
            return to_route('reception_approvisionnement')->with('warning', 'Désolé vous ne pouvez pas accéder à cette page');
        }


        // $detail_reglements = DB::table('detail_reglements')
        //     ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
        //     ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
        //     ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
        //     ->where('detail_reglements.Id_Reglement', '=', 0)
        //     ->get();


        $listeAgence = Agence::all();
        $clients = Client::all();
        // $mode_paiements = LibelleTypeOperation::all();
        $annees = Approvisionnement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        // dd($reglements, $detail_reglements);

        return view('page.approvisionnement.approvisionner.approvisionner',  [
            'approvisionnements' => $approvisionnements,
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
