<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AchatCumuleParMoisExport implements FromView
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
        return view('page.statistique.achat.exporter.imprimer-achat-cumule-par-mois',
        ['data' => $this->data,
        'texteEntetePied' => $this->texteEntetePied]);

    }
}
