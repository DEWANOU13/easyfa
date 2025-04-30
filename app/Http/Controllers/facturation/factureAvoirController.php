<?php

namespace App\Http\Controllers\facturation;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\Consignation;
use App\Models\Lignefacture;
use App\Models\TotalFacture;
use Illuminate\Http\Request;
use App\Models\ArchiveFacture;
use App\Models\StockEmballage;
use App\Models\StockHistories;
use App\Models\PrefixeReference;
use App\Services\FactureService;
use App\Models\LigneConsignation;
use Illuminate\Support\Facades\DB;
use App\Models\ArchiveLigneFacture;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriqueVPrestation;
use App\Models\LienFactureConsignation;
use App\Models\StockEmballageHistories;

class factureAvoirController extends Controller
{
    protected $factureService;

    public function __construct(FactureService $factureService)
    {
        $this->factureService = $factureService;
    }


    // cette fonction permet de verifier la validité de l'api
    public function statusFacture()
    {
        $response = $this->factureService->statusService();

        if ($response->successful()) {
            $invoices = $response->json();
            return  $this->postInvoiceRequestDto(Facture::first());
        } else {
            // Gérer les erreurs de requête
            return response()->json(['error' => 'Erreur lors de la récupération des factures'], $response->status());
        }
    }

    //cette fonction permet de creer une facture
    public function postInvoiceRequestDto($factureId)
    {
        // $facture = Facture::where('id', $factureId)->first();

        $archiveFacture = ArchiveFacture::where('id', $factureId)->first();
        if ($archiveFacture->Statut_facture == 'NORMALISEE'  || $archiveFacture->Statut_facture =='INVALIDEE') {
            return redirect()->route('avoir')->with('error', 'Cette Facture ne peut plus ètre normaliser en facture avoir');
        }

        $archiveFactureId = $archiveFacture->idFacture_originale;
        $dd = ArchiveFacture::where('facture_id', $archiveFactureId)->first();
        $idF = $dd->id;


        $ligneArchiveFacture = ArchiveLigneFacture::join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->select('archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Prix_revient', 'produits.id', 'produits.Reference', 'archive_ligne_factures.Produit_designation', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', DB::raw('SUM(archive_ligne_factures.Qte) as total_qte'))
            ->where('archive_ligne_factures.archive_factures_id', $idF)
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'archive_ligne_factures.Produit_designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Prix_revient')
            ->get();

        //dd($ligneArchiveFacture);



        $items = [];

        // Parcourir la liste des propositions et les ajouter au tableau d'items
        foreach ($ligneArchiveFacture as $ligne) {
            $items[] = [
                "name" => $ligne->Produit_designation,
                "price" => $ligne->Prix_revient,
                "quantity" => $ligne->total_qte,
                "taxGroup" => $ligne->Code_lettre
            ];
        }




        $user = Auth::user();
        $user_connecter = $user->name;
        $user_connecterId = $user->id;

        $nomClient = $archiveFacture->Nom_client;
        $codeClient = $archiveFacture->Code_client;
        $ifuClient = $archiveFacture->Ifu_client;
        $telephoneClient = $archiveFacture->Telephone_client;

        $typeFacture = $archiveFacture->Code_type_facture;

        $reference = $archiveFacture->Code_signature;

        $valAib = $archiveFacture->Aib;

        if ($valAib == 1) {
            if (strlen($ifuClient) == 13) {  // La fonction correcte pour obtenir la longueur d'une chaîne est strlen()
                $aib = 'A';
            } else {
                $aib = 'B';
            }
        } else {
            $aib = '';
        }

        $invoiceRequestDto = [
            'ifu' => '1201642438100',
            'type' => $typeFacture,
            'Reference' => $reference,
            'items' => $items,
            'aib' => $aib,
            'client' => [
                'contact' => $telephoneClient,
                'ifu' => $ifuClient,
                'name' => $nomClient,

            ],
            'operator' => [
                'id' => $user_connecterId,
                'name' => $user_connecter,
            ],
            'payment' => [
                [
                    "name" => "AUTRE",
                    //"amount" => 4000
                ]
            ],

        ];
        //  dd($type);

        $response = $this->factureService->postInvoiceRequestDtoService($invoiceRequestDto);

        if ($response->successful()) {
            $invoices = $response->json();
            // dd($invoices);
            if (!isset($invoices['uid'])) {
                return to_route('avoir')->with('error', 'Echec de la normalisation de la facture. NO UID');
            }
            $uid = $invoices['uid'];
            if ($uid) {
                return $this->getInvoiceDetailsDto($uid, $factureId);
            } else {
                // Gérer le cas où $uid est nul ou non défini
                return response()->json(['error' => 'UID de facture invalide'], 400);
            }
            // Traitez les données des factures reçues


            return response()->json($invoices);
        } else {
            // Gérer les erreurs de requête
            return response()->json(['error' => 'Erreur lors de la demande de facture'], $response->status());
        }
    }
    //recuperer les detail defacture
    public function getInvoiceDetailsDto($uid, $factureId)
    {

        $response = $this->factureService->getInvoiceDetailsDtoService($uid);


        if ($response->successful()) {
            $invoices = $response->json();

            // Traitez les données des factures reçues
            if ($invoices) {
                return $this->putFinalize($uid, $factureId);
            } else {
                // Gérer le cas où $uid est nul ou non défini
                return response()->json(['error' => 'UID de facture invalide'], 400);
            }
            return response()->json($invoices);
        } else {
            // Gérer les erreurs de requête
            return response()->json(['error' => 'Erreur lors de la récupération de facture'], $response->status());
        }
    }
    public function putFinalize($uid, $factureId)
    {
        $response = $this->factureService->putFinalizeService($uid);



        if ($response->successful()) {
            $invoices = $response->json();



            $nim = $invoices['nim'];
            $counters = $invoices['counters'];
            $codeMECeFDGI = $invoices['codeMECeFDGI'];
            $qrCode = $invoices['qrCode'];
            $dateTime = $invoices['dateTime'];
            $user = Auth::user();
            $user_connecterId = $user->id;


            $Agence_id = session()->get('site_id');


            // Division du compteur en deux parties
            $firstParts = explode(' ', $counters);
            $secondParts = explode('/', $firstParts[0]);

            $Compteur_type_facture = $secondParts[0]; // "556"
            $Compteur_total = $secondParts[1]; // "655"
            $Code_type_facture = $firstParts[1]; // "FV"

            //dd($Compteur_type_facture, $Compteur_total, $Code_type_facture);


            $formattedDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->format('Y-m-d H:i:s');


            $Date_facture =  Carbon::now();
            $archiveFacture = ArchiveFacture::where('id', $factureId)->first();
            $facture_id = $archiveFacture->facture_id;
            $facture = Facture::where('id', $facture_id)->first();
            $idFactureOrigine = $facture->idFacture_originale;
            $ancienneFacture = Facture::where('id', $idFactureOrigine)->first();


            $facture->Date_facture = $Date_facture;
            $facture->Nim_machine = $nim;
            $facture->Compteur_type_facture = $Compteur_type_facture;
            $facture->Compteur_total = $Compteur_total;
            $facture->Code_type_facture = $Code_type_facture;
            $facture->Code_signature = $codeMECeFDGI;
            $facture->QrCode = $qrCode;
            $facture->Statut_facture = 'NORMALISEE';
            $facture->Date_signature = $formattedDate;
            $facture->Aib = $archiveFacture->Aib;
            $facture->Aib_deductible = $archiveFacture->Aib_deductible;
            $facture->Commentaire = $archiveFacture->Commentaire;
            $facture->Objet_facture = $archiveFacture->Objet_facture;
            $facture->Validite = $archiveFacture->Validite;
            $facture->Net_a_payer = $archiveFacture->Net_a_payer;
            $facture->Autres_infos = $archiveFacture->Autres_infos;
            $facture->Numero_ifu_machine = $archiveFacture->Numero_ifu_machine;
            $facture->user_id = $user_connecterId;
            $facture->client_id = $ancienneFacture->client_id;
            $facture->agence_id = $Agence_id;
            $facture->update();


            //Info marchand
            $infoMarchand = User::where('id', $user_connecterId)->first();
            $nomMarchand = $infoMarchand->name;
            $idMarchand = $infoMarchand->id;
            $infoAgence = Agence::where('id', $facture->agence_id)->first();
            $nomAgence = $infoAgence->NomAgence;
            $adresseAgence = $infoAgence->adresseAgence;
            $telephoneAgence = implode('/', [$infoAgence->numero_telephone_1, $infoAgence->numero_telephone_2]);

            $archiveFacture->Date_facture = $Date_facture;
            $archiveFacture->Nim_machine = $nim;
            $archiveFacture->Compteur_type_facture = $Compteur_type_facture;
            $archiveFacture->Compteur_total = $Compteur_total;
            $archiveFacture->Code_type_facture = $Code_type_facture;
            $archiveFacture->Code_signature = $codeMECeFDGI;
            $archiveFacture->QrCode = $qrCode;
            $archiveFacture->Statut_facture = 'NORMALISEE';
            $archiveFacture->Date_signature = $formattedDate;
            $archiveFacture->Nom_agence = $nomAgence;
            $archiveFacture->Adresse_agence = $adresseAgence;
            $archiveFacture->Telephone_agence = $telephoneAgence;
            $archiveFacture->user_id = $idMarchand;
            $archiveFacture->Nom_user = $nomMarchand;

            $archiveFacture->update();

            $idNouvelFacture = $facture->id;

            //total facture
            $ligneTotalFV = TotalFacture::where('facture_id', $archiveFacture->facture_id)->first();
            $ligneTotalFV->Statut = '1';
            $ligneTotalFV->update();

            $ligneAch = ArchiveFacture::where('facture_id', $archiveFacture->idFacture_originale)->first();


            $ligneArchiveFacture = ArchiveLigneFacture::where('archive_factures_id', $ligneAch->id)->get();
            if ($ligneArchiveFacture->count() > 0) {
                foreach ($ligneArchiveFacture as $ligne) {
                    // Récupère le stock associé à la ligne de facture qui n'est pas de type "PRESTATION"
                    $stock = Stock::join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                        ->where('stocks.id', $ligne->stocks_id)
                        ->where('produits.Type', '!=', 'PRESTATION')
                        ->where('produits.Type', '!=', 'TAXE_SIMPLE')
                        ->first();
                    $stockPrestation = Stock::join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                        ->where('stocks.id', $ligne->stocks_id)
                        ->where(function ($query) {
                            $query->where('produits.Type', 'PRESTATION')
                                  ->orWhere('produits.Type', 'TAXE_SIMPLE');
                        })
                        ->first();


                    // Si un stock correspondant est trouvé, augmentez la quantité stockée
                    if ($stock) {
                        $modifStock = Stock::find($ligne->stocks_id);
                        $modifStock->Qte_stockee += $ligne->Qte;
                        $modifStock->update();
                        $idMagasinSock = $modifStock->Id_Magasin;
                            //dd($modifStock);


                        /*    $trouverhistorique = StockHistories::where('Id_Produit', $stock->Id_Produit)
                            ->where('Id_Magasin', $stock->Id_Magasin)
                            ->where('Justificatif', $ancienneFacture->Reference_facture)
                            ->where('operation', 'SORTIE')
                            ->where('type_operation','FACTURE_V')->first(); */


                        //Historiq stock
                        $historique_entree_produit = new StockHistories();
                        $historique_entree_produit->Date = $facture->Date_facture;
                        $historique_entree_produit->agence_id = $facture->agence_id;
                        $historique_entree_produit->Motif =  'Facture Avoir normalisée';
                        $historique_entree_produit->Justificatif = $ancienneFacture->Reference_facture;
                        $historique_entree_produit->operation = 'ENTREE';
                        $historique_entree_produit->type_operation = 'FACTURE_A';
                        $historique_entree_produit->Id_Utilisateur = auth()->user()->id;
                        $historique_entree_produit->Id_Produit = $stock->Id_Produit;
                        $historique_entree_produit->Id_Magasin = $stock->Id_Magasin;
                        $historique_entree_produit->Quantite = $ligne->Qte;
                        $historique_entree_produit->Prix_vente = $ligne->Prix_revient * $ligne->Qte;
                        $historique_entree_produit->Prix_achat = $stock->Prix_Achat_Net * $ligne->Qte;
                        $historique_entree_produit->save();




                        $produitIdd = $stock->Id_Produit;
                        $Produitemballage = Produit::where('id', $produitIdd)->first();


                        if ($Produitemballage->type_emballage == 'EMBALLAGE_RECUPERABLE') {

                            if($ligne->is_emballage == 0){

                                $idEmballage = $Produitemballage->Emballage_id;
                                if($idEmballage != null){
                                    $stockEmballage = StockEmballage::where('Id_Emballage',$idEmballage)->where('Id_Magasin',$idMagasinSock)->first();
                                      if(empty($stockEmballage)){
                                         $stockEmballage = new StockEmballage();
                                         $stockEmballage->Id_Emballage = $idEmballage;
                                         $stockEmballage->Id_Magasin = $idMagasinSock;
                                         $stockEmballage->Qte_stockee = 0;
                                         $stockEmballage->Prix_Achat_Net = 0;
                                         $stockEmballage->Enregistrer_par = auth()->user()->id;
                                         $stockEmballage->save();
                                     }

                                     $stockEmballage->Qte_stockee = $stockEmballage->Qte_stockee - $ligne->Qte;
                                     $stockEmballage->save();


                                                             //Historiq stock emballage
                                                             $historique_sortie_emballage = new StockEmballageHistories();
                                                             $historique_sortie_emballage->Date = Carbon::now();
                                                             $historique_sortie_emballage->agence_id = $facture->agence_id;
                                                             $historique_sortie_emballage->Motif = "Sortie d'emballage sur une Facture de vente";
                                                             $historique_sortie_emballage->Justificatif =$facture->Reference_facture;
                                                             $historique_sortie_emballage->operation ='SORTIE';
                                                             $historique_sortie_emballage->type_operation ='FACTURE_A';
                                                             $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                                                             $historique_sortie_emballage->Id_Emballage = $idEmballage;
                                                             $historique_sortie_emballage->Id_Magasin = $stock->Id_Magasin;
                                                             $historique_sortie_emballage->Quantite = $ligne->Qte;
                                                             $historique_sortie_emballage->save();
                                }
                            }


                            if($ligne->is_emballage == 1){

                                $idEmballage = $Produitemballage->Emballage_id;
                                if($idEmballage != null){
                                    $stockEmballage = StockEmballage::where('Id_Emballage',$idEmballage)->where('Id_Magasin',$idMagasinSock)->first();

                                     //$stockEmballage->Qte_stockee += $ligne->Qte;
                                     //$stockEmballage->save();


                                                             //Historiq stock emballage
                                                             $historique_sortie_emballage = new StockEmballageHistories();
                                                             $historique_sortie_emballage->Date = Carbon::now();
                                                             $historique_sortie_emballage->agence_id = $facture->agence_id;
                                                             $historique_sortie_emballage->Motif = "Entrée d'emballage sur une Facture avoir ";
                                                             $historique_sortie_emballage->Justificatif =$facture->Reference_facture;
                                                             $historique_sortie_emballage->operation ='SORTIE';
                                                             $historique_sortie_emballage->type_operation ='FACTURE_A_DECONSIGNATION';
                                                             $historique_sortie_emballage->Id_Utilisateur = auth()->user()->id;
                                                             $historique_sortie_emballage->Id_Emballage = $idEmballage;
                                                             $historique_sortie_emballage->Id_Magasin = $stock->Id_Magasin;
                                                             $historique_sortie_emballage->Quantite = $ligne->Qte;
                                                             $historique_sortie_emballage->Prix_vente = $ligne->Prix_revient * $ligne->Qte;
                                                             $historique_sortie_emballage->save();


                                            //retour des quantités dans ligne consignation
                                            $ancienneFacture = Facture::where('id', $idFactureOrigine)->first();
                                            $idFact = $ancienneFacture->id;

                                            $lienFactureConsignation = LienFactureConsignation::where('facture_id', $idFact)->first();

                                            $trouverSauvegardeConsignation = Consignation::findorfail($lienFactureConsignation->consignation_id);
                                            if ($trouverSauvegardeConsignation) {
                                                $trouverLigneConsignation = LigneConsignation::where('consignation_id', $trouverSauvegardeConsignation->id)->where('produit_id', $Produitemballage->id)->first();
                                                $trouverLigneConsignation->facturee = $trouverLigneConsignation->facturee - $ligne->Qte;
                                                $trouverLigneConsignation->save();
                                            }
                                }

                            }



                         }
                    }


                    if($stockPrestation)
                    {

                        $historique_prestation = new HistoriqueVPrestation();
                        $historique_prestation->Date = $facture->Date_facture;
                        $historique_prestation->agence_id =  $facture->agence_id;
                        $historique_prestation->Motif =  'Facture Avoir normalisée';
                        $historique_prestation->Justificatif = $ancienneFacture->Reference_facture;
                        $historique_prestation->operation ='ENTREE';
                        $historique_prestation->type_operation ='FACTURE_A';
                        $historique_prestation->Id_Utilisateur = auth()->user()->id;
                        $historique_prestation->Id_Produit = $stockPrestation->Id_Produit;
                       // $historique_prestation->Id_Magasin = $stock->Id_Magasin;
                        $historique_prestation->Quantite = $ligne->Qte;
                        $historique_prestation->Prix_vente = $ligne->Prix_revient * $ligne->Qte;
                        $historique_prestation->save();

                    }
                }
            }
            $ancienneFacture = Facture::where('id', $idFactureOrigine)->first();
            $idFact = $ancienneFacture->id;
            $annulerFacture = Facture::find($idFact);
            $annulerFacture->Statut_facture = 'ANNULEE';
            $annulerFacture->save();

            $annulerAchiveFacture = ArchiveFacture::where('facture_id', $idFact)->first();
            $annulerAchiveFacture->Statut_facture = 'ANNULEE';
            $annulerAchiveFacture->save();

            $trouverSauvegardeConsignation = Consignation::where('facture_id', $idFact)->first();
            if ($trouverSauvegardeConsignation) {
                $annulerSauvegardeConsignation = Consignation::find($trouverSauvegardeConsignation->id);
                $annulerSauvegardeConsignation->statut = 'ANNULEE';
                $annulerSauvegardeConsignation->save();
            }




            return to_route('avoir')->with('success', 'Facture avoir normalisée avec succès.');
        } else {
            // Gérer les erreurs de requête
            return response()->json(['error' => 'Erreur lors de la normalisation de la facture'], $response->status());
        }
    }
}
