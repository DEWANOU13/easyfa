<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReleverSortieExport implements FromView
{
    protected $data;
    protected $texteEntetePied;

    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.statistique.relever.imprimer.relever_sortieExcel', [

            'tableReleverSortieData' => $this->data['tableReleverSortieData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoCategorie' => $this->data['infoCategorie'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
