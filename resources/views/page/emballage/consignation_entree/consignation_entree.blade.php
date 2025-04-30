@extends('layouts.master', ['title' => 'Consignation Entrée'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Consignation Entrée',
        'infos2' => 'Consignation Entrée',
        'infos3' => 'Liste',
    ])

    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @can('regler-consignation-entree')
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('regler-consignation-entree')
                            <li><a href="{{ route('showFormRegleConsignation_entree') }}" class="dropdown-item"
                                    type="button">Régler Consignation entrée</a></li>
                        @endcan
                    </ul>
                </div>
            @endcan
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Vous êtes sur le point d'annuler des règlements. Cette opération est irréversible <br>
                    <strong><em>Voulez-vous continuer?</em></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="modifierBtn">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Liste</button>
                    {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Impression</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                            action="{{ route('filterConsignationEntree') }}" method="GET">
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                    <label class="m-1" for="annee">Année</label>
                                    <select name="annee" id="anneeSelect" class="form-select">
                                        <option value="">Sélectionner une année</option>
                                        @foreach ($annees as $annee)
                                            <option value="{{ $annee }}">{{ $annee }}</option>
                                        @endforeach
                                    </select>

                                </div>


                                <div class="col-md-12 mb-3">
                                    <select class="form-select" id="monthSelect" size="12" name="month"
                                        aria-label="Size 3 select example">
                                        <option value="">{{ __('Sélectionnez un mois') }}</option>
                                        @php
                                            $months = [
                                                1 => 'Janvier',
                                                2 => 'Février',
                                                3 => 'Mars',
                                                4 => 'Avril',
                                                5 => 'Mai',
                                                6 => 'Juin',
                                                7 => 'Juillet',
                                                8 => 'Août',
                                                9 => 'Septembre',
                                                10 => 'Octobre',
                                                11 => 'Novembre',
                                                12 => 'Décembre',
                                            ];
                                        @endphp
                                        @foreach ($months as $key => $month)
                                            <option value="{{ $key }}"
                                                @if (request()->query('month') == $key) selected style="color: blue;" @endif>
                                                {{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                        </div>
                        <div class="col-md-10">
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des consignations entrées</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="proformaTable"
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapsed ; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">
                                                        Date
                                                    </th>
                                                    <th scope="col">
                                                        Agence
                                                    </th>
                                                    <th scope="col">
                                                        Reférence entrée</th>
                                                    <th scope="col">
                                                        Dénomination Sociale</th>
                                                    <th scope="col">
                                                        Statut</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($listeConsignation as $consignation)
                                                    <tr style="cursor:pointer" class="clickable-row"
                                                        data-url="{{ route('getDetailConsignation_entree', ['id' => $consignation->id]) }}"
                                                        data-id="{{ $consignation->id }}">
                                                        <td class="entree-produit">
                                                            {{ \Carbon\Carbon::parse($consignation->created_at)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td class="entree-produit">{{ $consignation->NomAgence }}
                                                        </td>
                                                        <td class="entree-produit">{{ $consignation->ref_entree }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $consignation->Denomination_sociale }}</td>
                                                        <td class="entree-produit">{{ $consignation->statut }}
                                                        </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">
                                                            <h6 class="mt-3 text-center">Aucune donnée pour
                                                                l'instant.</h6>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Détails consignations entrées</h3>
                                </div>
                                <div class="card-body responsive-1">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered dt-responsive nowrap"
                                            id="">
                                            <thead class="table-primary">
                                                <tr>


                                                    <th scope="col">
                                                        Emballage</th>

                                                    <th scope="col">
                                                        Magasin</th>

                                                    <th scope="col">
                                                        Quantité</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if (isset($detailConsignation))
                                                    @forelse($detailConsignation as $detail)
                                                        <tr style="cursor:pointer" id="clickable-row2">

                                                            <td class="entree-produit">
                                                                {{ $detail->Nom_emballage }}
                                                            </td>
                                                            <td class="entree-produit">
                                                                {{ $detail->NomMagasin }}
                                                            </td>
                                                            <td class="entree-produit">{{ $detail->Qte }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8">
                                                                <h6 class="mt-3 text-center">Aucune donnée pour
                                                                    l'instant.</h6>
                                                            </td>
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
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <form action="{{ route('script') }}" method="POST">
                        @csrf
                    </form>
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Liste des règlements sur une période</h4>
                                    </legend>
                                    {{--  <div class="col-md-10 offset-md-1">
                                        <form action="{{ route('get.imprimerReglement') }}" method="POST"
                                            class="row gx-3 gy-2 ">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="form-label" id="inputGroup-sizing-sm">Debut</label>
                                                <input name="debut_periode" type="date" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="<?php echo date('Y-m-d', strtotime('-5 days')); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" id="inputGroup-sizing-sm">Fin</label>
                                                <input name="fin_periode" type="date" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="client">Client</label>
                                                <select name="client" type="text" class="form-select"
                                                    id="client-input" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">

                                                    <option value="Tous">Tous</option>
                                                    @foreach ($clients as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->Denomination_sociale }} ({{ $value->Numero_ifu }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Le client est obligatoire</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" id="produit">Mode règlement</label>
                                                <select name="mode_reglement" type="text" class="form-select"
                                                    id="mode_reglement" value="{{ old('mode_reglement') }}"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">

                                                    <option value="Tous">Tous</option>
                                                    @foreach ($mode_paiements as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->Libelle_Operation }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        id="flex1" checked value="1">
                                                    <label class="form-check-label" for="flex1">
                                                        La liste des règlements par client et par mode de paiement sur la
                                                        période
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        value="2" id="flex2">
                                                    <label class="form-check-label" for="flex2">
                                                        La liste des règlements par mode de paiement et par client sur la
                                                        période
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        value="3" id="flex3">
                                                    <label class="form-check-label" for="flex3">
                                                        La liste des règlements par mode de paiement sur la période
                                                    </label>
                                                </div>


                                            </div>

                                            <div class="row ">
                                                <div class="col-md-6 d-flex ">
                                                    <div class="mt-4 me-3">
                                                        <button type="submit" name="reponse" value="imprimer"
                                                            class="btn text-white"
                                                            style="{{ background_color_2() }}">Imprimer en PDF</button>
                                                    </div>

                                                    <div class="mt-4">
                                                        <button type="submit" name="reponse" value="exporter"
                                                            class="btn text-white"
                                                            style="{{ background_color_1() }}">Exporter en Excel</button>
                                                    </div>
                                                </div>
                                            </div>


                                        </form>
                                    </div> --}}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .selected>td {
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

        .selected-row {
            background-color: #d84b4b;
            /* Ou la couleur de votre choix */
        }
    </style>
    @includeIf('layouts.alert')
    <script>
        // Soumettre le formulaire automatiquement quand un mois est sélectionné
        document.getElementById('monthSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Vous pouvez également ajouter un événement similaire pour la sélection d'une année si vous le souhaitez
        document.getElementById('anneeSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Désactiver les boutons par défaut
            $('#imprimer-button').prop('disabled', true);
            $('#imprimer-button5').prop('disabled', true);
            $('#imprimer-button8').prop('disabled', true);

            // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
            $('.clickable-row').click(function() {
                // Activer les boutons
                $('#imprimer-button').prop('disabled', false);
                $('#imprimer-button5').prop('disabled', false);
                $('#imprimer-button8').prop('disabled', false);
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-1')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-1").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });

            // Lorsque le bouton d'impression est cliqué
            $('#showModal').on('click', function() {
                // Vérifiez si une ligne a été sélectionnée
                if ($('#reponse').val() !== '') {
                    // Soumettez le formulaire
                    $('#impressionForm').submit();
                } else {
                    alert('Veuillez sélectionner un règlement avant de procéder à l\'impression.');
                }
            });
        });
        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-2')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-2").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });

            // Lorsque le bouton d'impression est cliqué
            $('#showModal').on('click', function() {
                // Vérifiez si une ligne a été sélectionnée
                if ($('#reponse').val() !== '') {
                    // Soumettez le formulaire
                    $('#impressionForm').submit();
                } else {
                    alert('Veuillez sélectionner un règlement avant de procéder à l\'impression.');
                }
            });
        });
    </script>


    @include('layouts.alert')
@endsection
