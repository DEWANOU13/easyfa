<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\FromCollection;

class HistoriqueStockExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.produit.stock.imprimer.exporter-historique-stock',[
            'data' => $this->data['all_data'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
