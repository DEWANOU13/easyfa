<?php

namespace App\Http\Controllers\Produit;

use App\Exports\SortieActionPrintExport;
use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Transferer;
use Illuminate\Http\Request;
use App\Models\SortieProduit;
use App\Models\SortirProduit;
use App\Models\StockHistories;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use App\Exports\SortiePeriodeExport;
use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SortieController extends Controller
{
    // retourne la vue de stock
    public function  index(Request $request)
    {
        $this->authorize('consulter-sortie-produit');
        try {
            $site_id =session()->get('site_id');

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $query = DB::table('sortie_produits')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'sortie_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_produits.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_produits.*', 'users.name', 'agences.NomAgence')
                // ->where('agences.id', '=', $site_id)
                ->whereMonth('sortie_produits.created_at', $currentMonth)
                ->whereYear('sortie_produits.created_at', $currentYear);

                $query_agence = Agence::find($site_id);


                if($query_agence->NomAgence === 'Siège'){
                    $sortie_produits = $query->get();
                }else{
                    $sortie_produits = $query->where('agences.id', '=', $site_id)->get();
                }

            $sortir_produits = DB::table('sortir_produits')
                ->join('stocks', 'sortir_produits.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('sortir_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
                ->where('sortir_produits.Id_Sortie_Produit', '=', 0)
                ->get();
            // dd($sortie_produits);

            $produits = Produit::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();



            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $annees = SortieProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view('page.produit.sortie.sortie', [
                'sortie_produits' => $sortie_produits,
                'sortir_produits' => $sortir_produits,
                'produits' => $produits,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function filterSortie(Request $request)
    {
        $this->authorize('consulter-sortie-produit');
        try {

            $site_id =session()->get('site_id');

            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            $query = DB::table('sortie_produits')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'sortie_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_produits.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_produits.*', 'users.name', 'agences.NomAgence');
                // ->where('agences.id', '=', $site_id);

                $query_agence = Agence::find($site_id);



        if ($annee) {
            $query->whereYear('sortie_produits.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('sortie_produits.created_at', $month);
            $query->whereYear('sortie_produits.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('sortie_produits.created_at', '>=', $startDate)
                  ->where('sortie_produits.created_at', '<=', $endDate);
        }

            $sortie_produits = $query->get();


            if($query_agence->NomAgence === 'Siège'){
                $sortie_produits = $query->get();
            }else{
                $sortie_produits = $query->where('agences.id', '=', $site_id)->get();
            }

            $sortir_produits = DB::table('sortir_produits')
            ->join('stocks', 'sortir_produits.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->select('sortir_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
            ->where('sortir_produits.Id_Sortie_Produit', '=', 0)
            ->get();
        // dd($sortie_produits);

        $produits = Produit::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('created_at', 'desc')->get();
        $categories = CategorieProduit::orderBy('created_at', 'desc')->get();



        $magasins = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select('magasins.*', 'agences.NomAgence')
            ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

        $annees = SortieProduit::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        return view('page.produit.sortie.sortie', [
            'sortie_produits' => $sortie_produits,
            'sortir_produits' => $sortir_produits,
            'produits' => $produits,
            'magasins' => $magasins,
            'categories' => $categories,
            'annees' => $annees
        ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    //retourne la vue d'ajout une nouvelle entree
    public function  create()
    {
        $this->authorize('effectuer-sortie-produit');
        try {
            $site_id =session()->get('site_id');
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stocks')
                ->join('produits', 'stocks.Id_produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin')
                ->where('produits.Type', '=', 'PRODUIT')
                ->where('stocks.Qte_stockee', '>', 0)
                ->where('magasins.agence_id', '=', $site_id)
                ->get();
            // dd($stock_produits);
            return view('page.produit.sortie.nouveau', [
                'stock_produits' => $stock_produits,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function store(Request $request)
    {
        $this->authorize('effectuer-sortie-produit');
        // dd($request);
        // try {
            $validator = Validator::make(
                $request->all(),
                [
                    // 'fournisseur' => 'required',
                    'type_sortie' => 'required',
                    'observation' => 'required',
                    'inputs.*.produit' => 'required',
                    //  'inputs.*.designation' => 'required',
                    'inputs.*.magasin' => 'required',
                    // 'inputs.*.quantity' => 'required',
                    'inputs.*.quantity_out' => 'required',
                ],
                [
                    // 'fournisseur' => 'Fournisseur requis',
                    'observation' => 'Observations requis',
                    'inputs.*.produit' => "produit(s) requis",
                    // 'inputs.*.designation' => "designation(s) requise(s)",
                    'inputs.*.magasin' => "magasin(s) requis",
                    // 'inputs.*.quantity' => "quantite(s) requise(s)",
                    'inputs.*.quantity_out' => "Quantite sortie requise(s)",
                ]

            );

            if ($validator->fails()) {
                // Si la validation échoue, retournez à la page précédente avec les erreurs
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';

            // Obtenez le dernier numéro de référence enregistré
            // $lastReference = SortieProduit::count();
            $lastReference = SortieProduit::where('Id_Agence','=' ,session()->get('site_id'))->count();

            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = SortieProduit::orderBy('id', 'desc')->first();
                $lastReference = SortieProduit::where('Id_Agence','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();;
                $lastReferenceNumber = substr($lastReference->Reference_Sortie, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }


            //variable de creation entree produit
            $reference_sortie = "1/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
            $date_sortie =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $type_sortie = $request->input('type_sortie');

            $site_id =session()->get('site_id');


            $sortie_produit = new SortieProduit();
            $sortie_produit->Date_Sortie = $date_sortie;
            $sortie_produit->Id_Utilisateur = auth()->user()->id;
            $sortie_produit->Reference_Sortie = $reference_sortie;
            $sortie_produit->type_sortie = $type_sortie;
            $sortie_produit->Observations = $observation;
            $sortie_produit->Id_Agence = $site_id;
            // $entree_produit->Id_Fournisseur = $fournisseur;
            $sortie_produit->save();


            foreach ($request->inputs as $value) {
                $produit_formate = explode('-', $value['produit'], 2);
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode('|', $produit_formate[1], 2);
                $reference_produit = $reference_produit_formate[0];
                $magasin_formate = explode('-', $value['magasin'], 2);
                $id_magasin = trim($magasin_formate[0]);


                $produit = Produit::where('Reference', '=', $reference_produit)->first();

                $stock = Stock::find($id_stock);

                // dd($stock);

                if ($produit && $stock) {
                    // dd(trim($id_magasin[0]));
                    $ligne_sortie_produit = new SortirProduit();
                    $ligne_sortie_produit->Id_Sortie_Produit = $sortie_produit->id;
                    $ligne_sortie_produit->Id_Stock = $stock->id;
                    $ligne_sortie_produit->Qte_Sortie = $value['quantity_out'];
                    $ligne_sortie_produit->Enregistrer_par = auth()->user()->id;

                    $ligne_sortie_produit->save();


                    $new_quantite_stockee = $stock->Qte_stockee - $value['quantity_out'];
                    // dd($quantite_stockee);
                    $stock->Qte_stockee = $new_quantite_stockee;
                    $stock->update();

                    $produitId = $produit->id;

                    // dd($produit->id);
                    if($type_sortie == 'SORTIE'){

                        if ($produit->type_emballage == 'EMBALLAGE_RECUPERABLE') {

                            $Produitemballage = Produit::where('id', $produitId)->first();
                            $idEmballage = $Produitemballage->Emballage_id;
                            if($idEmballage != null){
                                $stockEmballage = StockEmballage::where('Id_Emballage',$idEmballage)->where('Id_Magasin',$id_magasin)->first();
                                  if(empty($stockEmballage)){
                                     $stockEmballage = new StockEmballage();
                                     $stockEmballage->Id_Emballage = $idEmballage;
                                     $stockEmballage->Id_Magasin = $id_magasin;
                                     $stockEmballage->Qte_stockee = 0;
                                     $stockEmballage->Prix_Achat_Net = 0;
                                     $stockEmballage->Enregistrer_par = auth()->user()->id;
                                     $stockEmballage->save();
                                 }

                                 $stockEmballage->Qte_stockee = $stockEmballage->Qte_stockee + $value['quantity_out'];
                                 $stockEmballage->update();

                                //Historiq stock emballage
                                 $historique_sortie_emballage = new StockEmballageHistories();
                                 $historique_sortie_emballage->Date = Carbon::now();
                                 $historique_sortie_emballage->agence_id = $site_id;
                                 $historique_sortie_emballage->Motif = "Entree d'emballage sur une sortie de produit ayant un emballage";
                                 $historique_sortie_emballage->Justificatif =$reference_sortie;
                                 $historique_sortie_emballage->operation ='ENTREE';
                                 $historique_sortie_emballage->type_operation ='ENTREE';
                                 $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                                 $historique_sortie_emballage->Id_Emballage = $idEmballage;
                                 $historique_sortie_emballage->Id_Magasin = $id_magasin;
                                 $historique_sortie_emballage->Quantite = $value['quantity_out'];
                                 $historique_sortie_emballage->save();
                            }


                        }
                    }
                    if($type_sortie == 'CASSE'){

                        if ($produit->type_emballage == 'EMBALLAGE_RECUPERABLE') {

                            $Produitemballage = Produit::where('id', $produitId)->first();
                            $idEmballage = $Produitemballage->Emballage_id;
                            if($idEmballage != null){
                                $stockEmballage = StockEmballage::where('Id_Emballage',$idEmballage)->where('Id_Magasin',$id_magasin)->first();
                                  if(empty($stockEmballage)){
                                     $stockEmballage = new StockEmballage();
                                     $stockEmballage->Id_Emballage = $idEmballage;
                                     $stockEmballage->Id_Magasin = $id_magasin;
                                     $stockEmballage->Qte_stockee = 0;
                                     $stockEmballage->Prix_Achat_Net = 0;
                                     $stockEmballage->Enregistrer_par = auth()->user()->id;
                                     $stockEmballage->save();
                                 }

                                 // retirer le stock et mise a jour
                                 $stockEmballage->Qte_stockee -= $value['quantity_out'];
                                 $stockEmballage->update();


                                //Historiq stock emballage
                                 $historique_sortie_emballage = new StockEmballageHistories();
                                 $historique_sortie_emballage->Date = Carbon::now();
                                 $historique_sortie_emballage->agence_id = $site_id;
                                 $historique_sortie_emballage->Motif = "Sortie  d'emballage sur une sortie de produit de cassé ayant un emballage";
                                 $historique_sortie_emballage->Justificatif =$reference_sortie;
                                 $historique_sortie_emballage->operation ='SORTIE';
                                 $historique_sortie_emballage->type_operation ='CASSE';
                                 $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                                 $historique_sortie_emballage->Id_Emballage = $idEmballage;
                                 $historique_sortie_emballage->Id_Magasin = $id_magasin;
                                 $historique_sortie_emballage->Quantite = $value['quantity_out'];
                                 $historique_sortie_emballage->save();
                            }


                        }
                    }

                    //Historiq stock coté sortie

                    $historique_sortie_produit = new StockHistories();
                    $historique_sortie_produit->Date = $date_sortie;
                    $historique_sortie_produit->agence_id = $site_id;
                    $historique_sortie_produit->Motif = $observation;
                    $historique_sortie_produit->Justificatif = $reference_sortie;
                    $historique_sortie_produit->operation ='SORTIE';
                    $historique_sortie_produit->type_operation ='SORTIE';
                    $historique_sortie_produit->Id_Utilisateur = auth()->user()->id;
                    $historique_sortie_produit->Id_Produit = $stock->Id_Produit;
                    $historique_sortie_produit->Id_Magasin = $stock->Id_Magasin;
                    $historique_sortie_produit->Quantite = $value['quantity_out'];;
                    $historique_sortie_produit->save();
                }
            }
            return to_route('page.sortie.sortie')->with('success', 'La sorite a bien été ajoutée');
        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function getSortirProduit(Request $request, $id)
    {
        $this->authorize('consulter-sortie-produit');
        try {
            $site_id =session()->get('site_id');

            $query = DB::table('sortie_produits')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'sortie_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_produits.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_produits.*', 'users.name', 'agences.NomAgence')
                ->orderBy('sortie_produits.id', 'desc');

                $query_agence = Agence::find($site_id);

                if($query_agence->NomAgence === 'Siège'){
                    $sortie_produits = $query->get();
                }else{
                    $sortie_produits = $query->where('agences.id', '=', $site_id)->get();
                }

            $sortir_produits = DB::table('sortir_produits')
                ->join('stocks', 'sortir_produits.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('sortir_produits.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'stocks.Qte_stockee', 'stocks.Prix_Achat_Net')
                ->where('sortir_produits.Id_Sortie_Produit', '=', $id)
                ->get();
            // dd($sortir_produits);
            // dd($sortir_produits);

            $produits = Produit::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();

            $site_id = session()->get('site_id');

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $annees = SortieProduit::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view('page.produit.sortie.sortie', [
                'sortie_produits' => $sortie_produits,
                'sortir_produits' => $sortir_produits,
                'produits' => $produits,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ])
                ->with('success', $sortir_produits->count() . ' produit(s) entré(s) trouvé(s)');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
        // return Redirect::route('page.entree.entree')->with(
        //          [
        //             'entrer_produits' => $entrer_produits,
        //             'entree_produits' => $entree_produits
        //         ]
        // );
    }

    public function imprimerSortie(Request $request)
    {
        // dd($request);
        $this->authorize('imprimer-liste-sorties-produits');
        try {
            $debut_periode = $request->input('date_debut_periode');
            $fin_periode = $request->input('date_fin_periode');
            $magasin = $request->input('magasin');
            $categorie = $request->input('categorie');
            $produit = $request->input('produit');
            $submit = $request->input('submit');

            $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
            $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


            if ($magasin === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->select(
                        'stocks.Id_Magasin',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('stocks.Id_Magasin',)
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();


                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    // ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ;

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'stocks.Id_Magasin',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('stocks.Id_Magasin',)
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit',)
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('entrer_produits.Id_Produit', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }
            if ($magasin === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
                // dd('ok');
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('categorie_produits.id', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
                // dd($magasin);
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('categorie_produits.id', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    // ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('magasins.id', '=', $magasin)
                    ->where('produits.id', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin === 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    // ->where('magasins.id', '=', $magasin)
                    ->where('produits.id', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);
                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin !== 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('magasins.id', '=', $magasin)
                    ->where('produits.id', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
                $get_magasin = DB::table('sortir_produits')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('categorie_produits.id', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $sortie_produit = DB::table('sortie_produits')
                    ->join('sortir_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('users', 'sortie_produits.Id_Utilisateur', 'users.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('stocks.Id_Magasin', '=', $magasin)
                    // ->where('stocks.Id_Produit', '=', $produit)
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                    ->select(
                        'sortir_produits.Id_Sortie_Produit',
                        DB::raw('MAX(users.name) as name'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(sortie_produits.Reference_Sortie) as Reference_Sortie'),
                        DB::raw('MAX(sortie_produits.Observations) as Observations'),
                        DB::raw('MAX(sortie_produits.Date_Sortie) as Date_Sortie'),
                    )
                    ->groupBy('sortir_produits.Id_Sortie_Produit')
                    ->get();

                $query = DB::table('sortir_produits')
                    ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                    ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                    ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                    ->join('produits', 'stocks.Id_Produit', 'produits.id')
                    ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                    ->where('produits.Id_Categorie', '=', $categorie)
                    ->where('magasins.id', '=', $magasin)
                    // ->where('produits.id', '=', $produit)
                    ->select(
                        'stocks.Id_Magasin',
                        'sortir_produits.id',
                        DB::raw('MAX(stocks.Id_Produit) as Id_Produit'),
                        DB::raw('MAX(sortir_produits.Id_Sortie_Produit) as Id_Sortie_Produit'),
                        DB::raw('MAX(stocks.Id_Magasin) as Id_Magasin'),
                        DB::raw('MAX(produits.Reference) as Reference'),
                        DB::raw('MAX(produits.Designation) as Designation'),
                        DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                        DB::raw('MAX(sortir_produits.Qte_Sortie) as Qte_Sortie'),
                    )
                    ->groupBy('stocks.Id_Magasin', 'sortir_produits.id')
                    ->whereBetween('sortie_produits.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

                $all_sortie = $query->get();
                // dd($get_magasin, $sortie_produit, $all_sortie);
                if (count($all_sortie) > 0) {
                    $get_request = $all_sortie;
                } else {
                    return to_route('page.sortie.sortie')->with('error', 'Aucunes données trouvées!');
                }
            }

            if ($magasin !== 'Tous') {
                $magasin = Magasin::find($magasin);
            }
            if ($produit !== 'Tous') {
                $produit = Produit::find($produit);
            }
            if ($categorie !== 'Toutes') {
                $categorie = CategorieProduit::find($categorie);
            }
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            $data = [
                'debut_periode' => $debut_periode,
                'fin_periode' => $fin_periode,
                'produit' => $produit,
                'magasin' => $magasin,
                'categorie' => $categorie,
                'getSortie' => $get_request,
                'getMagasin' => $get_magasin,
                'getSortieProduit' => $sortie_produit,
                'imageEntetePied' => $imageEntetePied,

            ];
            $prefixe = 'SORTIE';
            $date_et_heure = date('Ymd_His');

            if($submit == 'PDF'){



                $options = new Options();
                $options->set('chroot', realpath(''));
                $dompdf = new Dompdf($options);


                $htmlContent = view('page.produit.sortie.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('SORTIE_'.$date_et_heure, array("Attachment" => false));
            }

            if($submit == 'EXCEL'){
                return Excel::download(new SortiePeriodeExport($data), 'SORTIE_'.$date_et_heure.'.xlsx');
            }



        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite." );
        }
    }

    public function sortirImprimer(Request $request)
    {
        $this->authorize('imprimer-liste-sorties-produits');
        // dd($request);

        try {
            $id_sortie = $request->input('id_sortie');
            $reponse = $request->input('reponse');

            // dd($id_transfert);

            // $sortie_produits = SortieProduit::where('id', '=', $id_sortie)->get();
            $sortie_produits = DB::table('sortie_produits')
                ->join('agences', 'sortie_produits.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_produits.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_produits.*', 'agences.NomAgence', 'users.name')
                ->where('sortie_produits.id', '=', $id_sortie)
                ->get();
            // $transferers = Transferer::where('transferers.Id_Transfert_Produit', '=', $id_transfert)->get();
            $sortir_produits = DB::table('sortir_produits')
                ->join('sortie_produits', 'sortir_produits.Id_Sortie_Produit', 'sortie_produits.id')
                ->join('stocks', 'sortir_produits.Id_Stock', 'stocks.id')
                ->join('magasins', 'stocks.Id_Magasin', 'magasins.id')
                ->join('produits', 'stocks.Id_Produit', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', 'categorie_produits.id')
                ->select('sortir_produits.Qte_Sortie', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle', 'magasins.NomMagasin')
                ->where('sortir_produits.Id_Sortie_Produit', '=', $id_sortie)
                ->get();

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $data =  [
            'imageEntetePied' => $imageEntetePied,
            'texteEntetePied' => $texteEntetePied,
            'sortie_produits' => $sortie_produits,
            'sortir_produits' => $sortir_produits,
        ];

        if($reponse === 'imprimer'){
            // dd('imprimer');
            $htmlContent = view('page.produit.sortie.imprimer.imprimer-action', $data)->render();
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
            $prefixe = 'SORTIE';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }else{
            $prefixe = 'sortie_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new SortieActionPrintExport($data), $nom_excel);
        }



            // return view('page.produit.sortie.imprimer.imprimer-action', $data);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
}
