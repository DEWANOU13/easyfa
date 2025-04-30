<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmballageReceptionApprovActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement_emballage.receptionner_emballage.imprimer.export-action', [
           'ligne_receptions' => $this->data['ligne_receptions'],
           'receptions' => $this->data['receptions'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
