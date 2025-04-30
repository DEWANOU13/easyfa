<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransfertPeriodeExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('page.produit.transfert.imprimer.imprimerTransfertPeriodeExcel', [

           'produit' => $this->data['produit'],
           'magasin_source' => $this->data['magasin_source'],
           'debut_periode' => $this->data['debut_periode'],
           'fin_periode' => $this->data['fin_periode'],
           'magasin_destination' => $this->data['magasin_destination'],
           'transfert_produits' => $this->data['transfert_produits'],
           'getTransferer' => $this->data['getTransferer'],
           'categorie' => $this->data['categorie'],
           'texteEntetePied' => $this->data['texteEntetePied'],


        ]);

    }
}
