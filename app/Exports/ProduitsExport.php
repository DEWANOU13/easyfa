<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProduitsExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd('A',$this->data);
        return view('page.produit.produit.export',[
            'getProduits' => $this->data['produits'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);
        // return view('page.produit.entree.imprimer.exporter');

    }
}
