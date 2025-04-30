<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GestionPrixExportHistory implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd('A',$this->data);
        return view('page.produit.gestion_prix.export-history',[
            'getGestionPrixHistory' => $this->data['data_prix_history'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
