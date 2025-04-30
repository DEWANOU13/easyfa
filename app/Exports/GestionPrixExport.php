<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GestionPrixExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd('A',$this->data);
        return view('page.produit.gestion_prix.export',[
            'getGestionPrix' => $this->data['data_prix'],
            'texteEntetePied' => $this->data['texteEntetePied'],
            'reponse' => $this->data['reponse'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
