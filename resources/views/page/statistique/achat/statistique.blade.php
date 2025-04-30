@extends('layouts.master', ['title' => 'Statistique Achat'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Achat',
        'infos2' => 'Achat',
        'infos3' => 'Liste',
    ])
    <div class="d-flex justify-content-center">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @can('statistique-achat-cumulee-mois')
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link  @if (isset($filterMois)) active @endif  @if (!isset($filterMois) && !isset($filterCategorie) && !isset($filterFournisseur) && !isset($filterAgence)) ) active @endif"
                        id="cumMois-tab" data-bs-toggle="tab" data-bs-target="#cumMois" type="button" role="tab"
                        aria-controls="cumMois" aria-selected="true">Achats Cumulées par mois </button>
                </li>
            @endcan

            @can('statistique-achat-cumulee-categorie')
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if (isset($filterCategorie)) active @endif" id="cumCat-tab"
                        data-bs-toggle="tab" data-bs-target="#cumCat" type="button" role="tab" aria-controls="cumCat"
                        aria-selected="false">Achats Cumulées par Categorie </button>
                </li>
            @endcan

            @can('statistique-achat-cumulee-fournisseur')
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if (isset($filterFournisseur)) active @endif" id="cumClient-tab"
                        data-bs-toggle="tab" data-bs-target="#cumClient" type="button" role="tab" aria-controls="cumClient"
                        aria-selected="false">Achats Cumulées par Fournisseur </button>
                </li>
            @endcan

            @can('statistique-achat-cumulee-agence')
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if (isset($filterAgence)) active @endif" id="cumPro-tab"
                        data-bs-toggle="tab" data-bs-target="#cumPro" type="button" role="tab" aria-controls="cumPro"
                        aria-selected="false">Achats Cumulées par Agence </button>
                </li>
            @endcan
        </ul>
    </div>
    <div class="tab-content" id="myTabContent">

        @can('statistique-achat-cumulee-mois')
            <div class="tab-pane fade @if (isset($filterMois)) show active @endif @if (!isset($filterMois) && !isset($filterCategorie) && !isset($filterFournisseur) && !isset($filterAgence)) ) show active @endif "
                id="cumMois" role="tabpanel" aria-labelledby="cumMois-tab">

                <form method="POST" action="{{ route('achat-cumule-par-mois') }}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label" for="date_debut_periode">Debut Période</label>
                                        <input name="start_date" type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                            id="start_date">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label" for="date_fin_periode">Fin Période</label>
                                        <input name="end_date" type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                            id="end_date">
                                    </div>

                                    <div class="col-md-2">
                                        <div class="mt-4">
                                            <button type="submit" class="btn text-white w-100"
                                                style="{{ background_color_1() }}">Appliquer</button>
                                        </div>
                                    </div>

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-3">
                        @can('statistique-achat-cumulee-mois-pdf')
                            <button name="reponse" type="button" value="imprimer" id="printButtonImprimerMois"
                                class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_2() }}">Imprimer PDF</button>
                        @endcan

                        @can('statistique-achat-cumulee-mois-excel')
                            <button name="reponse" type="button" value="exporter" id="printButtonExporterMois"
                                class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_1() }}">Exporter en
                                Excel</button>
                        @endcan
                    </div>
                </div>
                <div class="card m-b-30 mt-3" wire:ignore>
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des achats cumulées par mois</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="magasinsTable" class="tableInfo tableInfoMois datatable table dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col">Période </th>
                                        <th scope="col">Achat HT </th>
                                        <th scope="col">TVA </th>
                                        <th scope="col">AIB</th>
                                        <th scope="col">Achat TTC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($achat_cumule_par_mois))
                                        <tr>
                                            @forelse($achat_cumule_par_mois as $value)
                                        <tr>
                                            <td class="td-mois">
                                                {{ \Carbon\Carbon::createFromFormat('m', $value->month)->translatedFormat('F') }}-{{ $value->year }}
                                            </td>
                                            <td class="td-mois">{{ $value->Prix_Achat_Net }}</td>
                                            <td class="td-mois">0</td>
                                            <td class="td-mois">0</td>
                                            <td class="td-mois">{{ $value->Prix_Achat_Net }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune donnée trouvée</td>
                                        </tr>
                                    @endforelse
                                    @endif


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <div class="tab-pane fade @if (isset($filterCategorie)) show active @endif" id="cumCat" role="tabpanel"
            aria-labelledby="cumCat-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('achat-cumule-par-categorie') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="dateDebut" type="date" class="form-control"
                                        max="{{ date('Y-m-d') }}" id="dateDebut">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="dateFin" type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                        id="dateFin">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Catégorie</label><br>
                                    <select name="categorie" type="date" class="form-select js-single"
                                        max="{{ date('Y-m-d') }}" id="categorie">
                                        <option value="">Sélectionnez une Catégorie</option>
                                        @foreach ($categorie as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->Libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <form action="" method="POST"> --}}
                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_2() }}">Exporter</button>
                                    </div>
                                </div> --}}
                                {{-- </form> --}}

                            </div>
                        </form>

                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    @can('statistique-achat-cumulee-categorie-pdf')
                        <button name="reponse" type="button" value="imprimer" id="printButtonImprimerCategorie"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_2() }}">Imprimer
                            PDF</button>
                    @endcan

                    @can('statistique-achat-cumulee-categorie-excel')
                        <button name="reponse" type="button" value="exporter" id="printButtonExporterCategorie"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_1() }}">Exporter en
                            Excel</button>
                    @endcan
                </div>
            </div>


            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des achats cumulées par catégorie</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="display: yes;">
                        <table id="deuxiemeTableau"
                            class="tableInfo tableInfoCategorie datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Categorie</th>
                                    <th scope="col">Achat HT </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">AIB</th>
                                    <th scope="col">Achat TTC</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($achat_cumule_par_categorie))
                                    @forelse($achat_cumule_par_categorie as $value)
                                        <tr>
                                            <td class="td-categorie">{{ $value->categorie }}</td>
                                            <td class="td-categorie">{{ $value->Prix_Achat_Net }}</td>
                                            <td class="td-categorie">0</td>
                                            <td class="td-categorie">0</td>
                                            <td class="td-categorie">{{ $value->Prix_Achat_Net }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune donnée trouvée</td>
                                        </tr>
                                    @endforelse
                                @endif


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade @if (isset($filterFournisseur)) show active @endif" id="cumClient" role="tabpanel"
            aria-labelledby="cumClient-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('achat-cumule-par-fournisseur') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fournisseur</label>
                                    <select name="fournisseur" type="date" class="form-select js-single"
                                        max="{{ date('Y-m-d') }}" id="fournisseur">
                                        <option value="">Sélectionnez un Fournisseur</option>
                                        @foreach ($fournisseur as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->DenominationSociale }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Début Période</label>
                                    <input name="dateDebut" type="date" class="form-control"
                                        max="{{ date('Y-m-d') }}" id="dateDebut">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="dateFin" type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                        id="dateFin">
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="button" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="button" class="btn text-white w-100"
                                            style="{{ background_color_2() }}">Exporter</button>
                                    </div>
                                </div> --}}
                            </div>
                        </form>

                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    @can('statistique-achat-cumulee-fournisseur-excel')
                        <button name="reponse" type="button" value="imprimer" id="printButtonImprimerFournisseur"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_2() }}">Imprimer
                            PDF</button>
                    @endcan

                    @can('statistique-achat-cumulee-fournisseur-pdf')
                        <button name="reponse" type="button" value="exporter" id="printButtonExporterFournisseur"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_1() }}">Exporter en
                            Excel</button>
                    @endcan


                </div>
            </div>
            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des achats cumulées par client</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="tableInfo tableInfoFournisseur datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Raison sociale du client
                                    </th>
                                    <th scope="col">Fournisseur </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">AIB</th>
                                    <th scope="col">Achat TTC</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($achat_cumule_par_fournisseur))
                                    @forelse($achat_cumule_par_fournisseur as $value)
                                        <tr>
                                            <td class="td-fournisseur">{{ $value->DenominationSociale }}</td>
                                            <td class="td-fournisseur">{{ $value->Prix_Achat_Net }}</td>
                                            <td class="td-fournisseur">0</td>
                                            <td class="td-fournisseur">0</td>
                                            <td class="td-fournisseur">{{ $value->Prix_Achat_Net }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune donnée trouvée</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade @if (isset($filterAgence)) show active @endif" id="cumPro" role="tabpanel"
            aria-labelledby="cumPro-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('achat-cumule-par-agence') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Agence</label><br>
                                    <select name="agence" type="date" class="form-select js-single"
                                        max="{{ date('Y-m-d') }}" id="agence">
                                        <option value="">Sélectionnez une agence</option>

                                        @if (isset($agences))
                                            @foreach ($agences as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->NomAgence }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Début Période</label>
                                    <input name="dateDebut" type="date" class="form-control"
                                        max="{{ date('Y-m-d') }}" id="dateDebut">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="dateFin" type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                        id="dateFin">
                                </div>
                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="button" class="btn text-white w-100"
                                            style="{{ background_color_2() }}">Exporter</button>
                                    </div>
                                </div> --}}
                            </div>
                        </form>

                    </fieldset>
                </div>
            </div>
            <div class="row">

                <div class="col-md-3">
                    @can('statistique-achat-cumulee-agence-pdf')
                        <button name="reponse" type="button" value="imprimer" id="printButtonImprimerAgence"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_2() }}">Imprimer
                            PDF</button>
                    @endcan

                    @can('statistique-achat-cumulee-agence-excel')
                        <button name="reponse" type="button" value="exporter" id="printButtonExporterAgence"
                            class="btn btn-sm btn-primary m-2 border-0" style="{{ background_color_1() }}">Exporter en
                            Excel</button>
                    @endcan
                </div>
            </div>
            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des achats cumulées par produit</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable" class="tableInfo tableInfoAgence datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Désignation Produit</th>
                                    <th scope="col">Quantite</th>
                                    <th scope="col">Achat HT </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">AIB</th>
                                    <th scope="col">Achat TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($achat_cumule_par_agence))
                                    @forelse($achat_cumule_par_agence as $value)
                                        <tr>
                                            <td class="td-agence">{{ $value->Designation }}</td>
                                            <td class="td-agence">{{ $value->Qte_Entree }}</td>
                                            <td>{{ $value->Prix_Achat_Net }}</td>
                                            <td class="td-agence">0</td>
                                            <td class="td-agence">0</td>
                                            <td class="td-agence">{{ $value->Prix_Achat_Net }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune donnée trouvée</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .selected {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }

        .tableInfo2 {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo2 th,
        .tableInfo2 td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }
    </style>
    <script>
        $(document).ready(function() {
            $('#magasinsTable').on('click', '.clickable-row', function() {
                var ID = $(this).attr('data-id');
                $('#modifierLink').attr('wire:click.prevent', "editCategorie(" + ID + ")");
            });
        });

        $(document).ready(function() {
            function logTableContent(action) {
                // Sélectionner le tableau avec la classe "tableInfoMois"
                var $table = $('.tableInfoMois');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-mois');
                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-achat-cumule-par-mois',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter l'action (imprimer ou exporter) au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: action
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur les boutons
            $('#printButtonImprimerMois').click(function() {
                logTableContent('imprimer');
            });

            $('#printButtonExporterMois').click(function() {
                logTableContent('exporter');
            });
        });

        $(document).ready(function() {
            function logTableContent(action) {
                // Sélectionner le tableau avec la classe "tableInfoMois"
                var $table = $('.tableInfoCategorie');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-categorie');
                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-achat-cumule-par-categorie',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter l'action (imprimer ou exporter) au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: action
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur les boutons
            $('#printButtonImprimerCategorie').click(function() {
                logTableContent('imprimer');
            });

            $('#printButtonExporterCategorie').click(function() {
                logTableContent('exporter');
            });
        });

        $(document).ready(function() {
            function logTableContent(action) {
                // Sélectionner le tableau avec la classe "tableInfoMois"
                var $table = $('.tableInfoFournisseur');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-fournisseur');
                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-achat-cumule-par-fournisseur',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter l'action (imprimer ou exporter) au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: action
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur les boutons
            $('#printButtonImprimerFournisseur').click(function() {
                logTableContent('imprimer');
            });

            $('#printButtonExporterFournisseur').click(function() {
                logTableContent('exporter');
            });
        });

        $(document).ready(function() {
            function logTableContent(action) {
                // Sélectionner le tableau avec la classe "tableInfoMois"
                var $table = $('.tableInfoAgence');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-agence');
                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-achat-cumule-par-agence',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter l'action (imprimer ou exporter) au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: action
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur les boutons
            $('#printButtonImprimerAgence').click(function() {
                logTableContent('imprimer');
            });

            $('#printButtonExporterAgence').click(function() {
                logTableContent('exporter');
            });
        });
    </script>
@endsection
