<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class InventairePeriodeExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd( $this->data);
        return view('page.produit.inventaire.imprimer.imprimerInventairePeriodeExcel', [

           'imageEntetePied' => $this->data['imageEntetePied'],
           'inventaire' => $this->data['inventaire'],
           'getInventaire' => $this->data['getInventaire'],
           'imageEntetePied' => $this->data['imageEntetePied'],
           'inventaire_magasin' => $this->data['inventaire_magasin'],
           'reponse' => $this->data['reponse'],
           'texteEntetePied' => $this->data['texteEntetePied'],


        ]);

    }
}
