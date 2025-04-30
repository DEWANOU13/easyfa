<?php

namespace App\Imports;

use App\Models\Emballage;
use App\Models\EntreeEmballage;
use App\Models\EntreLigneEmballage;
use App\Models\Fournisseur;
use App\Models\Magasin;
use App\Models\PrefixeReference;
use App\Models\StockEmballage;
use App\Models\StockEmballageHistories;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntreeEmballageImport implements ToCollection, WithHeadingRow
{


    protected $importEntreeEmballage;
    protected $fournisseur;

    public function __construct($fournisseur)
    {
        // Initialiser l'objet ImportStock
        $this->importEntreeEmballage = new EntreeEmballage();
        $this->fournisseur = $fournisseur;
    }


    public function collection(Collection $rows)
    {

        // dd('ici', $rows);
        // Appel à createImportStock après le traitement de toutes les lignes
        $this->createImportEntreeEmballage();
        foreach ($rows as $row) {


            $site_id = session()->get('site_id');

            // dd($row);

            $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
            $emballage = Emballage::where('Nom_emballage', $row['nom_emballage'])
                ->where('Reference', $row['reference_emballage'])
                // ->where('Statut', '=', 'ACTIF')
                // ->where('Type', '=', 'PRODUIT')
                ->first();

                // dd($magasin, $emballage);

            // dd($magasin, $emballage);
            // Vérifier si le magasin et le emballage existent
            // if (!$magasin || !$emballage) {
            //     throw new Exception('Magasin ou emballage introuvable.');
            // }

            if (!$magasin) {
                throw new Exception('Magasin introuvable.'.' '.$row['magasin']);
            }

            if ($magasin->agence_id !== $site_id) {
                throw new Exception('Les magasins ne correspondent pas à la agence connectée.'.' '.$row['magasin']);
            }

            if (!$emballage) {
                throw new Exception('Emballage introuvable.'. ' ' .$row['reference_emballage'].' '.$row['nom_emballage']);
            }

            $this->createEntrerProduit($row);
        }
    }


    public function createImportEntreeEmballage()
    {
        $date_import = Carbon::now();
        $site_id = session()->get('site_id');
        $lastDigitOfYear = Carbon::now()->format('y');
        $prefix = PrefixeReference::first()->entre_produit ?? '';

        // Générer une référence unique
        $lastReference = EntreeEmballage::where('Id_Agence', $site_id)->count();
        $incrementedReferenceNumber = str_pad($lastReference + 1, 5, '0', STR_PAD_LEFT);
        $reference_entree_emballage = "{$site_id}/{$lastDigitOfYear}/EEMB/{$incrementedReferenceNumber}";

        // dd($reference_entree_emballage);

        $fournisseur = Fournisseur::find($this->fournisseur);

        // dd($fournisseur);

        if ($fournisseur) {

            $this->importEntreeEmballage->Date_Entree = $date_import;
            $this->importEntreeEmballage->Id_Utilisateur = auth()->user()->id;
            $this->importEntreeEmballage->Reference_Entree = $reference_entree_emballage;
            $this->importEntreeEmballage->Observations = 'Entrée Emballage importer depuis le fichier excel';
            $this->importEntreeEmballage->Id_Agence = $site_id;
            $this->importEntreeEmballage->Id_Fournisseur = $fournisseur->id;

            // dd(' $this->importEntreeEmballage',  $this->importEntreeEmballage);
            // Sauvegarder avant d'accéder à son ID
            $this->importEntreeEmballage->save();
        } else {
            throw new Exception('Le fournisseur n\'exite pas. Veuillez le créer ');
        }


        // Vérifier si l'ID est bien présent après sauvegarde
        if ($this->importEntreeEmballage->id === null) {
            throw new Exception('Impossible de récupérer l\'ID après la sauvegarde de l\'importation.');
        }
    }

    protected function createEntrerProduit($row)
    {
        $site_id = session()->get('site_id');
        $magasin = Magasin::where('NomMagasin', $row['magasin'])->first();
        $emballage = Emballage::where('Nom_emballage', $row['nom_emballage'])
        ->where('Reference', $row['reference_emballage'])
        // ->where('Statut', '=', 'ACTIF')
        // ->where('Type', '=', 'PRODUIT')
        ->first();


        // dd($magasin->id, $emballage->id);

        $quantity = $row['quantite'];
        $prixAchat = $row['prix_achat'];
        $date_entree_emballage = Carbon::now();

        $entrer_produit = new EntreLigneEmballage();
        $entrer_produit->Id_Entree_Emballage = $this->importEntreeEmballage->id;
        $entrer_produit->Id_Emballage = $emballage->id;
        $entrer_produit->Id_Magasin = $magasin->id;
        $entrer_produit->Qte_Entree = $quantity;
        $entrer_produit->Prix_Achat_Net = $prixAchat;
        $entrer_produit->save();



        // Mettre à jour le stock_emballage et l'historique
        $this->updateStock($emballage->id, $magasin->id, $quantity, $prixAchat, $site_id, $date_entree_emballage, $this->importEntreeEmballage->Reference_Entree);
    }

    protected function updateStock($emballageId, $magasinId, $quantity, $prixAchat, $site_id, $date_entree_emballage, $reference_entree_emballage)
    {
        $stock_emballage = StockEmballage::where('Id_Emballage', $emballageId)
            ->where('Id_Magasin', $magasinId)
            ->first();

            // dd($stock_emballage);

        if ($stock_emballage) {
            $quantite_stockee = $stock_emballage->Qte_stockee + $quantity;


            // Vérifier que $quantite_stockee n'est pas zéro avant de faire la division
            if ($quantite_stockee > 0) {
                $prix_achat_net = (($stock_emballage->Prix_Achat_Net * $stock_emballage->Qte_stockee) + ($prixAchat * $quantity)) / $quantite_stockee;

                // $stock_emballage->update([
                //     'Qte_stockee' => $quantite_stockee,
                //     'Prix_Achat_Net' => $prix_achat_net,
                // ]);

                $stock_emballage->Qte_stockee = $quantite_stockee;
                $stock_emballage->Prix_Achat_Net = $prix_achat_net;
                $stock_emballage->save();
            } else {
                // Message d'erreur si la quantité stockée est insuffisante
                return back()->withErrors(['error' => 'Impossible de mettre à jour le stock_emballage : la quantité totale ne peut pas être égale à zéro. Veuillez vérifier les données saisies.']);
            }
        } else {
            $stock_emballage = new StockEmballage();
            $stock_emballage->Id_Emballage = $emballageId;
            $stock_emballage->Id_Magasin = $magasinId;
            $stock_emballage->Qte_stockee = $quantity;
            $stock_emballage->Prix_Achat_Net = $prixAchat;
            $stock_emballage->Enregistrer_par = auth()->user()->id;
            $stock_emballage->save();
        }

        $historique_stock = new StockEmballageHistories();
        $historique_stock->Date = $date_entree_emballage;
        $historique_stock->agence_id = $site_id;
        $historique_stock->Motif = 'Entrée importer';
        $historique_stock->Justificatif = $reference_entree_emballage;
        $historique_stock->operation = 'ENTREE'; // Tu peux définir l'opération si nécessaire
        $historique_stock->type_operation = 'ENTREE';
        $historique_stock->Id_Utilisateur = Auth::id();
        $historique_stock->Id_Emballage = $emballageId;
        $historique_stock->Id_Magasin = $magasinId;
        $historique_stock->Quantite = $quantity;

        // Sauvegarder l'enregistrement
        $historique_stock->save();
    }
}
