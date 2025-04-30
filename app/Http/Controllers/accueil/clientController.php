<?php

namespace App\Http\Controllers\accueil;

use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ClientExport;
use App\Models\CategorieClient;
use App\Models\DetailReglement;
use Illuminate\Support\Facades\DB;
use App\Exports\ClientCompteExport;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FiltreClient\FactureClientRequest;
use App\Models\HistoriqueReglement;

use function Laravel\Prompts\table;

class clientController extends Controller
{

    public function listeClient(FactureClientRequest $request)
    {
        $this->authorize('consulter-client');



        // Par défaut, ne rien afficher
        $isFiltered = $request->filled('debut_periode') || $request->filled('fin_periode') || $request->filled('client');

        // Initialisation de la liste des clients
        $listeClient = Client::orderBy('clients.created_at', 'desc')->get();

        // Préparer la requête pour les factures
        $facturesQuery = Facture::where('Code_type_facture', '<>', 'PR')
            ->whereIn('statut_facture', ['EN COURS DE REGLEMENT', 'NORMALISEE', 'SOLDE'])
            ->select('id', 'Reference_facture as reference_facture', 'Net_a_payer as montant', 'Code_type_facture as type_facture', 'IdFacture_originale as idFactOrig', 'client_id', 'created_at');

        // Préparer la requête pour les détails des règlements
        $detailsReglementsQuery = DetailReglement::select('id', 'Id_Reglement', 'Id_Facture', 'Montant_Regle as montant', 'Statut_Reglement as statut_reglement', 'created_at');

        // Appliquer les filtres si des données sont fournies
        if ($isFiltered) {
            if ($request->filled('debut_periode')) {
                $debut_periode = date('Y-m-d 00:00:00', strtotime($request->validated('debut_periode')));
                $facturesQuery->where('created_at', '>=', $debut_periode);
                $detailsReglementsQuery->where('created_at', '>=', $debut_periode);
            }

            if ($request->filled('fin_periode')) {
                $fin_periode = date('Y-m-d 23:59:59', strtotime($request->validated('fin_periode')));
                $facturesQuery->where('created_at', '<=', $fin_periode);
                $detailsReglementsQuery->where('created_at', '<=', $fin_periode);
            }

            if ($request->filled('client')) {
                $clientFiltre = $request->validated('client');
                $facturesQuery->where('client_id', $clientFiltre);
                $detailsReglementsQuery->whereHas('facture', function ($query) use ($clientFiltre) {
                    $query->where('client_id', $clientFiltre);
                });
            }
        } else {
            // Si aucun filtre n'est sélectionné, renvoyer une collection vide
            $facturesQuery->whereNull('id');
            $detailsReglementsQuery->whereNull('id');
        }

        // Récupérer et fusionner les factures et détails des règlements
        $factures = $facturesQuery->with('client')->get()->map(function ($item) {
            $item->source = 'factures';
            $item->client_name = $item->client ? $item->client->Denomination_sociale : '';
            return $item;
        });

        $detailsReglements = $detailsReglementsQuery->with('facture.client', 'reglement')->get()->map(function ($item) {
            $item->source = 'detail_reglements';
            $item->reference_reglement = $item->reglement ? $item->reglement->Reference_Reglement : null;
            $item->reference_facture = $item->facture ? $item->facture->Reference_facture : null;
            $item->client_name = $item->facture && $item->facture->client ? $item->facture->client->Denomination_sociale : '';
            return $item;
        });

        // Fusionner les deux collections
        $mergedArray = $factures->merge($detailsReglements)->sortBy('created_at');

        // Retourner la vue avec les données filtrées
        return view('page.accueil.client.client', [
            'datas' => $mergedArray,
            'listeClient' => $listeClient,
            'date_debut' => '',
            'date_fin' => '',
            'historique_reglemeent' => [],
            'clients' => Client::get(),
            'factures' => [],
            'input' => $request->validated(),
            'isFiltered' => $isFiltered,
        ]);
    }

    public function filtreCompte(Request $request)
    {

        // dd($request->all());



        // Initialisation de la liste des clients
        $listeClient = Client::orderBy('clients.created_at', 'desc')->get();

        $date_fin = $request->input('fin_periode');
        $date_debut = $request->input('debut_periode');
        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($date_debut));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($date_fin));
        $client_id = $request->input('client');

        $date_initiale = date('Y-m-d 00:00:00', strtotime($date_debut . ' -1 day'));


        // dd($date_initiale);

        $historique_reglemeent = HistoriqueReglement::where('Id_Client', '=', $client_id)
            ->whereBetween('Date_Reglement', [$date_debut_periode, $date_fin_periode])
            ->get();

        // $historique_reglement = DB::table('detail_reglements')
        //     ->join('reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
        //     ->join('factures', 'detail_reglements.Id_Facture', 'factures.id')
        //     ->select('detail_reglements.*', 'reglements.Reference_Reglement', 'reglements.Date_Reglement', 'factures.Reference_facture')
        //     ->whereBetween('reglements.Date_Reglement', [$date_debut_periode, $date_fin_periode])
        //     ->where('reglements.Id_Client', '=', $client_id)
        //     ->get();

        // // dd($historique_reglement);

        $facture_FV_EV = Facture::select('client_id', DB::raw('SUM(net_a_payer) as total_net_a_payer'))
        ->groupBy('client_id')
        ->where('client_id', $client_id)
        ->whereIn('Code_type_facture', ['FV', 'EV']) // Utiliser whereIn pour vérifier les types de facture
        ->where('Date_facture', '<', $date_debut_periode)
        ->first();

    $facture_FA_EA = Facture::select('client_id', DB::raw('SUM(net_a_payer) as total_net_a_payer'))
        ->groupBy('client_id')
        ->where('client_id', $client_id)
        ->whereIn('Code_type_facture', ['FA', 'EA']) // Utiliser whereIn ici aussi
        ->where('Date_facture', '<', $date_debut_periode)
        ->first();

    $reglement = DB::table('detail_reglements')
        ->join('reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
        ->select(
            'reglements.Id_Client',
            DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle')
        )
        ->where('detail_reglements.Statut_Reglement', '=', 1)
        ->where('reglements.Id_Client', $client_id)
        ->where('reglements.Date_Reglement', '<', $date_debut_periode)
        ->groupBy('reglements.Id_Client')
        ->first();

    $reglement_annule = DB::table('detail_reglements')
        ->join('reglements', 'detail_reglements.Id_Reglement', '=', 'reglements.id')
        ->select(
            'reglements.Id_Client',
            DB::raw('SUM(detail_reglements.Montant_Regle) as Montant_Regle')
        )
        ->where('reglements.Id_Client', $client_id)
        ->where('detail_reglements.Statut_Reglement', '=', 0)
        ->where('reglements.Date_Reglement', '<', $date_debut_periode)
        ->groupBy('reglements.Id_Client')
        ->first();

    $solde_reglement = 0; // Initialisation du solde

    // Vérification pour éviter l'erreur sur Montant_Regle null
    $reglement_montant = $reglement->Montant_Regle ?? 0; // Si null, on affecte 0
    $reglement_annule_montant = $reglement_annule->Montant_Regle ?? 0; // Idem pour l'annulé

    if (isset($reglement) && !isset($reglement_annule)) {
        $solde_reglement = $reglement_montant;
    } elseif (isset($reglement) && isset($reglement_annule)) {
        $solde_reglement = $reglement_montant - $reglement_annule_montant;
    } elseif (!isset($reglement) && isset($reglement_annule)) {
        $solde_reglement = $reglement_annule_montant;
    } else {
        $solde_reglement = 0;
    }

    // Vérification pour éviter les erreurs null pour les factures
    $facture_FV_EV_total = $facture_FV_EV->total_net_a_payer ?? 0;
    $facture_FA_EA_total = $facture_FA_EA->total_net_a_payer ?? 0;

    // Calcul du solde initial
    $solde_initial = ($facture_FV_EV_total - $facture_FA_EA_total) + $solde_reglement;

    // Debugger la valeur du solde réglements
    // dd($solde_initiale);

        $factures = Facture::where('Code_type_facture', '<>', 'PR')->where('client_id', $client_id)
            ->whereIn('statut_facture', ['EN COURS DE REGLEMENT', 'NORMALISEE', 'SOLDE', 'ANNULEE'])
            ->whereBetween('Date_facture', [$date_debut_periode, $date_fin_periode])
            ->get();

            // dd($factures);
        $client = Client::find($client_id);

        if (!$client) {
            return back()->with('error', 'Veuillez sélectionner le client ');
        }

        // dd($historique_reglemeent, $factures);
        // Retourner la vue avec les données filtrées
        return view('page.accueil.client.client', [
            'historique_reglements' => $historique_reglemeent,
            'factures' => $factures,
            'solde_initial' => $solde_initial,
            'date_initiale' => $date_initiale,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'listeClient' => $listeClient,
            'client_filtre' => Client::find($client_id),
            'clients' => Client::get(),
            // 'factures' => Facture::where('Code_type_facture', '<>', 'PR')->get(),
            // 'input' => $request->validated(),
            'isFiltered' => true,
        ]);
    }

    public function showForm()
    {
        $this->authorize('creer-client');
        try {
            $listeCategorieClient = CategorieClient::all();
            return view('page.accueil.client.nouveau', ['listeCategorieClient' => $listeCategorieClient, 'client' => new Client]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeCategorieClientR(Request $request)
    {
        $this->authorize('creer-categorie-client');
        try {
            $data = $request->only(['Libelle']);

            $validatorRules = [
                'Libelle' => 'required',
            ];

            $validationMessages = [
                'Libelle.required' => "Le nom de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('ShowFormCategorieClient')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['Libelle'];
            $existingCat = CategorieClient::where('Libelle', $libelle)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie client avec le même nom existe déjà.');
            }

            $catClient = new CategorieClient();
            $catClient->Libelle = $libelle;
            $catClient->save();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié la catégorie de client' . $libelle,
            ]);

            return response()->json([
                'success' => true,
                'newCategoryId' => $catClient->id,
                'newCategoryName' => $catClient->Libelle,
            ]);
            //return to_route('categorieclient')->with('success', 'La categorie a bien été ajoutée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function storeClient(Request $request)
    {
        $this->authorize('creer-client');

        try {
            $data = $request->only(['Denomination_sociale', 'Adresse_client', 'Telephone_fixe', 'Telephone_mobile', 'Adresse_mail', 'Pays', 'Numero_ifu', 'Code_client', 'Categorie_client_id', 'Statut_client']);
            $validatorRules = [
                'Denomination_sociale' => 'required',
                'Adresse_client' => '',
                'Telephone_fixe' => '',
                'Telephone_mobile' => '',
                'Adresse_mail' => '',
                'Pays' => 'required',
                'Numero_ifu' => 'nullable|min:13|max:13',
                'Code_client' => 'required',
                'Categorie_client_id' => 'required',
                'Statut_client' => 'required',
            ];
            $validationMessages = [
                'Denomination_sociale.required' => "Le nom de la société est requise",
                'Code_client.required' => "Le code du client est requise",
                'Categorie_client_id.required' => "La categorie du client est requise",
                'Statut_client.required' => "Le statut du clients est requis",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                //return to_route('showFormClient')->with('error', $validatorResult->errors()->first());
            }

            $denomination_sociale = $data['Denomination_sociale'];
            $adresse_client = $data['Adresse_client'];
            $telephone_fixe = $data['Telephone_fixe'];
            $telephone_mobile = $data['Telephone_mobile'];
            $adresse_mail = $data['Adresse_mail'];
            $pays = $data['Pays'];
            $numero_ifu = $data['Numero_ifu'];
            $code_client = $data['Code_client'];
            $catClient = $data['Categorie_client_id'];
            $statut_client = $data['Statut_client'];
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            // dd($numero_ifu);



            if ($numero_ifu !== null && strlen($numero_ifu) < 13) {
                return back()->with('error', "Le IFU doit contenir au moins 13 caractères.");
            }


            $existingClient = Client::where('Denomination_sociale', $denomination_sociale)
                ->orWhere('Code_client', $code_client)->first();
            if ($existingClient) {
                return back()->with('error', "Ce client existe déjà.");
            }

            if (Str::length($numero_ifu) > 1) {

                $countIfu = Client::where('Numero_ifu', $numero_ifu)->count();

                if ($countIfu >= 2) {
                    return back()->with('error', "Ce numero IFU existe déjà  2 fois.");
                }
            }




            $client = new Client();
            $client->Denomination_sociale = $denomination_sociale;
            $client->Adresse_client = $adresse_client;
            $client->Telephone_fixe = $telephone_fixe;
            $client->Telephone_mobile = $telephone_mobile;
            $client->Adresse_mail = $adresse_mail;
            $client->Pays = $pays;
            $client->Numero_ifu = $numero_ifu;
            $client->Code_client = $code_client;
            $client->Categorie_client_id = $catClient;
            $client->Statut_client = $statut_client;
            $client->user_id = $user_connecterId;
            $client->save();


            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a créé le client' . $denomination_sociale . 'de code ' . $code_client,
            ]);

            return to_route('client')->with('success', 'Le client a bien été ajouté');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function editClient(string $id)
    {
        $this->authorize('modifier-client');

        try {
            $client = Client::findorfail($id);
            $listeCategorieClient = CategorieClient::all();
            return view('page.accueil.client.nouveau', ['client' => $client, 'listeCategorieClient' => $listeCategorieClient]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function updateClient(Request $request, string $id)
    {
        $this->authorize('modifier-client');

        try {
            $data = $request->only(['Denomination_sociale', 'Adresse_client', 'Telephone_fixe', 'Telephone_mobile', 'Adresse_mail', 'Pays', 'Numero_ifu', 'Code_client', 'Categorie_client_id', 'Statut_client']);
            $validatorRules = [
                'Denomination_sociale' => 'required',
                'Adresse_client' => '',
                'Telephone_fixe' => '',
                'Telephone_mobile' => '',
                'Adresse_mail' => '',
                'Pays' => 'required',
                'Numero_ifu' => 'nullable|string|min:13|max:13',
                'Code_client' => 'required',
                'Categorie_client_id' => 'required',
                'Statut_client' => 'required',
            ];
            $validationMessages = [
                'Denomination_sociale.required' => "Le nom de la société est requise",
                'Code_client.required' => "Le code du client est requise",
                'Categorie_client_id.required' => "La categorie du client est requise",
                'Statut_client.required' => "Le statut du clients est requis",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                //return to_route('editClient')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $denomination_sociale = $data['Denomination_sociale'];
            $adresse_client = $data['Adresse_client'];
            $telephone_fixe = $data['Telephone_fixe'];
            $telephone_mobile = $data['Telephone_mobile'];
            $adresse_mail = $data['Adresse_mail'];
            $pays = $data['Pays'];
            $numero_ifu = $data['Numero_ifu'];
            $code_client = $data['Code_client'];
            $catClient = $data['Categorie_client_id'];
            $statut_client = $data['Statut_client'];
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');


            $fournisseurIfu = Client::findorfail($id);
            if ($fournisseurIfu->Numero_ifu != $numero_ifu) {
                if (Str::length($numero_ifu) > 1) {
                    $countIfu = Client::where('Numero_ifu', $numero_ifu)->count();

                    if ($countIfu >= 2) {
                        return back()->with('error', "Ce numero IFU existe déjà  2 fois.");
                    }
                }
            }



            $client = Client::findorfail($id);
            $client->Denomination_sociale = $denomination_sociale;
            $client->Adresse_client = $adresse_client;
            $client->Telephone_fixe = $telephone_fixe;
            $client->Telephone_mobile = $telephone_mobile;
            $client->Adresse_mail = $adresse_mail;
            $client->Pays = $pays;
            $client->Numero_ifu = $numero_ifu;
            $client->Code_client = $code_client;
            $client->Categorie_client_id = $catClient;
            $client->Statut_client = $statut_client;
            $client->Modifier_par = $user_connecterId;
            $client->update();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié le client' . $denomination_sociale . 'de code ' . $code_client,
            ]);

            return to_route('client')->with('success', 'Modification effectuée avec succès ');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }



    /* Recherche automatique */
    public function searchClient(Request $request)
    {
        try {

            $query = $request->input('query');
            $listeClient = Client::where('Denomination_sociale', 'like', '%' . $query . '%')
                ->orwhere('Code_client', 'like', '%' . $query . '%')
                ->get();

            return view('page.accueil.client.searchClient', ['listeClient' => $listeClient]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportExcelClient(Request $request)
    {
        $data = $request->validate([
            'tableClientData' => 'required',
        ]);

        // Créer l'exportation
        $export = new ClientExport($data);

        // Générer le nom de fichier
        $fileName = 'Client_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        // Retourner le fichier pour téléchargement direct
        return Excel::download($export, $fileName);
    }

    public function export_excel_client_compte(Request $request)
    {
        $data = $request->validate([
            'tableClientCompteData' => 'required',
        ]);

        // Créer l'exportation
        $export = new ClientCompteExport($data);

        // Générer le nom de fichier
        $fileName = 'Client_Compte' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        // Retourner le fichier pour téléchargement direct
        return Excel::download($export, $fileName);
    }

    public function import_pdf_categorie_client(Request $request)
    {
        $data = $request->validate([
            'tableCategorieClientData' => 'required',
        ]);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.accueil.categorie_client.document.categorie_client_import_A4', [
            'data' => $data,
            'imageEntetePied' => $imageEntetePied,
        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Liste_categorie_client_', ["Attachment" => false]);
    }

    public function import_pdf_client(Request $request)
    {
        $data = $request->validate([
            'tableClientData' => 'required',
        ]);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.accueil.client.document.client_import_A4', [
            'data' => $data,
            'imageEntetePied' => $imageEntetePied,
        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Liste_client_', ["Attachment" => false]);
    }

    public function import_pdf_compte_client(Request $request)
    {
        $data = $request->validate([
            'clientsTableData' => 'required|array',
            'debut_periode' => '',
            'fin_periode' => '',
            'clientRecherche' => '',
        ]);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.accueil.client.document.client_compte_import', [
            'data' => $data,
            'debut_periode' => $data['debut_periode'],
            'fin_periode' => $data['fin_periode'],
            'clientRecherche' => $data['clientRecherche'],

            'imageEntetePied' => $imageEntetePied,

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('liste_compte_client_pdf', ["Attachment" => false]);
    }
}
