<?php

namespace App\Exports;

use App\Models\Magasin;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class MagasinsExport implements FromView
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $texteEntetePied;


    public function __construct($data,$texteEntetePied)
    {
        $this->data = $data;
        $this->texteEntetePied = $texteEntetePied;
    }


    public function view(): View
    {
       
        return view('page.accueil.magasins.document.MagasinExport',[
            'getmagasins' => $this->data,
            'texteEntetePied' => $this->texteEntetePied
        ]);
     

    }
    
}
