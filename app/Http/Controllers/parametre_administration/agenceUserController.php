<?php

namespace App\Http\Controllers\parametre_administration;

use App\Models\User;
use App\Models\Agence;
use App\Models\Activity;
use App\Models\ActionUser;
use App\Models\AgenceUser;
use App\Models\GroupeUser;
use App\Models\groupeAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Affectation\affectationUserAgence;

class agenceUserController extends Controller
{
    public function index()
    {
        $this->authorize('attribuer-agence');

        if (Auth::user()->id == 1 || Auth::user()->id == 3) {
            $agenceUser = AgenceUser::orderBy('created_at', 'desc')->get();
            $users = User::get();
        } elseif (Auth::user()->id == 2) {
            $agenceUser = AgenceUser::whereNotIn('user_id', [1, 3])->orderBy('created_at', 'desc')->get();
            $users = User::whereNotIn('id', [1, 3])->get();
        } else {
            $agenceUser = AgenceUser::whereNotIn('user_id', [1, 2, 3])->orderBy('created_at', 'desc')->get();
            $users = User::whereNotIn('id', [1, 2, 3, auth()->user()->id])->get();
        }

        return view('page.parametre_administration.affectations_agence.affectation_agence', [
            'agenceUsers' => $agenceUser,
            'users' => $users,
            'agences' => Agence::get(),
        ]);
    }

    public function affecterAgence(affectationUserAgence $request)
    {
        $this->authorize('attribuer-agence');

        AgenceUser::create($request->validated());

        $groupesUser = GroupeUser::where('user_id', $request->input('user_id'))->pluck('groupe_id')->toArray();

        foreach($groupesUser as $groupeUser){

            $actions = groupeAction::where('groupe_id', $groupeUser)->pluck('action_id')->toArray();

            foreach ($actions as $action_id) {

                $userAction = ActionUser::where('user_id', $request->input('user_id'))->where('agence_id', $request->input('agence_id'))->where('action_id', $action_id)->first();

                if ($userAction ==  null) {
                    ActionUser::create([
                        'user_id' => $request->input('user_id'),
                        'action_id' => $action_id,
                        'agence_id' => $request->input('agence_id')
                    ]);
                }
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a affecté l\'agence ' . Agence::where('id', $request->input('agence_id'))->first()->NomAgence. ' à l\'utilisateur ' . User::where('id', $request->input('user_id'))->first()->name,
        ]);

        return to_route('agenceUtilisateur.index')->with('success', 'Agence affecté à l\'utlisateur avec succès');
    }

    public function retirerAgence(AgenceUser $agenceUser)
    {
        $this->authorize('attribuer-agence');
        $userName = $agenceUser->user->name;

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a supprimé l\'agence ' . $agenceUser->agence->NomAgence. ' à l\'utilisateur ' . $agenceUser->user->name,
        ]);

        $agenceUser->delete();

        return to_route('agenceUtilisateur.index')->with('success', 'Cette agence a bien été retiré a l\'utilisateur ' . $userName);
    }
}
