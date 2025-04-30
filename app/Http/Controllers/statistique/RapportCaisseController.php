<?php

namespace App\Http\Controllers\statistique;

use Exception;
use App\Models\User;
use App\Models\Image;
use App\Models\Agence;
use App\Models\AgenceUser;
use Illuminate\Http\Request;
use App\Models\OperationCaisse;
use App\Exports\RapportCaisseExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;

class RapportCaisseController extends Controller
{
    public function index()
    {
        $this->authorize('statistique-rapport-caisse');

        try {

            // $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();

            $utilisateur = User::wherenot('id',0)->wherenot('id',2)->get();

            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

            return view('page.statistique.caisse.caisse',
                [
                    'listeAgence' => $listeAgence,
                    'utilisateur' => $utilisateur
                ]

            );
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function rapportCaisseReq(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'user' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $user = $data['user'];
        $agence = $data['agence'];

        $query = OperationCaisse::join('agences', 'agences.id', '=', 'operation_caisses.agence_id')
        ->join('users', 'users.id', '=', 'operation_caisses.user_id')
        ->leftjoin('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
        ->leftjoin('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
        ->select('operation_caisses.*', 'categorie_depenses.designation as designation_depense', 'categorie_recettes.designation as designation_recette')
        ->whereBetween('operation_caisses.created_at', [$dateDebut, $dateFin]);

        if ($user != 'Tous') {
            $query->where('operation_caisses.user_id', $user);
        }

        if ($agence != 'Tous') {
            $query->where('operation_caisses.agence_id', $agence);
        }

        $listerpportCaisse = $query->get();

        $infoUser = $user !== 'Tous' ? User::find($user) : null;
        $infoAgence = $agence !== 'Tous' ? Agence::find($agence) : null;

        return response()->json([
            'listerpportCaisse' => $listerpportCaisse,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoUser' => $infoUser,
            'infoAgence' => $infoAgence
        ]);

        //dd($listerpportCaisse);

    }
    public function export_excel_rapportCaisse(Request $request)
    {
        $data = $request->validate([
            'tableRapportCaisseData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoUser' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new RapportCaisseExport($data, $texteEntetePied);

            $fileName = 'Rapport_caisse_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
    }
    public function export_rapport_caisse_pdf(Request $request)
    {
        try {

            $data = $request->validate([
                'tableRapportCaisseData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoUser' => '',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.caisse.document.rapport_caissePdf', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoUser' => $data['infoUser'],
                'infoAgence' => $data['infoAgence'],

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Rapport_caisse_'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

       } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
}
