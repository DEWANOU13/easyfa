<?php

namespace App\Exports;

use App\Models\Facture;
use Maatwebsite\Excel\Concerns\FromCollection;

class FacturesCsvExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Facture::get(['id', 'Code_type_facture', 'Date_signature', 'Net_a_payer']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code_type_facture',
            'Date_signature',
            'Net_a_payer',
        ];
    }

    public function map($facture): array
    {
        return [
            $facture->id,
            $facture->Code_type_facture,
            $facture->date->format('Y-m-d'),
            number_format($facture->montant, 2, '.', ''),
        ];
    }
}
