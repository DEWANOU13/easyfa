<?php

namespace App\Http\Controllers\Produit;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\UniteComptage;
use App\Exports\ProduitsExport;
use App\Models\CategorieClient;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Imports\PrixProduitsImport2;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HistoriquePrixProduit;

class ProduitController extends Controller
{
    // retourne la vue de produit
    public function  index()
    {
        $this->authorize('consulter-liste-produits');

        try {
            $produits = DB::table('produits')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->join('users', 'produits.Enregistrer_par', '=', 'users.id')
                ->leftJoin('users as modifier_par', 'unite_comptages.Modifier_par', '=', 'modifier_par.id')
                ->leftJoin('emballages', 'produits.Emballage_id', '=', 'emballages.id')
                ->select('produits.*', 'categorie_produits.Libelle as Libelle_categorie_produit','emballages.Nom_emballage as emballage', 'unite_comptages.Libelle', 'users.name as name', 'modifier_par.name as _name')
                ->orderBy('produits.id', 'desc')
                ->get();

            // dd($produits);
            return view('page.produit.produit.produit', ['produits' => $produits]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function imprimerProduit(Request $request)
    {
        $this->authorize('consulter-liste-produits');
        try {
            $reponse = $request->input('reponse');

            if ($reponse === 'ACTIF') {

                $get_categorie_produit = DB::table('categorie_produits')
                    ->join('produits', 'categorie_produits.id', 'produits.Id_Categorie')
                    ->where('produits.Statut', 'ACTIF')
                    ->select(
                        'produits.Id_Categorie',
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                    )
                    ->groupBy('produits.Id_Categorie')
                    ->get();

                $query = DB::table('produits')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->where('produits.Statut', 'ACTIF')
                    ->select('produits.*', 'categorie_produits.Libelle', 'unite_comptages.Libelle as Libelle_Comptage');

                $get_produit = $query->get();

                //  dd($get_produit, $get_categorie_produit);
                if (count($get_produit) <= 0) {
                    return to_route('page.produit.produit.produit')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($reponse === 'INACTIF') {

                $get_categorie_produit = DB::table('categorie_produits')
                    ->join('produits', 'categorie_produits.id', 'produits.Id_Categorie')
                    ->where('produits.Statut', 'INACTIF')
                    ->select(
                        'produits.Id_Categorie',
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                    )
                    ->groupBy('produits.Id_Categorie')
                    ->get();

                $query = DB::table('produits')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->where('produits.Statut', 'INACTIF')
                    ->select('produits.*', 'categorie_produits.Libelle', 'unite_comptages.Libelle as Libelle_Comptage');

                $get_produit = $query->get();

                //  dd($get_produit, $get_categorie_produit);
                if (count($get_produit) <= 0) {
                    return to_route('page.produit.produit')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($reponse === 'PRESTATION') {

                $get_categorie_produit = DB::table('categorie_produits')
                    ->join('produits', 'categorie_produits.id', 'produits.Id_Categorie')
                    ->where('produits.Type', 'PRESTATION')
                    ->select(
                        'produits.Id_Categorie',
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                    )
                    ->groupBy('produits.Id_Categorie')
                    ->get();

                $query = DB::table('produits')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->where('produits.Type', 'PRESTATION')
                    ->select('produits.*', 'categorie_produits.Libelle', 'unite_comptages.Libelle as Libelle_Comptage');

                $get_produit = $query->get();

                //  dd($get_produit, $get_categorie_produit);
                if (count($get_produit) <= 0) {
                    return to_route('page.produit.produit')->with('error', 'Aucunes données trouvées!');
                }
            }

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();


            // $prefixe = 'PROD';
            // $date_et_heure = date('Ymd_His');
            // $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            // $pdf = Pdf::loadView('page.produit.produit.imprimer.imprimer', $data);
            // return $pdf->download($nom_pdf);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $htmlContent = view('page.produit.produit.imprimer.imprimer', [
                'imageEntetePied' => $imageEntetePied,
                'getProduit' => $get_produit,
                'getCategorieProduit' => $get_categorie_produit,
                'reponse' => $reponse,
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

            // return view('page.produit.produit.imprimer.imprimer', $data);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export()
    {

        // $data = UniteComptage::all();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        // $produits = DB::table('produits')
        //     ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
        //     ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
        //     ->join('emballages', 'produits.Emballage_id', '=', 'emballages.id')
        //     ->join('categorie_emballages', 'produits.type_emballage', '=', 'categorie_emballages.id')
        //     ->select('produits.*', 'categorie_produits.Libelle as Libelle_Categorie', 'unite_comptages.Libelle as Libelle_Unite_Comptage', 'emballages.Nom_emballage', 'categorie_emballages.Libelle as categorieEmballage')
        //     ->orderBy('produits.Reference', 'asc')
        //     ->get();

        $produits = Produit::get();

        $data = [
            'produits' => $produits,
            'texteEntetePied' => $texteEntetePied,
        ];

        $prefixe = 'produit_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new ProduitsExport($data), $nom_excel);
    }

    public function importFixationPrixx(Request $request)
    {
        $request->validate([
            'fichier' => 'required|mimes:xlsx,xls'
        ]);
        DB::beginTransaction();
        try {
            $fichier = $request->file('fichier');
            $donnees = Excel::toArray(new PrixProduitsImport2, $fichier)[0];

            // Extraire les en-têtes
            $entetes = $donnees[0];
            $colonnes_categories = array_slice($entetes, 3); // Les catégories commencent à partir de la 4ème colonne

            // Récupérer toutes les catégories de la base de données
            $categories = CategorieClient::pluck('id', 'Libelle');

            //dd($colonnes_categories);

            // Vérifier si toutes les catégories de l'Excel existent dans la base de données
            $categories_manquantes = collect($colonnes_categories)->filter(function($categorie) use ($categories) {
                return !isset($categories[$categorie]);
            });

            if ($categories_manquantes->isNotEmpty()) {
                throw new \Exception('Les catégories suivantes sont introuvables : ' . $categories_manquantes->implode(', '));
            }

            $resultats = [
                'total_lignes' => count($donnees) - 1,
                'produits_traites' => 0,
                'produits_ignores' => 0,
                'prix_historises' => 0
            ];

            // Ignorer la première ligne (en-têtes)
            array_shift($donnees);

            foreach ($donnees as $ligne) {
                $designation = trim($ligne[0]);
                $agence_nom = trim($ligne[1]);

                // Rechercher le produit
                $produit = Produit::where('Designation', $designation)->first();
                if (!$produit) {
                    $resultats['produits_ignores']++;
                    continue;
                }

                // Rechercher l'agence
                $agence = Agence::where('NomAgence', $agence_nom)->first();

                // Créer les enregistrements pour chaque catégorie
                foreach ($colonnes_categories as $index => $categorie_nom) {
                    $prix = $ligne[$index + 3]; // +3 car les prix commencent à la 4ème colonne

                    if ($prix !== null && $prix !== '') {
                        HistoriquePrixProduit::create([
                            'produit_id' => $produit->id,
                            'categorie_client_id' => $categories[$categorie_nom],
                            'agence_id' => $agence ? $agence->id : null,
                            'date_changement_prix' => Carbon::now(),
                            'prix' => $prix,
                            'user_id' => Auth::id(),
                            'Modifier_par' => Auth::user()->name ?? 'Système'
                        ]);
                        $resultats['prix_historises']++;
                    }
                }
                $resultats['produits_traites']++;
            }

            DB::commit();

            $message_succes = sprintf(
                'Importation terminée. Total lignes : %d, Produits traités : %d, Produits ignorés : %d, Prix historisés : %d',
                $resultats['total_lignes'],
                $resultats['produits_traites'],
                $resultats['produits_ignores'],
                $resultats['prix_historises']
            );

            return redirect()->back()->with('success', $message_succes);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
