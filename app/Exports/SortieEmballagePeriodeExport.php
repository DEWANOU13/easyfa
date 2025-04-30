<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SortieEmballagePeriodeExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('page.emballage.sortie_emballage.imprimer.imprimerSortiePeriodeExcel', [

           'magasin' => $this->data['magasin'],
           'produit' => $this->data['produit'],
           'debut_periode' => $this->data['debut_periode'],
           'fin_periode' => $this->data['fin_periode'],
           'getSortie' => $this->data['getSortie'],
           'getMagasin' => $this->data['getMagasin'],
           'getSortieProduit' => $this->data['getSortieProduit'],
           'categorie' => $this->data['categorie'],


        ]);

    }
}
