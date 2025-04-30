<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Magasin;
use App\Models\Stock;
use App\Models\StockHistories;
use App\Models\CategorieClient;
use App\Models\Agence;
use App\Models\DetailImportStockEmballage;
use App\Models\Emballage;
use App\Models\PrefixeReference;
use App\Models\HistoriquePrixEmballage;
use App\Models\ImportStockEmballage;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class StockImportEmballage implements ToCollection, WithHeadingRow
{
    protected $importStockEmballage;

    public function __construct()
    {
        // Initialiser l'objet ImportStockEmballage
        $this->importStockEmballage = new ImportStockEmballage();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
            $emballage = Emballage::where(function ($query) use ($row) {
                $query->where('Nom_emballage', $row['nom_emballage'])
                      ->orWhere('Reference', $row['reference_emballage']);
            })
            // ->where('Statut', '=', 'ACTIF')
            // ->where('Type', '=', 'PRODUIT')
            ->first();

            // dd( $magasin, $emballage);

            // Vérifier si le magasin et le emballage existent
            if (!$magasin ) {
                throw new Exception('Magasin introuvable.');
            }

             // Vérifier si le magasin et le emballage existent
             if (!$emballage) {
                throw new Exception('Emballage introuvable.'. ' ' .$row['reference_emballage'].' '.$row['nom_emballage']);
            }


            // Vérifier le stock_emballage
            $stock_emballage = StockEmballage::where('Id_Emballage', $emballage->id)
                ->where('Id_Magasin', $magasin->id)
                ->first(); // Utilisation de first()

                // dd($stock_emballage);

            if ($stock_emballage) {
                // Créer l'entrée de emballage
                $this->createEntrerEmballage($row);
            } else {
                // Lever une exception sans interrompre la boucle
                throw new Exception('Emballage indisponible en stock.');
            }
        }

    }

    public function createImportStockEmballage()
    {
        $date_import = Carbon::now();
        $site_id = session()->get('site_id');
        $lastDigitOfYear = Carbon::now()->format('y');
        $prefix = PrefixeReference::first()->acheminement ?? '';

        // Générer une référence unique
        $lastReference = ImportStockEmballage::where('Id_Agence', $site_id)->count();
        $incrementedReferenceNumber = str_pad($lastReference + 1, 5, '0', STR_PAD_LEFT);
        $reference_import_stock = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";

        // dd($reference_import_stock);

        // Remplir et sauvegarder l'objet ImportStockEmballage
        $this->importStockEmballage->fill([
            'Date_Entree' => $date_import,
            'Id_Utilisateur' => auth()->user()->id,
            'Reference_Import_Stock' => $reference_import_stock,
            'Observations' => 'Imported from Excel',
            'Id_Agence' => $site_id,
        ]);

        // dd($this->importStockEmballage);

        // Sauvegarder avant d'accéder à son ID
        $this->importStockEmballage->save();

        // Vérifier si l'ID est bien présent après sauvegarde
        if ($this->importStockEmballage->id === null) {
            throw new Exception('Impossible de récupérer l\'ID après la sauvegarde de l\'importation.');
        }
    }


    protected function createEntrerEmballage($row)
    {
        $site_id = session()->get('site_id');
        $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
        $emballage = Emballage::where(function ($query) use ($row) {
            $query->where('Nom_emballage', $row['nom_emballage'])
                  ->orWhere('Reference', $row['reference_emballage']);
        })
        ->first();
        //  dd( $magasin, $emballage);

        $quantity = $row['quantite'];
        $prixAchat = $row['prix_achat'];
        $date_entree = Carbon::now();

        // Appel à createImportStockEmballage après le traitement de toutes les lignes
        $this->createImportStockEmballage();

        // dd($this->importStockEmballage->id);
        // Créer l'entrée de emballage
        DetailImportStockEmballage::create([
            'Id_Import_Stock_Emballage' => $this->importStockEmballage->id,
            'Id_Emballage' => $emballage->id,
            'Id_Magasin' => $magasin->id,
            'Qte_Importee' => $quantity,
            'Prix_Achat' => $prixAchat,
        ]);

        // Mettre à jour le stock et l'historique
        $this->updateStockEmballage($emballage->id, $magasin->id, $quantity, $prixAchat, $site_id, $date_entree, $this->importStockEmballage->Reference_Import_Stock);
    }

    protected function updateStockEmballage($emballageId, $magasinId, $quantity, $prixAchat, $site_id, $date_entree, $reference_import_stock)
    {
        $stock_emballage = StockEmballage::where('Id_Emballage', $emballageId)
            ->where('Id_Magasin', $magasinId)
            ->first();

            // dd($prixAchat);

        if ($stock_emballage) {
            // $stock_emballage->update([
            //     'Qte_stockee' => $quantity,
            //     'Prix_Achat_Net' => $prixAchat,
            // ]);

            $stock_emballage->Qte_stockee = $quantity;
            $stock_emballage->Prix_Achat_Net = $prixAchat;
            $stock_emballage->update();

            $historique_stock_emballage = new StockEmballageHistories();
            $historique_stock_emballage->Date = $date_entree;
            $historique_stock_emballage->agence_id = $site_id;
            $historique_stock_emballage->Motif = 'Mise à jour du stock_emballage';
            $historique_stock_emballage->Justificatif = $reference_import_stock;
            $historique_stock_emballage->operation = 'IMPORTATION_STOCK'; // Tu peux définir l'opération si nécessaire
            $historique_stock_emballage->type_operation = 'IMPORTATION_STOCK';
            $historique_stock_emballage->Id_Utilisateur = Auth::id();
            $historique_stock_emballage->Id_Emballage = $emballageId;
            $historique_stock_emballage->Id_Magasin = $magasinId;
            $historique_stock_emballage->Quantite = $quantity;

            // Sauvegarder l'enregistrement
            $historique_stock_emballage->save();

        } else {
            throw new Exception('Stock non trouvé.');
        }
    }
}
