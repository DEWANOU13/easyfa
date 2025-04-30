<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AchatCumuleParCategorieExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.statistique.achat.exporter.imprimer-achat-cumule-par-categorie',['data' => $this->data]);

    }
}
