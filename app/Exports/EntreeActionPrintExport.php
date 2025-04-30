<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EntreeActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.produit.entree.imprimer.exporter-action',[
            'imageEntetePied' => $this->data['imageEntetePied'],
            'texteEntetePied' => $this->data['texteEntetePied'],
            'entrer_produits' => $this->data['entrer_produits'],
            'entree_produits' => $this->data['entree_produits'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
