<?php

namespace App\Http\Controllers\Maintenance;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Maintenance;
use App\Jobs\MaintenanceJob;
use Illuminate\Http\Request;
use App\Jobs\misEnMaintenace;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Maintenace\maitenaceRequest;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {

        if(!in_array(auth()->user()->id, [1, 3])){
            abort('403');
        }

        Auth::user() ? $user_id = Auth::user()->id : $user_id = null;

        if (Auth::user() == null) {
            return redirect()->route('login')->with('message', 'Votre session est expirée.');
        }

        $users = User::whereNotIn('id', [1, 3])->get();
        return view('page.maintenance.maintenance', [
            'users' => $users,
            'maintenance' => Maintenance::first()
        ]);
    }

    public function storeInMaintenace(maitenaceRequest $request)
    {
        $jour =intval($request->validated('jour_maintenance'));
        $heure =intval($request->validated('heure_maintenance'));
        $minute =intval($request->validated('minute_maintenance'));

        $time_in_maintenance = (24 * 60 * $jour) + (60 * $heure) + ($minute);

        $maintenance = Maintenance::get()->first();

        if ($maintenance != null) {
            $maintenance->update([
                'pre_maintenance' => 1,
                'time_in_maintenance' => $time_in_maintenance,
                'updated_at' => now()
            ]);
        } else {
            Maintenance::create([
                'pre_maintenance' => 1,
                'maintenance' => 0,
                'time_in_maintenance' => $time_in_maintenance,
            ]);
        }

        MaintenanceJob::dispatch()->delay(now()->addMinutes($time_in_maintenance));

        return to_route('maintenance')->with(
            'success',
            'Le site sera mis en maintenance et rendu innacessible dans le délai défini'
        );
    }

    public function responseMaintenance()
    {
        $maintenance = Maintenance::first();

        $updatedMaitenance = Maintenance::first()->updated_at;
        $timeInMaitenance = Maintenance::first()->time_in_maintenance;

        $maitenanceTimeStart = Carbon::parse($updatedMaitenance)->addMinutes($timeInMaitenance);
        $maitenanceTimeStartFormated = $maitenanceTimeStart->format('Y-m-d H:i:s');

        return response()->json(['maintenance' => $maintenance, 'maitenanceTimeStartFormated' => $maitenanceTimeStartFormated, 'timeInMaitenance' => $timeInMaitenance]);
    }

    public function bladeMaintenance(){
        return view('components.siteMaintenance');
    }

    public function endMaintenance(){
        $maintenance = Maintenance::first();
        $maintenance->update(['pre_maintenance' => 0, 'maintenance' => 0, 'time_in_maintenance' => null]);
        return to_route('maintenance')->with('success', 'Le site a bien été mis en marche !');
    }
}
