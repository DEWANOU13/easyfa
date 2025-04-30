<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use App\Models\LoginHistory;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    // public function handle(object $event): void
    // {
    //     //
    // }


    public function handle(Logout $event)
    {
        $user = $event->user;
        LoginHistory::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest()
            ->first()
            ->update(['logout_at' => now()]);
    }
}
