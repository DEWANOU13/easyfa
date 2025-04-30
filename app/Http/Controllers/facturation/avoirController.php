<?php

namespace App\Http\Controllers\facturation;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Stock;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use App\Models\AgenceUser;
use App\Models\TotalFacture;
use Illuminate\Http\Request;
use App\Models\ArchiveFacture;
use App\Models\GroupeTaxation;
use App\Models\StockHistories;
use App\Models\PrefixeReference;
use App\Models\ArchiveLigneFacture;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class avoirController extends Controller
{
    public function listeavoir()
    {
        $this->authorize('consulter-avoirs');

        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $annees = ArchiveFacture::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $listeProforma = ArchiveFacture::join('factures', 'factures.id', '=', 'archive_factures.idFacture_originale')
            ->select('archive_factures.*', 'factures.Reference_facture as reference_facture_origine')
            ->where('archive_factures.agence_id', '=', $Agence_id)
            ->where(function ($query) {
                $query->where('archive_factures.Code_type_facture', 'FA')
                    ->orWhere('archive_factures.Code_type_facture', 'EA');
            })
            ->whereMonth('factures.created_at', $currentMonth)
            ->whereYear('factures.created_at', $currentYear)
            ->orderby('archive_factures.created_at', 'desc')
            ->get();


        $listeClient = Client::all();

        $detailFactures = ArchiveLigneFacture::join('factures', 'factures.id', '=', 'archive_ligne_factures.archive_factures_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->select('archive_ligne_factures.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
            ->where('archive_ligne_factures.archive_factures_id', '=', 0)
            ->get();

        // $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
        // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();

        $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        return view('page.facturation.avoir.avoir', [
            'detailFactures' => $detailFactures,
            'listeProforma' => $listeProforma,
            'annees' => $annees,
            'listeClient' => $listeClient,
            'listeAgence' => $listeAgence
        ]);
    }

    public function storeFactureAvoir($factureId)
    {


        $this->authorize('creer-avoir');
        $siAvoirCreer = ArchiveFacture::where('idFacture_originale', $factureId)->where('Statut_facture', 'EN COURS')->first();
        if ($siAvoirCreer) {
            return redirect()->route('facture')->with('error', "Cette Facture dispose déjà d'une facture avoir");
        }
        $siAvoirCreer = ArchiveFacture::where('idFacture_originale', $factureId)->where('Statut_facture', 'INVALIDEE')->first();
        if ($siAvoirCreer) {
            return redirect()->route('facture')->with('info', [
                "message" => "Cette Facture dispose déjà d-une facture avoir qui a été invalidée. Voulez-vous créer la facture avoir à nouveau ?",
                "factureId" => $factureId
            ]);
        }



        $facture = Facture::where('id', $factureId)->first();
        if ($facture->Statut_facture != 'NORMALISEE') {
            return redirect()->route('facture')->with('error', 'Cette Facture ne peut pas être convertir en facture avoir , car le statut de la facture est: ' . $facture->Statut_facture);
        }
        //dd($facture->Statut_facture);

        $prefixe = PrefixeReference::first();
        $prefix = $prefixe->avoir ?? '';
        if ($prefix == null) {
            return redirect()->back()->with('warning', 'Veuillez configurer les prefixes de reference');
        }
        $user = Auth::user();
        $user_connecterId = $user->id;


        $Agence_id = session()->get('site_id');
        //$formattedDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->format('Y-m-d H:i:s');


        $ancienneFacture = Facture::findorfail($factureId);
        $archiveFacture = ArchiveFacture::where('facture_id', $factureId)->first();


        $lastDigitOfYear = substr(Carbon::now()->year, -2);

        // Obtenez le dernier numéro de référence enregistré
        $lastReference = Facture::where('Code_type_facture','FA')
        ->orwhere('Code_type_facture','EA')
        ->where('agence_id','=' ,session()->get('site_id'))
        ->count();

        // Si aucun numéro de référence n'a été enregistré auparavant, commencez par 1
        if ($lastReference === 0) {
            $incrementedReferenceNumber = '0000001';
        } else {
            // Obtenez le dernier numéro de référence et incrémentez-le
            $lastReference = Facture::where('Code_type_facture','FA') ->orwhere('Code_type_facture','EA')->where('agence_id','=' ,session()->get('site_id'))->orderBy('id', 'desc')->first();
            $lastReferenceNumber = substr($lastReference->Reference_facture, -7); // Obtenez les 7 derniers chiffres
            $incrementedReferenceNumber = str_pad($lastReferenceNumber + 1, 7, '0', STR_PAD_LEFT);

        }

        $Reference_facture = "$Agence_id/{$lastDigitOfYear}/{$prefix}/{$incrementedReferenceNumber}";
        $Date_facture =  Carbon::now();

        if ($archiveFacture->Code_type_facture == 'FV') {
            $Code_type_facture = 'FA';
        }
        if ($archiveFacture->Code_type_facture == 'EV') {
            $Code_type_facture = 'EA';
        }

        $facture = new Facture();
        $facture->Reference_facture = $Reference_facture;
        $facture->idFacture_originale = $ancienneFacture->id;
        $facture->Date_facture = $Date_facture;
        $facture->Code_type_facture = $Code_type_facture;
        $facture->Statut_facture = 'EN COURS';
        $facture->Aib = $archiveFacture->Aib;
        $facture->Aib_deductible = $archiveFacture->Aib_deductible;
        $facture->Commentaire = $archiveFacture->Commentaire;
        $facture->Objet_facture = $archiveFacture->Objet_facture;
        $facture->Code_signature = $archiveFacture->Code_signature;
        $facture->Validite = $archiveFacture->Validite;
        $facture->Net_a_payer = $archiveFacture->Net_a_payer;
        $facture->Autres_infos = $archiveFacture->Autres_infos;
        $facture->user_id = $user_connecterId;
        $facture->client_id = $ancienneFacture->client_id;
        $facture->agence_id = $Agence_id;
        $facture->save();

        $idNouvelFacture = $facture->id;

        $ligneTotalFV = TotalFacture::where('facture_id', $factureId)->first();

        $totalFacture = new TotalFacture();
        $totalFacture->facture_id = $idNouvelFacture;
        $totalFacture->Statut = '0';
        $totalFacture->TotalExoneree = $ligneTotalFV->TotalExoneree;
        $totalFacture->TotalHT_B = $ligneTotalFV->TotalHT_B;
        $totalFacture->TotalTVA_B = $ligneTotalFV->TotalTVA_B;
        $totalFacture->TotalHT_C = $ligneTotalFV->TotalHT_C;
        $totalFacture->TotalHT_D = $ligneTotalFV->TotalHT_D;
        $totalFacture->TotalTVA_D = $ligneTotalFV->TotalTVA_D;
        $totalFacture->TotalHT_E = $ligneTotalFV->TotalHT_E;
        $totalFacture->TotalHT_F = $ligneTotalFV->TotalHT_F;
        $totalFacture->Aib_facturee = $ligneTotalFV->Aib_facturee;
        $totalFacture->Aib_deductible = $ligneTotalFV->Aib_deductible;
        $totalFacture->save();

        //info du client
        $infoClient = Client::where('id', $facture->client_id)->first();
        $nomClient = $infoClient->Denomination_sociale;
        $codeClient = $infoClient->Code_client;
        $ifuClient = $infoClient->Numero_ifu;
        $telephoneClient = $infoClient->Telephone_mobile;
        $adressClient = $infoClient->Adresse_client;

        $infoAgence = Agence::where('id', $facture->agence_id)->first();
        $nomAgence = $infoAgence->NomAgence;
        $adresseAgence = $infoAgence->AdresseAgence;
        $telephoneAgence = implode('/', [$infoAgence->numero_telephone_1, $infoAgence->numero_telephone_2]);


        //Info marchand
        $infoMarchand = User::where('id', $user_connecterId)->first();
        $nomMarchand = $infoMarchand->name;
        $idMarchand = $infoMarchand->id;
        //table achivement facture
        $archiveFV = new ArchiveFacture();
        $archiveFV->facture_id = $idNouvelFacture;
        $archiveFV->idFacture_originale  = $factureId;
        $archiveFV->Reference_facture = $facture->Reference_facture;
        $archiveFV->Date_facture = $facture->Date_facture;
        $archiveFV->Code_signature = $facture->Code_signature;
        $archiveFV->Modifie_le = $facture->Modifie_le;
        $archiveFV->Code_type_facture = $Code_type_facture;
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

        /*  $ligneArchiveFacture = ArchiveLigneFacture::where('archive_factures_id', $archiveFacture->id)->get();
            if($ligneArchiveFacture->count() > 0){
                foreach ($ligneArchiveFacture as $ligne) {
                     // Récupère le stock associé à la ligne de facture qui n'est pas de type "PRESTATION"
                        $stock = Stock::join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                        ->where('stocks.id', $ligne->stocks_id)
                        ->where('produits.Type', '!=', 'PRESTATION')
                        ->first();

                    // Si un stock correspondant est trouvé, augmentez la quantité stockée
                    if ($stock) {
                    $stock->Qte_stockee += $ligne->Qte;
                    $stock->save();
                    }
                }
            } */
        return to_route('avoir')->with('success', 'Facture avoir créée avec succès.');
    }
    public function convertirEnAvoir($factureId)
{
    $this->authorize('creer-avoir');
    $siAvoirCreer = ArchiveFacture::where('idFacture_originale', $factureId)->where('Statut_facture', 'INVALIDEE')->first();
    $user_connecterId = auth()->user()->id;
    if ($siAvoirCreer) {
        $siAvoirCreer->Statut_facture = 'EN COURS';
        $siAvoirCreer->created_at = Carbon::now();
        $siAvoirCreer->modifier_par = $user_connecterId;
        $siAvoirCreer->update();

        $facture = Facture::where('idFacture_originale', $factureId)->first();
        $facture->Statut_facture = 'EN COURS';
        $facture->created_at = Carbon::now();
        $facture->modifier_par = $user_connecterId;
        $facture->update();

        return redirect()->route('avoir')->with('success', "La facture avoir a été crée avec succès.");
    } else {
        return redirect()->route('facture')->with('error', "Erreur lors de la création de la facture avoir.");
    }
}


    public function getDetailFactureAvoir($id)
    {
        $this->authorize('consulter-avoirs');
        $annees = Facture::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        $facturedeBase = ArchiveFacture::find($id);
        $idfactureOriginale = $facturedeBase->idFacture_originale;

        $factureOriginale = ArchiveFacture::where('facture_id', $idfactureOriginale)->first();

        $idFacOriginale = $factureOriginale->id;

        $listeProforma = ArchiveFacture::join('factures', 'factures.id', '=', 'archive_factures.idFacture_originale')
            ->select('archive_factures.*', 'factures.Reference_facture as reference_facture_origine')
            ->where(function ($query) {
                $query->where('archive_factures.Code_type_facture', 'FA')
                    ->orWhere('archive_factures.Code_type_facture', 'EA');
            })
            ->orderby('archive_factures.created_at', 'desc')
            ->paginate(20);

        $detailFactures = ArchiveLigneFacture::join('factures', 'factures.id', '=', 'archive_ligne_factures.archive_factures_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->leftjoin('emballages', 'emballages.id', '=', 'produits.Emballage_id')
            ->select('archive_ligne_factures.*', 'produits.Reference', 'produits.Designation',  'emballages.Reference as Reference_emballage','emballages.Nom_emballage',  'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
            ->where('archive_ligne_factures.archive_factures_id', '=', $idFacOriginale)
            ->get();

        $listeClient = Client::all();
        $listeAgence = Agence::all();
        $listeTaxe = GroupeTaxation::all();



        return view('page.facturation.avoir.avoir', [
            'detailFactures' => $detailFactures,
            'listeProforma' => $listeProforma,
            'listeClient' => $listeClient,
            'listeAgence' => $listeAgence,
            'listeTaxe' => $listeTaxe,
            'annees' => $annees

        ]);
    }

    public function filterFactureAvoir(Request $request)
    {
        $this->authorize('consulter-avoirs');
        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : null;
        $annee = $request->query('annee');

        // Vérification de l'utilisateur et de l'agence
        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');

        if (is_array(getIdAgenceByUser())) {
            $query = ArchiveFacture::join('factures', 'factures.id', '=', 'archive_factures.idFacture_originale')
                ->select('archive_factures.*', 'factures.Reference_facture as reference_facture_origine')
                ->where(function ($query) {
                    $query->where('archive_factures.Code_type_facture', 'FA')
                        ->orWhere('archive_factures.Code_type_facture', 'EA');
                });
        } else {
            $query = ArchiveFacture::join('factures', 'factures.id', '=', 'archive_factures.idFacture_originale')
                ->select('archive_factures.*', 'factures.Reference_facture as reference_facture_origine')
                ->where(function ($query) {
                    $query->where('archive_factures.Code_type_facture', 'FA')
                        ->orWhere('archive_factures.Code_type_facture', 'EA');
                })
                ->where('archive_factures.agence_id', '=', getIdAgenceByUser());
        }

        if ($annee) {
            $query->whereYear('archive_factures.created_at', $annee);
        }

        if ($month && !$startDate && !$endDate) {
            // Si seul le mois est fourni, appliquer le filtre par mois et année
            $query->whereMonth('archive_factures.created_at', $month);
            $query->whereYear('archive_factures.created_at', $annee ?? date('Y')); // Utiliser l'année courante si aucune n'est fournie
        }

        if ($startDate && $endDate && !$month && !$annee) {
            // Si une période (start_date et end_date) est fournie, ignorer le mois et l'année
            $query->where('archive_factures.created_at', '>=', $startDate)
                  ->where('archive_factures.created_at', '<=', $endDate);
        }
        $listeProforma = $query->get();

        $listeClient = Client::all();

        $detailFactures = ArchiveLigneFacture::join('factures', 'factures.id', '=', 'archive_ligne_factures.archive_factures_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->select('archive_ligne_factures.*', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
            ->where('archive_ligne_factures.archive_factures_id', '=', 0)
            ->get();

        // dd($detailFactures);
        $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
        $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();

        $annees = ArchiveFacture::selectRaw('YEAR(created_at) as annee')
            ->distinct()
            ->pluck('annee');

        return view('page.facturation.avoir.avoir', [
            'detailFactures' => $detailFactures,
            'listeProforma' => $listeProforma,
            'annees' => $annees,
            'listeClient' => $listeClient,
            'listeAgence' => $listeAgence
        ]);
    }

    public function invaliderAvoirFacture($factureId)
    {
        $this->authorize('invalider-avoir');
        /*     $facture = Facture::where('id', $factureId)->first();
            if ($facture->Statut_facture == 'INVALIDEE' || $facture->Statut_facture == 'ANNULEE' || $facture->Statut_facture == 'EN COURS' || $facture->Statut_facture == 'SOLDE' || $facture->Statut_facture == 'EN COURS DE REGLEMENT') {
                return redirect()->route('facture')->with('error', 'Cette Facture ne peut pas etre convertir en facture avoir');
        } */
        $siAvoirCreer = ArchiveFacture::where('id', $factureId)->first();
        $stattut = $siAvoirCreer->Statut_facture;
        $facture_id = $siAvoirCreer->facture_id;
        //dd($facture_id);

        if ( $stattut == 'EN COURS') {
            $siAvoirCreer->Statut_facture = 'INVALIDEE';
            $siAvoirCreer->update();

            $facture = Facture::where('id', $facture_id)->first();
            $facture->Statut_facture = 'INVALIDEE';
            $facture->update();
            return redirect()->route('avoir')->with('success', "Facture invalidée avec succès");
        }else
        {
            return redirect()->route('avoir')->with('error', "Cette Facture ne peut pas etre invalidée");
        }


    }
}
