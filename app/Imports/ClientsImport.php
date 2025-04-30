<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Client;
use App\Models\CategorieClient;

class ClientsImport implements ToModel
{
    private $headerPassed = false; // Variable pour suivre si l'en-tête a été passé

    public function model(array $row)
    {
        if (!$this->headerPassed) {
            // Ignorer la première ligne (l'en-tête)
            $this->headerPassed = true;
            return null; // Retourner null pour indiquer que cette ligne ne doit pas être traitée
        }

        // Traiter les données normalement
        $categorie = CategorieClient::first();

        // dd($categorie,  $unite);
        // $categorie = CategorieProduit::firstOrCreate(['Libelle' => $row[3], '']);
        // $unite = UniteComptage::firstOrCreate(['Libelle' => $row[4], 'Code' => 'U']);

        return new Client([
            'Denomination_sociale' => $row[0],
            'Adresse_client' => $row[1],
            'Telephone_fixe' => $row[2],
            'Telephone_mobile' => $row[3],
            'Adresse_mail' => $row[4],
            'Pays' => $row[5],
            'Numero_ifu' => $row[6],
            'Code_client' => $row[7],
            'Categorie_client_id' => $categorie->id,
        ]);
    }
}
