<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VenteQuantiteExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data['dataEntete']);
        return view('page.statistique.vente.imprimer.exportQ', [

           'dataPrint' => $this->data['dataPrint'],
           'dataEntete' => $this->data['dataEntete'],
           'reponse' => $this->data['reponse'],
           'texteEntetePied' => $this->data['texteEntetePied']

        ]);

    }
}
