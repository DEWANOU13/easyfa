<?php

namespace App\Http\Controllers\parametre_administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Activity;
use App\Models\HistoriqueNomUser;
use App\Models\ProduitHistorique;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\UserNotification;


class SurveillanceController extends Controller
{
    //

    public function index()
    {
        return view('admin.index');
    }

    public function getOnlineUsers()
    {
        // Exemple de récupération d'utilisateurs en ligne
        $users = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return response()->json($users);
    }
    public function getLoginHistory()
    {

        $loginHistory = DB::table('login_histories')
            ->join('users', 'login_histories.user_id', '=', 'users.id')
            ->select('login_histories.user_id', 'login_histories.login_at', 'login_histories.logout_at', 'users.name', 'users.email')
            ->orderBy('login_at', 'desc')
            ->get();
        return response()->json($loginHistory);
    }

    public function getActivity()
    {
        $activity = DB::table('activities')
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->select('activities.user_id', 'users.name', 'activities.description', 'activities.heure')
            ->orderBy('activities.heure', 'desc')
            ->get();
        return response()->json($activity);
    }

    public function disconnectUser($id)
    {
        DB::table('sessions')->where('user_id', $id)->delete();
        LoginHistory::where('user_id', $id)
        ->whereNull('logout_at')
        ->latest()
        ->first()
        ->update(['logout_at' => now()]);
        return response()->json(['message' => 'Utilisateur déconnecté avec succès.']);
    }

    public function forcePasswordChange($id)
    {
        $user = User::findOrFail($id);
        $user->force_password_change = 1;
        $user->update();

        return response()->json(['message' => 'Changement de mot de passe forcé avec succès.']);
    }

    public function getHistoriqueNomUser(){
        $liste = HistoriqueNomUser::orderby('updated_at', 'desc')->get();
        return response()->json($liste);

    }

    public function getHistoriqueProduit(){
        // $liste = ProduitHistorique::all();
        $liste = DB::table('produit_historiques')
        ->join('users', 'produit_historiques.Enregistrer_par', '=', 'users.id')
        ->select('produit_historiques.*', 'users.name')
        ->get();
        return response()->json($liste);

    }

}
