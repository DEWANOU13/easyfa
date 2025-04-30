<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InventaireEmballagePeriodeExport implements FromView
{

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {

        // dd($this->data['texteEntetePied']);
        return view('page.emballage.inventaire_emballage.imprimer.imprimerInventairePeriodeExcel', [

           'imageEntetePied' => $this->data['imageEntetePied'],
           'inventaire' => $this->data['inventaire'],
           'getInventaire' => $this->data['getInventaire'],
           'imageEntetePied' => $this->data['imageEntetePied'],
           'inventaire_magasin_emballage' => $this->data['inventaire_magasin_emballage'],
           'reponse' => $this->data['reponse'],
           'texteEntetePied' => $this->data['texteEntetePied'],


        ]);

    }
}
