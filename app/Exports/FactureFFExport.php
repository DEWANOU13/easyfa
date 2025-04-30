<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FactureFFExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Date_facture',
            'Reference_facture',
            'Code_signature',
            'Denomination_sociale',
            'Adresse_client',
            'Numero_ifu',
            'Pays_livraison',
            'Date_livraison',
            'MontantHT',
            'MontantTTC',
        ];
    }
}
