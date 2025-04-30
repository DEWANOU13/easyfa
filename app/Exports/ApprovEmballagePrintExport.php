<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApprovEmballagePrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement_emballage.approvisionner_emballage.imprimer.export', [
           'data_approvisionners' => $this->data['data_approvisionners'],
           'data_agences' => $this->data['data_agences'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
