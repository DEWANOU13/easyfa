<?php

namespace App\Http\Controllers\emballage;


use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Emballage;
use App\Models\AgenceUser;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use App\Exports\ListeStockExport;
use App\Models\CategorieEmballage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HistoriqueStockExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Exports\ListeStockEmballageExport;
use App\Exports\HistoriqueStockExportEmballageExport;
use App\Imports\StockImportEmballage;
use Carbon\Carbon;

class StockEmballageController extends Controller
{
    public function  index(Request $request)
    {
        accessEmballage();
        $this->authorize('voir-inventaire-stock-emballage');
        try {
            $site_id = session()->get('site_id');

            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $categories = CategorieEmballage::orderBy('id', 'desc')->get();
            $agences = Agence::orderBy('id', 'desc')->whereIn('id', $agenceIds)->get();
            $produits = Emballage::orderBy('id', 'desc')->where('Statut_emballage', '=', '1')->get();
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->select('magasins.*', 'agences.NomAgence')
                ->get();


            $magasin_ids = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->orderBy('magasins.created_at', 'desc')
                ->pluck('magasins.id');

            $query = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                // ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                // ->whereIn('agences.id', $magasin_ids)
                // ->where('agences.id', $site_id)
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'agences.NomAgence')
                ->orderBy('stock_emballages.id', 'desc');

                $query_agence = Agence::find($site_id);

                if ($query_agence->NomAgence === 'Siège') {
                    $stock_emballages = $query->get();
                } else {
                    $stock_emballages = $query->where('agences.id', $site_id)->get();
                }


            $historiques_stock = DB::table('stock_histories')
                ->join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
                ->join('agences', 'agences.id', '=', 'stock_histories.agence_id')
                ->join('magasins', 'magasins.id', '=', 'stock_histories.Id_Magasin')
                ->whereIn('magasins.id', $magasin_ids)
                ->select('stock_histories.Date', 'produits.Designation', 'agences.NomAgence', 'magasins.NomMagasin', 'stock_histories.Motif', 'stock_histories.Justificatif', 'stock_histories.Quantite', 'stock_histories.operation')
                ->get();

            $import_stock_emballages = DB::table('import_stock_emballages')
                ->join('users', 'import_stock_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stock_emballages.Id_Agence', '=', 'agences.id')
                ->select('import_stock_emballages.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stock_emballages.id', 'desc')
                ->get();

            return view('page.emballage.stock_emballage.stock', [
                'stock_produits' => $stock_emballages,
                'historiques_stock' => $historiques_stock,
                'import_stock_emballages' => $import_stock_emballages,
                'solde_initial' => '',
                'date_veille_debut' => '',
                'agence' => $agences,
                'categorie' => $categories,
                'produit' => $produits,
                'magasin' => $magasins,
                'dp' => '',
                'df' => '',
                'ag' => Agence::find(0),
                'mg' => Magasin::find(0),
                'cat' => CategorieProduit::find(0),
                'prod' => Produit::find(0),
            ])->with('tabToShow', false);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
    public function filterStockEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('voir-inventaire-stock-emballage');
        try {

            $site_id = session()->get('site_id');
            // dd($request);
            // try{
            $agence = $request->input('agence');
            $magasin = $request->input('magasin');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');

            $query = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle as Libelle_Categorie', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('agences.id', $site_id);

            if ($agence) {
                $query->where('agences.id', $agence);
            }
            if ($magasin) {
                $query->where('magasins.id', $magasin);
            }
            if ($categorie && $categorie !== 'Toutes') {
                $query->where('categorie_emballages.id', $categorie);
            }
            if ($produit && $produit !== 'Tous') {
                $query->where('emballages.id', $produit);
            }

            // Exécuter la requête
            $stocke_produits = $query->get();



            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $agences = Agence::orderBy('id', 'desc')->whereIn('id', $agenceIds)->get();
            $categories = CategorieEmballage::orderBy('id', 'desc')->get();
            $produits = Emballage::orderBy('id', 'desc')->where('Statut_emballage', '1')->get();

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->select('magasins.*', 'agences.NomAgence')
                ->get();



            $import_stock_emballages = DB::table('import_stock_emballages')
                ->join('users', 'import_stock_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stock_emballages.Id_Agence', '=', 'agences.id')
                ->select('import_stock_emballages.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stock_emballages.id', 'desc')
                ->get();

            return view('page.emballage.stock_emballage.stock', [
                'stock_produits' => $stocke_produits,
                'import_stock_emballages' => $import_stock_emballages,
                'produit' => $produits,
                'magasin' => $magasins,
                'agence' => $agences,
                'categorie' => $categories,
                'dp' => '',
                'df' => '',
                'dp' => '',
                'df' => '',
                'solde_initial' => '',
                'date_veille_debut' => '',
                'ag' => $agence ? Agence::find($agence) : '',
                'mg' => $magasin ? Magasin::find($magasin) : '',
                'cat' => $categorie !== 'Toutes' ? CategorieEmballage::find($categorie) : '',
                'prod' => $produit !== 'Tous' ? Emballage::find($produit) : '',
            ])->with('tabToShow', false, '');
        } catch (\Exception $e) {
            return redirect()->route('page.stock.stock')->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        };
    }

    public function filter_stock_emballage_historique(Request $request)
    {
        accessEmballage();
        $this->authorize('voir-inventaire-stock-emballage');
        try {
            $site_id = session()->get('site_id');

            $date_fin_periode = $request->input('date_fin_periode');
            $date_debut_periode = $request->input('date_debut_periode');
            $agence = $request->input('agence');
            $magasin = $request->input('magasin');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');

            $dp = $date_debut_periode;
            $df = $date_fin_periode;
            $ag = $agence;
            $mg = $magasin;
            $cat = $categorie;
            $prod = $produit;

            // dd('$magasin', $magasin, '$agence', $agence);


            // Validation des dates
            if ($date_debut_periode && $date_fin_periode) {

                $date_debut_periode = Carbon::createFromFormat('Y-m-d\TH:i',  $dp)->format('Y-m-d 00:00:00');
                $date_fin_periode = Carbon::createFromFormat('Y-m-d\TH:i',  $df)->format('Y-m-d 23:59:59');
                $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dp . ' -1 day')); // Date de la veille

            } else {
                return redirect()->route('stock_emballage')->with('error', 'Veuillez sélectionner les dates de début et de fin.');
            }

            if (!$produit || !$magasin) {
                return redirect()->route('stock_emballage')->with('error', 'Sélectionnez ou moins un produit et un magasin.');
            }

            $query_historiques_stock_filter = DB::table('stock_emballage_histories')
                ->join('emballages', 'emballages.id', '=', 'stock_emballage_histories.Id_Emballage')
                ->join('agences', 'agences.id', '=', 'stock_emballage_histories.agence_id')
                ->join('magasins', 'magasins.id', '=', 'stock_emballage_histories.Id_Magasin');


            if ($magasin) {
                $query_historiques_stock_filter->where('stock_emballage_histories.Id_Magasin', $magasin);
            }
            if ($produit) {
                $query_historiques_stock_filter->where('stock_emballage_histories.Id_Emballage', $produit);
            }
            if ($date_debut_periode && $date_fin_periode) {
                $query_historiques_stock_filter->whereBetween('stock_emballage_histories.Date', [$date_debut_periode, $date_fin_periode]);
            }

            $query_historiques_stock_filter->select(
                'stock_emballage_histories.Date',
                'emballages.Nom_emballage as Designation',
                'agences.NomAgence',
                'magasins.NomMagasin',
                'stock_emballage_histories.Motif',
                'stock_emballage_histories.Justificatif',
                'stock_emballage_histories.Quantite',
                'stock_emballage_histories.operation'
            );

            $historiques_stock = $query_historiques_stock_filter->get();

            // dd($historiques_stock);
            $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
            $categories = CategorieEmballage::orderBy('id', 'desc')->get();
            $agences = Agence::orderBy('id', 'desc')->whereIn('id', $agenceIds)->get();
            $produits = Emballage::orderBy('id', 'desc')->where('Statut_emballage', '1')->get();
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->select('magasins.*', 'agences.NomAgence')
                ->get();

            $magasin_ids = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->where('agences.id', '=', $site_id)
                ->orderBy('magasins.created_at', 'desc')
                ->pluck('magasins.id');

            $stocke_produits =  DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                // ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                // ->whereIn('agences.id', $magasin_ids)
                ->where('agences.id', $site_id)
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'agences.NomAgence')
                ->orderBy('stock_emballages.id', 'desc')
                ->get();

            $import_stock_emballages = DB::table('import_stock_emballages')
                ->join('users', 'import_stock_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'import_stock_emballages.Id_Agence', '=', 'agences.id')
                ->select('import_stock_emballages.*', 'users.name', 'agences.NomAgence')
                ->where('agences.id', $site_id)
                ->orderBy('import_stock_emballages.id', 'desc')
                ->get();



            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit', $produit)
                ->where('Id_Magasin', $magasin)
                ->where('Date', '<=', $date_veille_debut)
                ->orderBy('Date', 'asc') // On parcourt les opérations dans l'ordre chronologique
                ->get();

            // Initialisation du solde
            $solde_initial = 0;

            // Parcourir les opérations pour calculer le solde
            foreach ($operations_anterieures as $operation) {
                if ($operation->operation === 'IMPORTATION_STOCK') {
                    // Réinitialiser le solde à la quantité de l'importation de stock
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'INVENTAIRE') {
                    // Réinitialiser le solde à la quantité spécifiée par l'inventaire
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'ENTREE') {
                    // Ajouter la quantité pour les entrées
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->operation === 'SORTIE') {
                    // Soustraire la quantité pour les sorties
                    $solde_initial -= $operation->Quantite;
                }
            }

            // S'assurer qu'il y a un solde, sinon on initialise à 0
            if (is_null($solde_initial)) {
                $solde_initial = 0;
            }

            // dd($stocke_produits);

            return view('page.emballage.stock_emballage.stock', [
                'historiques_stock' => $historiques_stock,
                'agence' => $agences,
                'categorie' => $categories,
                'produit' => $produits,
                'solde_initial' => $solde_initial,
                'date_veille_debut' => $date_veille_debut,
                'magasin' => $magasins,
                'dp' => $dp,
                'df' => $df,
                'ag' => Agence::find($ag),
                'mg' => Magasin::find($mg),
                'cat' => CategorieEmballage::find($cat),
                'prod' => Emballage::find($prod),
                'stock_produits' => $stocke_produits,
                'import_stock_emballages' => $import_stock_emballages,
            ])->with('tabToShow', 'historique_stock');
        } catch (\Exception $e) {
            return redirect()->route('page.stock_emballage.stock')->with('error', 'Une erreur est survenue . ');
        }
    }

    public function imprimerStockEmballage(Request $request)
    {
        accessEmballage();
        $this->authorize('exporter-pdf-inventaire-stock-emballage');
        $reponse = $request->input('reponse');
        //    dd($reponse);
        try {
            $get_stocks = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')

                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'magasins.NomMagasin')
                ->get();

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            if ($reponse === 'imprimer') {
                // reference the Dompdf namespace
                $options = new Options();
                $options->set('chroot', realpath(''));
                $dompdf = new Dompdf($options);

                $htmlContent = view('page.emballage.stock_emballage.imprimer.imprimer', [
                    'imageEntetePied' => $imageEntetePied,
                    'getStock' => $get_stocks,
                ])->render();


                $dompdf->loadHtml($htmlContent);
                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Afficher le PDF dans le navigateur
                $prefixe = 'stock_emballage';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';

                // Output the generated PDF to Browser
                $dompdf->stream($nom_pdf, array("Attachment" => false));
            }

            if ($reponse === 'exporter') {

                $data = [
                    'texteEntetePied' => $texteEntetePied,
                    'getStock' => $get_stocks,
                    'reponse' => $reponse

                ];

                // dd($htmlContent);
                // dd('faire une exportation');

                $prefixe = 'stock_emballage_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new ListeStockEmballageExport($data), $nom_excel);
            }

            if ($reponse === 'formatImportation') {

                $data = [
                    'texteEntetePied' => $texteEntetePied,
                    'getStock' => $get_stocks,
                    'reponse' => $reponse
                ];

                // dd($htmlContent);
                // dd('faire une exportation');

                $prefixe = 'modele_stock_emballages_test';
                $nom_excel = $prefixe . '.xlsx';

                return Excel::download(new ListeStockEmballageExport($data), $nom_excel);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function imprimerStockEmballageHistorique(Request $request)
    {
        accessEmballage();
        $this->authorize('exporter-pdf-inventaire-stock-emballage');
        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $htmlContent = view('page.emballage.stock_emballage.imprimer.imprimer-historique', [
            'imageEntetePied' => $imageEntetePied,
            'data' => $all_data,
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
        $prefixe = 'Historique_stock_emballage_du';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);
    }

    public function exporterStockEmballageHistorique(Request $request)
    {
        accessEmballage();
        $this->authorize('exporter-excel-inventaire-stock-emballage');
        // Récupérer les données du formulaire
        $all_data = json_decode($request->input('data'), true);

        // dd($all_data);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();



        $prefixe = 'historique_stock_emballage_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new HistoriqueStockExportEmballageExport($all_data), $nom_excel);
    }

    public function import(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Lancer l'importation du fichier Excel
            Excel::import(new StockImportEmballage, $request->file('file'));

            return to_route('stock_emballage')->with('success', 'Importation réussie.');
        } catch (Exception $e) {
            // Attraper l'exception levée dans StockImport et afficher un message d'erreur
            return redirect()->back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }
    }

    public function getDetailsImportStockEmballage(Request $request)
    {
        // dd($request);

        $id = $request->input('id');

        $details_import_stocks = DB::table('detail_import_stock_emballages')
            ->join('emballages', 'detail_import_stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'detail_import_stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->select('detail_import_stock_emballages.*', 'emballages.Reference', 'emballages.Nom_Emballage as Designation', 'magasins.NomMagasin', 'emballages.Nom_emballage')
            ->where('detail_import_stock_emballages.Id_Import_Stock_Emballage', '=', $id)
            ->get();

        // dd($details_import_stocks );

        return view('page.produit.stock.stock', [
            'details_import_stocks' => $details_import_stocks,
            'agence' => [],
            'historiques_stock' => [],
            'import_stocks' => [],
            'solde_initial' => '',
            'date_veille_debut' => '',
            'categorie' => [],
            'produit' => [],
            'magasin' => [],
            'dp' => '',
            'df' => '',
            'ag' => '',
            'mg' => '',
            'cat' => '',
            'prod' => '',
            'stock_produits' => [],
        ])->with('tabToShow', 'historique_import_stock');

        // return redirect()->route('page.stock.stock')->with('tabToShow', 'historique_import_stock');
    }

    public function getProductsByCategoryEmballage($categoryId)
    {
        if ($categoryId === 'Toutes') {
            // dd('ici');
            // Récupérer tous les produits si "Toutes" est sélectionné
            $products = Emballage::all();
        } else {
            // dd('ici(----');
            // Filtrer les produits en fonction de la catégorie
            $products = Emballage::where('Categorie_emballage_id', $categoryId)->get();
        }

        return response()->json($products);
    }
}
