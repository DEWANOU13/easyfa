<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ListeStockEmballageExport implements FromView
{
     protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.emballage.stock_emballage.imprimer.exporter-liste-stock',[
            'data' => $this->data['getStock'],
            'texteEntetePied' => $this->data['texteEntetePied'],
            'reponse' => $this->data['reponse'],
        ]);

    }
}
