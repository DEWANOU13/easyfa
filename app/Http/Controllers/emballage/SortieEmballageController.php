<?php

namespace App\Http\Controllers\emballage;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Emballage;
use App\Models\Transferer;
use Illuminate\Http\Request;
use App\Models\SortieProduit;
use App\Models\SortirProduit;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Models\SortieEmballage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use App\Models\CategorieEmballage;
use Illuminate\Support\Facades\DB;
use App\Exports\SortiePeriodeExport;
use App\Http\Controllers\Controller;
use App\Models\SortieLigneEmballage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Exports\SortieEmballagePeriodeExport;
use App\Exports\SortieEmballageActionPrintExport;
use App\Models\Agence;

class SortieEmballageController extends Controller
{
    public function  index(Request $request)
    {
        $this->authorize('voir-liste-sortie-emballage');
        $site_id = session()->get('site_id');

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $query = DB::table('sortie_emballages')
            ->join('agences', 'sortie_emballages.Id_Agence', '=', 'agences.id')
            ->join('users', 'sortie_emballages.Id_Utilisateur', '=', 'users.id')
            ->select('sortie_emballages.*', 'users.name', 'agences.NomAgence')
            // ->where('agences.id', '=', $site_id)
            ->whereMonth('sortie_emballages.created_at', $currentMonth)
            ->whereYear('sortie_emballages.created_at', $currentYear);

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $sortie_produits = $query->get();
            } else {
                $sortie_produits = $query->where('agences.id', '=', $site_id)->get();
            }


        $sortir_produits = DB::table('sortie_ligne_emballages')
            ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->select('sortie_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
            ->where('sortie_ligne_emballages.Id_Sortie_Emballage', '=', 0)
            ->get();



        $produits = Emballage::orderBy('created_at', 'desc')->get();
        $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();



        $magasins = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select('magasins.*', 'agences.NomAgence')
            ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

        $annees = SortieEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        return view('page.emballage.sortie_emballage.sortie', [
            'sortie_produits' => $sortie_produits,
            'sortir_produits' => $sortir_produits,
            'produits' => $produits,
            'magasins' => $magasins,
            'categories' => $categories,
            'annees' => $annees
        ]);
    }
    public function filterSortieEmballage(Request $request)
    {
        $this->authorize('consulter-sortie-produit');
        try {
            // dd($request);
            $site_id = session()->get('site_id');

            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            $user = Auth::user();
            $user_connecterId = $user->id;

            $query = DB::table('sortie_emballages')
                // ->join('fournisseurs', 'entree_produits.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('agences', 'sortie_emballages.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_emballages.*', 'users.name', 'agences.NomAgence');
                // ->where('agences.id', '=', $site_id);






            if ($annee) {
                $query->whereYear('sortie_emballages.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('sortie_emballages.created_at', $month);
                $query->whereYear('sortie_emballages.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('sortie_emballages.created_at', '>=', $startDate)
                    ->where('sortie_emballages.created_at', '<=', $endDate);
            }
            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $sortie_produits = $query->get();
            } else {
                $sortie_produits = $query->where('agences.id', '=', $site_id)->get();
            }

            // $sortie_produits = $query->get();



            $sortir_produits = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('sortie_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
                ->where('sortie_ligne_emballages.Id_Sortie_Emballage', '=', 0)
                ->get();



            $produits = Emballage::orderBy('created_at', 'desc')->get();
            $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();



            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $annees = SortieEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view('page.emballage.sortie_emballage.sortie', [
                'sortie_produits' => $sortie_produits,
                'sortir_produits' => $sortir_produits,
                'produits' => $produits,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite .");
        }
    }
    public function  create()
    {
        $this->authorize('effectuer-sortie-produit');
        try {
            $site_id = session()->get('site_id');
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $stock_produits = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
                ->where('stock_emballages.Qte_stockee', '>', 0)
                ->where('magasins.agence_id', '=', $site_id)
                ->get();
            return view('page.emballage.sortie_emballage.nouveau', [
                'stock_produits' => $stock_produits,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-sortie-produit');
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'observation' => 'required',
                    'type_sortie' => 'required',
                    'inputs.*.produit' => 'required',
                    'inputs.*.magasin' => 'required',
                    'inputs.*.quantity_out' => 'required',
                ],
                [
                    'observation' => 'Observations requis',
                    'type_sortie' => 'Type sortie requis',
                    'inputs.*.produit' => "produit(s) requis",
                    'inputs.*.magasin' => "magasin(s) requis",
                    'inputs.*.quantity_out' => "Quantite sortie requise(s)",
                ]

            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            $lastReference = SortieEmballage::count();

            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                $lastReference = SortieEmballage::orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Sortie, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';


            //variable de creation entree produit
            $reference_sortie = "1/{$lastDigitOfYear}/SEMB/{$incrementedReferenceNumber}";
            $date_sortie =  Carbon::now();
            $observation = $request->input('observation');
            $type_sortie = $request->input('type_sortie');

            $site_id = session()->get('site_id');


            $sortie_produit = new SortieEmballage();
            $sortie_produit->Date_Sortie = $date_sortie;
            $sortie_produit->Id_Utilisateur = auth()->user()->id;
            $sortie_produit->Reference_Sortie = $reference_sortie;
            $sortie_produit->type_sortie = $type_sortie;
            $sortie_produit->Observations = $observation;
            $sortie_produit->Id_Agence = $site_id;
            $sortie_produit->save();


            foreach ($request->inputs as $value) {
                $produit_formate = explode('-', $value['produit'], 2);
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode('|', $produit_formate[1], 2);
                $reference_produit = $reference_produit_formate[0];
                $magasin_formate = explode('-', $value['magasin'], 2);
                $id_magasin = trim($magasin_formate[0]);


                $produit = Emballage::where('Reference', '=', $reference_produit);

                $stock = StockEmballage::find($id_stock);


                if ($produit && $stock) {
                    $entrer_produit = new SortieLigneEmballage();
                    $entrer_produit->Id_Sortie_Emballage = $sortie_produit->id;
                    $entrer_produit->Id_Stock_Emballage = $stock->id;
                    $entrer_produit->Qte_Sortie = $value['quantity_out'];
                    $entrer_produit->Enregistrer_par = auth()->user()->id;

                    $entrer_produit->save();


                    $quantite_stockee = $stock->Qte_stockee - $value['quantity_out'];
                    $stock->Qte_stockee = $quantite_stockee;
                    $stock->update();
                    //Historiq stock coté sortie

                    $historique_sortie_produit = new StockEmballageHistories();
                    $historique_sortie_produit->Date = $date_sortie;
                    $historique_sortie_produit->agence_id = $site_id;
                    $historique_sortie_produit->Motif = $observation;
                    $historique_sortie_produit->Justificatif = $reference_sortie;
                    $historique_sortie_produit->operation = 'SORTIE';
                    $historique_sortie_produit->type_operation = 'SORTIE';
                    $historique_sortie_produit->Id_Utilisateur = auth()->user()->id;
                    $historique_sortie_produit->Id_Emballage = $stock->Id_Emballage;
                    $historique_sortie_produit->Id_Magasin = $stock->Id_Magasin;
                    $historique_sortie_produit->Quantite = $value['quantity_out'];;
                    $historique_sortie_produit->save();
                }
            }
            return to_route('sortie_emballage')->with('success', 'La sorite a bien été ajoutée');
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite .");
        }
    }

    public function getSortirEmballage(Request $request, $id)
    {

        $this->authorize('consulter-sortie-produit');
        try {
            $sortie_produits = DB::table('sortie_emballages')
                ->join('agences', 'sortie_emballages.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_emballages.*', 'users.name', 'agences.NomAgence')
                ->orderBy('sortie_emballages.id', 'desc')
                ->get();

            $sortir_produits = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('sortie_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'stock_emballages.Qte_stockee', 'stock_emballages.Prix_Achat_Net')
                ->where('sortie_ligne_emballages.Id_Sortie_Emballage', '=', $id)
                ->get();

            $produits = Emballage::orderBy('created_at', 'desc')->get();
            $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();

            $site_id = session()->get('site_id');

            $magasins = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence')
                ->where('agence_id', '=', $site_id)->orderBy('created_at', 'desc')->get();

            $annees = SortieEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view('page.produit.sortie_emballage.sortie', [
                'sortie_produits' => $sortie_produits,
                'sortir_produits' => $sortir_produits,
                'produits' => $produits,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ])
                ->with('success', $sortir_produits->count() . ' produit(s) entré(s) trouvé(s)');
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite .");
        }
    }
    public function imprimer_sortie_emballage(Request $request)
    {
        $this->authorize('imprimer-liste-sorties-produits');

        try {
            $id_sortie = $request->input('id_sortie');
            $reponse = $request->input('reponse');


            $sortie_produits = DB::table('sortie_emballages')
                ->join('agences', 'sortie_emballages.Id_Agence', '=', 'agences.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', '=', 'users.id')
                ->select('sortie_emballages.*', 'agences.NomAgence', 'users.name')
                ->where('sortie_emballages.id', '=', $id_sortie)
                ->get();
            $sortir_produits = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->select('sortie_ligne_emballages.Qte_Sortie', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'magasins.NomMagasin')
                ->where('sortie_ligne_emballages.Id_Sortie_Emballage', '=', $id_sortie)
                ->get();


            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data =  [
                'imageEntetePied' => $imageEntetePied,
                'texteEntetePied' => $texteEntetePied,
                'sortie_produits' => $sortie_produits,
                'sortir_produits' => $sortir_produits,
            ];

            if ($reponse === 'imprimer') {
                // dd('imprimer');
                $htmlContent = view('page.emballage.sortie_emballage.imprimer.imprimer-action', $data)->render();
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
                $prefixe = 'SORTIE_Emballage';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            } else {
                $prefixe = 'sortie_export_emballage';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new SortieEmballageActionPrintExport($data), $nom_excel);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function imprimerlisteSortieEmballage(Request $request)
    {
        $this->authorize('imprimer-liste-sorties-produits');

        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $magasin = $request->input('magasin');
        $categorie = $request->input('categorie');
        $produit = $request->input('produit');
        $submit = $request->input('submit');

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));


        if ($magasin === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->select(
                    'stock_emballages.Id_Magasin',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('stock_emballages.Id_Magasin',)
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();


            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                // ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
            ;

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'stock_emballages.Id_Magasin',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('stock_emballages.Id_Magasin',)
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('categorie_emballages.id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage',)
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('entrer_emballages.Id_Emballage', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }
        if ($magasin === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
            // dd('ok');
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                // ->where('categorie_emballages.id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
            // dd($magasin);
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                // ->where('categorie_emballages.id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                // ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('magasins.id', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin === 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('categorie_emballages.id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                // ->where('magasins.id', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);
            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin !== 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('categorie_emballages.id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('magasins.id', '=', $magasin)
                ->where('emballages.id', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
            $get_magasin = DB::table('sortie_ligne_emballages')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('categorie_emballages.id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $sortie_produit = DB::table('sortie_emballages')
                ->join('sortie_ligne_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('users', 'sortie_emballages.Id_Utilisateur', 'users.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('stock_emballages.Id_Magasin', '=', $magasin)
                // ->where('stock_emballages.Id_Emballage', '=', $produit)
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'sortie_ligne_emballages.Id_Sortie_Emballage',
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(sortie_emballages.Reference_Sortie) as Reference_Sortie'),
                    DB::raw('MAX(sortie_emballages.Observations) as Observations'),
                    DB::raw('MAX(sortie_emballages.Date_Sortie) as Date_Sortie'),
                )
                ->groupBy('sortie_ligne_emballages.Id_Sortie_Emballage')
                ->get();

            $query = DB::table('sortie_ligne_emballages')
                ->join('stock_emballages', 'sortie_ligne_emballages.Id_Stock_Emballage', 'stock_emballages.id')
                ->join('sortie_emballages', 'sortie_ligne_emballages.Id_Sortie_Emballage', 'sortie_emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('magasins.id', '=', $magasin)
                // ->where('emballages.id', '=', $produit)
                ->select(
                    'stock_emballages.Id_Magasin',
                    'sortie_ligne_emballages.id',
                    DB::raw('MAX(stock_emballages.Id_Emballage) as Id_Emballage'),
                    DB::raw('MAX(sortie_ligne_emballages.Id_Sortie_Emballage) as Id_Sortie_Emballage'),
                    DB::raw('MAX(stock_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(emballages.Reference) as Reference'),
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'),
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'),
                    DB::raw('MAX(sortie_ligne_emballages.Qte_Sortie) as Qte_Sortie'),
                )
                ->groupBy('stock_emballages.Id_Magasin', 'sortie_ligne_emballages.id')
                ->whereBetween('sortie_emballages.Date_Sortie', [$date_debut_periode, $date_fin_periode]);

            $all_sortie = $query->get();
            // dd($get_magasin, $sortie_produit, $all_sortie);
            if (count($all_sortie) > 0) {
                $get_request = $all_sortie;
            } else {
                return to_route('sortie_emballage')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($magasin !== 'Tous') {
            $magasin = Magasin::find($magasin);
        }
        if ($produit !== 'Tous') {
            $produit = Emballage::find($produit);
        }
        if ($categorie !== 'Toutes') {
            $categorie = CategorieEmballage::find($categorie);
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

        if ($submit == 'PDF') {



            $options = new Options();
            $options->set('chroot', realpath(''));
            $dompdf = new Dompdf($options);


            $htmlContent = view('page.emballage.sortie_emballage.imprimer.imprimer', $data)->render();


            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isHtmlHeaderFixed', true);
            $options->set('isHtmlFooterFixed', true);

            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream('SORTIE_' . $date_et_heure, array("Attachment" => false));
        }

        if ($submit == 'EXCEL') {
            return Excel::download(new SortieEmballagePeriodeExport($data), 'SORTIE_EMBALLAGE_' . $date_et_heure . '.xlsx');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
