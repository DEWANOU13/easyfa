<?php

namespace App\Http\Controllers\parametre_administration;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Lignefacture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Facture\listeFactureRequest;
use App\Http\Requests\FilterAgenceId\FilterAgenceId;
use App\Http\Requests\Facture\listeLigneFactureRequest;
use App\Http\Requests\FilterAgenceId\FilterAgenceIdRequestLigneFacture;

class Facture_FF_FLFController extends Controller
{
    public function  index(listeFactureRequest $requestFacture, listeLigneFactureRequest $requestLigneFacture, FilterAgenceId $request, FilterAgenceIdRequestLigneFacture $requestFilterLigneFacture)
    {
        $this->authorize('show-liste-facture');

        $isFiltered = $requestLigneFacture->filled('debut2') || $requestLigneFacture->filled('fin2') || isset($_GET['agenceFilter_id2']);

        $debut1 = date('Y-m-d 00:00:00', strtotime($requestFacture->validated('debut1')));
        $fin1 = date('Y-m-d 23:59:59', strtotime($requestFacture->validated('fin1')));

        $debut2 = date('Y-m-d 00:00:00', strtotime($requestLigneFacture->validated('debut2')));
        $fin2 = date('Y-m-d 23:59:59', strtotime($requestLigneFacture->validated('fin2')));

        $facturesQuery = Facture::whereIn('Code_type_facture', ['FV', 'EV'])
            ->select('id', 'Reference_facture as reference_facture', 'Net_a_payer as montant', 'Date_facture', 'Code_signature', 'Date_signature', 'Net_a_payer', 'Code_type_facture as type_facture', 'IdFacture_originale as idFactOrig', 'client_id', 'agence_id', 'created_at');

        if ($requestFacture->validated('debut1')) {
            $facturesQuery = $facturesQuery->where('created_at', '>=', $debut1);
        }

        if ($requestFacture->validated('fin1')) {
            $facturesQuery = $facturesQuery->where('created_at', '<=', $fin1);
        }

        if ($request->filled('agenceFilter_id1')) {
            if (is_array(getIdAgenceFilter($request->input('agenceFilter_id1')))) {
                $facturesQuery = $facturesQuery->whereIn('agence_id', getIdAgenceFilter($request->input('agenceFilter_id1')));
            } else {
                $facturesQuery = $facturesQuery->where('agence_id', '=', getIdAgenceFilter($request->input('agenceFilter_id1')));
            }
        } else {
            if (is_array(getIdAgenceByUser())) {
                $facturesQuery = $facturesQuery->whereIn('agence_id', getIdAgenceByUser());
            } else {
                $facturesQuery = $facturesQuery->where('agence_id', '=', getIdAgenceByUser());
            }
        }

        $lignesFactureQuery = Lignefacture::select('id', 'stocks_id', 'GroupeTaxe_id', 'facture_id', 'Taux_remise', 'Prix_unitaire_HT', 'Prix_revient', 'Qte', 'created_at');

        if ($requestLigneFacture->validated('debut2')) {
            $lignesFactureQuery = $lignesFactureQuery->where('created_at', '>=', $debut2);
        }

        if ($requestLigneFacture->validated('fin2')) {
            $lignesFactureQuery = $lignesFactureQuery->where('created_at', '<=', $fin2);
        }

        if ($requestFilterLigneFacture->filled('agenceFilter_id2')) {
            $agenceIds = getIdAgenceFilter($requestFilterLigneFacture->input('agenceFilter_id2'));
            if (is_array(getIdAgenceFilter($requestFilterLigneFacture->input('agenceFilter_id2')))) {
                $lignesFactureQuery = $lignesFactureQuery->whereHas('facture', function ($query) use ($agenceIds) {
                    $query->whereIn('agence_id', $agenceIds);
                });
            } else {
                $lignesFactureQuery = $lignesFactureQuery->whereHas('facture', function ($query) use ($agenceIds) {
                    $query->where('agence_id', '=',$agenceIds);
                });
            }
        } else {
            $agenceIds = getIdAgenceByUser();
            if (is_array(getIdAgenceByUser())) {
                $lignesFactureQuery = $lignesFactureQuery->whereHas('facture', function ($query) use ($agenceIds) {
                    $query->whereIn('agence_id', $agenceIds);
                });
            } else {
                $lignesFactureQuery = $lignesFactureQuery->whereHas('facture', function ($query) use ($agenceIds) {
                    $query->where('agence_id', '=', $agenceIds);
                });
            }
        }

        //dd($lignesFactureQuery->get());

        $factures = $facturesQuery->orderBy('created_at', 'desc')->get();
        $lignesFactures = $lignesFactureQuery->orderBy('created_at', 'desc')->get();

        return view('page.parametre_administration.facture_FF__FLF.facture_FF__FLF', [
            'factures' => $factures,
            'lignesFactures' => $lignesFactures,
            'isFiltered' => $isFiltered,
            'requestFacture' => $requestFacture,
            'requestLigneFacture' => $requestLigneFacture
        ]);
    }
    public function reqFacture_ff(Request $request,  listeLigneFactureRequest $requestLigneFacture,listeFactureRequest $requestFacture,)
    {

        $isFiltered = $requestLigneFacture->filled('debut2') || $requestLigneFacture->filled('fin2') || isset($_GET['agenceFilter_id2']);

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
        ->select('id', 'Reference_facture as reference_facture', 'Net_a_payer as montant', 'Date_facture', 'Code_signature', 'Date_signature', 'Net_a_payer', 'Code_type_facture as type_facture', 'IdFacture_originale as idFactOrig', 'client_id', 'agence_id', 'created_at')
        ->whereBetween('factures.created_at', [$debut1, $fin1]);

        $lignesFactureQuery = Lignefacture::select('id', 'stocks_id', 'GroupeTaxe_id', 'facture_id', 'Taux_remise', 'Prix_unitaire_HT', 'Prix_revient', 'Qte', 'created_at');
        $lignesFactures = $lignesFactureQuery->orderBy('created_at', 'desc')->get();


        $factures = $facturesQuery->orderBy('created_at', 'desc')->get();

        return view('page.parametre_administration.facture_FF__FLF.facture_FF__FLF', [
            'factures' => $factures,
            'isFiltered' => $isFiltered,
            'requestFacture' => $requestFacture,
            'requestLigneFacture' => $requestLigneFacture,
            'lignesFactures' => $lignesFactures,



        ]);


    }
}
