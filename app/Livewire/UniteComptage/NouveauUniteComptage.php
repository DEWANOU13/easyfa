<?php

namespace App\Livewire\UniteComptage;

use App\Models\UniteComptage;
use Livewire\Component;

class NouveauUniteComptage extends Component
{
    public $listeUniteComptage, $CodeUniteComptage, $NomUniteComptage, $UpIdUniteComptage, $UpNomUniteComptage, $UpCodeUniteComptage;

    public function createUniteComptage(){
        $this->validate([
            'CodeUniteComptage' => 'required',
            'NomUniteComptage' => 'required',
        ]);

        $this->dispatch('actionModalCreateUniteComptage');
    }
    public function editUniteComptage($id){
        $uniteComptage = UniteComptage::findOrfail($id);

        $this->UpIdUniteComptage = $uniteComptage->id;
        $this->UpNomUniteComptage = $uniteComptage->Libelle;
        $this->UpCodeUniteComptage = $uniteComptage->Code;
    }
    public function validateEditUniteComptage(){
        $this->validate([
            'UpCodeUniteComptage' => 'required',
            'UpNomUniteComptage' => 'required',
        ]);

        $this->dispatch('actionModalUpdateUniteComptage');
    }
    public function render()
    {
        return view('livewire.unite-comptage.nouveau-unite-comptage');
    }
}
