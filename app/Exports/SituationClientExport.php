<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SituationClientExport implements FromView
{
    protected $data,$texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        return view('page.emballage.consignation.document.situation_client_Excel', [
            'tableSituationClientData' => $this->data['tableSituationClientData'],

            'infoClient' => $this->data['infoClient'],
            'texteEntetePied' => $this->texteEntetePied,
            'infoAgence' => $this->data['infoAgence'],
        ]);

    }
}
