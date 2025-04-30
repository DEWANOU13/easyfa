<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ListeStockExportCategorie implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.produit.stock.imprimer.exporter-liste-stock-categorie',[
            'data' => $this->data['getStock'],
            'data_categorie_stock' => $this->data['data_categorie_stock'],
            'texteEntetePied' => $this->data['texteEntetePied'],
            'reponse' => $this->data['reponse'],
        ]);

    }
}
