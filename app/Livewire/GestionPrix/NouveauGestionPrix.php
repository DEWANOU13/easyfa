<?php

namespace App\Livewire\GestionPrix;

use Livewire\Component;

class NouveauGestionPrix extends Component
{
    public $produits, $historique_prix_revients, $historique_prix_ventes, $categorie_client, $agence;

    public function createCategorie(){
        $this->validate([
            'NomCategorie' => 'required',
        ]);

        $this->dispatch('actionModalCreateCategorie');
    }

    public function render()
    {
        return view('livewire.gestion-prix.nouveau-gestion-prix');
    }
}
