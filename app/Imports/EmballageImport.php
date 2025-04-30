<?php

namespace App\Imports;

use App\Models\CategorieEmballage;
use App\Models\Emballage;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmballageImport implements ToCollection, WithHeadingRow
{
    protected $importEmballage;

    public function __construct()
    {
        // Initialiser l'objet ImportStock
        $this->importEmballage = new Emballage();
    }

    public function collection(Collection $rows)
    {
        // dd($rows);
        // dd('bien dans l\'importation');
        foreach ($rows as $row) {

            // dd($row);
            $emaballage = Emballage::where('Reference', $row['reference'])->first();

            if (!$emaballage) {
                // dd('parfait!');
                // Créer l'entrée de produit
                $this->createEmballage($row);
            }else{
                throw new Exception('Emballage '. $emaballage->Reference.' => '. $emaballage->Nom_emballage .' existe déjà.');
            }
        }

    }

    protected function createEmballage($row)
    {

        $categorie_emballage_existe = CategorieEmballage::where('Libelle', $row['categorie_emballage'])->first();

        if(!$categorie_emballage_existe){
            $categorie_emballage = new CategorieEmballage();
            $categorie_emballage->Libelle = $row['categorie_emballage'];
            $categorie_emballage->save();

            $emballage = new Emballage();
            $emballage->Reference = $row['reference'];
            $emballage->Nom_emballage = $row['nom_emballage'];
            $emballage->Categorie_emballage_id = $categorie_emballage->id;
            $emballage->Enregistrer_par = auth()->user()->id;
            $emballage->save();

        }else{

            $emballage = new Emballage();
            $emballage->Reference = $row['reference'];
            $emballage->Nom_emballage = $row['nom_emballage'];
            $emballage->Categorie_emballage_id = $categorie_emballage_existe->id;
            $emballage->Enregistrer_par = auth()->user()->id;
            $emballage->save();
        }



    }


}
