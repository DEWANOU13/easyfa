<?php

namespace App\Livewire\CategorieClient;

use Livewire\Component;
use App\Models\CategorieClient;

class NouveauCategorieClient extends Component
{
    public $listeCategorieClient, $Libelle, $UpLibelle, $UpIdCategorieClient;

    public function createCategorieClient(){
        $this->validate([
            'Libelle' => 'required',
        ]);

        $this->dispatch('actionModalcreateCategorieClient');
    }

    public function editCategorieClient($id){
        $CatClient = CategorieClient::findOrfail($id);
    

        $this->UpIdCategorieClient = $CatClient->id;
        $this->UpLibelle = $CatClient->Libelle;
    }

    public function validateEditCategorieClient(){
        $this->validate([
            'UpLibelle' => 'required',
        ]);

        $this->dispatch('actionModalUpdateCategorieClient');
    }

    public function render()
    {
        return view('livewire.categorie-client.nouveau-categorie-client');
    }
}
