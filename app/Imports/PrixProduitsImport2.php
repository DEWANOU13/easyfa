<?php

namespace App\Imports;

use Exception;
use App\Models\Agence;
use App\Models\Produit;
use App\Models\CategorieClient;
use Illuminate\Support\Collection;
use App\Models\HistoriquePrixProduit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;


class PrixProduitsImport2 implements ToArray, WithHeadingRow
{

   /*  public function collection(Collection $rows)
    {
        $headers = $rows->first()->keys();

        // Afficher les colonnes exactement comme dans le fichier Excel
        dd($headers);

        $errors = [];
        foreach ($rows as $row) {
            // Récupérer les références (produit, agence, etc.)
            $produit = Produit::where('Designation', $row['produit'] ?? null)->first();
            if (!$produit) {
                throw new Exception("Produit introuvable: " . ($row['produit'] ?? 'Non spécifiée'));
            }

            $agence = Agence::where('NomAgence', $row['agence'] ?? null)->first();
            if (!$agence) {
                throw new Exception("Agence introuvable: " . ($row['agence'] ?? 'Non spécifiée'));
            }

            // Colonnes fixes
            $fixedData = [
                'produit_id' => $produit->id ?? null,
                'agence_id' => $agence->id ?? null,
                'date_changement_prix' => now(),
                'user_id' => auth()->id(),
                'Modifier_par' => auth()->user()->name ?? null,
            ];

            // Colonnes dynamiques : PRIX NORMAL, CATÉGORIE 1, etc.

            // Colonnes dynamiques : Toutes les catégories et prix
            // Colonnes dynamiques (à partir de la 3e colonne)
            // Colonnes dynamiques (à partir de la 4e colonne)
            foreach ($row as $key => $value) {
                if (in_array($key, ['Produit', 'Catégorie produit', 'Agence'])) {
                    continue; // Ignorer les colonnes fixes
                }

                // Vérifier si la catégorie existe dans la base de données
                $categorie = CategorieClient::where('Libelle', $key)->first();
                dd($key);
                if (!$categorie) {
                    $errors[] = "Catégorie client introuvable : " . $key;
                    continue;
                }

                // Enregistrer les données
                HistoriquePrixProduit::create(array_merge($fixedData, [
                    'categorie_client_id' => $categorie->id,
                    'prix' => $value,
                ]));
            }
        }
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode('<br>', $errors));
        }

        // Succès : Si aucune erreur
        session()->flash('success', 'Tous les prix ont été importés avec succès.');
        return redirect()->back();
    } */

    public function array(array $array)
    {
        return $array;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
