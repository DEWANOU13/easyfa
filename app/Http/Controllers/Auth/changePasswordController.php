<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Auth\changePasswordRequest;

class changePasswordController extends Controller
{
    public function changePassword(){
        return view('page.auth.changePassword');
    }

    // Le traitement pour changer le mot de passe
    public function doChangePassword(changePasswordRequest $request)
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Si c'est le cas alors on met a jour et on faire une redirection
        $userU = User::find($user->id);
        $userU->password = Hash::make($request->input('new_password'));
        $userU->save();

        return Redirect::to(url()->previous())->with('success', 'Mot de passe changé vec succès.');
    }
}
