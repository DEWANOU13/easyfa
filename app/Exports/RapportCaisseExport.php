<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RapportCaisseExport implements FromView
{
    protected $data,$texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.statistique.caisse.document.rapport_CaisseExcel', [
            'tableRapportCaisseData' => $this->data['tableRapportCaisseData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoUser' => $this->data['infoUser'],
            'texteEntetePied' => $this->texteEntetePied,
            'infoAgence' => $this->data['infoAgence'],
        ]);

    }
}
