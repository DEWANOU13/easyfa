<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Produit;
use App\Models\CategorieProduit;
use App\Models\UniteComptage;
use Maatwebsite\Excel\Concerns\ToArray;

class ProduitsImport implements ToArray
{

    public function array(array $array)
    {
        return $array;
    }

    // private $headerPassed = false; // Variable pour suivre si l'en-tête a été passé

    // public function model(array $row)
    // {
    //     if (!$this->headerPassed) {
    //         // Ignorer la première ligne (l'en-tête)
    //         $this->headerPassed = true;
    //         return null; // Retourner null pour indiquer que cette ligne ne doit pas être traitée
    //     }

    //     // Traiter les données normalement
    //     $categorie = CategorieProduit::first();
    //     $unite = UniteComptage::first();

    //     // dd($categorie,  $unite);
    //     // $categorie = CategorieProduit::firstOrCreate(['Libelle' => $row[3], '']);
    //     // $unite = UniteComptage::firstOrCreate(['Libelle' => $row[4], 'Code' => 'U']);

    //     return new Produit([
    //         'Type' => $row[0],
    //         'Reference' => $row[1],
    //         'Designation' => $row[2],
    //         'Id_Categorie' => $categorie->id,
    //         'Id_Unite_Comptage' => $unite->id,
    //         'Statut' => 'ACTIF',
    //     ]);
    // }

}
