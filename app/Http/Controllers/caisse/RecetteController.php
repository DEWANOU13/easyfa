<?php

namespace App\Http\Controllers\caisse;

use Exception;
use Carbon\Carbon;
use App\Models\Caisse;
use Illuminate\Http\Request;
use App\Models\OperationCaisse;
use App\Models\CategorieDepense;
use App\Http\Controllers\Controller;
use App\Models\CategorieRecette;
use Illuminate\Support\Facades\Auth;

class RecetteController extends Controller
{
    public function index()
    {
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
        ->join('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
        ->select('operation_caisses.*', 'categorie_recettes.designation')
        ->where('operation_caisses.caisse_id', '=', 0)
        ->get();
        $listeCategorieRecette = CategorieRecette::all();
        return view('page.caisse.recette.recette',[
            'listeCaisse' => $listeCaisse,
            'listeCategorieRecette' => $listeCategorieRecette ,
            'annees' => $annees,
            'detailCaisse' => $detailCaisse,
        ]);
    }

    public function getDetailCaisse3($id)
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
                ->join('categorie_recettes', 'categorie_recettes.id', '=', 'operation_caisses.categorie_recette_id')
                ->select('operation_caisses.*', 'categorie_recettes.designation')
                ->where('operation_caisses.caisse_id', '=', $id)
                ->get();


            return view('page.caisse.recette.recette', [
                'detailCaisse' => $detailCaisse,
                'listeCaisse' => $listeCaisse,
                'annees' => $annees,
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function new_recette()
    {
        $listeCategorieRecette = CategorieRecette::where('statut', 1)->get();

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

        return view('page.caisse.recette.nouveau',
        [
            'caisse' => $caisse,
            'listeCategorieRecette' => $listeCategorieRecette,
        ]

    );
    }

    public function storeRecette(Request $request)
    {
        $request->validate([
            'caisse_id' => 'required',
            'categorie_recette_id' => 'required',
            'montant' => 'required',
            'description' => 'required',
        ]);




        $id = $request->input('caisse_id');
        $categorie_recette_id = $request->input('categorie_recette_id');
        $montant = $request->input('montant');
        $description = $request->input('description');


        $caisse = Caisse::findOrFail($id);

        $Agence_id =  $caisse->agence_id;

      /*   if($caisse->fonds_actuel < $montant){
            return to_route('recette')->with('error', 'Le fond actuel de la caisse est insuffisant.');
        } */

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
        $operation->type = 'entree';
        $operation->montant = $montant;
        $operation->description = $description;
        $operation->categorie_recette_id = $categorie_recette_id;
        $operation->user_id = auth()->user()->id;
        $operation->agence_id = $caisse->agence_id;
        $operation->save();

        // Mettre à jour les fonds actuels de la caisse
        $caisse->fonds_actuel += $montant;
        $caisse->save();

        return to_route('recette')->with('success', 'Recette enregistrée avec succès.');
    }
}
