<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection,WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Récupérer les données des produits en fonction des paramètres fournis
        return $this->data;
    }

    public function headings(): array
    {
        // Définissez les en-têtes personnalisés ici
        return [
            'Nom client',
            'Adresse client',
            'Téléphone fixe',
            'Téléphone mobile',
            'Adresse email',
            'Pays',
            'IFU client',
            'Code client',
            'Catégorie client',
        ];
    }
}