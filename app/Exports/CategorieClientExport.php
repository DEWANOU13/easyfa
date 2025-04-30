<?php

namespace App\Exports;

use App\Models\Image;
use App\Models\CategorieClient;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class CategorieClientExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $entetePiedExcel = Image::where('nom', 'entetePiedExcel')->first();

        return view('page.accueil.categorie_client.document.categorie_client_export', [
            'tableCategorieClientData' => $this->data['tableCategorieClientData'],
            'entetePiedExcel' => $entetePiedExcel
        ]);
    }
}
