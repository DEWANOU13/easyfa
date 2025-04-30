<?php

namespace App\Http\Controllers\emballage;

use Exception;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\CategorieEmballage;
use App\Http\Controllers\Controller;
use App\Imports\EmballageImport;
use App\Models\Emballage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class emballageController extends Controller
{

    public function categorie_emballage()
    {
        accessEmballage();
        $this->authorize('liste-categorie-emballage');

        $listeCatEmballage = CategorieEmballage::join('users as creator', 'categorie_emballages.Enregistrer_par', '=', 'creator.id')
            ->leftjoin('users as modifier', 'categorie_emballages.Modifier_par', '=', 'modifier.id')
            ->select('categorie_emballages.*', 'creator.name as Enregistrer_par', 'modifier.name as Modifier_par')
            ->get();

        return view('page.emballage.categorie_emballage.categorie_embalage', ['listeCatEmballage' => $listeCatEmballage]);
    }

    public function storeCatEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('creer-categorie-emballage');

        try {
            $data = $request->only(['Libelle', 'Statut_cat_emballage']);

            $validatorRules = [
                'Libelle' => 'required',
                'Statut_cat_emballage' => 'required',
            ];

            $validationMessages = [
                'Libelle.required' => "Le nom de la catégorie est requise",
                'Statut_cat_emballage.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_emballage')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['Libelle'];
            $statut = $data['Statut_cat_emballage'];
            $existingCat = CategorieEmballage::where('Libelle', $libelle)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie client avec le même nom existe déjà.');
            }
            $user_connecterId = auth()->user()->id;

            $catEmballage = new CategorieEmballage();
            $catEmballage->Libelle = $libelle;
            $catEmballage->Statut_cat_emballage = $statut;
            $catEmballage->Enregistrer_par = $user_connecterId;
            $catEmballage->save();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a créé la catégorie d'emballage " . $libelle,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_emballage')->with('success', 'La categorie a bien été ajoutée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function updateCatEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('modifier-categorie-emballage');

        try {
            $data = $request->only(['Libelle', 'Statut_cat_emballage', 'id']);
            //dd($data);

            $validatorRules = [
                'Libelle' => 'required',
                'Statut_cat_emballage' => 'required',
                "id" => 'required',
            ];

            $validationMessages = [
                'Libelle.required' => "Le nom de la catégorie est requise",
                'Statut_cat_emballage.required' => "Le statut de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('categorie_emballage')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['Libelle'];
            $id = $data['id'];
            $statut = $data['Statut_cat_emballage'];

            $user_connecterId = auth()->user()->id;

            $catEmballage = CategorieEmballage::findorfail($id);

            $catEmballage->Libelle = $libelle;
            $catEmballage->Statut_cat_emballage = $statut;
            $catEmballage->Modifier_par = $user_connecterId;
            $catEmballage->update();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a modifié la catégorie d'emballage " . $libelle,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorie_emballage')->with('success', 'La categorie a bien été modifiée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function listeEmballage()
    {
        accessEmballage();
        $this->authorize('liste-emballage');

        $listeEmballage = Emballage::join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->join('users as creator', 'emballages.Enregistrer_par', '=', 'creator.id')
            ->leftjoin('users as modifier', 'emballages.Modifier_par', '=', 'modifier.id')
            ->select('emballages.*', 'creator.name as Enregistrer_par', 'modifier.name as Modifier_par', 'categorie_emballages.Libelle as Categorie_emballage')
            ->orderBy('emballages.created_at', 'desc')
            ->get();
        $listeCatEmballage = CategorieEmballage::where('Statut_cat_emballage', 1)->get();
        return view('page.emballage.emballage', ['listeCatEmballage' => $listeCatEmballage, 'listeEmballage' => $listeEmballage]);
    }

    public function storeEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('creer-emballage');

        try {
            $data = $request->only(['Nom_emballage', 'Categorie_emballage_id', 'Statut_emballage', 'Reference','type_emb']);

            $validatorRules = [
                'Nom_emballage' => 'required',
                'Categorie_emballage_id' => 'required',
                'Statut_emballage' => 'required',
                'type_emb' => 'required',
                'Reference' => 'required',
            ];

            $validationMessages = [
                'Nom_emballage.required' => "Le nom  est requis",
                'Categorie_emballage_id.required' => "La catégorie est requise",
                'Statut_emballage.required' => "Le statut de la catégorie est requise",
                'Reference.required' => "La reference est requise",
                'type_emb.required' => "Le type est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('emballage')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $Nom_emballage = $data['Nom_emballage'];
            $Reference = $data['Reference'];
            $statut = $data['Statut_emballage'];
            $categorie = $data['Categorie_emballage_id'];
            $type_emb = $data['type_emb'];
            $existingCat = Emballage::where('Nom_emballage', $Nom_emballage)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie client avec le même nom existe déjà.');
            }
            $user_connecterId = auth()->user()->id;

            $Emballage = new Emballage();
            $Emballage->Nom_emballage = $Nom_emballage;
            $Emballage->Reference = $Reference;
            $Emballage->Categorie_emballage_id = $categorie;
            $Emballage->type_emb = $type_emb;
            $Emballage->Statut_emballage = $statut;
            $Emballage->Enregistrer_par = $user_connecterId;
            $Emballage->save();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a créé l'emballage " . $Nom_emballage,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('emballage')->with('success', 'La categorie a bien été ajoutée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function updateEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('modifier-emballage');

        try {
            $data = $request->only(['Nom_emballage', 'Categorie_emballage_id', 'Statut_emballage', 'id', 'Reference','type_emb']);
            //dd($data);

            $validatorRules = [
                'Nom_emballage' => 'required',
                'Categorie_emballage_id' => 'required',
                'Statut_emballage' => 'required',
                "id" => 'required',
                'Reference' => 'required',
                'type_emb' => 'required',
            ];

            $validationMessages = [
                'Nom_emballage.required' => "Le nom  est requis",
                'Categorie_emballage_id.required' => "La catégorie est requise",
                'Statut_emballage.required' => "Le statut de la catégorie est requise",
                'Reference.required' => "La reference est requise",
                'type_emb.required' => "Le type est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('emballage')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $id = $data['id'];
            $Nom_emballage = $data['Nom_emballage'];
            $Reference = $data['Reference'];
            $statut = $data['Statut_emballage'];
            $categorie = $data['Categorie_emballage_id'];
            $type_emb = $data['type_emb'];
            $user_connecterId = auth()->user()->id;

            $Emballage = Emballage::findorfail($id);

            $Emballage->Nom_emballage = $Nom_emballage;
            $Emballage->Reference = $Reference;
            $Emballage->Categorie_emballage_id = $categorie;
            $Emballage->Statut_emballage = $statut;
            $Emballage->type_emb = $type_emb;
            $Emballage->Modifier_par = $user_connecterId;
            $Emballage->update();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => "a modifié l'emballage " . $Nom_emballage,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('emballage')->with('success', "L'emballage a bien été modifiée");
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }


    public function importerEmballage(Request $request){

        // dd('bien ici', $request->all());
        $this->authorize('effectuer-entrer-produit');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            // 'fournisseur' => 'required',
        ]);

        $fournisseur = $request->input('fournisseur');

        try {
            // Lancer l'importation du fichier Excel
            Excel::import(new EmballageImport(), $request->file('file'));

            return redirect()->back()->with('success', 'Importation réussie.');
        } catch (Exception $e) {
            // Attraper l'exception levée dans StockImport et afficher un message d'erreur
            return redirect()->back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }

    }
}
