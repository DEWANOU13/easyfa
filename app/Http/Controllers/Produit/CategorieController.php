<?php

namespace App\Http\Controllers\Produit;

use App\Exports\CategorieProduit as ExportsCategorieProduit;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\CategorieProduit;
use App\Models\Image;
use App\Models\Produit;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CategorieController extends Controller
{

    // retourne la vue de categorie
    public function index()
    {
        $this->authorize('consulter-liste-categorie-produit');
        try {
            // Récupérer les catégories produits en ordre décroissant de création
            // $listeCategorieProduit = CategorieProduit::orderBy('created_at', 'desc')->get();

            $listeCategorieProduit = DB::table('categorie_produits')
                ->leftJoin('users', 'categorie_produits.Enregistrer_par', '=', 'users.id')
                ->leftJoin('users as _users', 'categorie_produits.Modifier_par', '=', '_users.id')
                ->select(
                    'categorie_produits.*',
                    'users.name as name',
                    '_users.name as _name'
                )
                ->get();


            // Passer la liste des catégories produits à la vue
            return view('page.produit.categorie.categorie', ['listeCategorieProduit' => $listeCategorieProduit]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    // retourne la vue d'ajout une nouvelle categorie
    public function  create()
    {
        $this->authorize('creer-categorie-produit');

        try {
            return view('page.produit.categorie.nouveau');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('creer-categorie-produit');

        try {
            // Définir les données à valider
            $data = $request->only(['NomCategorie']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'NomCategorie' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'NomCategorie.required' => "La catégorie produit est requise",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['NomCategorie'];

            $categorie_produit_exist = CategorieProduit::where('Libelle', '=', $libelle)->first();
            if ($categorie_produit_exist !== null) {
                return back()->with('error', 'La catégorie que vous essayez d\'ajouter existe déjà.');
            } else {
                $categorie_produit = new CategorieProduit();
                $categorie_produit->Libelle = $libelle;
                $categorie_produit->Enregistrer_par = auth()->user()->id;
                $categorie_produit->save();

                // Enregistrement de l'activité associée
                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a créé la categorie de produit  ' . $libelle,
                ]);

                return to_route('page.produit.categorie')->with('success', 'La categorie a bien été ajoutée');
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeProduit(Request $request)
    {
        $this->authorize('creer-categorie-produit');
        try {
            // Définir les données à valider
            $data = $request->only(['libelle']);

            // Définir les règles de validation
            $validatorRules = [
                'libelle' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'libelle.required' => "L'adresse email est requise",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('page.produit.categorie_nouveau')->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['libelle'];

            $categorie_produit_exist = CategorieProduit::where('Libelle', '=', $libelle)->first();
            if ($categorie_produit_exist !== null) {
                return to_route('page.nouveau.nouveau')->with('error', 'La catégorie que vous essayez d\'ajouter existe déjà.');
            } else {
                $categorie_produit = new CategorieProduit();
                $categorie_produit->Libelle = $libelle;
                $categorie_produit->Enregistrer_par = auth()->user()->id;
                $categorie_produit->save();


                // Enregistrement de l'activité associée
                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a créé la categorie de produit ' . $libelle,
                ]);

                return response()->json([
                    'success' => true,
                    'newCategoryId' => $categorie_produit->id,
                    'newCategoryName' => $categorie_produit->Libelle,
                ]);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('modifier-categorie-produit');
        try {
            $categorie = CategorieProduit::find($id);
            return view('page.produit.categorie.edit', ['categorie' => $categorie]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('modifier-categorie-produit');

        try {
            // Définir les données à valider
            $data = $request->only(['UpNomCategorie']);

            // dd($request);

            // Définir les règles de validation
            $validatorRules = [
                'UpNomCategorie' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'UpNomCategorie.required' => "L'adresse email est requise",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('page.produit.categorie_nouveau')->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $libelle = $data['UpNomCategorie'];

            $categorie = CategorieProduit::find($id);

            $categorie->Libelle = $libelle;
            $categorie->Modifier_par = auth()->user()->id;
            $categorie->update();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié la categorie de produit ' . $libelle,
            ]);
            return to_route('page.produit.categorie')->with('success', 'La categorie a bien été mofidiée avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export()
    {

        $categorie_produits = CategorieProduit::all();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $data = [
            'categorie_produits' => $categorie_produits,
            'texteEntetePied' => $texteEntetePied,
        ];

        $prefixe = 'categorie_produit_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new ExportsCategorieProduit($data), $nom_excel);
    }

    public function imprimer()
    {

        $data = CategorieProduit::all();
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $htmlContent = view('page.produit.categorie.imprimer', [
            'categorie_produits' => $data,
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
