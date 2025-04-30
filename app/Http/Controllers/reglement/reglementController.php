<?php

namespace App\Http\Controllers\reglement;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Caisse;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Reglement;
use Illuminate\Http\Request;
use App\Models\ArchiveFacture;
use App\Models\GroupeTaxation;
use App\Models\DetailReglement;
use App\Models\OperationCaisse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PrefixeReference;
use App\Models\TransfertProduit;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriqueReglement;
use App\Http\Controllers\Controller;
use App\Models\LibelleTypeOperation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReglementPrintExport;
use App\Exports\ReglementPeriodeExport;
use Illuminate\Support\Facades\Validator;
use App\Exports\ReglementActionPrintExport;

class reglementController extends Controller
{

    public function listeReglement()
    {

        $site_id = session()->get('site_id');
        $this->authorize('consulter-reglement');
        $annees = Reglement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $query = DB::table('reglements')
            ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
            ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('agences', 'reglements.Id_Agence', '=', 'agences.id')
            // ->where('reglements.Id_Agence', '=', $site_id)
            ->select(
                'reglements.id',
                'clients.Code_client',
                'clients.Denomination_sociale',
                DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle'),
                DB::raw('MAX(reglements.Statut_Operation) as Statut_Operation'),
                DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'),
                DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'),
                DB::raw('MAX(reglements.Observations) as Observations'),
                DB::raw('MAX(agences.NomAgence) as NomAgence'),
            )
            ->groupBy('reglements.id', 'clients.Code_client', 'clients.Denomination_sociale')
            // ->whereMonth('reglements.created_at', $currentMonth)
            //->whereYear('reglements.created_at', $currentYear)
            ->orderBy('reglements.id', 'desc');

        $query_agence = Agence::find($site_id);

        if ($query_agence->NomAgence === 'Siège') {
            $reglements = $query->get();
        } else {
            $reglements = $query->where('reglements.Id_Agence', '=', $site_id)->get();
        }




        // dd($reglements);

        $detail_reglements = DB::table('detail_reglements')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
            ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
            ->where('detail_reglements.Id_Reglement', '=', 0)
            ->get();


        $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        $clients = Client::all();
        $mode_paiements = LibelleTypeOperation::all();
        // dd($reglements, $detail_reglements);

        return view('page.reglement.relgement',  [
            'reglements' => $reglements,
            'detail_reglements' => $detail_reglements,
            'clients' => $clients,
            'mode_paiements' => $mode_paiements,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
    }

    public function filterReglement(Request $request)
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


        $query = DB::table('reglements')
            ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
            ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('agences', 'reglements.Id_Agence', '=', 'agences.id')
            ->where('reglements.Id_Agence', '=', $site_id)
            ->select(
                'reglements.*',
                'clients.Code_client',
                'clients.Denomination_sociale',
                'agences.NomAgence',
                DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle')
            )
            ->groupBy('reglements.id', 'clients.Code_client', 'clients.Denomination_sociale')
            ->orderBy('reglements.id', 'desc');

        $query_agence = Agence::find($site_id);





        if ($annee) {
            $query->whereYear('reglements.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('reglements.created_at', $month);
            $query->whereYear('reglements.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('reglements.created_at', '>=', $startDate)
                ->where('reglements.created_at', '<=', $endDate);
        }

        if ($query_agence->NomAgence === 'Siège') {
            $reglements = $query->get();
        } else {
            $reglements = $query->where('reglements.Id_Agence', '=', $site_id);
        }


        $detail_reglements = DB::table('detail_reglements')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
            ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
            ->where('detail_reglements.Id_Reglement', '=', 0)
            ->get();


        $listeAgence = Agence::all();
        $clients = Client::all();
        $mode_paiements = LibelleTypeOperation::all();
        $annees = Reglement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        // dd($reglements, $detail_reglements);


        return view('page.reglement.relgement',  [
            'reglements' => $reglements,
            'detail_reglements' => $detail_reglements,
            'clients' => $clients,
            'mode_paiements' => $mode_paiements,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

    public function listeImprimerReglement()
    {
        $this->authorize('consulter-reglement');
        return view('page.reglement.imprimer.imprimer');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function showForm()
    {
        $this->authorize('enregistrer-reglement');
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->reglement ?? '';
        $site_id = session()->get('site_id');
        if ($prefix == null) {
            return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
        }
        $factures = Facture::whereIn('Statut_facture', ['NORMALISEE', 'EN COURS DE REGLEMENT'])
            ->whereIn('Code_type_facture', ['FV', 'EV'])
            ->where('agence_id', '=', $site_id)
            ->get();

        $detail_reglements = DetailReglement::where('Statut_Reglement', '=', 1)->get();
        $type_operations = LibelleTypeOperation::all();
        $clients = Client::all();

        return view('page.reglement.nouveau', [
            'clients' => $clients,
            'factures' => $factures,
            'type_operations' => $type_operations,
            'detail_reglements' => $detail_reglements,
        ]);
    }

    public function getDetailReglement($id)
    {
        $this->authorize('consulter-reglement');
        $annees = Reglement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $reglements = DB::table('reglements')
            ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
            ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('agences', 'reglements.Id_Agence', '=', 'agences.id')
            ->select('reglements.*', 'clients.Code_client', 'agences.NomAgence', 'clients.Denomination_sociale', 'detail_reglements.Montant_Regle')
            ->get();

        $detail_reglements = DB::table('detail_reglements')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
            ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
            ->where('detail_reglements.Id_Reglement', '=', $id)
            ->get();

        $clients = Client::all();
        $mode_paiements = LibelleTypeOperation::all();
        $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        return view('page.reglement.relgement',  [
            'reglements' => $reglements,
            'detail_reglements' => $detail_reglements,
            'clients' => $clients,
            'mode_paiements' => $mode_paiements,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
    }


    public  function modifierStatutDetailReglement(Request $request)
    {
        $this->authorize('annuler-reglement');
        // dd($request);

        $id = $request->id;



        if (!$id) {
            return response()->json([
                'status' => 404,
                'error' => 'Désolé! veuillez sélectionner le détail du réglement que vous aimeriez annulé.',
            ]);
        }
        $site_id = session()->get('site_id');



        // if (PrefixeReference::first()->caisse == 1) {
        // if (PrefixeReference::first()->caisse) {

            $caisse = Caisse::where('agence_id', $site_id)
                ->where('user_id', auth()->user()->id)
                ->where('statut', 1)
                ->first();

            // dd($caisse);

            if (!$caisse) {
                return response()->json([
                    'status' => 404,
                    'error' => 'Veuillez ouvrir une caisse avant de faire un reglement.',
                ]);
            }
        // }



        $detail_reglement = DetailReglement::find($id);

        // dd($detail_reglement);

        if ($detail_reglement->Statut_Reglement === 1) {
            // dd($detail_reglement);

            $reglements = DB::table('reglements')
                ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
                ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
                ->where('reglements.id', '=', $detail_reglement->Id_Reglement)
                ->where('reglements.Id_Agence', '=', $site_id)
                ->where('detail_reglements.Statut_Reglement', '=', 1)
                ->select(
                    'reglements.id',
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle'),
                    DB::raw('SUM(reglements.Statut_Operation) as Statut_Operation'),
                    DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'),
                    DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'),
                    DB::raw('MAX(reglements.Observations) as Observations')
                )
                ->groupBy('reglements.id', 'clients.Code_client', 'clients.Denomination_sociale')
                ->orderBy('reglements.id', 'desc')
                ->first();

            // dd($reglements);

            $montant = $reglements->Montant_Regle - $detail_reglement->Montant_Regle;

            // dd($montant);


            $reglement = Reglement::find($detail_reglement->Id_Reglement);
            if ($montant == 0.0) {
                $reglement->Statut_Operation = 'ANNULE';
                $reglement->update();
            } elseif ($montant > 0) {
                $reglement->Statut_Operation = 'PARTIELLEMENT ANNULE';
                $reglement->update();
            }

            $facture = Facture::find($detail_reglement->Id_Facture);

            $archive_facture = ArchiveFacture::where('facture_id', '=', $facture->id);

            // dd($detail_reglement->Montant_Regle);

            $sommeMontants = DB::table('detail_reglements')
                ->where('Id_Reglement', $detail_reglement->Id_Reglement)
                ->where('Statut_Reglement', '=', 1)
                ->sum('Montant_Regle');

            $somme_total_regle = $sommeMontants - $detail_reglement->Montant_Regle;
            // dd($somme_total_regle);

            // $difference = $facture->Net_a_payer - $sommeMontants->Montant_Regle;
            // dd($sommeMontants, $difference, $facture->Net_a_payer);


            if ($somme_total_regle == 0.0) {
                // dd('ici');
                $facture->Statut_facture = 'NORMALISEE';
                $facture->update();
                // $facture->update(['Statut_facture' => 'NORMALISEE']);
                $archive_facture->update(['Statut_facture' => 'NORMALISEE']);
            }
            if ($somme_total_regle > 0.0) {
                // dd('ici(((((((((');
                $facture->update();
                // $facture->update(['Statut_facture' => 'EN COURS DE REGLEMENT']);
                $archive_facture->update(['Statut_facture' => 'EN COURS DE REGLEMENT']);
            }
            // else{
            //     dd('icièèèèèèè');
            //     // dd('je suis dans le deuxieme if ==',  $somme_total_regle);
            //     $facture->Statut_facture = 'NORMALISEE';
            //     $facture->update();
            //     // $facture->update(['Statut_facture' => 'NORMALISEE']);
            //     $archive_facture->update(['Statut_facture' => 'NORMALISEE']);

            // }


            $detail_reglement->Statut_Reglement = 0;
            $detail_reglement->update();

            $historique_reglement = new HistoriqueReglement();
            $historique_reglement->Date_Reglement =  Carbon::now();
            $historique_reglement->Reference_Reglement = $reglement->Reference_Reglement;
            $historique_reglement->Reference_Facture = $facture->Reference_facture;
            $historique_reglement->Montant_Regle = $detail_reglement->Montant_Regle;
            $historique_reglement->Operation = 'REGLEMENT ANNULE';
            $historique_reglement->Id_Client =  $reglement->Id_Client;
            $historique_reglement->save();

            $site_id = $request->session()->get('site_id');

            $modeReg = LibelleTypeOperation::find($detail_reglement->Id_Libelle_Type_Operation);

            // if (PrefixeReference::first()->caisse == 1) {
                if (PrefixeReference::first()->caisse) {
                if ($modeReg->Libelle_Operation == 'ESPECE') {

                    $caisse = Caisse::where('agence_id', $site_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('statut', 1)
                        ->first();

                    $Agence_id = $caisse->agence_id;

                    $lastDigitOfYear = substr(Carbon::now()->year, -2);

                    // Obtenez le dernier numéro de référence enregistré
                    $lastReference = OperationCaisse::where('agence_id', '=', session()->get('site_id'))
                        ->count();

                    // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
                    if ($lastReference === 0) {
                        $incrementedReferenceNumber = '0000001';
                    } else {
                        // Obtenez le dernier numéro de référence et incrémentez-le
                        $lastReference = OperationCaisse::where('agence_id', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                        $lastReferenceNumber = substr($lastReference->reference_operation, -7); // Obtenez les 7 derniers chiffres
                        $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);
                    }

                    $reference_operation = "$Agence_id/{$lastDigitOfYear}/OC/{$incrementedReferenceNumber}";

                    $operation = new OperationCaisse();
                    $operation->reference_operation = $reference_operation;
                    $operation->statut = 'EFFECTUEE';
                    $operation->caisse_id = $caisse->id;
                    $operation->type = 'sortie';
                    $operation->montant = $detail_reglement->Montant_Regle;
                    $operation->description = 'Reglement annulee';
                    $operation->reference_reglement = $reglement->Reference_Reglement;
                    $operation->user_id = auth()->user()->id;
                    $operation->agence_id = $caisse->agence_id;
                    $operation->save();

                    // Mettre à jour les fonds actuels de la caisse
                    $caisse->fonds_actuel -= $detail_reglement->Montant_Regle;
                    $caisse->save();
                }
            }
        } else if ($detail_reglement->Statut_Reglement === 0) {
            // dd('erreur');
            return response()->json([
                'status' => 404,
                'error' => 'Certains règlements sélectionnés sont déjà annulés.',
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('enregistrer-reglement');
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
        $site_id = $request->session()->get('site_id');

        if (PrefixeReference::first()->caisse) {
            $caisse = Caisse::where('agence_id', $site_id)
                ->where('user_id', auth()->user()->id)
                ->where('statut', 1)
                ->first();

            if (!$caisse) {
                return redirect()->route('caisse_index')->with('error', 'Veuillez ouvrir une caisse avant de faire un reglement.');
            }
        }


        // Obtenez le dernier chiffre de l'année actuelle
        $lastDigitOfYear = substr(Carbon::now()->year, -2);
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->reglement ?? '';

        // Obtenez le dernier numéro de référence enregistré
        //   $lastReference = Reglement::count();
        $lastReference = Reglement::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            //   $lastReference = Reglement::orderBy('id', 'desc')->first();
            $lastReference = Reglement::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Reglement, -5); // Obtenez les 5 derniers chiffres
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
        $reglement = new Reglement();
        $reglement->Date_Reglement = $date_reglement;
        $reglement->Reference_Reglement = $reference_reglement;
        $reglement->Observations = $observation;
        $reglement->Id_Client = $client;
        $reglement->Id_Agence = $site_id;
        $reglement->Statut_Operation = 'EFFECTUE';
        $reglement->Id_Utilisateur = auth()->user()->id;
        // $entree_produit->Id_Fournisseur = $fournisseur;
        $reglement->save();

        foreach ($request->inputs as $value) {

            $facture_formate = explode('-', $value['num_facture'], 2);
            $mode_reglement_formate = explode('-', $value['mode_reglement'], 2);
            $facture_id = trim($facture_formate[0]);
            $mode_reglement_id = trim($mode_reglement_formate[0]);

            $sommeMontants = DB::table('detail_reglements')
                ->where('id_facture', $facture_id)
                ->where('Statut_Reglement', '=', 1)
                ->sum('Montant_Regle');

            $somme_total_regle = $sommeMontants + $value['montant'];
            // dd($somme_total_regle);

            $facture_exist = Facture::find($facture_id);
            $archive_facture = ArchiveFacture::where('facture_id', '=', $facture_id);

            $reste_a_payer = $facture_exist->Net_a_payer - $somme_total_regle;
            if ($facture_exist->Net_a_payer > $somme_total_regle) {

                // dd('ici');
                $facture_exist->Statut_facture = 'EN COURS DE REGLEMENT';
                $facture_exist->update();

                // $archive_facture->Statut_facture = 'EN COURS DE REGLEMENT';
                // $archive_facture->update();
                // $facture_exist->update(['Statut_facture' => 'EN COURS DE REGLEMENT']);
                $archive_facture->update(['Statut_facture' => 'EN COURS DE REGLEMENT']);
            }
            if ($facture_exist->Net_a_payer == $somme_total_regle) {
                // dd('ici---');
                // dd('je suis dans le deuxieme if ==',  $somme_total_regle);
                // $facture_exist->update(['Statut_facture' => 'SOLDE']);

                $facture_exist->Statut_facture = 'SOLDE';
                $facture_exist->update();


                $archive_facture->update(['Statut_facture' => 'SOLDE']);
            }

            if ($facture_exist->Net_a_payer < $somme_total_regle) {

                $parametre = PrefixeReference::first();

                // dd($parametre);
                if ($parametre->surplus_reglement !== 1) {
                    $parametre->surplus_reglement = 1;
                    $parametre->update();
                }
                // dd('La somme des montants réglés excèdent le net à payer',  $somme_total_regle);
                // return to_route('reglement')->with('error', 'La somme des montants réglés excèdent le net à payer');
            }

            $detail_reglement = new DetailReglement();
            $detail_reglement->Id_Reglement = $reglement->id;
            $detail_reglement->Id_Facture = $facture_id;
            $detail_reglement->Id_Libelle_Type_Operation = $mode_reglement_id;
            $detail_reglement->Montant_Regle = $value['montant'];
            $detail_reglement->save();



            $historique_reglement = new HistoriqueReglement();
            $historique_reglement->Date_Reglement = $date_reglement;
            $historique_reglement->Reference_Reglement = $reglement->Reference_Reglement;
            $historique_reglement->Reference_Facture = $facture_exist->Reference_facture;
            $historique_reglement->Montant_Regle = $value['montant'];
            $historique_reglement->Operation = 'REGLEMENT';
            $historique_reglement->Id_Client = $client;
            $historique_reglement->save();

            $modeReg = LibelleTypeOperation::find($mode_reglement_id);

            if (PrefixeReference::first()->caisse) {
                if ($modeReg->Libelle_Operation == 'ESPECE') {



                    $caisse = Caisse::where('agence_id', $site_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('statut', 1)
                        ->first();

                    $Agence_id = $caisse->agence_id;


                    $lastDigitOfYear = substr(Carbon::now()->year, -2);

                    // Obtenez le dernier numéro de référence enregistré
                    $lastReference = OperationCaisse::where('agence_id', '=', session()->get('site_id'))
                        ->count();

                    // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
                    if ($lastReference === 0) {
                        $incrementedReferenceNumber = '0000001';
                    } else {
                        // Obtenez le dernier numéro de référence et incrémentez-le
                        $lastReference = OperationCaisse::where('agence_id', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                        $lastReferenceNumber = substr($lastReference->reference_operation, -7); // Obtenez les 7 derniers chiffres
                        $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);
                    }

                    $reference_operation = "$Agence_id/{$lastDigitOfYear}/OC/{$incrementedReferenceNumber}";

                    $operation = new OperationCaisse();
                    $operation->reference_operation = $reference_operation;
                    $operation->statut = 'EFFECTUEE';
                    $operation->caisse_id = $caisse->id;
                    $operation->type = 'Entree';
                    $operation->montant = $value['montant'];
                    $operation->description = 'REGLEMENT';
                    $operation->reference_reglement = $reglement->Reference_Reglement;
                    $operation->user_id = auth()->user()->id;
                    $operation->agence_id = $caisse->agence_id;
                    $operation->save();

                    // Mettre à jour les fonds actuels de la caisse
                    $caisse->fonds_actuel += $value['montant'];
                    $caisse->save();
                }
            }

            //
        }
        // dd($reste_a_payer);
        return to_route('reglement')->with('success', 'Le règlement a bien été ajouté');
    }

    public function imprimerReglement(Request $request)
    {
        $this->authorize('consulter-reglement');
        // dd($request);
        $site_id = session()->get('site_id');

        $debut_periode = $request->input('debut_periode');
        $fin_periode = $request->input('fin_periode');
        $client = $request->input('client');
        $agence = $request->input('agence');
        $mode_reglement = $request->input('mode_reglement');
        $statut = $request->input('statut');
        $reponse = $request->input('reponse');

        // dd($reponse);

        // Ajouter les heures, minutes et secondes à la date de début et de fin
        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        // $query = DB::table('reglements')
        // ->join('detail_reglements', 'reglements.id', '=', 'detail_reglements.Id_Reglement')
        // ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id',)
        // ->select('reglements.*', DB::raw('SUM(detail_reglements.Montant_Regle) as total'),  );

        if ($statut === "1") {
            if ($client !== 'Tous' && $mode_reglement !== 'Tous') {
                // dd('ok');

                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    // ->where('agences.id', '=', $site_id)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(agences.NomAgence) as NomAgence'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client !== 'Tous' && $mode_reglement === 'Tous') {
                // dd('ok ce ca');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'),
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement !== 'Tous') {
                // dd('troisieme');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    // ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement === 'Tous') {
                // // dd('quatrieme');
                $reglements = DB::table('reglements')
                    ->join('detail_reglements', 'reglements.id', 'detail_reglements.Id_Reglement')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'reglements.Id_Agence', '=', 'agences.id')
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    // ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    // ->where('detail_reglements.Statut_Reglement', '=', '1')
                    // ->where('agences.id', '=', $site_id)
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc')->get();

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('agences', 'reglements.Id_Agence', 'agences.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->select(
                        'detail_reglements.Id_Reglement',
                        'reglements.Date_Reglement',
                        'reglements.Reference_Reglement',
                        'detail_reglements.Montant_Regle',
                        'libelle_type_operations.Libelle_Operation',
                        'users.name',
                        'reglements.Observations',
                        'factures.Reference_facture',
                        'factures.Objet_facture'
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    // ->where('detail_reglements.Id_Reglement', '=', $item)
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                // dd($reglements, $detail_reglements);
                $all_client_all_mode_reglement = $reglements;
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            }
        }

        if ($statut === "2") {
            if ($client !== 'Tous' && $mode_reglement !== 'Tous') {
                // dd('ok');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client !== 'Tous' && $mode_reglement === 'Tous') {
                // dd('ok ce ca');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    // ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement !== 'Tous') {
                // dd('troisieme');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    // ->where('clients.id', '=', $client)
                    // ->where('agences.id', '=', $site_id)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    // ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement === 'Tous') {
                // // dd('quatrieme');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    // ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    // ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            }
        }

        if ($statut === "3") {
            if ($client !== 'Tous' && $mode_reglement !== 'Tous') {
                // dd('ok');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');


                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client !== 'Tous' && $mode_reglement === 'Tous') {
                // dd('ok ce ca');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    ->where('clients.id', '=', $client)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement !== 'Tous') {
                // dd('troisieme');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    // ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    // ->where('clients.id', '=', $client)
                    ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }

                // dd($all_client_all_mode_reglement, $detail_reglements);
            } else if ($client === 'Tous' && $mode_reglement === 'Tous') {
                // // dd('quatrieme');
                $reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_Reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
                    ->join('agences', 'factures.agence_id', '=', 'agences.id')
                    // ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    // ->where('agences.id', '=', $site_id)
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'detail_reglements.Id_Reglement',
                        // 'detail_reglements.Id_Reglement',
                        DB::raw('MAX(agences.NomAgence) as NomAgence'),
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement')
                    ->orderBy('reglements.id', 'desc');

                if ($agence !== 'Toutes') {
                    $reglements->where('agences.id', '=', $site_id);
                }

                $detail_reglements = DB::table('detail_reglements')
                    ->join('reglements', 'detail_reglements.Id_reglement', 'reglements.id')
                    ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
                    ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
                    ->join('clients', 'reglements.Id_Client', 'clients.id')
                    ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
                    // ->where('clients.id', '=', $client)
                    // ->where('libelle_type_operations.id', '=', $mode_reglement)
                    ->select(
                        'detail_reglements.id',
                        'detail_reglements.Id_Reglement',
                        DB::raw('MAX(detail_reglements.Id_Reglement) as Id_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'), // Utilisation de MAX() pour la date
                        DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(libelle_type_operations.Libelle_Operation) as Libelle_Operation'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(users.name) as name'), // Utilisation de MAX() pour la référence de règlement
                        DB::raw('MAX(reglements.Observations) as Observations'),
                        DB::raw('MAX(clients.Code_client) as Code_client'), // Utilisation de MAX() pour les observations,
                        DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale '),
                        DB::raw('MAX(factures.Reference_facture) as Reference_facture'), // Utilisation de MAX() pour la référence de facture
                        DB::raw('MAX(factures.Objet_facture) as Objet_facture'), // Utilisation de MAX() pour la référence de facture
                        // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                    )
                    ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
                    ->where('detail_reglements.Statut_Reglement', '=', '1')
                    ->groupBy('detail_reglements.Id_Reglement', 'detail_reglements.id')
                    ->orderBy('detail_reglements.Id_Reglement', 'desc')
                    ->get();

                $all_client_all_mode_reglement = $reglements->get();
                if (count($all_client_all_mode_reglement) > 0) {
                    $get_request_reglements = $all_client_all_mode_reglement;
                } else {
                    return to_route('reglement')->with('error', 'Aucunes données trouvées!');
                }
                // dd($all_client_all_mode_reglement, $detail_reglements);
            }
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $data = [
            'imageEntetePied' => $imageEntetePied,
            'texteEntetePied' => $texteEntetePied,
            'debut_periode' => $debut_periode,
            'statut' => $statut,
            'reglements' => $get_request_reglements,
            'detail_reglements' => $detail_reglements,
            'm_r' => LibelleTypeOperation::find($mode_reglement),
            'fin_periode' => $fin_periode,
            // 'clients' => $get_client,
            'clt' => Client::find($client),
            // 'getRequeste' => $get_request,
            // 'client' => $get_client,
        ];


        if ($reponse === 'imprimer') {


            $htmlContent = view('page.reglement.imprimer.imprimer', $data)->render();

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


            $prefixe = 'REG';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';

            // Afficher le PDF dans le navigateur
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }

        if ($reponse === 'exporter') {
            $prefixe = 'reglement_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new ReglementPrintExport($data), $nom_excel);
        }


        // $pdf = Pdf::loadView('page.reglement.imprimer.imprimer', $data);
        // return $pdf->download($nom_pdf);



        // return view('page.reglement.imprimer.imprimer', $data);
    }

    public function ImprimerReglementA4(Request $request)
    {
        $this->authorize('consulter-reglement');
        // dd($request);
        // Récupérer l'identifiant du règlement à imprimer
        $response = $request->input('reponse');
        $type = $request->input('type');
        $value = $request->input('value');
        $reglement_print  = DB::table('reglements')
            ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->join('agences', 'factures.agence_id', '=', 'agences.id')
            ->join('users', 'reglements.Id_Utilisateur', '=', 'users.id')
            ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
            ->select(
                'reglements.id',
                DB::raw('MAX(detail_reglements.Montant_Regle) as Montant_Regle'),
                DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'),
                DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'),
                DB::raw('MAX(reglements.Observations) as Observations'),
                DB::raw('MAX(factures.Net_a_payer) as Net_a_payer'),
                DB::raw('MAX(factures.Objet_facture) as Objet_facture'),
                DB::raw('MAX(users.name) as name'),
                DB::raw('MAX(clients.Code_client) as Code_client'),
                DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale'), // Correction ici
                DB::raw('MAX(clients.Telephone_mobile) as Telephone_mobile'),
                DB::raw('MAX(clients.Telephone_fixe) as Telephone_fixe'),
                DB::raw('MAX(agences.NomAgence) as NomAgence')
            )
            ->groupBy('reglements.id')
            ->orderBy('reglements.id', 'desc')
            ->where('detail_reglements.Id_Reglement', '=', $response)
            ->get();

        $detail_reglements = DB::table('detail_reglements')
            ->join('reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
            // ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
            ->select('detail_reglements.*', 'reglements.Reference_Reglement', 'reglements.Observations', 'reglements.Date_Reglement', 'factures.Reference_facture', 'factures.Objet_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
            ->where('detail_reglements.Id_Reglement', '=', $response)
            ->get();

        // dd($reglement_print, $detail_reglements);
        // dd($reglement)

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        // Générer le contenu HTML
        if ($type === 'A4') {
            // dd('ici A4');
            $data = [
                'detail_reglements' => $detail_reglements,
                'reglement' => $reglement_print,
                'texteEntetePied' => $texteEntetePied,
                'imageEntetePied' => $imageEntetePied,
            ];

            if ($value === 'imprimer') {
                // dd('imprimer');

                $htmlContent = view('page.reglement.imprimer.imprimer-bordereau-A4', $data)->render();

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
                return $dompdf->stream('document.pdf', ['Attachment' => false]);
            }
            if ($value === 'exporter') {

                $prefixe = 'reglement_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ReglementActionPrintExport($data), $nom_excel);
            }
        }

        if ($type === 'A5') {
            // dd('ici A5');
            $htmlContent = view('page.reglement.imprimer.imprimer-bordereau-A5', [
                'detail_reglements' => $detail_reglements,
                'reglement' => $reglement_print,
                'imageEntetePied' => $imageEntetePied,
            ])->render();
        }

        if ($type === 'A8') {
            // dd('ici A8');
            $htmlContent = view('page.reglement.imprimer.imprimer-bordereau-A8', [
                'detail_reglements' => $detail_reglements,
                'reglement' => $reglement_print,
                'imageEntetePied' => $imageEntetePied,
            ])->render();
        }
    }

    public function imprimerReglement_periode(Request $request)
    {
        $data = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'client' => '',
            'agence' => 'required',
        ]);

        $dateDebut = $data['date_debut'];
        $dateFin = $data['date_fin'];
        //$dateFin = Carbon::parse($data['date_fin_periode'])->endOfDay();
        $client = $data['client'];
        $agence = $data['agence'];

        $site_id = session()->get('site_id');

        $query = DB::table('reglements')
            ->join('clients', 'reglements.Id_Client', '=', 'clients.id')
            ->join('detail_reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('agences', 'agences.id', '=', 'reglements.Id_Agence')
            ->whereBetween('reglements.created_at', [$dateDebut, $dateFin])
            ->select(
                'reglements.id',
                'agences.NomAgence',
                'clients.Code_client',
                'clients.Denomination_sociale',
                DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle'),
                DB::raw('MAX(reglements.Statut_Operation) as Statut_Operation'),
                DB::raw('MAX(reglements.Date_Reglement) as Date_Reglement'),
                DB::raw('MAX(reglements.Reference_Reglement) as Reference_Reglement'),
                DB::raw('MAX(reglements.Observations) as Observations')
            )
            ->groupBy('reglements.id', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale')
            ->orderBy('reglements.id', 'desc');

        if ($client !== 'Tous') {
            $query->where('reglements.Id_Client', $client);
            $infoClient = Client::where('id', $client)->first();
        } else {
            $infoClient = Client::where('id', 0)->first();
        }
        if ($agence !== 'Tous') {
            $query->where('reglements.Id_Agence', $agence);
            $infoAgence = Agence::where('id', $agence)->first();
        } else {
            $infoAgence = Agence::where('id', 0)->first();
        }
        $listeReglement = $query->get();
        // dd($infoAgence);
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        return response()->json([
            'listeReglement' => $listeReglement,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoClient' => $infoClient,
            'infoAgence' => $infoAgence, // Retourne également les infos de l'agence
            'imageEntetePied' => $imageEntetePied // Ajoute l'image si nécessaire
        ]);
    }
    public function export_impression_reglement_periode_pdf(Request $request)
    {
        // try {
        $data = $request->validate([
            'tableReglementParPeriodeData' => 'required|array',
            'dateDebut' => '',
            'dateFin' => '',
            'infoClient' => '',
            'infoAgence' => '',
        ]);




        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.reglement.imprimer.reglement_par_periode', [
            'data' => $data,
            'dateDebut' => $data['dateDebut'],
            'dateFin' => $data['dateFin'],
            'infoClient' => $data['infoClient'],
            'infoAgence' => $data['infoAgence'],

            'imageEntetePied' => $imageEntetePied,

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Liste_reglement_periode', ["Attachment" => false]);

        /*  } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }
    public function export_excel_impression_reglement_periode(Request $request)
    {
        try {
            $data = $request->validate([
                'tableReglementParPeriodeData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoClient' => '',
                'infoAgence' => '',
            ]);
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new ReglementPeriodeExport($data, $texteEntetePied);

            $fileName = 'Liste_reglement_periode_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function script()
    {
        $scripts = DB::table('detail_reglements')
            ->join('reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
            ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
            ->select('detail_reglements.Montant_Regle', 'factures.Reference_facture', 'reglements.Reference_Reglement', 'reglements.Id_Client', 'reglements.Date_Reglement', 'reglements.created_at', 'reglements.updated_at')
            ->get();

        // dd($scripts);

        foreach ($scripts as $script) {

            $historique_reglement = new HistoriqueReglement();
            $historique_reglement->Date_Reglement = $script->Date_Reglement;
            $historique_reglement->Reference_Reglement = $script->Reference_Reglement;
            $historique_reglement->Reference_Facture = $script->Reference_facture;
            $historique_reglement->Montant_Regle = $script->Montant_Regle;
            $historique_reglement->Operation = 'REGLEMENT';
            $historique_reglement->Id_Client = $script->Id_Client;
            $historique_reglement->created_at = $script->created_at;
            $historique_reglement->updated_at = $script->updated_at;
            $historique_reglement->save();
        }
    }

    public function reglement_surplus(Request $request)
    {
        $format_num_facture = explode('-', $request->input('num_facture'));
        $sum = $request->input('sum');

        $id_facture = $format_num_facture[0];
        $num_facture = $format_num_facture[1];
        // dd($num_facture, $id_facture);

        $facture = Facture::find($id_facture);
        $prefixe_param = PrefixeReference::first();

        if ($sum > $facture->Net_a_payer) {
            if ($prefixe_param->surplus_reglement === 1) {
                return response()->json([
                    'status' => true,

                ]);
            } else {
                return response()->json([
                    'status' => false,

                ]);
            }
        } else {
            return response()->json([
                'status' => true,

            ]);
        }
    }
}
