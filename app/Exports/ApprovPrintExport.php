<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApprovPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement.approvisionner.imprimer.export', [
            'data_agences' => $this->data['data_agences'],
            'data_approvisionners' => $this->data['data_approvisionners'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

        //
    }
}
