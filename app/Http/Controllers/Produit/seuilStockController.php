<?php

namespace App\Http\Controllers\Produit;

use Exception;
use App\Models\SeuilStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CategorieProduit;
use App\Models\ChoixSeuil;
use App\Models\Produit;
use App\Models\SeuilStockProduit;
use App\Models\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class seuilStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('seuil-stock');
        // $seuil_stock = SeuilStock::orderBy('id', 'desc')->first();
        $listeCategorie = CategorieProduit::where('Libelle', '<>', 'PRESTATION')->orderBy('id', 'desc')->get();
        $choix_seuil = ChoixSeuil::first();

        $produits = Produit::all();

        $seuil_stocks = DB::table('seuil_stocks')
            ->join('categorie_produits', 'seuil_stocks.id_categorie_produit', 'categorie_produits.id')
            ->select('seuil_stocks.*', 'categorie_produits.Libelle')
            ->get();

        $seuil_stock_produits = DB::table('seuil_stock_produits')
            ->join('produits', 'seuil_stock_produits.id_produit', 'produits.id')
            ->select('seuil_stock_produits.*', 'produits.Designation')
            ->get();

        // dd($seuil_stock_produits);

        return view('page.produit.seuil_stock.seuil_stock', [
            'seuil_stocks' => $seuil_stocks,
            'listeCategorie' => $listeCategorie,
            'seuil_stock_produits' => $seuil_stock_produits,
            'produits' => $produits,
            'choix_seuil' => $choix_seuil,

        ])->with('value', 'categorie');
    }

    public function storeSeuilStock(Request $request)
    {
        // $this->authorize('creer-groupe-categorie-produit');

        try {

            $tableau_categorie_produit_id = $request->input(['categorie_produit_id']);
            $quantity_seuil = $request->input(['quantity_seuil']);

            // Définir les règles de validation
            $validatorRules = [
                'quantity_seuil' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'categorie_produit_id' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'quantity_seuil.required' => "Le seuil_stock est requis",
                'categorie_produit_id.required' => "Le seuil_stock est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            foreach ($tableau_categorie_produit_id as $categorie_produit_id) {

                $categorie = new SeuilStock();
                $categorie->seuil_stock = $quantity_seuil;
                $categorie->id_categorie_produit = $categorie_produit_id;
                $categorie->Enregistrer_par = auth()->user()->id;
                $categorie->save();
            }

            return to_route('seuilStock')->with('success', 'Le seuil stock est défini');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite ");
        }
    }



    public function editSeuil(Request $request)
    {

        $choix_seuil = ChoixSeuil::first();
        // dd($request->all());

        $id = $request->input('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Désolé l\'opération ne peut être effectué. Veuillez selectionner avant toutes actions.');
        }

        $listeCategorie = CategorieProduit::where('Libelle', '<>', 'PRESTATION')->orderBy('id', 'desc')->get();

        $produits = Produit::all();

        $seuil_stocks = DB::table('seuil_stocks')
            ->join('categorie_produits', 'seuil_stocks.id_categorie_produit', 'categorie_produits.id')
            ->select('seuil_stocks.*', 'categorie_produits.Libelle')
            ->get();

        $seuil_edit = DB::table('seuil_stocks')
            ->join('categorie_produits', 'seuil_stocks.id_categorie_produit', 'categorie_produits.id')
            ->select('seuil_stocks.*', 'categorie_produits.Libelle')
            ->where('seuil_stocks.id', $id)
            ->first();

        // dd($seuil_edit);

        return view('page.produit.seuil_stock.seuil_stock', [
            'seuil_stocks' => $seuil_stocks,
            'listeCategorie' => $listeCategorie,
            'edit_seuil' => $seuil_edit,
            'produits' => $produits,
            'choix_seuil' => $choix_seuil,
        ])->with('value', 'categorie');
    }



    public function updateSeuilStock(Request $request, $id)
    {
        $this->authorize('modifier-groupe-categorie-produit');

        // dd($id);

        try {
            // Définir les données à valider
            // $data = $request->only(['seuil_stock']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'seuil_stock' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'seuil_stock.required' => "Le seuil_stock est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $quantity_seuil = $request->input('quantity_seuil');
            $seuil_categorie = SeuilStock::find($id);

            // dd($seuil_produit);

            if ($seuil_categorie) {
                $seuil_categorie->seuil_stock = $quantity_seuil;
                $seuil_categorie->update();
            }

            return to_route('seuilStock')->with('success', 'Le seuil a été modifié avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite ");
        }
    }

    public function storeSeuilStockProduit(Request $request)
    {
        // $this->authorize('creer-groupe-categorie-produit');

        try {

            // dd($request->all());

            $tableau_produit_id = $request->input(['produit_id']);
            $quantity_seuil = $request->input(['quantity_seuil']);

            // Définir les règles de validation
            $validatorRules = [
                'quantity_seuil' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'produit_id' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'quantity_seuil.required' => "Le seuil_stock est requis",
                'produit_id.required' => "Le seuil_stock est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            foreach ($tableau_produit_id as $produit_id){

                $seuil_produit_existe = SeuilStockProduit::where('id_produit', $produit_id)->first();

                if ($seuil_produit_existe) {
                    $seuil_produit_existe->seuil_stock = $quantity_seuil;
                    $seuil_produit_existe->update();
                } else {
                    $seuil_produit = new SeuilStockProduit();
                    $seuil_produit->seuil_stock = $quantity_seuil;
                    $seuil_produit->id_produit = $produit_id;
                    $seuil_produit->Enregistrer_par = auth()->user()->id;
                    $seuil_produit->save();
                }

                // dd($seuil_produit);
            }

            // return to_route('seuilStock')->with('success', 'Le seuil stock est défini');
            return redirect()->back()->with('success', 'Le seuil stock est défini');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite ");
        }
    }


    public function storeSeuilStockProduitAll(Request $request)
    {
        // $this->authorize('creer-groupe-categorie-produit');


        $produits = Produit::where('Type', '<>', 'PRESTATION')->get();

        // dd($produits);
        try {

            $quantity_seuil = $request->input(['quantity_seuil']);

            // Définir les règles de validation
            $validatorRules = [
                'quantity_seuil' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                // 'produit_id' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'quantity_seuil.required' => "Le seuil_stock est requis",
                // 'produit_id.required' => "Le seuil_stock est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            foreach ($produits as $produit) {

                $seuil_produit_existe = SeuilStockProduit::where('id_produit', $produit->id)->first();

                if ($seuil_produit_existe) {
                    $seuil_produit_existe->seuil_stock = $quantity_seuil;
                    $seuil_produit_existe->update();
                } else {
                    $seuil_produit = new SeuilStockProduit();
                    $seuil_produit->seuil_stock = $quantity_seuil;
                    $seuil_produit->id_produit = $produit->id;
                    $seuil_produit->Enregistrer_par = auth()->user()->id;
                    $seuil_produit->save();
                }
                // dd($seuil_produit);
            }

            // return to_route('seuilStock')->with('success', 'Le seuil stock est défini');
            return redirect()->back()->with('success', 'Le seuil stock est défini');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite ");
        }
    }

    public function editSeuilProduit(Request $request)
    {

        // dd($request->all());

        $id = $request->input('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Désolé l\'opération ne peut être effectué. Veuillez selectionner avant toutes actions.');
        }
        $choix_seuil = ChoixSeuil::first();

        $listeCategorie = CategorieProduit::where('Libelle', '<>', 'PRESTATION')->orderBy('id', 'desc')->get();

        $produits = Produit::all();

        $seuil_stocks = DB::table('seuil_stocks')
            ->join('categorie_produits', 'seuil_stocks.id_categorie_produit', 'categorie_produits.id')
            ->select('seuil_stocks.*', 'categorie_produits.Libelle')
            ->get();

        $seuil_edit_produit = DB::table('seuil_stock_produits')
            ->join('produits', 'seuil_stock_produits.id_produit', 'produits.id')
            ->select('seuil_stock_produits.*', 'produits.Designation')
            ->where('seuil_stock_produits.id', $id)
            ->first();

        $produits = Produit::all();

        $seuil_stock_produits = DB::table('seuil_stock_produits')
            ->join('produits', 'seuil_stock_produits.id_produit', 'produits.id')
            ->select('seuil_stock_produits.*', 'produits.Designation')
            ->get();

        // dd($seuil_edit);

        return view('page.produit.seuil_stock.seuil_stock', [
            'seuil_stocks' => $seuil_stocks,
            'choix_seuil' => $choix_seuil,
            'listeCategorie' => $listeCategorie,
            'edit_seuil_produit' => $seuil_edit_produit,
            'produits' => $produits,
            'seuil_stock_produits' => $seuil_stock_produits,
        ])->with('value', 'categorie');
    }


    public function updateSeuilStockProduit(Request $request, $id)
    {
        $this->authorize('modifier-groupe-categorie-produit');

        // dd($request->all());

        // dd($id);



        try {
            // Définir les données à valider
            // $data = $request->only(['seuil_stock']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'quantity_seuil' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'quantity_seuil.required' => "Le seuil_stock est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }


            $quantity_seuil = $request->input('quantity_seuil');
            $seuil_produit = SeuilStockProduit::find($id);

            // dd($seuil_produit);

            if ($seuil_produit) {
                $seuil_produit->seuil_stock = $quantity_seuil;
                $seuil_produit->update();
            }

            return to_route('seuilStock')->with('success', 'Le seuil a été modifié avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite ");
        }
    }

    public function ChoixSeuil(Request $request)
    {

        // dd($request->all());

        $reponse = $request->input('reponse');

        // dd($reponse);
        $choix_seuil = ChoixSeuil::first();

        // dd($choix_seuil);

        if ($choix_seuil == null) {
            $new_choix_seuil = new ChoixSeuil();
            $new_choix_seuil->libelle = $reponse;
            $new_choix_seuil->save();
        } else {
            $choix_seuil->libelle = $reponse;
            $choix_seuil->update();
        }

        return to_route('seuilStock')->with('success', 'La seuil modifiée avec succès');
    }

    public function RestituerProduit(Request $request)
    {

        // dd($request->all());
        $id = $request->input('id');

        // dd($reponse);

        $restituer_produit = SeuilStockProduit::find($id);

        // dd($choix_seuil);

        if ($restituer_produit) {
            $restituer_produit->delete();
        }

        return to_route('seuilStock')->with('success', 'Le seuil a été restitué avec succès');
    }

    public function Restituer(Request $request)
    {

        // dd($request->all());
        // dd($request->all());
        $id = $request->input('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Désolé l\'opération ne peut être effectué. Veuillez selectionner avant toutes actions.');
        }

        // dd($reponse);

        $restituer = SeuilStock::find($id);

        // dd($choix_seuil);
        if ($restituer) {
            $restituer->delete();
        }

        return to_route('seuilStock')->with('success', 'Le seuil a été restitué avec succès');
    }
}
