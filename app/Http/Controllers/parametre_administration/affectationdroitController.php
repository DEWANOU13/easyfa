<?php

namespace App\Http\Controllers\parametre_administration;

use App\Models\User;
use App\Models\Action;
use App\Models\Agence;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Activity;
use App\Models\ActionUser;
use App\Models\AgenceUser;
use App\Models\GroupeUser;
use App\Models\groupeAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Affectation\affectationUserGroupe;
use App\Http\Requests\Affectation\showGroupeModuleRequest;

class affectationdroitController extends Controller
{
    public function index(showGroupeModuleRequest $request)
    {
        $this->authorize('droit-acces');
        AccessPageGroupeInFunctionAuthUser($request->input('groupe_id1'), $request->input('groupe_id1'), $request->input('module'));
        AccessPageUserInFunctionAuthUser($request->input('user_id'), $request->input('agence_id'), $request->input('module1'));

        $user = Auth::user();

        if (!$request->has('module')) {
            $actions = [];
        } else {
            if ($request->validated('module')) {
                if (in_array($user->id, [1, 3])) {
                    $actions = Action::where('module_id', '=', $request->validated('module'))->get();
                }else{
                    $actions = Action::where('module_id', '=', $request->validated('module'))->where('id', '!=', 20)->get();
                }
            }
        }

        if (!$request->has('module1')) {
            $actions1 = [];
        } else {
            if (in_array($user->id, [1, 3])) {
                $actions1 = Action::where('module_id', $request->input('module1'))->orderBy('id', 'asc')->get();
            }else{
                $actions1 = Action::where('module_id', $request->input('module1'))->where('id', '!=', 20)->orderBy('id', 'asc')->get();
            }
        }

        if (in_array($user->id, [1, 3])) {
            $users = User::where('actif', 1)->get();
            $groupes = Groupe::get();
            $groupeUsers = GroupeUser::get();
        } elseif ($user->id == 2) {
            $users = User::whereNotIn('id', [1, 3])->where('actif', 1)->get();
            $groupes = Groupe::where('id', '<>', 1)->get();
            $groupeUsers = GroupeUser::whereNotIn('id', [1, 3])->get();
        } else {
            $users = User::whereNotIn('id', [1, 2, 3, auth()->user()->id])->where('actif', 1)->get();
            $groupes = Groupe::whereNotIn('id', [1, 2])->get();
            $groupeUsers = GroupeUser::whereNotIn('user_id', [1, 2, 3])->get();
        }

        if ($request->filled('agence_id')) {
            $agencesUser = AgenceUser::where('user_id', $request->input('user_id'))->get();
        } else {
            $agencesUser = AgenceUser::where('user_id', null)->first();
        }

        $activeTab = $request->filled('active_tab') ? $request->input('active_tab') : 'affectation_groupe';

        return view('page.parametre_administration.affectations_droit.affectation_droit', [
            'users' => $users,
            'groupes' => $groupes,
            'groupeUsers' => $groupeUsers,
            'modules' => Module::get(),
            'actions' => $actions,
            'actions1' => $actions1,
            'request' => $request,
            'input' => $request->validated(),
            'agencesUser' => $agencesUser,
            'activeTab' => $activeTab
        ]);
    }

    // Affecter un utilisateur a un groupe
    public function store(affectationUserGroupe $request)
    {
        $this->authorize('droit-acces');

        GroupeUser::create($request->validated());

        $actionsGroupes = GroupeAction::where('groupe_id', $request->validated('groupe_id'))->pluck('action_id')->toArray();

        foreach ($actionsGroupes as $actionGroupe) {
            $agenceUserIds = AgenceUser::where('user_id', $request->validated('user_id'))->pluck('agence_id')->toArray();

            foreach ($agenceUserIds as $agenceUserId) {
                $userAction = ActionUser::where('user_id', $request->validated('user_id'))->where('action_id', $actionGroupe)->where('agence_id', $agenceUserId)->first();

                if (!$userAction) {
                    ActionUser::create([
                        'user_id' => $request->validated('user_id'),
                        'action_id' => $actionGroupe,
                        'agence_id' => $agenceUserId
                    ]);
                }
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a affecté le groupe ' . Groupe::where('id', $request->validated('groupe_id'))->first()->nom_groupe. ' à l\'utilisateur ' . User::where('id', $request->validated('user_id'))->first()->name,
        ]);

        return to_route('groupeUser.index', ['active_tab' => 'groupe_utilisateur'])->with('success', 'Groupe affecté à l\'utlisateur avec succès');
    }

    // Supprimer un groupe a un utilisateur
    public function destroy(Request $request, GroupeUser $groupeUser)
    {
        $this->authorize('droit-acces');

        // On recupere toutes les actions du groupe
        $actionsGroupes = GroupeAction::where('groupe_id', $groupeUser->groupe_id)->pluck('action_id')->toArray();

        foreach ($actionsGroupes as $actionGroupe) {
            $agenceUserIds = AgenceUser::where('user_id', $groupeUser->user_id)->pluck('agence_id')->toArray();

            foreach ($agenceUserIds as $agenceUserId) {
                $userAction = ActionUser::where('user_id', $groupeUser->user_id)->where('action_id', $actionGroupe)->where('agence_id', $agenceUserId)->get()->first();

                if ($userAction) {
                    $groupesUser = GroupeUser::where('user_id', $groupeUser->user_id)->where('groupe_id', '<>', $groupeUser->groupe_id)->pluck('groupe_id')->toArray();

                    // On verifie si d'autres groupe auxquelles appartient l'utilisateur n'a pas cette action
                    if (actionByOtherGroupe($groupesUser, $actionGroupe)) {
                        ActionUser::where('user_id', $groupeUser->user_id)
                            ->where('action_id', $actionGroupe)
                            ->where('agence_id', $agenceUserId)
                            ->delete();
                    }
                }
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a supprimé le groupe ' . $groupeUser->groupe->nom_groupe. ' à l\'utilisateur ' . $groupeUser->user->name,
        ]);

        // On supprime
        $groupeUser->delete();

        return to_route('groupeUser.index', ['active_tab' => 'groupe_utilisateur'])->with('success', 'Ce droit d\'accès a bien été retiré');
    }

    public function affecterDroit(Request $request)
    {
        $this->authorize('droit-acces');
        $groupe_id = $request->input('getGroupe');
        $action_id = $request->input('actionId');

        // On verifie si l'utilisateur a le droit d'affecter ce groupe(N'importe quel utilisateur ne peut pas affecter n'importe quel droit(Super Admin et Admin par exemple))
        if (attributeAccessGroupeInFunctionAuthUser($groupe_id)) {
            $groupeAction = groupeAction::where('groupe_id', $groupe_id)->where('action_id', $action_id)->first();

            // Verifie si ce groupe n'avait pas cet acces
            if ($groupeAction == null) {
                groupeAction::create([
                    'groupe_id' => $groupe_id,
                    'action_id' => $action_id
                ]);

                // On recupere les ids des utilisateurs qui appartiennent a ce groupe
                $user_ids = GroupeUser::where('groupe_id', $groupe_id)->pluck('user_id')->toArray();

                // Affecter a tous les utilisateurs qui ont ce droit cet acces egalement
                foreach ($user_ids as $user_id) {

                    $agenceUserIds = AgenceUser::where('user_id', $user_id)->pluck('agence_id')->toArray();

                    foreach ($agenceUserIds as $agenceUserId) {
                        $userAction = ActionUser::where('user_id', $user_id)->where('agence_id', $agenceUserId)->where('action_id', $action_id)->first();
                        if ($userAction == null) {
                            ActionUser::create([
                                'user_id' => $user_id,
                                'agence_id' => $agenceUserId,
                                'action_id' => $action_id
                            ]);
                        }
                    }
                }
            }

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a affecté le droit \'' . Action::where('id', $action_id)->first()->nom_action. '\' au groupe ' . Groupe::where('id', $groupe_id)->first()->nom_groupe,
            ]);

            $module_id = Action::where('id', $action_id)->first()->module_id;

            return response()->json([
                'view' => view('ajax.selectAllActionGroupe', [
                    'request' => $request,
                    'isAllActionAffectedToGroupe' => isAllActionAffectedToGroupe($module_id, $groupe_id)
                ])->render(),
                'groupeAction' => $groupeAction,
            ]);
        } else {
            return response()->json(['attention' => 'Attention ! Action non autorisée et dangereuse']);
        }
    }

    public function retirerDroit(Request $request)
    {
        $this->authorize('droit-acces');
        $groupe_id = $request->input('getGroupe');
        $action_id = $request->input('actionId');

        if (attributeAccessGroupeInFunctionAuthUser($groupe_id)) {
            $groupeAction = groupeAction::where('groupe_id', $groupe_id)->where('action_id', $action_id);

            // On verifie si le groupe existe ou non et on le supprime
            if ($groupeAction != null) {

                // On supprime les actions des utilisateurs qui appartiennt et qui ne sont pas commun avec d'autres groupes. Et pour ça:

                // On recupere les ids des utilisateurs qui appartiennent a ce groupe
                $user_ids = GroupeUser::where('groupe_id', $groupe_id)->pluck('user_id')->toArray();

                // Supprimer ce droit a tous les utilisateurs qui ne l'ont que par le biais de ce groupe
                foreach ($user_ids as $user_id) {

                    // On recupere l'id des agences auxquels l'utilisateur a accès
                    $agenceUserIds = AgenceUser::where('user_id', $user_id)->pluck('agence_id')->toArray();

                    foreach ($agenceUserIds as $agenceUserId) {
                        $userAction = ActionUser::where('user_id', $user_id)->where('agence_id', $agenceUserId)->where('action_id', $action_id)->first();
                        if ($userAction != null) {

                            // On recupere tous les autres groupes auxquels l'utilisateurs a acces
                            $groupesUser = GroupeUser::where('user_id', $user_id)->where('groupe_id', '<>', $groupe_id)->pluck('groupe_id')->toArray();

                            // On verifie si ces autres groupes auxquels l'utilisatetur appartient n'ont pas cette action
                            if (actionByOtherGroupe($groupesUser, $action_id)) {
                                ActionUser::where('user_id', $user_id)
                                    ->where('action_id', $action_id)
                                    ->where('agence_id', $agenceUserId)
                                    ->delete();
                            }
                        }
                    }
                }

                // Enregistrement de l'activité associée
                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a retiré le droit \'' . Action::where('id', $action_id)->first()->nom_action. '\' au groupe ' . Groupe::where('id', $groupe_id)->first()->nom_groupe,
                ]);

                // On supprime le groupe
                $groupeAction->delete();
            }

            $module_id = Action::where('id', $action_id)->first()->module_id;

            return response()->json([
                'view' => view('ajax.selectAllActionGroupe', [
                    'request' => $request,
                    'isAllActionAffectedToGroupe' => isAllActionAffectedToGroupe($module_id, $groupe_id)
                ])->render(),
            ]);
        } else {
            return response()->json(['attention' => 'Attention ! Action non autorisée et dangereuse']);
        }
    }

    public function affecterDroitUser(Request $request)
    {
        $this->authorize('droit-acces');
        $user_id = $request->input('getUser');
        $action_id = $request->input('actionId');
        $agence_id = $request->input('getAgence');

        // On verifie si l'utilisateur peut executer l'action
        if (attributeAccessUserInFunctionAuthUser($user_id, $agence_id)['success']) {
            $userAction = ActionUser::where('user_id', $user_id)->where('agence_id', $agence_id)->where('action_id', $action_id)->first();

            if ($userAction == null) {
                ActionUser::create([
                    'user_id' => $user_id,
                    'action_id' => $action_id,
                    'agence_id' => $agence_id
                ]);
            }

            $module_id = Action::where('id', $action_id)->first()->module_id;

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a affecté le droit \'' . Action::where('id', $action_id)->first()->nom_action. '\' à l\'utilisateur ' . User::where('id', $user_id)->first()->name,
            ]);

            return response()->json([
                'view' => view('ajax.selectAllActionUser', [
                    'request' => $request,
                    'isAllActionAffectedToUser' => isAllActionAffectedToUser($module_id, $user_id, $agence_id)
                ])->render(),
            ]);
        } else {
            return response()->json(['attention' => attributeAccessUserInFunctionAuthUser($user_id, $agence_id)['message']]);
        }
    }

    public function retirerDroitUser(Request $request)
    {
        $this->authorize('droit-acces');
        $user_id = $request->input('getUser');
        $action_id = $request->input('actionId');
        $agence_id = $request->input('getAgence');

        // On verifie si l'utilisateur peut executer l'action
        if (attributeAccessUserInFunctionAuthUser($user_id, $agence_id)['success']) {

            $userAction = ActionUser::where('user_id', $user_id)->where('action_id', $action_id)->where('agence_id', $agence_id);

            if ($userAction != null) {
                $userAction->forceDelete();
            }

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a retiré le droit \'' . Action::where('id', $action_id)->first()->nom_action. '\' à l\'utilisateur ' . User::where('id', $user_id)->first()->name,
            ]);

            $module_id = Action::where('id', $action_id)->first()->module_id;

            return response()->json([
                'view' => view('ajax.selectAllActionUser', [
                    'request' => $request,
                    'isAllActionAffectedToUser' => isAllActionAffectedToUser($module_id, $user_id, $agence_id)
                ])->render(),
            ]);
        } else {
            return response()->json(['attention' => attributeAccessUserInFunctionAuthUser($user_id, $agence_id)['message']]);
        }
    }

    // Affecter un ensemble d'action a un groupe
    public function affecterToutGroupe(Request $request)
    {
        $groupe_id = $request->input('getGroupe');
        $module_id = $request->input('getModule');

        $actionIdsByGroupe = Action::where('module_id', $module_id)->pluck('id')->toArray();

        foreach ($actionIdsByGroupe as $actionIdByGroupe) {
            $groupeAction = GroupeAction::where('groupe_id', $groupe_id)->where('action_id', $actionIdByGroupe)->first();

            // On verifie si cette affectation n'existait
            if (!$groupeAction) {

                // On affecte cette action au groupe
                GroupeAction::create([
                    'groupe_id' => $groupe_id,
                    'action_id' => $actionIdByGroupe
                ]);

                // On recupère les ids des utilisateurs qui ont ce groupe
                $usersIdsGroupe = GroupeUser::where('groupe_id', $groupe_id)->pluck('user_id')->toArray();

                foreach ($usersIdsGroupe as $userIdGroupe) {
                    $agenceIdsUser = AgenceUser::where('user_id', $userIdGroupe)->pluck('agence_id')->toArray();

                    foreach ($agenceIdsUser as $agenceIdUser) {

                        $userAction = ActionUser::where('user_id', $userIdGroupe)->where('agence_id', $agenceIdUser)->where('action_id', $actionIdByGroupe)->first();
                        if ($userAction == null) {
                            ActionUser::create([
                                'user_id' => $userIdGroupe,
                                'agence_id' => $agenceIdUser,
                                'action_id' => $actionIdByGroupe
                            ]);
                        }
                    }
                }
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a affecté tous les accès du module \'' . Module::where('id', $module_id)->first()->nom_module. '\' au groupe \'' . Groupe::where('id', $groupe_id)->first()->nom_groupe.'\'',
        ]);

        $actions = Action::where('module_id', '=', $module_id)->orderBy('created_at', 'desc')->get();

        return view('ajax.tableActionGroupe', [
            'request' => $request,
            'actions' => $actions,
            'groupe_id' => $groupe_id,
        ]);
    }

    public function retirerToutGroupe(Request $request)
    {
        $groupe_id = $request->input('getGroupe');
        $module_id = $request->input('getModule');


        $actionIdsByGroupe = Action::where('module_id', $module_id)->pluck('id')->toArray();

        foreach ($actionIdsByGroupe as $actionIdByGroupe) {
            $groupeAction = GroupeAction::where('groupe_id', $groupe_id)->where('action_id', $actionIdByGroupe)->first();

            if ($groupeAction) {
                GroupeAction::where('groupe_id', $groupe_id)->where('action_id', $actionIdByGroupe)->delete();
            }

            // On recupère les ids des utilisateurs qui ont ce groupe
            $usersIdsGroupe = GroupeUser::where('groupe_id', $groupe_id)->pluck('user_id')->toArray();

            foreach ($usersIdsGroupe as $userIdGroupe) {
                $agenceIdsUser = AgenceUser::where('user_id', $userIdGroupe)->pluck('agence_id')->toArray();

                foreach ($agenceIdsUser as $agenceIdUser) {

                    $userAction = ActionUser::where('user_id', $userIdGroupe)->where('agence_id', $agenceIdUser)->where('action_id', $actionIdByGroupe)->first();
                    if ($userAction != null) {

                        // On recupere tous les autres groupes auxquels l'utilisateurs a acces
                        $groupesUser = GroupeUser::where('user_id', $userIdGroupe)->where('groupe_id', '<>', $groupe_id)->pluck('groupe_id')->toArray();

                        // On verifie si ces autres groupes auxquels l'utilisatetur appartient n'ont pas cette action
                        if (actionByOtherGroupe($groupesUser, $actionIdByGroupe)) {
                            ActionUser::where([
                                'user_id' => $userIdGroupe,
                                'agence_id' => $agenceIdUser,
                                'action_id' => $actionIdByGroupe
                            ])->delete();
                        }
                    }
                }
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a retiré tous les accès du module \'' . Module::where('id', $module_id)->first()->nom_module. '\' au groupe \'' . Groupe::where('id', $groupe_id)->first()->nom_groupe.'\'',
        ]);

        $actions = Action::where('module_id', '=', $module_id)->orderBy('created_at', 'desc')->get();

        return view('ajax.tableActionGroupe', [
            'request' => $request,
            'actions' => $actions,
            'groupe_id' => $groupe_id,
        ]);
    }

    // Affecter un ensemble d'action d'un module a un utilisateur
    public function affecterToutUser(Request $request)
    {
        $module_id = $request->input('getModule');
        $user_id = $request->input('getUser');
        $agence_id = $request->input('getAgence');

        $actionIdsByGroupe = Action::where('module_id', $module_id)->pluck('id')->toArray();

        foreach ($actionIdsByGroupe as $actionIdByGroupe) {
            $actionUser = ActionUser::where('action_id', $actionIdByGroupe)->where('user_id', $user_id)->where('agence_id', $agence_id)->first();

            if (!$actionUser) {
                ActionUser::create([
                    'action_id' => $actionIdByGroupe,
                    'user_id' => $user_id,
                    'agence_id' => $agence_id,
                ]);
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a affecté tous les accès du module \'' . Module::where('id', $module_id)->first()->nom_module. '\' à l\'utilisateur \'' . User::where('id', $user_id)->first()->name.'\''.' sur l\'agence \''.Agence::where('id', $agence_id)->first()->NomAgence.'\'',
        ]);

        $actions1 = Action::where('module_id', $module_id)->orderBy('created_at', 'desc')->get();

        return view('ajax.tableActionUser', [
            'request' => $request,
            'actions1' => $actions1,
            'user_id' => $user_id,
            'agence_id' => $agence_id
        ]);
    }

    public function retirerToutUser(Request $request)
    {
        $module_id = $request->input('getModule');
        $user_id = $request->input('getUser');
        $agence_id = $request->input('getAgence');

        $actionIdsByGroupe = Action::where('module_id', $module_id)->pluck('id')->toArray();

        foreach ($actionIdsByGroupe as $actionIdByGroupe) {
            $actionUser = ActionUser::where('action_id', $actionIdByGroupe)->where('user_id', $user_id)->where('agence_id', $agence_id)->first();
            if ($actionUser) {
                ActionUser::where('user_id', $user_id)->where('agence_id', $agence_id)->where('action_id', $actionIdByGroupe)->delete();
            }
        }

        // Enregistrement de l'activité associée
        Activity::create([
            'user_id' => auth()->user()->id,
            'heure' => now(),
            'description' => 'a retiré tous les accès du module \'' . Module::where('id', $module_id)->first()->nom_module. '\' à l\'utilisateur \'' . User::where('id', $user_id)->first()->name.'\''.' sur l\'agence \''.Agence::where('id', $agence_id)->first()->NomAgence.'\'',
        ]);

        $actions1 = Action::where('module_id', $module_id)->orderBy('created_at', 'desc')->get();

        return view('ajax.tableActionUser', [
            'request' => $request,
            'actions1' => $actions1,
            'user_id' => $user_id,
            'agence_id' => $agence_id
        ]);
    }

    public function selectAgenceDynamique(Request $request)
    {
        $query = $request->input('query');
        $agencesUser = AgenceUser::where('user_id', $query)->get();

        return view('ajax.agenceUserDynamique', ['agencesUser' => $agencesUser, 'request' => $request]);
    }
}
