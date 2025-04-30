<?php

namespace App\Http\Controllers\accueil;

use Exception;
use App\Models\Fournisseur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\FournisseursExport;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;


class fournisseurController extends Controller
{
    public function listeFournisseur()
    {
        $this->authorize('consulter-liste-fournisseurs');
        try {
            $listeFournisseur = Fournisseur::all();
            return view('page.accueil.fournisseur.fournisseur', ['listeFournisseur' => $listeFournisseur]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function showForm()
    {
        $this->authorize('creer-fournisseur');
        try {
            return view('page.accueil.fournisseur.nouveau', ['fournisseur' => New Fournisseur]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeFournisseur(Request $request)
    {
        $this->authorize('creer-fournisseur');
        $request->validate([
            'DenominationSociale' => 'required',
            // // 'AdresseFournisseur' => 'required',
            // 'TelephoneFixe' => 'required',
            // 'TelephoneMobile' => 'required',
            // 'AdresseMail' => 'required',
            'Pays' => 'required',
            // 'NumeroIfu' => 'required|string|min:13|max:13',
            'Statut_fournisseur' => 'required',
        ]);

        $existingFournisseur = Fournisseur::where('DenominationSociale', $request->DenominationSociale)->first();

        if ($existingFournisseur) {
            return back()->with('error', "Ce fournisseur existe déjà.");
        }

        $user = Auth::user();
        $user_connecterId = $user->id;
        $Agence_id = session()->get('site_id');

        $ifu = $request->input('NumeroIfu');

        if ($ifu !== null && strlen($ifu) < 13) {
            return back()->with('error', "Le IFU doit contenir au moins 13 caractères.");
        }

        if(Str::length($ifu) > 1 ){

            $countIfu = Fournisseur::where('NumeroIfu', $ifu)->count();

            if ($countIfu >= 2) {
                return back()->with('error', "Ce numero IFU existe déjà  2 fois.");
            }

        }

        Fournisseur::create([
            'DenominationSociale' => $request->DenominationSociale,
            'AdresseFournisseur' => $request->AdresseFournisseur,
            'TelephoneFixe' => $request->TelephoneFixe,
            'TelephoneMobile' => $request->TelephoneMobile,
            'AdresseMail' => $request->AdresseMail,
            'Pays' => $request->Pays,
            'NumeroIfu' => $ifu,
            'Statut_fournisseur' => $request->Statut_fournisseur,
            'user_id' => $user_connecterId,
        ]);

        return to_route('fournisseur')->with('success', 'Fournisseur ajouté avec succès ');

    }
    public function editFournisseur(string $id)
    {
        $this->authorize('modifier-fournisseur');
        try {
            $fournisseur = Fournisseur::findorfail($id);
            return view('page.accueil.fournisseur.nouveau', ['fournisseur' => $fournisseur]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function updateFournisseur(Request $request, string $id)
    {

        $this->authorize('modifier-fournisseur');
        try {
            $data = $request->only(['DenominationSociale', 'AdresseFournisseur', 'TelephoneFixe', 'TelephoneMobile', 'AdresseMail', 'Pays', 'NumeroIfu', 'Statut_fournisseur']);

            $validatorRules = [
                'NumeroIfu' => 'nullable|string|min:13|max:13',
                'DenominationSociale' => 'required',
                'Statut_fournisseur' => 'required',
            ];
            $validationMessages = [

                'DenominationSociale.required' => "Le nom de la société est requise",
                'Statut_fournisseur.required' => "Le statut de la société est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                //return to_route('editFournisseur')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $DenominationSociale = $data['DenominationSociale'];
            $AdresseFournisseur = $data['AdresseFournisseur'];
            $TelephoneFixe = $data['TelephoneFixe'];
            $TelephoneMobile = $data['TelephoneMobile'];
            $AdresseMail = $data['AdresseMail'];
            $Pays = $data['Pays'];
            $NumeroIfu = $data['NumeroIfu'];
            $Statut_fournisseur = $data['Statut_fournisseur'];


            $fournisseurIfu = Fournisseur::findorfail($id);
            if($fournisseurIfu->NumeroIfu != $NumeroIfu){
                if(Str::length($NumeroIfu) > 1 ){
                    $countIfu = Fournisseur::where('NumeroIfu', $NumeroIfu)->count();
                    if ($countIfu >= 2) {
                        return back()->with('error', "Ce numero IFU existe déjà  2 fois.");
                    }

                }
            }


            $fournisseur = Fournisseur::findorfail($id);
            $fournisseur->DenominationSociale = $DenominationSociale;
            $fournisseur->AdresseFournisseur = $AdresseFournisseur;
            $fournisseur->TelephoneFixe = $TelephoneFixe;
            $fournisseur->TelephoneMobile = $TelephoneMobile;
            $fournisseur->AdresseMail = $AdresseMail;
            $fournisseur->Pays = $Pays;
            $fournisseur->NumeroIfu = $NumeroIfu;
            $fournisseur->Statut_fournisseur = $Statut_fournisseur;
            $fournisseur->update();

            return to_route('fournisseur')->with('success', 'Modification effectuée avec succès ');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function searchFournisseur(Request $request)
    {
        try {
            $query = $request->input('query');
            $listeFournisseur = Fournisseur::where('DenominationSociale', 'like', '%' . $query . '%')
                ->orwhere('AdresseMail', 'like', '%' . $query . '%')
                ->get();

            return view('page.accueil.fournisseur.searchFournisseur', ['listeFournisseur' => $listeFournisseur]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }
    public function export ()
    {
        try {
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();
        $data = Fournisseur::all();
        $export = new FournisseursExport($data, $texteEntetePied);

       $fileName = 'fournisseur_export' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download($export,$fileName);
    } catch (Exception $e) {
        // Redirection avec message d'erreur
        return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
    }

    }
    public function imprimer()
    {
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        $data = Fournisseur::all();

            $html = view('page.accueil.fournisseur.document.fournisseurimpression', [
               "data" => $data,
               "imageEntetePied"=> $imageEntetePied
            ])->render();

        $prefixe = 'fournisseur_impression';
        $date_et_heure = date('Ymd_His');
        $nom_pdf = $prefixe . '_' . $date_et_heure . '.pdf';

        $options = new Options();
        $options->set('chroot', realpath(''));

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream($nom_pdf, ["Attachment" => false]);

    }

}
