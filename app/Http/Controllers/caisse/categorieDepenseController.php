<?php

namespace App\Http\Controllers\caisse;

use Exception;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\CategorieDepense;
use App\Http\Controllers\Controller;
use App\Models\CategorieRecette;
use Illuminate\Support\Facades\Validator;

class categorieDepenseController extends Controller
{
    public function categorie_depense()
    {
        $this->authorize('voir-liste-categorie-depense');
        $listeCatDepenses = CategorieDepense::join('users as creator', 'categorie_depenses.Enregistrer_par', '=', 'creator.id')
        ->leftjoin('users as modifier', 'categorie_depenses.Modifier_par', '=', 'modifier.id')
        ->select('categorie_depenses.*', 'creator.name as Enregistrer_par', 'modifier.name as Modifier_par')
        ->get();
        return view('page.caisse.categorie_depense.categorie', ['listeCatDepenses' => $listeCatDepenses]);
    }

    public function storeCatDepense(Request $request)
    {
        $this->authorize('creer-categorie-depense');
        $data = $request->only(['designation', 'statut']);

            $validatorRules = [
                'designation' => 'required',
                'statut' => 'required',
            ];

            $validationMessages = [
                'designation.required' => "Le nom de la catégorie est requise",
                'statut.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_depense')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $designation = $data['designation'];
            $statut = $data['statut'];
            $existingCat = CategorieDepense::where('designation', $designation)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie de depense avec le même nom existe déjà.');
            }
            $user_connecterId = auth()->user()->id;

            $catEmballage = new CategorieDepense();
            $catEmballage->designation = $designation;
            $catEmballage->statut = $statut;
            $catEmballage->Enregistrer_par = $user_connecterId;
            $catEmballage->save();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a créé la catégorie de dépense " . $designation,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_depense')->with('success', 'La categorie a bien été ajoutée');
    }
    public function updateCatDepense(Request $request)
    {
        $this->authorize('modifier-categorie-depense');
        try {
            $data = $request->only(['designation', 'statut', 'id']);
            //dd($data);

            $validatorRules = [
                'designation' => 'required',
                'statut' => 'required',
                "id" => 'required',
            ];

            $validationMessages = [
                'designation.required' => "Le nom de la catégorie est requise",
                'statut.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_depense')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $designation = $data['designation'];
            $id = $data['id'];
            $statut = $data['statut'];

            $user_connecterId = auth()->user()->id;

            $catEmballage = CategorieDepense::findorfail($id);

            $catEmballage->designation = $designation;
            $catEmballage->statut = $statut;
            $catEmballage->Modifier_par = $user_connecterId;
            $catEmballage->update();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a modifié la catégorie d'emballage " . $designation,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_depense')->with('success', 'La categorie a bien été modifiée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function categorie_recette()
    {
        $listeCatRecettes = CategorieRecette::join('users as creator', 'categorie_recettes.Enregistrer_par', '=', 'creator.id')
        ->leftjoin('users as modifier', 'categorie_recettes.Modifier_par', '=', 'modifier.id')
        ->select('categorie_recettes.*', 'creator.name as Enregistrer_par', 'modifier.name as Modifier_par')
        ->get();
        return view('page.caisse.categorie_recette.categorie', ['listeCatRecettes' => $listeCatRecettes]);
    }
    public function storeCatRecette(Request $request)
    {
        $data = $request->only(['designation', 'statut']);

            $validatorRules = [
                'designation' => 'required',
                'statut' => 'required',
            ];

            $validationMessages = [
                'designation.required' => "Le nom de la catégorie est requise",
                'statut.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_recette')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $designation = $data['designation'];
            $statut = $data['statut'];
            $existingCat = CategorieRecette::where('designation', $designation)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie de recette avec le même nom existe déjà.');
            }
            $user_connecterId = auth()->user()->id;

            $catEmballage = new CategorieRecette();
            $catEmballage->designation = $designation;
            $catEmballage->statut = $statut;
            $catEmballage->Enregistrer_par = $user_connecterId;
            $catEmballage->save();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a créé la catégorie de rectte " . $designation,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_recette')->with('success', 'La categorie a bien été ajoutée');
    }
    public function updateCatRecette(Request $request)
    {

        try {
            $data = $request->only(['designation', 'statut', 'id']);
            //dd($data);

            $validatorRules = [
                'designation' => 'required',
                'statut' => 'required',
                "id" => 'required',
            ];

            $validationMessages = [
                'designation.required' => "Le nom de la catégorie est requise",
                'statut.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_recette')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $designation = $data['designation'];
            $id = $data['id'];
            $statut = $data['statut'];

            $user_connecterId = auth()->user()->id;

            $catRecette = CategorieRecette::findorfail($id);

            $catRecette->designation = $designation;
            $catRecette->statut = $statut;
            $catRecette->Modifier_par = $user_connecterId;
            $catRecette->update();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a modifié la catégorie d'emballage " . $designation,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_recette')->with('success', 'La categorie a bien été modifiée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }
}
