<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CaisseExport implements FromView
{
    protected $infoCaisse,$detailCaisse, $texteEntetePied;


    public function __construct($infoCaisse,$detailCaisse, $texteEntetePied)
    {
        $this->infoCaisse = $infoCaisse;
        $this->detailCaisse = $detailCaisse;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.caisse.document.caisseExcel', [
            'infoCaisse' => $this->infoCaisse,
            'detailCaisse' => $this->detailCaisse,
            'texteEntetePied' => $this->texteEntetePied,
        ]);

    }
}
