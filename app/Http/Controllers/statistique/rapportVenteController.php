<?php

namespace App\Http\Controllers\statistique;

use Exception;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Produit;
use App\Models\AgenceUser;
use App\Models\Fournisseur;
use App\Models\Lignefacture;
use Illuminate\Http\Request;
use App\Models\StockHistories;
use App\Models\CategorieProduit;
use Illuminate\Support\Facades\DB;
use App\Exports\MargeParJourExport;
use App\Exports\MargeParMoisExport;
use App\Exports\RapportVenteExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MargeParClientExport;
use App\Models\HistoriqueVPrestation;
use App\Exports\MargeParProduitExport;
use App\Exports\RapportVenteExportSansMarge;
use App\Exports\RapportVentePresExport;

class rapportVenteController extends Controller
{
    //
    public function listeRapport()
    {
        $this->authorize('voir-rapport-statistique');

        try {
            $client = Client::all();
            $produit = Produit::all();
            $categorie = CategorieProduit::all();
            $fournisseur = Fournisseur::all();
            $prestation = Produit::where('Type', 'prestation')->get();

            // $agenceIdUserConnect = AgenceUser::where('user_id', auth()->user()->id)->pluck('agence_id')->toArray();
            // $listeAgence = Agence::whereIn('id', $agenceIdUserConnect)->get();

            $listeAgence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

            return view(
                'page.statistique.rapport.rapport',
                [
                    'client' => $client,
                    'produit' => $produit,
                    'prestation' => $prestation,
                    'categorie' => $categorie,
                    'fournisseur' => $fournisseur,
                    'listeAgence' => $listeAgence
                ]

            );
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    /*     public function rapportVente(Request $request)
    {

        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'agence' => 'required',
        ]);


        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];

        $produit = $data['produit'];
        $agence = $data['agence'];
        $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dateDebut . ' -1 day')); // Date de la veille



          $listeVente = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->leftJoin(DB::raw('(SELECT Id_Produit, SUM(Quantite) as TotalEntree FROM stock_histories WHERE type_operation = "ENTREE" GROUP BY Id_Produit) as total_entree'), function($join) {
                $join->on('total_entree.Id_Produit', '=', 'stock_histories.Id_Produit');
            })
            ->select(
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('SUM(stock_histories.Quantite) as Quantite'),
                DB::raw('SUM(stock_histories.Prix_vente) as Prix_vente'),
                DB::raw('SUM(stock_histories.Prix_achat) as Prix_achat'),
                DB::raw('IFNULL(total_entree.TotalEntree, 0) as TotalEntree') // Total des entrées par produit
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->where('stock_histories.type_operation', 'FACTURE_V')
            ->where('stock_histories.agence_id', $agence)
            ->groupBy(
                'stock_histories.Id_Produit', // Ajout de cette colonne pour satisfaire le GROUP BY
                'produits.id',
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle',
                'total_entree.TotalEntree'
            );

        if ($produit !== 'Tous') {
            $listeVente->where('stock_histories.Id_Produit', $produit);

            $operations_anterieures = DB::table('stock_histories')
            ->where('Id_Produit', $produit)
            ->where('agence_id', $agence)
            ->where('Date', '<=', $date_veille_debut)
            ->orderBy('Date', 'asc') // On parcourt les opérations dans l'ordre chronologique
            ->get();

        // Initialisation du solde
        $solde_initial = 0;

        // Parcourir les opérations pour calculer le solde
        foreach ($operations_anterieures as $operation) {
            if ($operation->operation === 'IMPORTATION_STOCK') {
                // Réinitialiser le solde à la quantité de l'importation de stock
                $solde_initial = $operation->Quantite;
            } elseif ($operation->operation === 'INVENTAIRE') {
                // Réinitialiser le solde à la quantité spécifiée par l'inventaire
                $solde_initial = $operation->Quantite;
            } elseif ($operation->operation === 'ENTREE') {
                // Ajouter la quantité pour les entrées
                $solde_initial += $operation->Quantite;
            } elseif ($operation->operation === 'SORTIE') {
                // Soustraire la quantité pour les sorties
                $solde_initial -= $operation->Quantite;
            }
        }

        // S'assurer qu'il y a un solde, sinon on initialise à 0
        if (is_null($solde_initial)) {
            $solde_initial = 0;
        }


        }

        $listeVente = $listeVente->get()->groupBy('Categorie_produit');
        $infoProduit = Produit::where('id', $produit)->first();
        $infoAgence = Agence::where('id', $agence)->first();

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoProduit' => $infoProduit,
            'infoAgence' => $infoAgence
        ]);
    } */
    public function rapportVente(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $produit = $data['produit'];
        $agence = $data['agence'];
        $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dateDebut . ' -1 day'));

        // Requête principale
        $listeVente = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->leftJoin(DB::raw('(SELECT Id_Produit, SUM(Quantite) as TotalEntree FROM stock_histories WHERE type_operation = "ENTREE" GROUP BY Id_Produit) as total_entree'), function ($join) {
                $join->on('total_entree.Id_Produit', '=', 'stock_histories.Id_Produit');
            })
            ->select(
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Quantite
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Quantite
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Quantite
                        ELSE 0
                    END
                ) as Quantite
                '),
                DB::raw('SUM(CASE WHEN stock_histories.type_operation IN ("SORTIE") THEN stock_histories.Quantite ELSE 0 END) as Quantite_Sorties'),
                DB::raw('SUM(CASE WHEN stock_histories.type_operation IN ("ENTREE") THEN stock_histories.Quantite ELSE 0 END) as Quantite_Entrees'),
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Prix_vente
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Prix_vente
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Prix_vente
                        ELSE 0
                    END
                ) as Prix_vente
                '),
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Prix_achat
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Prix_achat
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Prix_achat
                        ELSE 0
                    END
                ) as Prix_achat
                '),
                DB::raw('IFNULL(total_entree.TotalEntree, 0) as TotalEntree'),
                'stock_histories.Id_Produit'
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->whereIn('stock_histories.type_operation', ['SORTIE', 'FACTURE_V', 'FACTURE_A', 'ENTREE', 'FACTURE_INVALIDEE'])
            ->groupBy(
                'stock_histories.Id_Produit',
                'produits.id',
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle',
                'total_entree.TotalEntree'
            );

        if ($produit !== 'Tous') {
            $listeVente->where('stock_histories.Id_Produit', $produit);
        }

        if ($agence !== 'Toutes') {
            $listeVente->where('stock_histories.agence_id', $agence);
        }

        $resultats = $listeVente->get();

        // Ajouter les soldes initiaux
        /*         foreach ($resultats as $vente) {
            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit', $vente->Id_Produit)
                ->where('agence_id', $agence)
                ->where('created_at', '<=', $date_veille_debut)
                ->orderBy('created_at', 'asc')
                ->get();

            $solde_initial = 0;

            foreach ($operations_anterieures as $operation) {
                if ($operation->type_operation === 'IMPORTATION_STOCK') {
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->type_operation === 'INVENTAIRE') {
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->type_operation === 'ENTREE') {
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->type_operation === 'SORTIE') {
                    $solde_initial -= $operation->Quantite;
                }
            }

            // Ajouter le solde initial aux résultats
            $vente->SoldeInitial = $solde_initial;
        } */
        foreach ($resultats as $vente) {
            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit',  $vente->Id_Produit)
                ->where('agence_id', $agence)
                ->where('Date', '<=', $date_veille_debut)
                ->orderBy('Date', 'asc') // On parcourt les opérations dans l'ordre chronologique
                ->get();


            // Initialisation du solde
            $solde_initial = 0;

            // Parcourir les opérations pour calculer le solde
            foreach ($operations_anterieures as $operation) {
                if ($operation->operation === 'IMPORTATION_STOCK') {
                    // Réinitialiser le solde à la quantité de l'importation de stock
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'INVENTAIRE') {
                    // Réinitialiser le solde à la quantité spécifiée par l'inventaire
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'ENTREE') {
                    // Ajouter la quantité pour les entrées
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->operation === 'SORTIE') {
                    // Soustraire la quantité pour les sorties
                    $solde_initial -= $operation->Quantite;
                }
            }
            $vente->SoldeInitial = $solde_initial;
            //dd($vente->SoldeInitial);
        }
        //dd($date_veille_debut);

        $listeVente = $resultats->groupBy('Categorie_produit');

        $infoProduit = $produit !== 'Tous' ? Produit::find($produit) : null;
        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        //dd($listeVente);

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoProduit' => $infoProduit,
            'infoAgence' => $infoAgence
        ]);
    }



    public function export_excel_rapport_vente(Request $request)
    {
        $data = $request->validate([
            'tableMargeParProduitData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoProduit' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $export = new RapportVenteExport($data, $texteEntetePied);

        $fileName = 'Rapport_vente_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download($export, $fileName);
    }
    public function rapportVenteSansMarge(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $produit = $data['produit'];
        $agence = $data['agence'];
        $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dateDebut . ' -1 day'));

        // Requête principale
        $listeVente = StockHistories::join('produits', 'produits.id', '=', 'stock_histories.Id_Produit')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->leftJoin(DB::raw('(SELECT Id_Produit, SUM(Quantite) as TotalEntree FROM stock_histories WHERE type_operation = "ENTREE" GROUP BY Id_Produit) as total_entree'), function ($join) {
                $join->on('total_entree.Id_Produit', '=', 'stock_histories.Id_Produit');
            })
            ->select(
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Quantite
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Quantite
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Quantite
                        ELSE 0
                    END
                ) as Quantite
                '),
                DB::raw('SUM(CASE WHEN stock_histories.type_operation IN ("SORTIE") THEN stock_histories.Quantite ELSE 0 END) as Quantite_Sorties'),
                DB::raw('SUM(CASE WHEN stock_histories.type_operation IN ("ENTREE") THEN stock_histories.Quantite ELSE 0 END) as Quantite_Entrees'),
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Prix_vente
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Prix_vente
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Prix_vente
                        ELSE 0
                    END
                ) as Prix_vente
                '),
                DB::raw('
                SUM(
                    CASE
                        WHEN stock_histories.type_operation = "FACTURE_V" THEN stock_histories.Prix_achat
                        WHEN stock_histories.type_operation = "FACTURE_A" THEN -stock_histories.Prix_achat
                        WHEN stock_histories.type_operation = "FACTURE_INVALIDEE" THEN -stock_histories.Prix_achat
                        ELSE 0
                    END
                ) as Prix_achat
                '),
                DB::raw('IFNULL(total_entree.TotalEntree, 0) as TotalEntree'),
                'stock_histories.Id_Produit'
            )
            ->whereBetween('stock_histories.created_at', [$dateDebut, $dateFin])
            ->whereIn('stock_histories.type_operation', ['SORTIE', 'FACTURE_V', 'FACTURE_A', 'ENTREE', 'FACTURE_INVALIDEE'])
            ->groupBy(
                'stock_histories.Id_Produit',
                'produits.id',
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle',
                'total_entree.TotalEntree'
            );

        if ($produit !== 'Tous') {
            $listeVente->where('stock_histories.Id_Produit', $produit);
        }

        if ($agence !== 'Toutes') {
            $listeVente->where('stock_histories.agence_id', $agence);
        }

        $resultats = $listeVente->get();

        // Ajouter les soldes initiaux
 /*        foreach ($resultats as $vente) {
            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit', $vente->Id_Produit)
                ->where('agence_id', $agence)
                ->where('created_at', '<=', $date_veille_debut)
                ->orderBy('created_at', 'asc')
                ->get();

            $solde_initial = 0;

            foreach ($operations_anterieures as $operation) {
                if ($operation->type_operation === 'IMPORTATION_STOCK') {
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->type_operation === 'INVENTAIRE') {
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->type_operation === 'ENTREE') {
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->type_operation === 'SORTIE') {
                    $solde_initial -= $operation->Quantite;
                }
            }

            // Ajouter le solde initial aux résultats
            $vente->SoldeInitial = $solde_initial;
        } */
        foreach ($resultats as $vente) {
            $operations_anterieures = DB::table('stock_histories')
                ->where('Id_Produit',  $vente->Id_Produit)
                ->where('agence_id', $agence)
                ->where('Date', '<=', $date_veille_debut)
                ->orderBy('Date', 'asc') // On parcourt les opérations dans l'ordre chronologique
                ->get();


            // Initialisation du solde
            $solde_initial = 0;

            // Parcourir les opérations pour calculer le solde
            foreach ($operations_anterieures as $operation) {
                if ($operation->operation === 'IMPORTATION_STOCK') {
                    // Réinitialiser le solde à la quantité de l'importation de stock
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'INVENTAIRE') {
                    // Réinitialiser le solde à la quantité spécifiée par l'inventaire
                    $solde_initial = $operation->Quantite;
                } elseif ($operation->operation === 'ENTREE') {
                    // Ajouter la quantité pour les entrées
                    $solde_initial += $operation->Quantite;
                } elseif ($operation->operation === 'SORTIE') {
                    // Soustraire la quantité pour les sorties
                    $solde_initial -= $operation->Quantite;
                }
            }
            $vente->SoldeInitial = $solde_initial;
            //dd($vente->SoldeInitial);
        }

        $listeVente = $resultats->groupBy('Categorie_produit');

        $infoProduit = $produit !== 'Tous' ? Produit::find($produit) : null;
        $infoAgence = $agence !== 'Toutes' ? Agence::find($agence) : null;

        //dd($listeVente);

        return response()->json([
            'listeVente' => $listeVente,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoProduit' => $infoProduit,
            'infoAgence' => $infoAgence
        ]);
    }

    public function export_excel_rapport_vente_sans_marge(Request $request)
    {
        $data = $request->validate([
            'tableMargeParProduitData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoProduit' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $export = new RapportVenteExportSansMarge($data, $texteEntetePied);

        $fileName = 'Rapport_vente_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download($export, $fileName);
    }
    public function export_rapport_vente_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableMargeParProduitData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoProduit' => '',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.rapport.document.rapport_ventePdf', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoProduit' => $data['infoProduit'],
                'infoAgence' => $data['infoAgence'],

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Marge_par_produit' . now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function export_rapport_vente_sans_marge_pdf(Request $request)
    {
        // try {
        $data = $request->validate([
            'tableMargeParProduitData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoProduit' => '',
            'infoAgence' => '',
        ]);

        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $html = view('page.statistique.rapport.document.rapport_vente_sans_marge_Pdf', [
            'data' => $data,
            'dateDebut' => $data['dateDebut'],
            'dateFin' => $data['dateFin'],
            'imageEntetePied' => $imageEntetePied,
            'infoProduit' => $data['infoProduit'],
            'infoAgence' => $data['infoAgence'],

        ])->render();

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return $dompdf->stream('Marge_par_produit' . now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);

        //    } catch (Exception $e) {
        //         return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        //     }
    }

    public function rapportVentePrestation(Request $request)
    {
        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'prestation' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $prestation = $data['prestation'];
        $agence = $data['agence'];

        $listeVente = HistoriqueVPrestation::join('produits', 'produits.id', '=', 'historique_v_prestations.Id_Produit')
            ->join('categorie_produits', 'categorie_produits.id', '=', 'produits.Id_Categorie')
            ->leftJoin(DB::raw('(SELECT Id_Produit, SUM(Quantite) as TotalEntree FROM historique_v_prestations WHERE type_operation = "ENTREE" GROUP BY Id_Produit) as total_entree'), function ($join) {
                $join->on('total_entree.Id_Produit', '=', 'historique_v_prestations.Id_Produit');
            })
            ->select(
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle as Categorie_produit',
                DB::raw('
            SUM(
                CASE
                    WHEN historique_v_prestations.type_operation = "FACTURE_V" THEN historique_v_prestations.Quantite
                    WHEN historique_v_prestations.type_operation = "FACTURE_A" THEN -historique_v_prestations.Quantite
                    WHEN historique_v_prestations.type_operation = "FACTURE_INVALIDEE" THEN -historique_v_prestations.Quantite
                    ELSE 0
                END
            ) as Quantite
            '),
                DB::raw('
            SUM(
                CASE
                    WHEN historique_v_prestations.type_operation = "FACTURE_V" THEN historique_v_prestations.Prix_vente
                    WHEN historique_v_prestations.type_operation = "FACTURE_A" THEN -historique_v_prestations.Prix_vente
                    WHEN historique_v_prestations.type_operation = "FACTURE_INVALIDEE" THEN -historique_v_prestations.Prix_vente
                    ELSE 0
                END
            ) as Prix_vente
            '),

                DB::raw('IFNULL(total_entree.TotalEntree, 0) as TotalEntree'),
                'historique_v_prestations.Id_Produit'
            )
            ->whereBetween('historique_v_prestations.created_at', [$dateDebut, $dateFin])
            ->whereIn('historique_v_prestations.type_operation', ['FACTURE_V', 'FACTURE_A', 'FACTURE_INVALIDEE'])
            ->where('historique_v_prestations.agence_id', $agence)
            ->groupBy(
                'historique_v_prestations.Id_Produit',
                'produits.id',
                'produits.Reference',
                'produits.Designation',
                'categorie_produits.Libelle',
                'total_entree.TotalEntree'
            );

        if ($prestation !== 'Tous') {
            $listeVente->where('historique_v_prestations.Id_Produit', $prestation);
        }

        $resultats = $listeVente->get();

        //dd($resultats);



        $infoProduit = $prestation !== 'Tous' ? Produit::find($prestation) : null;
        $infoAgence = Agence::find($agence);

        return response()->json([
            'listeVente' => $resultats,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'infoProduit' => $infoProduit,
            'infoAgence' => $infoAgence
        ]);
    }
    public function export_excel_rapport_vente_prestation(Request $request)
    {
        $data = $request->validate([
            'tableRapportPrestationData' => 'required',
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'infoProduit' => '',
            'infoAgence' => '',
        ]);
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();


        $export = new RapportVentePresExport($data, $texteEntetePied);

        $fileName = 'Rapport_vente_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function export_rapport_vente_prestation_pdf(Request $request)
    {
        try {
            $data = $request->validate([
                'tableRapportPrestationData' => 'required',
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after_or_equal:dateDebut',
                'infoProduit' => '',
                'infoAgence' => '',
            ]);

            $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

            // Générer le contenu HTML
            $html = view('page.statistique.rapport.document.rapport_vente_presPdf', [
                'data' => $data,
                'dateDebut' => $data['dateDebut'],
                'dateFin' => $data['dateFin'],
                'imageEntetePied' => $imageEntetePied,
                'infoProduit' => $data['infoProduit'],
                'infoAgence' => $data['infoAgence'],

            ])->render();

            $options = new Options();
            $options->set('chroot', realpath(''));

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream('Rapport_vente_prestation_' . now()->format('Y_m_d_H_i_s'), ["Attachment" => false]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite. ");
        }
    }

    public function expot_rapport_agnce(Request $request)
    {
        dd('ici');

        $data = $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after_or_equal:dateDebut',
            'produit' => 'required',
            'agence' => 'required',
        ]);

        $dateDebut = $data['dateDebut'];
        $dateFin = $data['dateFin'];
        $produit = $data['produit'];
        $agence = $data['agence'];
        $date_veille_debut = date('Y-m-d 23:59:59', strtotime($dateDebut . ' -1 day'));
    }
}
