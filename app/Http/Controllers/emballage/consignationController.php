<?php

namespace App\Http\Controllers\emballage;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Consignation;
use App\Models\Lignefacture;
use App\Models\TotalFacture;
use Illuminate\Http\Request;
use App\Models\GroupeTaxation;
use App\Models\StockEmballage;
use App\Models\PrefixeReference;
use App\Models\LigneConsignation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SituationClientExport;
use App\Models\LienFactureConsignation;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Validator;


class consignationController extends Controller
{
    public function consignation()
    {
        accessEmballage();
        $this->authorize('consignation');

        $annees = Consignation::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
        $Agence_id = session()->get('site_id');

        $listeConsignation = Consignation::join('users', 'consignations.user_id', '=', 'users.id')
            ->join('clients', 'consignations.client_id', '=', 'clients.id')
            ->join('agences', 'consignations.agence_id', '=', 'agences.id')
            ->select('consignations.*', 'users.name', 'clients.Denomination_sociale')
            ->where('consignations.agence_id', $Agence_id)
            ->whereYear('consignations.created_at', $currentYear)
            ->whereMonth('consignations.created_at', $currentMonth)

            ->orderBy('consignations.created_at', 'desc')
            ->get();

        $detailConsignation = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
            ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
            ->select('ligne_consignations.*', 'emballages.Nom_emballage', 'produits.Designation')
            ->orderBy('created_at', 'desc')
            ->where('consignations.id', 0)
            ->get();
        $listefactureDeconsignation = LienFactureConsignation::join('factures', 'lien_facture_consignations.facture_id', '=', 'factures.id')
            ->join('consignations', 'lien_facture_consignations.consignation_id', '=', 'consignations.id')
            ->select('lien_facture_consignations.*', 'factures.Reference_facture', 'factures.Statut_facture', 'factures.Date_facture')
            ->where('consignations.id',)
            ->get();

        $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
        $listeClient = Client::all();

        return view('page.emballage.consignation.consignation',  [
            'annees' => $annees,
            'listeConsignation' => $listeConsignation,
            'detailConsignation' => $detailConsignation,
            'listefactureDeconsignation' => $listefactureDeconsignation,
            'listeClient' => $listeClient,
            'listeAgence' => $listeAgence


        ]);
    }
    public function filterConsignation(Request $request)
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

            // Création de la requête initiale
            if (is_array($Agence_id)) {
                $query =  Consignation::join('users', 'consignations.user_id', '=', 'users.id')
                        ->join('clients', 'consignations.client_id', '=', 'clients.id')
                        ->join('agences', 'consignations.agence_id', '=', 'agences.id')
                        ->select('consignations.*', 'users.name', 'clients.Denomination_sociale')
                        ->where('consignations.agence_id', $Agence_id)
                        ->orderBy('consignations.created_at', 'desc');
            } else {
                $query =  Consignation::join('users', 'consignations.user_id', '=', 'users.id')
                ->join('clients', 'consignations.client_id', '=', 'clients.id')
                ->join('agences', 'consignations.agence_id', '=', 'agences.id')
                ->select('consignations.*', 'users.name', 'clients.Denomination_sociale')
                ->where('consignations.agence_id', $Agence_id)
                ->orderBy('consignations.created_at', 'desc');
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('consignations.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('consignations.created_at', $month);
                $query->whereYear('consignations.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('consignations.created_at', '>=', $startDate)
                      ->where('consignations.created_at', '<=', $endDate);
            }

            // Exécuter la requête
            $listeConsignation = $query->get();

            // Autres données nécessaires pour la vue
            $detailConsignation = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
            ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
            ->select('ligne_consignations.*', 'emballages.Nom_emballage', 'produits.Designation')
            ->orderBy('created_at', 'desc')
            ->where('consignations.id', 0)
            ->get();
            $listefactureDeconsignation = LienFactureConsignation::join('factures', 'lien_facture_consignations.facture_id', '=', 'factures.id')
            ->join('consignations', 'lien_facture_consignations.consignation_id', '=', 'consignations.id')
            ->select('lien_facture_consignations.*', 'factures.Reference_facture', 'factures.Statut_facture', 'factures.Date_facture')
            ->where('consignations.id',)
            ->get();

            $listeClient = Client::all();
            $listeAgence = Agence::all();
            $annees = Consignation::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

                return view('page.emballage.consignation.consignation',  [
                    'annees' => $annees,
                    'listeConsignation' => $listeConsignation,
                    'detailConsignation' => $detailConsignation,
                    'listefactureDeconsignation' => $listefactureDeconsignation,
                    'listeClient' => $listeClient,
                    'listeAgence' => $listeAgence


                ]);

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function situationClient(Request $request)
    {
        $data = $request->validate([
           // 'dateDebut' => 'required|date',
           // 'dateFin' => 'required|date|after_or_equal:dateDebut',
            'client' => 'required',
            'agence' => 'required',
        ]);
        $client = $data['client'];
        $agence = $data['agence'];

        $queryListeConsignation = Consignation::join('clients', 'consignations.client_id', '=', 'clients.id')
            ->join('agences', 'consignations.agence_id', '=', 'agences.id')
            ->join('users', 'consignations.user_id', '=', 'users.id')
            ->where('consignations.client_id', $client)

            ->select('consignations.*', 'clients.Denomination_sociale', 'agences.NomAgence', 'users.name')
            ->orderBy('consignations.created_at', 'desc');


            if ($agence !== 'Tous') {
                $queryListeConsignation ->where('consignations.agence_id', $agence);
            }
            $queryListeConsignation = $queryListeConsignation->get();

        $idsConsignation = $queryListeConsignation->pluck('id');

        /* $detailConsignation = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
            ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('ligne_consignations.*', 'emballages.Nom_emballage', 'produits.Designation', 'categorie_emballages.Libelle')
            ->orderBy('created_at', 'desc')
            ->whereIn('ligne_consignations.consignation_id', $idsConsignation)
            ->get(); */
            // Récupérer la dernière date des enregistrements
            $latestDate = LigneConsignation::whereIn('consignation_id', $idsConsignation)
            ->max('created_at');
            // Récupérer les enregistrements de la dernière date (nouvelle dette)
            $nouvelleDette = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
                ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
                ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'categorie_emballages.Libelle',
                    DB::raw('SUM(ligne_consignations.Qte) as total_Qte'),
                    DB::raw('SUM(ligne_consignations.restituee) as total_restituee'),
                    DB::raw('SUM(ligne_consignations.facturee) as total_facturee')
                )
                ->where('ligne_consignations.created_at', $latestDate)
                ->groupBy('categorie_emballages.Libelle')
                ->get();

            // Récupérer les enregistrements des dates antérieures (ancienne dette)
            $ancienneDette = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
                ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
                ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'categorie_emballages.Libelle',
                    DB::raw('SUM(ligne_consignations.Qte) as total_Qte'),
                    DB::raw('SUM(ligne_consignations.restituee) as total_restituee'),
                    DB::raw('SUM(ligne_consignations.facturee) as total_facturee')
                )
                ->where('ligne_consignations.created_at', '<', $latestDate)
                ->groupBy('categorie_emballages.Libelle')
                ->get();
           // dd($ancienneDette);

           $infoClient = $client !== 'Tous' ? Client::find($client) : null;
           $infoAgence = $agence !== 'Tous' ? Agence::find($agence) : null;


            return response()->json([
                'nouvelleDette' => $nouvelleDette,
                'ancienneDette' => $ancienneDette,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence,

            ]);


    }

    public function export_excel_situation_client(Request $request)
    {
        $data = $request->validate([
            'tableSituationClientData' => 'required',
            'infoClient' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new SituationClientExport($data, $texteEntetePied);

            $fileName = 'Situation_client_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
    }
    public function export_situation_client_pdf(Request $request)
    {
        $data = $request->validate([
            'tableSituationClientData' => 'required',
            'infoClient' => '',
            'infoAgence' => '',
        ]);
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.emballage.consignation.document.situation_client_Pdf', [
            'data' => $data,
            'imageEntetePied' => $imageEntetePied,
            'infoClient' => $data['infoClient'],
            'infoAgence' => $data['infoAgence'],

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Marge_par_produit'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

    }

    public function getDetailConsignation($id)
    {
        accessEmballage();
        $this->authorize('consignation');

        $annees = Consignation::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        $Agence_id = session()->get('site_id');


        $listeConsignation = Consignation::join('users', 'consignations.user_id', '=', 'users.id')
            ->join('clients', 'consignations.client_id', '=', 'clients.id')
            ->join('agences', 'consignations.agence_id', '=', 'agences.id')
            ->select('consignations.*', 'users.name', 'clients.Denomination_sociale')
            ->where('consignations.agence_id', $Agence_id)
            ->orderby('created_at', 'desc')->get();

        $detailConsignation = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
            ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
            ->select('ligne_consignations.*', 'emballages.Nom_emballage', 'produits.Designation')
            ->where('consignations.id', $id)
            ->get();
        $listefactureDeconsignation = LienFactureConsignation::join('factures', 'lien_facture_consignations.facture_id', '=', 'factures.id')
            ->join('consignations', 'lien_facture_consignations.consignation_id', '=', 'consignations.id')
            ->select('lien_facture_consignations.*', 'factures.Reference_facture', 'factures.Statut_facture', 'factures.Date_facture')
            ->where('consignations.id', $id)
            ->get();
        $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
        $listeClient = Client::all();


        return view('page.emballage.consignation.consignation',  [
            'annees' => $annees,
            'listeConsignation' => $listeConsignation,
            'detailConsignation' => $detailConsignation,
            'listefactureDeconsignation' => $listefactureDeconsignation,
            'listeAgence' => $listeAgence,
            'listeClient' => $listeClient


        ]);
    }

    public function showFormRegleConsignation()
    {
        accessEmballage();
        $this->authorize('consignation');

        $clients = Client::all();

        return view('page.emballage.consignation.nouveau', [
            'clients' => $clients
        ]);
    }

    public function getConsignationDuClient($clientId)
    {
        accessEmballage();
        $this->authorize('consignation');

        // Récupérez les factures liées à ce client
        $consignation = Consignation::where('client_id', $clientId)
            ->wherein('statut', ['EN_COURS', 'PARTIELLEMENT DECONSIGNE'])
            ->where('agence_id', session()->get('site_id'))
            ->get();



        return response()->json($consignation);
    }

    public function getLigneConsignationSelectionnee($consignationId)
    {
        accessEmballage();
        $this->authorize('consignation');

        $ligneConsignation = LigneConsignation::join('consignations', 'ligne_consignations.consignation_id', '=', 'consignations.id')
            ->join('emballages', 'ligne_consignations.emballage_id', '=', 'emballages.id')
            ->join('produits', 'ligne_consignations.produit_id', '=', 'produits.id')
            ->select('ligne_consignations.*', 'emballages.Nom_emballage', 'produits.Designation')
            ->where('ligne_consignations.consignation_id', $consignationId)
            ->get();
        $listeGTaxation = GroupeTaxation::all();


        return response()->json([
            'ligneConsignation' => $ligneConsignation,
            'groupesTaxation' => $listeGTaxation
        ]);
    }

    public function storeDeconsignation(Request $request)
    {
        accessEmballage();
        $this->authorize('consignation');

        $validator = Validator::make(
            $request->all(),
            [
                'client_id' => 'required',
                'consignation_id' => 'required',

                'id_ligne_consignation.*' => 'required',
                'nouvelle_qte_restituer.*' => 'nullable|numeric',
                'nouvelle_qte_facturer.*' => 'nullable|numeric',

                'nouvelle_qte_facturer_x.*' => 'nullable|numeric',
                'groupe_taxation.*' => 'nullable|numeric',
                'prix_ht.*' => 'nullable|numeric',

            ],
            [
                'client_id' => 'le client est requis',
                'consignation_id' => 'La consignation est requise',
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $idLigneConsignations = $request->get('id_ligne_consignation');

        $qteRestituer = $request->get('nouvelle_qte_restituer');
        $qteFacturer = $request->get('nouvelle_qte_facturer');

        $qteFacturerX = $request->get('nouvelle_qte_facturer_x');
        $groupeTaxation = $request->get('groupe_taxation');
        $prix_ht = $request->get('prix_ht');
        $client_id = $request->get('client_id');

        $consignation = Consignation::findorfail($request->consignation_id);

        if (!empty($qteFacturerX)) {

            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            $lastReference = Facture::where(function ($query) {
                $query->where('Code_type_facture', 'FV')
                    ->orWhere('Code_type_facture', 'EV');
            })->where('agence_id', session()->get('site_id'))->count();


            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '0000001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = Facture::where(function ($query) {
                    $query->where('Code_type_facture', 'FV')
                        ->orWhere('Code_type_facture', 'EV');
                })
                    ->where('agence_id', session()->get('site_id'))
                    ->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_facture, -7); // Obtenez les 7 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);
            }
            $user = Auth::user();
            $user_id = $user->id;

            $Agence_id = session()->get('site_id');

            $infoClient = Client::where('id', $client_id)->first();
            $pays_client = $infoClient->Pays;
            if ($pays_client == 'Bénin') {
                $Code_type_facture = 'FV';
            } else {
                $Code_type_facture = 'EV';
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->facture ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";

            $Date_facture =  Carbon::now();
            $Aib = 0;

            $montant_ttc_total = 0;

            foreach ($idLigneConsignations as $index => $ligneId) {

                if (isset($qteFacturerX[$ligneId])) {
                    $qteFacture = $qteFacturerX[$ligneId];
                    $prix = $prix_ht[$ligneId];
                    // dd($ligneId);
                    $gTaxation = $groupeTaxation[$ligneId];

                    //calcul du prix TTC
                    $req_groupe_taxation = GroupeTaxation::findorfail($gTaxation);
                    $lettre_taxe = $req_groupe_taxation->Code_lettre;
                    $valeur_taxe = $req_groupe_taxation->valeur_taxe;

                    $prix_ttc = $prix * (1 + $valeur_taxe / 100);

                    $montant_ttc = round($prix_ttc) * $qteFacture;

                    $montant_ttc_total += $montant_ttc;
                }
            }

            $Net_a_payer = $montant_ttc_total;

            $facture = new Facture();
            $facture->Reference_facture = $Reference_facture;
            $facture->Date_facture = $Date_facture;
            $facture->client_id = $client_id;
            $facture->Aib = $Aib;
            //$facture->Aib_deductible = $Aib_deductible;
            $facture->Objet_facture = 'Facture de déconsignation sur les emballages non rendus';
            //$facture->Commentaire = $Commentaire;
            //$facture->Autres_infos = $Autres_infos;
            $facture->Agence_id = $Agence_id;
            $facture->user_id = $user_id;
            $facture->Code_type_facture = $Code_type_facture;
            $facture->Statut_facture = 'EN COURS';
            $facture->Net_a_payer = $Net_a_payer;
            $facture->save();

            $facture_id = $facture->id;

            // Enregistrement des lignes de facture
            foreach ($idLigneConsignations as $index => $ligneId) {
                if (isset($qteFacturerX[$ligneId])) {
                    $qteFacture = $qteFacturerX[$ligneId];
                    $prix = $prix_ht[$ligneId];
                    $gTaxation = $groupeTaxation[$ligneId];

                    //calcul du prix TTC
                    $req_groupe_taxation = GroupeTaxation::findorfail($gTaxation);
                    $valeur_taxe = $req_groupe_taxation->valeur_taxe;
                    $lettre_taxe = $req_groupe_taxation->Code_lettre;


                    $prix_ttc = $prix * (1 + $valeur_taxe / 100);
                    $montant_ttc = round($prix_ttc) * $qteFacture;
                    $TVA = $prix_ttc - $prix;;
                    //dd($TVA);

                    $find_ligne_consignation = LigneConsignation::findorfail($ligneId);

                    $lignefacture = new Lignefacture();
                    $lignefacture->facture_id = $facture_id;
                    $lignefacture->stocks_id = $find_ligne_consignation->stock_id;
                    $lignefacture->Qte = $qteFacture;
                    $lignefacture->Prix_unitaire_HT = $prix;
                    $lignefacture->Prix_revient = $prix_ttc;
                    $lignefacture->GroupeTaxe_id = $gTaxation;
                    $lignefacture->Taux_remise = 0;
                    $lignefacture->is_emballage = 1;
                    $lignefacture->save();


                    $stockEmballage = StockEmballage::where('id', $find_ligne_consignation->stock_emballage_id)->first();
                    $stockEmballage->Qte_stockee += $qteFacture;
                    $stockEmballage->save();

                    $historique_Entreemballage = new StockEmballageHistories();
                    $historique_Entreemballage->Date = $Date_facture;
                    $historique_Entreemballage->agence_id = $Agence_id;
                    $historique_Entreemballage->Motif = "Entrée d'emballage sur une déconsignation";
                    $historique_Entreemballage->Justificatif = $Reference_facture;
                    $historique_Entreemballage->operation = 'ENTREE';
                    $historique_Entreemballage->type_operation = 'FACTURE_V_DECONSIGNATION';
                    $historique_Entreemballage->Id_Utilisateur = auth()->user()->id;
                    $historique_Entreemballage->Id_Emballage = $find_ligne_consignation->emballage_id;
                    $historique_Entreemballage->Id_Magasin = $stockEmballage->Id_Magasin;
                    $historique_Entreemballage->Quantite = $qteFacture;
                    $historique_Entreemballage->save();

                    //historique

                    $stockEmballage->Qte_stockee -= $qteFacture;
                    $stockEmballage->save();

                    $historique_sortie_emballage = new StockEmballageHistories();
                    $historique_sortie_emballage->Date = $Date_facture;
                    $historique_sortie_emballage->agence_id = $Agence_id;
                    $historique_sortie_emballage->Motif = "Sortie d'emballage sur une Facture de vente ";
                    $historique_sortie_emballage->Justificatif = $Reference_facture;
                    $historique_sortie_emballage->operation = 'SORTIE';
                    $historique_sortie_emballage->type_operation = 'FACTURE_V_DECONSIGNATION';
                    $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                    $historique_sortie_emballage->Id_Emballage = $find_ligne_consignation->emballage_id;
                    $historique_sortie_emballage->Id_Magasin = $stockEmballage->Id_Magasin;
                    $historique_sortie_emballage->Quantite = $qteFacture;
                    $historique_sortie_emballage->Prix_vente = $qteFacture * $prix_ttc;
                    $historique_sortie_emballage->save();

                    //logique pour remplir la table total_factures
                    if ($lettre_taxe == 'A') {
                        $TotalExoneree = $montant_ttc;
                    } else {
                        $TotalExoneree = 0;
                    }

                    if ($lettre_taxe == 'B') {
                        $TotalHT_B = $montant_ttc;
                        $TotalTVA_B = $TVA;
                    } else {
                        $TotalHT_B = 0;
                        $TotalTVA_B = 0;
                    }
                    if ($lettre_taxe == 'C') {
                        $TotalHT_C = $montant_ttc;
                    } else {
                        $TotalHT_C = 0;
                    }
                    if ($lettre_taxe == 'C') {
                        $TotalHT_D = $montant_ttc;
                        $TotalTVA_D = $TVA;
                    } else {
                        $TotalHT_D = 0;
                        $TotalTVA_D     = 0;
                    }
                    if ($lettre_taxe == 'E') {
                        $TotalHT_E = $montant_ttc;
                    } else {
                        $TotalHT_E = 0;
                    }
                    if ($lettre_taxe == 'F') {
                        $TotalHT_F = $montant_ttc;
                    } else {
                        $TotalHT_F = 0;
                    }

                    $save_total_facture = new TotalFacture();
                    $save_total_facture->facture_id = $facture_id;
                    $save_total_facture->Statut = 1;
                    $save_total_facture->TotalExoneree = $TotalExoneree;
                    $save_total_facture->TotalHT_B = $TotalHT_B;
                    $save_total_facture->TotalTVA_B = $TotalTVA_B;
                    $save_total_facture->TotalHT_C = $TotalHT_C;
                    $save_total_facture->TotalHT_D = $TotalHT_D;
                    $save_total_facture->TotalTVA_D = $TotalTVA_D;
                    $save_total_facture->TotalHT_E = $TotalHT_E;
                    $save_total_facture->TotalHT_F = $TotalHT_F;
                    $save_total_facture->Aib_facturee = 0;
                    $save_total_facture->Aib_deductible = 0;
                    $save_total_facture->save();
                }
            }

            $save_lien_facture_consignations = new LienFactureConsignation();
            $save_lien_facture_consignations->facture_id = $facture_id;
            $save_lien_facture_consignations->consignation_id = $request->consignation_id;
            $save_lien_facture_consignations->save();
        }



        foreach ($idLigneConsignations as $index => $idLigne) {
            $ligneConsignation = LigneConsignation::find($idLigne);
            // dd($ligneConsignation);

            $qteRestante = $ligneConsignation->Qte - $ligneConsignation->restituee - $ligneConsignation->facturee;

            $quantite_restitue_soumis = $qteRestituer[$index] ?? 0;
            $quantite_facture_soumis = $qteFacturer[$index] ?? 0;

            if ($quantite_restitue_soumis + $quantite_facture_soumis > $qteRestante) {
                return redirect()->back()->with('warning', 'La quantité soumise ne doit pas depasser la quantité restante');
            }
            //deconsignation

            $ligneConsignation->restituee += $qteRestituer[$index] ?? 0;
            $ligneConsignation->facturee += $qteFacturer[$index] ?? 0;
            $ligneConsignation->update();

            $somme_quantite_restituee_facturee = $ligneConsignation->restituee + $ligneConsignation->facturee;




            $qte_total_restante_des_lignes = 0;
            $qteRestante_x = $qteRestante - $quantite_restitue_soumis - $quantite_facture_soumis;

            $qte_total_restante_des_lignes += $qteRestante_x;


            $idEmballage = $ligneConsignation->emballage_id;
            $magasin_id  = $ligneConsignation->magasin_id;
            $stock_emballage_id = $ligneConsignation->stock_emballage_id;


            if ($idEmballage != null) {
                if ($quantite_restitue_soumis > 0) {

                    $stockEmballage = StockEmballage::where('id', $stock_emballage_id)->first();
                    //sauvegarde de l'entree en stock
                    $stockEmballage->Qte_stockee += $quantite_restitue_soumis;
                    $stockEmballage->save();


                    //Historiq stock emballage
                    $historique_sortie_emballage = new StockEmballageHistories();
                    $historique_sortie_emballage->Date = date('Y-m-d H:i:s');
                    $historique_sortie_emballage->agence_id = $consignation->agence_id;
                    $historique_sortie_emballage->Motif = "Entree d'emballage sur une déconsignation";
                    $historique_sortie_emballage->Justificatif = $consignation->ref_facture;
                    $historique_sortie_emballage->operation = 'ENTREE';
                    $historique_sortie_emballage->type_operation = 'FACTURE_V_DECONSIGNATION';
                    $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                    $historique_sortie_emballage->Id_Emballage = $idEmballage;
                    $historique_sortie_emballage->Id_Magasin = $stockEmballage->Id_Magasin;
                    $historique_sortie_emballage->Quantite = $quantite_restitue_soumis;
                    $historique_sortie_emballage->save();
                }
            }
        }



        // dd($request->consignation_id);

        $sommeConsigner = DB::table('ligne_consignations')
            ->join('consignations', 'ligne_consignations.consignation_id', 'consignations.id')
            ->where('ligne_consignations.consignation_id', '=', $request->consignation_id)
            ->sum('Qte');

        $sommeRestituee = DB::table('ligne_consignations')
            ->join('consignations', 'ligne_consignations.consignation_id', 'consignations.id')
            ->where('ligne_consignations.consignation_id', '=', $request->consignation_id)
            ->sum('restituee');

        $sommeFacturee = DB::table('ligne_consignations')
            ->join('consignations', 'ligne_consignations.consignation_id', 'consignations.id')
            ->where('ligne_consignations.consignation_id', '=', $request->consignation_id)
            ->sum('facturee');

        // dd($sommeConsigner, $sommeRestituee, $sommeFacturee);

        // foreach($ligne_consignations as $ligne_consignation){
        if ($sommeConsigner !=  $sommeRestituee + $sommeFacturee) {
            // dd('1');
            $statut_consignation = 'PARTIELLEMENT DECONSIGNE';
        } else {
            // dd('2');
            $statut_consignation = 'DECONSIGNATION TERMINEE';
        }

        $consignation->statut = $statut_consignation;
        $consignation->update();

        // }

        // //dd($qte_total_restante_des_lignes);
        // if ($qte_total_restante_des_lignes == 0) {

        //     $consignation->statut = "DECONSIGNATION TERMINEE";
        //     $consignation->update();
        // } else {
        //     // dd($qte_total_restante_des_lignes);
        // }


        return to_route('consignation')->with('Déconsignation effectué avec succes');
    }
}
