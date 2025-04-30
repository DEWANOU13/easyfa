<?php

namespace App\Http\Controllers\Produit;

use App\Exports\UniteComptageExport;
use App\Http\Controllers\Controller;
use App\Models\UniteComptage;
use App\Models\Activity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

class UniteComptageController extends Controller
{
    // retourne la vue de unité de comptage
    public function  index()
    {
        $this->authorize('consulter-unite-comptage');

        try {
            $listeUniteComptage = DB::table('unite_comptages')
                ->join('users as enregistrer_par', 'unite_comptages.Enregistrer_par', '=', 'enregistrer_par.id')
                ->leftJoin('users as modifier_par', 'unite_comptages.Modifier_par', '=', 'modifier_par.id')
                ->select(
                    'unite_comptages.*',
                    'enregistrer_par.name as name',
                    'modifier_par.name as _name'
                )
                ->orderBy('unite_comptages.created_at', 'desc')
                ->get();



            return view('page.produit.unite_comptage.unite_comptage', ['listeUniteComptage' => $listeUniteComptage]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    // retourne la vue d'ajout une nouvelle unité compatge
    public function  Create()
    {
        $this->authorize('creer-unite-comptage');

        try {

            return view('page.produit.unite_comptage.nouveau');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('creer-unite-comptage');
        try {
            // dd($request);
            // Définir les données à valider
            $data = $request->only(['CodeUniteComptage', 'NomUniteComptage']);

            // Définir les règles de validation
            $validatorRules = [
                'CodeUniteComptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'NomUniteComptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'CodeUniteComptage.required' => "Le code email est requis",
                'NomUniteComptage.required' => "Le libellé est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return to_route('page.produit.unite_comptage')->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $code = $data['CodeUniteComptage'];
            $libelle = $data['NomUniteComptage'];

            $unite_comptage_exist = UniteComptage::where('Code', '=', $code)->first();
            // dd($unite_comptage_exist);
            if ($unite_comptage_exist !== null) {
                return to_route('page.produit.unite_comptage')->with('error', 'L\'unité de comptage que vous essayez d\'ajouter existe déjà.');
            } else {
                $unite_comptage = new UniteComptage();
                $unite_comptage->Code = $code;
                $unite_comptage->Libelle = $libelle;
                $unite_comptage->Enregistrer_par = auth()->user()->id;
                $unite_comptage->save();

                Activity::create([
                    'user_id' => auth()->user()->id,
                    'heure' => now(),
                    'description' => 'a créé l\'unité de comptage ' . $libelle . ' de code ' . $code,
                ]);
                return to_route('page.produit.unite_comptage')->with('success', 'L\'unité de comptage a bien été ajoutée');
            }
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function storeProduit(Request $request)
    {
        $this->authorize('creer-unite-comptage');
        try {
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
        // Définir les données à valider
        $data = $request->only(['code', 'libelle']);

        // Définir les règles de validation
        $validatorRules = [
            'code' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            'libelle' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
        ];

        // Définir les messages d'erreur pour chaque règle de validation
        $validationMessages = [
            'code.required' => "Le code email est requis",
            'libelle.required' => "Le libellé est requis",
        ];

        // alert si une regle n'est pas validé
        $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
        if ($validatorResult->fails()) {
            return to_route('page.unite_comptage_nouveau')->with('erreur', 'Veuillez renseigner  des informations valide');
        }

        $code = $data['code'];
        $libelle = $data['libelle'];

        $unite_comptage_exist = UniteComptage::where('Libelle', '=', $libelle)->first();
        if ($unite_comptage_exist !== null) {
            return to_route('page.nouveau.nouveau')->with('error', 'L\'unité de comptage que vous essayez d\'ajouter existe déjà.');
        } else {
            $unite_comptage = new UniteComptage();
            $unite_comptage->Code = $code;
            $unite_comptage->Libelle = $libelle;
            $unite_comptage->Enregistrer_par = auth()->user()->id;
            $unite_comptage->save();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a créé l\'unité de comptage ' . $libelle . ' de code ' . $code,
            ]);

            return response()->json([
                'success' => true,
                'newCategoryId' => $unite_comptage->id,
                'newCategoryName' => $unite_comptage->Libelle,
            ]);
            return to_route('page.nouveau.nouveau')->with('success', 'L\'unité de comptage a bien été ajoutée');
        }
    }

    public function edit($id)
    {
        $this->authorize('modifier-unite-comptage');
        try {
            $unite_comptage = UniteComptage::find($id);
            return view('page.produit.unite_comptage.edit', ['unite_comptage' => $unite_comptage]);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('modifier-unite-comptage');
        try {
            // dd($request);
            // Définir les données à valider
            $data = $request->only(['UpCodeUniteComptage', 'UpNomUniteComptage']);

            // Définir les règles de validation
            $validatorRules = [
                'UpCodeUniteComptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
                'UpNomUniteComptage' => 'required', // Ajoutez la règle 'email' ici pour vérifier le format de l'email
            ];

            // Définir les messages d'erreur pour chaque règle de validation
            $validationMessages = [
                'UpCodeUniteComptage.required' => "Le code email est requis",
                'UpNomUniteComptage.required' => "Le libellé est requis",
            ];

            // alert si une regle n'est pas validé
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return back()->with('erreur', 'Veuillez renseigner  des informations valide');
            }

            $code = $data['UpCodeUniteComptage'];
            $libelle = $data['UpNomUniteComptage'];

            $unite_comptage = UniteComptage::find($id);
            $unite_comptage->Code = $code;
            $unite_comptage->Libelle = $libelle;
            $unite_comptage->Modifier_par = auth()->user()->id;
            $unite_comptage->update();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié l\'unité de comptage' . $libelle . 'de code' . $code,
            ]);

            return to_route('page.produit.unite_comptage')->with('success', 'L\'unité de comptage a bien été modifiée');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function export()
    {

        $unite_comptages = UniteComptage::all();
        $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

        $data = [
            'unite_comptages' => $unite_comptages,
            'texteEntetePied' => $texteEntetePied
        ];

        $prefixe = 'unite_comptage_export';
        $date_et_heure = date('Ymd_His');
        $nom_excel = $prefixe . '_' . $date_et_heure . '.xlsx';

        return Excel::download(new UniteComptageExport($data), $nom_excel);
    }

    public function imprimer()
    {

        $data = UniteComptage::all();
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();

        // Générer le contenu HTML
        $htmlContent = view('page.produit.unite_comptage.imprimer', [
            'unite_comptages' => $data,
            'imageEntetePied' => $imageEntetePied,
        ])->render();

        // Configurer les options de Dompdf
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isRemoteEnabled', true);

        // Instancier Dompdf avec les options configurées
        $dompdf = new Dompdf($options);

        // Charger le contenu HTML
        $dompdf->loadHtml($htmlContent);

        // Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Afficher le PDF dans le navigateur
        return $dompdf->stream('document.pdf', ['Attachment' => false]);
    }
}
