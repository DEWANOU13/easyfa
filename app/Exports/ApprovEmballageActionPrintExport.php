<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApprovEmballageActionPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // dd($this->data);
        return view('page.approvisionnement_emballage.approvisionner_emballage.imprimer.export-action', [
           'appro_emballages' => $this->data['appro_emballages'],
           'ligne_appro_emballages' => $this->data['ligne_appro_emballages'],
           'texteEntetePied' => $this->data['texteEntetePied'],
        ]);

    }
}
