<?php

namespace App\Http\Controllers\facturation;

use Exception;
use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\AgenceUser;
use App\Models\Consignation;
use App\Models\Lignefacture;
use App\Models\TotalFacture;
use Illuminate\Http\Request;
use App\Models\DetailProforma;
use App\Models\GroupeTaxation;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Models\PrefixeReference;
use App\Models\PrixVenteProduit;
use App\Models\LigneConsignation;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriquePrixVente;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriqueVPrestation;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Facades\Validator;


class proformaController extends Controller
{
    public function listeProforma()
    {
        $this->authorize('consulter-proformas');
        try {
            $user = Auth::user();
            $user_connecterId = $user->id;

            // Récupérer les années distinctes des factures
            $annees = Facture::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            if (is_array(getIdAgenceByUser())) {
                $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale', 'users.name as user_name')
                    ->where('Code_type_facture', 'PR')

                    ->orderby('factures.created_at', 'DESC')
                    ->whereMonth('factures.created_at', $currentMonth)
                    ->whereYear('factures.created_at', $currentYear)
                   ->get();
            } else {
                $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale', 'users.name as user_name')
                    ->where('Code_type_facture', 'PR')
                    ->where('factures.agence_id', '=', getIdAgenceByUser())
                    ->whereMonth('factures.created_at', $currentMonth)
                    ->whereYear('factures.created_at', $currentYear)
                    ->orderby('factures.created_at', 'DESC')
                   ->get();
            }

            $detailProformas = DetailProforma::join('factures', 'factures.id', '=', 'detail_proformas.facture_id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'detail_proformas.GroupeTaxe_id')
                ->select('detail_proformas.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('detail_proformas.facture_id', '=', 0)
                ->get();
            $listeClient = Client::all();
            $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();

            // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();
            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();


            return view('page.facturation.proforma.proforma', [
                'detailProformas' => $detailProformas,
                'listeProforma' => $listeProforma,
                'listeClient' => $listeClient,
                'annees' => $annees,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function getDetailProforma($id)
    {
        $this->authorize('consulter-proformas');
        try {
            $annees = Facture::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

            $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale', 'users.name as user_name')
                ->where('Code_type_facture', 'PR')
                ->get();

            $detailProformas = DetailProforma::join('factures', 'factures.id', '=', 'detail_proformas.facture_id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'detail_proformas.GroupeTaxe_id')
                ->select('detail_proformas.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('detail_proformas.facture_id', '=', $id)
                ->get();
            $listeClient = Client::all();
            $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();
            return view('page.facturation.proforma.proforma', [
                'detailProformas' => $detailProformas,
                'listeProforma' => $listeProforma,
                'listeClient' => $listeClient,
                'annees' => $annees,
                'listeAgence' => $listeAgence
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function filterProformas(Request $request)
    {
        $this->authorize('consulter-proformas');
        try {
            $month = $request->query('month');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
            $annee = $request->query('annee');

            // Vérification de l'utilisateur et de l'agence
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            if (is_array($Agence_id)) {
                $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale', 'users.name as user_name')
                    ->where('Code_type_facture', 'PR')
                    ->where('factures.agence_id', '=', $Agence_id);
            } else {
                $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                    ->join('clients', 'clients.id', '=', 'factures.client_id')
                    ->join('users', 'users.id', '=', 'factures.user_id')
                    ->select('factures.*', 'agences.NomAgence', 'clients.Code_client', 'clients.Denomination_sociale', 'users.name as user_name')
                    ->where('Code_type_facture', 'PR')
                    ->where('factures.agence_id', '=', $Agence_id);
            }

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



            $listeProforma = $query->get();

            $detailProformas = DetailProforma::join('factures', 'factures.id', '=', 'detail_proformas.facture_id')
            ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
            ->join('groupe_taxations', 'groupe_taxations.id', '=', 'detail_proformas.GroupeTaxe_id')
            ->select('detail_proformas.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
            ->where('detail_proformas.facture_id', '=', 0)
            ->get();
        $listeClient = Client::all();
        $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
        $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();
        $annees = Facture::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');

        return view('page.facturation.proforma.proforma', [
            'detailProformas' => $detailProformas,
            'listeProforma' => $listeProforma,
            'listeClient' => $listeClient,
            'annees' => $annees,
            'listeAgence' => $listeAgence
        ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function showForm()
    {
        $this->authorize('creer-proforma');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->proforma ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $listeClient = Client::where('Statut_client', '1')->get();
            $listeGroupeTaxation = DB::table('groupe_taxations')->get();

            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            $produits = DB::table('produits')->get();


            return view('page.facturation.proforma.nouveaupf', [
                'listeClient' => $listeClient,
                'produits' => $produits,
                'listeGroupeTaxation' => $listeGroupeTaxation,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function recuperer_prix_produit(Request $request, $produitId)
    {
        // $this->authorize('consulter-proformas');
        try {
            $clientCatId = $request->input('clientCatId');
            $user = Auth::user();
            $user_connecterId = $user->id;
            $Agence_id = session()->get('site_id');

            if (is_array($Agence_id)) {
                $Listeprixproduit = PrixVenteProduit::where('produit_id', $produitId)
                    ->where('categorie_client_id', $clientCatId)
                    ->first();
            } else {
                $Listeprixproduit = PrixVenteProduit::where('produit_id', $produitId)
                    ->where('agence_id', $Agence_id)
                    ->where('categorie_client_id', $clientCatId)
                    ->first();
            }

            if ($Listeprixproduit == null) {

                return response(['prix_produit' => 0]);
            }

            $prixproduit = $Listeprixproduit->prix;
            return response(['prix_produit' => $prixproduit]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function recuperer_valeur_taxe($taxeID)
    {
        // $this->authorize('consulter-proformas');
        try {

            $taxe = GroupeTaxation::find($taxeID);
            $valeurTaxe = $taxe->valeur_taxe;

            return response(['valeurTaxe' => $valeurTaxe]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getLigneProforma($id)
    {
        $this->authorize('consulter-proformas');
        try {

            $detailProforma = DetailProforma::join('factures', 'factures.id', '=', 'detail_proformas.facture_id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'detail_proformas.GroupeTaxe_id')
                ->select('detail_proformas.*', 'produits.Reference', 'produits.Designation', 'produits.Type', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('detail_proformas.facture_id', '=', $id)->get();

            return response(['detailProforma' => $detailProforma]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getLigneProforma2($id)
    {
        $this->authorize('consulter-proformas');
        try {
            // Récupérer toutes les lignes de la proforma associées à la facture spécifiée
            $ligneProforma = DetailProforma::join('groupe_taxations', 'detail_proformas.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->select('detail_proformas.*', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'produits.Reference', 'produits.Designation', 'produits.Type')
                ->where('detail_proformas.facture_id', $id)
                ->get();

            // Récupérer toutes les informations sur les stocks disponibles
            $listeStocke = Stock::join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->select('stocks.*', 'magasins.NomMagasin')
                ->where('magasins.agence_id', session()->get('site_id'))
                ->get();

            // Créer une collection pour stocker les lignes de proforma mises à jour
            $updatedDetailProforma = collect();

            // Collection pour stocker les alertes de produits non disponibles
            $produitsNonDisponibles = collect();

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

                            if ($qteDemandee == 0) {
                                $qteSatisfaite = true;
                                break;
                            }
                        }
                    }

                    // Si la quantité demandée n'a pas été satisfaite, ajouter un message d'alerte
                    if (!$qteSatisfaite) {
                        $produitsNonDisponibles->push("Le produit {$ligne->Designation} (Référence : {$ligne->Reference}) n'est pas disponible en stock , ou la quantité voulue est trop grande et a donc été réduite a la quantité disponible.");
                    }
                }
            }

            return response(['listeStocke' => $listeStocke, 'detailProforma' => $updatedDetailProforma, 'alertes' => $produitsNonDisponibles]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function getstockId($id)
    {
        try {
            $stock = Stock::where('Id_Produit', $id)->first();
            $idStock = $stock->id;

            return response(['idStock' => $idStock]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeProforma(Request $request)
    {
        $this->authorize('creer-proforma');
        try {
            //dd($request);
            $validator = Validator::make(
                $request->all(),
                [
                    'client_id' => 'required',
                    'Validite' => 'required',
                    'Aib' => '',
                    'hiddenAib_deductible' => '',
                    'Objet_facture' => '',
                    'Commentaire' => '',
                    'Autres_infos' => '',
                    'Net_a_payer' => '',
                    'inputs.*.produit' => 'required',
                    'inputs.*.taxe_id' => 'required',
                    'inputs.*.quantity' => 'required',
                    'inputs.*.pu_HT' => 'required',
                    'inputs.*.Taux_remise' => 'required',
                    'inputs.*.pu_HT_net' => 'required',
                    'inputs.*.pu_TTC' => 'required',
                    'inputs.*.montantHT' => 'required',
                    'inputs.*.montantTTC' => 'required',
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

            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->proforma ?? '';

            $lastDigitOfYear = substr(Carbon::now()->year, -2);

            // Obtenez le dernier numéro de référence enregistré
            $lastReference = Facture::where('Code_type_facture','=' ,$prefix)->where('agence_id','=' ,session()->get('site_id'))->count();

            // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
            if ($lastReference === 0) {
                $incrementedReferenceNumber = '0000001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = Facture::where('Code_type_facture','=' ,$prefix)->where('agence_id','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_facture, -7); // Obtenez les 7 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);
                //dd($incrementedReferenceNumber);
            }

            $user = Auth::user();
            $user_id = $user->id;

            if (is_array(getIdAgenceByUser())) {
                $Agence_id = 1;
            } else {
                $Agence_id = session()->get('site_id');
            }

            //variable de creation entree produit
            $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
            $Date_facture =  Carbon::now();
            $client_id = $request->input('client_id');
            $Validite = $request->input('Validite');
            $Aib = $request->input('Aib');
            $Aib_deductible = $request->input('hiddenAib_deductible');
            $Objet_facture = $request->input('Objet_facture');
            $Commentaire = $request->input('Commentaire');
            $Autres_infos = $request->input('Autres_infos');
            $Net_a_payer = $request->input('Net_a_payer');

            if (!empty($request->inputs)) {
                $facture = new Facture();
                $facture->Reference_facture = $Reference_facture;
                $facture->Date_facture = $Date_facture;
                $facture->client_id = $client_id;
                $facture->Validite = $Validite;
                $facture->Aib = $Aib;
                $facture->Aib_deductible = $Aib_deductible;
                $facture->Objet_facture = $Objet_facture;
                $facture->Commentaire = $Commentaire;
                $facture->Autres_infos = $Autres_infos;
                $facture->Agence_id = $Agence_id;
                $facture->user_id = $user_id;
                $facture->Code_type_facture = "PR";
                $facture->Net_a_payer = $Net_a_payer;
                $facture->save();

                $facture_id = $facture->id;
                foreach ($request->inputs as $value) {
                    $taxe_formate = explode('-', $value['taxe_id'], 2);
                    $taxe_id = trim($taxe_formate[0]);

                    $produit = Produit::where('Reference', '=', $value['produit'])->first();
                    $produit_id = $produit->id;


                    $ligneProforma = new DetailProforma();
                    $ligneProforma->facture_id =  $facture_id;
                    $ligneProforma->produit_id = $produit_id;
                    $ligneProforma->GroupeTaxe_id = $taxe_id;
                    $ligneProforma->Qte = $value['quantity'];
                    $ligneProforma->Taux_remise = $value['Taux_remise'];
                    $ligneProforma->Prix_unitaire_HT = $value['pu_HT'];
                    $ligneProforma->Prix_unitaire_TTC = $value['pu_TTC'];
                    $ligneProforma->save();
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins une ligne facture');
            }

            return to_route('proforma')->with('success', 'proforma enregistré avec succés');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function editProforma(string $id)
    {
        $this->authorize('modifier-proforma');
        try {
            $infoProforma = Facture::findorfail($id);
            $detailProforma = DetailProforma::where('facture_id', '=', $id);
            $listeClient = Client::where('Statut_client', '1')->get();
            $listeGroupeTaxation = DB::table('groupe_taxations')->get();
            $produits = DB::table('produits')->get();

            return view('page.facturation.proforma.modifier', [
                'infoProforma' => $infoProforma,
                'detailProforma' => $detailProforma,
                'listeClient' => $listeClient,
                'produits' => $produits,
                'listeGroupeTaxation' => $listeGroupeTaxation
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function dupliquerProforma(string $id)
    {
        $this->authorize('dupliquer-proforma');
        try {
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->proforma ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $infoProforma = Facture::findorfail($id);
            $detailProforma = DetailProforma::where('facture_id', '=', $id);
            $listeClient = Client::where('Statut_client', '1')->get();
            $listeGroupeTaxation = DB::table('groupe_taxations')->get();
            $produits = DB::table('produits')->get();

            return view('page.facturation.proforma.dupliquer', [
                'infoProforma' => $infoProforma,
                'detailProforma' => $detailProforma,
                'listeClient' => $listeClient,
                'produits' => $produits,
                'listeGroupeTaxation' => $listeGroupeTaxation
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function conversionProforma(string $id)
    {
        $this->authorize('creer-facture');
        try {

            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->facture ?? '';
            if ($prefix == null) {
                return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
            }
            $infoProforma = Facture::findorfail($id);
            $detailProforma = DetailProforma::where('facture_id', '=', $id)->get();
            $listeClient = Client::where('Statut_client', '1')->get();
            $listeGroupeTaxation = DB::table('groupe_taxations')->get();

            $user = Auth::user();
            $user_id = $user->id;
            $Agence_id = session()->get('site_id');

            if (is_array($Agence_id)) {
                $produits = Stock::leftJoin('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->leftJoin('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    ->leftJoin('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('stocks.*', 'produits.Type', 'produits.Reference', 'produits.Designation', 'produits.id as produit_id', 'magasins.NomMagasin')
                    ->where('stocks.Qte_stockee', '>', 0)
                    ->get();

            } else {
                $produits = Stock::leftJoin('produits', 'stocks.Id_Produit', '=', 'produits.id')
                    ->leftJoin('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                    ->leftJoin('agences', 'magasins.agence_id', '=', 'agences.id')
                    ->select('stocks.*', 'produits.Type', 'produits.Reference', 'produits.Designation', 'produits.id as produit_id', 'magasins.NomMagasin')
                    ->where('stocks.Qte_stockee', '>', 0)
                    ->where(function ($query) use ($Agence_id) {
                        $query->where('agences.id', '=', $Agence_id)
                            ->orWhereNull('magasins.agence_id'); // Ajoutez cette condition pour inclure les produits sans agence
                    })
                    ->get();

            }



            //dd($produits);

            return view('page.facturation.proforma.conversion', [
                'infoProforma' => $infoProforma,
                'detailProforma' => $detailProforma,
                'listeClient' => $listeClient,
                'produits' => $produits,
                'listeGroupeTaxation' => $listeGroupeTaxation
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function updateProforma(Request $request, $id)
    {
        $this->authorize('modifier-proforma');
        try {
            //   dd($request);

            $validator = Validator::make(
                $request->all(),
                [
                    'client_id' => 'required',
                    'Validite' => 'required',
                    'Aib' => '',
                    'hiddenAib_deductible' => '',
                    'Objet_facture' => '',
                    'Commentaire' => '',
                    'Autres_infos' => '',
                    'Net_a_payer' => '',
                    'inputs.*.produit' => 'required',
                    'inputs.*.taxe_id' => 'required',
                    'inputs.*.quantity' => 'required',
                    'inputs.*.pu_HT' => 'required',
                    'inputs.*.Taux_remise' => 'required',
                    'inputs.*.pu_HT_net' => 'required',
                    'inputs.*.pu_TTC' => 'required',
                    'inputs.*.montantHT' => 'required',
                    'inputs.*.montantTTC' => 'required',
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


            $user = Auth::user();
            $user_id = $user->id;


            if (is_array(getIdAgenceByUser())) {
                $Agence_id = 1;
            } else {
                $Agence_id = session()->get('site_id');
            }



            //variable de creation entree produit
            // $Reference_facture = "$Agence_id/{$lastDigitOfYear}/PR/{$incrementedReferenceNumber}";
            // $Date_facture =  Carbon::now();
            $client_id = $request->input('client_id');
            $Validite = $request->input('Validite');
            $Aib = $request->input('Aib');
            $Aib_deductible = $request->input('hiddenAib_deductible');
            $Objet_facture = $request->input('Objet_facture');
            $Commentaire = $request->input('Commentaire');
            $Autres_infos = $request->input('Autres_infos');
            $Net_a_payer = $request->input('Net_a_payer');


            //$facture_id = $facture->id;

            if (!empty($request->inputs)) {
                $facture = Facture::findorfail($id);
                //$facture->Date_facture = $Date_facture;
                $facture->client_id = $client_id;
                $facture->Validite = $Validite;
                $facture->Aib = $Aib;
                $facture->Aib_deductible = $Aib_deductible;
                $facture->Objet_facture = $Objet_facture;
                $facture->Commentaire = $Commentaire;
                $facture->Autres_infos = $Autres_infos;
                $facture->Agence_id = $Agence_id;
                $facture->user_id = $user_id;
                $facture->Code_type_facture = "PR";
                $facture->Net_a_payer = $Net_a_payer;
                $facture->update();
                foreach ($request->inputs as $value) {
                    $taxe_formate = explode('-', $value['taxe_id'], 2);
                    $taxe_id = trim($taxe_formate[0]);

                    $produit = Produit::where('Reference', '=', $value['produit'])->first();
                    $produit_id = $produit->id;

                    // Vérifier si la ligne de facture existe déjà pour ce produit et cette facture
                    $ligneProforma = DetailProforma::where('facture_id', '=', $id)
                        ->where('produit_id', '=', $produit_id)
                        ->first();


                    if ($ligneProforma) {
                        // Modifier les valeurs de la ligne existante
                        $ligneProforma->GroupeTaxe_id = $taxe_id;
                        $ligneProforma->Qte = $value['quantity'];
                        $ligneProforma->Taux_remise = $value['Taux_remise'];
                        $ligneProforma->Prix_unitaire_HT = $value['pu_HT'];
                        $ligneProforma->save();
                    } else {
                        // Créer une nouvelle ligne de facture
                        $ligneProforma = new DetailProforma();
                        $ligneProforma->facture_id = $id;
                        $ligneProforma->produit_id = $produit_id;
                        $ligneProforma->GroupeTaxe_id = $taxe_id;
                        $ligneProforma->Qte = $value['quantity'];
                        $ligneProforma->Taux_remise = $value['Taux_remise'];
                        $ligneProforma->Prix_unitaire_HT = $value['pu_HT'];
                        $ligneProforma->Prix_unitaire_TTC = $value['pu_TTC'];
                        $ligneProforma->save();
                    }

                    $produitsEnCours[] = $produit_id;
                }
                DetailProforma::where('facture_id', '=', $id)
                ->whereNotIn('produit_id', $produitsEnCours)
                ->delete();
            } else {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins une ligne facture');
            }

            return redirect()->route('proforma')->with('success', 'Proforma modifié avec succès');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function dupliquerStoreProforma(Request $request)
    {
        $this->authorize('dupliquer-proforma');
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'client_id' => 'required',
                    'Validite' => 'required',
                    'Aib' => '',
                    'hiddenAib_deductible' => '',
                    'Objet_facture' => '',
                    'Commentaire' => '',
                    'Autres_infos' => '',
                    'Net_a_payer' => '',
                    'inputs.*.produit' => 'required',
                    'inputs.*.taxe_id' => 'required',
                    'inputs.*.quantity' => 'required',
                    'inputs.*.pu_HT' => 'required',
                    'inputs.*.Taux_remise' => 'required',
                    'inputs.*.pu_HT_net' => 'required',
                    'inputs.*.pu_TTC' => 'required',
                    'inputs.*.montantHT' => 'required',
                    'inputs.*.montantTTC' => 'required',
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

            /*      if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->with('error','Veuillez renseigner  des informations valide');
        } */
            $lastDigitOfYear = substr(Carbon::now()->year, -2);
            $prefixe = PrefixeReference::first();
            $prefix = $prefixe->proforma ?? '';


            // Obtenez le dernier numéro de référence enregistré
            $lastReference = Facture::where('Code_type_facture','=' ,$prefix)->where('agence_id','=' ,session()->get('site_id'))->count();

            if ($lastReference === 0) {
                $incrementedReferenceNumber = '0000001';
            } else {
                // Obtenez le dernier numéro de référence et incrémentez-le
                $lastReference = Facture::where('Code_type_facture','=' ,$prefix)->where('agence_id','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
                $lastReferenceNumber = substr($lastReference->Reference_facture, -7); // Obtenez les 7 derniers chiffres
                $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);

            }

            $user = Auth::user();
            $user_id = $user->id;

            if (is_array(getIdAgenceByUser())) {
                $Agence_id = 1;
            } else {
                $Agence_id = session()->get('site_id');
            }


            //variable de creation entree produit
            $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
            $Date_facture =  Carbon::now();
            $client_id = $request->input('client_id');
            $Validite = $request->input('Validite');
            $Aib = $request->input('Aib');
            $Aib_deductible = $request->input('hiddenAib_deductible');
            $Objet_facture = $request->input('Objet_facture');
            $Commentaire = $request->input('Commentaire');
            $Autres_infos = $request->input('Autres_infos');
            $Net_a_payer = $request->input('Net_a_payer');






            if (!empty($request->inputs)) {
                $facture = new Facture();
                $facture->Reference_facture = $Reference_facture;
                $facture->Date_facture = $Date_facture;
                $facture->client_id = $client_id;
                $facture->Validite = $Validite;
                $facture->Aib = $Aib;
                $facture->Aib_deductible = $Aib_deductible;
                $facture->Objet_facture = $Objet_facture;
                $facture->Commentaire = $Commentaire;
                $facture->Autres_infos = $Autres_infos;
                $facture->Agence_id = $Agence_id;
                $facture->user_id = $user_id;
                $facture->Code_type_facture = "PR";
                $facture->Net_a_payer = $Net_a_payer;
                $facture->save();

                $facture_id = $facture->id;


                foreach ($request->inputs as $value) {
                    $taxe_formate = explode('-', $value['taxe_id'], 2);
                    $taxe_id = trim($taxe_formate[0]);

                    $produit = Produit::where('Reference', '=', $value['produit'])->first();
                    $produit_id = $produit->id;


                    $ligneProforma = new DetailProforma();
                    $ligneProforma->facture_id =  $facture_id;
                    $ligneProforma->produit_id = $produit_id;
                    $ligneProforma->GroupeTaxe_id = $taxe_id;
                    $ligneProforma->Qte = $value['quantity'];
                    $ligneProforma->Taux_remise = $value['Taux_remise'];
                    $ligneProforma->Prix_unitaire_HT = $value['pu_HT'];
                    $ligneProforma->Prix_unitaire_TTC = $value['pu_TTC'];
                    $ligneProforma->save();
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins une ligne facture');
            }

            return to_route('proforma')->with('success', 'Proforma dupliqué avec success');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function convertirEnFacture(Request $request , $id)
    {

        $this->authorize('creer-facture');
        try {
            // dd($request->all());
            $validator = Validator::make(
                $request->all(),
                [
                    'Vente_consignation' => '',
                    'client_id' => 'required',
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
                    'inputs.*.magasin' => '',
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

            /*      if ($validator->fails()) {
            // Si la validation échoue, retournez à la page précédente avec les erreurs
            return redirect()->back()->with('error','Veuillez renseigner  des informations valide');
        } */
            $lastDigitOfYear = substr(Carbon::now()->year, -2);

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
            //dd($TTCTotal);


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

            $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";




            //changer le statut du proforma
            $proforma = Facture::findorfail($id);
            $proforma->Statut_facture = 'FACTUREE';
            $proforma->save();



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
                    if ($value['TypeP'] == 'PRESTATION' || $value['TypeP'] == 'TAXE_SIMPLE') {

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
                       // $historique_prestation->Id_Magasin = $stockPrestation->Id_Magasin;
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



            return to_route('facture')->with('success', 'Conversion de la facture effectuée avec success');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
}
