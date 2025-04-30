<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReglementPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.reglement.imprimer.export', [
            'texteEntetePied' => $this->data['texteEntetePied'],
           'reglements' => $this->data['reglements'],
           'detail_reglements' => $this->data['detail_reglements'],
           'm_r' => $this->data['m_r'],
           'clt' => $this->data['clt'],
           'debut_periode' => $this->data['debut_periode'],
           'fin_periode' => $this->data['fin_periode'],
           'statut' => $this->data['statut'],
        ]);

    }
}
