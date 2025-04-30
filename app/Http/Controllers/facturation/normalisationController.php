<?php

namespace App\Http\Controllers\facturation;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Lignefacture;
use Illuminate\Http\Request;
use App\Services\FactureService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\ArchiveFacture;
use App\Models\ArchiveLigneFacture;
use App\Models\HistoriqueReglement;
use App\Models\PrefixeReference;
use App\Models\TotalFacture;
use Illuminate\Support\Facades\Auth;


class normalisationController extends Controller
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
    public function postInvoiceRequestDto( $factureId)
    {
        $this->authorize('normaliser-facture');
        $facture = Facture::where('id', $factureId)->first();
        if($facture->Statut_facture == 'INVALIDEE' || $facture->Statut_facture == 'ANNULEE' || $facture->Statut_facture == 'SOLDE' || $facture->Statut_facture == 'NORMALISEE' || $facture->Statut_facture == 'EN COURS DE REGLEMENT') {

            return redirect()->route('facture')->with('error', 'Cette Facture ne peut plus etre normalisée');
        }
        $priseEnCompteReglement = PrefixeReference::first()->prise_en_compte_reglement;
        if($priseEnCompteReglement == null){

            return redirect()->route('facture')->with('error', 'Veuillez configurer le règlement dans les paramètres du site');
        }


         $ligneFacture = Lignefacture::join('groupe_taxations', 'groupe_taxations.id', '=', 'lignefactures.GroupeTaxe_id')
            ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->select('lignefactures.Prix_unitaire_HT','lignefactures.Taux_remise','lignefactures.Prix_revient','produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', DB::raw('SUM(lignefactures.Qte) as total_qte'))
            ->where('lignefactures.facture_id', $factureId)
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre','lignefactures.Prix_unitaire_HT','lignefactures.Prix_revient','lignefactures.Taux_remise')
            ->get();


        $items = [];
        $totalTTC = 0;
        $totalHt = 0;
        // Parcourir la liste des propositions et les ajouter au tableau d'items
        foreach ($ligneFacture as $ligne) {
            $prixUHT= $ligne->Prix_unitaire_HT - ($ligne->Taux_remise * $ligne->Prix_unitaire_HT) / 100;

            $totalLigne = $ligne->Prix_revient * $ligne->total_qte;


            $totalLigneHT = round($totalLigne / 1.18);

            $totalTTC += $totalLigne;
            $totalHt += $totalLigneHT;
            $items[] = [
                "name" => $ligne->Designation,
                "price" => $ligne->Prix_revient,
                "quantity" => $ligne->total_qte,
                "taxGroup" => $ligne->Code_lettre
            ];
        }


       $user = Auth::user();
        $user_connecter = $user->name;
        $user_connecterId = $user->id;

        $infoClient = Client::where('id', $facture->client_id)->first();
        $nomClient = $infoClient->Denomination_sociale;
        $codeClient = $infoClient->Code_client;
        $ifuClient = $infoClient->Numero_ifu;
        $telephoneClient = $infoClient->Telephone_mobile;
        $adressClient = $infoClient->Adresse_client;

        $type = $facture->Code_type_facture;
        $valAib = $facture->Aib;

        if ($valAib == 1) {
            if (strlen($ifuClient) == 13) {  // La fonction correcte pour obtenir la longueur d'une chaîne est strlen()
                $aib = 'A';
                $total_avec_aib = round($totalTTC + ($totalHt * 0.01));
            } else {
                $aib = 'B';
                $total_avec_aib = round($totalTTC + ($totalHt * 0.05));
            }
        }else{
            $aib = '';
            $total_avec_aib = round($totalTTC);
        }

        $invoiceRequestDto = [
            'ifu' => '1201642438100',
            'type' => $type,
            'items' => $items,
            'aib' => $aib,
            'client' => [
                'contact' => $telephoneClient,
                'ifu' => $ifuClient,
                'name' => $nomClient,
                'address' => $adressClient,
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
        $ttc = round($total_avec_aib);

        $response = $this->factureService->postInvoiceRequestDtoService($invoiceRequestDto);

        if ($response->successful()) {
            $invoices = $response->json();

            $total_dgi = $invoices['total'];
       // dd($invoices);
            $uid = $invoices['uid'];
            if(!isset($invoices['uid'])){
                return to_route('facture')->with('error','Echec de la normalisation de la facture. NO UID');
            }
            if($total_dgi) {
                //dd($total_dgi, $ttc);
               // if($total_dgi == $ttc) {
                    if ($uid) {
                        return $this->getInvoiceDetailsDto($uid, $factureId);
                    } else {
                        // Gérer le cas où $uid est nul ou non défini
                        return response()->json(['error' => 'UID de facture invalide'], 400);
                    }
               /*  }else{
                    return to_route('facture')->with('error','Echec de la normalisation de la facture car la somme des montants est differente de la somme de la facture');
                } */
                // Traitez les données des factures reçues

            }


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

            if (is_array(getIdAgenceByUser())) {
                $Agence_id = 1;
            }else{
                $Agence_id = session()->get('site_id');
            }


            // Division du compteur en deux parties
            $firstParts = explode(' ', $counters);
            $secondParts = explode('/', $firstParts[0]);

            $Compteur_type_facture = $secondParts[0]; // "556"
            $Compteur_total = $secondParts[1]; // "655"
            $Code_type_facture = $firstParts[1]; // "FV"

            //dd($Compteur_type_facture, $Compteur_total, $Code_type_facture);


            $formattedDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->format('Y-m-d H:i:s');

            $priseEnCompteReglement = PrefixeReference::first()->prise_en_compte_reglement;
            if($priseEnCompteReglement == 1){

                $statutFacture = 'NORMALISEE';
            }else if($priseEnCompteReglement == 0){
                $statutFacture = 'SOLDE';

            }

            $facture = Facture::findorfail($factureId);

            $facture->Nim_machine = $nim;
            $facture->Compteur_type_facture = $Compteur_type_facture;
            $facture->Compteur_total = $Compteur_total;
            $facture->Code_type_facture = $Code_type_facture;
            $facture->Code_signature = $codeMECeFDGI;
            $facture->QrCode = $qrCode;
            $facture->Statut_facture = $statutFacture;
            $facture->Date_signature = $formattedDate;
            $facture->user_id = $user_connecterId;
            $facture->agence_id = $Agence_id;

            $facture->save();

            //total facture statut
            $totalfacture = TotalFacture::where('facture_id', $factureId)->first();
            $totalfacture->Statut = '1';
            $totalfacture->save();

            //info du client
            $infoClient = Client::where('id', $facture->client_id)->first();
            $nomClient = $infoClient->Denomination_sociale;
            $codeClient = $infoClient->Code_client;
            $ifuClient = $infoClient->Numero_ifu;
            $telephoneClient = $infoClient->Telephone_mobile;
            $adressClient = $infoClient->Adresse_client;

            $infoAgence = Agence::where('id', $facture->agence_id)->first();
            $nomAgence = $infoAgence->NomAgence;
            $adresseAgence = $infoAgence->adresseAgence;
            $telephoneAgence = implode('/', [$infoAgence->numero_telephone_1, $infoAgence->numero_telephone_2]);

            //Info marchand
            $infoMarchand = User::where('id', $user_connecterId)->first();
            $nomMarchand = $infoMarchand->name;
            $idMarchand = $infoMarchand->id;
            //table achivement facture
            $archiveFV = new ArchiveFacture();
            $archiveFV->facture_id = $factureId;
            $archiveFV->idFacture_originale  = $facture->idFacture_originale;
            $archiveFV->Reference_facture = $facture->Reference_facture;
            $archiveFV->Date_facture = $facture->Date_facture;
            $archiveFV->Modifie_le = $facture->Modifie_le;
            $archiveFV->Compteur_type_facture = $Compteur_type_facture;
            $archiveFV->Compteur_total = $Compteur_total;
            $archiveFV->Code_type_facture = $Code_type_facture;
            $archiveFV->Date_signature = $formattedDate;
            $archiveFV->Nim_machine = $nim;
            $archiveFV->Code_signature = $codeMECeFDGI;
            $archiveFV->QrCode = $qrCode;
            $archiveFV->Statut_facture = $facture->Statut_facture;
            $archiveFV->Aib = $facture->Aib;
            $archiveFV->Aib_deductible = $facture->Aib_deductible;
            $archiveFV->Commentaire = $facture->Commentaire;
            $archiveFV->Objet_facture = $facture->Objet_facture;
            $archiveFV->Validite = $facture->Validite;
            $archiveFV->user_id = $idMarchand;
            $archiveFV->Nom_user = $nomMarchand;
            $archiveFV->Code_client = $codeClient;
            $archiveFV->Nom_client = $nomClient;
            $archiveFV->Telephone_client = $telephoneClient;
            $archiveFV->Ifu_client = $ifuClient;
            $archiveFV->Net_a_payer = $facture->Net_a_payer;
            $archiveFV->Autres_infos = $facture->Autres_infos;
            $archiveFV->Numero_ifu_machine = $facture->Numero_ifu_machine;
            $archiveFV->agence_id = $Agence_id;
            $archiveFV->Nom_agence = $nomAgence;
            $archiveFV->Adresse_agence = $adresseAgence;
            $archiveFV->Telephone_agence = $telephoneAgence;
            $archiveFV->save();

            //archivage des ligne facture
            $idArchiveFV = $archiveFV->id;


            $ligneFacture = Lignefacture::join('factures', 'factures.id', '=', 'lignefactures.facture_id')
                ->join('stocks', 'stocks.id', '=', 'lignefactures.stocks_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'lignefactures.GroupeTaxe_id')
                ->join('produits','produits.id','=','stocks.Id_Produit')
                ->select('lignefactures.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('lignefactures.facture_id', '=', $factureId)
                ->get();

             foreach($ligneFacture as $ligne){
                 $archiveLigne = new ArchiveLigneFacture();
                 $archiveLigne->archive_factures_id = $idArchiveFV;
                 $archiveLigne->stocks_id = $ligne->stocks_id;
                 $archiveLigne->Produit_designation = $ligne->Designation;
                 $archiveLigne->GroupeTaxe_id = $ligne->GroupeTaxe_id;
                 $archiveLigne->Taux_remise = $ligne->Taux_remise;
                 $archiveLigne->Prix_unitaire_HT = $ligne->Prix_unitaire_HT;
                 $archiveLigne->Prix_revient = $ligne->Prix_revient;
                 $archiveLigne->Qte = $ligne->Qte;
                 $archiveLigne->is_emballage = $ligne->is_emballage;
                 $archiveLigne->save();
             }




            return redirect()->back()->with('success', 'Facture normalisée avec succès.');
        } else {
            // Gérer les erreurs de requête
            return response()->json(['error' => 'Erreur lors de la normalisation de la facture'], $response->status());
        }
    }
}
