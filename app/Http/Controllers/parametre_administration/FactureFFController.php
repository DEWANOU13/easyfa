<?php

namespace App\Http\Controllers\parametre_administration;

use App\Exports\FactureFFExport;
use App\Exports\FactureFLFExport;
use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\ArchiveFacture;
use App\Models\ArchiveLigneFacture;
use App\Models\Facture;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class FactureFFController extends Controller
{
    public function index(){
        try {
            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
        return view('page.parametre_administration.facture_FF__FLF.factureFF_FLF',[
            'listeAgence' => $listeAgence
        ]);
    }
    public function factureFF( Request $request){
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:date_debut',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
       // $dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $agence = $data['agence'];

        $queryBase = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
        ->join('clients', 'clients.id', '=', 'factures.client_id')
        ->join('users', 'users.id', '=', 'factures.user_id')
        ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
        ->select(
            'factures.id',
            'factures.Reference_facture',
            'factures.Code_signature',
            'factures.Date_facture',
            'factures.Net_a_payer',
            'agences.NomAgence',
            'clients.Denomination_sociale',
            'clients.Adresse_client',
            'clients.Numero_ifu',
            'clients.Pays',

            'users.name as user_name',
            DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
            DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA')
        )

        ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
        ->where('factures.Code_signature', '!=', 'NULL')
        ->whereIn('factures.Code_type_facture', ['FV', 'EV', 'FA', 'EA'])

        ->groupBy(
            'factures.id',
            'factures.Reference_facture',
            'factures.Code_signature',
            'factures.Date_facture',
            'factures.Net_a_payer',
            'agences.NomAgence',
            'clients.Denomination_sociale',
            'clients.Adresse_client',
            'clients.Numero_ifu',
            'clients.Pays',
            'users.name',
            'total_factures.facture_id',
            'factures.idFacture_originale'
        );

        if ($agence !== 'Toutes') {
            $queryBase->where('factures.agence_id', $agence);
        }

        $listeFacture = $queryBase->get();
        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        return response()->json([
            'listeFacture' => $listeFacture,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoAgence' => $infoAgence,
        ]);

    }

    public function export_factureFF_csv(Request $request)
    {
        try {
            $data = $request->validate([
                'tableFactureData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoAgence' => '',
            ]);
            $tableFactureData = $data['tableFactureData'];
            return Excel::download(new FactureFFExport($tableFactureData), 'facturesFF.csv');




       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function facctureFLF(Request $request){
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:date_debut',
            'agence' => 'required',
        ]);
        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
       // $dateFinH = Carbon::parse($data['dateFin'])->endOfDay();
        $agence = $data['agence'];

       /*  $queryBase = ArchiveLigneFacture::join('archive_factures', 'archive_factures.id', '=', 'archive_ligne_factures.archive_factures_id')
        ->join('agences', 'agences.id', '=', 'archive_factures.agence_id')
        ->join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
        ->select(
            'archive_factures.Date_facture',
            'archive_factures.Reference_facture',
            'archive_factures.Date_facture',
            'archive_ligne_factures.Produit_designation',
            'groupe_taxations.Code_lettre',
            'archive_ligne_factures.Qte',
            'archive_ligne_factures.Prix_revient',
            DB::raw('archive_ligne_factures.Prix_unitaire_HT * archive_ligne_factures.Qte as total_ht')
        )
        ->whereBetween('archive_ligne_factures.created_at', [$dateDebut, $dateFin])
        ->where('archive_factures.Code_signature', '!=', 'NULL')
        ->whereIn('archive_factures.Code_type_facture', ['FV', 'EV', 'FA', 'EA']);
        if ($agence !== 'Toutes') {
            $queryBase->where('archive_factures.agence_id', $agence);
        }



        $listeLigneFacture = $queryBase->get();
        dd($listeLigneFacture); */


/*         $query = ArchiveFacture::leftjoin('archive_ligne_factures', 'archive_factures.id', '=', 'archive_ligne_factures.archive_factures_id')
        ->join('agences', 'agences.id', '=', 'archive_factures.agence_id')
        ->leftjoin('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
        ->select(
            'archive_factures.Date_facture',
            'archive_factures.Reference_facture',
            'archive_factures.Date_facture',
            'archive_ligne_factures.Produit_designation',
            'groupe_taxations.Code_lettre',
            'archive_ligne_factures.Qte',
            'archive_ligne_factures.Prix_revient',
            DB::raw('archive_ligne_factures.Prix_unitaire_HT * archive_ligne_factures.Qte as total_ht')
        )
        ->whereBetween('archive_ligne_factures.created_at', [$dateDebut, $dateFin])
        ->where('archive_factures.Code_signature', '!=', 'NULL');
        if ($agence !== 'Toutes') {
            $query->where('archive_factures.agence_id', $agence);
        }
        $listeLigneFacture = $query->get();
        dd($listeLigneFacture); */
        $query = ArchiveFacture::leftJoin('archive_ligne_factures', function ($join) {
            $join->on('archive_factures.id', '=', 'archive_ligne_factures.archive_factures_id')
                 ->whereIn('archive_factures.Code_type_facture', ['FV','EV']); // Lignes directes des FV
        })
        ->leftJoin('archive_factures as facture_originale', function ($join) {
            $join->on('facture_originale.facture_id', '=', 'archive_factures.idFacture_originale')
                 ->whereIn('archive_factures.Code_type_facture', ['FA','EA']); // Lignes des FV associées aux FA
        })
        ->leftJoin('archive_ligne_factures as lignes_fv', 'lignes_fv.archive_factures_id', '=', 'facture_originale.id')
        ->join('agences', 'agences.id', '=', 'archive_factures.agence_id')
        ->leftJoin('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
        ->select(
            'archive_factures.id as id_facture',
            'archive_factures.Code_type_facture as type_facture',
            'archive_factures.Date_facture',
            'archive_factures.Reference_facture',
            DB::raw('COALESCE(archive_ligne_factures.Produit_designation, lignes_fv.Produit_designation) as Produit_designation'),
            DB::raw('COALESCE(groupe_taxations.Code_lettre, (SELECT Code_lettre FROM groupe_taxations WHERE id = lignes_fv.GroupeTaxe_id)) as Code_lettre'),
            DB::raw('COALESCE(archive_ligne_factures.Qte, lignes_fv.Qte) as Qte'),
            DB::raw('COALESCE(archive_ligne_factures.Prix_revient, lignes_fv.Prix_revient) as Prix_revient'),
            DB::raw('COALESCE(archive_ligne_factures.Prix_unitaire_HT * archive_ligne_factures.Qte, lignes_fv.Prix_unitaire_HT * lignes_fv.Qte) as total_ht')
        )
        ->whereBetween('archive_factures.created_at', [$dateDebut, $dateFin])
        ->where('archive_factures.Code_signature', '!=', 'NULL')
        ->whereIn('archive_factures.Code_type_facture', ['FV','EV','EA','FA']);

        if ($agence !== 'Toutes') {
            $query->where('archive_factures.agence_id', $agence);
        }

        $listeLigneFacture = $query->get();
       // dd($listeLigneFacture);


        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        return response()->json([
            'listeLigneFacture' => $listeLigneFacture,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoAgence' => $infoAgence,
        ]);

    }
    public function export_factureFLF_csv(Request $request)
    {
        try {
            $data = $request->validate([
                'tableFactureFlFData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoAgence' => '',
            ]);
            $tableFactureFlFData = $data['tableFactureFlFData'];
            return Excel::download(new FactureFLFExport($tableFactureFlFData), 'facturesFF.csv');




       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
}
