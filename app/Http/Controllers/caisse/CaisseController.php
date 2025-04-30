<?php

namespace App\Http\Controllers\caisse;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Caisse;
use Illuminate\Http\Request;
use App\Exports\CaisseExport;
use App\Models\OperationCaisse;
use App\Models\CategorieDepense;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CaisseController extends Controller
{
    public function index()
    {
        $this->authorize('voir-caisse');
        $annees = Caisse::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $Agence_id = session()->get('site_id');
        $listeCaisse = Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
            ->join('users', 'caisses.user_id', '=', 'users.id')
            ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
            ->whereMonth('caisses.created_at', $currentMonth)
            ->whereYear('caisses.created_at', $currentYear)
            ->where('caisses.agence_id',$Agence_id)
            ->orderBy('caisses.created_at', 'desc')
        ->get();


        $detailCaisse = OperationCaisse::join('caisses', 'caisses.id', '=', 'operation_caisses.caisse_id')
        ->join('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
        ->select('operation_caisses.*', 'categorie_depenses.designation')
        ->where('operation_caisses.caisse_id', '=', 0)
        ->get();
        $listeCategorieDepense = CategorieDepense::all();
        return view('page.caisse.caisse.caisse',[
            'listeCaisse' => $listeCaisse,
            'listeCategorieDepense' => $listeCategorieDepense ,
            'annees' => $annees,
            'detailCaisse' => $detailCaisse,
        ]);
    }
    public function ouvrirCaisse(Request $request)
    {
        $data = $request->only(['fonds_initial']);

        $validatorRules = [
            'fonds_initial' => 'required',

        ];

        $validationMessages = [
            'fonds_initial.required' => "Le fond initial est est requise",

        ];
        $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
        if ($validatorResult->fails()) {
            return redirect()->back()->withErrors($validatorResult)->withInput();
            return to_route('caisse_index')->with('error', 'Veuillez renseigner  des informations valide');
        }

        $Agence_id = session()->get('site_id');

        $caisse = Caisse::where('agence_id', $Agence_id)
        ->where('user_id',auth()->user()->id)
        ->where('statut', 1)
        ->first();

    if ($caisse !== null) {
        return redirect()->route('caisse_index')->with('error', 'Vous avez deja une caisse ouverte.');
    }


    $lastDigitOfYear = substr(Carbon::now()->year, -2);

    // Obtenez le dernier numéro de référence enregistré
    $lastReference = OperationCaisse::where('agence_id','=' ,session()->get('site_id'))
    ->count();

    // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
    if ($lastReference === 0) {
        $incrementedReferenceNumber = '0000001';
    } else {
        // Obtenez le dernier numéro de référence et incrémentez-le
        $lastReference = OperationCaisse::where('agence_id','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
        $lastReferenceNumber = substr($lastReference->reference_operation, -7); // Obtenez les 7 derniers chiffres
        $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);

    }

    $reference_operation = "$Agence_id/{$lastDigitOfYear}/OC/{$incrementedReferenceNumber}";

        $caisse = new Caisse();
        $caisse->fonds_initial = $request->input('fonds_initial');
        $caisse->fonds_actuel = $request->input('fonds_initial');
        $caisse->date_ouverture = now();
        $caisse->statut = '1';
        $caisse->agence_id = $Agence_id;
        $caisse->user_id = auth()->user()->id; // Assurez-vous que l'utilisateur est authentifié
        $caisse->save();

        // Enregistrer l'opération d'ouverture
        $operation = new OperationCaisse();
        $operation->reference_operation = $reference_operation;
        $operation->statut = 'EFFECTUEE';
        $operation->caisse_id = $caisse->id;
        $operation->type = 'ouverture';
        $operation->montant = $caisse->fonds_initial;
        $operation->user_id = auth()->user()->id;
        $operation->agence_id = $caisse->agence_id;
        $operation->save();

        return redirect()->back()->with('success', 'Caisse ouverte avec succès.');
    }

    public function getDetailCaisse($id)
    {
        try {
            $annees = Caisse::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;
                $Agence_id = session()->get('site_id');
            $listeCaisse = Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
                ->join('users', 'caisses.user_id', '=', 'users.id')
                ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
                ->whereMonth('caisses.created_at', $currentMonth)
                ->whereYear('caisses.created_at', $currentYear)
                ->where('caisses.agence_id',$Agence_id)
                ->orderBy('caisses.created_at', 'desc')
                ->get();

            $detailCaisse = OperationCaisse::join('caisses', 'caisses.id', '=', 'operation_caisses.caisse_id')
                ->leftjoin('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
                ->leftjoin('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
                ->select('operation_caisses.*', 'categorie_depenses.designation as designation_depense', 'categorie_recettes.designation as designation_recette')
                ->where('operation_caisses.caisse_id', '=', $id)
                ->get();


            return view('page.caisse.caisse.caisse', [
                'detailCaisse' => $detailCaisse,
                'listeCaisse' => $listeCaisse,
                'annees' => $annees,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function fermerCaisse(Request $request, $id)
    {
        $caisse = Caisse::find($id);
        $user_caisse = $caisse->user_id;

        $user_connecterId = auth()->user()->id;
        if ($user_caisse != $user_connecterId) {
            return redirect()->back()->with('error', 'Vous n\'etses pas les autorisé à fermer la caisse d\'un autre utilisateur.');
        }
        if($caisse->statut == "0"){
            return redirect()->back()->with('error', 'La caisse est deja fermée.');
        }
        $caisse->statut = "0";
        $caisse->date_fermeture = now();
        $caisse->save();

        $Agence_id = $caisse->agence_id;

        $lastDigitOfYear = substr(Carbon::now()->year, -2);

        // Obtenez le dernier numéro de référence enregistré
        $lastReference = OperationCaisse::where('agence_id','=' ,session()->get('site_id'))
        ->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '0000001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            $lastReference = OperationCaisse::where('agence_id','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->reference_operation, -7); // Obtenez les 7 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);

        }

        $reference_operation = "$Agence_id/{$lastDigitOfYear}/OC/{$incrementedReferenceNumber}";

        $operation = new OperationCaisse();
        $operation->reference_operation = $reference_operation;
        $operation->statut = 'EFFECTUEE';
        $operation->caisse_id = $caisse->id;
        $operation->type = 'fermeture';
        $operation->montant = $caisse->fonds_actuel;
        $operation->user_id = auth()->user()->id;
        $operation->agence_id = $caisse->agence_id;
        $operation->save();

        return redirect()->back()->with('success', 'Caisse fermée avec succès.');
    }

    public function filterCaisse(Request $request)
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
                $query =Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
                ->join('users', 'caisses.user_id', '=', 'users.id')
                ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
                ->where('caisses.agence_id',$Agence_id)
                ->orderBy('caisses.created_at', 'desc');
            } else {
                $query =Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
                ->join('users', 'caisses.user_id', '=', 'users.id')
                ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
                ->where('caisses.agence_id',$Agence_id)
                ->orderBy('caisses.created_at', 'desc');
            }

            // Application des filtres en fonction des paramètres
            if ($annee) {
                $query->whereYear('caisses.created_at', $annee);
            }

            if ($month && !$startDate && !$endDate) {
                // Si seul le mois est fourni, appliquer le filtre par mois et année
                $query->whereMonth('caisses.created_at', $month);
                $query->whereYear('caisses.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
            }

            if ($startDate && $endDate && !$month && !$annee) {
                // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
                $query->where('caisses.created_at', '>=', $startDate)
                      ->where('caisses.created_at', '<=', $endDate);
            }

            // Exécuter la requête
            $listeCaisse = $query->get();

            // Autres données nécessaires pour la vue
            $detailCaisse = OperationCaisse::join('caisses', 'caisses.id', '=', 'operation_caisses.caisse_id')
                ->join('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
                ->select('operation_caisses.*', 'categorie_depenses.designation')
                ->where('operation_caisses.caisse_id', '=', 0)
                ->get();

                $listeCategorieDepense = CategorieDepense::all();
                $annees = Caisse::selectRaw('YEAR(created_at) as annee')
                ->distinct()
                ->pluck('annee');

                return view('page.caisse.caisse.caisse', [
                    'detailCaisse' => $detailCaisse,
                    'listeCaisse' => $listeCaisse,
                    'annees' => $annees,
                ]);

        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function caisseExportPdf($id)
    {

        $infoCaisse = Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
        ->join('users', 'caisses.user_id', '=', 'users.id')
        ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
        ->where('caisses.id',$id)
        ->first();


        $detailCaisse = OperationCaisse::join('caisses', 'caisses.id', '=', 'operation_caisses.caisse_id')
        ->leftjoin('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
        ->leftjoin('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
        ->select('operation_caisses.*', 'categorie_depenses.designation as designation_depense', 'categorie_recettes.designation as designation_recette')
        ->where('operation_caisses.caisse_id', '=', $id)
        ->orderBy('operation_caisses.created_at', 'ASC')
        ->get();


        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();


        $html = view('page.caisse.document.caissePDF', [
            'infoCaisse' => $infoCaisse,
            'detailCaisse' => $detailCaisse,
            'imageEntetePied' => $imageEntetePied,

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Caisse_'.now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);


    }
    public function caisseExportExcel($id)
    {
        $infoCaisse = Caisse::join('agences', 'caisses.agence_id', '=', 'agences.id')
        ->join('users', 'caisses.user_id', '=', 'users.id')
        ->select('caisses.*', 'agences.NomAgence', 'users.name as user_name')
        ->where('caisses.id',$id)
        ->first();


        $detailCaisse = OperationCaisse::join('caisses', 'caisses.id', '=', 'operation_caisses.caisse_id')
        ->leftjoin('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
        ->leftjoin('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
        ->select('operation_caisses.*', 'categorie_depenses.designation as designation_depense', 'categorie_recettes.designation as designation_recette')
        ->where('operation_caisses.caisse_id', '=', $id)
        ->orderBy('operation_caisses.created_at', 'ASC')
        ->get();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new CaisseExport($infoCaisse,$detailCaisse, $texteEntetePied);

            $fileName = 'Caisse_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
    }
}
