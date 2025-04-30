<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReglementActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.reglement.imprimer.export-bordereau-A4', [
           'reglement' => $this->data['reglement'],
           'detail_reglements' => $this->data['detail_reglements'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
