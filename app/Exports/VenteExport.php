<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VenteExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.statistique.vente.imprimer.export', [

           'dataPrint' => $this->data['dataPrint'],
           'reponse' => $this->data['reponse'],
           'texteEntetePied' => $this->data['texteEntetePied']

        ]);

    }
}
