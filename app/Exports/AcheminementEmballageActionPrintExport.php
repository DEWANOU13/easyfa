<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AcheminementEmballageActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        // dd($this->data);
        return view('page.acheminement_emballage.acheminer.imprimer.export-action', [
           'acheminement' => $this->data['acheminement'],
           'acheminer' => $this->data['acheminer'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
