<?php

namespace App\Http\Controllers\Produit;

use Exception;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\groupeCategorie;
use App\Models\CategorieProduit;
use App\Http\Controllers\Controller;
use App\Models\associationGroupeCategorie;
use Illuminate\Support\Facades\Validator;

class groupeCategorieController extends Controller
{
    public function index()
    {
        $this->authorize('liste-groupe-categorie-produit');

        $listeGroupeCat = groupeCategorie::all();
        $listeCategorieNonLiee = CategorieProduit::whereNotIn('id', function($query) {
            $query->select('categorie_produit_id')
                  ->from('association_groupe_categories');
        })->get();

        $listeAssociation = associationGroupeCategorie::join('groupe_categories', 'association_groupe_categories.groupe_categorie_id', '=', 'groupe_categories.id')
                            ->join('categorie_produits', 'association_groupe_categories.categorie_produit_id', '=', 'categorie_produits.id')
                            ->select('association_groupe_categories.*', 'groupe_categories.libelle as libelle_groupe', 'categorie_produits.Libelle as libelle_cat_produit')
                            ->get();

        return view('page.produit.groupeCategorie.groupe_categorie', ['listeGroupeCat' => $listeGroupeCat,
            'listeCategorieNonLiee' => $listeCategorieNonLiee,
            'listeAssociation' => $listeAssociation

        ]);
    }

    public function storeGroupeCategorie(Request $request)
    {
        $this->authorize('creer-groupe-categorie-produit');

        try{
            // Définir les données à valider
            $data = $request->only(['libelle']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'libelle' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'libelle.required' => "Le nom est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['libelle'];

            $categorie_exist = groupeCategorie::where('libelle', '=', $libelle)->first();
            if($categorie_exist !== null){
                return back()->with('error', 'Le groupe que vous essayez d\'ajouter existe déjà.');
            }else{
                $categorie = new groupeCategorie();
                $categorie->libelle = $libelle;
                $categorie->Enregistrer_par = auth()->user()->id;
                $categorie->save();

                // Enregistrement de l'activité associée
                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a créé le groupe  ' . $libelle,
                ]);

                return to_route('groupeCategorie')->with('success', 'La categorie a bien été ajoutée');
            }
        }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }


    }

    public function updateGroupeCategorie(Request $request)
    {
        $this->authorize('modifier-groupe-categorie-produit');

        try{
            // Définir les données à valider
            $data = $request->only(['libelle']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'libelle' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'libelle.required' => "Le nom est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['libelle'];

            $categorie_exist = groupeCategorie::where('libelle', '=', $libelle)->first();
            if($categorie_exist !== null){
                return back()->with('error', 'Le groupe que vous essayez d\'ajouter existe déjà.');
            }else{
                $categorie = groupeCategorie::find($request->groupe_categorie_ids);
                $categorie->libelle = $libelle;
                $categorie->Enregistrer_par = auth()->user()->id;
                $categorie->update();

                return to_route('groupeCategorie')->with('success', 'La categorie a bien été modifiée');
            }
        }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }


    }


    public function storeAssociationGroupeCategorieProduit(Request $request)
    {
        $this->authorize('associer-categorie-et-groupe-categorie-produit');
        $request->validate([
            'groupe_categorie_id' => 'required|exists:groupe_categories,id',
            'categorie_produit_id' => 'required|array',
            'categorie_produit_id.*' => 'exists:categorie_produits,id', // Vérifie que chaque ID existe
        ]);


        $groupeCatid = $request->input('groupe_categorie_id');

        foreach($request->input('categorie_produit_id') as $id) {
            $association = new associationGroupeCategorie();
            $association->groupe_categorie_id = $groupeCatid;
            $association->categorie_produit_id = $id;
            $association->save();
        }

        return to_route('groupeCategorie')->with('success', 'La liaison a bien été ajoutée');


    }


    public function deleteSelected(Request $request)
    {
        // Valider que des IDs sont passés
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:association_groupe_categories,id', // Vérifie que chaque ID existe
        ]);

        if(count($request->ids) == 0){
            return redirect()->back()->with('error', 'Veuillez selectionner au moins un element');
        }

        // Supprimer les associations sélectionnées
        associationGroupeCategorie::whereIn('id', $request->ids)->delete();

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Les éléments sélectionnés ont été révoqués.');
    }

}
