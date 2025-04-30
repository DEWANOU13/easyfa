<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatistiqueStockMagasinExport implements FromView
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

        // dd($this->data['tableStatMagasinData'], $this->data['dateDebut'], $this->data['dateFin']);


        return view('page.statistique.stock.document.stat_stock_magasinExcel', [
            'tableStatMagasinData' => $this->data['tableStatMagasinData'],
            'infoMagasin' => $this->data['infoMagasin'],
            'dateDebut' => $this->data['dateDebut'],
            'dateFin' => $this->data['dateFin'],
            'user' => $this->user,
            'texteEntetePied' => $this->texteEntetePied
        ]);

    }
}
