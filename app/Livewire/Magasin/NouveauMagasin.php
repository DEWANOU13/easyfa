<?php

namespace App\Livewire\Magasin;

use App\Models\Magasin;
use Livewire\Component;

class NouveauMagasin extends Component
    {
    public $listeAgence, $listeMagasin, $NomMagasin, $agence_id, $Statut_Magasin,
    $UpIdMagasin, $UpNomMagasin, $up_agence_id, $Up_Statut_Magasin;

    public function createMagasin(){
        $this->validate([
            'NomMagasin' => 'required',
            'agence_id' => 'required',
            'Statut_Magasin' => 'required',
        ]);

        $this->dispatch('actionModalCreateMagasin');
    }

    public function editMagasin($id){
        $magasin = Magasin::findOrfail($id);

        $this->UpIdMagasin = $magasin->id;
        $this->UpNomMagasin = $magasin->NomMagasin;
        $this->up_agence_id = $magasin->agence_id;
        $this->Up_Statut_Magasin = $magasin->Statut_Magasin;
    }

    public function validateEditMagasin(){
        $this->validate([
            'UpNomMagasin' => 'required',
            'up_agence_id' => 'required',
            'Up_Statut_Magasin' => 'required',
        ]);

        $this->dispatch('actionModalUpdateMagasin');
    }

    public function render()
    {
        return view('livewire.magasin.nouveau-magasin');
    }
}
