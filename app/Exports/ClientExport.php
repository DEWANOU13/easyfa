<?php

namespace App\Exports;

use App\Models\Image;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class ClientExport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $entetePiedExcel = Image::where('nom', 'entetePiedExcel')->first();

        return view('page.accueil.client.document.client_export', [
            'tableClientData' => $this->data['tableClientData'],
            'entetePiedExcel' => $entetePiedExcel
        ]);
    }
}
