<?php

namespace App\Http\Controllers\facturation;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use NumberFormatter;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Facture;
use Endroid\QrCode\QrCode;
use App\Models\Lignefacture;
use Illuminate\Http\Request;
use App\Models\ArchiveFacture;
use App\Models\DetailProforma;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;

use App\Exports\AvoirPeriodeExport;
use App\Models\ArchiveLigneFacture;
use Endroid\QrCode\Builder\Builder;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacturePeriodeExport;
use Endroid\QrCode\Encoding\Encoding;
use App\Exports\ProformaPeriodeExport;
use App\Models\TotalFacture;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;

class documentController extends Controller
{


    public function generatePDFA4(Request $request, $id)
    {
        //try{
        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $infoFactureNonAchivee = Facture::where('id', $id)->first();
        $statutInfoFactureNonAchivee = $infoFactureNonAchivee->Statut_facture;
        if ($statutInfoFactureNonAchivee == 'EN COURS') {
            return to_route('facture')->with('error', 'La facture est en cours de traitement, elle ne peut pas être imprimée');
        }
        if ($statutInfoFactureNonAchivee == 'INVALIDEE') {
            return to_route('facture')->with('error', 'Cette facture est invalidée ,elle ne peut plus être imprimée');
        }

        $infoFacture = ArchiveFacture::where('facture_id', $id)->first();
        $statut = $infoFacture->Statut_facture;
        if ($statut == 'EN COURS') {
            return to_route('facture')->with('error', 'La facture est en cours de traitement, elle ne peut pas être imprimée');
        }
        if ($statut == 'INVALIDEE') {
            return to_route('facture')->with('error', 'Cette facture est invalidée ,elle ne peut plus être imprimée');
        }

        $infoTotal = TotalFacture::where('facture_id', $id)->first();

        //dd($infoTotal);


        $formatter = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
        $montantEnLettres = ucfirst($formatter->format($infoFacture->Net_a_payer));

        $qrData = $infoFacture->QrCode; // Assurez-vous que cette propriété contient les données que vous voulez encoder dans le code QR

        // Créer le code QR
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(100) // Taille du code QR
            ->margin(10) // Marge autour du code QR
            ->build();
        $ligneFacture = ArchiveLigneFacture::join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
            ->leftjoin('emballages', 'produits.Emballage_id', '=', 'emballages.id')
            ->select('archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation',
                     'archive_ligne_factures.Prix_revient', 'archive_ligne_factures.is_emballage','archive_ligne_factures.Taux_remise',
                     'archive_ligne_factures.Qte','emballages.Nom_emballage',
                     'produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe',
                     'groupe_taxations.Code_lettre', 'unite_comptages.Libelle as Unite_Comptage', DB::raw('SUM(archive_ligne_factures.Qte) as total_qte'))
            ->where('archive_ligne_factures.archive_factures_id', $infoFacture->id)
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation', 'archive_ligne_factures.Qte',
                    'archive_ligne_factures.Prix_revient', 'archive_ligne_factures.Taux_remise','archive_ligne_factures.is_emballage', 'emballages.Nom_emballage', 'unite_comptages.Libelle', 'groupe_taxations.valeur_taxe')
            ->get();
        $infoClient = Client::where('Code_client', $infoFacture->Code_client)->first();




        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $prefice = PrefixeReference::first();
        $prefix = $prefice->facture ?? '';
        $dateInstant = Carbon::now();

        Carbon::setLocale('fr');
        $Date_facture = Carbon::parse($infoFacture->Date_facture)->translatedFormat('d/m/Y à H:i:s');
        $Date_signature = Carbon::parse($infoFacture->Date_signature)->translatedFormat('d/m/Y H:i:s');

        $signataire = Agence::where('id', $infoFacture->agence_id)->first();



        if ($request->format == 'a4') {
            $htmlContent = view('page.facturation.facture.document.factureA4', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,
                'infoClient' => $infoClient,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'Date_facture' => $Date_facture,
                'Date_signature' => $Date_signature,
                'signataire' => $signataire,

            ])->render();
        }

        if ($request->format == 'a5') {
            $htmlContent = view('page.facturation.facture.document.factureA5', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,
                'infoClient' => $infoClient,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'Date_facture' => $Date_facture,
                'Date_signature' => $Date_signature,
                'signataire' => $signataire,

            ])->render();
        }
        if ($request->format == 'a8') {
            $htmlContent = view('page.facturation.facture.document.factureA8', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,
                'infoClient' => $infoClient,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'Date_facture' => $Date_facture,
                'Date_signature' => $Date_signature,
                'signataire' => $signataire,

            ])->render();
        }

        $dompdf->loadHtml($htmlContent);



        if ($request->format == 'a4') {
            $dompdf->setPaper('A4', 'portrait');
        }
        if ($request->format == 'a5') {
            $dompdf->setPaper('A5', 'portrait');
        }
        if ($request->format == 'a8') {
            $dompdf->setPaper([0, 0, 147, 210], 'portrait'); // Dimensions en points
            //$dompdf->setPaper('A8', 'portrait');
            // $dompdf->setPaper([0, 0, 0, 0], 'portrait');
        }

        $options->set('isHtmlHeaderFixed', true);
        $options->set('isHtmlFooterFixed', true);

        $dompdf->render();

        $dompdf->stream($prefix . '_' . $dateInstant, array("Attachment" => false));
        /*  } catch (Exception $e) {
        return redirect()->back()->with('error', "Une erreur s'est produite". $e->getMessage());
    } */
    }


    public function generatePDFAVOIRA4(Request $request, $id)
    {
        // try{
        $options = new Options();
        $options->set('chroot', realpath(''));
        $dompdf = new Dompdf($options);
        $infoFacture = ArchiveFacture::where('id', $id)->first();
        $statutfacture = $infoFacture->Statut_facture;

        if ($statutfacture == 'INVALIDEE') {
            return to_route('avoir')->with('error', "Cette facture avoir est invalidée ,elle ne peut pas etre imprimée");
        }
        if ($statutfacture == 'EN COURS') {
            return to_route('avoir')->with('error', 'La facture avoir est en cours de traitement');
        }
        $formatter = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
        $montantEnLettres = ucfirst($formatter->format($infoFacture->Net_a_payer));

        $qrData = $infoFacture->QrCode; // Assurez-vous que cette propriété contient les données que vous voulez encoder dans le code QR



        // Créer le code QR
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(100) // Taille du code QR
            ->margin(10) // Marge autour du code QR
            ->build();
        $ancienFacture = ArchiveFacture::where('facture_id', $infoFacture->idFacture_originale)->first();

        $infoTotal = TotalFacture::where('facture_id', $infoFacture->facture_id)->first();
        //dd( $infoTotal);

        $reference_ancienneFacture = $ancienFacture->Code_signature;
        $numero_ancienne_facture = $ancienFacture->Reference_facture;

        $ligneFacture = ArchiveLigneFacture::join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
            ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
            ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
            ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
            ->select('archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation', 'produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'unite_comptages.Libelle as Unite_Comptage', DB::raw('SUM(archive_ligne_factures.Qte) as total_qte'))
            ->where('archive_ligne_factures.archive_factures_id', $ancienFacture->id)
            ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.Code_lettre', 'archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation', 'unite_comptages.Libelle', 'groupe_taxations.valeur_taxe')
            ->get();
        $infoClient = Client::where('Code_client', $infoFacture->Code_client)->first();
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();


        $prefice = PrefixeReference::first();
        $prefix = $prefice->avoir ?? '';
        $dateInstant = Carbon::now();
        Carbon::setLocale('fr');


        $signataire = Agence::where('id', $infoFacture->agence_id)->first();

        if ($request->format == 'a4') {
            $dompdf->loadHtml(view('page.facturation.facture.document.factureA4', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,
                'reference_ancienneFacture' => $reference_ancienneFacture,
                'numero_ancienne_facture' => $numero_ancienne_facture,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'infoClient' => $infoClient,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'signataire' => $signataire

            ])->render());
        }
        if ($request->format == 'a5') {
            $dompdf->loadHtml(view('page.facturation.facture.document.factureA5', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,

                'reference_ancienneFacture' => $reference_ancienneFacture,
                'numero_ancienne_facture' => $numero_ancienne_facture,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'infoClient' => $infoClient,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'signataire' => $signataire

            ])->render());
        }
        if ($request->format == 'a8') {
            $dompdf->loadHtml(view('page.facturation.facture.document.factureA8', [
                'infoFacture' => $infoFacture,
                'infoTotal' => $infoTotal,

                'reference_ancienneFacture' => $reference_ancienneFacture,
                'numero_ancienne_facture' => $numero_ancienne_facture,
                'lignefacture' => $ligneFacture,
                'qrCode' => $qrCode,
                'infoClient' => $infoClient,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'signataire' => $signataire

            ])->render());
        }
        // (Optional) Setup the paper size and orientation
        if ($request->format == 'a4') {
            $dompdf->setPaper('A4', 'portrait');
        }
        if ($request->format == 'a5') {
            $dompdf->setPaper('A5', 'portrait');
        }
        if ($request->format == 'a8') {
            $dompdf->setPaper([0, 0, 147, 210], 'portrait'); // Dimensions en points
        }
        $options->set('isHtmlHeaderFixed', true);
        $options->set('isHtmlFooterFixed', true);

        $dompdf->render();

        $dompdf->stream($prefix . '_' . $dateInstant, array("Attachment" => false));
        /*   } catch (Exception $e) {
        // Redirection avec message d'erreur
        return redirect()->back()->with('error', "Une erreur s'est produite");
    } */
    }

    public function bordereauPDF($id)
    {
        try {
            // reference the Dompdf namespace
            $options = new Options();
            $options->set('chroot', realpath(''));
            // instantiate and use the dompdf class


            $dompdf = new Dompdf($options);


            $infoFacture = ArchiveFacture::where('facture_id', $id)->first();
            // dd($infoFacture);
            $statut = $infoFacture->Statut_facture;
            if ($statut == 'EN COURS') {
                return to_route('facture')->with('error', 'La facture est en cours de traitement');
            }

            $ligneFacture = ArchiveLigneFacture::join('groupe_taxations', 'groupe_taxations.id', '=', 'archive_ligne_factures.GroupeTaxe_id')
                ->join('stocks', 'stocks.id', '=', 'archive_ligne_factures.stocks_id')
                ->join('produits', 'produits.id', '=', 'stocks.Id_Produit')
                ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
                ->select('archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation', 'produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'unite_comptages.Libelle as Unite_Comptage', DB::raw('SUM(archive_ligne_factures.Qte) as total_qte'))
                ->where('archive_ligne_factures.archive_factures_id', $infoFacture->id)
                ->groupBy('produits.id', 'produits.Reference', 'produits.Designation', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre', 'archive_ligne_factures.Prix_unitaire_HT', 'archive_ligne_factures.Produit_designation', 'unite_comptages.Libelle')
                ->get();
            //$infoClient = Client::where('Code_client', $infoFacture->Code_client)->first();
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            $dateInstant = Carbon::now();
            Carbon::setLocale('fr');

            $Date_facture = Carbon::parse($infoFacture->Date_facture)->translatedFormat('d/m/Y à H:i:s');

            $numFacture = $infoFacture->Reference_facture;
            $parts = explode('/', $numFacture);
            if (isset($parts[2]) && $parts[2] === 'FV'  || isset($parts[2]) && $parts[2] === 'EV') {
                $parts[2] = 'BL';
            }
            $num_BL = implode('/', $parts);

            $factureAvoir = ArchiveFacture::where('idFacture_originale', $id)->first();

            $htmlContent = view('page.facturation.facture.document.bordereaulivraison', [
                'infoFacture' => $infoFacture,
                // 'infoClient' => $infoClient,
                'lignefacture' => $ligneFacture,
                'imageEntetePied' => $imageEntetePied,
                'Date_facture' => $Date_facture,
                'num_BL' => $num_BL,
                'factureAvoir' => $factureAvoir

            ])->render();


            // Load HTML with header and footer
            $dompdf->loadHtml($htmlContent);



            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isHtmlHeaderFixed', true);
            $options->set('isHtmlFooterFixed', true);

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream('BL_' . $dateInstant, array("Attachment" => false));
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('error', "Le bordereau de la facture ne peut pas etre imprimée car la facture est en cours de traitement ");
        }
    }
    public function PDFProformaA4($id)
    {
        try {
            // reference the Dompdf namespace
            $options = new Options();
            $options->set('chroot', realpath(''));
            // instantiate and use the dompdf class
            $dompdf = new Dompdf($options);
            $infoFacture = Facture::where('id', $id)->first();
            Carbon::setLocale('fr');

            $Date_facture = Carbon::parse($infoFacture->Date_facture)->translatedFormat('d/m/Y à H:i:s');
            // dd($infoFacture);
            $formatter = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
            $montantEnLettres = ucfirst($formatter->format($infoFacture->Net_a_payer));

            $ligneFacture = DetailProforma::join('factures', 'factures.id', '=', 'detail_proformas.facture_id')
                ->join('produits', 'produits.id', '=', 'detail_proformas.produit_id')
                ->join('groupe_taxations', 'groupe_taxations.id', '=', 'detail_proformas.GroupeTaxe_id')
                ->join('unite_comptages', 'unite_comptages.id', '=', 'produits.Id_Unite_Comptage')
                ->select('detail_proformas.*', 'produits.Reference', 'produits.Designation', 'produits.Type', 'unite_comptages.Libelle as Unite_Comptage', 'groupe_taxations.valeur_taxe', 'groupe_taxations.Code_lettre')
                ->where('detail_proformas.facture_id', '=', $id)->get();

            $infoClient = Client::where('id', $infoFacture->client_id)->first();
            $infoAgence = Agence::where('id', $infoFacture->agence_id)->first();
            $infoVendeur = User::where('id', $infoFacture->user_id)->first();
            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            $prefice = PrefixeReference::first();
            $prefix = $prefice->proforma ?? '';
            $dateInstant = Carbon::now();
            $htmlContent = view('page.facturation.proforma.pDocument.proformaA4', [
                'includecss' => '<link rel="stylesheet" href="{{ asset("bootstrap-5.3.3-dist/css/bootstrap.min.css") }}">',
                'infoFacture' => $infoFacture,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence,
                'infoVendeur' => $infoVendeur,
                'lignefacture' => $ligneFacture,
                'montantEnLettres' => $montantEnLettres,
                'imageEntetePied' => $imageEntetePied,
                'Date_facture' => $Date_facture
            ])->render();


            // Load HTML with header and footer
            $dompdf->loadHtml($htmlContent);



            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isHtmlHeaderFixed', true);
            $options->set('isHtmlFooterFixed', true);

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream($prefix . '_' . $dateInstant, array("Attachment" => false));
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('error', "Une erreur s'est produite");
        }
    }

    public function proformaByPeriode(Request $request)
    {
        $this->authorize('imprimer-proforma');
        try {
            $data = $request->validate([
                'date_debut_periode' => 'required|date',
                'date_fin_periode' => 'required|date|after_or_equal:date_debut_periode',
                'client' => '',
                'agence' => 'required',
            ]);

            $dateDebut = $data['date_debut_periode'];
            $dateFin = $data['date_fin_periode'];
            //$dateFin = Carbon::parse($data['date_fin_periode'])->endOfDay();
            $client = $data['client'];
            $agence = $data['agence'];

            $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->select(
                    'factures.*',
                    'agences.NomAgence',
                    'clients.Code_client',
                    'clients.Denomination_sociale',
                    'users.name as user_name'
                )
                ->where('Code_type_facture', 'PR')
                ->whereBetween('factures.created_at', [$dateDebut, $dateFin]);

            if ($client !== 'Tous') {
                $query->where('factures.client_id', $client);
                $infoClient = Client::where('id', $client)->first();
            } else {
                $infoClient = Client::where('id', 0)->first();
            }

            if ($agence !== 'Tous') {
                $query->where('factures.agence_id', $agence);
                $infoAgence = Agence::where('id', $agence)->first();
            } else {
                $infoAgence = Agence::where('id', 0)->first();
            }

            $listeProforma = $query->get();

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            return response()->json([
                'listeProforma' => $listeProforma,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence, // Retourne également les infos de l'agence
                'imageEntetePied' => $imageEntetePied // Ajoute l'image si nécessaire
            ]);
        } catch (Exception $e) {
            // Gestion des erreurs
            return redirect()->back()->with('error', "Une erreur s'est produite");
        }
    }


    public function exportProformaPeriodeExcel(Request $request)
    {
        $this->authorize('consulter-factures');
        try {
            $data = $request->validate([

                'tableFactureParPeriodeData' => 'required|array',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoClient' => '',
                'infoAgence' => '',

            ]);
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new ProformaPeriodeExport($data, $texteEntetePied);

            $fileName = 'Proforma_periode_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function exportProformaPeriodePdf(Request $request)
    {
        // try {
        $data = $request->validate([
            'tableFactureParPeriodeData' => 'required|array',
            'dateDebut' => '',
            'dateFin' => '',
            'infoClient' => '',
            'infoAgence' => '',
        ]);




        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.facturation.proforma.pDocument.proformaPeriodeA4', [
            'data' => $data,
            'dateDebut' => $data['dateDebut'],
            'dateFin' => $data['dateFin'],
            'infoClient' => $data['infoClient'],
            'infoAgence' => $data['infoAgence'],

            'imageEntetePied' => $imageEntetePied,

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Liste_proforma_', ["Attachment" => false]);

        /*  } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        } */
    }


    public function imprimerlisteFacture(Request $request)
    {
        try {
            $data = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'client' => '',
                'statut' => 'array',
                'agence' => 'required',
            ]);

            $dateDebut = $data['date_debut'];
            //$dateFin = Carbon::parse($data['date_fin'])->endOfDay();
            $dateFin = $data['date_fin'];
            $client = $data['client'];
            $checkboxes = $data['statut'] ?? [];
            $agence = $data['agence'];

            $allStatuses = ['EN COURS', 'INVALIDEE', 'NORMALISEE', 'EN COURS DE REGLEMENT', 'SOLDE', 'ANNULEE'];
            $afficherToutesFactures = in_array('TOUS', $checkboxes);
            $statutsAUtiliser = $afficherToutesFactures ? $allStatuses : $checkboxes;

            // Liste des factures par statut
            $listeProformaParStatut = [];

            // Requête de base
            $queryBase = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->select(
                    'factures.id',
                    'factures.Reference_facture',
                    'factures.Date_facture',
                    'total_factures.Aib_facturee',
                    'total_factures.Aib_deductible',
                    'factures.Net_a_payer',
                    'agences.NomAgence',
                    'clients.Denomination_sociale',
                    'users.name as user_name',
                    DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                    DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA')
                )

                ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                ->where(function ($query) {
                    $query->where('factures.Code_type_facture', 'FV')
                        ->orWhere('factures.Code_type_facture', 'EV');
                })
                ->groupBy(
                    'factures.id',
                    'factures.Reference_facture',
                    'factures.Date_facture',
                    'total_factures.Aib_facturee',
                    'total_factures.Aib_deductible',
                    'factures.Net_a_payer',
                    'agences.NomAgence',
                    'clients.Denomination_sociale',
                    'users.name',
                    'total_factures.facture_id',
                    'factures.idFacture_originale'
                );

            // Si un client est spécifié
            if ($client != 'Tous') {
                $queryBase->where('factures.client_id', $client);
            }
            if ($agence != 'Tous') {
                $queryBase->where('factures.agence_id', $agence);
            }


            // Boucle à travers les statuts
            foreach ($statutsAUtiliser as $statut) {
                $listeProforma = clone $queryBase; // Clone la requête de base pour éviter les conflits
                $listeProforma = $listeProforma->where('factures.Statut_facture', $statut)->get();
                $listeProformaParStatut[$statut] = $listeProforma;
            }

            // Informations sur le client
            $infoClient = ($client != 'Tous') ? Client::where('id', $client)->first() : Client::where('id', 0)->first();
            $infoAgence = ($agence != 'Tous') ? Agence::where('id', $agence)->first() : Agence::where('id', 0)->first();

            return response()->json([
                'listeProformaParStatut' => $listeProformaParStatut,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => "Une erreur s'est produite", 'error' => $e->getMessage()], 500);
        }
    }

    public function exportFacturePeriode(Request $request)
    {
        $this->authorize('consulter-factures');
        try {
            $data = $request->validate([

                'tableFactureParPeriodeData' => 'required|array',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoClient' => '',
                'infoAgence' => '',

            ]);
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


            $export = new FacturePeriodeExport($data, $texteEntetePied);

            $fileName = 'Liste_facture_periode_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function exportFacturePeriodePdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableFactureParPeriodeData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoClient' => '',
                'infoAgence' => '',
            ]);



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.facturation.facture.document.imprimerlisteFacture', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'infoClient' => $data['infoClient'],
                'infoAgence' => $data['infoAgence'],

                'imageEntetePied' => $imageEntetePied,

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Liste_facture_', ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }


 /*    public function avoirByPeriode(Request $request)
    {
        try {
            $data = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'client' => '',
                'statut' => 'array',
                'agence' => '',
            ]);


            $dateDebut = $data['date_debut'];
            $dateFin = $data['date_fin'];
            $dateFin = Carbon::parse($data['date_fin'])->endOfDay();
            $client = $data['client'];
            $checkboxes = $data['statut'] ?? []; // Récupère les statuts sélectionnés, ou un tableau vide si aucun n'est sélectionné

            $agence = $data['agence'];

            // Déterminez si toutes les factures doivent être affichées
            $afficherToutesFactures = in_array('TOUS', $checkboxes);
            $allStatuses = ['EN COURS', 'INVALIDEE', 'NORMALISEE'];

            if ($client == 'Tous') {
                $listeProformaParStatut = [];

                if ($afficherToutesFactures) {
                    foreach ($allStatuses as $statut) {
                        $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                            ->join('clients', 'clients.id', '=', 'factures.client_id')
                            ->join('users', 'users.id', '=', 'factures.user_id')
                            ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                            ->leftJoin('factures as factures_origine', 'factures_origine.id', '=', 'factures.idFacture_originale')
                            ->select(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name as user_name',
                                DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                                DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA'),
                                'factures_origine.Reference_facture as Reference_origine'
                            )
                            ->where('factures.agence_id', $agence)
                            ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                            ->where('factures.Statut_facture', $statut)
                            ->where(function ($query) {
                                $query->where('factures.Code_type_facture', 'FA')
                                    ->orWhere('factures.Code_type_facture', 'EA');
                            })
                            ->groupBy(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name',
                                'total_factures.facture_id',
                                'factures_origine.Reference_facture'
                            )
                            ->get();

                        $listeProformaParStatut[$statut] = $listeProforma;
                    }
                } else {
                    foreach ($checkboxes as $statut) {
                        $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                            ->join('clients', 'clients.id', '=', 'factures.client_id')
                            ->join('users', 'users.id', '=', 'factures.user_id')
                            ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                            ->leftJoin('factures as factures_origine', 'factures_origine.id', '=', 'factures.idFacture_originale')
                            ->select(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name as user_name',
                                DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                                DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA'),
                                'factures_origine.Reference_facture as Reference_origine'
                            )
                            ->where('factures.agence_id', $agence)
                            ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                            ->where('factures.Statut_facture', $statut)
                            ->where(function ($query) {
                                $query->where('factures.Code_type_facture', 'FA')
                                    ->orWhere('factures.Code_type_facture', 'EA');
                            })
                            ->groupBy(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name',
                                'total_factures.facture_id',
                                'factures_origine.Reference_facture'
                            )
                            ->get();

                        $listeProformaParStatut[$statut] = $listeProforma;
                    }
                }

                $infoClient = Client::where('id', 0)->first();
            } else {
                $listeProformaParStatut = [];

                if ($afficherToutesFactures) {
                    foreach ($allStatuses as $statut) {
                        $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                            ->join('clients', 'clients.id', '=', 'factures.client_id')
                            ->join('users', 'users.id', '=', 'factures.user_id')
                            ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                            ->leftJoin('factures as factures_origine', 'factures_origine.id', '=', 'factures.idFacture_originale')
                            ->select(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name as user_name',
                                DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                                DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA'),
                                'factures_origine.Reference_facture as Reference_origine'
                            )
                            ->where('factures.agence_id', $agence)
                            ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                            ->where('factures.Statut_facture', $statut)
                            ->where(function ($query) {
                                $query->where('factures.Code_type_facture', 'FA')
                                    ->orWhere('factures.Code_type_facture', 'EA');
                            })
                            ->where('factures.client_id', $client)
                            ->groupBy(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name',
                                'total_factures.facture_id',
                                'factures_origine.Reference_facture'
                            )
                            ->get();

                        $listeProformaParStatut[$statut] = $listeProforma;
                    }
                } else {
                    foreach ($checkboxes as $statut) {
                        $listeProforma = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                            ->join('clients', 'clients.id', '=', 'factures.client_id')
                            ->join('users', 'users.id', '=', 'factures.user_id')
                            ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                            ->leftJoin('factures as factures_origine', 'factures_origine.id', '=', 'factures.idFacture_originale')
                            ->select(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name as user_name',
                                DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                                DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D ) as TotalGlobalTVA'),
                                'factures_origine.Reference_facture as Reference_origine'
                            )
                            ->where('factures.agence_id', $agence)
                            ->where('factures.client_id', $client)
                            ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                            ->where('factures.Statut_facture', $statut)
                            ->where(function ($query) {
                                $query->where('factures.Code_type_facture', 'FA')
                                    ->orWhere('factures.Code_type_facture', 'EA');
                            })
                            ->where('factures.client_id', $client)
                            ->groupBy(
                                'factures.id',
                                'factures.Reference_facture',
                                'factures.Date_facture',
                                'factures.Aib',
                                'factures.Aib_deductible',
                                'factures.Net_a_payer',
                                'agences.NomAgence',
                                'clients.Denomination_sociale',
                                'users.name',
                                'total_factures.facture_id',
                                'factures_origine.Reference_facture'
                            )
                            ->get();

                        $listeProformaParStatut[$statut] = $listeProforma;
                    }
                }
                $infoClient = Client::where('id', $client)->first();
            }

            return response()->json([
                'listeProformaParStatut' => $listeProformaParStatut,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'infoClient' => $infoClient
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return response()->json(['message' => "Une erreur s'est produite", 'error' => $e->getMessage()], 500);
        }
    }
*/
    public function avoirByPeriode(Request $request)
    {
        try {
            // Validation des données du formulaire
            $data = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'client' => '',
                'statut' => 'array',
                'agence' => '',
            ]);

            // Récupération des paramètres
            $dateDebut = $data['date_debut'];
            $dateFin = $data['date_fin'];
            $client = $data['client'];
            $agence = $data['agence'];
            $checkboxes = $data['statut'] ?? [];
            $allStatuses = ['EN COURS', 'INVALIDEE', 'NORMALISEE'];

            // Déterminer si toutes les factures doivent être affichées
            $afficherToutesFactures = in_array('TOUS', $checkboxes);
            $statusesToQuery = $afficherToutesFactures ? $allStatuses : $checkboxes;

            // Requête de base
            $query = Facture::join('agences', 'agences.id', '=', 'factures.agence_id')
                ->join('clients', 'clients.id', '=', 'factures.client_id')
                ->join('users', 'users.id', '=', 'factures.user_id')
                ->join('total_factures', 'total_factures.facture_id', '=', 'factures.id')
                ->leftJoin('factures as factures_origine', 'factures_origine.id', '=', 'factures.idFacture_originale')
                ->select(
                    'factures.id',
                    'factures.Reference_facture',
                    'factures.Date_facture',
                    'factures.Aib',
                    'factures.Aib_deductible',
                    'factures.Net_a_payer',
                    'agences.NomAgence',
                    'clients.Denomination_sociale',
                    'users.name as user_name',
                    DB::raw('SUM(total_factures.TotalExoneree + total_factures.TotalHT_B + total_factures.TotalHT_C + total_factures.TotalHT_D + total_factures.TotalHT_E + total_factures.TotalHT_F) as TotalGlobalHT'),
                    DB::raw('SUM(total_factures.TotalTVA_B + total_factures.TotalTVA_D) as TotalGlobalTVA'),
                    'factures_origine.Reference_facture as Reference_origine',
                    'factures.Statut_facture'
                )

                ->whereBetween('factures.created_at', [$dateDebut, $dateFin])
                ->where(function ($query) {
                    $query->where('factures.Code_type_facture', 'FA')
                          ->orWhere('factures.Code_type_facture', 'EA');
                });

            // Filtrer par client s'il n'est pas "Tous"
            if ($client !== 'Tous') {
                $query->where('factures.client_id', $client);
            }
            if ($agence !== 'Tous') {
                $query->where('factures.agence_id', $agence);
            }

            // Filtrer par statut si des statuts sont fournis
            if (!empty($statusesToQuery)) {
                $query->whereIn('factures.Statut_facture', $statusesToQuery);
            }



            // Exécution de la requête avec regroupement par facture et par statut
            $listeProforma = $query->groupBy(
                'factures.id',
                'factures.Reference_facture',
                'factures.Date_facture',
                'factures.Aib',
                'factures.Aib_deductible',
                'factures.Net_a_payer',
                'agences.NomAgence',
                'clients.Denomination_sociale',
                'users.name',
                'total_factures.facture_id',
                'factures_origine.Reference_facture',
                'factures.Statut_facture'
            )->get()->groupBy('Statut_facture');

            // Récupération des informations sur le client
            $infoClient = $client === 'Tous' ? Client::where('id', 0)->first() : Client::where('id', $client)->first();
            $infoAgence = $agence === 'Tous' ? Agence::where('id', 0)->first() : Agence::where('id', $agence)->first();

            // Retour des données en réponse JSON
            return response()->json([
                'listeProformaParStatut' => $listeProforma,
                'dateDebut' => $dateDebut,
                'dateFin' => $data['date_fin'],
                'infoClient' => $infoClient,
                'infoAgence' => $infoAgence
            ]);
        } catch (Exception $e) {
            // Gestion des erreurs
            return response()->json(['message' => "Une erreur s'est produite", 'error' => $e->getMessage()], 500);
        }
    }


    public function exportFactureAvoirPeriodeExcel(Request $request)
    {
        // $this->authorize('consulter-factures');
        //  try {
        $data = $request->validate([

            'tableFactureParPeriodeData' => 'required|array',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoClient' => '',
            'infoAgence' => '',

        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $export = new AvoirPeriodeExport($data, $texteEntetePied);

        $fileName = 'Liste_FA_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        // Retourner le fichier pour téléchargement direct
        return Excel::download($export, $fileName);
        /*   } catch (Exception $e) {
                // Redirection avec message d'erreur
                return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
            } */
    }
    public function exportFactureAvoirPeriodePdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableFactureParPeriodeData' => 'required|array',
                'dateDebut' => '',
                'dateFin' => '',
                'infoClient' => '',
                'infoAgence' => '',
            ]);



            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.facturation.avoir.document.imprimerAvoirByPeriode', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'infoClient' => $data['infoClient'],
                'infoAgence' => $data['infoAgence'],

                'imageEntetePied' => $imageEntetePied,

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Liste_facture_', ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }
}
