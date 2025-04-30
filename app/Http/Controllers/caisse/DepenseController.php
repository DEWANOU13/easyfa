<?php

namespace App\Http\Controllers\caisse;

use Exception;
use Carbon\Carbon;
use App\Models\Caisse;
use Illuminate\Http\Request;
use App\Models\OperationCaisse;
use App\Models\CategorieDepense;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DepenseController extends Controller
{
    public function index()
    {
        $this->authorize('voir-liste-recette');
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
        ->select('operation_caisses.*', 'categorie_depenses.designation', 'caisses.statut')
        ->where('operation_caisses.caisse_id', '=', 0)
        ->get();
        $listeCategorieDepense = CategorieDepense::all();
        return view('page.caisse.depense.depense',[
            'listeCaisse' => $listeCaisse,
            'listeCategorieDepense' => $listeCategorieDepense ,
            'annees' => $annees,
            'detailCaisse' => $detailCaisse,
        ]);
    }
    public function getDetailCaisse2($id)
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
                ->join('categorie_depenses', 'categorie_depenses.id', '=', 'operation_caisses.categorie_depense_id')
                ->select('operation_caisses.*', 'categorie_depenses.designation','caisses.statut as statut_caisse')
                ->where('operation_caisses.caisse_id', '=', $id)
                ->get();


            return view('page.caisse.depense.depense', [
                'detailCaisse' => $detailCaisse,
                'listeCaisse' => $listeCaisse,
                'annees' => $annees,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function new_depense()
    {
        $listeCategorieDepense = CategorieDepense::where('statut', 1)->get();
        $site_id = session()->get('site_id');
        $caisse = Caisse::join('users', 'caisses.user_id', '=', 'users.id')
            ->select('caisses.*','users.name as user_name')
            ->where('agence_id', $site_id)
            ->where('user_id',auth()->user()->id)
            ->where('statut', 1)
            ->first();

        if (!$caisse) {
            return redirect()->route('caisse_index')->with('error', "Veuillez ouvrir une caisse avant d'ajouter une dépense.");
        }

        return view('page.caisse.depense.nouveau',
        [
            'caisse' => $caisse,
            'listeCategorieDepense' => $listeCategorieDepense,
        ]

    );
    }
    public function filterDepense(Request $request)
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
                $annees = Caisse::selectRaw('YEAR(created_at) as annee')
        ->distinct()
        ->pluck('annee');

                $listeCategorieDepense = CategorieDepense::all();
                return view('page.caisse.depense.depense',[
                    'listeCaisse' => $listeCaisse,
                    'listeCategorieDepense' => $listeCategorieDepense ,
                    'annees' => $annees,
                    'detailCaisse' => $detailCaisse,
                ]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function storeDepense(Request $request)
    {
        $request->validate([
            'caisse_id' => 'required',
            'categorie_depense_id' => 'required',
            'montant' => 'required',
            'description' => 'required',
        ]);




        $id = $request->input('caisse_id');
        $categorie_depense_id = $request->input('categorie_depense_id');
        $montant = $request->input('montant');
        $description = $request->input('description');


        $caisse = Caisse::findOrFail($id);

        $Agence_id =  $caisse->agence_id;

        if($caisse->fonds_actuel < $montant){
            return to_route('depense')->with('error', 'Le fond actuel de la caisse est insuffisant.');
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

        $operation = new OperationCaisse();
        $operation->reference_operation = $reference_operation;
        $operation->statut = 'EFFECTUEE';
        $operation->caisse_id = $caisse->id;
        $operation->type = 'sortie';
        $operation->montant = $montant;
        $operation->description = $description;
        $operation->categorie_depense_id = $categorie_depense_id;
        $operation->user_id = auth()->user()->id;
        $operation->agence_id = $caisse->agence_id;
        $operation->save();

        // Mettre à jour les fonds actuels de la caisse
        $caisse->fonds_actuel -= $montant;
        $caisse->save();

        return to_route('depense')->with('success', 'Dépense enregistrée avec succès.');
    }

    public function annulerDepense(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'description' => 'required',
        ]);




        $id = $request->input('id');
        $description = $request->input('description');


        $Operationcaisse = OperationCaisse::findOrFail($id);

        $op_user = $Operationcaisse->user_id;
        $caisse_id = $Operationcaisse->caisse_id;

        $user_connecterId = auth()->user()->id;
        if ($op_user != $user_connecterId) {
            return to_route('depense')->with('error', 'Vous n\'avez pas les autorisations pour annuler cette depense.');
        }

        $caisse = Caisse::findOrFail($caisse_id);
        $caisse -> fonds_actuel += $Operationcaisse->montant;
        $caisse -> save();

        $Operationcaisse->statut = 'ANNULEE';
        $Operationcaisse->description = $description;
        $Operationcaisse->save();



        return to_route('depense')->with('success', 'Dépense enregistrée avec succès.');
    }
}
