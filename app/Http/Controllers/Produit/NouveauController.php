<?php

namespace App\Http\Controllers\Produit;

use Exception;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Produit;
use App\Models\Activity;
use App\Models\Emballage;
use Illuminate\Http\Request;
use App\Models\UniteComptage;
use App\Models\CategorieClient;
use App\Models\CategorieProduit;
use App\Models\PrixVenteProduit;
use App\Models\ProduitHistorique;
use App\Models\CategorieEmballage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NouveauController extends Controller
{

    // retourne la vue de nouveau
    public function  index()
    {
        $this->authorize('consulter-liste-produits');
        try {
            $categorie_produits = CategorieProduit::orderBy('created_at', 'desc')->get();
            $unite_comptages = UniteComptage::orderBy('created_at', 'desc')->get();
            $emballages = Emballage::where('Statut_emballage', 1)->get();
            $listeCatEmballage = CategorieEmballage::all();
            return view('page.produit.nouveau.nouveau', [
                'categorie_produits' => $categorie_produits,
                'unite_comptages' => $unite_comptages,
                'produit' => new Produit,
                'emballages' => $emballages,
                'listeCatEmballage' => $listeCatEmballage,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('creer-produit');

        try {
            // Définir les données à valider
            $data = $request->only(['type', 'reference', 'designation', 'categorie', 'unite_comptage','Emballage_id','type_emballage', 'statut']);
            // Définir les règles de validation
            $validatorRules = [
                'type' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'reference' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'designation' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'categorie' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'unite_comptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'statut' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'type.required' => "Le type  est requis",
                'reference.required' => "La reference  est requise",
                'designation.required' => "La designation  est requise",
                'categorie.required' => "La categorie est requise",
                'statut.required' => "Le statut est requis",
                'unite_comptage.required' => "L\'unite_comptage est requise",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('page.nouveau.nouveau')->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $type = $data['type'];
            $reference = $data['reference'];
            $designation = $data['designation'];
            $categorie = $data['categorie'];
            $statut = $data['statut'];
            $unite_comptage = $data['unite_comptage'];
            $Emballage_id = $data['Emballage_id'];
            $type_emballage = $data['type_emballage'];

            if ($type === 'AUCUN') {
                return to_route('page.nouveau.nouveau',)->with('error', 'Le type de produit selectionnez doit être soit une prestation ou un produit.');
            }

            $produit_exist = Produit::where('type', $type)
                ->where('Reference', $reference)
                ->where('Designation', $designation)
                ->where('Id_Categorie', $categorie)
                ->where('Statut', $statut)
                ->where('Id_Unite_Comptage', $unite_comptage)
                ->exists();

                $produit_exist_ref = Produit::where('type', $type)
                ->where('Reference', $reference)
                ->exists();

                $produit_exist_desig = Produit::where('type', $type)
                ->where('Designation', $designation)
                ->exists();

                if ($produit_exist_desig) {
                    return to_route('page.nouveau.nouveau')->with('error', 'La désignation produit que vous essayez d\'ajouter existe déjà.');
                }

                if ($produit_exist_ref) {
                    return to_route('page.nouveau.nouveau')->with('error', 'Le reéference produit que vous essayez d\'ajouter existe déjà.');
                }

            if ($produit_exist) {
                return to_route('page.nouveau.nouveau')->with('error', 'Le produit que vous essayez d\'ajouter existe déjà.');
            } else {

                $produit = new Produit();
                $produit->Type = $type;
                $produit->Reference = $reference;
                $produit->Designation = $designation;
                $produit->Id_Categorie = $categorie;
                $produit->Id_Unite_Comptage = $unite_comptage;
                $produit->Emballage_id = $Emballage_id;
                $produit->type_emballage = $type_emballage;
                $produit->Statut = $statut;
                $produit->Enregistrer_par = auth()->user()->id;
                $produit->save();

                $historique_produit = new ProduitHistorique();
                $historique_produit->Id_Produit = $produit->id;
                $historique_produit->Designation = $designation;
                $historique_produit->Enregistrer_par = auth()->user()->id;
                $historique_produit->save();

                //Prérenplissage de la table de gestion des prix
                $agences = Agence::where('EnActivite', 1)->get();
                $categorie_clients = CategorieClient::where('Statut', 1)->get();
                foreach ($agences as $agence) {
                    foreach ($categorie_clients as $categorie_client) {
                        $insert_prix_produit = new PrixVenteProduit;
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

                if ($type == 'PRESTATION') {
                    $stock = new Stock();
                    $stock->Id_Produit = $produit->id;
                    $stock->Qte_stockee = '1';
                    $stock->save();
                }
                if ($type == 'TAXE_SIMPLE') {
                    $stock = new Stock();
                    $stock->Id_Produit = $produit->id;
                    $stock->Qte_stockee = '1';
                    $stock->save();
                }


                // Enregistrement de l'activité associée
                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a créé un nouveau produit de référence ' . $reference,
                ]);

                return to_route('page.produit.produit')->with('success', 'Le produit a bien été ajouté');
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('modifier-produit');
        try {
            $produit = DB::table('produits')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->leftJoin('emballages', 'produits.Emballage_id', '=', 'emballages.id')
                ->select('produits.*', 'categorie_produits.Libelle as Libelle_categorie', 'unite_comptages.Libelle', 'emballages.Nom_emballage')
                ->where('produits.id', '=', $id)
                ->get();
            // dd($produit);
            $categorie_produits = CategorieProduit::orderBy('created_at', 'desc')->get();
            $unite_comptages = UniteComptage::orderBy('created_at', 'desc')->get();
            $emballages = Emballage::where('Statut_emballage', 1)->get();
            $listeCatEmballage = CategorieEmballage::all();
            return view('page.produit.nouveau.edit', [
                'produit' => $produit,
                'categorie_produits' => $categorie_produits,
                'unite_comptages' => $unite_comptages,
                'emballages' => $emballages,
                'listeCatEmballage' => $listeCatEmballage
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        $this->authorize('modifier-produit');
        try {
            // Définir les données à valider
            $data = $request->only(['type', 'reference', 'designation', 'categorie', 'unite_comptage','Emballage_id','type_emballage', 'statut']);
            // Définir les règles de validation
            $validatorRules = [
                // 'type' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                // 'reference' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'designation' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'categorie' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'unite_comptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                // 'statut' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                // 'type.required' => "Le type  est requis",
                // 'reference.required' => "La reference  est requise",
                'designation.required' => "La designation  est requise",
                'categorie.required' => "La categorie est requise",
                // 'statut.required' => "Le statut est requis",
                'unite_comptage.required' => "L\'unite_comptage est requise",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('page.produit.produit')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $type = $data['type'];
            // $reference = $data['reference'];
            $designation = $data['designation'];
            $categorie = $data['categorie'];
            $statut = $data['statut'];
            $unite_comptage = $data['unite_comptage'];
            $Emballage_id = $data['Emballage_id'];

            if($Emballage_id == null){
                $type_emballage = null;
            }else{
                $type_emballage = $data['type_emballage'];
            }


            $produit = Produit::find($id);
            // $produit->Reference = $reference;
            $produit->Type = $type;
            $produit->Designation = $designation;
            $produit->Id_Categorie = $categorie;
            $produit->Statut = $statut;
            $produit->Id_Unite_Comptage = $unite_comptage;
            $produit->Emballage_id = $Emballage_id;
            $produit->type_emballage = $type_emballage;
            $produit->Modifier_par = auth()->user()->id;
            $produit->Enregistrer_par = auth()->user()->id;
            $produit->update();

            $historique_produit = new ProduitHistorique();
            $historique_produit->Id_Produit = $produit->id;
            $historique_produit->Designation = $designation;
            $historique_produit->Enregistrer_par = auth()->user()->id;
            $historique_produit->save();


            if ($type== 'PRESTATION' || $type == 'TAXE_SIMPLE') {
                $stock = Stock::where('Id_Produit', $produit->id)->first();

                if ($stock) {
                    $stock->Id_Magasin = Null;
                    $stock->Qte_stockee = 1;
                    $stock->save();
                } else {
                    $stock = new Stock();
                    $stock->Id_Produit = $produit->id;
                    $stock->Qte_stockee = 1;
                    $stock->save();
                }
            }


            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifier le produit de référence ' . $designation,
            ]);
            return to_route('page.produit.produit')->with('success', 'Le produit a bien été modifié');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
}
