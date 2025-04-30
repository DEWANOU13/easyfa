<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatistiqueGlobalFactureExport implements FromView
{ protected $data;
    protected $texteEntetePied;


    public function __construct($data, $texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }

    public function view(): View
    {
        return view('page.facturation.facture.document.statistique-global-facture', [
            'totauxParCategorie' => $this->data['totauxParCategorie'],
            'listeFacture' => $this->data['listeFacture'],
            'totalParTypeFacture' => $this->data['totalParTypeFacture'],
            'tableStatGlobalData' => $this->data['tableStatGlobalData'],
            'diffTableData' => $this->data['diffTableData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoClient' => $this->data['infoClient'],
            'client' => $this->data['client'],
            'infoAgence' => $this->data['infoAgence'],
            'texteEntetePied' => $this->texteEntetePied

        ]);

    }
}
