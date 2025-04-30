<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RapportVenteExport implements FromView
{
    protected $data,$texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.statistique.rapport.document.rapport_venteExcel', [
            'tableMargeParProduitData' => $this->data['tableMargeParProduitData'],

            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoProduit' => $this->data['infoProduit'],
            'texteEntetePied' => $this->texteEntetePied,
            'infoAgence' => $this->data['infoAgence'],
        ]);

    }
}
