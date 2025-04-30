<?php

namespace App\Livewire\Produit;
use App\Models\Produit;

use Livewire\Component;

class NouveauProduit extends Component
{
    public function render()
    {
        return view('livewire.produit.nouveau-produit');
    }
}
