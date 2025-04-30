<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceptionAchemEmballagePeriodeExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.acheminement_emballage.receptionner.imprimer.imprimerTransfertPeriodeExcel', [
            'debut_periode' => $this->data['debut_periode'],
            'fin_periode' => $this->data['fin_periode'],
            'getAcheminements' => $this->data['getAcheminements'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
