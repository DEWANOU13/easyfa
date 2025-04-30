<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\FromCollection;

class EntreeEmballageListeExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        // dd($this->data);
        return view('page.emballage.entree_emballage.imprimer.exporter',[

            'imageEntetePied' => $this->data['imageEntetePied'],
             'texteEntetePied' => $this->data['texteEntetePied'],
                'debut_periode' => $this->data['debut_periode'],
                'fin_periode' => $this->data['fin_periode'],
                'fournisseur' => $this->data['fournisseur'],
                'produit' => $this->data['produit'],
                'magasin' => $this->data['magasin'],
                'categorie' => $this->data['categorie'],
                'getEntree' => $this->data['getEntree'],
                'getFournisseur' => $this->data['getFournisseur'],
                'getMagasin' => $this->data['getMagasin'],
                'getFournisseurEntreeProduits' => $this->data['getFournisseurEntreeProduits']
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
