<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Agence;
use App\Models\AgenceUser;
use Illuminate\Support\Facades\DB;
use App\Notifications\IdentityVerificationNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function login()
    {
        return view('page.auth.login');
    }

    public function passwordCharge(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ], [
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->force_password_change = 0;
        $user->save();
        return redirect()->route('verif-access');
    }


    public function choixAgence()
    {
        $agenceIds = AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
        $agences = Agence::whereIn('id', $agenceIds)->pluck('NomAgence', 'id');

        return view('page.auth.choixAgence', ['agences' => $agences]);
    }

    public function switchAgence(Request $request)
    {
        $request->validate([
            'agence' => 'required|exists:agences,id'
        ]);

        $request->session()->put('site_id', $request->input('agence'));

        return redirect()->back()->with('status', 'Agence changée avec succès');
    }

    public function doLoginChoixAgence(Request $request)
    {
        $request->validate([
            'agence' => 'required'
        ]);

        $request->session()->put('site_id', $request->input('agence'));

        return redirect()->intended(route('home'));
    }

    // La déconnexion
    public function deconnexion()
    {
        session()->flush();
        Auth::logout();
        return to_route('login');
    }

    public function identityTriggerVerification()
    {
        $user = Auth::user();
        if ($user) {

            $token = $this->generateRandomCode(6);
            $verification_token = Str::random(64);
            $user->verification_token = $token;
            session()->put('verification_token', $verification_token);

            $user->save();
            $subjet = $token . ' est votre code de vérification de d\'identité';
            $view = 'emails.verify_identity_mail_form';
            $route = 'identity.verify.notice';

            $user->notify(new IdentityVerificationNotification($token, $subjet, $view));


            return redirect()->route($route, ['token' => $verification_token])
                ->with('status', 'Saisissez le code envoyé à votre adresse email.');
        }

        return redirect()->route('login');
    }

    public function emailTriggerVerification()
    {
        $user = Auth::user();
        if ($user) {

            $token = $this->generateRandomCode(6);
            $verification_token = Str::random(64);
            session()->put('verification_token', $verification_token);
            $user->verification_token = $token;
            $user->save();
            $subjet = $token . ' est votre code de vérification de l\'adresse email';
            $view = 'emails.verify_email_mail_form';
            $route = 'email.verify.notice';

            $user->notify(new IdentityVerificationNotification($token, $subjet, $view));

            return redirect()->route($route, ['token' => $verification_token])
                ->with('status', 'Saisissez le code envoyé à votre adresse email.');
        }

        return redirect()->route('login');
    }

    public function identityShow($token)
    {
        $user = Auth::user();
        if (!$user || session()->get('verification_token') !== $token) {
            abort(404, 'Token invalide ou expiré.');
        }
        return view('auth.verify-identity', ['token' => $token]);
    }

    public function emailShow($token)
    {
        $user = Auth::user();
        if (!$user || session()->get('verification_token') !== $token) {
            // Afficher la page 404
            abort(404, 'Token invalide ou expiré.');
        }

        return view('auth.email-verify', ['token' => $token]);
    }

    private function generateRandomCode($length = 10)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    public function identityVerify(Request $request)
    {
        $user = Auth::user();

        $inputToken = $request->input('verification_token');
        $inputCode = $request->input('code');
        // dd(' $inputToken',  $inputToken);
        if (!$user || session()->get('verification_token') !== $inputToken) {
            return redirect()->route('login');
        }


        // Initialiser le compteur de tentatives dans la session
        if (!$request->session()->has('verification_attempts')) {
            $request->session()->put('verification_attempts', 0);
        }

        $attempts = $request->session()->get('verification_attempts');

        if ($attempts >= 2) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with('message', 'Vous avez dépassé le nombre de tentatives autorisées. Veuillez vous reconnecter.');
        }

        if ($user->verification_token === $inputCode) {

            $user->verification_token = null;
            $user->save();

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '<>', session()->getId())
                ->delete();

            $request->session()->forget('verification_attempts');

            return redirect()->route('identity.verify.notice', ['token' => $inputToken])->with([
                'statusVerify' => 'Identité confirmée avec succès.'
            ]);
        } else {
            $request->session()->increment('verification_attempts');
            $chancesRestantes = 3 - session()->get('verification_attempts');
            return redirect()->route('identity.verify.notice', ['token' => $inputToken])->with([
                'noVerify' => 'Code erroné. Vous avez encore ' . $chancesRestantes . ' chances.',
            ]);
        }
    }

    public function emailVerify(Request $request)
    {

        $user = Auth::user();

        $inputToken = $request->input('verification_token');
        $inputCode = $request->input('code');
        if (!$user || session()->get('verification_token') !== $inputToken) {
            return redirect()->route('login');
        }

        // Initialiser le compteur de tentatives dans la session
        if (!$request->session()->has('verification_attempts')) {
            $request->session()->put('verification_attempts', 0);
        }

        $attempts = $request->session()->get('verification_attempts');

        if ($attempts >= 2) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->with('message', 'Vous avez dépassé le nombre de tentatives autorisées. Veuillez vous reconnecter.');
        }

        if ($user->verification_token === $inputCode) {
            $user->identity_verified = true;
            $user->email_verified_at = now();
            $user->verification_token = null;
            $user->save();
            $request->session()->forget('verification_attempts');
            return redirect()->route('email.verify.notice', ['token' => $inputToken])->with([
                'statusEmail' => 'Adresse email confirmée avec succès.',
            ]);
        } else {
            $request->session()->increment('verification_attempts');
            $chancesRestantes = 3 - session()->get('verification_attempts');
            return redirect()->route('email.verify.notice', ['token' => $inputToken])->with([
                'noVerify' => 'Code erroné. Vous avez encore ' . $chancesRestantes . ' chances.',
            ]);
        }
    }
}
