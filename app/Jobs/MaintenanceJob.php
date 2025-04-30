<?php

namespace App\Jobs;

use App\Http\Controllers\Maintenance\MaintenanceController;
use App\Models\Maintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MaintenanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    public function handle(): void
    {
        $maintenance = Maintenance::first();

        if ($maintenance->pre_maintenance == 1) {
            $maintenance->update(['maintenance' => 1, 'pre_maintenance' => 0, 'time_in_maintenance' => null]);
            redirect()->action([MaintenanceController::class, 'bladeMaintenance']);
        }
    }
}
