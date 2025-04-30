<?php

namespace App\Exports;
use App\Models\Image;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClientCompteExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $entetePiedExcel = Image::where('nom', 'entetePiedExcel')->first();

        return view('page.accueil.client.document.categorie_client_compte_export', [
            'tableClientCompteData' => $this->data['tableClientCompteData'],
            'entetePiedExcel' => $entetePiedExcel
        ]);
    }
}
