<?php

namespace App\Http\Controllers\facturation;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\Image;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\AgenceUser;
use App\Models\SeuilStock;
use App\Models\Consignation;
use App\Models\Lignefacture;
use App\Models\TotalFacture;
use Illuminate\Http\Request;
use App\Models\DetailProforma;
use App\Models\GroupeTaxation;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Exports\FacturesExport;
use App\Models\PrefixeReference;
use App\Models\LigneConsignation;
use App\Exports\FacturesCsvExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\StatistiqueGlobalFactureExport;
use App\Exports\StatistiqueDetailleFactureExport;
use App\Models\HistoriqueVPrestation;

class factureController extends Controller
{

    public function exportExcel(Request $request)
    {
        // $this->authorize('consulter-factures');

        try {
            // Valider les données envoyées par le frontend
            $data = $request->validate([
                'totauxParCategorie' => 'required|array',
                'totalParTypeFacture' => 'required|array',
                'listeFacture' => 'required|array',
                'tableStatGlobalData' => 'required|array',
                'diffTableData' => 'required|array',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'client' => '',
                'infoClient' => '',
                'infoAgence' => '',
            ]);
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
            $export = new StatistiqueGlobalFactureExport($data, $texteEntetePied);



            $fileName = 'Statistique_global_facture_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            // Retourner le fichier pour téléchargement direct
            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportStatGlobalPdf(Request $request)
    {
        // $this->authorize('consulter-factures');
        try {
            // Valider les données envoyées par le frontend
            $data = $request->validate([
                'totauxParCategorie' => 'required|array',
                'totalParTypeFacture' => 'required|array',
                'listeFacture' => 'nullable|array',
                'tableStatGlobalData' => 'required|array',
                'diffTableData' => 'required|array',
                'client' => 'nullable|string',
                'infoClient' => 'nullable|array',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoAgence' => 'nullable|array',
            ]);

            Carbon::setLocale('fr');


            $Code_client = $data['infoClient']['Code_client'] ?? '';

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.facturation.facture.document.statistique_global_facture_pdf', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'Code_client' => $Code_client,
                'imageEntetePied' => $imageEntetePied,
                'infoAgence' => $data['infoAgence'],

            ])->render();

            // Options de Dompdf
            $options = new Options();
            $options->set('chroot', realpath(''));

            // Initialiser Dompdf
            $dompdf = new Dompdf($options);

            // Charger le HTML
            $dompdf->loadHtml($html);

            // (Optionnel) Configuration du format de papier et de l'orientation
            $dompdf->setPaper('A4', 'portrait');

            // Rendre le HTML en PDF
            $dompdf->render();

            // Télécharger le PDF
            return $dompdf->stream('Statistique_global_facture_', ["Attachment" => false]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportStatDetaillePdf(Request $request)
    {
         $this->authorize('consulter-factures');
        try {
            $data = $request->validate([

                'tableStatDetailleData' => 'required|array',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'taxe' => '',
                'infoClient' => '',
                'infoAgence' => '',
            ]);



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            $html = view('page.facturation.facture.document.statistique_detaille_facture_pdf', [
                'data' => $data,
                'imageEntetePied' => $imageEntetePied,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'taxe' => $data['taxe'],
                'infoClient' => $data['infoClient'],
                'infoAgence' => $data['infoAgence'],
            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'landscape');

            $dompdf->render();

            return $dompdf->stream('statistique_detaille_facture_', ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportExcelStatDetaille(Request $request)
    {
         $this->authorize('consulter-factures');
      try {
            $data = $request->validate([

            'tableStatDetailleData' => 'required|array',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'taxe' => '',
            'infoClient' => '',
            'infoAgence' => '',

        ]);


        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
        $export = new StatistiqueDetailleFactureExport($data, $texteEntetePied);

        $fileName = 'Statistique_detaille_facture_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download($export, $fileName);
          } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function listeFacture()
    {
        $this->authorize('consulter-factures');
        try {
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');
            $annees = Facture::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            if (is_array($Agence_id)) {
                $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale','clients.Numero_ifu', 'users.name as user_name')
                    ->where(function ($query) {
                        $query->where('Code_type_facture', 'FV')
                            ->orWhere('Code_type_facture', 'EV');
                    })
                    //->whereMonth('factures.created_at', $currentMonth)
                    //->whereYear('factures.created_at', $currentYear)
                    ->orderBy('factures.created_at', 'desc')
                    ->get();
            } else {
                $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale','clients.Numero_ifu', 'users.name as user_name')
                    ->where('factures.agence_id', '=', $Agence_id)
                    ->where(function ($query) {
                        $query->where('Code_type_facture', 'FV')
                            ->orWhere('Code_type_facture', 'EV');
                    })
                    ->whereMonth('factures.created_at', $currentMonth)
                    ->whereYear('factures.created_at', $currentYear)
                    ->orderBy('factures.created_at', 'desc')
                    ->get();
            }

            $detailFactures = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
                ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'lignefactures.GroupeTaxe_id')
                ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                ->select('lignefactures.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('lignefactures.facture_id', '=', 0)
                ->get();
            $listeClient = Client::all();
            // $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();
            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
            $listeTaxe = GroupeTaxation::all();
            return view('page.facturation.facture.facture', [
                'detailFactures' => $detailFactures,
                'listeProforma' => $listeProforma,
                'listeClient' => $listeClient,
                'listeAgence' => $listeAgence,
                'listeTaxe' => $listeTaxe,
                'annees' => $annees,
                //'etatDuStock' => $etatDuStock,
                //'produitsEnRupture' => $produitsEnRupture
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getDetailFacture($id)
    {
        $this->authorize('consulter-factures');
        try {
            $annees = Facture::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale','clients.Numero_ifu', 'users.name as user_name')
                ->where(function ($query) {
                    $query->where('Code_type_facture', 'FV')
                        ->orWhere('Code_type_facture', 'EV');
                })
                ->orderby('factures.created_at', 'desc')
                ->paginate(20);

            $detailFactures = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
                ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'lignefactures.GroupeTaxe_id')
                ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                ->leftjoin('emballages', 'emballages.id', '=', 'produits.Emballage_id')
                ->select('lignefactures.*', 'produits.Reference', 'produits.Designation', 'emballages.Reference as Reference_emballage','emballages.Nom_emballage', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('lignefactures.facture_id', '=', $id)
                ->get();
            $listeClient = Client::all();
            $listeAgence = Agence::all();
            $listeTaxe = GroupeTaxation::all();


            return view('page.facturation.facture.facture', [
                'detailFactures' => $detailFactures,
                'listeProforma' => $listeProforma,
                'listeClient' => $listeClient,
                'listeAgence' => $listeAgence,
                'listeTaxe' => $listeTaxe,
                'annees' => $annees

            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function filterFacture(Request $request)
{
    $this->authorize('consulter-factures');

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
            $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale','clients.Numero_ifu', 'users.name as user_name')
                ->where('factures.agence_id', '=', $Agence_id)
                ->where(function ($query) {
                    $query->where('Code_type_facture', 'FV')
                        ->orWhere('Code_type_facture', 'EV');
                });
        } else {
            $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale','clients.Numero_ifu', 'users.name as user_name')
                ->where(function ($query) {
                    $query->where('Code_type_facture', 'FV')
                        ->orWhere('Code_type_facture', 'EV');
                })
                ->where('factures.agence_id', '=', $Agence_id);
        }

        // Application des filtres en fonction des paramètres
        if ($annee) {
            $query->whereYear('factures.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('factures.created_at', $month);
            $query->whereYear('factures.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('factures.created_at', '>=', $startDate)
                  ->where('factures.created_at', '<=', $endDate);
        }

        // Exécuter la requête
        $listeProforma = $query->get();

        // Autres données nécessaires pour la vue
        $detailFactures = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
            ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
            ->join('groupe_taxations', 'groupe_taxations.id', '=', 'lignefactures.GroupeTaxe_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->select('lignefactures.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
            ->where('lignefactures.facture_id', '=', 0)
            ->get();

        $listeClient = Client::all();
        $listeAgence = Agence::all();
        $listeTaxe = GroupeTaxation::all();
        $annees = Facture::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        // Retourner la vue avec les résultats
        return view('page.facturation.facture.facture', [
            'detailFactures' => $detailFactures,
            'listeProforma' => $listeProforma,
            'listeClient' => $listeClient,
            'listeAgence' => $listeAgence,
            'listeTaxe' => $listeTaxe,
            'annees' => $annees
        ]);

    } catch (Exception $e) {
        return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
    }
}



    public function showForm()
    {
        $this->authorize('creer-facture');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->facture ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $listeClient = Client::where('Statut_client', '1')->get();
            $listeGroupeTaxation = DB::table('groupe_taxations')->get();

            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            if (is_array($Agence_id)) {
                $produits = Stock::leftJoin('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->leftJoin('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    ->leftJoin('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('stocks.*', 'produits.Type', 'produits.Reference', 'produits.Designation', 'produits.id as produit_id', 'magasins.NomMagasin')
                    ->where('stocks.Qte_stockee', '>', 0)
                    ->where('produits.Statut', '=', 'ACTIF')
                    ->get();
            } else {
                $produits = Stock::leftJoin('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->leftJoin('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    ->leftJoin('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('stocks.*', 'produits.Type', 'produits.Reference', 'produits.Designation', 'produits.id as produit_id', 'magasins.NomMagasin')
                    ->where('stocks.Qte_stockee', '>', 0)
                    ->where('produits.Statut', '=', 'ACTIF')
                    ->where(function ($query) use ($Agence_id) {
                        $query->where('agences.id', '=', $Agence_id)
                            ->orWhereNull('magasins.agence_id'); // Ajoutez cette condition pour inclure les produits sans agence
                    })
                    ->get();
            }




            return view('page.facturation.facture.nouveauf', [
                'listeClient' => $listeClient,
                'produits' => $produits,
                'listeGroupeTaxation' => $listeGroupeTaxation,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getLigneProforma2($id)
    {
        $this->authorize('consulter-factures');
        try {
            // Récupérer toutes les lignes de la proforma associées à la facture spécifiée
            $ligneProforma = DetailProforma::join('groupe_taxations', 'detail_proformas.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->select('detail_proformas.*', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'produits.Reference', 'produits.Designation', 'produits.Type')
                ->where('detail_proformas.facture_id', $id)
                //->orWhere('produits.Type', 'PRESTATION')
                ->get();

            // Récupérer toutes les informations sur les stocks disponibles
            $listeStocke = Stock::join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'magasins.NomMagasin')
                ->get();

            // Créer une collection pour stocker les lignes de proforma mises à jour
            $updatedDetailProforma = collect();

            // Parcourir chaque ligne de la proforma
            foreach ($ligneProforma as $ligne) {
                // Si le produit est de type "PRESTATION", l'ajouter directement à la collection mise à jour
                if ($ligne->Type === 'PRESTATION' || $ligne->Type === 'TAXE_SIMPLE') {
                    $updatedDetailProforma->push($ligne);
                } else {
                    // Copier la quantité de la ligne pour utilisation ultérieure
                    $qteDemandee = $ligne->Qte;
                    $qteSatisfaite = false;

                    // Parcourir chaque stock pour cette ligne
                    foreach ($listeStocke as $stock) {
                        // Vérifier si ce stock correspond au produit de la ligne
                        if ($stock->Id_Produit === $ligne->produit_id) {
                            // Calculer la quantité disponible dans ce stock
                            $qteDisponible = min($qteDemandee, $stock->Qte_stockee);

                            // Créer une nouvelle ligne avec la quantité disponible
                            $newLigne = clone $ligne;
                            $newLigne->Qte = $qteDisponible;
                            $newLigne->id_stock = $stock->id; // Mettre à jour l'ID du magasin
                            $newLigne->Id_Magasin = $stock->Id_Magasin; // Mettre à jour l'ID du magasin
                            $newLigne->NomMagasin = $stock->NomMagasin; // Mettre à jour le nom du magasin
                            $updatedDetailProforma->push($newLigne);

                            // Soustraire la quantité disponible de la quantité demandée
                            $qteDemandee -= $qteDisponible;
                            $qteSatisfaite = true;
                        }
                    }

                    // Si la quantité demandée n'a pas été satisfaite, ne pas ajouter cette ligne
                    if (!$qteSatisfaite) {
                        continue;
                    }
                }
            }



            return response(['listeStocke' => $listeStocke, 'detailProforma' => $updatedDetailProforma]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeFacture(Request $request)
    {
        $this->authorize('creer-facture');
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'client_id' => 'required',
                    'Vente_consignation' => '',

                    'Aib' => '',
                    'Aib_deductible' => '',
                    'Objet_facture' => '',
                    'Commentaire' => '',
                    'Autres_infos' => '',
                    'Net_a_payer' => '',

                    'totalExoneres' => '',
                    'totalHT_B' => '',
                    'totalTVA_B' => '',
                    'totalHT_C' => '',
                    'totalHT_D' => '',
                    'totalTVA_D' => '',
                    'totalHT_E' => '',
                    'totalHT_F' => '',
                    'aib_facturer' => '',
                    'aib' => '',
                    'inputs.*.StockeId' => 'required',
                    'inputs.*.produit' => 'required',
                    'inputs.*.taxe_id' => 'required',
                    'inputs.*.quantity' => 'required',
                    'inputs.*.pu_HT' => 'required',
                    'inputs.*.Taux_remise' => 'required',
                    'inputs.*.pu_HT_net' => 'required',
                    'inputs.*.pu_TTC' => 'required',
                    'inputs.*.montantHT' => 'required',
                    'inputs.*.montantTTC' => 'required',
                    'inputs.*.TypeP' => '',
                ],
                [
                    'client_id' => 'le client est requis',
                    'inputs.*.produit' => "produit(s) requis",
                    'inputs.*.taxe_id' => "La taxe(s) requis",
                    'inputs.*.quantity' => "quantite(s) requise(s)",
                    'inputs.*.pu_HT' => "prix unitaire HT requis",
                ]

            );
            if ($validator->fails()) {
                // Si la validation échoue, retournez à la page précédente avec les erreurs
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }



            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            $lastReference = Facture::where(function($query) {
                $query->where('Code_type_facture', 'FV')
                      ->orWhere('Code_type_facture', 'EV');
            })
            ->where('agence_id', session()->get('site_id'))
            ->count();


            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '0000001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = Facture::where(function($query) {
                    $query->where('Code_type_facture', 'FV')
                          ->orWhere('Code_type_facture', 'EV');
                })
                ->where('agence_id', session()->get('site_id'))
                ->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_facture, -7); // Obtenez les 7 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);

            }



            $user = Auth::user();
            $user_id = $user->id;

            $Agence_id = session()->get('site_id');



            //variable de creation entree produit
            $Date_facture =  Carbon::now();
            $client_id = $request->input('client_id');

            $Aib = $request->input('Aib');
            $Aib_deductible = $request->input('Aib_deductible');
            $Objet_facture = $request->input('Objet_facture');
            $Commentaire = $request->input('Commentaire');
            $Autres_infos = $request->input('Autres_infos');
            $Net_a_payer = $request->input('Net_a_payer');



            $TTCTotal = 0;

            //verification du net a peyer
            foreach ($request->inputs as $value) {

                $montantTTC = $value['pu_TTC'] *  $value['quantity'];

                $TTCTotal += $montantTTC;
            }

            $TTCTotal = round( $TTCTotal - $request->input('aib') + $request->input('aib_facturer'));
           // dd($TTCTotal);


            //dd($TTCTotal);
            if($TTCTotal != $Net_a_payer){
                return redirect()->back()->with('warning', 'Le net a payer ne correspond pas au total TTC');
            }

            //verification de taxe séjour
            foreach ($request->inputs as $value) {


                if ($value['designation'] == 'Taxe de séjour') {
                    $taxe_formate = explode('-', $value['taxe_id'], 2);
                    $taxe_Lettre = trim($taxe_formate[1]);
                   // dd($taxe_Lettre);
                    if ($taxe_Lettre != 'F') {
                        return redirect()->back()->with('warning', 'Veuillez selectionner le groupe taxation F pour la taxe de séjour');
                    }
                }
            }



            $infoClient = Client::where('id', $client_id)->first();
            $pays_client = $infoClient->Pays;
            if ($pays_client == 'Bénin') {
                $Code_type_facture = 'FV';
            } else {
                $Code_type_facture = 'EV';
            }
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->facture ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";





            if (!empty($request->inputs)) {
                $facture = new Facture();
                $facture->Reference_facture = $Reference_facture;
                $facture->Date_facture = $Date_facture;
                $facture->client_id = $client_id;

                $facture->Aib = $Aib;
                $facture->Aib_deductible = $Aib_deductible;
                $facture->Objet_facture = $Objet_facture;
                $facture->Commentaire = $Commentaire;
                $facture->Autres_infos = $Autres_infos;
                $facture->Agence_id = $Agence_id;
                $facture->user_id = $user_id;
                $facture->Code_type_facture = $Code_type_facture;
                $facture->Net_a_payer = $Net_a_payer;
                $facture->Statut_facture = 'EN COURS';
                $facture->save();

                $facture_id = $facture->id;

                if($request->Vente_consignation == 1){

                    $save_consignation = new Consignation();
                    $save_consignation->ref_facture = $Reference_facture;
                    $save_consignation->facture_id = $facture_id;
                    $save_consignation->client_id = $request->input('client_id');
                    $save_consignation->statut = 'EN_COURS';
                    $save_consignation->user_id = auth()->user()->id;
                    $save_consignation->agence_id = $Agence_id;
                    $save_consignation->save();


                    $consignation_id = $save_consignation->id;


                 }



                foreach ($request->inputs as $value) {
                    $taxe_formate = explode('-', $value['taxe_id'], 2);
                    $taxe_id = trim($taxe_formate[0]);

                    $produit = Produit::where('Reference', '=', $value['produit'])->first();
                    $produit_id = $produit->id;
                    $type_emballage = $produit->type_emballage;



                    $ligneProforma = new Lignefacture();
                    $ligneProforma->facture_id =  $facture_id;
                    $ligneProforma->stocks_id = $value['StockeId'];
                    $ligneProforma->GroupeTaxe_id = $taxe_id;
                    $ligneProforma->Qte = $value['quantity'];
                    $ligneProforma->Taux_remise = $value['Taux_remise'];
                    $ligneProforma->Prix_unitaire_HT = $value['pu_HT'];
                    $ligneProforma->Prix_revient = $value['pu_TTC'];
                    $ligneProforma->save();

                    //mise a jour du stock
                    if ($value['TypeP'] != 'PRESTATION' && $value['TypeP'] != 'TAXE_SIMPLE') {

                        $stock = Stock::find($value['StockeId']);
                        $stock->Qte_stockee = $stock->Qte_stockee - $value['quantity'];
                        $stock->save();

                        //Historiq stock
                        $historique_sortie_produit = new StockHistories();
                        $historique_sortie_produit->Date = $Date_facture;
                        $historique_sortie_produit->agence_id = $Agence_id;
                        $historique_sortie_produit->Motif = 'Facture de vente';
                        $historique_sortie_produit->Justificatif = $Reference_facture;
                        $historique_sortie_produit->operation ='SORTIE';
                        $historique_sortie_produit->type_operation ='FACTURE_V';
                        $historique_sortie_produit->Id_Utilisateur = auth()->user()->id;
                        $historique_sortie_produit->Id_Produit = $stock->Id_Produit;
                        $historique_sortie_produit->Id_Magasin = $stock->Id_Magasin;
                        $historique_sortie_produit->Quantite = $value['quantity'];
                        $historique_sortie_produit->Prix_vente = $value['pu_TTC'] * $value['quantity'];
                        $historique_sortie_produit->Prix_achat = $stock->Prix_Achat_Net * $value['quantity'];
                        $historique_sortie_produit->save();


                        $produitIdd = $stock->Id_Produit;
                        $idMagasinSock = $stock->Id_Magasin;

                        if ($type_emballage == 'EMBALLAGE_RECUPERABLE') {

                            $Produitemballage = Produit::where('id', $produitIdd)->first();
                            $idEmballage = $Produitemballage->Emballage_id;
                            if($idEmballage != null){
                                $stockEmballage = StockEmballage::where('Id_Emballage',$idEmballage)->where('Id_Magasin',$idMagasinSock)->first();
                                  if(empty($stockEmballage)){
                                     $stockEmballage = new StockEmballage();
                                     $stockEmballage->Id_Emballage = $idEmballage;
                                     $stockEmballage->Id_Magasin = $idMagasinSock;
                                     $stockEmballage->Qte_stockee = 0;
                                     $stockEmballage->Prix_Achat_Net = 0;
                                     $stockEmballage->Enregistrer_par = auth()->user()->id;
                                     $stockEmballage->save();
                                 }

                                 if($request->Vente_consignation == 0){

                                        $stockEmballage->Qte_stockee += $value['quantity'];
                                        $stockEmballage->save();


                                            //Historiq stock emballage
                                             $historique_sortie_emballage = new StockEmballageHistories();
                                             $historique_sortie_emballage->Date = $Date_facture;
                                             $historique_sortie_emballage->agence_id = $Agence_id;
                                             $historique_sortie_emballage->Motif = "Entree d'emballage sur une Facture de vente";
                                             $historique_sortie_emballage->Justificatif = $Reference_facture;
                                             $historique_sortie_emballage->operation ='ENTREE';
                                             $historique_sortie_emballage->type_operation ='FACTURE_V';
                                             $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                                             $historique_sortie_emballage->Id_Emballage = $idEmballage;
                                             $historique_sortie_emballage->Id_Magasin = $stock->Id_Magasin;
                                             $historique_sortie_emballage->Quantite = $value['quantity'];
                                             $historique_sortie_emballage->save();

                                 }
                                 if($request->Vente_consignation == 1){

                                    $save_ligne_consignation = new LigneConsignation();
                                    $save_ligne_consignation->consignation_id = $consignation_id;
                                    $save_ligne_consignation->emballage_id = $idEmballage;
                                    $save_ligne_consignation->produit_id = $produitIdd;
                                    $save_ligne_consignation->Qte = $value['quantity'];
                                    $save_ligne_consignation->restituee = 0;
                                    $save_ligne_consignation->facturee = 0;
                                    $save_ligne_consignation->stock_emballage_id = $stockEmballage->id;
                                    $save_ligne_consignation->stock_id = $stock->id;
                                    $save_ligne_consignation->save();
                                 }

                            }


                         }
                    }
                    if($value['TypeP'] == 'PRESTATION' || $value['TypeP'] == 'TAXE_SIMPLE')
                    {
                        $stockPrestation = Stock::find($value['StockeId']);

                        $historique_prestation = new HistoriqueVPrestation();
                        $historique_prestation->Date = $Date_facture;
                        $historique_prestation->agence_id = $Agence_id;
                        $historique_prestation->Motif = 'Facture de vente';
                        $historique_prestation->Justificatif = $Reference_facture;
                        $historique_prestation->operation ='SORTIE';
                        $historique_prestation->type_operation ='FACTURE_V';
                        $historique_prestation->Id_Utilisateur = auth()->user()->id;
                        $historique_prestation->Id_Produit = $stockPrestation->Id_Produit;
                       // $historique_prestation->Id_Magasin = $stock->Id_Magasin;
                        $historique_prestation->Quantite = $value['quantity'];
                        $historique_prestation->Prix_vente = $value['pu_TTC'] * $value['quantity'];
                        $historique_prestation->save();

                    }
                }

                $totalFacture = new TotalFacture();
                $totalFacture->facture_id = $facture_id;
                $totalFacture->Statut = '0';
                $totalFacture->TotalExoneree = $request->input('totalExoneres');
                $totalFacture->TotalHT_B = $request->input('totalHT_B');
                $totalFacture->TotalTVA_B = $request->input('totalTVA_B');
                $totalFacture->TotalHT_C = $request->input('totalHT_C');
                $totalFacture->TotalHT_D = $request->input('totalHT_D');
                $totalFacture->TotalTVA_D = $request->input('totalTVA_D');
                $totalFacture->TotalHT_E = $request->input('totalHT_E');
                $totalFacture->TotalHT_F = $request->input('totalHT_F');
                $totalFacture->Aib_facturee = $request->input('aib_facturer');
                $totalFacture->Aib_deductible = $request->input('aib');
                $totalFacture->save();
            } else {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins une ligne facture');
            }


            return to_route('facture')->with('success', 'Facture créée avec success');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function invaliderFacture($id)
    {
        $this->authorize('annuler-facture');
        try {
            $facture = Facture::find($id);
            if ($facture->Statut_facture == 'INVALIDEE' || $facture->Statut_facture == 'ANNULEE' || $facture->Statut_facture == 'PAYEE' || $facture->Statut_facture == 'NORMALISEE') {

                return redirect()->route('facture')->with('error', 'Cette Facture ne peut pas être invalidée');
            }
            $facture->Statut_facture = 'INVALIDEE';
            $facture->save();
            $ligneFacture = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
                ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
                ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                ->select('lignefactures.*', 'produits.Reference', 'produits.Type')
                ->where('lignefactures.facture_id', $id)->get();
            if ($ligneFacture->count() > 0) {
                foreach ($ligneFacture as $ligne) {
                    if ($ligne->Type != 'PRESTATION' && $ligne->Type != 'TAXE_SIMPLE') {
                        $stock = Stock::find($ligne->stocks_id);
                        // dump($stock); // Vérifiez le contenu du stock
                        $stock->Qte_stockee +=  $ligne->Qte;
                        $stock->save();


                        $trouverhistorique = StockHistories::where('Id_Produit', $stock->Id_Produit)
                            ->where('Id_Magasin', $stock->Id_Magasin)
                            ->where('Justificatif', $facture->Reference_facture)
                            ->where('operation', 'SORTIE')
                            ->where('type_operation','FACTURE_V')->first();



                        //Historiq stock
                        $historique_entree_produit = new StockHistories();
                        $historique_entree_produit->Date = $facture->Date_facture;
                        $historique_entree_produit->agence_id = $facture->agence_id;
                        $historique_entree_produit->Motif =  'Facture de vente invalidée';
                        $historique_entree_produit->Justificatif = $facture->Reference_facture;
                        $historique_entree_produit->operation ='ENTREE';
                        $historique_entree_produit->type_operation ='FACTURE_INVALIDEE';
                        $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                        $historique_entree_produit->Id_Produit = $stock->Id_Produit;
                        $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                        $historique_entree_produit->Quantite = $ligne->Qte;
                        $historique_entree_produit->Prix_vente = $ligne->Prix_revient * $ligne->Qte;
                        $historique_entree_produit->Prix_achat = $trouverhistorique->Prix_achat;
                        $historique_entree_produit->save();
                    }
                    if($ligne->Type == 'PRESTATION' || $ligne->Type == 'TAXE_SIMPLE')
                    {
                        $stockPrestation = Stock::find($ligne->stocks_id);

                        $trouverhistorique = HistoriqueVPrestation::where('Id_Produit', $stockPrestation->Id_Produit)
                        ->where('Justificatif', $facture->Reference_facture)
                        ->where('operation', 'SORTIE')
                        ->where('type_operation','FACTURE_V')->first();

                        $historique_prestation = new HistoriqueVPrestation();
                        $historique_prestation->Date = $facture->Date_facture;
                        $historique_prestation->agence_id =  $facture->agence_id;
                        $historique_prestation->Motif =  'Facture de vente invalidée';
                        $historique_prestation->Justificatif =$facture->Reference_facture;
                        $historique_prestation->operation ='ENTREE';
                        $historique_prestation->type_operation ='FACTURE_INVALIDEE';
                        $historique_prestation->Id_Utilisateur = auth()->user()->id;
                        $historique_prestation->Id_Produit = $stockPrestation->Id_Produit;
                       // $historique_prestation->Id_Magasin = $stock->Id_Magasin;
                        $historique_prestation->Quantite = $ligne->Qte;
                        $historique_prestation->Prix_vente =$ligne->Prix_revient * $ligne->Qte;
                        $historique_prestation->save();

                    }
                }
            }

            //invalidation de la consignation
            $findConsignation = Consignation::where('facture_id', $id)->first();
            if ($findConsignation) {
                $findConsignation->statut = 'INVALIDEE';
                $findConsignation->save();
            }


            return redirect()->route('facture')->with('success', 'Facture invalidée avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function statistiqueGlobalFacture(Request $request)
    {
        $this->authorize('consulter-factures');
        try {

            $data = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'client' => '',
                'agence' => '',
            ]);

            $dateDebut = $data['date_debut'];
            $dateFin = $data['date_fin'];
           // $dateFin = Carbon::parse($data['date_fin'])->endOfDay();
            $client = $data['client'];
            $agence = $data['agence'];

            if (is_array(getIdAgenceByUser())) {
                $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'users.name as user_name')
                    ->wherenot('factures.Code_type_facture', 'PR')
                    ->wherenot('factures.Statut_facture', 'EN COURS')
                    ->whereBetween('factures.created_at', [$dateDebut, $dateFin]);
                   // $infoAgence = Agence::findOrFail($agence);
            } else {
                $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'users.name as user_name')
                    ->wherenot('factures.Code_type_facture', 'PR')
                    ->wherenot('factures.Statut_facture', 'EN COURS')

                    ->whereBetween('factures.created_at', [$dateDebut, $dateFin]);
                    //$infoAgence = Agence::findOrFail($agence);
            }


            $listeTotal = TotalFacture::join('factures', 'factures.id', '=', 'total_factures.facture_id')
                ->selectRaw('factures.Code_type_facture, SUM(TotalExoneree) as totalExoneree, SUM(TotalHT_B + TotalTVA_B) as totalHT_B,
                SUM(TotalHT_C) as totalHT_C,SUM(TotalHT_D + TotalTVA_D) as totalHT_D,
                SUM(TotalHT_E) as totalHT_E,SUM(TotalHT_F) as totalHT_F,
                SUM(Aib_facturee) as AibFacturee,SUM(total_factures.Aib_deductible) as AibDeductible')
                ->where('total_factures.Statut', '1')
                ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                ->groupBy('factures.Code_type_facture');


            if ($client !== 'Tous') {
                $query->where('factures.client_id', $client);
                $listeTotal->where('factures.client_id', $client);
                $infoClient = Client::findOrFail($client);
            } else {
                $infoClient = Client::where('id', 0)->first();
            }

            if($agence !== 'Toutes') {
                $query->where('factures.agence_id', $agence);
                $listeTotal->where('factures.agence_id', $agence);
            }
            $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

            $listeFacture = $query->get();
            /*    if($listeFacture->count() <= 0) {

                return redirect()->route('facture')->with('warning', 'Aucune facture', true);
            } */

            $totalParTypeFacture = $listeFacture->groupBy('Code_type_facture')
                ->map->count();

            $listeTotalO = $listeTotal->get();



            // Créer un tableau pour stocker les totaux par catégorie
            $totauxParCategorie = [];

            // Parcourir chaque élément de la listeTotal
            foreach ($listeTotalO as $total) {
                // Extraire le Code_type_facture
                $codeTypeFacture = $total->Code_type_facture;

                // Ajouter le Code_type_facture à l'objet de totaux s'il n'existe pas encore
                if (!isset($totauxParCategorie[$codeTypeFacture])) {
                    $totauxParCategorie[$codeTypeFacture] = [];
                }

                // Ajouter chaque total à l'objet de totaux
                $totauxParCategorie[$codeTypeFacture]['totalExoneree'] = $total->totalExoneree;
                $totauxParCategorie[$codeTypeFacture]['totalHT_B'] = $total->totalHT_B;

                $totauxParCategorie[$codeTypeFacture]['totalHT_D'] = $total->totalHT_D;
                $totauxParCategorie[$codeTypeFacture]['totalHT_C'] = $total->totalHT_C;

                $totauxParCategorie[$codeTypeFacture]['totalHT_E'] = $total->totalHT_E;
                $totauxParCategorie[$codeTypeFacture]['totalHT_F'] = $total->totalHT_F;
                $totauxParCategorie[$codeTypeFacture]['AibFacturee'] = $total->AibFacturee;
                $totauxParCategorie[$codeTypeFacture]['AibDeductible'] = $total->AibDeductible;
            }

            // Maintenant, $totauxParCategorie contient les totaux pour chaque catégorie pour chaque type de facture

           // dd($infoAgence);




            return response()->json([
                'totauxParCategorie' => $totauxParCategorie,
                'listeFacture' => $listeFacture,
                'totalParTypeFacture' => $totalParTypeFacture,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'client' => $client,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence

            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    /*  public function statistiqueDetailleFacture(Request $request)
    {
        $data = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'taxe' => '', // Vous pouvez ajouter des règles de validation supplémentaires ici si nécessaire
            'client' => '', // Vous pouvez ajouter des règles de validation supplémentaires ici si nécessaire
        ]);

        $dateDebut = $data['date_debut'];
        $dateFin = $data['date_fin'];
        $dateFin = Carbon::parse($data['date_fin'])->endOfDay();
        $client = $data['client'];
        $taxe = $data['taxe'];

        $listeTotal = TotalFacture::join('factures', 'factures.id', '=', 'total_factures.facture_id')
            ->join('clients', 'clients.id', '=', 'factures.client_id')
            ->select('factures.Reference_facture','factures.Code_type_facture','factures.Date_signature',
                        'clients.Denomination_sociale', 'total_factures.TotalTVA_B', 'total_factures.TotalTVA_D',
                        'total_factures.Aib_facturee', 'total_factures.Aib_deductible',)
            ->where('total_factures.Statut', '1')
            ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
            ->where(function ($query) {
                $query->where('total_factures.TotalTVA_B', '>', 0)
                    ->orWhere('total_factures.TotalTVA_D', '>', 0)
                    ->orWhere('total_factures.Aib_facturee', '>', 0)
                    ->orWhere('total_factures.Aib_deductible', '>', 0);
            })
            ->groupBy('total_factures.TotalTVA_B', 'total_factures.TotalTVA_D', 'total_factures.Aib_facturee',
                        'total_factures.Aib_deductible', 'factures.Reference_facture', 'factures.Code_type_facture',
                        'factures.Date_signature', 'clients.Denomination_sociale');

        if ($client !== 'Tous' && $taxe !== 'Tous') {
            $listeTotal->where('factures.client_id', $client);
            $listeTotal->where('total_factures.TotalTVA_B', '>', 0);
            $infoClient = Client::findOrFail($client);
        } else {
            $infoClient = Client::where('id', 0)->first();
        }

        $listeTotalO = $listeTotal->get();

        $response = [];

        // Organiser les données par les colonnes spécifiées
        foreach ($listeTotalO as $total) {
            $response[] = [
                'TotalTVA_B' => $total->TotalTVA_B,
                'TotalTVA_D' => $total->TotalTVA_D,
                'Aib_facturee' => $total->Aib_facturee,
                'Aib_deductible' => $total->Aib_deductible,
                'Reference_facture' => $total->Reference_facture,
                'Code_type_facture' => $total->Code_type_facture,
                'Date_signature' => $total->Date_signature,
                'Denomination_sociale' => $total->Denomination_sociale

            ];
        }

        return response()->json($response);
    } */

    public function statistiqueDetailleFacture(Request $request)
    {
        $this->authorize('consulter-factures');
        try {
            $data = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'taxe' => '',
                'client' => '',
                'agence' => '',
            ]);


            $dateDebut = $data['date_debut'];
            $dateFin = $data['date_fin'];
            //$dateFin = Carbon::parse($data['date_fin'])->endOfDay();
            $client = $data['client'];
            $taxe = $data['taxe'];
            $agence = $data['agence'];

            $listeTotal = TotalFacture::join('factures', 'factures.id', '=', 'total_factures.facture_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->select(
                    'factures.Reference_facture',
                    'factures.Code_type_facture',
                    'factures.Date_signature',
                    'factures.Objet_facture',
                    'clients.Denomination_sociale',
                    'total_factures.TotalTVA_B',
                    'total_factures.TotalTVA_D',
                    'total_factures.Aib_facturee',
                    'total_factures.Aib_deductible'
                )
                ->where('total_factures.Statut', '1')
                ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                ->where(function ($query) {
                    $query->where('total_factures.TotalTVA_B', '>', 0)
                        ->orWhere('total_factures.TotalTVA_D', '>', 0)
                        ->orWhere('total_factures.Aib_facturee', '>', 0)
                        ->orWhere('total_factures.Aib_deductible', '>', 0);
                });

            if ($client !== 'Tous') {
                $listeTotal->where('factures.client_id', $client);
            }
            if($agence !== 'Toutes') {
                $listeTotal->where('factures.agence_id', $agence);
            }

            if ($taxe !== 'Tous') {
                if ($taxe == 'TVA_Taxable') {
                    $listeTotal->where('total_factures.TotalTVA_B', '>', 0);
                } elseif ($taxe == 'TVA_Exception') {
                    $listeTotal->where('total_factures.TotalTVA_D', '>', 0);
                } elseif ($taxe == 'Aib_Facturee') {
                    $listeTotal->where('total_factures.Aib_facturee', '>', 0);
                } elseif ($taxe == 'Aib_Deductible') {
                    $listeTotal->where('total_factures.Aib_deductible', '>', 0);
                }
            }

            $listeTotalO = $listeTotal->get();
            //dd($listeTotalO);

            $response = [];

            foreach ($listeTotalO as $total) {
                $response[] = [
                    'TotalTVA_B' => $total->TotalTVA_B,
                    'TotalTVA_D' => $total->TotalTVA_D,
                    'Aib_facturee' => $total->Aib_facturee,
                    'Aib_deductible' => $total->Aib_deductible,
                    'Reference_facture' => $total->Reference_facture,
                    'Objet_facture' => $total->Objet_facture,
                    'Code_type_facture' => $total->Code_type_facture,
                    'Date_signature' => $total->Date_signature,
                    'Denomination_sociale' => $total->Denomination_sociale,

                ];


            }
            $infoClient = Client::where('id', $client)->first();
            $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;



            return response()->json([
                'response' => $response,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'taxe' => $taxe,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function exportFactureCsv(Request $request)
    {
        $this->authorize('consulter-factures');
        // Obtenez l'URL précédente
        $previousUrl = url()->previous();

        // Parsez l'URL précédente pour obtenir les paramètres de requête
        $parsedUrl = parse_url($previousUrl);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

        parse_str($query, $params);

        // Récupérer la valeur du paramètre 'bien' de l'URL précédente
        $debut1_url = isset($params['debut1']) ? $params['debut1'] : '2024-06-20 00:00:00';
        $fin1_url = isset($params['fin1']) ? $params['fin1'] : now();

        $debut1 = date('Y-m-d 00:00:00', strtotime($debut1_url));
        $fin1 = date('Y-m-d 23:59:59', strtotime($fin1_url));

        $facturesQuery = Facture::whereIn('Code_type_facture', ['FV', 'EV'])
            // ->where()
            ->select('*');

        if (filled($debut1)) {
            $facturesQuery = $facturesQuery->where('created_at', '>=', $debut1);
        }

        if (filled($fin1)) {
            $facturesQuery = $facturesQuery->where('created_at', '<=', $fin1);
        }

        $data = $facturesQuery->orderBy('created_at', 'desc')->get();

        $filename = "facturesCsv.csv";

        // Ouvrir le fichier en mode écriture
        $fp = fopen($filename, "w+");

        // Vérifier si le fichier a été ouvert correctement
        if ($fp === false) {
            die('Erreur lors de l\'ouverture du fichier');
        }

        // Ajouter le BOM pour indiquer l'encodage UTF-8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définir les en-têtes
        $header = ['ID', 'Num Facture', 'Type Facture', 'Code Signature', 'QrCode', 'Utilisateur', 'Client', 'Net A Payer', 'Agence', 'Date Facture'];
        fputcsv($fp, $header);

        // Écrire les données
        foreach ($data as $row) {
            fputcsv($fp, [$row->id, $row->Reference_facture, $row->Code_type_facture, $row->Code_signature, $row->QrCode, $row->user->name, $row->client->Denomination_sociale, $row->Net_a_payer, $row->agence->NomAgence, $row->Date_facture]);
        }

        // Fermer le fichier pour s'assurer que les données sont bien enregistrées
        fclose($fp);

        // Définir les en-têtes pour le téléchargement
        $headers = array('Content-Type' => 'text/csv');

        // Télécharger le fichier CSV
        return response()->download($filename, 'factures_Csv_' . $debut1_url . '_' . $fin1_url . '.csv', $headers);
    }

    public function exportLignesFactureCsv(Request $request){
        $this->authorize('consulter-factures');
        // Obtenez l'URL précédente
        $previousUrl = url()->previous();

        // Parsez l'URL précédente pour obtenir les paramètres de requête
        $parsedUrl = parse_url($previousUrl);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

        parse_str($query, $params);

        // Récupérer la valeur du paramètre 'bien' de l'URL précédente
        $debut2_url = isset($params['debut2']) ? $params['debut2'] : '2024-06-20 00:00:00';
        $fin2_url = isset($params['fin2']) ? $params['fin2'] : now();

        $debut2 = date('Y-m-d 00:00:00', strtotime($debut2_url));
        $fin2 = date('Y-m-d 23:59:59', strtotime($fin2_url));

        $lignesFacturesQuery = Lignefacture::select('*');

        if (filled($debut2)) {
            $lignesFacturesQuery = $lignesFacturesQuery->where('created_at', '>=', $debut2);
        }

        if (filled($fin2)) {
            $lignesFacturesQuery = $lignesFacturesQuery->where('created_at', '<=', $fin2);
        }

        $data = $lignesFacturesQuery->orderBy('created_at', 'desc')->get();

        // dd($data);

        $filename = "facturesCsv.csv";

        // Ouvrir le fichier en mode écriture
        $fp = fopen($filename, "w+");

        // Vérifier si le fichier a été ouvert correctement
        if ($fp === false) {
            die('Erreur lors de l\'ouverture du fichier');
        }

        // Ajouter le BOM pour indiquer l'encodage UTF-8
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Définir les en-têtes
        $header = ['ID', 'Num Facture', 'Code Article', 'Designation', 'Famille', 'Quantité', 'Prix Unitaire', 'Montant'];
        fputcsv($fp, $header);

        // Écrire les données
        foreach ($data as $row) {
            fputcsv($fp, [$row->id, $row->facture->Reference_facture, $row->stock->produit->Reference, $row->stock->produit->Designation, $row->stock->produit->categorieProduit->Libelle, $row->Qte, $row->Prix_unitaire_HT, $row->Prix_revient]);
        }

        // Fermer le fichier pour s'assurer que les données sont bien enregistrées
        fclose($fp);

        // Définir les en-têtes pour le téléchargement
        $headers = array('Content-Type' => 'text/csv');

        // Télécharger le fichier CSV
        return response()->download($filename, 'Export_Factures_Cvs_' . $debut2_url . '_' . $fin2_url . '.csv', $headers);
    }
}
