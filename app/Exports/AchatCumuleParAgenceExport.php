<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AchatCumuleParAgenceExport implements FromView
{
    protected $data,$texteEntetePied;

    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.statistique.achat.exporter.imprimer-achat-cumule-par-agence',[
            'data' => $this->data,
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
