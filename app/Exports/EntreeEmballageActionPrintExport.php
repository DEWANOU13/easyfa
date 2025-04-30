<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EntreeEmballageActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.emballage.entree_emballage.imprimer.exporter-action',[
            'imageEntetePied' => $this->data['imageEntetePied'],
            'texteEntetePied' => $this->data['texteEntetePied'],
            'entre_ligne_emballages' => $this->data['entre_ligne_emballages'],
            'entree_emballages' => $this->data['entree_emballages'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
