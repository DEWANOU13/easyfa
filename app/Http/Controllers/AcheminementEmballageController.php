<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use Illuminate\Http\Request;
use App\Models\StockEmballage;
use App\Models\PrefixeReference;
use App\Models\AcheminerEmballage;
use App\Models\NotificationAchemi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AcheminementEmballage;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Validator;
use App\Models\NotificationAchemiEmballage;
use App\Exports\AcheminementEmballagePeriodeExport;
use App\Exports\AcheminementEmballageActionPrintExport;


class AcheminementEmballageController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-acheminenment-emballage');
        $annees = AcheminementEmballage::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;


        $site_id = session()->get('site_id');
        $acheminements = DB::table('acheminement_emballages')
            ->join('agences', 'acheminement_emballages.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
            ->select('acheminement_emballages.id', 'acheminement_emballages.Date_acheminement', 'acheminement_emballages.Statut_acheminement', 'acheminement_emballages.Reference_acheminement', 'acheminement_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->orderBy('acheminement_emballages.id', 'desc')
            ->where('acheminement_emballages.Id_Agence_Source', $site_id)
            ->whereMonth('acheminement_emballages.created_at', $currentMonth)
            ->whereYear('acheminement_emballages.created_at', $currentYear)
            ->get();

        return view('page.acheminement_emballage.acheminer.acheminer',
            [
                'acheminements' => $acheminements,
                'annees' => $annees
            ]
        );
    }
    public function filterAchminerEmballage(Request $request)
    {

        try {
            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            // Création de la requête initiale
            if (is_array($Agence_id)) {
                $query =   DB::table('acheminement_emballages')
                ->join('agences', 'acheminement_emballages.Id_Agence_Destination', 'agences.id')
                ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
                ->select('acheminement_emballages.id', 'acheminement_emballages.Date_acheminement', 'acheminement_emballages.Statut_acheminement', 'acheminement_emballages.Reference_acheminement', 'acheminement_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->orderBy('acheminement_emballages.id', 'desc')
                ->where('acheminement_emballages.Id_Agence_Source', $Agence_id);
            } else {
                $query =   DB::table('acheminement_emballages')
                ->join('agences', 'acheminement_emballages.Id_Agence_Destination', 'agences.id')
                ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
                ->select('acheminement_emballages.id', 'acheminement_emballages.Date_acheminement', 'acheminement_emballages.Statut_acheminement', 'acheminement_emballages.Reference_acheminement', 'acheminement_emballages.Observations', 'users.name', 'agences.NomAgence')
                ->orderBy('acheminement_emballages.id', 'desc')
                ->where('acheminement_emballages.Id_Agence_Source', $Agence_id);
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('acheminement_emballages.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('acheminement_emballages.created_at', $month);
                $query->whereYear('acheminement_emballages.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('acheminement_emballages.created_at', '>=', $startDate)
                      ->where('acheminement_emballages.created_at', '<=', $endDate);
            }


            // Exécuter la requête
            $acheminements = $query->get();
            $annees = AcheminementEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');




        return view('page.acheminement_emballage.acheminer.acheminer',
        [
            'acheminements' => $acheminements,
            'annees' => $annees
        ]

        );

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function  create(Request $request)
    {
        $this->authorize('effectuer-acheminement-emballage');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            $site_id = session()->get('site_id');
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stock_emballages')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->select('stock_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin','magasins.agence_id')
                ->where('stock_emballages.Qte_stockee', '>', 0)
                ->where('magasins.agence_id', '=', $site_id)
                ->get();


            $site_id = session()->get('site_id');

            $agence_source = Agence::where('id', $site_id)->get();
            $agence_destination = Agence::where('id', '!=', $site_id)->get();
            // $agences = Agence::orderBy('id', 'desc')->get();

            return view('page.acheminement_emballage.acheminer.nouveau', [
                'stock_produits' => $stock_produits,
                'agence_source' => $agence_source,
                'agence_destination' => $agence_destination
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function getAcheminer(Request $request, $id)
    {


        try {
            $annees = AcheminementEmballage::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');


            $acheminements = DB::table('acheminement_emballages')
            ->join('agences', 'acheminement_emballages.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
            ->select('acheminement_emballages.id', 'acheminement_emballages.Date_acheminement', 'acheminement_emballages.Statut_acheminement', 'acheminement_emballages.Reference_acheminement', 'acheminement_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->get();




            $acheminers = DB::table('acheminer_emballages')
                ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
                ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
                ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
                ->select('acheminer_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
                ->where('acheminer_emballages.Id_Acheminement', '=', $id)
                ->get();

            return view(
                'page.acheminement_emballage.acheminer.acheminer',
                [
                    'acheminers' => $acheminers,
                    'acheminements' => $acheminements,
                    'annees' => $annees
                ]
            );
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        $this->authorize('effectuer-acheminement-emballage');
        // dd($request);
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    // 'fournisseur' => 'required',
                    'magasin_source' => 'required',
                    'magasin_destination' => 'required',
                    'inputs.*.produit' => 'required',
                    //  'inputs.*.designation' => 'required',
                    'inputs.*.magasin' => 'required',
                    // 'inputs.*.quantity' => 'required',
                    'inputs.*.quantity_transfer' => 'required',
                ],
                [
                    // 'fournisseur' => 'Fournisseur requis',
                    'magasin_source' => 'Magasin source required',
                    'magasin_destination' => 'Magasin destination required',
                    'observation' => 'Observations requis',
                    'inputs.*.produit' => "produit(s) requis",
                    // 'inputs.*.designation' => "designation(s) requise(s)",
                    'inputs.*.magasin' => "magasin(s) requis",
                    // 'inputs.*.quantity' => "quantite(s) requise(s)",
                    'inputs.*.quantity_transfer' => "Quantite à transférer requise(s)",
                ]

            );

            if ($validator->fails()) {
                // Si la validation échoue, retournez à la page précédente avec les erreurs
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            // Obtenez le dernier chiffre de l'année actuelle
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            // $lastReference = TransfertProduit::count();
            $lastReference = AcheminementEmballage::where('Id_Agence_Source', '=', session()->get('site_id'))->count();
            $site_id = session()->get('site_id');
            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = TransfertProduit::orderBy('id', 'desc')->first();
                $lastReference = AcheminementEmballage::where('Id_Agence_Source', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_acheminement, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->approvisionnement ?? '';
            //variable de creation entree produit
            $Reference_acheminement = "{$site_id}/{$lastDigitOfYear}/ACHEMB/{$incrementedReferenceNumber}";
            $Date_acheminement =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $id_agence_source = $request->input('magasin_source');
            $id_agence_destination = $request->input('magasin_destination');

            // dd($id_agence_destination, $id_agence_source);


            $Acheminement = new AcheminementEmballage();
            $Acheminement->Date_acheminement = $Date_acheminement;
            $Acheminement->Id_Utilisateur = auth()->user()->id;
            $Acheminement->Reference_acheminement = $Reference_acheminement;
            $Acheminement->Observations = $observation;
            $Acheminement->Statut_acheminement = 'EN COURS';
            $Acheminement->Id_Agence_Source = $id_agence_source;
            $Acheminement->Id_Agence_Destination = $id_agence_destination;
            $Acheminement->Enregistrer_par = auth()->user()->id;
            $Acheminement->save();

            $quantite_total_approv = 0;


            foreach ($request->inputs as $value) {

                $produit_formate = explode(' ', $value['produit'], 2);
                $reference_produit = $produit_formate[1];
                $id_stock = trim($produit_formate[0]);
                $reference_produit_formate = explode(' ', $produit_formate[1], 2);
                // $magasin_formate = explode('-', $value['magasin'], 2);
                // $id_magasin = trim($magasin_formate[0]);
                // dd($id_stock);

               // $produit = Produit::where('Reference', '=', $reference_produit_formate[0])->first();
                // dd($produit);

                $stock = StockEmballage::find($id_stock);
                // dd($stock);


                // dd($stock_source->id, $stock_destination->id);
                $new_quantite_stock = $stock->Qte_stockee - $value['quantity_transfer'];
                $stock->Qte_stockee = $new_quantite_stock;
                $stock->update();

                // Concerne le magasin destination

                $acheminer = new AcheminerEmballage();
                $acheminer->Id_Acheminement  = $Acheminement->id;
                $acheminer->Id_Stock_Emballage  = $stock->id;
                $acheminer->Prix_Revient = $stock->Prix_Achat_Net;
                $acheminer->Qte_acheminee = $value['quantity_transfer'];
                $acheminer->Qte_Receptionnee = 0;
                $acheminer->save();


                // L'historik
                $historique_entree_produit = new StockEmballageHistories();
                $historique_entree_produit->Date = $Date_acheminement;
                $historique_entree_produit->agence_id = $id_agence_source;
                $historique_entree_produit->Motif = 'Sortie emballage par acheminement';
                $historique_entree_produit->Justificatif = $Reference_acheminement;
                $historique_entree_produit->operation = 'SORTIE';
                $historique_entree_produit->type_operation = 'SORTIE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Emballage  = $stock->Id_Emballage;
                $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];
                $historique_entree_produit->save();

                $quantite_total_approv += $value['quantity_transfer'];
            }

            $notification_approv = new NotificationAchemiEmballage();
            $notification_approv->Id_Acheminement = $Acheminement->id;
            $notification_approv->Id_Agence_Source = $id_agence_source;
            $notification_approv->Id_Agence_Destination = $id_agence_destination;
            $notification_approv->Qte_acheminee = $quantite_total_approv;
            $notification_approv->Motif = 'Acheminement emballage';
            $notification_approv->save();


            return to_route('acheminer_emballage')->with('success', 'L\' acheminement a bien été effectué');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function acheminement_imprimer_action(Request $request)
    {
        try {
            $id_acheminement = $request->input('id_acheminement');
            $reponse = $request->input('reponse');
            if (empty($id_acheminement)) {
                return redirect()->back()->with('warning', 'ID de transfert non fourni');
            }

            $acheminement = DB::table('acheminement_emballages')
            ->join('agences', 'acheminement_emballages.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
            ->select('acheminement_emballages.id', 'acheminement_emballages.Date_acheminement', 'acheminement_emballages.Statut_acheminement', 'acheminement_emballages.Reference_acheminement', 'acheminement_emballages.Observations', 'users.name', 'agences.NomAgence')
            ->where('acheminement_emballages.id', $id_acheminement)
            ->get();


            if ($acheminement->isEmpty()) {
                return redirect()->back()->with('warning', 'Acheminement non trouvé');
            }

            $acheminer = DB::table('acheminer_emballages')
            ->join('stock_emballages', 'acheminer_emballages.Id_Stock_Emballage', '=', 'stock_emballages.id')
            ->join('emballages', 'stock_emballages.Id_Emballage', '=', 'emballages.id')
            ->join('magasins', 'stock_emballages.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_emballages', 'emballages.Categorie_emballage_id', '=', 'categorie_emballages.id')
            ->select('acheminer_emballages.*', 'emballages.Reference', 'emballages.Nom_emballage as Designation', 'magasins.NomMagasin', 'categorie_emballages.Libelle')
            ->where('acheminer_emballages.Id_Acheminement', '=', $id_acheminement)
            ->get();




            // dd($approvisionnement, $approvisionners );
            if ($acheminer->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucun produit acheminé trouvé');
            }




            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $data = [
                'texteEntetePied' => $texteEntetePied,
                'imageEntetePied' => $imageEntetePied,
                'acheminement' => $acheminement,
                'acheminer' => $acheminer,
            ];

            if($reponse === 'imprimer'){
                $htmlContent = view('page.acheminement_emballage.acheminer.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'Acheminement_emballage';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if($reponse === 'exporter'){
                $prefixe = 'Acheminement_emballage';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new AcheminementEmballageActionPrintExport($data), $nom_excel);
            }



            } catch (Exception $e) {
                // Redirection avec message d'erreur
                return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
            }
    }
    public function imprimer_acheminement(Request $request)
    {
        $this->authorize('imprimer-liste-transfert');
        $site_id = session()->get('site_id');
       // try {

            $debut_periode = $request->input('date_debut_periode');
            $fin_periode = $request->input('date_fin_periode');
            $agence = $request->input('agence');
            $submit = $request->input('submit');

            $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
            $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

            $query = DB::table('acheminement_emballages')
                ->join('agences', 'acheminement_emballages.Id_Agence_Source', 'agences.id')
                ->join('agences as agences2', 'acheminement_emballages.Id_Agence_Destination', 'agences2.id')
                ->join('users', 'acheminement_emballages.Id_Utilisateur', 'users.id')
                ->whereBetween('acheminement_emballages.Date_acheminement', [$date_debut_periode, $date_fin_periode])
                ->select('acheminement_emballages.*','users.name', 'agences.NomAgence', 'agences2.NomAgence as agence_destination');

            if($agence === 'Tous') {

                $liste_acheminements = $query->get();
                //dd($liste_acheminements);
                if (count($liste_acheminements) > 0) {
                    $get_request = $liste_acheminements;
                } else {
                    return to_route('acheminer_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }
            if($agence !== 'Tous') {
                $liste_acheminements = $query->where('acheminements.Id_Agence_Source', $agence)->get();


                if (count($liste_acheminements) > 0) {
                    $get_request = $liste_acheminements;
                } else {
                    return to_route('acheminer_emballage')->with('error', 'Aucunes données trouvées!');
                }
            }
            if ($agence !== 'Tous') {
                $infoAgence = Agence::where('id', $agence)->first();
            } else {
                $infoAgence = Agence::where('id', 0)->first();
            }



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $data = [
                'texteEntetePied' => $texteEntetePied,
                'debut_periode' => $debut_periode,
                'fin_periode' => $fin_periode,
                'getAcheminements' => $get_request,
                'infoAgence' => $infoAgence,
                'imageEntetePied' => $imageEntetePied
            ];


            $date_et_heure = date('Ymd_His');

            if($submit == 'PDF'){



                $options = new Options();
                $options->set('chroot', realpath(''));
                $dompdf = new Dompdf($options);


                $htmlContent = view('page.acheminement_emballage.acheminer.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('acheminements_emballage_'.$date_et_heure, array("Attachment" => false));
            }
            if($submit == 'EXCEL'){
                return Excel::download(new AcheminementEmballagePeriodeExport($data), 'acheminements_emballage_'.$date_et_heure.'.xlsx');
            }

       /*   } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }
}
