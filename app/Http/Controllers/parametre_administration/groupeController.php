<?php

namespace App\Http\Controllers\parametre_administration;

use App\Models\Groupe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Groupe\groupeFormRequest;

class groupeController extends Controller
{
    public function index(){

        $this->authorize('consulter-liste-groupe');

        if (Auth::user()->id == 1) {
            $groupes = Groupe::orderBy('created_at', 'asc')->get();
        } elseif (Auth::user()->id == 2) {
            $groupes = Groupe::where('id', '<>', 1)->orderBy('created_at', 'asc')->get();
        } else {
            $groupes = Groupe::whereNotIn('id', [1, 2])->orderBy('created_at', 'asc')->get();
        }

        return view('page.parametre_administration.groupe.groupe', ['groupes' => $groupes]);
    }

    public function create(){
        $this->authorize('creer-groupe');
        return view('page.parametre_administration.groupe.form', ['groupe' => new Groupe]);
    }

    public function store(groupeFormRequest $request){
        $this->authorize('creer-groupe');
        Groupe::create($request->validated());
        return to_route('groupe.index')->with('success', 'Le groupe a bien été créé');
    }

    public function edit(Groupe $groupe){
        $this->authorize('modifier-groupe');
        return view('page.parametre_administration.groupe.form', ['groupe' => $groupe]);
    }

    public function update(groupeFormRequest $request, Groupe $groupe){
        $this->authorize('modifier-groupe');
        $groupe->update($request->validated());
        return to_route('groupe.index')->with('success', 'Le groupe a bien été modifié');
    }

    /* Recherche automatique */
    public function searchGroupe(Request $request){
        $query = $request->input('query');
        $groupes = Groupe::where('nom_groupe', 'like', '%'.$query.'%')->get();

        return view('ajax.groupe', ['groupes' => $groupes]);
    }
}
