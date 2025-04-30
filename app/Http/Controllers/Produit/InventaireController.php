<?php

namespace App\Http\Controllers\Produit;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Magasin;
use App\Models\Inventorier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PrefixeReference;
use App\Models\InventaireMagasin;
use App\Models\InventaireProduit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\HistoriquePrixRevient;
use App\Exports\InventairePeriodeExport;
use App\Models\Agence;
use App\Models\CategorieProduit;
use App\Models\Produit;
use App\Models\StockHistories;
use Illuminate\Support\Facades\Validator;

class InventaireController extends Controller
{
    // retourne la vue d'inventaire
    public function  index(Request $request)
    {
        $this->authorize('consulter-inventaire');

        $site_id = session()->get('site_id');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->inventaire ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $annees = InventaireProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $query  = DB::table('inventaire_produits')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'inventaire_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'inventaire_produits.Id_Utilisateur', '=', 'users.id')
                ->select('inventaire_produits.*', 'agences.NomAgence', 'users.name')
                // ->where('agences.id', '=', $site_id)
                ->whereMonth('inventaire_produits.created_at', $currentMonth)
                ->whereYear('inventaire_produits.created_at', $currentYear)
                ->orderBy('inventaire_produits.id', 'desc');

                $query_agence = Agence::find($site_id);

                if($query_agence->NomAgence === 'Siège'){
                    $inventaire_produits = $query->get();
                }else{
                    $inventaire_produits = $query->where('agences.id', '=', $site_id)->get();
                }

            // dd($inventaire_produits);

            $inventoriers = DB::table('inventoriers')
                ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                ->join('inventaire_produits', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->select('inventoriers.*', 'magasins.NomMagasin',  'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage')
                ->where('inventoriers.Id_Inventaire_Produit', '=', 0)
                ->get();

            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', 0)
                ->select('inventaire_magasins.*', 'magasins.NomMagasin')
                ->get();

            // $magasins = Magasin::orderBy('id', 'desc')->get();

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



            // dd($inventaire_magasins);
            return view('page.produit.inventaire.inventaire', [
                'inventaire_produits' => $inventaire_produits,
                'inventaire_magasins' => $inventaire_magasins,
                'inventoriers' => $inventoriers,
                'magasins' => $magasins,

                'annees' => $annees
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function filterInventaire(Request $request)
    {
        $this->authorize('consulter-inventaire');
        //try{
        // dd($request);
        $site_id = session()->get('site_id');
        $annees = InventaireProduit::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $annee = $request->query('annee');

        // Vérification de l'utilisateur et de l'agence
        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');

        $query = DB::table('inventaire_produits')
            // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
            ->join('agences', 'inventaire_produits.Id_Agence', '=', 'agences.id')
            // ->where('agences.id', '=', $site_id)
            ->join('users', 'inventaire_produits.Id_Utilisateur', '=', 'users.id')
            ->select('inventaire_produits.*', 'agences.NomAgence', 'users.name')
            ->orderBy('inventaire_produits.id', 'desc');

            $query_agence = Agence::find($site_id);




        if ($annee) {
            $query->whereYear('inventaire_produits.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('inventaire_produits.created_at', $month);
            $query->whereYear('inventaire_produits.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('inventaire_produits.created_at', '>=', $startDate)
                ->where('inventaire_produits.created_at', '<=', $endDate);
        }


        if($query_agence->NomAgence === 'Siège'){
            $inventaire_produits = $query->get();
        }else{
            $inventaire_produits = $query->where('agences.id', '=', $site_id)->get();
        }


        $inventoriers = DB::table('inventoriers')
            ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
            ->join('inventaire_produits', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
            ->select('inventoriers.*', 'magasins.NomMagasin',  'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage')
            ->where('inventoriers.Id_Inventaire_Produit', '=', 0)
            ->get();


        $inventaire_magasins = DB::table('inventaire_magasins')
            ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
            ->where('inventaire_magasins.Id_Inventaire_Produit', '=', 0)
            ->select('inventaire_magasins.*', 'magasins.NomMagasin')
            ->get();

        // $magasins = Magasin::orderBy('id', 'desc')->get();

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

        // dd($inventaire_magasins);
        return view('page.produit.inventaire.inventaire', [
            'inventaire_produits' => $inventaire_produits,
            'inventaire_magasins' => $inventaire_magasins,
            'inventoriers' => $inventoriers,
            'magasins' => $magasins,
            'annees' => $annees
        ]);
        /* }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

    public function  create(Request $request)
    {
        $this->authorize('effectuer-inventaire');
        $site_id = session()->get('site_id');
        // try {
        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->inventaire ?? '';
        if ($prefix == null) {
            return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
        }
        // if (is_array(getIdAgenceByUser())) {
        //     $magasins = DB::table('magasins')
        //         ->join('agences', 'magasins.agence_id', '=', 'agences.id')
        //         ->join('stocks', 'stocks.Id_Magasin', 'produits.id')
        //         ->select('magasins.*', 'agences.NomAgence')->orderBy('created_at', 'desc')->get();
        // } else {
        //     $magasins = DB::table('magasins')
        //         ->join('agences', 'magasins.agence_id', '=', 'agences.id')
        //         ->select('magasins.*', 'agences.NomAgence')
        //         ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
        // }

        $magasins = DB::table('magasins')
            ->join('stocks', 'stocks.Id_Magasin', 'magasins.id')
            ->join('produits', 'stocks.Id_Magasin', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
            ->where('magasins.agence_id', '=', $site_id)
            ->select('stocks.Id_Magasin',
            DB::raw('MAX(magasins.NomMagasin) as  NomMagasin'),
            )
            ->groupBy('stocks.Id_Magasin')
            ->get();

        // dd($magasins);

        $produits = Produit::orderBy('id', 'desc')->get();
        $categories = CategorieProduit::orderBy('id', 'desc')->get();

        $produits = DB::table('produits')
            ->join('stocks', 'stocks.Id_Produit', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'stocks.Id_Produit',
                DB::raw('MAX(produits.Reference) as Reference'),
                DB::raw('MAX(produits.Designation) as Designation'),
            )
            ->where('agences.id', '=', getIdAgenceByUser())
            ->groupBy('stocks.Id_Produit')
            ->get();

        $categories = DB::table('produits')
            ->join('stocks', 'stocks.Id_Produit', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
            ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'produits.Id_Categorie',
                DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
            )
            ->where('agences.id', '=', getIdAgenceByUser())
            ->groupBy('produits.Id_Categorie')
            ->get();


        return view('page.produit.inventaire.nouveau', [
            'magasins' => $magasins,
            'produits' => $produits,
            'categories' => $categories,
        ]);
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
        // $produits = Produit::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('id', 'desc')->get();

    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-inventaire');
        // try{

        // dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                // 'fournisseur' => 'required',
                'observation' => 'required',
                // 'inputs.*.produit' => 'required',
                //  'inputs.*.designation' => 'required',
                'inputs.*.magasin' => 'required',
                'inputs.*.categorie' => 'required',
                'inputs.*.produit' => 'required',
            ],
            [
                // 'fournisseur' => 'Fournisseur requis',
                'observation' => 'Observations requis',
                // 'inputs.*.produit' => "produit(s) requis",
                // 'inputs.*.designation' => "designation(s) requise(s)",
                'inputs.*.magasin' => "magasin(s) requis",
                'inputs.*.categorie' => "categorie(s) requise(s)",
                'inputs.*.produit' => "produit(s) requis",
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
        $lastReference = InventaireProduit::where('Id_Agence', '=', session()->get('site_id'))->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '00001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            // $lastReference = InventaireProduit::orderBy('id', 'desc')->first();
            $lastReference = InventaireProduit::where('Id_Agence', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_Inventaire, -5); // Obtenez les 5 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        if (is_array(getIdAgenceByUser())) {
            $site_id = 1;
        } else {
            $site_id = getIdAgenceByUser();
        }

        //variable de creation entree produit
        $reference_inventaire = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
        $date_inventaire =  Carbon::now();
        $observation = $request->input('observation');

        // dd(auth()->user()->id);


        $inventaire_produit = new InventaireProduit();
        $inventaire_produit->Date_Inventaire = $date_inventaire;
        $inventaire_produit->Id_Utilisateur = auth()->user()->id;
        $inventaire_produit->Reference_Inventaire = $reference_inventaire;
        $inventaire_produit->Observations = $observation;
        $inventaire_produit->Id_Agence = $site_id;
        // $entree_produit->Id_Fournisseur = $fournisseur;
        $inventaire_produit->save();


        foreach ($request->inputs as $value) {

            $magasin_formate = explode('-', $value['magasin'], 2);
            $id_magasin = trim($magasin_formate[0]);

            if ($value['categorie'] !== 'Toutes' && $value['produit'] !== 'Tous') {
                // dd('1');
                $categorie_formate = explode('-', $value['categorie'], 3);
                $id_categorie = trim($categorie_formate[1]);

                $produit_formate = explode('-', $value['produit'], 2);
                $id_produit = trim($produit_formate[0]);

                $stock = DB::table('stocks')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->where('stocks.Id_Magasin', '=', $id_magasin)
                    ->where('stocks.Id_Produit', '=', $id_produit)
                    ->where('produits.Id_Categorie', '=', $id_categorie)
                    ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'produits.Id_Categorie')
                    ->get();

                if (count($stock) <= 0) {
                    // dd($stock);
                    return to_route('page.inventaire.inventaire')->with('error', "Le produit {$produit_formate[1]} qui a pour catégorie {$categorie_formate[1]} n\'est pas disponible  dans le stock du magasin {$magasin_formate[1]}");
                }
            }elseif ($value['categorie'] !== 'Toutes' && $value['produit'] === 'Tous') {
                // dd('2');
                $categorie_formate = explode('-', $value['categorie'], 3);
                $id_categorie = trim($categorie_formate[1]);

                $stock = DB::table('stocks')
                ->join('produits', 'stocks.Id_Produit', 'produits.id')
                ->where('stocks.Id_Magasin', '=', $id_magasin)
                ->where('produits.Id_Categorie', '=', $id_categorie)
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'produits.Id_Categorie')
                ->get();

                // dd($stock);

                // if (count($stock) <= 0) {
                //     // dd($stock);
                //     return to_route('inventaire_emballage')->with('error', "L\'emballage {$produit_formate[1]} qui a pour catégorie {$categorie_formate[1]} n\'est pas disponible  dans le stock du magasin {$magasin_formate[1]}");
                // }
            }  else {
                // dd('2');
                $stock = Stock::where('Id_magasin', '=', $id_magasin)->get();
                // dd($stock);
            }

            // dd(trim($id_magasin[0]));

            // $inventaire_magasin->Id_Categorie_Produit = $id_categorie;
            // dd($stock);
            // $historique_prix_revient = HistoriquePrixRevient::where('Id_Produit', '=', $stock->Id_produit)->first();
            if ($stock) {

                foreach ($stock as $item) {

                    $inventaire_magasin = new InventaireMagasin();
                    $inventaire_magasin->Id_Inventaire_Produit = $inventaire_produit->id;
                    $inventaire_magasin->Id_Magasin = $id_magasin;
                    $inventaire_magasin->Id_Stock = $item->id;
                    $inventaire_magasin->save();


                    $inventorier = new Inventorier();
                    $inventorier->Id_Stock = $item->id;
                    $inventorier->Id_Inventaire_Produit = $inventaire_produit->id;
                    $inventorier->Qte_Initiale = $item->Qte_stockee;
                    $inventorier->Qte_Comptee = 0.00;
                    $inventorier->Qte_Ecart = 0.00;
                    $inventorier->Analyse_Ecart = 0.00;
                    $inventorier->Qte_Ajustee = 0.00;
                    $inventorier->save();
                }
            } else {
                return redirect()->back()->with('error', " Pas de stock disponible. Veuillez en le créer");
            }
            // dd($stock);

        }

        return to_route('page.inventaire.inventaire')->with('success', 'L\'inventaire a bien été ajouté');
        // }catch(Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }

    }

    public function getInventaireMagasin($id)
    {

        // dd($id);
        $this->authorize('effectuer-inventaire');
        try {
            $annees = InventaireProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $inventaire_produits = DB::table('inventaire_produits')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'inventaire_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'inventaire_produits.Id_Utilisateur', '=', 'users.id')
                ->select('inventaire_produits.*', 'agences.NomAgence', 'users.name')
                ->orderBy('inventaire_produits.id', 'desc')
                ->get();

            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Inventaire_Produit) as Id_Inventaire_Produit'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $id)
                // ->groupBy('inventaire_magasins.Id_Inventaire_Produit')
                ->groupBy('inventaire_magasins.Id_Magasin')
                ->get();

            // dd($inventaire_magasins);
            foreach ($inventaire_magasins as $inventaire_magasin) {

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'inventoriers.Qte_Initiale')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire_magasin->Id_Inventaire_Produit)
                    // ->where('stocks.Id_Produit', '=', $inventaire_magasin->Id_Produit)
                    // ->where('produits.Id_Categorie', '=', $inventaire_magasin->Id_Catgorie_Produit)
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $id)
                    ->get();
            }

            // dd($inventoriers);
            // Utilisez la méthode with() pour passer des données et des messages à la vue
            return view('page.produit.inventaire.inventaire', [
                'inventaire_produits' => $inventaire_produits,
                'inventaire_magasins' => $inventaire_magasins,
                'inventoriers' => $inventoriers,
                'annees' => $annees
                // 'magasins' => Magasin::all()
            ])
                ->with('success', $inventaire_magasins->count() . ' produit(s) entré(s) trouvé(s)');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
        // dd($id);



    }

    public function modifierStatutInventaireProduit(Request $request)
    {
        $this->authorize('boucler-inventaire');
        try {

            $site_id = session()->get('site_id');
            $id = $request->id;

            $inventaire_produit = InventaireProduit::find($id);

            if ($inventaire_produit->Statut_Inventaire !== 'BOUCLER') {
                $inventaire_produit->Statut_Inventaire = 'BOUCLER';
                $inventaire_produit->update();

                $inventoriers =  Inventorier::where('Id_Inventaire_Produit', '=', $id)->get();

                // dd($inventaire_produit);

                foreach ($inventoriers as $inventorier) {
                    $stock = Stock::find($inventorier->Id_Stock);

                    if ($stock) {

                        // if ($inventorier->Qte_Ajustee > 0) {
                        $stock->Qte_stockee = $inventorier->Qte_Ajustee;
                        $stock->update();
                        // }

                        $date_entree = Carbon::now();

                        $difference_stock = $inventorier->Qte_Initiale - $inventorier->Qte_Comptee;

                        // dd($di)

                        if ($difference_stock < 0) {
                            $operation = 'ENTREE';
                            $type_operation = 'ENTREE';
                            $motif = 'Entrée inventaire';
                            $difference_stock = abs($difference_stock);
                        } else if ($difference_stock > 0) {
                            $operation = 'SORTIE';
                            $type_operation = 'SORTIE';
                            $motif = 'Sortie inventaire';
                        }

                        $historique_stock = new StockHistories();
                        $historique_stock->Date = $date_entree;
                        $historique_stock->agence_id = $site_id;
                        $historique_stock->Motif = $motif;
                        $historique_stock->Justificatif = $inventaire_produit->Reference_Inventaire;
                        $historique_stock->operation = $operation; // Tu peux définir l'opération si nécessaire
                        $historique_stock->type_operation = $type_operation;
                        $historique_stock->Id_Utilisateur = Auth::id();
                        $historique_stock->Id_Produit = $stock->Id_Produit;
                        $historique_stock->Id_Magasin = $stock->Id_Magasin;
                        $historique_stock->Quantite = $difference_stock;

                        // Sauvegarder l'enregistrement
                        $historique_stock->save();
                    }
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'Cet inventaire est déjà bouclé !',
                ]);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function modifierInventorier(Request $request)
    {
        $this->authorize('consulter-inventaire');
        // dd($request->inputs);
        try {
            foreach ($request->inputs as $value) {
                // dd($value['id']);
                $inventorier = Inventorier::find($value['id']);
                if ($inventorier) {
                    $inventorier->Qte_Initiale = $value['quantite_initiale'];
                    $inventorier->Qte_Comptee = $value['quantite_comptee'];
                    $inventorier->Qte_Ecart = $value['ecart'];
                    $inventorier->Justificatif = $value['justificatif'];
                    $inventorier->Qte_Ajustee = $value['justifiee'];
                    $inventorier->update();
                } else {
                    return to_route('page.inventaire.inventaire')->with('error', 'Mise à jour echouée!');
                }
            }
            return to_route('page.inventaire.inventaire')->with('success', 'Mise à jour avec succès!');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function imprimerInventaire(Request $request)
    {
        $this->authorize('consulter-inventaire');
        $site_id = session()->get('site_id');

        // dd($request);
        // try{
        $reponse = $request->input('reponse');
        $inventaire = $request->input('reference_inventaire');
        $submit = $request->input('submit');

        // dd($inventaire);

        if ($inventaire === null) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un inventaire!');
        }

        if ($reponse === 'fiche_stock') {
            // $inventaire_produit = InventaireProduit::where('id', '=', );

            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

                // dd($inventaire_magasins);

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
                    ->get();

            // dd($inventaire_magasins, $inventoriers );

            $all_inventaire_produit_stock = $inventoriers;
            // dd($all_inventaire_produit_stock);
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }


            $all_inventaire_produit_stock = $inventoriers;
            if (count($all_inventaire_produit_stock) > 0) {
                $get_request = $all_inventaire_produit_stock;
            } else {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

            // dd($inventoriers);

        }

        if ($reponse === 'fiche_comptage') {
            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
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

        if ($reponse === 'ecart_stock') {

            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
                    ->where('inventoriers.Qte_Ecart', '<>', '0')
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

        if ($reponse === 'valorisation_ecart') {
            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin', 'stocks.Prix_Achat_Net')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
                    ->where('inventoriers.Qte_Ecart', '<>', '0')
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
            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin', 'stocks.Prix_Achat_Net')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
                    ->where('inventoriers.Qte_Ecart', '<>', '0')
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
            $inventaire_magasins = DB::table('inventaire_magasins')
                ->join('magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'inventaire_magasins.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    DB::raw('MAX(inventaire_magasins.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(inventaire_magasins.Id_Stock) as Id_Stock'),
                    // DB::raw('MAX(inventaire_magasins.Id_Produit) as Id_Catgorie_Produit')
                )
                ->where('inventaire_magasins.Id_Inventaire_Produit', '=', $inventaire)
                ->where('agences.id', '=', $site_id)
                ->groupBy('inventaire_magasins.Id_Magasin')
                // ->orderBy('inventaire_magasins.Id_Magasin')
                ->get();

            if (count($inventaire_magasins) <= 0) {
                return to_route('page.inventaire.inventaire')->with('error', 'Aucunes données trouvées!');
            }

                $inventoriers = DB::table('inventoriers')
                    ->join('stocks', 'inventoriers.Id_Stock', '=', 'stocks.id')
                    ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_magasins', 'inventaire_magasins.Id_Magasin', '=', 'magasins.id')
                    // ->join('inventaire_produits', 'inventaire_magasins.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    // ->join('inventoriers', 'inventoriers.Id_Inventaire_Produit', '=', 'inventaire_produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                    ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                    ->select('magasins.NomMagasin', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'stocks.Qte_stockee', 'unite_comptages.Libelle as Libelle_Comptage', 'inventoriers.*', 'inventoriers.Id_Stock', 'stocks.Id_Magasin', 'stocks.Prix_Achat_Net')
                    ->where('inventoriers.Id_Inventaire_Produit', '=', $inventaire)
                    ->where('inventoriers.Qte_Ecart', '<>', '0')
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

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $get_inventaire = DB::table('inventaire_produits')
        ->join('users', 'inventaire_produits.Id_Utilisateur', '=', 'users.id')
        ->join('agences', 'inventaire_produits.Id_Agence', '=', 'agences.id')
        ->select('inventaire_produits.*', 'users.name',  'agences.NomAgence')
        ->where('inventaire_produits.id', '=', $inventaire)
        ->first();


        $data = [
            'inventaire_magasin' => $inventaire_magasins,
            'getInventaire' => $get_request,
            'reponse' => $reponse,
            'inventaire' => $get_inventaire,
            'imageEntetePied' => $imageEntetePied,
            'texteEntetePied' => $texteEntetePied,
        ];


        $prefixe = 'IN';
        $date_et_heure = date('Ymd_His');

        if ($submit == 'PDF') {

            $options = new Options();
            $options->set('chroot', realpath(''));
            $dompdf = new Dompdf($options);

            $htmlContent = view('page.produit.inventaire.imprimer.imprimer', $data)->render();

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isHtmlHeaderFixed', true);
            $options->set('isHtmlFooterFixed', true);

            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream('INVENTAIRES_' . $date_et_heure, array("Attachment" => false));
        }

        if ($submit == 'EXCEL') {
            return Excel::download(new InventairePeriodeExport($data), 'INVENTAIRES_' . $date_et_heure . '.xlsx');
        }
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }


    public function getProductsByCategory($categoryId)
    {
        // dd($id_categorie, $id_magasin);

        if ($categoryId !== 'Toutes') {

            $categorie_formate = explode('-', $categoryId, 3);
            $id_categorie = trim($categorie_formate[1]);
            $id_magasin = trim($categorie_formate[0]);
            // dd('ici(----');
            // Filtrer les produits en fonction de la catégorie
            // $products = Produit::where('Id_Categorie', $categoryId)->get();

            $products = DB::table('produits')
                ->join('stocks', 'stocks.Id_Produit', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select(
                    'stocks.Id_Produit',
                    DB::raw('MAX(produits.Reference) as Reference'),
                    DB::raw('MAX(produits.Designation) as Designation'),
                )
                ->where('agences.id', '=', getIdAgenceByUser())
                ->where('produits.Id_Categorie', '=', $id_categorie)
                ->where('stocks.Id_Magasin', '=', $id_magasin)
                ->groupBy('stocks.Id_Produit')
                ->get();
        } else {
            $products = [];
        }

        return response()->json($products);
    }

    public function getCategorysByMagasin($magasinId)
    {
        $magasin_formate = explode('-', $magasinId, 2);
        $id_magasin = trim($magasin_formate[0]);

        $category = DB::table('produits')
            ->join('stocks', 'stocks.Id_Produit', 'produits.id')
            ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
            ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select(
                'produits.Id_Categorie',
                'stocks.Id_Magasin',
                DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                DB::raw('MAX(categorie_produits.id) as category_id'),
            )
            ->where('agences.id', '=', getIdAgenceByUser())
            ->groupBy('produits.Id_Categorie', 'stocks.Id_Magasin')
            ->where('stocks.Id_Magasin', '=', $id_magasin)
            ->get();

        // dd($category);

        return response()->json($category);
    }
}
