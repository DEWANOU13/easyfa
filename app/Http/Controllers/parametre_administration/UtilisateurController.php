<?php

namespace App\Http\Controllers\parametre_administration;

use App\Models\User;
use App\Models\UserWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\User\userFormRequest;
use App\Models\HistoriqueNomUser;
use Carbon\Carbon;

class utilisateurController extends Controller
{
    public function index()
    {
        $this->authorize('consulter-liste-users');

        if (Auth::user()->id == 1) {
            $users = User::orderBy('created_at', 'asc')->get();
        } elseif (Auth::user()->id == 2) {
            $users = User::where('id', '<>', 1)->orderBy('created_at', 'asc')->get();
        } else {
            $users = User::whereNotIn('id', [1, 2])->orderBy('created_at', 'asc')->get();
        }

        return view('page.parametre_administration.utilisateur.user', ['users' => $users]);
    }

    public function create()
    {
        $this->authorize('creer-user');
        return view('page.parametre_administration.utilisateur.form', ['user' => new User]);
    }

    public function store(userFormRequest $request)
    {
        $this->authorize('creer-user');

        $user = New User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->actif = $request->input('actif');

        if($request->input('ForcePassChange') == 'on'){
            $user->force_password_change = 1;
        }else{
            $user->force_password_change = 0;
        }

        $user->save();

        $storeHistoriqueNameUser = New HistoriqueNomUser();
        $storeHistoriqueNameUser->user_id = $user->id;
        $storeHistoriqueNameUser->name = $user->name;
        $storeHistoriqueNameUser->email = $user->email;
        $storeHistoriqueNameUser->created_at = Carbon::now();

        $storeHistoriqueNameUser->save();


        $default_user_widgets =[11,7,10,9,8,4,6];
        $user->widgets()->sync($default_user_widgets);

        return to_route('user.index')->with('success', "L'utilisateur a bien été créé");
    }

    public function edit(User $user)
    {
        $this->authorize('modifier-user');
        return view('page.parametre_administration.utilisateur.form', ['user' => $user]);
    }

    public function update(Request $request, string $id, )
    {
        $this->authorize('modifier-user');
        $data = $request->only(['UpEmail', 'UpName', 'UpActif']);
        $validatorRules = [
            'UpEmail' => 'required',
            'UpName' => 'required',
            'UpActif' => 'required',
        ];

        $validationMessages = [
            'UpEmail.required' => "L'email est requis",
            'UpName.required' => "Le nom est requis",
            'UpActif.required' => "Le statut est requise",
        ];
        $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
        if ($validatorResult->fails()) {
            return redirect()->back()->withErrors($validatorResult)->withInput();
        }

        $name = $data['UpName'];
        $email = $data['UpEmail'];
        $actif = $data['UpActif'];


        $user = User::findorfail($id);
        $user->name = $name;
        $user->email = $email;
        $user->actif = $actif;
        $user->update();



        $updateHistoriqueNonUser = HistoriqueNomUser::where('user_id', $id)
        ->latest()
       // ->skip(1) // Skip the latest record
        ->first();

    if ($updateHistoriqueNonUser) {
        $updateHistoriqueNonUser->Modifier_le = Carbon::now();
        $updateHistoriqueNonUser->update();
    }

        $storeHistoriqueNameUser = New HistoriqueNomUser();
        $storeHistoriqueNameUser->user_id = $user->id;
        $storeHistoriqueNameUser->name = $user->name;
        $storeHistoriqueNameUser->email = $user->email;
        $storeHistoriqueNameUser->created_at = Carbon::now();
        $storeHistoriqueNameUser->save();


        return to_route('user.index')->with('success', 'Le groupe a bien été modifié');
    }

    /* Recherche automatique */
    public function searchUser(Request $request){
        $query = $request->input('query');
        $users = User::where('email', 'like', '%'.$query.'%')->get();

        return view('ajax.user', ['users' => $users]);
    }
}
