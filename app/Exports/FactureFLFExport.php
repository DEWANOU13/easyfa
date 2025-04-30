<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FactureFLFExport implements FromArray, WithHeadings
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
            'Produit_designation',
            'Denomination_sociale',
            'Code_lettre',
            'Qte',
            'Prix_net',
            'total_ht',

        ];
    }
}
