<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransfertActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.produit.transfert.imprimer.export-action', [
           'transferers' => $this->data['transferers'],
           'transfert_produits' => $this->data['transfert_produits'],
           'data_categorie_transfert' => $this->data['data_categorie_transfert'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
