<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceptionApprovActionPrintExport  implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement.receptionner.imprimer.export-action', [
           'receptions' => $this->data['receptions'],
           'ligne_receptions' => $this->data['ligne_receptions'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    } //

}
