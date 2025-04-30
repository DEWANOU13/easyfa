<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatistiqueFIcheStockConsolideExport implements FromView
{
    protected $data , $user, $texteEntetePied;


    public function __construct($data, $user, $texteEntetePied)
    {
        $this->data = $data;
        $this->user = $user;
        $this->texteEntetePied = $texteEntetePied;
    }
    public function view(): View
    {
        // dd($this->data['infoMagasin'], $this->data['infoProduit']);
        return view('page.statistique.stock.document.fiche_stock_consolideExcel', [
            'tableFicheStockConsolideData' => $this->data['tableFicheStockConsolideData'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'infoMagasin' => $this->data['infoMagasin'],
            'infoProduit' => $this->data['infoProduit'],
            'user' => $this->user,
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
