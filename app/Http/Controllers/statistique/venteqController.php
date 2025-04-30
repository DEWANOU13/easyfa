<?php
namespace App\Http\Controllers\statistique;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\Fournisseur;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenteCumuleeClientExport;
use App\Exports\VenteCumuleeProduitExport;
use App\Exports\VenteExport;
use App\Models\Agence;
use App\Models\Image;
use App\Models\User;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

class venteqController extends Controller
{
    public function listeventeq()
    {
        return view(
            'page.statistique.vente.venteq',
            [
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
                          
}
