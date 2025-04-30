<?php

namespace App\Http\Controllers\parametre_administration;

use Exception;
use App\Models\Image;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\UniteComptage;
use App\Models\CategorieProduit;
use App\Models\PrefixeReference;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Parametre\entetePiedExcel;
use App\Http\Requests\UploadTemplate\entetePiedPageRequest;
use App\Http\Requests\Parametre\prefixeReferenceFormRequest;
use App\Http\Requests\UploadTemplate\entetePiedPageA5Request;
use App\Http\Requests\UploadTemplate\entetePiedPageA8Request;
use App\Models\ChoixSeuil;

class parametreController extends Controller
{
    public function index()
    {
        $this->authorize('parametres');
        $prefixeReference = PrefixeReference::first();
        $prefixeReferenceExists = PrefixeReference::exists();
        $imageA4 = Image::where('nom', 'entetePiedA4')->first();
        $imageA5 = Image::where('nom', 'entetePiedA5')->first();
        $imageA8 = Image::where('nom', 'entetePiedA8')->first();
        $entetePiedExcel = Image::where('nom', 'entetePiedExcel')->first();
        $choix_seuil = ChoixSeuil::first();

        return view('page.parametre_administration.parametre.parametre', [
            'prefixeReferenceExists' => $prefixeReferenceExists,
            'prefixeReference' => $prefixeReference,
            'imageA4' => $imageA4,
            'imageA5' => $imageA5,
            'imageA8' => $imageA8,
            'entetePiedExcel' => $entetePiedExcel,
            'active_tab' => 'parametre',
            'choix_seuil' => $choix_seuil,
        ]);
    }

    public function storePrefixeReference(prefixeReferenceFormRequest $request)
    {
        $this->authorize('parametres');
        $prefixeReferenceExists = PrefixeReference::exists();

        if ($prefixeReferenceExists) {
            $message = 'Prefixe de référence sauvegarder avec succès';
            PrefixeReference::first()->update($request->validated());
        }else{
            $message = 'Prefixe de référence enregistrer avec succès';
            PrefixeReference::create($request->validated());
        }

        return to_route('parametre', ['active_tab' => 'parametre'])->with('success', $message);
    }

    public function storeEntetePiedExcel(entetePiedExcel $request)
    {
        $this->authorize('parametres');

        $data = $request->validated();
        $data['nom'] = "entetePiedExcel";

        $entetePiedExcelExists = Image::where('nom', 'entetePiedExcel')->first();

        if ($entetePiedExcelExists) {
            $message = 'Entête et pied de page Excel sauvegarder avec succès';
            $entetePiedExcelExists->update($data);
        }else{
            $message = 'Entête et pied de page Excel enregistrer avec succès';
            Image::create($data);
        }

        return to_route('parametre', ['active_tab' => 'Excel'])->with('success', $message);
    }

    public function uploadEntetePiedA4(entetePiedPageRequest $request){
        $this->authorize('parametres');
        $data = $request->validated();

        $entete = $request->validated('entete');
        $pied = $request->validated('pied');

        if ($entete !== null && !$entete->getError() && $pied !== null && !$pied->getError()) {
            // Définir les chemins de destination
            $enteteFileName = time() . '_' . $entete->getClientOriginalName();
            $piedFileName = time() . '_' . $pied->getClientOriginalName();

            // Chemins publics relatifs
            $entetePath = 'entetePied/entetePiedA4/' . $enteteFileName;
            $piedPath = 'entetePied/entetePiedA4/' . $piedFileName;

            // Créer le répertoire s'il n'existe pas
            if (!file_exists(public_path('entetePiedA4'))) {
                mkdir(public_path('entetePiedA4'), 0777, true);
            }

            // Déplacer les fichiers
            $entete->move(public_path('entetePied/entetePiedA4'), $enteteFileName);
            $pied->move(public_path('entetePied/entetePiedA4'), $piedFileName);

            // Stocker les chemins dans $data
            $data['entete'] = $entetePath;
            $data['pied'] = $piedPath;
        }

        $entetePiedA4 = Image::where('nom', 'entetePiedA4')->first();

        if($entetePiedA4){
            $message = 'Entête et pied de page sauvegarder avec succès';
            if (file_exists(public_path($entetePiedA4->entete))) {
                unlink(public_path($entetePiedA4->entete));
            }
            if (file_exists(public_path($entetePiedA4->pied))) {
                unlink(public_path($entetePiedA4->pied));
            }
            $entetePiedA4->update($data);
        }else{
            $message = 'Entête et pied de page enregistrer avec succès';
            $data['nom'] = 'entetePiedA4';
            Image::create($data);
        }

        return to_route('parametre', ['active_tab' => 'A4'])->with('success', $message);
    }

    public function uploadEntetePiedA5(entetePiedPageA5Request $request){
        $this->authorize('parametres');
        $data = $request->validated();

        $entete = $request->validated('entete');
        $pied = $request->validated('pied');

        if ($entete !== null && !$entete->getError() && $pied !== null && !$pied->getError()) {

            $enteteFileName = time() . '_' . $entete->getClientOriginalName();
            $piedFileName = time() . '_' . $pied->getClientOriginalName();

            // Chemins publics relatifs
            $entetePath = 'entetePied/entetePiedA5/' . $enteteFileName;
            $piedPath = 'entetePied/entetePiedA5/' . $piedFileName;

            // Créer le répertoire s'il n'existe pas
            if (!file_exists(public_path('entetePiedA5'))) {
                mkdir(public_path('entetePiedA5'), 0777, true);
            }

            // Déplacer les fichiers
            $entete->move(public_path('entetePied/entetePiedA5'), $enteteFileName);
            $pied->move(public_path('entetePied/entetePiedA5'), $piedFileName);

            // Stocker les chemins dans $data
            $data['entete'] = $entetePath;
            $data['pied'] = $piedPath;
        }

        $entetePiedA5 = Image::where('nom', 'entetePiedA5')->first();


        if($entetePiedA5){
            $message = 'Entête et pied de page sauvegarder avec succès';
            if (file_exists(public_path($entetePiedA5->entete))) {
                unlink(public_path($entetePiedA5->entete));
            }
            if (file_exists(public_path($entetePiedA5->pied))) {
                unlink(public_path($entetePiedA5->pied));
            }
            $entetePiedA5->update($data);
        }else{
            $message = 'Entête et pied de page enregistrer avec succès';
            $data['nom'] = 'entetePiedA5';
            Image::create($data);
        }

        return to_route('parametre', ['active_tab' => 'A5'])->with('success', $message);
    }

    public function uploadEntetePiedA8(entetePiedPageA8Request $request){
        $this->authorize('parametres');
        $data = $request->validated();

        $entete = $request->validated('entete');
        $pied = $request->validated('pied');

        if ($entete !== null && !$entete->getError() && $pied !== null && !$pied->getError()) {

            $enteteFileName = time() . '_' . $entete->getClientOriginalName();
            $piedFileName = time() . '_' . $pied->getClientOriginalName();

            // Chemins publics relatifs
            $entetePath = 'entetePied/entetePiedA8/' . $enteteFileName;
            $piedPath = 'entetePied/entetePiedA8/' . $piedFileName;

            // Créer le répertoire s'il n'existe pas
            if (!file_exists(public_path('entetePiedA8'))) {
                mkdir(public_path('entetePiedA8'), 0777, true);
            }

            // Déplacer les fichiers
            $entete->move(public_path('entetePied/entetePiedA8'), $enteteFileName);
            $pied->move(public_path('entetePied/entetePiedA8'), $piedFileName);

            // Stocker les chemins dans $data
            $data['entete'] = $entetePath;
            $data['pied'] = $piedPath;
        }

        $entetePiedA8 = Image::where('nom', 'entetePiedA8')->first();

        if($entetePiedA8){
            $message = 'Entête et pied de page sauvegarder avec succès';
            if (file_exists(public_path($entetePiedA8->entete))) {
                unlink(public_path($entetePiedA8->entete));
            }
            if (file_exists(public_path($entetePiedA8->pied))) {
                unlink(public_path($entetePiedA8->pied));
            }
            $entetePiedA8->update($data);
        }else{
            $message = 'Entête et pied de page enregistrer avec succès';
            $data['nom'] = 'entetePiedA8';
            Image::create($data);
        }

        return to_route('parametre', ['active_tab' => 'A8'])->with('success', $message);
    }

    public function taxe(){
        $this->authorize('voir-taxes');

        try {
            $produits = DB::table('produits')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->join('users', 'produits.Enregistrer_par', '=', 'users.id')
                ->select('produits.*', 'categorie_produits.Libelle as Libelle_categorie_produit', 'unite_comptages.Libelle', 'users.name')
                ->orderBy('produits.id', 'desc')
                ->where('produits.type', 'TAXE_SIMPLE')
                ->get();

            return view('page.parametre_administration.taxe.taxe', ['produits' => $produits]);
        } catch (Exception $e) {
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }

    }

    public function taxeNouveau(){
        $this->authorize('creer-taxe');

        $categorie_produits = CategorieProduit::orderBy('created_at', 'desc')->get();
        $unite_comptages = UniteComptage::orderBy('created_at', 'desc')->get();
        return view('page.parametre_administration.taxe.nouveau', [
            'categorie_produits' => $categorie_produits,
            'unite_comptages' => $unite_comptages,
            'produit' => new Produit
        ]);
    }

    public function editTaxe($id)
    {
        $this->authorize('modifier-taxe');
        try {
            $produit = DB::table('produits')
                ->join('categorie_produits', 'produits.Id_Categorie', '=', 'categorie_produits.id')
                ->join('unite_comptages', 'produits.Id_Unite_Comptage', '=', 'unite_comptages.id')
                ->select('produits.*', 'categorie_produits.Libelle as Libelle_categorie', 'unite_comptages.Libelle')
                ->where('produits.id', '=', $id)
                ->get();
            // dd($produit);
            $categorie_produits = CategorieProduit::orderBy('created_at', 'desc')->get();
            $unite_comptages = UniteComptage::orderBy('created_at', 'desc')->get();
            return view('page.parametre_administration.taxe.edit', [
                'produit' => $produit,
                'categorie_produits' => $categorie_produits,
                'unite_comptages' => $unite_comptages
            ]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function ChoixSeuilParametre(Request $request)
    {
        $reponse = $request->input('reponse');

        $choix_seuil = ChoixSeuil::first();

        if ($choix_seuil == null) {
            $new_choix_seuil = new ChoixSeuil();
            $new_choix_seuil->libelle = $reponse;
            $new_choix_seuil->save();
        } else {
            $choix_seuil->libelle = $reponse;
            $choix_seuil->update();
        }

        return to_route('parametre', ['active_tab' => 'seuil_stock'])->with('success', 'La seuil modifiée avec succès');
    }
}
