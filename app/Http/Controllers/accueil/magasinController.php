<?php

namespace App\Http\Controllers\accueil;

use Exception;
use App\Models\Agence;
use App\Models\Magasin;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FiltreAgence\FiltreAgenceRequest;
use App\Exports\MagasinsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Image;
use PhpParser\Node\Stmt\TryCatch;

class magasinController extends Controller
{
    public function listeMagasin(Request $request)
    {
        $this->authorize('consulter-liste-magasin');
        $query = Magasin::query();
        $site_id = session()->get('site_id');

        if ($request->filled('agenceFilter_id')) {
            if (is_array(getIdAgenceFilter($request->input('agenceFilter_id')))) {
                $query = $query->whereIn('agence_id', getIdAgenceFilter($request->input('agenceFilter_id')));
            } else {
                $query = $query->where('agence_id', '=', getIdAgenceFilter($request->input('agenceFilter_id')));
            }
        } else {

            $query_agence = Agence::find($site_id);

            if ($query_agence->NomAgence === 'Siège') {
                $query = $query;
            } else {
                $query = $query->where('agence_id', '=', $site_id);
            }
        }

        $agencesIds = auth()->user()->agences->pluck('agence_id')->toArray();


        if (is_array($agencesIds)) {
            $listeAgence = Agence::whereIn('id', $agencesIds)->get();
        } else {
            $listeAgence = Agence::where('id', '=', $agencesIds)->get();
        }

        $listeMagasin = $query->orderBy('created_at', 'desc')->get();



        return view('page.accueil.magasins.magasin', ['listeMagasin' => $listeMagasin, 'listeAgence' => $listeAgence]);
    }


    public function storeMagasin(Request $request)
    {
        $this->authorize('creer-magasin');
        try {
            $request->validate([
                'NomMagasin' => ['required'],
                'agence_id' => ['required'],
                'Statut_Magasin' => ['required'],
            ]);
            $existingMagasin = Magasin::where('NomMagasin', $request->NomMagasin)
                ->where('agence_id', $request->agence_id)
                ->first();

            if ($existingMagasin) {
                return back()->with('error', "Un magasin avec le même nom existe déjà dans l'agence.");
            }
            $user_connecterId = auth()->user()->id;
            $magasin = new Magasin();
            $magasin->NomMagasin = $request->NomMagasin;
            $magasin->agence_id = $request->agence_id;
            $magasin->Statut_Magasin = $request->Statut_Magasin;
            $magasin->Enregistrer_par = $user_connecterId;
            $magasin->save();

            // Enregistrement de l'activité associée
            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a créé le magasin ' . $request->NomMagasin . 'pour l\'agence' . $request->agence_id,
            ]);

            return to_route('magasin')->with('success', 'Magasin ajouté avec succès ');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function updateMagasin(Request $request, string $id)
    {
        $this->authorize('modifier-magasin');
        try {
            $data = $request->only(['UpNomMagasin', 'up_agence_id', 'Up_Statut_Magasin']);

            $validatorRules = [
                'UpNomMagasin' => 'required',
                'up_agence_id' => 'required',
                'Up_Statut_Magasin' => 'required',
            ];

            $validationMessages = [
                'UpNomMagasin.required' => "Le nom de la catégorie est requise",
                'up_agence_id.required' => "L'agence est requise",
            ];
            $validatorResult = Validator::make($data, $validatorRules, $validationMessages);
            if ($validatorResult->fails()) {
                return redirect()->back()->withErrors($validatorResult)->withInput();
                //return to_route('editMagasin')->with('error', 'Veuillez renseigner  des informations valide');
            }

            $NomMagasin = $data['UpNomMagasin'];
            $agence_id = $data['up_agence_id'];
            $Statut_Magasin = $data['Up_Statut_Magasin'];

            $user_connecterId = auth()->user()->id;

            $magasin = Magasin::findorfail($id);
            $magasin->NomMagasin = $NomMagasin;
            $magasin->agence_id = $agence_id;
            $magasin->Statut_Magasin = $Statut_Magasin;
            $magasin->Modifier_par = $user_connecterId;
            $magasin->update();

            Activity::create([
                'user_id' => auth()->user()->id,
                'heure' => now(),
                'description' => 'a modifié le magasin ' . $NomMagasin . 'de l\'agence' . $agence_id,
            ]);
            //return redirect()->back()->with('success', 'La categorie a bien été ajoutée');
            return to_route('magasin')->with('success', 'Modification effectuée avec succès ');
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        //
    }
    public function export()
    {
        try {
            $site_id =session()->get('site_id');

            $data = Magasin::all();
            $data = DB::table('magasins')
                ->join('agences', 'magasins.agence_id', '=', 'agences.id')
                ->select('magasins.*', 'agences.NomAgence as Nom_Agence')
                ->where('agences.id', '=', $site_id)
                ->get();
            $texteEntetePied = Image::where('nom', 'entetePiedExcel')->first();

            $export = new MagasinsExport($data, $texteEntetePied);

            $fileName = 'magasin_export' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download($export, $fileName);
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            return redirect()->back()->with('warning', "Une erreur s'est produite : " . $e->getMessage());
        }
    }


    public function imprimer()
    {
        
        $imageEntetePied = Image::where('nom', 'entetePiedA4')->first();
        $site_id =session()->get('site_id');

        $data = DB::table('magasins')
            ->join('agences', 'magasins.agence_id', '=', 'agences.id')
            ->select('magasins.*', 'agences.NomAgence as NomF_Agence')
            ->where('agences.id', '=', $site_id)
            ->get();

            // dd($data);

        // $data = Magasin::all();
        $html = view('page.accueil.magasins.document.magasinimpression', [
            "data" => $data,

            "imageEntetePied" => $imageEntetePied
        ])->render();

        $prefixe = 'magasin_impression';
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
