<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\EntreeProduit;
use App\Models\EntrerProduit;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\StockHistories;
use App\Models\CategorieClient;
use App\Models\Agence;
use App\Models\DetailImportStock;
use App\Models\Fournisseur;
use App\Models\PrefixeReference;
use App\Models\HistoriquePrixProduit;
use App\Models\ImportStock;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntreeImport  implements ToCollection, WithHeadingRow
{
    protected $importEntreeProduit;
    protected $fournisseur;
    protected $entree_consignation;

    public function __construct($fournisseur, $entree_consignation)
    {
        // Initialiser l'objet ImportStock
        $this->importEntreeProduit = new EntreeProduit();
        $this->fournisseur = $fournisseur;
        $this->entree_consignation = $entree_consignation;
    }


    public function collection(Collection $rows)
    {

        // dd('ici', $rows);
        // Appel à createImportStock après le traitement de toutes les lignes
        $this->createImportEntreeProduit();
        foreach ($rows as $row) {

        $site_id = session()->get('site_id');


            $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
            // dd($magasin, $site_id);
            $produit = Produit::where('Reference', '=', $row['reference_produit'])
                // ->where('Designation', '=', $row['nom_produit'])
                ->where('Statut', '=', 'ACTIF')
                ->where('Type', '=', 'PRODUIT')
                ->first();

            // dd($magasin, $produit);
            // Vérifier si le magasin et le produit existent
            if (!$magasin) {
                throw new Exception('Magasin introuvable.'.' '.$row['magasin']);
            }

            if ($magasin->agence_id != $site_id) {
                throw new Exception('Les magasins ne correspondent pas à la agence connectée.'.' '.$row['magasin']);
            }

            if (!$produit) {
                throw new Exception('Produit introuvable.'. ' ' .$row['reference_produit'].' '.$row['nom_produit']);
            }

            $this->createEntrerProduit($row);
        }
    }


    public function createImportEntreeProduit()
    {
        $date_import = Carbon::now();
        $site_id = session()->get('site_id');
        $lastDigitOfYear = Carbon::now()->format('y');
        $prefix = PrefixeReference::first()->entre_produit ?? '';

        // Générer une référence unique
        $lastReference = EntreeProduit::where('Id_Agence', $site_id)->count();
        $incrementedReferenceNumber = str_pad($lastReference + 1, 5, '0', STR_PAD_LEFT);
        $reference_entree_produit = "{$site_id}/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";

        // dd($reference_entree_produit);

        $fournisseur = Fournisseur::find($this->fournisseur);

        // dd($fournisseur);

        if ($fournisseur) {

            $this->importEntreeProduit->Date_Entree = $date_import;
            $this->importEntreeProduit->Id_Utilisateur = auth()->user()->id;
            $this->importEntreeProduit->Reference_Entree = $reference_entree_produit;
            $this->importEntreeProduit->Observations = 'Entrée importer depuis le fichier excel';
            $this->importEntreeProduit->Id_Agence = $site_id;
            $this->importEntreeProduit->Id_Fournisseur = $fournisseur->id;

            // dd(' $this->importEntreeProduit',  $this->importEntreeProduit);
            // Sauvegarder avant d'accéder à son ID
            $this->importEntreeProduit->save();
        } else {
            throw new Exception('Le fournisseur n\'exite pas. Veuillez le créer ');
        }


        // Vérifier si l'ID est bien présent après sauvegarde
        if ($this->importEntreeProduit->id === null) {
            throw new Exception('Impossible de récupérer l\'ID après la sauvegarde de l\'importation.');
        }
    }


    protected function createEntrerProduit($row)
    {



        $site_id = session()->get('site_id');
        $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
        $produit = Produit::where('Reference', '=', $row['reference_produit'])
        // ->where('Designation', '=', $row['nom_produit'])
        ->where('Statut', '=', 'ACTIF')
        ->where('Type', '=', 'PRODUIT')
        ->first();

        // dd($magasin->id, $produit->id);

        $quantity = $row['quantite'];
        $prixAchat = $row['prix_achat'];
        $date_entree = Carbon::now();

        $entrer_produit = new EntrerProduit();
        $entrer_produit->Id_Entree_Produit = $this->importEntreeProduit->id;
        $entrer_produit->Id_Produit = $produit->id;
        $entrer_produit->Id_Magasin = $magasin->id;
        $entrer_produit->Qte_Entree = $quantity;
        $entrer_produit->Prix_Achat_Net = $prixAchat;
        $entrer_produit->save();



        // Mettre à jour le stock et l'historique
        $this->updateStock($produit->id, $magasin->id, $quantity, $prixAchat, $site_id, $date_entree, $this->importEntreeProduit->Reference_Entree);
    }

    protected function updateStock($produitId, $magasinId, $quantity, $prixAchat, $site_id, $date_entree, $reference_entree_produit)
    {
        $stock = Stock::where('Id_Produit', $produitId)
            ->where('Id_Magasin', $magasinId)
            ->first();

        if ($stock) {
            $quantite_stockee = $stock->Qte_stockee + $quantity;

            // Vérifier que $quantite_stockee n'est pas zéro avant de faire la division
            if ($quantite_stockee > 0) {
                $prix_achat_net = (($stock->Prix_Achat_Net * $stock->Qte_stockee) + ($prixAchat * $quantity)) / $quantite_stockee;

                $stock->update([
                    'Qte_stockee' => $quantite_stockee,
                    'Prix_Achat_Net' => $prix_achat_net,
                ]);
            } else {
                // Message d'erreur si la quantité stockée est insuffisante
                return back()->withErrors(['error' => 'Impossible de mettre à jour le stock : la quantité totale ne peut pas être égale à zéro. Veuillez vérifier les données saisies.']);
            }
        } else {
            $stock = new Stock();
            $stock->Id_Produit = $produitId;
            $stock->Id_Magasin = $magasinId;
            $stock->Qte_stockee = $quantity;
            $stock->Prix_Achat_Net = $prixAchat;
            $stock->Enregistrer_par = auth()->user()->id;
            $stock->save();
        }

        $produit = Produit::find($produitId);
        if ($this->entree_consignation == '1'){
            // dd('je suis ici');
            if ($produit->type_emballage == 'EMBALLAGE_RECUPERABLE') {

                // dd('EMBALLAGE_RECUPERABLE consi...');

                $Produitemballage = Produit::where('id', $produitId)->first();
                $idEmballage = $Produitemballage->Emballage_id;
                if ($idEmballage != null) {
                    $stockEmballage = StockEmballage::where('Id_Emballage', $idEmballage)->where('Id_Magasin', $magasinId)->first();
                    if (empty($stockEmballage)) {
                        $stockEmballage = new StockEmballage();
                        $stockEmballage->Id_Emballage = $idEmballage;
                        $stockEmballage->Id_Magasin = $magasinId;
                        $stockEmballage->Qte_stockee = 0;
                        $stockEmballage->Prix_Achat_Net = 0;
                        $stockEmballage->Enregistrer_par = auth()->user()->id;
                        $stockEmballage->save();
                    }


                    // dd('repponse consi...', $reponse_consignation, $stockEmballage->id);

                    $stockEmballage->Qte_stockee  -= $quantity;
                    $stockEmballage->update();


                    //Historiq stock emballage
                    $historique_sortie_emballage = new StockEmballageHistories();
                    $historique_sortie_emballage->Date = Carbon::now();
                    $historique_sortie_emballage->agence_id = $site_id;
                    $historique_sortie_emballage->Motif = "Sortie d'emballage sur une entrée de produit ayant un emballage";
                    $historique_sortie_emballage->Justificatif = $reference_entree_produit;
                    $historique_sortie_emballage->operation = 'SORTIE';
                    $historique_sortie_emballage->type_operation = 'SORTIE';
                    $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                    $historique_sortie_emballage->Id_Emballage = $idEmballage;
                    $historique_sortie_emballage->Id_Magasin = $magasinId;
                    $historique_sortie_emballage->Quantite = $quantity;
                    $historique_sortie_emballage->save();
                }
            }
        }


        $historique_stock = new StockHistories();
        $historique_stock->Date = $date_entree;
        $historique_stock->agence_id = $site_id;
        $historique_stock->Motif = 'Entrée importer';
        $historique_stock->Justificatif = $reference_entree_produit;
        $historique_stock->operation = 'ENTREE'; // Tu peux définir l'opération si nécessaire
        $historique_stock->type_operation = 'ENTREE';
        $historique_stock->Id_Utilisateur = Auth::id();
        $historique_stock->Id_Produit = $produitId;
        $historique_stock->Id_Magasin = $magasinId;
        $historique_stock->Quantite = $quantity;

        // Sauvegarder l'enregistrement
        $historique_stock->save();
    }
}
