<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class AcheminementEmballagePeriodeExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('page.acheminement_emballage.acheminer.imprimer.imprimerTransfertPeriodeExcel', [

           'debut_periode' => $this->data['debut_periode'],
           'fin_periode' => $this->data['fin_periode'],
           'getAcheminements' => $this->data['getAcheminements'],
           'infoAgence' => $this->data['infoAgence'],
           'texteEntetePied' => $this->data['texteEntetePied'],


        ]);

    }
}
