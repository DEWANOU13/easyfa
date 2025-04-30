<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CategorieProduit implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd('A',$this->data);
        return view('page.produit.categorie.export',[
            'getCategorieProduits' => $this->data['categorie_produits'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
