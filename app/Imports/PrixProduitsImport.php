<?php
// 'prix' => $row[4],
// $k = 1;
namespace App\Imports;

use App\Models\Produit;
use App\Models\CategorieProduit;
use App\Models\CategorieClient;
use App\Models\Agence;
use App\Models\PrixVenteProduit;
use App\Models\HistoriquePrixProduit;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;


class PrixProduitsImport implements ToArray
{
    public function array(array $data)
    {

        $k = 2;
        foreach ($data as $row) {
            if ($k == 1) {
                $k += 1;
                continue; // Ignorer la première ligne (entêtes)
            }

            $categorie_produit = CategorieProduit::where('Libelle', $row[1])->first();
            $produit = Produit::where('Designation', nettoyerPhrase($row[0]))->where('Id_Categorie', $categorie_produit->id)->first();
            $categorie_client = CategorieClient::firstOrCreate(['Libelle' => $row[2]]);
            $agence = Agence::where('NomAgence', nettoyerPhrase($row[3]))->first();

            if ($produit) {
                $prix_produit_vente = PrixVenteProduit::where('produit_id', $produit->id)
                    ->where('categorie_client_id', $categorie_client->id)
                    ->where('agence_id', $agence->id)
                    ->first();

                if ($prix_produit_vente) {
                    $prix_produit_vente->update([
                        'prix' => $row[4],
                        'date_variation_prix' => now(),
                        'user_id' => Auth::user()->id,
                    ]);
                } else {
                    PrixVenteProduit::create([
                        'produit_id' => $produit->id,
                        'categorie_client_id' => $categorie_client->id,
                        'agence_id' => $agence->id,
                    'prix' => $row[3],
                    // 'prix' => $row[4],
                        'date_enregistrement' => now(),
                        'date_variation_prix' => now(),
                        'user_id' => Auth::user()->id,
                    ]);
                }

                HistoriquePrixProduit::create([
                    'produit_id' => $produit->id,
                    'categorie_client_id' => $categorie_client->id,
                    'agence_id' => $agence->id,
                    'prix' => $row[3],
                    // 'prix' => $row[4],
                    'date_changement_prix' => now(),
                    'user_id' => Auth::user()->id,
                ]);
            }
        }
    }
}