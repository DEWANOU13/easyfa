<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FacturePeriodeExport implements FromView
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
        return view('page.facturation.facture.document.facturePeriodeExcel', [

            'tableFactureParPeriodeData' => $this->data['tableFactureParPeriodeData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoClient' => $this->data['infoClient'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }

}
