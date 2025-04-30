<?php

namespace App\Livewire\CategorieProduit;

use App\Models\CategorieProduit;
use Livewire\Component;

class NouveauCategorieProduit extends Component
{

    public $listeCategorieProduit, $NomCategorie, $UpIdCategorie, $UpNomCategorie, $updateCategorie;

    public function createCategorie(){
        $this->validate([
            'NomCategorie' => 'required',
        ]);

        $this->dispatch('actionModalCreateCategorie');
    }
    public function editCategorie($id){
        $categorieProduit = CategorieProduit::findOrfail($id);

        $this->UpIdCategorie = $categorieProduit->id;
        $this->UpNomCategorie = $categorieProduit->Libelle;
    }
    public function validateEditCategorie(){
        $this->validate([
            'UpNomCategorie' => 'required',
        ]);

        $this->dispatch('actionModalUpdateCategorie');
    }

    public function render()
    {
        return view('livewire.categorie-produit.nouveau-categorie-produit');
    }
}
