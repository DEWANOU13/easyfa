<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApprovActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement.approvisionner.imprimer.export-action', [
           'approvisionnement' => $this->data['approvisionnement'],
           'approvisionners' => $this->data['approvisionners'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
