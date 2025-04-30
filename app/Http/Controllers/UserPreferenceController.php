<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserPreferenceController extends Controller
{
    //
    public function index()
    {
        $widgets = Widget::all();
        $userWidgets = Auth::user()->widgets->pluck('id')->toArray();
        return response()->json(['widgets' => $widgets, 'userWidgets' => $userWidgets]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $user->widgets()->sync($request->widgets);
        return response()->json(['success' => 'Preferences sauvegardés avec succès']);
    }
}
