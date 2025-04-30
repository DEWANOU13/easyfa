<?php

namespace App\Http\Controllers\emballage;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\StockEmballage;
use App\Models\Magasin;
use App\Models\Inventorier;
use Illuminate\Http\Request;
use App\Models\InventorierEmb;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PrefixeReference;
use App\Models\InventaireMagasin;
use App\Models\InventaireProduit;
use Illuminate\Support\Facades\DB;
use App\Models\InventaireEmballage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HistoriquePrixRevient;
use App\Exports\InventairePeriodeExport;
use Illuminate\Support\Facades\Validator;
use App\Models\InventaireEmballageMagasin;
use App\Exports\InventaireEmballagePeriodeExport;
use App\Models\Agence;
use App\Models\StockEmballageHistories;

class InventaireEmballageController extends Controller
{
    public function  index(Request $request)
    {
        $this->authorize('voir-liste-inventaire-emballage');
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->inventaire ?? '';
        if ($prefix == null) {
            return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
        }
        $annees = InventaireEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $site_id = session()->get('site_id');

        $query  = DB::table('inventaire_emballages')
            ->join('agences', 'inventaire_emballages.Id_Agence', '=', 'agences.id')
            ->join('users', 'inventaire_emballages.Id_Utilisateur', '=', 'users.id')
            ->select('inventaire_emballages.*', 'agences.NomAgence', 'users.name')
            ->whereMonth('inventaire_emballages.created_at', $currentMonth)
            ->whereYear('inventaire_emballages.created_at', $currentYear)
            // ->where('agences.id', '=', $site_id)
            ->orderBy('inventaire_emballages.id', 'desc');

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $inventaire_emballages = $query->get();
            } else {
                $inventaire_emballages = $query->where('agences.id', '=', $site_id)->get();
            }

        $inventoriers = DB::table('inventorier_embs')
            ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('inventaire_emballages', 'inventorier_embs.Id_Inventaire_Emballage', '=', 'inventaire_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('inventorier_embs.*', 'magasins.NomMagasin',  'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee')
            ->where('inventorier_embs.Id_Inventaire_Emballage', '=', 0)
            ->get();

        $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
            ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
            ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', 0)
            ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin')
            ->get();

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $inventaire_emballages = $query->get();
            } else {
                $inventaire_emballages = $query->where('agences.id', '=', $site_id)->get();
            }


        if (is_array(getIdAgenceByUser())) {
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->whereIn('agence_id', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
        } else {
            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
        }

        return view('page.emballage.inventaire_emballage.inventaire', [
            'inventaire_emballages' => $inventaire_emballages,
            'inventaire_magasin_emballages' => $inventaire_magasin_emballages,
            'inventoriers' => $inventoriers,
            'magasins' => $magasins,
            'annees' => $annees
        ]);
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        } */
    }
    public function  create(Request $request)
    {
        $this->authorize('effectuer-inventaire');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';
            $site_id = session()->get('site_id');
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // if (is_array(getIdAgenceByUser())) {
            //     $magasins = DB::table('magasins')
            //         ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            //         ->select('magasins.*', 'agences.NomAgence')->orderBy('created_at', 'desc')->get();
            // } else {
            //     $magasins = DB::table('magasins')
            //         ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            //         ->select('magasins.*', 'agences.NomAgence')
            //         ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
            // }

            $magasins = DB::table('magasins')
                ->join('stock_emballages', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('magasins.agence_id', $site_id)
                ->select(
                    'stock_emballages.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as  NomMagasin'),
                )
                ->groupBy('stock_emballages.Id_Magasin')
                ->get();

            return view('page.emballage.inventaire_emballage.nouveau', [
                'magasins' => $magasins
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
        // $produits = Produit::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('id', 'desc')->get();

    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-inventaire');
        // try{

        $validator = Validator::make(
            $request->all(),
            [
                // 'fournisseur' => 'required',
                'observation' => 'required',
                // 'inputs.*.produit' => 'required',
                //  'inputs.*.designation' => 'required',
                'inputs.*.magasin' => 'required',
                // 'inputs.*.quantity' => 'required',
                // 'inputs.*.prix_achat' => 'required',
            ],
            [
                // 'fournisseur' => 'Fournisseur requis',
                'observation' => 'Observations requis',
                // 'inputs.*.produit' => "produit(s) requis",
                // 'inputs.*.designation' => "designation(s) requise(s)",
                'inputs.*.magasin' => "magasin(s) requis",
                // 'inputs.*.quantity' => "quantite(s) requise(s)",
                // 'inputs.*.prix_achat' => "prix achat(s) requis",
            ]

        );

        if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Obtenez le dernier chiffre de l'année actuelle
        $lastDigitOfYear = substr(Carbon::now()->year, -2);
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->inventaire ?? '';

        // Obtenez le dernier numéro de référence enregistré
        // $lastReference = InventaireProduit::count();
        $lastReference = InventaireEmballage::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            // $lastReference = InventaireProduit::orderBy('id', 'desc')->first();
            $lastReference = InventaireEmballage::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Inventaire, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        if (is_array(getIdAgenceByUser())) {
            $site_id = 1;
        } else {
            $site_id = getIdAgenceByUser();
        }

        //variable de creation entree produit
        $reference_inventaire = "{$site_id}/{$lastDigitOfYear}/INEMB/{$incrementedReferenceNumber}";
        $date_inventaire =  Carbon::now();
        $observation = $request->input('observation');

        // dd(auth()->user()->id);

        $inventaire_emballage = new InventaireEmballage();
        $inventaire_emballage->Date_Inventaire = $date_inventaire;
        $inventaire_emballage->Id_Utilisateur = auth()->user()->id;
        $inventaire_emballage->Reference_Inventaire = $reference_inventaire;
        $inventaire_emballage->Observations = $observation;
        $inventaire_emballage->Id_Agence = $site_id;
        // $entree_produit->Id_Fournisseur = $fournisseur;
        $inventaire_emballage->save();


        foreach ($request->inputs as $value) {

            $magasin_formate = explode('-', $value['magasin'], 2);
            $id_magasin = trim($magasin_formate[0]);

            if ($value['categorie'] !== 'Toutes' && $value['produit'] !== 'Tous') {
                // dd('1');
                $categorie_formate = explode('-', $value['categorie'], 3);
                $id_categorie = trim($categorie_formate[1]);

                $produit_formate = explode('-', $value['produit'], 2);
                $id_produit = trim($produit_formate[0]);

                $stock = DB::table('stock_emballages')
                    ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                    ->where('stock_emballages.Id_Magasin', '=', $id_magasin)
                    ->where('stock_emballages.Id_Emballage', '=', $id_produit)
                    ->where('emballages.Categorie_emballage_id', '=', $id_categorie)
                    ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage', 'emballages.Categorie_emballage_id')
                    ->get();

                // dd($stock);

                if (count($stock) <= 0) {
                    // dd($stock);
                    return to_route('inventaire_emballage')->with('error', "L\'emballage {$produit_formate[1]} qui a pour catégorie {$categorie_formate[1]} n\'est pas disponible  dans le stock du magasin {$magasin_formate[1]}");
                }
            } elseif ($value['categorie'] !== 'Toutes' && $value['produit'] === 'Tous') {
                // dd('2');
                $categorie_formate = explode('-', $value['categorie'], 3);
                $id_categorie = trim($categorie_formate[1]);


                $stock = DB::table('stock_emballages')
                    ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                    ->where('stock_emballages.Id_Magasin', '=', $id_magasin)
                    ->where('emballages.Categorie_emballage_id', '=', $id_categorie)
                    ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage', 'emballages.Categorie_emballage_id')
                    ->get();

                // dd($stock);

                // if (count($stock) <= 0) {
                //     // dd($stock);
                //     return to_route('inventaire_emballage')->with('error', "L\'emballage {$produit_formate[1]} qui a pour catégorie {$categorie_formate[1]} n\'est pas disponible  dans le stock du magasin {$magasin_formate[1]}");
                // }
            } else {
                // dd('3');
                $stock = StockEmballage::where('Id_magasin', '=', $id_magasin)->get();
                // dd($stock);
            }



            if ($stock) {

                foreach ($stock as $item) {
                    $inventaire_magasin_emballage = new InventaireEmballageMagasin();
                    $inventaire_magasin_emballage->Id_Inventaire_Emballage = $inventaire_emballage->id;
                    $inventaire_magasin_emballage->Id_Magasin = $id_magasin;
                    $inventaire_magasin_emballage->Id_Stock_Emballage = $item->id;
                    $inventaire_magasin_emballage->save();


                    $inventorier = new InventorierEmb();
                    $inventorier->Id_Stock_Emballage = $item->id;
                    $inventorier->Id_Inventaire_Emballage = $inventaire_emballage->id;
                    $inventorier->Qte_Initiale = $item->Qte_stockee;
                    $inventorier->Qte_Comptee = 0.00;
                    $inventorier->Qte_Ecart = 0.00;
                    $inventorier->Analyse_Ecart = 0.00;
                    $inventorier->Qte_Ajustee = 0.00;
                    $inventorier->save();
                }
            } else {
                return redirect()->back()->with('error', " Pas de stock d'emballage disponible. Veuillez en le créer");
            }

        }

        return to_route('inventaire_emballage')->with('success', 'L\'inventaire a bien été ajouté');
        // }catch(Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }

    }

    public function getInventaireEmballageMagasin($id)
    {
        $this->authorize('effectuer-inventaire');
        // try {


        $site_id = session()->get('site_id');
        $annees = InventaireEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $inventaire_emballages = DB::table('inventaire_emballages')
            // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('agences', 'inventaire_emballages.Id_Agence', '=', 'agences.id')
            ->join('users', 'inventaire_emballages.Id_Utilisateur', '=', 'users.id')
            ->select('inventaire_emballages.*', 'agences.NomAgence', 'users.name')
            ->where('agences.id', '=', $site_id)
            ->orderBy('inventaire_emballages.id', 'desc')
            ->get();

        $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
            ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'inventaire_emballage_magasins.Id_Magasin',
                DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                DB::raw('MAX(agences.NomAgence) as NomAgence'),
                DB::raw('MAX(inventaire_emballage_magasins.Id_Inventaire_Emballage) as Id_Inventaire_Emballage'),
                DB::raw('MAX(inventaire_emballage_magasins.Id_Magasin) as Id_Magasin'),
                DB::raw('MAX(inventaire_emballage_magasins.Id_Stock_Emballage) as Id_Stock_Emballage'),
                // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
            )
            ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $id)
            // ->groupBy('inventaire_magasins.Id_Inventaire_Produit')
            ->groupBy('inventaire_emballage_magasins.Id_Magasin')
            ->get();

        // dd($inventaire_magasins);
        foreach ($inventaire_magasin_emballages as $inventaire_magasin) {

            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage', 'categorie_emballages.Libelle', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'inventorier_embs.Qte_Initiale')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire_magasin->Id_Inventaire_Emballage)
                // ->where('stocks.Id_Produit', '=', $inventaire_magasin->Id_Produit)
                // ->where('produits.Id_Categorie', '=', $inventaire_magasin->Id_Catgorie_Produit)
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $id)
                ->get();
        }

        // dd($inventoriers);
        // Utilisez la méthode with() pour passer des données et des messages à la vue
        return view('page.emballage.inventaire_emballage.inventaire', [
            'inventaire_emballages' => $inventaire_emballages,
            'inventaire_magasin_emballages' => $inventaire_magasin_emballages,
            'inventoriers' => $inventoriers,
            'annees' => $annees
            // 'magasins' => Magasin::all()
        ])
            ->with('success', $inventaire_magasin_emballages->count() . ' produit(s) entré(s) trouvé(s)');
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        // }
        // dd($id);



    }
    public function filterInventaireEmballage(Request $request)
    {
        $this->authorize('consulter-inventaire');
        $site_id = session()->get('site_id');

        //try{
        // dd($request);
        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $year = date('Y'); // Ou une autre année si nécessaire
        $annee = $request->query('annee');

        $user = Auth::user();
        $user_connecterId = $user->id;
        $annees = InventaireEmballage::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');

        $query = DB::table('inventaire_emballages')
            ->join('agences', 'inventaire_emballages.Id_Agence', '=', 'agences.id')
            ->join('users', 'inventaire_emballages.Id_Utilisateur', '=', 'users.id')
            ->select('inventaire_emballages.*', 'agences.NomAgence', 'users.name')
            ->orderBy('inventaire_emballages.id', 'desc');

        if ($month) {
            $query->whereMonth('inventaire_emballages.created_at', $month)
                ->whereYear('inventaire_emballages.created_at', $year);
        }

        if ($startDate) {
            $query->whereDate('inventaire_emballages.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('inventaire_emballages.created_at', '<=', $endDate);
        }

        if ($annee) {
            $query->whereYear('inventaire_emballages.created_at', $annee);
        }

        $query_agence = Agence::find($site_id);

        if ($query_agence->NomAgence === 'Siège') {
            $inventaire_emballages = $query->get();
        } else {
            $inventaire_emballages = $query->where('agences.id', '=', $site_id)->get();
        }

        // $inventaire_emballages = $query->get();



        // Formater les données pour qu'elles soient bien interprétées en JSON
        return view('page.emballage.inventaire_emballage.inventaire', [
            'inventaire_emballages' => $inventaire_emballages,
            // 'inventaire_magasin_emballages' => $inventaire_magasin_emballages,
            // 'inventoriers' => $inventoriers,
            // 'magasins' => $magasins,
            'annees' => $annees
        ]);

        // dd($entree_produits);


        return response()->json($inventaire_emballages);
        /* }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

    public function modifier_statut_inventaire_emballage(Request $request)
    {
        $this->authorize('boucler-inventaire');
        // dd($request->id);

        // try {

            $site_id = session()->get('site_id');
            $id = $request->id;

            $inventaire_produit = InventaireEmballage::find($id);
            // dd($inventaire_produit);

            if ($inventaire_produit->Statut_Inventaire !== 'BOUCLER') {
                $inventaire_produit->Statut_Inventaire = 'BOUCLER';
                $inventaire_produit->update();

                $inventoriers =  InventorierEmb::where('Id_Inventaire_Emballage', '=', $id)->get();
                // dd($inventoriers);

                foreach ($inventoriers as $inventorier) {
                    $stock = StockEmballage::find($inventorier->Id_Stock_Emballage);

                    // dd($stock);

                    if ($stock) {

                        // if ($inventorier->Qte_Ajustee > 0) {
                        $stock->Qte_stockee = $inventorier->Qte_Ajustee;
                        $stock->update();
                        // }

                        $date_entree = Carbon::now();
                        $difference_stock = $inventorier->Qte_Initiale - $inventorier->Qte_Comptee;

                        if ($difference_stock < 0) {
                            $operation = 'ENTREE';
                            $type_operation = 'ENTREE';
                            $motif = 'Entrée inventaire emballage';
                            $difference_stock = abs($difference_stock);
                        } else if ($difference_stock > 0) {
                            $operation = 'SORTIE';
                            $type_operation = 'SORTIE';
                            $motif = 'Sortie inventaire emballage';
                        }

                        $historique_stock_emballage = new StockEmballageHistories();
                        $historique_stock_emballage->Date = $date_entree;
                        $historique_stock_emballage->agence_id = $site_id;
                        $historique_stock_emballage->Motif = $motif;
                        $historique_stock_emballage->Justificatif = $inventaire_produit->Reference_Inventaire;
                        $historique_stock_emballage->operation = $operation; // Tu peux définir l'opération si nécessaire
                        $historique_stock_emballage->type_operation = $type_operation;
                        $historique_stock_emballage->Id_Utilisateur = Auth::id();
                        $historique_stock_emballage->Id_Emballage = $stock->Id_Emballage;
                        $historique_stock_emballage->Id_Magasin = $stock->Id_Magasin;
                        $historique_stock_emballage->Quantite = $difference_stock;

                        // Sauvegarder l'enregistrement
                        $historique_stock_emballage->save();
                    }
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'Cet inventaire est déjà bouclé !',
                ]);
            }
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }

    }
    public function modifier_inventorier_emballage(Request $request)
    {
        $this->authorize('consulter-inventaire');
        // dd($request->inputs);
        try {
            foreach ($request->inputs as $value) {

                $inventorier = InventorierEmb::find($value['id']);

                // dd($inventorier);
                if ($inventorier) {
                    $inventorier->Qte_Initiale = $value['quantite_initiale'];
                    $inventorier->Qte_Comptee = $value['quantite_comptee'];
                    $inventorier->Qte_Ecart = $value['ecart'];
                    $inventorier->Justificatif = $value['justificatif'];
                    $inventorier->Qte_Ajustee = $value['justifiee'];
                    $inventorier->update();
                } else {
                    return to_route('inventaire_emballage')->with('error', 'Mise à jour echouée!');
                }
            }
            return to_route('inventaire_emballage')->with('success', 'Mise à jour avec succès!');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite .");
        }
    }

    public function impimer_inventaire_emballage(Request $request)
    {
        $this->authorize('consulter-inventaire');
        $site_id = session()->get('site_id');
        // dd($request);
        // try{
        $reponse = $request->input('reponse');
        $inventaire = $request->input('reference_inventaire');
        $submit = $request->input('submit');

        if ($inventaire === null) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un inventaire!');
        }

        if ($reponse === 'fiche_stock') {
            // $inventaire_produit = InventaireProduit::where('id', '=', );

            // dd('fiche stock');
            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net',  'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasin_emballages, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }


            $all_inventaire_produit_stock = $inventoriers;
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventoriers);

        }

        if ($reponse === 'fiche_comptage') {
            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net', 'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasins, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($reponse === 'ecart_stock') {

            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net', 'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasin_emballages, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($reponse === 'valorisation_ecart') {
            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net', 'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasins, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($reponse === 'bilan_inventaire') {
            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net', 'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasins, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($reponse === 'valorisation_detail_inventaire') {
            $inventaire_magasin_emballages = DB::table('inventaire_emballage_magasins')
                ->join('magasins', 'inventaire_emballage_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('inventaire_emballage_magasins.*', 'magasins.NomMagasin', 'agences.NomAgence')
                ->where('inventaire_emballage_magasins.Id_Inventaire_Emballage', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->get();

            if (count($inventaire_magasin_emballages) <= 0) {
                return redirect()->back()->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventaire_magasins);
            $inventoriers = DB::table('inventorier_embs')
                ->join('stock_emballages', 'inventorier_embs.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('magasins.NomMagasin', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'stock_emballages.Qte_stockee', 'inventorier_embs.*', 'inventorier_embs.Id_Stock_Emballage', 'stock_emballages.Prix_Achat_Net', 'stock_emballages.Id_Magasin')
                ->where('inventorier_embs.Id_Inventaire_Emballage', '=', $inventaire)
                ->get();

            // dd($inventaire_magasins, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }
        }


        //    dd('je usi la');


        $all_inventaire_produit_stock = $inventoriers;
        // dd($all_inventaire_produit_stock);
        if (count($all_inventaire_produit_stock) > 0) {
            $get_request = $all_inventaire_produit_stock;
        } else {
            return to_route('inventaire_emballage')->with('error', 'Aucunes données trouvées!');
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $data = [
            'inventaire_magasin_emballage' => $inventaire_magasin_emballages,
            'getInventaire' => $get_request,
            'reponse' => $reponse,
            'inventaire' => InventaireEmballage::find($inventaire),
            'imageEntetePied' => $imageEntetePied,
            'texteEntetePied' => $texteEntetePied
        ];

        $prefixe = 'IN';
        $date_et_heure = date('Ymd_His');

        if ($submit == 'PDF') {

            $options = new Options();
            $options->set('chroot', realpath(''));
            $dompdf = new Dompdf($options);


            $htmlContent = view('page.emballage.inventaire_emballage.imprimer.imprimer', $data)->render();


            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isHtmlHeaderFixed', true);
            $options->set('isHtmlFooterFixed', true);

            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream('INVENTAIRES_' . $date_et_heure, array("Attachment" => false));
        }

        if ($submit == 'EXCEL') {
            return Excel::download(new InventaireEmballagePeriodeExport($data), 'INVENTAIRES_EMBALLAGE_' . $date_et_heure . '.xlsx');
        }
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

    public function getProductsByCategoryEmballage($categoryId)
    {
        // dd($categoryId);

        if ($categoryId !== 'Toutes') {

            $categorie_formate = explode('-', $categoryId, 3);
            $id_categorie = trim($categorie_formate[1]);
            $id_magasin = trim($categorie_formate[0]);
            // dd('ici(----');
            // Filtrer les produits en fonction de la catégorie
            // $products = Produit::where('Id_Categorie', $categoryId)->get();

            $products = DB::table('emballages')
                ->join('stock_emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'stock_emballages.Id_Emballage',
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                )
                ->where('agences.id', '=', getIdAgenceByUser())
                ->where('emballages.Categorie_emballage_id', '=', $id_categorie)
                ->where('stock_emballages.Id_Magasin', '=', $id_magasin)
                ->groupBy('stock_emballages.Id_Emballage')
                ->get();
        } else {
            $products = [];
        }

        // dd($products);

        return response()->json($products);
    }

    public function getCategorysByMagasinEmballage($magasinId)
    {

        // dd('kflkd');

        $magasin_formate = explode('-', $magasinId, 2);
        $id_magasin = trim($magasin_formate[0]);

        $category = DB::table('emballages')
            ->join('stock_emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'emballages.Categorie_emballage_id',
                'stock_emballages.Id_Magasin',
                DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                DB::raw('MAX(categorie_emballages.id) as category_id'),
            )
            ->where('agences.id', '=', getIdAgenceByUser())
            ->groupBy('emballages.Categorie_emballage_id', 'stock_emballages.Id_Magasin')
            ->where('stock_emballages.Id_Magasin', '=', $id_magasin)
            ->get();

        // dd($category);

        return response()->json($category);
    }
}
