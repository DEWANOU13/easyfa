<?php

namespace App\Http\Controllers\statistique;

use App\Models\Stock;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenteCumuleeClientExport;
use App\Exports\VenteCumuleeProduitExport;
use App\Exports\VenteExport;
use App\Exports\VenteQuantiteExport;
use App\Models\Agence;
use App\Models\Image;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Laravel\Jetstream\Agent;

class venteController extends Controller
{
    //
    public function listevente()
    {
        $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        return view('page.statistique.vente.vente',
            [
                'agences' => $agences,
                'start_date' => '',
                'end_date' => '',
                'user' => '',
                'agence' => '',
                'ventes' => '',
                'ventes_par_categorie' => '',
                'ventes_par_client' => '',
                'ventes_par_produit' => '',
                'ventes_par_agence' => '',
                'journal_ventes' => '',
                'journal_ventes_user' => '',
                'journal_ventes_user_avoir' => '',
                'endDate_new' => '',
                'startDate_new' => '',
                'facture_avoirs' => '',
                'clt' => '',
                'prod' => '',
                'date_debut' => '',
                'date_fin' => '',
                'filtered' => '',
                'isFiltered' => false,
                'isFilteredAllVente' => false,
                'isFilteredClient' => false,
                'vente_cumulee_produit' => null,
                'vente_cumulee_client' => null
            ]
        );
    }


    public function venteStatistiques(Request $request)
    {
        $request->validate([
            'filtered' => 'required'
        ]);
        // dd($request);

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $ventes_par_categorie = '';
        $ventes_par_client = '';
        $ventes_par_produit = '';
        $ventes_par_agence = '';
        $journal_ventes_user = '';
        $journal_ventes_user_avoir = '';
        $journal_ventes = '';
        $ventes = '';
        $ctl = '';
        $filtered = '';
        $facture_avoirs = '';
        $endDate_new = '';
        $startDate_new = '';
        $agence = '';
        $user = '';
        $ag = '';
        $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        if ($request->filtered == 'sale_by_days') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $agence_id = $request->input('agence_id');
        } elseif ($request->filtered == 'sale_by_category') {
            $filtered = $request->filtered;
            // @dd($filtered);
            $request->validate([
                'start_date_1' => 'required',
                'end_date_1' => 'required',
                // 'categorie' => 'required'
            ]);
            $start_date = $request->input('start_date_1');
            $end_date = $request->input('end_date_1');
        } elseif ($request->filtered == 'sale_by_customer') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_2' => 'required',
                'end_date_2' => 'required',
            ]);
            $start_date = $request->input('start_date_2');
            $end_date = $request->input('end_date_2');
        } elseif ($request->filtered == 'sale_by_product') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_3' => 'required',
                'end_date_3' => 'required',
            ]);
            $start_date = $request->input('start_date_3');
            $end_date = $request->input('end_date_3');
        } elseif ($request->filtered == 'sale_by_agence') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_4' => 'required',
                'end_date_4' => 'required',
            ]);
            $start_date = $request->input('start_date_4');
            $end_date = $request->input('end_date_4');
        } elseif ($request->filtered == 'sale_by_user') {
            // dd('je suis bien la');
            $filtered = $request->filtered;
            // $request->validate([
            //     'start_date_8' => 'required',
            //     'end_date_8' => 'required',
            //     ]);
            $start_date = $request->input('start_date_8');
            $end_date = $request->input('end_date_8');

            $start_date_new = new DateTime($start_date);
            $end_date_new = new DateTime($end_date);

            $startDate_new = $start_date_new->format('Y-m-d H:i:s');
            $endDate_new = $end_date_new->format('Y-m-d H:i:s');

            // dd($startDate, $startDate);
        } elseif ($request->filtered == 'sale_by_detail_agence') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_5' => 'required',
                'end_date_5' => 'required',
            ]);
            $start_date = $request->input('start_date_5');
            $end_date = $request->input('end_date_5');
        } elseif ($request->filtered == 'sale_log') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_6' => 'required',
                'end_date_6' => 'required',
            ]);
            $start_date = $request->input('start_date_6');
            $end_date = $request->input('end_date_6');
        } elseif ($request->filtered == 'credit_note') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_7' => 'required',
                'end_date_7' => 'required',
            ]);
            $start_date = $request->input('start_date_7');
            $end_date = $request->input('end_date_7');
        }
        $startDate = $start_date;
        $endDate = $end_date;

        if ($filtered == 'sale_by_days') {
            // dd('ici');
            // $ventes = Facture::where('Statut_facture','!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->whereBetween('Date_facture', [$startDate, $endDate])->get();



            if ($agence_id !== 'Toutes') {

                // dd('ici', $agence_id);
                $ventes = DB::table('lignefactures')
                    ->select(
                        // 'factures.id',
                        // 'factures.Date_facture',
                        DB::raw('MAX(lignefactures.created_at) as date'),
                        DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                        DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                    )
                    ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                    ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                    ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    // ->groupBy('factures.id')
                    ->where('factures.agence_id', '=', $agence_id)
                    ->groupBy(DB::raw('DATE(factures.Date_facture)'))
                    ->get();

                $ag = Agence::find($agence_id);
            } else {

                $ventes = DB::table('lignefactures')
                    ->select(
                        // 'factures.id',
                        // 'factures.Date_facture',
                        DB::raw('MAX(lignefactures.created_at) as date'),
                        DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                        DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                        DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                    )
                    ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                    ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                    ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    // ->groupBy('factures.id')
                    // ->where('factures.agence_id', '=', $agence_id)
                    ->groupBy(DB::raw('DATE(factures.Date_facture)'))
                    ->get();

                $ag = 'Toutes';
            }

            // dd($ventes);

        } elseif ($filtered == 'sale_by_category') {
            // $ventes_par_categorie = Facture::where('Statut_facture','!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->where('Date_facture', '>', $startDate)->where('Date_facture', $endDate)->get();
            // $ventes_par_categorie = Facture::where('Statut_facture','!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->get();

            // dd(produits_par_categorie($request->categorie));

            // $pdt_en_stock = Stock::where('Id_Produit', '')->get();

            $select1 = DB::table('lignefactures')
                ->select(
                    // 'factures.id',
                    // 'factures.Date_facture',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe'),
                    DB::raw('MAX(categorie_produits.Libelle) as Libelle'),
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');

            // dd($request->categorie);
            if ($request->categorie) {
                $ventes_par_categorie = $select1
                    ->where('produits.Id_Categorie', $request->categorie)
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('categorie_produits.Libelle')
                    ->get();
            } else {
                $ventes_par_categorie = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('categorie_produits.Libelle')
                    ->get();
            }
        } elseif ($filtered == 'sale_by_customer') {
            $select1 = DB::table('lignefactures')
                ->select(
                    // 'factures.id',
                    // 'factures.Date_facture',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe'),
                    DB::raw('MAX(clients.Code_client) as Code_client')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');
            if ($request->client) {
                $ventes_par_client = $select1
                    ->where('clients.id', '=', $request->client) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                    ->get();
            } else {
                $ventes_par_client = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                    ->get();
            }

            // dd($ventes_par_client);
        } elseif ($filtered == 'sale_by_product') {
            $select1 = DB::table('lignefactures')
                ->select(
                    // 'factures.id',
                    // 'factures.Date_facture',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe'),
                    DB::raw('MAX(produits.Reference) as Reference'),
                    DB::raw('MAX(produits.Designation) as Designation'),
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');

            // dd($request->produit);
            if ($request->produit) {
                $ventes_par_produit = $select1
                    ->where('produits.id', '=', $request->produit) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('produits.Reference', 'produits.Designation')
                    ->get();
            } else {
                $ventes_par_produit = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('produits.Reference', 'produits.Designation')
                    ->get();
            }

            // dd($ventes_par_produit);
        } elseif ($filtered == 'sale_by_agence') {
            $select1 = DB::table('lignefactures')
                ->select(
                    // 'factures.id',
                    // 'factures.Date_facture',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_revient) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');
            if ($request->agence) {
                $ventes_par_agence = $select1
                    ->where('agences.id', '=', $request->agence) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            } else {
                $ventes_par_agence = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            }
        } elseif ($filtered == 'sale_by_user') {

            $user = User::find($request->user); // Pas besoin de '->first()' après 'find()'
            $agence = Agence::find($request->agence);

            if ($user) {
                $user = $user;
            } else {
                $user = '';
            }

            if ($agence) {
                $agence = $agence;
            } else {
                $agence = '';
            }

            // Construction de la requête de base
            $select1 = DB::table('factures')
                ->select(
                    'agences.NomAgence',
                    'factures.created_at',
                    'factures.Reference_facture',
                    'users.name',
                    'factures.Net_a_payer as Prix_revient',
                    'factures.Code_type_facture',
                    'clients.Denomination_sociale'
                )
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('users', 'factures.user_id', '=', 'users.id')
                ->where('factures.Statut_facture', '!=', 'EN COURS')
                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                ->where('factures.Statut_facture', '!=', 'ANNULEE')

                ->whereNotNull('factures.Statut_facture')
                ->whereBetween('factures.Date_facture', [$startDate_new, $endDate_new]);

            // $select1 = DB::table('lignefactures')
            //     ->select(
            //         'factures.id as facture_id',
            //         'agences.NomAgence',
            //         'factures.created_at',
            //         'factures.Reference_facture',
            //         'users.name',
            //         DB::raw('SUM(lignefactures.Prix_revient * lignefactures.Qte) as Prix_revient'),
            //         'factures.Code_type_facture',
            //         'clients.Denomination_sociale'
            //     )
            //     ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
            //     ->join('agences', 'factures.agence_id', '=', 'agences.id')
            //     ->join('clients', 'factures.client_id', '=', 'clients.id')
            //     ->join('users', 'factures.user_id', '=', 'users.id')
            //     ->where('factures.Statut_facture', '!=', 'EN COURS')
            //     ->where('factures.Statut_facture', '!=', 'INVALIDEE')
            //     ->where('factures.Statut_facture', '!=', 'ANNULEE')
            //     ->whereNotNull('factures.Statut_facture')
            //     ->whereBetween('factures.Date_facture', [$startDate_new, $endDate_new])
            //     ->groupBy('factures.id', 'agences.NomAgence', 'factures.created_at', 'factures.Reference_facture', 'users.name', 'factures.Code_type_facture', 'clients.Denomination_sociale');
            // ->get();

            // ->get();

            // dd($select2);

            // Conditions supplémentaires basées sur l'utilisateur et l'agence
            if ($agence && $user) {
                // Si les deux existent
                $journal_ventes_user = $select1
                    ->where('agences.id', $agence->id)
                    ->where('users.id', $user->id);
            } elseif ($user) {
                // Si seulement l'utilisateur existe
                $journal_ventes_user = $select1
                    ->where('users.id', $user->id);
            } elseif ($agence) {
                // Si seulement l'agence existe (Ajout de cette condition si besoin)
                $journal_ventes_user = $select1
                    ->where('agences.id', $agence->id);
            } else {
                return redirect()->back()->with('error', "Veuillez sélectionner un utilisateur ou une agence");
            }

            // Filtrage par type de facture (FV, EV, FA, EA)
            $journal_ventes_user = $journal_ventes_user->where(function ($query) {
                $query->where('factures.Code_type_facture', '!=', 'PR');
                // ->orWhere('factures.Code_type_facture', '=', 'EV')
                // ->orWhere('factures.Code_type_facture', '=', 'FA')
                // ->orWhere('factures.Code_type_facture', '=', 'EA');
            })->get();

            // dd($journal_ventes_user);
        } elseif ($filtered == 'sale_by_detail_agence') {
            $select1 = DB::table('lignefactures')
                ->select(
                    'agences.NomAgence',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('MAX(factures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as total_montant_ht'),
                    DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D) as total_tva'),
                    DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F +total_factures.TotalTVA_B + total_factures.TotalTVA_D) as total_montant_ttc'),

                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');

            if ($request->agence) {
                $ventes_par_agence = $select1
                    ->where('agences.id', '=', $request->agence) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            } else {
                $ventes_par_agence = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->where('total_factures.Statut', '!=', 0)
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            }
        } elseif ($filtered == 'sale_log') {
            $journal_ventes = Facture::where('Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->whereBetween('factures.Date_facture', [$startDate, $endDate])->get();
        } elseif ($filtered == 'credit_note') {
            /*           $facture_avoirs = Facture::where('Statut_facture', '!=', 'EN COURS')
                                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                                ->where('factures.Statut_facture', '!=', NULL)
                                ->whereIn('factures.Code_type_facture', ['EA', 'FA'])
                                ->whereBetween('factures.Date_facture', [$startDate, $endDate])->get(); */
            $facture_avoirs = Facture::where('factures.Statut_facture', '!=', 'EN COURS')
                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                ->whereNotNull('factures.Statut_facture')
                ->whereIn('factures.Code_type_facture', ['EA', 'FA'])
                ->whereBetween('factures.Date_facture', [$startDate, $endDate])
                ->leftJoin('factures as factures_orig', 'factures.idFacture_originale', '=', 'factures_orig.id')
                ->select('factures.*', 'factures_orig.Reference_facture as reference_ancienneFacture')
                ->get();

            //return $facture_avoirs;
        }
        return view('page.statistique.vente.vente', compact('ag', 'agence', 'agences', 'user', 'filtered', 'ventes', 'ventes_par_categorie', 'startDate', 'endDate', 'ventes_par_client', 'ventes_par_produit', 'ventes_par_agence', 'journal_ventes', 'facture_avoirs', 'journal_ventes_user', 'journal_ventes_user_avoir', 'texteEntetePied', 'endDate_new', 'startDate_new'));
    }

    // Vente cumulee par produit
    public function venteCumuleeParProduit(Request $request)
    {
        //  dd($request);

        $debut_periode = $request->input('dateDebut');
        $fin_periode = $request->input('dateFin');
        $produit = $request->input('produit');



        if (!$produit) {
            return to_route('vente')->with('error', 'Veuillez sélectionner un produit');
        }

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        if ($produit === 'Tous') {
            $vente_cumulee_par_produit = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'produits.Reference',
                    'produits.Designation',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                // ->where('produits.id', '=', $produit) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('produits.Reference', 'produits.Designation')
                ->get();
        } else {
            $vente_cumulee_par_produit = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'produits.Reference',
                    'produits.Designation',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->where('produits.id', '=', $produit) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('produits.Reference', 'produits.Designation')
                ->get();
        }



        // dd($vente_cumulee_par_produit);
        if (count($vente_cumulee_par_produit) <= 0) {
            return to_route('vente')->with('error', 'Aucunes données trouvées!');
        }


        $categorie = CategorieProduit::all();
        $client = Client::all();
        $produit = Produit::all();
        $fournisseur = Fournisseur::all();

        $data = [
            'date_debut' =>  $debut_periode,
            'date_fin' => $fin_periode,
            'prod' => Produit::find($produit),
            'clt' => '',
            'isFiltered' => true,
            'isFilteredClient' => false,
            'vente_cumulee_produit' => $vente_cumulee_par_produit,
            'vente_cumulee_client' => null,
            'categorie' => $categorie,
            'client' => $client,
            'produit' => $produit,
            'fournisseur' => $fournisseur,
        ];

        return view('page.statistique.vente.vente', $data);
    }

    // Vente cumulee par produit
    public function venteCumuleeParClient(Request $request)
    {
        //  dd($request);

        $debut_periode = $request->input('dateDebut');
        $fin_periode = $request->input('dateFin');
        $client = $request->input('client');

        if (!$client) {
            return to_route('vente')->with('error', 'Veuillez sélectionner un produit');
        }

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        if ($client === 'Tous') {
            $vente_cumulee_par_client = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                // ->where('clients.id', '=', $client) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', 'NORMALISEE') // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                ->get();

            $clt = 'Tous';
        } else {
            $vente_cumulee_par_client = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->where('clients.id', '=', $client) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', 'NORMALISEE') // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                ->get();

            $clt = Client::find($client);
        }



        // dd($vente_cumulee_par_client);
        if (count($vente_cumulee_par_client) <= 0) {
            return to_route('vente')->with('error', 'Aucunes données trouvées!');
        }


        $categorie = CategorieProduit::all();
        $client = Client::all();
        $produit = Produit::all();
        $fournisseur = Fournisseur::all();

        $data = [
            'date_debut' =>  $debut_periode,
            'date_fin' => $fin_periode,
            'prod' => Produit::find($produit),
            'isFiltered' => false,
            'isFilteredClient' => true,
            'vente_cumulee_client' => $vente_cumulee_par_client,
            'vente_cumulee_produit' => null,
            'categorie' => $categorie,
            'client' => $client,
            'clt' => $clt,
            'produit' => $produit,
            'fournisseur' => $fournisseur,
        ];

        return view('page.statistique.vente.vente', $data);
    }

    public function imprimerventeCumuleeParProduit(Request $request)
    {
        // dd($request->all());
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);
        // dd($dataPrint['reponse']);
        // Vérifier si le décodage JSON a réussi
        if ($dataPrint['tableData'] === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint['tableData'])) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Si tout est correct, préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'reponse' => $dataPrint['reponse'],
            'imageEntetePied' => $imageEntetePied,
        ];

        // Afficher les données pour vérification
        // dd($data);

        // Configurer les options de Dompdf
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isRemoteEnabled', true);

        $htmlContent = view('page.statistique.vente.imprimer.imprimer', compact('data'))->render();

        // Instancier Dompdf avec les options configurées
        $dompdf = new Dompdf($options);

        // Charger le contenu HTML
        $dompdf->loadHtml($htmlContent);

        // Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Afficher le PDF dans le navigateur
        $prefixe = 'VCC';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);

        // Rediriger vers la vue avec les données
        // return view('page.statistique.vente.imprimer.imprimer-VCP', compact('data'));
    }

    public function imprimerventeQuantite(Request $request)
    {
        // dd($request->all());
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);
        // dd($dataPrint['reponse']);
        // Vérifier si le décodage JSON a réussi
        if ($dataPrint['tableData'] === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint['tableData'])) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        if(isset($dataPrint['jsEntete'][0]['categorie']) && $dataPrint['jsEntete'][0]['categorie'] !== "Toutes"){
            $categorie = CategorieProduit::find($dataPrint['jsEntete'][0]['categorie']);
        }else{
            $categorie = 'Toutes';
        }

        if(isset($dataPrint['jsEntete'][0]['produit']) && $dataPrint['jsEntete'][0]['produit'] !== "Tous"){
            $produit = Produit::find($dataPrint['jsEntete'][0]['produit']);
        }else{
            $produit = 'Tous';
        }


        if($dataPrint['jsEntete'][0]['agence'] !== "Toutes"){
            $agence = Agence::find($dataPrint['jsEntete'][0]['agence']);
        }else{
            $agence = 'Toutes';
        }

        if($dataPrint['jsEntete'][0]['client'] !== "Tous"){
            $client = Client::find($dataPrint['jsEntete'][0]['client']);
        }else{
            $client = 'Tous';
        }

        // dd($client);

        $dateDebut = $dataPrint['jsEntete'][0]['start_date_1'];
        $dateFin = $dataPrint['jsEntete'][0]['end_date'];

        $dataEntete = [
            'categorie' => $categorie,
            'produit' => $produit,
            'agence' => $agence,
            'client' => $client,
            'dateDebut' => Carbon::parse($dateDebut)->format('d-m-Y H:m:s') ,
            'dateFin' => Carbon::parse($dateFin)->format(('d-m-Y H:m:s')),
        ];

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Si tout est correct, préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'dataEntete' => $dataEntete,
            'reponse' => $dataPrint['reponse'],
            'imageEntetePied' => $imageEntetePied,
        ];



        // Afficher les données pour vérification
        // dd($data);

        // Configurer les options de Dompdf
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isRemoteEnabled', true);

        $htmlContent = view('page.statistique.vente.imprimer.imprimerQ', compact('data'))->render();

        // Instancier Dompdf avec les options configurées
        $dompdf = new Dompdf($options);

        // Charger le contenu HTML
        $dompdf->loadHtml($htmlContent);

        // Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Afficher le PDF dans le navigateur
        $prefixe = 'VCC';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);

        // Rediriger vers la vue avec les données
        // return view('page.statistique.vente.imprimer.imprimer-VCP', compact('data'));
    }
    public function exportVente(Request $request)
    {
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);

        // Vérifier si le décodage JSON a réussi
        if ($dataPrint === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint)) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        // Préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'texteEntetePied' => $texteEntetePied,
            'reponse' => $dataPrint['reponse'],
        ];

        // Générer le fichier Excel
        $prefixe = 'vente_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new VenteExport($data), $nom_excel);
    }

    public function exportVenteQuantite(Request $request)
    {
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);

        // dd($dataPrint);


        // Vérifier si le décodage JSON a réussi
        if ($dataPrint === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint)) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        if(isset($dataPrint['jsEntete'][0]['categorie']) && $dataPrint['jsEntete'][0]['categorie'] !== "Toutes"){
            $categorie = CategorieProduit::find($dataPrint['jsEntete'][0]['categorie']);
        }else{
            $categorie = 'Toutes';
        }

        if(isset($dataPrint['jsEntete'][0]['produit']) && $dataPrint['jsEntete'][0]['produit'] !== "Tous"){
            $produit = Produit::find($dataPrint['jsEntete'][0]['produit']);
        }else{
            $produit = 'Tous';
        }

        if(isset($dataPrint['jsEntete'][0]['agence'] ) && $dataPrint['jsEntete'][0]['agence'] !== "Toutes"){
            $agence = Agence::find($dataPrint['jsEntete'][0]['agence']);
        }else{
            $agence = 'Toutes';
        }

        if($dataPrint['jsEntete'][0]['client'] !== "Tous"){
            $client = Client::find($dataPrint['jsEntete'][0]['client']);
        }else{
            $client = 'Tous';
        }

        // dd($dataPrint['jsEntete'][0]['client'] );

        $dateDebut = $dataPrint['jsEntete'][0]['start_date_1'];
        $dateFin = $dataPrint['jsEntete'][0]['end_date'];

        $dataEntete = [
            'categorie' => $categorie,
            'produit' => $produit,
            'agence' => $agence,
            'client' => $client,
            'dateDebut' => Carbon::parse($dateDebut)->format('d-m-Y H:m:s') ,
            'dateFin' => Carbon::parse($dateFin)->format(('d-m-Y H:m:s')),
        ];
        // dd($dataPrint['jsEntete'][0]['client']);


        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        // Préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'dataEntete' => $dataEntete,
            'texteEntetePied' => $texteEntetePied,
            'reponse' => $dataPrint['reponse'],
        ];

        // Générer le fichier Excel
        $prefixe = 'vente_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new VenteQuantiteExport($data), $nom_excel);
    }

    public function listeventeq()
    {
        $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();
        return view('page.statistique.vente.venteQ',
            [
                'agences' => $agences,
                'start_date' => '',
                'end_date' => '',
                'user' => '',
                'agence' => '',
                'ventes' => '',
                'ventes_par_categorie' => '',
                'ventes_par_client' => '',
                'ventes_par_produit' => '',
                'ventes_par_agence' => '',
                'journal_ventes' => '',
                'journal_ventes_user' => '',
                'journal_ventes_user_avoir' => '',
                'endDate_new' => '',
                'startDate_new' => '',
                'facture_avoirs' => '',
                'clt' => '',
                'prod' => '',
                'date_debut' => '',
                'date_fin' => '',
                'filtered' => '',
                'isFiltered' => false,
                'isFilteredAllVente' => false,
                'isFilteredClient' => false,
                'vente_cumulee_produit' => null,
                'vente_cumulee_client' => null
            ]
        );
    }


    public function venteStatistiquesq(Request $request)
    {
        $request->validate([
            'filtered' => 'required'
        ]);
        // dd($request);

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $ventes_par_categorie = '';
        $ventes_par_client = '';
        $ventes_par_produit = '';
        $ventes_par_agence = '';
        $journal_ventes_user = '';
        $journal_ventes_user_avoir = '';
        $journal_ventes = '';
        $ventes = '';
        $ctl = '';
        $filtered = '';
        $facture_avoirs = '';
        $endDate_new = '';
        $startDate_new = '';
        $agence = '';
        $user = '';
        $categorie = '';
        $client = '';
        $agence = '';
        $produit = '';


        if ($request->filtered == 'sale_by_days') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
        } elseif ($request->filtered == 'sale_by_category') {
            $filtered = $request->filtered;
            // @dd($filtered);
            $request->validate([
                'start_date_1' => 'required',
                'end_date_1' => 'required',
                // 'categorie' => 'required'
            ]);
            $start_date = $request->input('start_date_1');
            $end_date = $request->input('end_date_1');
        } elseif ($request->filtered == 'sale_by_customer') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_2' => 'required',
                'end_date_2' => 'required',
            ]);
            $start_date = $request->input('start_date_2');
            $end_date = $request->input('end_date_2');
        } elseif ($request->filtered == 'sale_by_product') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_3' => 'required',
                'end_date_3' => 'required',
            ]);
            $start_date = $request->input('start_date_3');
            $end_date = $request->input('end_date_3');
        } elseif ($request->filtered == 'sale_by_agence') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_4' => 'required',
                'end_date_4' => 'required',
            ]);
            $start_date = $request->input('start_date_4');
            $end_date = $request->input('end_date_4');
        } elseif ($request->filtered == 'sale_by_user') {
            // dd('je suis bien la');
            $filtered = $request->filtered;
            // $request->validate([
            //     'start_date_8' => 'required',
            //     'end_date_8' => 'required',
            //     ]);
            $start_date = $request->input('start_date_8');
            $end_date = $request->input('end_date_8');

            $start_date_new = new DateTime($start_date);
            $end_date_new = new DateTime($end_date);

            $startDate_new = $start_date_new->format('Y-m-d H:i:s');
            $endDate_new = $end_date_new->format('Y-m-d H:i:s');

            // dd($startDate, $startDate);
        } elseif ($request->filtered == 'sale_by_detail_agence') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_5' => 'required',
                'end_date_5' => 'required',
            ]);
            $start_date = $request->input('start_date_5');
            $end_date = $request->input('end_date_5');
        } elseif ($request->filtered == 'sale_log') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_6' => 'required',
                'end_date_6' => 'required',
            ]);
            $start_date = $request->input('start_date_6');
            $end_date = $request->input('end_date_6');
        } elseif ($request->filtered == 'credit_note') {
            $filtered = $request->filtered;
            $request->validate([
                'start_date_7' => 'required',
                'end_date_7' => 'required',
            ]);
            $start_date = $request->input('start_date_7');
            $end_date = $request->input('end_date_7');
        }
        $startDate = $start_date;
        $endDate = $end_date;

        if ($filtered == 'sale_by_days') {
            // $ventes = Facture::where('Statut_facture','!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->whereBetween('Date_facture', [$startDate, $endDate])->get();
            $ventes = DB::table('lignefactures')
                ->select(
                    // 'factures.id',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->distinct()
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                // ->groupBy('factures.id')
                ->get();
        } elseif ($filtered == 'sale_by_category') {

            // dd('lcdk');
            // Commencer la requête de base
            $select1 = DB::table('lignefactures')
                ->select(
                    'categorie_produits.Libelle',
                    'magasins.NomMagasin',
                    'clients.Denomination_sociale',
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    // DB::raw('(SELECT NomMagasin FROM magasins WHERE magasins.agence_id = agences.id LIMIT 1) as NomMagasin'),
                    // DB::raw('(SELECT NomAgence FROM agences WHERE agences.id = factures.agence_id LIMIT 1) as NomAgence')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id'); // Correction ici

            // ->join('agences', 'magasins.agence_id', '=', 'agence.id');

            // Filtrer par catégorie si une catégorie est sélectionnée
            if ($request->categorie != 'Toutes') {
                $select1->where('produits.Id_Categorie', $request->categorie);
            }

            // Filtrer par agence si une agence est sélectionnée
            if ($request->agence != 'Toutes') {
                $select1->where('factures.agence_id', $request->agence);
            }

            // Filtrer par client si un client est sélectionné
            if ($request->client !== 'Tous') {
                $select1->where('factures.client_id', $request->client);
            }

            // Filtrer par période si les dates sont sélectionnées
            if ($startDate && $endDate) {
                $select1->whereBetween('factures.Date_facture', [$startDate, $endDate]);
            }

            $categorie = $request->categorie != 'Toutes' ? CategorieProduit::find($request->categorie) : $request->categorie;
            $agence = $request->agence != 'Toutes' ? Agence::find($request->agence) : $request->agence;
            $client = $request->client != 'Tous' ? Client::find($request->client) : $request->client;

            // dd($agence, $categorie, $client);
            // Grouper les résultats par catégorie, agence et client
            $ventes_par_categorie = $select1->groupBy('categorie_produits.Libelle', 'magasins.NomMagasin', 'clients.Denomination_sociale')->get();
            // dd($ventes_par_categorie, $client);
            // $ventes_par_categorie = $select1->groupBy('categorie_produits.Libelle')->get();
        } elseif ($filtered == 'sale_by_customer') {
            $select1 = DB::table('lignefactures')
                ->select(
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');
            if ($request->client) {
                $ventes_par_client = $select1
                    ->where('clients.id', '=', $request->client) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                    ->get();
            } else {
                $ventes_par_client = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                    ->get();
            }
        } elseif ($filtered == 'sale_by_product') {
            // dd('ldfkf');
            $select1 = DB::table('lignefactures')
                ->select(
                    'produits.Reference',
                    'produits.Designation',
                    'magasins.NomMagasin',
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    // DB::raw('MAX(agences.NomAgence) as NomAgence'),
                    // DB::raw('MAX(clients.Denomination_sociale) as Denomination_sociale'),
                    // DB::raw('MAX(magasins.NomMagasin) as NomMagasin'),
                    'agences.NomAgence', // Ajout du nom de l'agence aux résultats
                    'clients.Denomination_sociale', // Ajout du nom du client aux résultats

                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('magasins', 'stocks.Id_Magasin', '=', 'magasins.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->join('agences', 'factures.Agence_id', '=', 'agences.id') // Liaison avec la table agences
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                // ->where('factures.Statut_facture', '!=', 'EN COURS')
                // ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                // ->where('factures.Statut_facture', '!=', NULL)
                // ->whereBetween('factures.Date_facture', [$startDate, $endDate])
                ;

            // ->join('magasins', 'produits.id', '=', 'magasins.produit_id'); // Jointure avec magasins; // Liaison avec la table clients
            // Filtrer par produit
            if ($request->produit != "Tous") {
                $select1->where('produits.id', '=', $request->produit);
            }

            // Filtrer par agence si sélectionnée
            if ($request->agence != "Toutes") {
                $select1->where('factures.Agence_id', '=', $request->agence);
            }

            // Filtrer par client si sélectionné
            if ($request->client != "Tous") {
                $select1->where('factures.client_id', '=', $request->client);
            }
            $produit = $request->produit != 'Tous' ? Produit::find($request->produit) : $request->produit;
            $agence = $request->agence != 'Toutes' ? Agence::find($request->agence) : $request->agence;
            $client = $request->client != 'Tous' ? Client::find($request->client) : $request->client;

            // Filtrer par période
            $ventes_par_produit = $select1
                ->groupBy('produits.Reference', 'produits.Designation', 'magasins.NomMagasin', 'agences.NomAgence', 'clients.Denomination_sociale')
                ->get();

                // dd($ventes_par_produit);
        } elseif ($filtered == 'sale_by_agence') {
            $select1 = DB::table('lignefactures')
                ->select(
                    'agences.NomAgence',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');
            if ($request->agence) {
                $ventes_par_agence = $select1
                    ->where('agences.id', '=', $request->agence) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            } else {
                $ventes_par_agence = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            }
        } elseif ($filtered == 'sale_by_user') {

            $user = User::find($request->user); // Pas besoin de '->first()' après 'find()'
            $agence = Agence::find($request->agence);

            if ($user) {
                $user = $user;
            } else {
                $user = '';
            }

            if ($agence) {
                $agence = $agence;
            } else {
                $agence = '';
            }

            // Construction de la requête de base
            // $select1 = DB::table('factures')
            //     ->select(
            //         'agences.NomAgence',
            //         'factures.created_at',
            //         'factures.Reference_facture',
            //         'users.name',
            //         'factures.Net_a_payer',
            //         'factures.Code_type_facture',
            //         'clients.Denomination_sociale'
            //     )
            //     ->join('agences', 'factures.agence_id', '=', 'agences.id')
            //     ->join('clients', 'factures.client_id', '=', 'clients.id')
            //     ->join('users', 'factures.user_id', '=', 'users.id')
            //     ->where('factures.Statut_facture', '!=', 'EN COURS')
            //     ->where('factures.Statut_facture', '!=', 'INVALIDEE')
            //     ->whereNotNull('factures.Statut_facture')
            //     ->whereBetween('factures.Date_facture', [$startDate_new, $endDate_new]);

            $select1 = DB::table('lignefactures')
                ->select(
                    'factures.id as facture_id',
                    'agences.NomAgence',
                    'factures.created_at',
                    'factures.Reference_facture',
                    'users.name',
                    DB::raw('SUM(lignefactures.Prix_revient * lignefactures.Qte) as Prix_revient'),
                    'factures.Code_type_facture',
                    'clients.Denomination_sociale'
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('users', 'factures.user_id', '=', 'users.id')
                ->where('factures.Statut_facture', '!=', 'EN COURS')
                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                ->whereNotNull('factures.Statut_facture')
                ->whereBetween('factures.Date_facture', [$startDate_new, $endDate_new])
                ->groupBy('factures.id', 'agences.NomAgence', 'factures.created_at', 'factures.Reference_facture', 'users.name', 'factures.Code_type_facture', 'clients.Denomination_sociale');
            // ->get();

            // ->get();

            // dd($select2);

            // Conditions supplémentaires basées sur l'utilisateur et l'agence
            if ($agence && $user) {
                // Si les deux existent
                $journal_ventes_user = $select1
                    ->where('agences.id', $agence->id)
                    ->where('users.id', $user->id);
            } elseif ($user) {
                // Si seulement l'utilisateur existe
                $journal_ventes_user = $select1
                    ->where('users.id', $user->id);
            } elseif ($agence) {
                // Si seulement l'agence existe (Ajout de cette condition si besoin)
                $journal_ventes_user = $select1
                    ->where('agences.id', $agence->id);
            } else {
                return redirect()->back()->with('error', "Veuillez sélectionner un utilisateur ou une agence");
            }

            // Filtrage par type de facture (FV, EV, FA, EA)
            $journal_ventes_user = $journal_ventes_user->where(function ($query) {
                $query->where('factures.Code_type_facture', 'FV')
                    ->orWhere('factures.Code_type_facture', 'EV')
                    ->orWhere('factures.Code_type_facture', 'FA')
                    ->orWhere('factures.Code_type_facture', 'EA');
            })->get();

            // dd($journal_ventes_user);
        } elseif ($filtered == 'sale_by_detail_agence') {
            $select1 = DB::table('lignefactures')
                ->select(
                    'agences.NomAgence',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('MAX(factures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('agences', 'factures.agence_id', '=', 'agences.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id');

            if ($request->agence) {
                $ventes_par_agence = $select1
                    ->where('agences.id', '=', $request->agence) // Utiliser la variable $produit
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            } else {
                $ventes_par_agence = $select1
                    ->where('factures.Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                    ->whereBetween('factures.Date_facture', [$startDate, $endDate]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                    ->groupBy('agences.NomAgence')
                    ->get();
            }
        } elseif ($filtered == 'sale_log') {
            $journal_ventes = Facture::where('Statut_facture', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL)->whereBetween('factures.Date_facture', [$startDate, $endDate])->get();
        } elseif ($filtered == 'credit_note') {
            /*           $facture_avoirs = Facture::where('Statut_facture', '!=', 'EN COURS')

                                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                                ->where('factures.Statut_facture', '!=', NULL)
                                ->whereIn('factures.Code_type_facture', ['EA', 'FA'])
                                ->whereBetween('factures.Date_facture', [$startDate, $endDate])->get(); */
            $facture_avoirs = Facture::where('factures.Statut_facture', '!=', 'EN COURS')
                ->where('factures.Statut_facture', '!=', 'INVALIDEE')
                ->whereNotNull('factures.Statut_facture')
                ->whereIn('factures.Code_type_facture', ['EA', 'FA'])
                ->whereBetween('factures.Date_facture', [$startDate, $endDate])
                ->leftJoin('factures as factures_orig', 'factures.idFacture_originale', '=', 'factures_orig.id')
                ->select('factures.*', 'factures_orig.Reference_facture as reference_ancienneFacture')
                ->get();

            //return $facture_avoirs;
        }

        $agences = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'asc')->get() : Agence::where('id', session()->get('site_id'))->get();

        return view('page.statistique.vente.venteQ', compact('agence', 'produit', 'categorie', 'client', 'agences', 'user', 'filtered', 'ventes', 'ventes_par_categorie', 'startDate', 'endDate', 'ventes_par_client', 'ventes_par_produit', 'ventes_par_agence', 'journal_ventes', 'facture_avoirs', 'journal_ventes_user', 'journal_ventes_user_avoir', 'texteEntetePied', 'endDate_new', 'startDate_new'));
    }
    // Vente cumulee par produit
    public function venteCumuleeParProduitq(Request $request)
    {
        //  dd($request);

        $debut_periode = $request->input('dateDebut');
        $fin_periode = $request->input('dateFin');
        $produit = $request->input('produit');



        if (!$produit) {
            return to_route('vente')->with('error', 'Veuillez sélectionner un produit');
        }

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        if ($produit === 'Tous') {
            $vente_cumulee_par_produit = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'produits.Reference',
                    'produits.Designation',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                // ->where('produits.id', '=', $produit) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('produits.Reference', 'produits.Designation')
                ->get();
        } else {
            $vente_cumulee_par_produit = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('produits', 'stocks.Id_Produit', '=', 'produits.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'produits.Reference',
                    'produits.Designation',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->where('produits.id', '=', $produit) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', '!=', 'EN COURS')->where('factures.Statut_facture', '!=', 'INVALIDEE')->where('factures.Statut_facture', '!=', NULL) // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('produits.Reference', 'produits.Designation')
                ->get();
        }



        // dd($vente_cumulee_par_produit);
        if (count($vente_cumulee_par_produit) <= 0) {
            return to_route('vente')->with('error', 'Aucunes données trouvées!');
        }


        $categorie = CategorieProduit::all();
        $client = Client::all();
        $produit = Produit::all();
        $fournisseur = Fournisseur::all();

        $data = [
            'date_debut' =>  $debut_periode,
            'date_fin' => $fin_periode,
            'prod' => Produit::find($produit),
            'clt' => '',
            'isFiltered' => true,
            'isFilteredClient' => false,
            'vente_cumulee_produit' => $vente_cumulee_par_produit,
            'vente_cumulee_client' => null,
            'categorie' => $categorie,
            'client' => $client,
            'produit' => $produit,
            'fournisseur' => $fournisseur,
        ];

        return view('page.statistique.vente.venteQ', $data);
    }

    // Vente cumulee par produit
    public function venteCumuleeParClientq(Request $request)
    {
        //  dd($request);

        $debut_periode = $request->input('dateDebut');
        $fin_periode = $request->input('dateFin');
        $client = $request->input('client');

        if (!$client) {
            return to_route('vente')->with('error', 'Veuillez sélectionner un produit');
        }

        $date_debut_periode = date('Y-m-d 00:00:00', strtotime($debut_periode));
        $date_fin_periode = date('Y-m-d 23:59:59', strtotime($fin_periode));

        if ($client === 'Tous') {
            $vente_cumulee_par_client = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                // ->where('clients.id', '=', $client) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', 'NORMALISEE') // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                ->get();

            $clt = 'Tous';
        } else {
            $vente_cumulee_par_client = DB::table('lignefactures')
                ->join('factures', 'lignefactures.facture_id', '=', 'factures.id')
                ->join('stocks', 'lignefactures.stocks_id', '=', 'stocks.id')
                ->join('clients', 'factures.client_id', '=', 'clients.id')
                ->join('groupe_taxations', 'lignefactures.GroupeTaxe_id', '=', 'groupe_taxations.id')
                ->select(
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    DB::raw('MAX(lignefactures.created_at) as date'),
                    DB::raw('SUM(lignefactures.Qte) as total_quantite'),
                    DB::raw('SUM(lignefactures.Prix_unitaire_HT) as total_montant_ht'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (groupe_taxations.valeur_taxe / 100)) as total_tva'),
                    DB::raw('SUM(lignefactures.Qte * lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100)) as total_montant_ttc'),
                    // DB::raw('SUM(lignefactures.Prix_unitaire_HT * (1 + groupe_taxations.valeur_taxe / 100) as total_montant_ttc'),
                    DB::raw('MAX(groupe_taxations.valeur_taxe) as moyenne_taxe')
                )
                ->where('clients.id', '=', $client) // Utiliser la variable $produit
                ->where('factures.Statut_facture', '=', 'NORMALISEE') // Utiliser la variable $produit
                ->whereBetween('factures.Date_facture', [$date_debut_periode, $date_fin_periode]) // Utiliser les variables $date_debut_periode et $date_fin_periode si nécessaire
                ->groupBy('clients.Code_client', 'clients.Denomination_sociale')
                ->get();

            $clt = Client::find($client);
        }

        // dd($vente_cumulee_par_client);
        if (count($vente_cumulee_par_client) <= 0) {
            return to_route('vente')->with('error', 'Aucunes données trouvées!');
        }

        $categorie = CategorieProduit::all();
        $client = Client::all();
        $produit = Produit::all();
        $fournisseur = Fournisseur::all();

        $data = [
            'date_debut' =>  $debut_periode,
            'date_fin' => $fin_periode,
            'prod' => Produit::find($produit),
            'isFiltered' => false,
            'isFilteredClient' => true,
            'vente_cumulee_client' => $vente_cumulee_par_client,
            'vente_cumulee_produit' => null,
            'categorie' => $categorie,
            'client' => $client,
            'clt' => $clt,
            'produit' => $produit,
            'fournisseur' => $fournisseur,
        ];

        return view('page.statistique.vente.venteQ', $data);
    }

    public function imprimerventeCumuleeParProduitq(Request $request)
    {
        // dd($request->all());
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);
        // dd($dataPrint['reponse']);
        // Vérifier si le décodage JSON a réussi
        if ($dataPrint['tableData'] === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint['tableData'])) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Si tout est correct, préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'reponse' => $dataPrint['reponse'],
            'imageEntetePied' => $imageEntetePied,
        ];

        // Afficher les données pour vérification
        // dd($data);

        // Configurer les options de Dompdf
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isRemoteEnabled', true);

        $htmlContent = view('page.statistique.vente.imprimer.imprimer', compact('data'))->render();

        // Instancier Dompdf avec les options configurées
        $dompdf = new Dompdf($options);

        // Charger le contenu HTML
        $dompdf->loadHtml($htmlContent);

        // Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Afficher le PDF dans le navigateur
        $prefixe = 'VCC';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';
        return $dompdf->stream($nom_pdf, ['Attachment' => false]);

        // Rediriger vers la vue avec les données
        // return view('page.statistique.vente.imprimer.imprimer-VCP', compact('data'));
    }

    public function exportVenteq(Request $request)
    {
        // Vérifier si la chaîne JSON est correctement décodée
        $dataPrint = json_decode($request->data, true);

        // Vérifier si le décodage JSON a réussi
        if ($dataPrint === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si le décodage a échoué, afficher un message d'erreur
            return response()->json(['error' => 'Erreur de décodage JSON'], 400);
        }

        // Vérifier si $dataPrint est un tableau
        if (!is_array($dataPrint)) {
            // Si $dataPrint n'est pas un tableau, afficher un message d'erreur
            return response()->json(['error' => 'Les données doivent être au format JSON valide'], 400);
        }

        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        // Préparer les données pour l'affichage ou le téléchargement
        $data = [
            'dataPrint' => $dataPrint['tableData'],
            'texteEntetePied' => $texteEntetePied,
            'reponse' => $dataPrint['reponse'],
        ];

        // Générer le fichier Excel
        $prefixe = 'vente_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new VenteExport($data), $nom_excel);
    }


}
