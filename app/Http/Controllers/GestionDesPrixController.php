<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agence;
use App\Models\CategorieClient;
use App\Models\PrixVenteProduit;
use App\Models\HistoriquePrixProduit;
use App\Models\Produit;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PrixProduitsImport;


class GestionDesPrixController extends Controller
{
    //

    public function importPrixProduitsViaGestion(Request $request)
    {
        // Valider le fichier avant de traiter
        $request->validate([
            'uploadedData' => 'required|mimes:xlsx,xls',
        ]);

        // Traiter le fichier Excel
        Excel::import(new PrixProduitsImport, $request->file('uploadedData'));

        // Rediriger avec un message de succès
        return back()->with('success', 'Les prix ont été importés avec succès.');
    }

    public function gestion_des_prix()
    {
        $this->authorize('consulter-prix-vente');
        $site_id =session()->get('site_id');
        $produits = Produit::orderBy('id', 'desc')->where('Statut', '=', 'ACTIF')->where('Type', '=', 'PRODUIT')->get();

        $prix_ventes = DB::table('prix_vente_produits')
            ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
            ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
            ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
            ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
            ->select('prix_vente_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as nom_categorie_client', 'categorie_clients.Libelle', 'agences.NomAgence')
            ->where('prix_vente_produits.prix', '!=', 0)
            ->where('agences.id', '=', $site_id)
            ->orderBy('produits.id', 'desc')
            ->get();
        // dd($prix_ventes);
        // dd($historique_prix_revients);
        // $agence = Agence::orderBy('id', 'desc')->get();

        // $agence = (session()->get('site_id') == 1) ? Agence::orderBy('id', 'desc')->get() : Agence::where('id', session()->get('site_id'))->get();

        if(session()->get('site_id') === 1){
            $agence = Agence::orderBy('id', 'desc')->get();
        }else{
            $agence = Agence::where('id', '=', session()->get('site_id'))->get();
        }


        $categorie_client = CategorieClient::orderBy('id', 'desc')->get();



        return view('page.produit.gestion_prix.gestion_des_prix', [
            'produits' => $produits,
            'prix_ventes' => $prix_ventes,
            'categorie_client' => $categorie_client,
            'agence' => $agence,
        ]);
    }

    public function filterProduits(Request $request)
    {
        try {

            $site_id =session()->get('site_id');            // $liste_produits = Produit::orderBy('id', 'desc')->where('Statut', '=', 'ACTIF')->where('Type', '=', 'PRODUIT')->get();

            $produits = DB::table('prix_vente_produits')
                ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
                ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
                ->where('prix_vente_produits.agence_id', $site_id);
                ;
            // dd($produits);
            if ($request->produit) {
                $produits->where('produits.Reference', $request->produit);
            }

            if ($request->categorie_client) {
                $categorie = explode('-', $request->categorie_client);
                $produits->where('prix_vente_produits.categorie_client_id', $categorie[0]);
            }

            if ($request->agence) {
                $agence = explode('-', $request->agence);
                $produits->where('prix_vente_produits.agence_id', $agence[0]);
            }

            $result = $produits->select('prix_vente_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as nom_categorie_client', 'categorie_clients.Libelle', 'agences.NomAgence')->get();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function filterListeProduits(Request $request)
    {
        try {
            $site_id =session()->get('site_id');

            $produits = DB::table('prix_vente_produits')
                ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
                ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id');

            // dd($produits);
            if ($request->produit) {
                $produits->where('produits.Reference', $request->produit);
            }

            if ($request->categorie_client) {
                $categorie = explode('-', $request->categorie_client);
                $produits->where('prix_vente_produits.categorie_client_id', $categorie[0]);
            }

            if ($request->agence) {
                $agence = explode('-', $request->agence);
                $produits->where('prix_vente_produits.agence_id', $agence[0]);
            }

            $result = $produits->select('prix_vente_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as nom_categorie_client', 'categorie_clients.Libelle', 'agences.NomAgence')
            ->where('prix_vente_produits.prix', '!=', 0)
            ->where('agences.id', '=', $site_id)
            ->get();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function historiquePrixProduits(Request $request)
    {
        try {

            $produits = DB::table('historique_prix_produits')
                ->join('produits', 'historique_prix_produits.produit_id', '=', 'produits.id')
                ->join('categorie_clients', 'historique_prix_produits.categorie_client_id', '=', 'categorie_clients.id')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('agences', 'historique_prix_produits.agence_id', '=', 'agences.id');
            // dd($produits);
            if ($request->produit) {
                $produits->where('produits.Reference', $request->produit);
            }

            if ($request->categorie_client) {
                $categorie = explode('-', $request->categorie_client);
                $produits->where('historique_prix_produits.categorie_client_id', $categorie[0]);
            }

            if ($request->agence) {
                $agence = explode('-', $request->agence);
                $produits->where('historique_prix_produits.agence_id', $agence[0]);
            }

            $result = $produits->select('historique_prix_produits.*', 'produits.Reference', 'produits.Designation', 'categorie_produits.Libelle as nom_categorie_client', 'categorie_clients.Libelle', 'agences.NomAgence')->get();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePrixProduits(Request $request)
    {
        try {
            foreach ($request->produits as $produit) {
                if ($produit['nouveau_prix'] !== null) {
                    DB::table('prix_vente_produits')
                        ->join('produits', 'prix_vente_produits.produit_id', '=', 'produits.id')
                        ->join('categorie_clients', 'prix_vente_produits.categorie_client_id', '=', 'categorie_clients.id')
                        ->join('agences', 'prix_vente_produits.agence_id', '=', 'agences.id')
                        ->where('produits.Reference', $produit['reference'])
                        ->where('categorie_clients.Libelle', $produit['categorie_client'])
                        ->where('agences.NomAgence', $produit['agence'])
                        ->update(['prix' => $produit['nouveau_prix']]);


                    $produitSelect = Produit::where('produits.Reference', $produit['reference'])->first();
                    $categorieClientSelect = CategorieClient::where('categorie_clients.Libelle', $produit['categorie_client'])->first();
                    $agenceSelect = Agence::where('agences.NomAgence', $produit['agence'])->first();
                    $historique_prix_produit = new HistoriquePrixProduit;
                    $historique_prix_produit->produit_id = $produitSelect->id;
                    $historique_prix_produit->categorie_client_id = $categorieClientSelect->id;
                    $historique_prix_produit->agence_id = $agenceSelect->id;
                    $historique_prix_produit->date_changement_prix = now();
                    $historique_prix_produit->prix = $produit['nouveau_prix'];
                    $historique_prix_produit->user_id = auth()->user()->id;
                    $historique_prix_produit->save();
                }
            }

            return response()->json(['success' => 'Les prix ont été mis à jour avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
