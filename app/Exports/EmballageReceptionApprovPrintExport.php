<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmballageReceptionApprovPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement_emballage.receptionner_emballage.imprimer.export', [
           'data_magasins' => $this->data['data_magasins'],
           'data_receptions' => $this->data['data_receptions'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    } 
}
