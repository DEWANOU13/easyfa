<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MargeParClientExport implements FromView
{
    protected $data,$texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.statistique.marge.document.marge_par_clientExcel', [
            'tableMargeParClientData' => $this->data['tableMargeParClientData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoClient' => $this->data['infoClient'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }

}
