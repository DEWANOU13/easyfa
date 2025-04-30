<?php

namespace App\Http\Controllers\emballage;

use App\Exports\EntreeEmballageActionPrintExport;
use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Emballage;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Exports\EntreeExport;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Models\EntreeEmballage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use App\Models\CategorieEmballage;
use Illuminate\Support\Facades\DB;
use App\Models\EntreLigneEmballage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Exports\EntreeEmballageListeExport;
use App\Imports\EntreeEmballageImport;
use App\Imports\EntreeImport;

class EntreeEmballageController extends Controller
{
    public function  index(Request $request)
    {
        $this->authorize('voir-liste-entree-emballage');
        $site_id = session()->get('site_id');

        $magasin_ids = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->where('agences.id', '=', $site_id)
            ->orderBy('magasins.created_at', 'desc')
            ->pluck('magasins.id');


        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->entre_produit ?? '';
        if ($prefix == null) {
            return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
        }
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;




        $query = DB::table('entree_emballages')
            ->leftJoin('fournisseurs', function ($join) {
                $join->on('entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                    ->whereNotNull('entree_emballages.Id_Fournisseur');
            })
            ->leftJoin('agences as source_agences', function ($join) {
                $join->on('entree_emballages.Id_Agence_source', '=', 'source_agences.id')
                    ->whereNull('entree_emballages.Id_Fournisseur');
            })
            ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
            ->join('agences', 'entree_emballages.Id_Agence', '=', 'agences.id')
            ->select(
                'entree_emballages.*',
                DB::raw('COALESCE(fournisseurs.DenominationSociale, source_agences.NomAgence) as DenominationSociale'),
                'users.name',
                'agences.NomAgence'
            )
            ->whereMonth('entree_emballages.created_at', $currentMonth)
            ->whereYear('entree_emballages.created_at', $currentYear)
            // ->where('agences.id', $site_id)
            ->orderBy('entree_emballages.id', 'desc');

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $entree_emballages = $query->get();
            } else {
                $entree_emballages = $query->where('agences.id', '=', $site_id)->get();
            }


        $entre_ligne_emballages = DB::table('entre_ligne_emballages')
            ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
            ->select('entre_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage', 'magasins.NomMagasin')
            ->where('entre_ligne_emballages.Id_Entree_Emballage', '=', 0)
            // ->whereIn('magasins.id', $magasin_ids)
            ->get();

        $annees = EntreLigneEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $produits = Emballage::orderBy('created_at', 'desc')->get();
        $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
        // $magasins = Magasin::orderBy('created_at', 'desc')->get();
        $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();


        $magasins = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select('magasins.*', 'agences.NomAgence')
            ->whereIn('magasins.id', $magasin_ids)
            ->orderBy('created_at', 'desc')->get();




        return view('page.emballage.entree_emballage.entree', [
            'entree_produits' => $entree_emballages,
            'entrer_produits' => $entre_ligne_emballages,
            'produits' => $produits,
            'fournisseurs' => $fournisseurs,
            'magasins' => $magasins,
            'categories' => $categories,
            'annees' => $annees
        ]);
        /* } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }

    public function  Create(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->sortie_produit ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $produits = Emballage::where('Statut_emballage', '=', '1')->where('type_emb','=','recuperable')->pluck('Nom_emballage', 'Reference');
            //dd($produits);
            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->where('Statut_fournisseur', '=', '1')->get();

            if (is_array(getIdAgenceByUser())) {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')->get();
            } else {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')
                    ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
            }

            $listeAgence = Agence::all();



            // $magasins = Magasin::
            return view('page.emballage.entree_emballage.nouveau', [
                'produits' => $produits,
                'fournisseurs' => $fournisseurs,
                'magasins' => $magasins,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function store(Request $request)
    {
        $this->authorize('effectuer-entrer-produit');
        try {
            // dd($request);

            $validator = Validator::make(
                $request->all(),
                [
                    'fournisseur' => 'required',
                    'observation' => 'required',
                    'inputs.*.produit' => 'required',
                    //  'inputs.*.designation' => 'required',
                    'inputs.*.magasin' => 'required',
                    'inputs.*.quantity' => 'required',
                    'inputs.*.prix_achat' => 'required',
                ],
                [
                    'fournisseur' => 'Fournisseur requis',
                    'observation' => 'Observations requis',
                    'inputs.*.produit' => "produit(s) requis",
                    // 'inputs.*.designation' => "designation(s) requise(s)",
                    'inputs.*.magasin' => "magasin(s) requis",
                    'inputs.*.quantity' => "quantite(s) requise(s)",
                    'inputs.*.prix_achat' => "prix achat(s) requis",
                ]

            );

            if ($validator->fails()) {
                // Si la validation échoue, retournez à la page précédente avec les erreurs
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            $lastReference = EntreeEmballage::count();

            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = EntreeEmballage::orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_Entree, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }

            $site_id = session()->get('site_id');

            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->entre_produit ?? '';

            // dd($site_id);
            //variable de creation entree produit
            $reference_entree = "{$site_id}/{$lastDigitOfYear}/EEMB/{$incrementedReferenceNumber}";
            $date_entree =  Carbon::now();
            $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');


            // dd(auth()->user()->id);

            $entree_produit = new EntreeEmballage();
            $entree_produit->Date_Entree = $date_entree;
            $entree_produit->Id_Utilisateur = auth()->user()->id;
            $entree_produit->Reference_Entree = $reference_entree;
            $entree_produit->Observations = $observation;
            $entree_produit->Id_Agence = $site_id;
            $entree_produit->Id_Fournisseur = $fournisseur;
            $entree_produit->save();


            foreach ($request->inputs as $value) {

                $emballage = Emballage::where('Reference', '=', $value['produit'])->first();

                $magasin_formate = explode('-', $value['magasin'], 2);
                $id_magasin = trim($magasin_formate[0]);
                // dd(trim($id_magasin[0]));
                $entrer_produit = new EntreLigneEmballage();
                $entrer_produit->Id_Entree_Emballage = $entree_produit->id;
                $entrer_produit->Id_Emballage = $emballage->id;
                $entrer_produit->Id_Magasin = $id_magasin;
                $entrer_produit->Qte_Entree = $value['quantity'];
                $entrer_produit->Prix_Achat_Net = $value['prix_achat'];
                $entrer_produit->save();


                //Historiq stock coté entrée
                $historique_entree_produit = new StockEmballageHistories();
                $historique_entree_produit->Date = $date_entree;
                $historique_entree_produit->agence_id = $site_id;
                $historique_entree_produit->Motif = $observation;
                $historique_entree_produit->Justificatif = $reference_entree;
                $historique_entree_produit->operation = 'ENTREE';
                $historique_entree_produit->type_operation = 'ENTREE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Emballage = $emballage->id;
                $historique_entree_produit->Id_Magasin = $id_magasin;
                $historique_entree_produit->Quantite = $value['quantity'];;
                $historique_entree_produit->save();

                $stock = StockEmballage::where('Id_Emballage', '=', $emballage->id)->where('Id_Magasin', '=', $id_magasin)->first();

                if ($stock) {
                    $quantite_stockee = $stock->Qte_stockee + $value['quantity'];
                    $prix_achat_net = (($stock->Prix_Achat_Net * $stock->Qte_stockee) + ($value['prix_achat'] * $value['quantity'])) / ($stock->Qte_stockee + $value['quantity']);
                    $stock->Qte_stockee = $quantite_stockee;
                    $stock->Prix_Achat_Net = $prix_achat_net;
                    $stock->update();
                } else {
                    $stocker_produit = new StockEmballage();
                    $stocker_produit->Id_Emballage = $emballage->id;
                    $stocker_produit->Id_Magasin = $id_magasin;
                    $stocker_produit->Qte_stockee = $value['quantity'];
                    $stocker_produit->Prix_Achat_Net = $value['prix_achat'];
                    $stocker_produit->save();
                }
            }
            return to_route('entree_emballage')->with('success', 'L\'entree a bien été ajoutée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getEntrerEmballage($id)
    {
        $this->authorize('consulter-entree-produit');

        try {
            $entree_emballages = DB::table('entree_emballages')
                ->join('fournisseurs', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'entree_emballages.Id_Agence', '=', 'agences.id')
                ->select('entree_emballages.*', 'fournisseurs.DenominationSociale as DenominationSociale', 'users.name', 'agences.NomAgence')
                ->orderBy('entree_emballages.id', 'desc')
                ->get();

            $entre_ligne_emballages = DB::table('entre_ligne_emballages')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('entre_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
                ->where('entre_ligne_emballages.Id_Entree_Emballage', '=', $id)
                ->get();

            $produits = Emballage::orderBy('created_at', 'desc')->get();
            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
            $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieEmballage::orderBy('created_at', 'desc')->get();
            $annees = EntreeEmballage::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');


            return view('page.emballage.entree_emballage.entree', [
                'entree_produits' => $entree_emballages,
                'entrer_produits' => $entre_ligne_emballages,
                'produits' => $produits,
                'fournisseurs' => $fournisseurs,
                'magasins' => $magasins,
                'categories' => $categories,
                'annees' => $annees
            ])
                ->with('success', $entre_ligne_emballages->count() . ' produit(s) entré(s) trouvé(s)');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite.");
        }
    }

    public function filterEntreeEmballage(Request $request)
    {
        $this->authorize('consulter-entree-produit');
        try {

            $site_id = session()->get('site_id');

            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $year = date('Y'); // Ou une autre année si nécessaire
            $annee = $request->query('annee');

            $user = Auth::user();
            $user_connecterId = $user->id;

            $query = DB::table('entree_emballages')
                ->join('fournisseurs', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('agences', 'entree_emballages.Id_Agence', '=', 'agences.id')
                ->select('entree_emballages.*', 'fournisseurs.DenominationSociale as DenominationSociale', 'users.name', 'agences.NomAgence')
                ->orderBy('entree_emballages.id', 'desc');

            $entre_ligne_emballages = DB::table('entre_ligne_emballages')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('entre_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin')
                ->where('entre_ligne_emballages.Id_Entree_Emballage', '=', 0)
                ->get();

            $produits = Produit::orderBy('created_at', 'desc')->get();

            $fournisseurs = Fournisseur::orderBy('created_at', 'desc')->get();
            // $magasins = Magasin::orderBy('created_at', 'desc')->get();
            $categories = CategorieProduit::orderBy('created_at', 'desc')->get();

            if (is_array(getIdAgenceByUser())) {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')->get();
            } else {
                $magasins = DB::table('magasins')
                    ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('magasins.*', 'agences.NomAgence')
                    ->where('agence_id', '=', getIdAgenceByUser())->orderBy('created_at', 'desc')->get();
            }

            if ($month) {
                $query->whereMonth('entree_emballages.created_at', $month)
                    ->whereYear('entree_emballages.created_at', $year);
            }

            if ($startDate) {
                $query->whereDate('entree_emballages.created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('entree_emballages.created_at', '<=', $endDate);
            }

            if ($annee) {
                $query->whereYear('entree_emballages.created_at', $annee);
            }

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $entree_emballages = $query->get();
            } else {
                $entree_emballages = $query->where('agences.id', '=', $site_id)->get();
            }

            // $entree_emballages = $query->get();

            $annees = EntreeEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        return view('page.emballage.entree_emballage.entree', [
            'entree_produits' => $entree_emballages,
            'entrer_produits' => $entre_ligne_emballages,
            'produits' => $produits,
            'fournisseurs' => $fournisseurs,
            'magasins' => $magasins,
            'categories' => $categories,
            'annees' => $annees
        ]);


        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite . ");
        }
    }

    public function imprimer_entree_emballage(Request $request)
    {
        $this->authorize('imprimer-liste-entrees-produits');
        try {
            $id_entree = $request->input('id_entree');
            $reponse = $request->input('reponse');

            $entree_emballages = DB::table('entree_emballages')
                ->join('agences', 'entree_emballages.Id_Agence', '=', 'agences.id')
                ->join('fournisseurs', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->select('entree_emballages.*', 'agences.NomAgence', 'users.name', 'fournisseurs.DenominationSociale')
                ->where('entree_emballages.id', '=', $id_entree)
                ->get();

            $entre_ligne_emballages = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', 'magasins.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', 'categorie_emballages.id')
                ->select('entre_ligne_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'categorie_emballages.Libelle', 'magasins.NomMagasin', 'entree_emballages.Date_Entree')
                ->where('entre_ligne_emballages.Id_Entree_Emballage', '=', $id_entree)
                ->get();

            //  dd($entree_produits, $entrer_produits);
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data =  [
                'entree_emballages' => $entree_emballages,
                'entre_ligne_emballages' => $entre_ligne_emballages,
                'texteEntetePied' => $texteEntetePied,
                'imageEntetePied' => $imageEntetePied,
            ];

            if ($reponse === 'imprimer') {

                // dd($reponse);

                $htmlContent = view('page.emballage.entree_emballage.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'ENTREE_EMBALLAGE';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            } else {

                $prefixe = 'entree_emballage_export';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new EntreeEmballageActionPrintExport($data), $nom_excel);
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function imprimerEntreeEmballagePeriode(Request $request)
    {
        $this->authorize('imprimer-liste-entrees-produits');
        //try {

        $reponse = $request->input('response');

        $debut_periode = $request->input('date_debut_periode');
        $fin_periode = $request->input('date_fin_periode');
        $magasin = $request->input('magasin');
        $fournisseur = $request->input('fournisseur');
        $categorie = $request->input('categorie');
        $produit = $request->input('produit');

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        if ($fournisseur === 'Tous' && $magasin === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur',)
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Entree_Emballage) as Id_Entree_Emballage'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin',)
                ->get();

            // if(count($get_magasin) <= 0){
            //     return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            // }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('magasin.Id_Magasin', '=', $magasin)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
        }

        if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                // ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_fournisseur_entree_emballages) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('magasin.Id_Magasin', '=', $magasin)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('reglement')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('magasin.Id_Magasin', '=', $magasin)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);

        }

        if ($fournisseur === 'Tous' && $magasin === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                    DB::raw('MAX(emballages.Categorie_emballage_id) as Categorie_emballage_id '),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    DB::raw('MAX(emballages.Categorie_emballage_id ) as Categorie_emballage_id'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);

        }

        if ($fournisseur === 'Tous' && $magasin === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur !== 'Tous' && $magasin !== 'Tous' && $categorie === 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur === 'Tous' && $magasin === 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id ', '=', 'categorie_emballages.id')
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('emballages.Categorie_emballage_id ', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('emballages.Categorie_emballage_id ', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            $all_entree = $query->get();
            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0 && count($all_entree)) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id ', '=', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id ', '=', 'categorie_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');

            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $categorie !== 'Toutes' && $produit === 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id ', '=', $categorie)
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                // ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');


            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $categorie === 'Toutes' && $produit !== 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('emballages.Categorie_emballage_id ', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                // ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                // ->where('emballages.Categorie_emballage_id ', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                // ->where('categorie_emballages.id', '=', $categorie)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');

            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0  && $get_magasin) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);
        }

        if ($fournisseur !== 'Tous' && $magasin !== 'Tous' && $categorie !== 'Toutes' && $produit !== 'Tous') {
            // dd('premier');
            $get_fournisseur = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id ', '=', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    DB::raw('MAX(fournisseurs.DenominationSociale) as Denomination_sociale'),
                    DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur', 'entre_ligne_emballages.Id_Entree_Emballage')
                ->get();

            $get_fournisseur_entree_emballages = DB::table('fournisseurs')
                ->join('entree_emballages', 'entree_emballages.Id_Fournisseur', '=', 'fournisseurs.id')
                // ->join('users', 'entree_emballages.Id_Utilisateur', '=', 'users.id')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->where('emballages.Categorie_emballage_id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->where('entre_ligne_emballages.Id_Emballage ', '=', $produit)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    DB::raw(
                        'MAX(fournisseurs.DenominationSociale) as Denomination_sociale'
                    ),
                    // DB::raw('MAX(entree_emballages.Reference_Entree) as Reference_Entree'),
                    // DB::raw('MAX(entree_emballages.Date_Entree) as Date_Entree'),
                    // DB::raw('MAX(entre_ligne_emballages.Id_Magasin) as Id_Magasin'),
                    // DB::raw('MAX(users.name) as name'),
                )
                ->groupBy('entree_emballages.Id_Fournisseur')
                ->get();

            $get_magasin = DB::table('magasins')
                ->join('entre_ligne_emballages', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->select(
                    'entre_ligne_emballages.Id_Magasin',
                    'entree_emballages.Id_Fournisseur',
                    DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    DB::raw('MAX(entree_emballages.Id_Fournisseur) as Id_Fournisseur')
                )
                ->groupBy('entre_ligne_emballages.Id_Magasin', 'entree_emballages.Id_Fournisseur')
                ->get();

            if (count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            $query = DB::table('entre_ligne_emballages')
                ->join('entree_emballages', 'entre_ligne_emballages.Id_Entree_Emballage', '=', 'entree_emballages.id')
                ->join('emballages', 'entre_ligne_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'entre_ligne_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select(
                    'entree_emballages.Id_Fournisseur',
                    'entre_ligne_emballages.Id_Magasin',
                    'entre_ligne_emballages.Id_Emballage',
                    'entre_ligne_emballages.Id_Entree_Emballage',
                    // 'emballages.Categorie_emballage_id ',
                    DB::raw('MAX(emballages.Reference) as Reference'), // Utilisation de MAX() pour la date
                    DB::raw('MAX(emballages.Nom_emballage) as Designation'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(categorie_emballages.Libelle) as Libelle'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Qte_Entree) as Qte_Entree'), // Utilisation de MAX() pour la référence de règlement
                    DB::raw('MAX(entre_ligne_emballages.Prix_Achat_Net) as Prix_Achat_Net'), // Utilisation de MAX() pour la référence de règlement
                    // DB::raw('SUM(detail_reglements.Montant_Regle) as total')
                )
                ->whereBetween('entree_emballages.Date_Entree', [$date_debut_periode, $date_fin_periode])
                ->where('entre_ligne_emballages.Id_Emballage', '=', $produit)
                ->where('categorie_emballages.id', '=', $categorie)
                ->where('entre_ligne_emballages.Id_Magasin', '=', $magasin)
                ->where('entree_emballages.Id_Fournisseur', '=', $fournisseur)
                ->groupBy('entre_ligne_emballages.Id_Magasin',  'entre_ligne_emballages.Id_Emballage', 'entre_ligne_emballages.Id_Entree_Emballage', 'entree_emballages.Id_Fournisseur');

            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0  && $get_magasin) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }

            if (count($get_fournisseur) <= 0 && count($get_fournisseur_entree_emballages) <= 0 && count($get_magasin) <= 0) {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            $all_entree = $query->get();
            // dd($get_fournisseur, $all_entree, $get_magasin, $get_fournisseur_entree_emballages);
            if (count($all_entree) > 0) {
                $get_request = $all_entree;
            } else {
                return to_route('page.entree_emballage.entree')->with('error', 'Aucunes données trouvées!');
            }
            // dd($get_fournisseur, $all_entree, $get_magasin);

        }

        // dd($fournisseur, $produit, $categorie);

        if ($fournisseur !== 'Tous') {
            $fournisseur = Fournisseur::find($fournisseur);
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
        // dd($magasin, $fournisseur, $produit, $categorie);
        // dd($debut_periode, $fin_periode);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        // dd($imageEntetePied,);
        $data = [
            'imageEntetePied' => $imageEntetePied,
            'debut_periode' => $debut_periode,
            'fin_periode' => $fin_periode,
            'fournisseur' => $fournisseur,
            'produit' => $produit,
            'magasin' => $magasin,
            'categorie' => $categorie,
            'getEntree' => $get_request,
            'getFournisseur' => $get_fournisseur,
            'getMagasin' => $get_magasin,
            'getFournisseurEntreeProduits' => $get_fournisseur_entree_emballages

        ];

        if ($reponse === 'imprimer') {
            // Configurer les options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));
            $options->set('isRemoteEnabled', true);

            $htmlContent = view('page.produit.entree_emballage.imprimer.imprimer', $data)->render();

            // Instancier Dompdf avec les options configurées
            $dompdf = new Dompdf($options);

            // Charger le contenu HTML
            $dompdf->loadHtml($htmlContent);

            // Configurer la taille et l'orientation du papier
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Afficher le PDF dans le navigateur
            $prefixe = 'ENTREE';
            $date_et_heure = date('Ymd_His');
            $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
            return $dompdf->stream($nom_pdf, ['Attachment' => false]);
        }
        if ($reponse === 'exporter') {




            $prefixe = 'historique_stock_export';
            $date_et_heure = date('Ymd_His');
            $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

            return Excel::download(new EntreeEmballageListeExport($data), $nom_excel);
        }



        // return view('page.produit.entree.imprimer.imprimer', $data);

        // } catch (Exception $e) {
        //     // Redirection avec message d'erreur
        //     return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        // }
    }

    public function import(Request $request)
    {
        // dd('bien ici', $request->all());
        $this->authorize('effectuer-entrer-produit');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'fournisseur' => 'required',
        ]);

        $fournisseur = $request->input('fournisseur');


        try {
            // Lancer l'importation du fichier Excel
            Excel::import(new EntreeEmballageImport($fournisseur), $request->file('file'));

            return redirect()->back()->with('success', 'Importation réussie.');
        } catch (Exception $e) {
            // Attraper l'exception levée dans StockImport et afficher un message d'erreur
            return redirect()->back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        }
    }
}
