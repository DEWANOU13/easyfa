<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
class FournisseursExport implements FromView
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
        // dd('A',$this->data);
        return view('page.accueil.fournisseur.document.FournisseurExport',[
            'getfournisseurs' => $this->data,
            'texteEntetePied' => $this->texteEntetePied
        ]);
     

    }
}