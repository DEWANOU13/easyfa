<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceptionAcheminementActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.acheminement.receptionner.imprimer.export-action', [
           'recetionner' => $this->data['recetionner'],
           'reception' => $this->data['reception'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
