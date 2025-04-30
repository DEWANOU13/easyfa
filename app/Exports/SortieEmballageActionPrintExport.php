<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SortieEmballageActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.emballage.sortie_emballage.imprimer.export-action', [
           'sortir_produits' => $this->data['sortir_produits'],
           'sortie_produits' => $this->data['sortie_produits'],
            'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
