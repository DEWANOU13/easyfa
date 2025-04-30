<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UniteComptageExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd('A',$this->data);
        return view('page.produit.unite_comptage.export',[
            'getUniteComptages' => $this->data['unite_comptages'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
