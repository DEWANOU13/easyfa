<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Produit;
use App\Models\Acheminer;
use App\Models\Acheminement;
use Illuminate\Http\Request;
use App\Models\StockHistories;
use App\Models\PrefixeReference;
use App\Models\NotificationAchemi;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationApprovs;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Exports\AcheminementPeriodeExport;
use App\Exports\AcheminementActionPrintExport;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class AcheminementController extends Controller
{
    public function index()
    {
        $site_id = session()->get('site_id');
        $acheminements = DB::table('acheminements')
            ->join('agences', 'acheminements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminements.Id_Utilisateur', 'users.id')
            ->select('acheminements.id', 'acheminements.Date_acheminement', 'acheminements.Statut_acheminement', 'acheminements.Reference_acheminement', 'acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->orderBy('acheminements.id', 'desc')
            ->where('acheminements.Id_Agence_Source', $site_id)
            ->get();

            $annees = Acheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        return view('page.acheminement.acheminer.acheminer',
            [
                'acheminements' => $acheminements,
                'annees' => $annees,
            ]
        );
    }

    public function  create(Request $request)
    {
        $this->authorize('effectuer-acheminement');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->transfert_produit ?? '';
            $site_id = session()->get('site_id');
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            // $produits = Produit::orderBy('created_at', 'desc')->get();
            $stock_produits = DB::table('stocks')
                ->join('produits', 'stocks.Id_produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin','magasins.agence_id')
                ->where('stocks.Qte_stockee', '>', 0)
                ->where('magasins.agence_id', '=', $site_id)
                ->get();

            $site_id = session()->get('site_id');

            $agence_source = Agence::where('id', $site_id)->get();
            $agence_destination = Agence::where('id', '!=', $site_id)->get();
            // $agences = Agence::orderBy('id', 'desc')->get();

            return view('page.acheminement.acheminer.nouveau', [
                'stock_produits' => $stock_produits,
                'agence_source' => $agence_source,
                'agence_destination' => $agence_destination
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        $this->authorize('effectuer-acheminement');
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
            $lastReference = Acheminement::where('Id_Agence_Source', '=', session()->get('site_id'))->count();
            $site_id = session()->get('site_id');
            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '00001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                // $lastReference = TransfertProduit::orderBy('id', 'desc')->first();
                $lastReference = Acheminement::where('Id_Agence_Source', '=', session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_acheminement, -5); // Obtenez les 5 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 5, '0', STR_PAD_LEFT);
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->approvisionnement ?? '';
            //variable de creation entree produit
            $Reference_acheminement = "{$site_id}/{$lastDigitOfYear}/ACH/{$incrementedReferenceNumber}";
            $Date_acheminement =  Carbon::now();
            // $fournisseur = $request->input('fournisseur');
            $observation = $request->input('observation');
            $id_agence_source = $request->input('magasin_source');
            $id_agence_destination = $request->input('magasin_destination');

            // dd($id_agence_destination, $id_agence_source);


            $Acheminement = new Acheminement();
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

                $produit = Produit::where('Reference', '=', $reference_produit_formate[0])->first();
                // dd($produit);

                $stock = Stock::find($id_stock);
                // dd($stock);


                // dd($stock_source->id, $stock_destination->id);
                $new_quantite_stock = $stock->Qte_stockee - $value['quantity_transfer'];
                $stock->Qte_stockee = $new_quantite_stock;
                $stock->update();

                // Concerne le magasin destination

                $acheminer = new Acheminer();
                $acheminer->Id_Acheminement  = $Acheminement->id;
                $acheminer->Id_Stock  = $stock->id;
                $acheminer->Prix_Revient = $stock->Prix_Achat_Net;
                $acheminer->Qte_acheminee = $value['quantity_transfer'];
                $acheminer->Qte_Receptionnee = 0;
                $acheminer->save();


                // L'historik
                $historique_entree_produit = new StockHistories();
                $historique_entree_produit->Date = $Date_acheminement;
                $historique_entree_produit->agence_id = $id_agence_source;
                $historique_entree_produit->Motif = 'Sortie produit par acheminement';
                $historique_entree_produit->Justificatif = $Reference_acheminement;
                $historique_entree_produit->operation = 'SORTIE';
                $historique_entree_produit->type_operation = 'SORTIE';
                $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                $historique_entree_produit->Id_Produit = $stock->Id_Produit;
                $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                $historique_entree_produit->Quantite = $value['quantity_transfer'];
                $historique_entree_produit->save();

                $quantite_total_approv += $value['quantity_transfer'];
            }

            $notification_approv = new NotificationAchemi();
            $notification_approv->Id_Acheminement = $Acheminement->id;
            $notification_approv->Id_Agence_Source = $id_agence_source;
            $notification_approv->Id_Agence_Destination = $id_agence_destination;
            $notification_approv->Qte_acheminee = $quantite_total_approv;
            $notification_approv->Motif = 'Acheminement';
            $notification_approv->save();


            return to_route('acheminer')->with('success', 'L\' acheminement a bien été effectué');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function getAcheminer(Request $request, $id)
    {


        try {
            $annees = Acheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');


            $acheminements = DB::table('acheminements')
            ->join('agences', 'acheminements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminements.Id_Utilisateur', 'users.id')
            ->select('acheminements.id', 'acheminements.Date_acheminement', 'acheminements.Statut_acheminement', 'acheminements.Reference_acheminement', 'acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->get();

            $acheminers = DB::table('acheminers')
                ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->select('acheminers.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
                ->where('acheminers.Id_Acheminement', '=', $id)
                ->get();

                $annees = Acheminement::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            return view(
                'page.acheminement.acheminer.acheminer',
                [
                    'acheminers' => $acheminers,
                    'acheminements' => $acheminements,
                    'annees' => $annees,
                ]
            );
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

            $acheminement = DB::table('acheminements')
            ->join('agences', 'acheminements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminements.Id_Utilisateur', 'users.id')
            ->select('acheminements.id', 'acheminements.Date_acheminement', 'acheminements.Statut_acheminement', 'acheminements.Reference_acheminement', 'acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->where('acheminements.id', $id_acheminement)
            ->get();


            if ($acheminement->isEmpty()) {
                return redirect()->back()->with('warning', 'Acheminement non trouvé');
            }

            $acheminer = DB::table('acheminers')
            ->join('stocks', 'acheminers.Id_Stock', '=', 'stocks.id')
            ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
            ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->select('acheminers.*', 'produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'categorie_produits.Libelle')
            ->where('acheminers.Id_Acheminement', '=', $id_acheminement)
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
                $htmlContent = view('page.acheminement.acheminer.imprimer.imprimer-action', $data)->render();

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
                $prefixe = 'Acheminement';
                $date_et_heure = date('Ymd_His');
                $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
                return $dompdf->stream($nom_pdf, ['Attachment' => false]);
            }

            if($reponse === 'exporter'){
                $prefixe = 'acheminement&';
                $date_et_heure = date('Ymd_His');
                $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

                return Excel::download(new AcheminementActionPrintExport($data), $nom_excel);
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

            $query = DB::table('acheminements')
                ->join('agences', 'acheminements.Id_Agence_Source', 'agences.id')
                ->join('agences as agences2', 'acheminements.Id_Agence_Destination', 'agences2.id')
                ->join('users', 'acheminements.Id_Utilisateur', 'users.id')
                ->whereBetween('acheminements.Date_acheminement', [$date_debut_periode, $date_fin_periode])
                ->select('acheminements.*','users.name', 'agences.NomAgence', 'agences2.NomAgence as agence_destination');

            if($agence === 'Tous') {

                $liste_acheminements = $query->get();
               // dd($liste_acheminements);
                if (count($liste_acheminements) > 0) {
                    $get_request = $liste_acheminements;
                } else {
                    return to_route('achminer')->with('error', 'Aucunes données trouvées!');
                }
            }
            if($agence !== 'Tous') {
                $liste_acheminements = $query->where('acheminements.Id_Agence_Source', $agence)->get();


                if (count($liste_acheminements) > 0) {
                    $get_request = $liste_acheminements;
                } else {
                    return to_route('achminer')->with('error', 'Aucunes données trouvées!');
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


                $htmlContent = view('page.acheminement.acheminer.imprimer.imprimer', $data)->render();


                $dompdf->loadHtml($htmlContent);
                $dompdf->setPaper('A4', 'portrait');
                $options->set('isHtmlHeaderFixed', true);
                $options->set('isHtmlFooterFixed', true);

                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream('acheminements_'.$date_et_heure, array("Attachment" => false));
            }
            if($submit == 'EXCEL'){
                return Excel::download(new AcheminementPeriodeExport($data), 'acheminements_'.$date_et_heure.'.xlsx');
            }

       /*   } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }

    public function filterAcheminement(Request $request)
    {
        $this->authorize('consulter-reglement');
        $site_id = session()->get('site_id');
        // try{
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $annee = $request->query('annee');

        // Vérification de l'utilisateur et de l'agence
        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');


        $query = DB::table('acheminements')
            ->join('agences', 'acheminements.Id_Agence_Destination', 'agences.id')
            ->join('users', 'acheminements.Id_Utilisateur', 'users.id')
            ->select('acheminements.id', 'acheminements.Date_acheminement', 'acheminements.Statut_acheminement', 'acheminements.Reference_acheminement', 'acheminements.Observations', 'users.name', 'agences.NomAgence')
            ->orderBy('acheminements.id', 'desc')
            ->where('acheminements.Id_Agence_Source', $site_id);


        if ($annee) {
            $query->whereYear('acheminements.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('acheminements.created_at', $month);
            $query->whereYear('acheminements.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('acheminements.created_at', '>=', $startDate)
                ->where('acheminements.created_at', '<=', $endDate);
        }

        $acheminements = $query->get();


        // $detail_reglements = DB::table('detail_reglements')
        //     ->join('factures', 'detail_reglements.Id_Facture', '=', 'factures.id')
        //     ->join('libelle_type_operations', 'detail_reglements.Id_Libelle_Type_Operation', '=', 'libelle_type_operations.id')
        //     ->select('detail_reglements.*', 'factures.Reference_facture', 'factures.Net_a_payer', 'libelle_type_operations.Libelle_Operation')
        //     ->where('detail_reglements.Id_Reglement', '=', 0)
        //     ->get();


        $listeAgence = Agence::all();
        $clients = Client::all();
        // $mode_paiements = LibelleTypeOperation::all();
        $annees = Acheminement::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');
        // dd($reglements, $detail_reglements);

        return view('page.acheminement.acheminer.acheminer',  [
            'acheminements' => $acheminements,
            // 'detail_reglements' => $detail_reglements,
            'clients' => $clients,
            // 'mode_paiements' => $mode_paiements,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
        /*   }catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        } */
    }

}
