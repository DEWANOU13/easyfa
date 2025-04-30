<?php

namespace App\Imports;

use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\StockHistories;
use App\Models\CategorieClient;
use App\Models\Agence;
use App\Models\DetailImportStock;
use App\Models\PrefixeReference;
use App\Models\HistoriquePrixProduit;
use App\Models\ImportStock;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockImport implements ToCollection, WithHeadingRow
{
    protected $importStock;

    public function __construct()
    {
        // Initialiser l'objet ImportStock
        $this->importStock = new ImportStock();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
            $produit = Produit::where(function ($query) use ($row) {
                $query->where('Designation', $row['nom_produit'])
                      ->orWhere('Reference', $row['reference_produit']);
            })
            ->where('Statut', '=', 'ACTIF')
            ->where('Type', '=', 'PRODUIT')
            ->first();

            // dd( $magasin, $produit);


            // Vérifier si le magasin et le produit existent
            if (!$magasin ) {
                throw new Exception('Magasin introuvable.' .' '.$row['magasin']);
            }

             // Vérifier si le magasin et le produit existent
             if (!$produit) {
                throw new Exception('Produit introuvable.' .$row['reference_produit'].' => '. $row['nom_produit']);
            }

            // Vérifier le stock
            $stock = Stock::where('Id_Produit', $produit->id)
                ->where('Id_Magasin', $magasin->id)
                ->first(); // Utilisation de first()

                // dd($stock);

            if ($stock) {
                // Créer l'entrée de produit
                $this->createEntrerProduit($row);
            } else {
                // Lever une exception sans interrompre la boucle
                throw new Exception('Produit indisponible en stock.');
            }
        }

    }

    public function createImportStock()
    {
        $date_import = Carbon::now();
        $site_id = session()->get('site_id');
        $lastDigitOfYear = Carbon::now()->format('y');
        $prefix = PrefixeReference::first()->acheminement ?? '';

        // Générer une référence unique
        $lastReference = ImportStock::where('Id_Agence', $site_id)->count();
        $incrementedReferenceNumber = str_pad($lastReference + 1, 5, '0', STR_PAD_LEFT);
        $reference_import_stock = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";

        // Remplir et sauvegarder l'objet ImportStock
        $this->importStock->fill([
            'Date_Entree' => $date_import,
            'Id_Utilisateur' => auth()->user()->id,
            'Reference_Import_Stock' => $reference_import_stock,
            'Observations' => 'Imported from Excel',
            'Id_Agence' => $site_id,
        ]);

        // Sauvegarder avant d'accéder à son ID
        $this->importStock->save();

        // Vérifier si l'ID est bien présent après sauvegarde
        if ($this->importStock->id === null) {
            throw new Exception('Impossible de récupérer l\'ID après la sauvegarde de l\'importation.');
        }
    }


    protected function createEntrerProduit($row)
    {
        $site_id = session()->get('site_id');
        $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
        $produit = Produit::where(function ($query) use ($row) {
            $query->where('Designation', $row['nom_produit'])
                  ->orWhere('Reference', $row['reference_produit']);
        })
        ->where('Statut', '=', 'ACTIF')
        ->where('Type', '=', 'PRODUIT')
        ->first();

        //  dd( $magasin, $produit);


        $quantity = $row['quantite'];
        $prixAchat = $row['prix_achat'];
        $date_entree = Carbon::now();

        // Appel à createImportStock après le traitement de toutes les lignes
        $this->createImportStock();

        // dd($this->importStock->id);

        // Créer l'entrée de produit
        DetailImportStock::create([
            'Id_Import_Stock' => $this->importStock->id,
            'Id_Produit' => $produit->id,
            'Id_Magasin' => $magasin->id,
            'Qte_Importee' => $quantity,
            'Prix_Achat' => $prixAchat,
        ]);

        // Mettre à jour le stock et l'historique
        $this->updateStock($produit->id, $magasin->id, $quantity, $prixAchat, $site_id, $date_entree, $this->importStock->Reference_Import_Stock);
    }

    protected function updateStock($produitId, $magasinId, $quantity, $prixAchat, $site_id, $date_entree, $reference_import_stock)
    {
        $stock = Stock::where('Id_Produit', $produitId)
            ->where('Id_Magasin', $magasinId)
            ->first();

            // dd($prixAchat);

        if ($stock) {
            // $stock->update([
            //     'Qte_stockee' => $quantity,
            //     'Prix_Achat_Net' => $prixAchat,
            // ]);

            $stock->Qte_stockee = $quantity;
            $stock->Prix_Achat_Net = $prixAchat;
            $stock->update();

            $historique_stock = new StockHistories();
            $historique_stock->Date = $date_entree;
            $historique_stock->agence_id = $site_id;
            $historique_stock->Motif = 'Mise à jour du stock';
            $historique_stock->Justificatif = $reference_import_stock;
            $historique_stock->operation = 'IMPORTATION_STOCK'; // Tu peux définir l'opération si nécessaire
            $historique_stock->type_operation = 'IMPORTATION_STOCK';
            $historique_stock->Id_Utilisateur = Auth::id();
            $historique_stock->Id_Produit = $produitId;
            $historique_stock->Id_Magasin = $magasinId;
            $historique_stock->Quantite = $quantity;

            // Sauvegarder l'enregistrement
            $historique_stock->save();

        } else {
            throw new Exception('Stock non trouvé.');
        }
    }
}

