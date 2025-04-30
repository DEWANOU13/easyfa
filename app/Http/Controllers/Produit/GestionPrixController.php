<?php

namespace App\Http\Controllers\Produit;

use App\Exports\GestionPrixExport;
use App\Exports\GestionPrixExportHistory;
use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\CategorieClient;
use App\Models\HistoriquePrixRevient;
use App\Models\HistoriquePrixVente;
use App\Models\Image;
use App\Models\PrixVenteProduit;
use App\Models\Produit;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class GestionPrixController extends Controller
{

    // retourne la vue de gestion des prix
    public function  index()
    {
        $this->authorize('consulter-prix-vente');
        try {
            $produits = Produit::orderBy('id', 'desc')->where('Statut', '=', 'ACTIF')->where('Type', '=', 'PRODUIT')->get();

            $Agence_id = session()->get('site_id');
            $historique_prix_revients = HistoriquePrixRevient::join('produits', 'historique_prix_revients.Id_Produit', '=', 'produits.id')
                ->join('categorie_clients', 'historique_prix_revients.Id_Categorie_Client', '=', 'categorie_clients.id')
                ->join('agences', 'historique_prix_revients.Id_Agence', '=', 'agences.id')
                ->select('historique_prix_revients.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence')
                ->where('historique_prix_revients.agence_id', $Agence_id)
                ->orderBy('produits.id', 'desc')
                ->get();

            if ($historique_prix_revients) {
                // dd($historique_prix_revients);
                $agence = Agence::orderBy('id', 'desc')->where('id', '=', $Agence_id)->get();
                $categorie_client = CategorieClient::orderBy('id', 'desc')->get();

                $historique_prix_vente = DB::table('prix_vente_produits')
                    ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
                    ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
                    ->select('prix_vente_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence', 'categorie_produits.Libelle as Libelle_Categorie')
                    ->where('prix_vente_produits.agence_id', $Agence_id)
                    ->orderBy('prix_vente_produits.id', 'desc')
                    ->get();

                return view('page.produit.gestion_prix.gestion_prix', [
                    'produits' => $produits,
                    'historique_prix_revients' => $historique_prix_revients,
                    'categorie_client' => $categorie_client,
                    'agence' => $agence,
                    'historique_prix_ventes' => $historique_prix_vente,
                ]);
            } else {
                // Récupérer tous les produits
                $produits = Produit::orderBy('produits.id', 'desc')->get();
                $agence = Agence::orderBy('agences.id', 'desc')->get();
                $categorie_client = CategorieClient::orderBy('id', 'desc')->get();

                $historique_prix_vente = DB::table('prix_vente_produits')
                    ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
                    ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
                    ->select('prix_vente_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence', 'categorie_produits.Libelle as Libelle_Categorie')
                    ->where('prix_vente_produits.agence_id', $Agence_id)
                    ->orderBy('prix_vente_produits.id', 'desc')
                    ->get();

                // dd($produits, $categorie_client, $agence, $historique_prix_vente);

                return view('page.produit.gestion_prix.gestion_prix', [
                    'produits' => $produits,
                    'categorie_client' => $categorie_client,
                    'agence' => $agence,
                    'historique_prix_ventes' => $historique_prix_vente,
                ]);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('creer-prix-vente');
        // try{
        // dd($request);

        $validator = Validator::make(
            $request->all(),
            [
                // 'categorie_client' => 'required',
                // 'agence' => 'required',
                'inputs.*.produit' => 'required',
                'inputs.*.agence' => 'required',
                'inputs.*.categorie_client' => 'required',
                'inputs.*.nouveau_prix' => 'required',
                // 'inputs.*.prix_achat' => 'required',
            ],
            [
                // 'fournisseur' => 'Fournisseur requis',
                // 'observation' => 'Observations requis',
                'inputs.*.produit' => "produit(s) requis",
                'inputs.*.agence' => "agence(s) requise(s)",
                'inputs.*.categorie_client' => "categorie(s) cient(s) requise(s)",
                'inputs.*.nouveau_prix' => "nouveau prix est requis",
                // 'inputs.*.prix_achat' => "prix achat(s) requis",
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        foreach ($request->inputs as $value) {

            $produit = Produit::where('Reference', '=', $value['produit'])->first();
            // dd($produit);



            // dd('$value[agence]',$value['agence']);
            // dd($value['categorie_client']);
            $categorie_client_formate = explode('-', $value['categorie_client'], 2);
            $agence_formate = explode('-', $value['agence'], 2);
            // dd($categorie_client_formate, $agence_formate);
            $categorie_client_id = trim($categorie_client_formate[0]);
            $agence_id = trim($agence_formate[0]);
            // dd('agence_id',$categorie_client_id);

            // dd($value['agence']);

            $date_variation_prix = Carbon::now();

            $historique_prix_revient_exist = HistoriquePrixRevient::where([
                ['Id_Categorie_Client', '=', $categorie_client_id],
                ['Id_Produit', '=', $produit->id],
                ['Id_Agence', '=', $agence_id],
            ])->first();

            $historique_prix_vente_exist = PrixVenteProduit::where([
                ['categorie_client_id', '=', $categorie_client_id],
                ['produit_id', '=', $produit->id],
                ['agence_id', '=', $agence_id],
            ])->first();


            if ($historique_prix_revient_exist && $historique_prix_vente_exist) {
                // dd('ok je suis la');

                $historique_prix_revient_exist->Date_variation_prix = Carbon::now();
                $historique_prix_revient_exist->Prix_Revient = $value['nouveau_prix'];
                $historique_prix_revient_exist->Modifier_par = auth()->user()->id;
                $historique_prix_revient_exist->update();

                $historique_prix_vente_exist->date_variation_prix = Carbon::now();
                $historique_prix_vente_exist->prix = $value['nouveau_prix'];
                $historique_prix_vente_exist->Modifier_par = auth()->user()->id;
                $historique_prix_vente_exist->update();
            } else {

                $historique_prix_revient = new HistoriquePrixRevient();
                $historique_prix_revient->Id_Produit = $produit->id;
                $historique_prix_revient->Id_Categorie_Client = $categorie_client_id;
                $historique_prix_revient->Id_Agence = $agence_id;
                $historique_prix_revient->Date_variation_prix = $date_variation_prix;
                $historique_prix_revient->Prix_Revient = $value['nouveau_prix'];
                $historique_prix_revient->Enregistrer_par = auth()->user()->id;
                $historique_prix_revient->save();


                $historique_prix_vente = new PrixVenteProduit();
                $historique_prix_vente->produit_id  = $produit->id;
                $historique_prix_vente->categorie_client_id  = $categorie_client_id;
                $historique_prix_vente->agence_id  = $agence_id;
                $historique_prix_vente->date_variation_prix = $date_variation_prix;
                $historique_prix_vente->date_enregistrement = Carbon::now();
                $historique_prix_vente->prix = $value['nouveau_prix'];
                $historique_prix_vente->user_id = auth()->user()->id;
                $historique_prix_vente->save();
            }
        }
        return to_route('page.gestion_prix.gestion_prix')->with('success', 'La gestion de prix a bien été ajoutée');
        // }catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }

    }

    public function filterListeGestionPrix(Request $request)
    {
        // dd($request);
        $this->authorize('consulter-prix-vente');
        try {
            $agence = $request->input('agence');
            $categorie_client = $request->input('categorie_client');
            $produit = $request->input('produit');

            // Récupérer tous les produits

            $historique_prix_revients = HistoriquePrixRevient::join('produits', 'historique_prix_revients.Id_Produit', '=', 'produits.id')
                ->join('categorie_clients', 'historique_prix_revients.Id_Categorie_Client', '=', 'categorie_clients.id')
                ->join('agences', 'historique_prix_revients.Id_Agence', '=', 'agences.id')
                ->select('historique_prix_revients.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence')
                ->orderBy('produits.id', 'desc')
                ->get();

            if ($historique_prix_revients) {
                // dd($historique_prix_revients);

                $produits = Produit::orderBy('id', 'desc')->where('Statut', '=', 'ACTIF')->where('Type', '=', 'PRODUIT')->get();
                $agences = Agence::orderBy('id', 'desc')->get();
                $categorie_clients = CategorieClient::orderBy('id', 'desc')->get();

                $query = DB::table('historique_prix_ventes')
                    ->join('produits', 'historique_prix_ventes.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('categorie_clients', 'historique_prix_ventes.Id_Categorie_Client', '=', 'categorie_clients.id')
                    ->join('agences', 'historique_prix_ventes.Id_Agence', '=', 'agences.id')
                    ->select('historique_prix_ventes.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence', 'categorie_produits.Libelle as Libelle_Categorie')
                    ->orderBy('historique_prix_ventes.id', 'desc');
                // Ajouter les conditions WHERE en fonction des valeurs fournies
                if ($agence) {
                    $query->where('agences.id', $agence);
                }
                if ($categorie_client) {
                    $query->where('categorie_clients.id', $categorie_client);
                }
                if ($produit) {
                    $query->where('produits.Reference', $produit);
                }

                $historique_prix_vente = $query->get();

                return view('page.produit.gestion_prix.gestion_prix', [
                    'produits' => $produits,
                    'historique_prix_revients' => $historique_prix_revients,
                    'categorie_client' => $categorie_clients,
                    'agence' => $agences,
                    'historique_prix_ventes' => $historique_prix_vente,
                ]);
            } else {
                $historique_prix_vente = DB::table('historique_prix_ventes')
                    ->join('produits', 'historique_prix_ventes.Id_Produit', '=', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('categorie_clients', 'historique_prix_ventes.Id_Categorie_Client', '=', 'categorie_clients.id')
                    ->join('agences', 'historique_prix_ventes.Id_Agence', '=', 'agences.id')
                    ->select('historique_prix_ventes.*', 'produits.Reference', 'produits.Designation', 'categorie_clients.Libelle', 'agences.NomAgence', 'categorie_produits.Libelle as Libelle_Categorie')
                    ->orderBy('historique_prix_ventes.id', 'desc')
                    ->get();

                // Récupérer tous les produits
                $produits = Produit::all();
                $agences = Agence::all();
                $categorie_clients = CategorieClient::all();

                // dd($produits, $categorie_client, $agence, $historique_prix_vente);
                return view('page.produit.gestion_prix.gestion_prix', [
                    'produits' => $produits,
                    'categorie_client' => $categorie_clients,
                    'agence' => $agences,
                    'historique_prix_ventes' => $historique_prix_vente,
                ]);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportPrix(Request $request)
    {

        $data_prix = json_decode($request->input('data'), true);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
        $site_id =session()->get('site_id');


        $data_prix = DB::table('prix_vente_produits')
            ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
            ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
            ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as Libelle_produit', 'agences.NomAgence', 'categorie_clients.Libelle as Libelle_client', 'prix_vente_produits.prix')
            ->where('prix_vente_produits.prix', '>', 0)
            ->where('agences.id', '=', $site_id)
            ->orderBy('prix_vente_produits.id', 'desc')
            ->get();

            // dd($data_prix);

        $data = [
            'data_prix' => $data_prix,
            'reponse' => $request->reponse,
            'texteEntetePied' => $texteEntetePied,
        ];

        // dd($data);


        if($request->reponse == 'exporter'){
            $prefixe = 'prix_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';
        return Excel::download(new GestionPrixExport($data), $nom_excel);


        }
        if($request->reponse == 'formatImportation'){
            $prefixe = 'modele_prix_produits';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe .'.xlsx';
           return Excel::download(new GestionPrixExport($data), $nom_excel);


        }

    }


    public function imprimerPrix(Request $request)
    {

        // $data = json_decode($request->input('data'), true);
        // dd($data);
        $site_id =session()->get('site_id');

        $data_prix = DB::table('prix_vente_produits')
            ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
            ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
            ->select('produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as Libelle_produit', 'agences.NomAgence', 'categorie_clients.Libelle as Libelle_client', 'prix_vente_produits.prix')
            ->where('prix_vente_produits.prix', '>', 0)
            ->where('prix_vente_produits.agence_id', '=', $site_id)
            ->orderBy('prix_vente_produits.id', 'desc')
            ->get();

            // dd($data_prix);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $htmlContent = view('page.produit.gestion_prix.imprimer', [
            'gestions_prix' => $data_prix,
            'imageEntetePied' => $imageEntetePied,
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
        $prefixe = 'gestion_prix';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        // Afficher le PDF dans le navigateur
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);

    }

    public function exportPrixHistory(Request $request)
    {

        // $data_prix_history = json_decode($request->input('data'), true);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $site_id =session()->get('site_id');


        $data_prix_history = DB::table('historique_prix_produits')
            ->join('produits', 'historique_prix_produits.produit_id', '=', 'produits.id')
            ->join('agences', 'historique_prix_produits.agence_id', 'agences.id')
            ->join('categorie_clients', 'historique_prix_produits.categorie_client_id', 'categorie_clients.id')
            ->select('historique_prix_produits.date_changement_prix', 'historique_prix_produits.prix', 'produits.Designation', 'agences.NomAgence', 'categorie_clients.Libelle')
            ->orderBy('historique_prix_produits.id', 'desc')
            ->where('agences.id', '=', $site_id)
            ->get();


        $data = [
            'data_prix_history' => $data_prix_history,
            'texteEntetePied' => $texteEntetePied,
        ];

        // dd($data);

        $prefixe = 'prix_export_history';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new GestionPrixExportHistory($data), $nom_excel);
    }

    public function imprimerPrixHistory(Request $request)
    {

        // $data = json_decode($request->input('data'), true);
        // dd($data);

        $site_id =session()->get('site_id');


        $data_prix_history = DB::table('historique_prix_produits')
            ->join('produits', 'historique_prix_produits.produit_id', '=', 'produits.id')
            ->join('agences', 'historique_prix_produits.agence_id', 'agences.id')
            ->join('categorie_clients', 'historique_prix_produits.categorie_client_id', 'categorie_clients.id')
            ->select('historique_prix_produits.date_changement_prix', 'historique_prix_produits.prix', 'produits.Designation', 'agences.NomAgence', 'categorie_clients.Libelle')
            ->orderBy('historique_prix_produits.id', 'desc')
            ->where('agences.id', '=', $site_id)
            ->get();

            // dd($data_prix_history);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $htmlContent = view('page.produit.gestion_prix.imprimer-history', [
            'gestions_prix_history' => $data_prix_history,
            'imageEntetePied' => $imageEntetePied,
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
        return $dompdf->stream('document.pdf', ['Attachment' => false]);
    }
}
