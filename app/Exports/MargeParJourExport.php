<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MargeParJourExport implements FromView
{
    protected $data,$texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.statistique.marge.document.marge_par_jourExcel', [
            'tableMargeParJourData' => $this->data['tableMargeParJourData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoAgence' => $this->data['infoAgence'],

            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
