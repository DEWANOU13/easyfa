<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');
            $timeout = 3600; // En secondes donc 1H 
            $timeoutH = $timeout / 3600; //  1H 

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with([
                    'messageInactivite' => 'Vous avez été déconnecté pour cause d\'inactivité pendant ' . $timeoutH . ' heure.',
                ]);
            }

            session(['last_activity_time' => time()]);
        }
        return $next($request);
    }
}
