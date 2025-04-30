<?php

namespace App\Http\Controllers\accueil;

use Exception;
use App\Models\Action;
use App\Models\Agence;
use App\Models\ActionUser;
use App\Models\AgenceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CategorieClient;
use App\Models\PrixVenteProduit;
use App\Models\Produit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class agenceController extends Controller
{
    public function listeAgences(Request $request)
    {
        $this->authorize('consulter-liste-agence');
        // $query = Agence::join('users', 'agences.create_user_id', '=', 'users.id')
        // //->join('users', 'agences.Modifier_par', '=', 'users.id')
        // ->select('agences.*', 'users.name as create_user_id');

        $agenceUserId = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
        $query = Agence::query()->whereIn('id', $agenceUserId);

        $site_id = session()->get('site_id');

        if ($request->filled('agenceFilter_id')) {
            if (is_array(getIdAgenceFilter($request->input('agenceFilter_id')))) {
                $query = $query->whereIn('id', getIdAgenceFilter($request->input('agenceFilter_id')));
            } else {
                $query = $query->where('id', '=', getIdAgenceFilter($request->input('agenceFilter_id')));
            }
        } else {
            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $query = $query;
            } else {
                $query = $query->where('id', '=', $site_id);
            }
        }

        $listeAgences = $query->orderBy('agences.created_at', 'desc')->get();
        // dd($listeAgences);

        return view('page.accueil.agences.agence', ['listeAgences' => $listeAgences]);
    }

    public function showForm()
    {
        $this->authorize('creer-agence');
        try {
            return view('page.accueil.agences.nouveau');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function storeAgence(Request $request)
    {
        $this->authorize('creer-agence');
        $request->validate([
            'NomAgence' => ['required'],
            'EnActivite' => ['required'],
            'titre_signataire_facture' => ['nullable'],
            'nom_signataire' => ['nullable'],
            'adresseAgence' => ['required'],
            'numero_telephone_1' => ['required'],
            'numero_telephone_2' => ['nullable'],
        ]);
        $existingAgence = Agence::where('NomAgence', $request->NomAgence)->first();

        if ($existingAgence) {
            return back()->with('error', 'Une agence avec le même nom existe déjà.');
        }

        $agence = new Agence();
        $agence->NomAgence = $request->NomAgence;
        $agence->EnActivite = $request->EnActivite;
        $agence->titre_signataire_facture = $request->titre_signataire_facture;
        $agence->nom_signataire = $request->nom_signataire;
        $agence->adresseAgence = $request->adresseAgence;
        $agence->numero_telephone_1 = $request->numero_telephone_1;
        $agence->numero_telephone_2 = $request->numero_telephone_2;
        $agence->create_user_id = auth()->user()->id;
        $agence->save();


        $categorie_clients = CategorieClient::where('Statut', 1)->get();
        $produits = Produit::all();

        // foreach ($agence as $agence) {
        foreach ($produits as $produit) {
            foreach ($categorie_clients as $categorie_client) {
                $insert_prix_produit = new PrixVenteProduit();
                $insert_prix_produit->produit_id = $produit->id;
                $insert_prix_produit->categorie_client_id = $categorie_client->id;
                $insert_prix_produit->date_enregistrement = now();
                $insert_prix_produit->date_variation_prix = now();
                $insert_prix_produit->prix = 0;
                $insert_prix_produit->user_id = auth()->user()->id;
                $insert_prix_produit->agence_id = $agence->id;
                $insert_prix_produit->save();
            }
        }
        // }

        $idsAction = Action::pluck('id')->toArray();

        foreach ($idsAction as $idAction) {
            DB::table('action_users')->insert([
                ['user_id' => 1, 'agence_id' => $agence->id, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => 2, 'agence_id' => $agence->id, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => 3, 'agence_id' => $agence->id, 'action_id' => $idAction, 'created_at' => now(), 'updated_at' => now()],

            ]);
        }

        if($agence->id != null){
            DB::table('agence_users')->insert([
                ['agence_id' => $agence->id, 'user_id' => 1],
                ['agence_id' => $agence->id, 'user_id' => 2],
                ['agence_id' => $agence->id, 'user_id' => 3]
            ]);
        }

        return back()->with('success', 'Agence ajoutée avec succès ');
    }


    public function updateAgence(Request $request, string $id)
    {

        $this->authorize('modifier-agence');
        try {


            $data = $request->only(['UpNomAgence', 'Up_EnActivite', 'nom_signataire', 'titre_signataire_facture', 'adresseAgence', 'numero_telephone_1', 'numero_telephone_2']);

            $validatorRules = [
                'UpNomAgence' => 'required',
                'Up_EnActivite' => 'required',
                'titre_signataire_facture' => 'nullable',
                'nom_signataire' => 'nullable',
                'adresseAgence' => 'required',
                'numero_telephone_1' => 'required',
                'numero_telephone_2' => 'nullable',
            ];

            $validationMessages = [
                'UpNomAgence.required' => "Le nom de la catégorie est requise",
                'Up_EnActivite.required' => "Le statut est requise",
                'titre_signataire_facture.nullable' => "",
                'nom_signataire.nullable' => "",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
            }

            $user_connecterId = auth()->user()->id;


            $NomAgence = $data['UpNomAgence'];
            $EnActivite = $data['Up_EnActivite'];
            $titre_signataire_facture = $data['titre_signataire_facture'];
            $nom_signataire = $data['nom_signataire'];
            $adresseAgence = $data['adresseAgence'];
            $numero_telephone_1 = $data['numero_telephone_1'];
            $numero_telephone_2 = $data['numero_telephone_2'];


            $agence = Agence::findorfail($id);
            $agence->NomAgence = $NomAgence;
            $agence->EnActivite = $EnActivite;
            $agence->titre_signataire_facture = $titre_signataire_facture;
            $agence->nom_signataire = $nom_signataire;
            $agence->adresseAgence = $adresseAgence;
            $agence->numero_telephone_1 = $numero_telephone_1;
            $agence->numero_telephone_2 = $numero_telephone_2;
            $agence->update_user_id = auth()->user()->id;
            $agence->update();
            return to_route('agences')->with('success', 'Modification éffectuée avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    /* Recherche automatique */
    public function searchAgence(Request $request)
    {
        try {
            $query = $request->input('query');
            $listeAgences = Agence::where('NomAgence', 'like', '%' . $query . '%')->get();

            // Retourner une vue partielle avec les résultats de la recherche
            return view('page.accueil.agences.searchAgence', ['listeAgences' => $listeAgences]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
}
