<?php

namespace App\Livewire\Agence;

use App\Models\Agence;
use Livewire\Component;

class NouveauAgence extends Component
{
    public $listeAgences, $NomAgence, $EnActivite, $UpIdAgence, $UpNomAgence, $Up_EnActivite, $titre_signataire_facture, $nom_signataire,$adresseAgence, $numero_telephone_1, $numero_telephone_2;

    public function createAgence(){
        $this->validate([
            'NomAgence' => 'required',
            'EnActivite' => 'required',
            'adresseAgence' => 'required',
            'numero_telephone_1' => 'required',
            'numero_telephone_2' => 'nullable',
        ]);

        $this->dispatch('actionModalcreateAgence');
    }

    public function editAgence($id){

        $agence = Agence::findOrfail($id);

        $this->UpIdAgence = $agence->id;
        $this->UpNomAgence = $agence->NomAgence;
        $this->Up_EnActivite = $agence->EnActivite;
        $this->titre_signataire_facture = $agence->titre_signataire_facture;
        $this->nom_signataire = $agence->nom_signataire;
        $this->adresseAgence = $agence->adresseAgence;
        $this->numero_telephone_1 = $agence->numero_telephone_1;
        $this->numero_telephone_2 = $agence->numero_telephone_2;
    }

    public function validateEditAgence(){
        $this->validate([
            'UpNomAgence' => 'required',
            'Up_EnActivite' => 'required',
            'titre_signataire_facture' => 'nullable',
            'nom_signataire' => 'nullable',
            'adresseAgence' => 'required',
            'numero_telephone_1' => 'required',
            'numero_telephone_2' => 'nullable',
        ]);

        $this->dispatch('actionModalUpdateAgence');
    }

    public function render()
    {
        return view('livewire.agence.nouveau-agence');
    }
}
