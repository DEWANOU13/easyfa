<?php

namespace App\Http\Controllers\accueil;

use Exception;
use App\Models\Image;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\CategorieClient;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategorieClientExport;
use App\Models\Agence;
use App\Models\PrixVenteProduit;
use App\Models\Produit;
use Illuminate\Support\Facades\Validator;


class categorieclientController extends Controller
{

    public function listeCategorieClient()
    {
        $this->authorize('consulter-categorie-client');
        try {
            //$listeCategorieClient = CategorieClient::orderBy('created_at', 'desc')->get();
            $listeCategorieClient = CategorieClient::join('users as creator', 'categorie_clients.Enregistrer_par', '=', 'creator.id')
                ->leftjoin('users as modifier', 'categorie_clients.Modifier_par', '=', 'modifier.id')
                ->select('categorie_clients.*', 'creator.name as Enregistrer_par', 'modifier.name as Modifier_par')
                ->orderBy('categorie_clients.created_at', 'desc')
                ->get();

            return view('page.accueil.categorie_client.categorie_client', ['listeCategorieClient' => $listeCategorieClient]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite");
        }
    }

    /*  public function showForm()
    {
        try {
            return view('page.accueil.categorie_client.nouveau');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    } */



    public function storeCategorieClient(Request $request)
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
                return redirect()->back()->withErrors($validatorResult)->withInput();
                return to_route('ShowFormCategorieClient')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['Libelle'];
            $existingCat = CategorieClient::where('Libelle', $libelle)->first();

            if ($existingCat) {
                return back()->with('error', 'Une catégorie client avec le même nom existe déjà.');
            }
            $user_connecterId = auth()->user()->id;

            $catClient = new CategorieClient();
            $catClient->Libelle = $libelle;
            $catClient->Enregistrer_par = $user_connecterId;
            $catClient->save();


        $agences = Agence::all();
        $produits = Produit::all();

            foreach ($produits as $produit) {
                foreach ($agences as $agence) {
                    $insert_prix_produit = new PrixVenteProduit();
                    $insert_prix_produit->produit_id = $produit->id;
                    $insert_prix_produit->categorie_client_id = $catClient->id;
                    $insert_prix_produit->date_enregistrement = now();
                    $insert_prix_produit->date_variation_prix = now();
                    $insert_prix_produit->prix = 0;
                    $insert_prix_produit->user_id = auth()->user()->id;
                    $insert_prix_produit->agence_id = $agence->id;
                    $insert_prix_produit->save();
                }
            }

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a créé la catégorie de client' . $libelle,
            ]);

            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('categorieclient')->with('success', 'La categorie a bien été ajoutée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function updateCategorieClient(Request $request, string $id)
    {
        $this->authorize('modifier-categorie-client');

        try {
            $data = $request->only(['UpLibelle',]);



            $validatorRules = [
                'UpLibelle' => 'required',
            ];

            $validationMessages = [
                'UpLibelle.required' => "Le nom de la catégorie est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                //return to_route('editMagasin')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['UpLibelle'];

            $user_connecterId = auth()->user()->id;


            $catClient = CategorieClient::findorfail($id);
            $catClient->Libelle = $libelle;
            $catClient->Modifier_par = $user_connecterId;
            $catClient->update();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié la catégorie de client' . $libelle,
            ]);

            return to_route('categorieclient')->with('success', 'Modification effectuée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportExcelCategorieClient(Request $request){
        $data = $request->validate([
            'tableCategorieClientData' => 'required',
        ]);

        // Créer l'exportation
        $export = new CategorieClientExport($data);

        // Générer le nom de fichier
        $fileName = 'Categorie_Client_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        // Retourner le fichier pour téléchargement direct
        return Excel::download($export, $fileName);
    }
}
