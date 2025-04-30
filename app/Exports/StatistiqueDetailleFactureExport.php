<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class StatistiqueDetailleFactureExport implements FromView
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
        return view('page.facturation.facture.document.statistique-detaille-facture', [

            'tableStatDetailleData' => $this->data['tableStatDetailleData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoClient' => $this->data['infoClient'],
            'taxe' => $this->data['taxe'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->texteEntetePied,


        ]);

    }
}
