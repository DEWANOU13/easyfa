<?php

namespace App\Http\Controllers\ImportExport;

//namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CategorieProduit;
use App\Models\UniteComptage;
use App\Models\Fournisseur;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\PrixVenteProduit;
use App\Models\HistoriquePrixProduit;
use App\Models\Activity;
use App\Models\Stock;
use App\Imports\ProduitsImport;
use App\Exports\ProduitsExport;
use App\Imports\ClientsImport;
use App\Exports\ClientsExport;
use App\Exports\FournisseursExport;
use App\Exports\FournisseursImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use App\Models\Image;


use App\Invoice;
use App\Models\Agence;
use App\Models\CategorieClient;
use App\Models\CategorieEmballage;
use App\Models\Client;
use App\Models\Emballage;

class ExcelController extends Controller
{

    public function chargementFichier(Request $request)
    {
        $file = $request->file('file');
        $importClass = $request->input('importClass');

        // dd($importClass);

        if ($file && $importClass) {
            try {
                $importInstance = app()->make("App\\Imports\\{$importClass}");
                $data = Excel::toArray($importInstance, $file);

                return response()->json(['data' => $data[0], 'message' => 'Fichier chargé avec succès'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erreur lors de l\'importation du fichier : ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'Fichier ou classe d\'importation manquants'], 400);
    }


    public function importProduits(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');
        // dd($uploadedData);

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                $k += 1;
            } else {
                // dd($row);
                $RechercheProduit = Produit::where('Reference', $row[1])
                    ->exists();
                if ($RechercheProduit) {
                    // echo $row[2] . 'existe déjà';
                    return redirect()->back()->with('error', 'Produit ' . $row[1] . ' existe déjà.');
                } else {
                    $user = auth()->user();

                    $categorie_existe = CategorieProduit::where('Libelle', '=', $row[3])->first();
                    $unite_comptage = UniteComptage::where('Libelle', '=', $row[4])->first();
                    $emballage_existe = null; // Initialisation de la variable

                    if (!empty($row[6])) {
                        $emballage_existe = Emballage::where('Nom_emballage', '=', $row[5])->first();
                    }

                    if (!$categorie_existe) {
                        // dd('categorie');
                        return redirect()->back()->with('error', 'La categorie ' . $row[3] . ' n\'existe pas. Veuillez créer.');
                    }

                    if (!$unite_comptage) {
                        // dd('unite_comptage');
                        return redirect()->back()->with('error', 'L\'unité de comptage ' . $row[4] . ' n\'existe pas. Veuillez créer.');
                    }

                    if($row[6] !== null){

                        if (!$emballage_existe) {

                            // dd('emballage_existe');
                            return redirect()->back()->with('error', 'L\'emballage ' . $row[5] . ' n\'existe pas. Veuillez créer.');
                        }
                    }

                    // $categorie = CategorieProduit::firstOrCreate(
                    //     ['Libelle' => $row[3]],
                    //     ['Enregistrer_par' => $user->id]
                    // );

                    // $uniteDeComptage = UniteComptage::firstOrCreate(
                    //     ['Libelle' => $row[4]],
                    //     ['Enregistrer_par' => $user->id]
                    // );

                    // $emballage = Emballage::firstOrCreate(
                    //     ['Nom_emballage' => $row[5]],
                    //     ['Enregistrer_par' => $user->id]
                    // );

                    $produit = new Produit;
                    $produit->Type = strtoupper($row[0]);
                    $produit->Reference = $row[1];
                    $produit->Designation = $row[2];
                    $produit->Id_Categorie = $categorie_existe->id;
                    $produit->Id_Unite_Comptage = $unite_comptage->id;
                    if (!empty($row[5])) {
                        $emballage_existe = Emballage::where('Nom_emballage', '=', $row[5])->first();
                        $produit->Emballage_id = $emballage_existe ? $emballage_existe->id : null;
                        $produit->type_emballage = $row[6];
                    } else {
                        // Assignation de null si $row[6] est vide
                        $produit->Emballage_id = null;
                        $produit->type_emballage = null;
                    }

                    $produit->Statut = 'ACTIF';
                    $produit->Enregistrer_par = $user->id;
                    $produit->save();

                    $produit_id = $produit->id;

                    //Prérenplissage de la table de gestion des prix
                    $agences = Agence::where('EnActivite', 1)->get();
                    $categorie_clients = CategorieClient::where('Statut', 1)->get();
                    foreach ($agences as $agence) {
                        foreach ($categorie_clients as $categorie_client) {
                            $insert_prix_produit = new PrixVenteProduit;
                            $insert_prix_produit->produit_id = $produit_id;
                            $insert_prix_produit->categorie_client_id = $categorie_client->id;
                            $insert_prix_produit->date_enregistrement = now();
                            $insert_prix_produit->date_variation_prix = now();
                            $insert_prix_produit->prix = 0;
                            $insert_prix_produit->user_id = auth()->user()->id;
                            $insert_prix_produit->agence_id = $agence->id;
                            $insert_prix_produit->save();
                        }
                    }

                    if (strtoupper($row[0]) == 'PRESTATION' || strtoupper($row[0]) == 'TAXE_SIMPLE') {
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
                        'description' => 'a créé un nouveau produit de référence ' . $row[1],
                    ]);
                }
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Produits importés avec succès!');
        } else {
            return to_route('page.produit.produit')->with('success', 'Les produits ont bien été importés');
        }
    }




    // Méthode pour demander à l'utilisateur de confirmer l'importation
    private function confirmImport()
    {
        // JavaScript pour afficher une boîte de dialogue modale de confirmation
        return <<<SCRIPT
        <script>
            var confirmation = confirm("Des catégories de produits ou des unités de comptage manquent. Voulez-vous continuer l'importation des produits ?");
            if (confirmation) {
                window.confirmImport = true;
            } else {
                window.confirmImport = false;
            }
        </script>
        SCRIPT;
    }


    public function exportProduits()
    {
        $this->authorize('consulter-liste-produits');
        // Récupérer les données des produits en fonction des besoins du client
        $produits = DB::table('produits')->select(
            'produits.Type',
            'produits.Reference',
            'produits.Designation',
            'categorie_produits.Libelle as Categorie',
            'unite_comptages.Libelle as Unite'
        )
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
            ->get();

        // Générer un nom de fichier basé sur la date d'exportation
        $fileName = 'export_produits_' . now()->format('Y-m-d_H-i-s') . '.xlsx';



        // Exporter les données avec le nom de fichier personnalisé
        return Excel::download(new ProduitsExport($produits), $fileName);
    }

    public function importAgences(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheAgence = Agence::where('NomAgence', $row[0])
                ->exists();

            if ($RechercheAgence) {
                return redirect()->back()->with('error', 'L\' Agence ' . $row[0] . ' existe déjà.');
            } else {

                Agence::create([
                    'NomAgence' => $row[0],
                    'titre_signataire_facture' => $row[1],
                    'nom_signataire' => $row[2],
                    'create_user_id' => auth()->user()->id,
                ]);
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les agences ont été importés avec succès!');
        } else {
            return to_route('agences')->with('success', 'Les agences ont été importés avec succès');
        }
    }
    public function exportAgences() {}
    public function importMagasins(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheAgence = Agence::where('NomAgence', $row[1])
                ->first();

            if (!$RechercheAgence) {
                return redirect()->back()->with('error', 'L\' Agence ' . $row[1] . ' n\'existe pas. Veuillez créer.');
            } else {
                $RechercheMagasin = Magasin::where('NomMagasin', $row[0])
                    ->exists();

                // dd( $RechercheAgence);
                if ($RechercheMagasin) {
                    return redirect()->back()->with('error', 'Le magasin ' . $row[0] . ' existe déjà.');
                } else {
                    Magasin::create([
                        'NomMagasin' => $row[0],
                        'agence_id' => $RechercheAgence->id,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les magasins ont été importés avec succès!');
        } else {
            return to_route('magasin')->with('success', 'Les magasins ont été importés avec succès');
        }
    }

    public function importUniteComptages(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheUniteComptage = UniteComptage::where('Code', $row[0])->where('Libelle', $row[1])->exists();

            if ($RechercheUniteComptage) {
                return redirect()->back()->with('error', 'L\'unité de comptage ' . $row[0] . '( ' . $row[1] . ' )' . ' existe déjà.');
            } else {
                UniteComptage::create([
                    'Code' => $row[0],
                    'Libelle' => $row[1],
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les magasins ont été importés avec succès!');
        } else {
            return to_route('page.produit.unite_comptage')->with('success', 'Les magasins ont été importés avec succès');
        }
    }

    public function importCategorieProduits(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheCategorieProduit = CategorieProduit::where('Libelle', $row[0])->exists();

            if ($RechercheCategorieProduit) {
                return redirect()->back()->with('error', 'La Catégorie produit ' . $row[0] . ' existe déjà.');
            } else {
                CategorieProduit::create([
                    'Libelle' => $row[0],
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les categories produits ont été importés avec succès!');
        } else {
            return to_route('page.produit.categorie')->with('success', 'Les categorie produits ont été importés avec succès');
        }
    }


    public function importCategorieEmballages(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheCategorieProduit = CategorieEmballage::where('Libelle', $row[0])->exists();

            if ($RechercheCategorieProduit) {
                return redirect()->back()->with('error', 'La Catégorie emballage ' . $row[0] . ' existe déjà.');
            } else {

                $categorie_emballage = new CategorieEmballage();
                $categorie_emballage->Libelle = $row[0];
                $categorie_emballage->Statut_cat_emballage = 1;
                $categorie_emballage->Enregistrer_par = auth()->user()->id;
                $categorie_emballage->save();
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les categories emballages ont été importés avec succès!');
        } else {
            return to_route('categorie_emballage')->with('success', 'Les categorie emballages ont été importés avec succès');
        }
    }


    public function importEmballages(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheCategorieProduit = Emballage::where('Reference', $row[0])
            ->where('Nom_emballage', '=', $row[1])
            ->exists();

            $reference_existe = Emballage::where('Reference', $row[0])
            ->exists();

            if($reference_existe){
                return redirect()->back()->with('error', 'La reference de l\'emballage ' . $row[0] . ' existe déjà. Veuillez le mofidier.');
            }

            if ($RechercheCategorieProduit) {
                return redirect()->back()->with('error', 'L\' emballage ' . $row[0] . ' existe déjà.');
            } else {

                $categorie_emballage_existe = CategorieEmballage::where('Libelle', '=', $row[2])->first();

                if(!$categorie_emballage_existe){
                    return redirect()->back()->with('error', 'La catégorie emballage ' . $row[2] . ' n\'existe pas. Veuillez créer.');
                }

                $categorie_emballage = new Emballage();
                $categorie_emballage->Reference = $row[0];
                $categorie_emballage->Nom_emballage = $row[1];
                $categorie_emballage->Categorie_emballage_id = $categorie_emballage_existe->id;
                $categorie_emballage->Statut_emballage = 1;
                $categorie_emballage->Enregistrer_par = auth()->user()->id;
                $categorie_emballage->save();
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les emballages ont été importés avec succès!');
        } else {
            return to_route('emballage')->with('success', 'Les emballages ont été importés avec succès');
        }
    }


    public function importCategorieClients(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheCategorieClient = CategorieProduit::where('Libelle', $row[0])->exists();

            if ($RechercheCategorieClient) {
                return redirect()->back()->with('error', 'La Caatégorie produit ' . $row[0] . ' existe déjà.');
            } else {
                CategorieProduit::create([
                    'Libelle' => $row[0],
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les categories produits ont été importés avec succès!');
        } else {
            return to_route('categorieclient')->with('success', 'Les categorie produits ont été importés avec succès');
        }
    }

    public function exportMagasins() {}
    public function importFournisseurs(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheFournisseur = DB::table('fournisseurs')
                ->where('DenominationSociale', $row[0])
                ->exists();

            if ($RechercheFournisseur) {
            } else {

                Fournisseur::create([
                    'DenominationSociale' => $row[0],
                    'AdresseFournisseur' => $row[1],
                    // 'TelephoneFixe' => $row[4],
                    'TelephoneMobile' => $row[4],
                    'AdresseMail' => $row[5],
                    'Pays' => $row[2],
                    'NumeroIfu' => $row[3],
                    'Statut_fournisseur' => '1',
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les fournisseurs ont été importés avec succès!');
        } else {
            return to_route('agence')->with('success', 'Le(s) agence(s) ont été importés avec succès');
        }
    }

    public function exportFournisseurs()
    {
        try {
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
            $data = Fournisseur::all();
            $export = new FournisseursExport($data, $texteEntetePied);

            $fileName = 'fournisseur_export' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function importCatClients() {}
    public function exportCatClients() {}
    public function importClients(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        // dd($data);
        $k = 1;
        foreach ($data as $row) {

            if ($k == 1) {
                // Sauter la première ligne (les en-têtes)
                $k += 1;
                continue;
            }

            $RechercheClient = DB::table('clients')
                ->where(function ($query) use ($row) {
                    $query->where('Code_client', $row[0])
                        ->orWhere('Denomination_sociale', $row[1]);
                })
                ->exists();

            if ($RechercheClient) {
            } else {

                $categorieClient = CategorieClient::firstOrCreate(['Libelle' => $row[3]]);

                // dd($categorieClient);
                $client = new Client();
                $client->Denomination_sociale = $row[1];
                $client->Adresse_client = $row[4];
                // $client->Telephone_fixe = $row[5];
                $client->Telephone_mobile = $row[5];
                $client->Adresse_mail = $row[6];
                $client->Pays = $row[2];
                $client->Numero_ifu = $row[7];
                $client->Code_client = $row[0];
                $client->Categorie_client_id = $categorieClient->id;
                $client->Statut_client = '1';
                $client->user_id = auth()->user()->id;
                $client->save();
            }
        }
        if ($nobougepage) {
            return redirect()->back()->with('success', 'Les clients ont été importés avec succès!');
        } else {
            return to_route('client')->with('success', 'Les clients ont été importés avec succès');
        }
    }
    public function exportClients()
    {

        // Récupérer les données des clients en fonction des besoins du client
        $clients = DB::table('clients')->select(
            'clients.Denomination_sociale',
            'clients.Adresse_client',
            'clients.Telephone_fixe',
            'clients.Telephone_mobile',
            'clients.Adresse_mail',
            'clients.Pays',
            'clients.Numero_ifu',
            'clients.Code_client',
            'categorie_clients.Libelle as Categorie'
        )
            ->join('categorie_clients', 'categorie_clients.id', '=', 'clients.Categorie_client_id')
            ->get();

        // Générer un nom de fichier basé sur la date d'exportation
        $fileName = 'export_clients_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Exporter les données avec le nom de fichier personnalisé
        return Excel::download(new ClientsExport($clients), $fileName);
    }

    public function importPrixProduits(Request $request)
    {
        $nobougepage = $request->input('nobougepage', false);

        $uploadedData = $request->input('uploadedData');

        $data = json_decode($uploadedData, true);

        //dd($data);
        $k = 1;
        foreach ($data as $row) {
            if ($k == 1) {
                $k += 1;
            } else {
                $categorie_produit = CategorieProduit::where('Libelle', $row[1])->first();
                // dd($categorie_produit);
                $produit = Produit::where('Designation', $row[0])->where('Id_Categorie', $categorie_produit->id)->first();
                $categorie_client = CategorieClient::firstOrCreate(['Libelle' => $row[2]]);
                $agence = Agence::where('NomAgence', $row[3])->first(); // Reste à valider l'agence
                $produitExists = 0;
                if ($produit->exists()) {
                    $prix_produit_vente = PrixVenteProduit::where('produit_id', $produit->id)
                        ->where('categorie_client_id', $categorie_client->id)
                        ->where('agence_id', $agence->id);

                    if ($prix_produit_vente->exists()) {

                        $prix_produit_vente->date_variation_prix = now();
                        // $prix_produit_vente->prix = $row[4];
                        $prix_produit_vente->user_id = auth()->user()->id;
                        $prix_produit_vente->update(['prix' => $row[4]]);
                    } else {
                        $insert_prix_produit = new PrixVenteProduit;
                        $insert_prix_produit->produit_id = $produit->id;
                        $insert_prix_produit->categorie_client_id = $categorie_client->id;
                        // $insert_prix_produit->categorie_produit_id = $categorie_produit->id;
                        $insert_prix_produit->agence_id = $agence->id;
                        $insert_prix_produit->date_enregistrement = now();
                        $insert_prix_produit->date_variation_prix = now();
                        $insert_prix_produit->prix = $row[4];
                        $insert_prix_produit->user_id = auth()->user()->id;
                        $insert_prix_produit->save();
                    }

                    $historique_prix_produit = new HistoriquePrixProduit;
                    $historique_prix_produit->produit_id = $produit->id;
                    $historique_prix_produit->categorie_client_id = $categorie_client->id;
                    // $insert_prix_produit->categorie_produit_id = $categorie_produit->id;
                    $historique_prix_produit->agence_id = $agence->id;
                    $historique_prix_produit->date_changement_prix = now();
                    $historique_prix_produit->prix = $row[4];
                    $historique_prix_produit->user_id = auth()->user()->id;
                    $historique_prix_produit->save();
                } else {
                    $produitExists += 1;
                }
            }
        }
        if ($nobougepage) {
            if ($produitExists == 0) {
                return redirect()->back()->with('success', 'Tous les prix sont importés avec succès!');
            } else {
                return redirect()->back()->with('success', 'Certains prix sont ignorés car les produits ne sont pas trouvés ou sont mal écrits!');
            }
        } else {
            if ($produitExists == 0) {
                return to_route('gestion_des_prix')->with('success', 'Tous les prix sont importés avec succès!');
            } else {
                return to_route('gestion_des_prix')->with('success', 'Certains prix sont ignorés car les produits ne sont pas trouvés ou sont mal écrits!');
            }
        }
    }
}
