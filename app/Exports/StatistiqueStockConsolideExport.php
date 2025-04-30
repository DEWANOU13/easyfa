<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatistiqueStockConsolideExport implements FromView
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

        // dd($this->data['tableStatStockConsolideData'], $this->data['infoMagasin'], $this->data['infoProduit'] );
        return view('page.statistique.stock.document.stat_stock_consolideExcel', [
            'tableStatStockConsolideData' => $this->data['tableStatStockConsolideData'],
            'infoMagasin' => $this->data['infoMagasin'],
            'infoProduit' => $this->data['infoProduit'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'user' => $this->user,
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
