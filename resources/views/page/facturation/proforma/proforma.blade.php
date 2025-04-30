@extends('layouts.master', ['title' => 'Proforma'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Proformas',
        'infos2' => 'Proformas',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start ">
        <div class="col text-end">
            @canany(['creer-proforma', 'modifier-proforma', 'dupliquer-proforma', 'convertir-proforma-en-facture',
                'imprimer-proforma'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @can('creer-proforma')
                            <li><a href="{{ route('nouveaupf') }}" class="dropdown-item" type="button">Nouveau</a></li>
                        @endcan
                        @can('modifier-proforma')
                            <li><a id="modifierProformaLink" class="dropdown-item" type="button">Modifier</a></li>
                        @endcan
                        @can('dupliquer-proforma')
                            <li><a id="dupliquerProformaLink" class="dropdown-item" type="button">Dupliquer</a></li>
                        @endcan
                        @can('convertir-proforma-en-facture')
                            <li><a id="conversionProformaLink" class="dropdown-item" type="button">Convertir en facture</a></li>
                        @endcan
                        @can('imprimer-proforma')
                            <li><a id="pdfProformaLink" target="_blank" class="dropdown-item" type="button">Imprimer</a></li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="18"
                            height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l11 0" />
                            <path d="M9 12l11 0" />
                            <path d="M9 18l11 0" />
                            <path d="M5 6l0 .01" />
                            <path d="M5 12l0 .01" />
                            <path d="M5 18l0 .01" />
                        </svg>
                        Liste</button>
                    @can('voir-impression-prorformas')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="18"
                                height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                            </svg>
                            Impression</button>
                    @endcan

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filterProformas') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des proformas</h3>
                                </div>
                                <div class="card-body responsive-2">
                                    <div class="table-responsive">
                                        <table class="tableInfo table table-striped datatable table-bordered  "
                                            id="proformaTable">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 200px;">Agence</th>
                                                    <th style="width: 130px;">Date</th>
                                                    <th style="width: 150px;">
                                                        Référence</th>
                                                    <th style="width: 100px;">
                                                        Dénomination&nbsp;sociale</th>
                                                    <th style="width: 100px;">
                                                        Statut proforma</th>
                                                    <th style="width: 400px;">
                                                        Objet</th>
                                                    <th style="width: 100px;">
                                                        Validité</th>
                                                    <th style="width: 300px;">
                                                        Autres&nbsp;Infos </th>
                                                    <th style="width: 300px;">
                                                        Commentaires</th>
                                                    <th style="width: 50px;">
                                                        Taux&nbsp;AIB</th>
                                                    <th style="width: 50px;">
                                                        Taux&nbsp;AIB&nbsp;Deductible</th>
                                                    <th style="width: 100px;">
                                                        Net&nbsp;A&nbsp;payer </th>
                                                    <th style="width: 200px;">
                                                        Enregistrer&nbsp;Par </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($listeProforma as $proforma)
                                                    <tr style="cursor:pointer;" class="clickable-row "
                                                        data-url="{{ route('getDetailProforma', ['id' => $proforma->id]) }}">

                                                        <td>{{ $proforma->NomAgence }}</td>
                                                        <td>{{ $proforma->created_at->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>{{ $proforma->Reference_facture }}</td>
                                                        <td>{{ $proforma->Denomination_sociale }}</td>
                                                        <td><span
                                                                class="badge bg-success">{{ $proforma->Statut_facture }}</span>
                                                        </td>
                                                        <td>{{ $proforma->Objet_facture }}</td>
                                                        <td>{{ $proforma->Validite }}</td>
                                                        <td>{{ $proforma->Autres_infos }}</td>
                                                        <td>{{ $proforma->Commentaire }}</td>
                                                        <td>{{ $proforma->Aib }}</td>
                                                        <td>{{ $proforma->Aib_deductible }}</td>
                                                        <td>{{ number_format($proforma->Net_a_payer, 0, ',', ' ') }}</td>
                                                        <td>{{ $proforma->user_name }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des lignes proforma</h3>
                                </div>
                                <div class="card-body responsive-1">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>
                                                        Réf</th>
                                                    <th>
                                                        Désignation</th>
                                                    <th>G.&nbsp;T. </th>
                                                    <th>PU&nbsp;HT
                                                    </th>
                                                    <th>%&nbsp;Remise
                                                    </th>
                                                    <th>PU&nbsp;NET&nbsp;HT
                                                    </th>
                                                    <th>PU&nbsp;NET&nbsp;TTC</th>
                                                    <th>Qte</th>
                                                    <th>Montant&nbsp;Net&nbsp;HT</th>
                                                    <th>Montant&nbsp;Net&nbsp;TTC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($detailProformas as $detailProforma)
                                                    @php

                                                        $puHT = $detailProforma->Prix_unitaire_HT;
                                                        $puHT_Net =
                                                            $puHT - $puHT * ($detailProforma->Taux_remise / 100);
                                                        $puNetTTC = $detailProforma->Prix_unitaire_TTC;
                                                        $MontantNetTTC = $puNetTTC * $detailProforma->Qte;

                                                        $MontantNetHT =
                                                            $MontantNetTTC / (1 + $detailProforma->valeur_taxe / 100);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $detailProforma->Reference }}</td>
                                                        <td>{{ $detailProforma->Designation }}</td>
                                                        <td>{{ $detailProforma->Code_lettre }}</td>
                                                        <td>{{ number_format($detailProforma->Prix_unitaire_TTC, 0, ',', ' ') }}
                                                        </td>
                                                        <td>{{ $detailProforma->Taux_remise }}</td>
                                                        <td>{{ number_format($puHT_Net, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($puNetTTC), 0, ',', ' ') }}</td>
                                                        <td>{{ number_format($detailProforma->Qte, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($MontantNetHT), 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($MontantNetTTC), 0, ',', ' ') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">Aucune donnée (Selectionner
                                                            une
                                                            facture)</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mt-2">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Liste des proformas sur une période</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form action="{{ route('proformaByPeriode') }}" method="POST" target='_blank'
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center ">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="form-label" for="date_debut_periode">Debut Période</label>
                                                <input name="date_debut_periode" type="date" class="form-control"
                                                    max="{{ date('Y-m-d') }}" id="date_debut_periode">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="date_fin_periode">Fin Période</label>
                                                <input name="date_fin_periode" type="date" class="form-control"
                                                    max="{{ date('Y-m-d') }}" id="date_fin_periode">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="client">Client</label>
                                                <select name="client" type="text" class="form-select"
                                                    id="client">
                                                    <option value="Tous">Tous</option>
                                                    @forelse ($listeClient as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->Denomination_sociale }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mt-4">
                                                    <button type="submit" class="btn text-white w-100"
                                                        style="{{ background_color_1() }}">Imprimer</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </fieldset>
                            </div>

                        </div>
                    </div>

                </div> --}}
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
        <div class="card mb-5">
            <div class="card-body">
                <div class="row mt-2">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">
                            <h4>Liste des proformas sur une période</h4>
                        </legend>
                        <div class="col-md-12offset-md-1">
                            <form id="listeFacturePeriodeForm" action="{{ route('proformaByPeriode') }}" method="GET"
                                class="row gx-3 gy-2 d-flex align-items-center justify-content-center ">
                                @csrf
                                <div class="col-md-3">
                                    <label class="" id="inputGroup-sizing-sm">Agence</label>
                                    <select name="agence" type="text" class="form-select js-single  w-100"
                                        style="width: 100%;" id="agence4">
                                        @if (userAffectedSiege())
                                            <option value="Tous">Tous</option>
                                        @endif
                                        @foreach ($listeAgence as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                {{ $value->NomAgence }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="date_debut_periode" type="datetime-local" class="form-control"
                                        max="{{ date('Y-m-d\TH:i') }}" id="date_debut_periode">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="date_fin_periode">Fin Période</label>
                                    <input name="date_fin_periode" type="datetime-local" class="form-control"
                                        max="{{ date('Y-m-d\TH:i') }}" id="date_fin_periode">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="client">Client</label>
                                    <select name="client" type="text" class="form-select js-single" id="client">
                                        <option value="Tous">Tous</option>
                                        @forelse ($listeClient as $client)
                                            <option value="{{ $client->id }}">{{ $client->Denomination_sociale }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-2 float-left">
                                    <button id="AppliquerForm3" type="submit" class="btn text-white w-100 mt-4"
                                        style="{{ background_color_1() }}">Appliquer</button>
                                </div>

                            </form>
                        </div>
                        <div id="dateError3" style="display: none; color: red;">La date de début doit être antérieure à la
                            date de fin.</div>
                    </fieldset>
                </div>

            </div>
        </div>
        @can('exporter-excel-proformas')
            <button id="exportButton3" data-bs-toggle="modal" data-bs-target="#staticBackdropImp"
                class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
        @endcan

        @can('imprimer-proformas')
            <button id="exportButton3P" data-bs-toggle="modal" data-bs-target="#staticBackdropImpP"
                class="btn mb-2 text-white" style="{{ background_color_2() }}">Exporter en PDF</button>
        @endcan

        <div class="card m-b-30 mb-5">
            <div class="card-header" style="{{ background_color_2() }}">
                <h3 class="mt-2  d-inline-block text-dark">Liste des proformas</h3>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="table-responsive">
                        <table class="tableInfo2 " id="tableFactureParPeriode" style="border: 1px solid #ddd">
                            <thead class="">
                                <tr>
                                    <th style="width: 20px; font-size: 18px;" scope="col">#
                                    </th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">
                                        Date</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Ref</th>
                                    <th style="width: 300px; font-size: 18px;" scope="col">
                                        Dénomination&nbsp;sociale</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Validité</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Aib</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Aib à déduire</th>

                                    <th style="width: 200px; font-size: 18px;" scope="col">
                                        Net&nbsp;à&nbsp;payer</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div id="req_message3" style="text-align: center; color: red; display: none;">
                        Veuillez faire une requette pour afficher les données
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <br><br>
        <div class="modal fade" id="staticBackdropImp" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment exporter les données actuels ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        <button id="exportExcel_Imp" class="btn btn-sm btn-success">Oui Exporter en Excel</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="staticBackdropImpP" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment exporter les données en PDF ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        <button id="exportPDF_Imp" target="_blank" class="btn btn-sm btn-success">Oui Exporter en
                            PDF</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>
    </div>
    </div>
    <script>
        document.getElementById('proformaByPeriodeForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    </script>
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
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = parseFloat(number).toFixed(decimals);
            var parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
            return parts.join(dec_point);
        }

        function applyTableEvents() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-1').html();
                    $(".responsive-1").html(tableContent);
                });
                $('.clickable-row').removeClass('active');
                $(this).addClass('active');
            });

            $('#proformaTable').on('click', '.clickable-row', function() {
                var proformaId = $(this).data('url').split('/').pop();

                var editProformaUrl = "{{ route('editProforma', ['id' => ':proformaId']) }}";
                var dupliquerProformaUrl = "{{ route('dupliquerProforma', ['id' => ':proformaId']) }}";
                var conversionProformaUrl = "{{ route('conversionProforma', ['id' => ':proformaId']) }}";
                var pdfProformaUrl = "{{ route('PDFProformaA4', ['id' => ':proformaId']) }}";
                editProformaUrl = editProformaUrl.replace(':proformaId', proformaId);
                dupliquerProformaUrl = dupliquerProformaUrl.replace(':proformaId', proformaId);
                conversionProformaUrl = conversionProformaUrl.replace(':proformaId', proformaId);
                pdfProformaUrl = pdfProformaUrl.replace(':proformaId', proformaId);
                $('#modifierProformaLink').attr('href', editProformaUrl);
                $('#dupliquerProformaLink').attr('href', dupliquerProformaUrl);
                $('#conversionProformaLink').attr('href', conversionProformaUrl);
                $('#pdfProformaLink').attr('href', pdfProformaUrl);
            });

            $('.clickable-row').on('click', function() {
                $('.clickable-row').removeClass('selected');
                $(this).addClass('selected');
            });
        }

        function clearDetailTable() {
            var detailTbody = document.querySelector('.tableInfo2 tbody');
            detailTbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">Aucune donnée (Selectionner une facture)</td>
                </tr>
            `;
        }

        $(document).ready(function() {
            applyTableEvents();
        });
    </script>
    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 3000px;
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
            border: 1px solid #ddd;
            width: 100%;
        }

        .tableInfo2 th,
        .tableInfo2 td {

            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }
    </style>
    <style>
        .tableInfo2 {
            width: 1500px;
        }



        @media (min-width: 1600px) {
            .tableInfo2 {
                width: 100%;
            }

            th {
                width: auto;
            }
        }
    </style>
    <!-- Lien "Modifier" -->
    @include('layouts.alert')
    <script>
        function imprimer(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
    {{-- impression --}}
    <script>
        $(document).ready(function() {
            var exportButton3 = document.getElementById("exportButton3");
            var exportButton3P = document.getElementById("exportButton3P");
            var table = document.getElementById("tableFactureParPeriode").getElementsByTagName("tbody")[0];

            // Déclaration d'une variable globale pour stocker la réponse AJAX
            var ajaxResponse;

            // Fonction pour vérifier si le tableau est vide
            function checkTable() {
                if (table.rows.length === 0) {
                    exportButton3.disabled = true;
                    exportButton3P.disabled = true;
                    $('#req_message3').show();
                } else {
                    exportButton3.disabled = false;
                    exportButton3P.disabled = false;
                    $('#req_message3').hide();
                }
            }

            function numberFormat(value) {
                return Number(value).toLocaleString('fr-FR');
            }

            // Appel la fonction de vérification au chargement de la page
            checkTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#listeFacturePeriodeForm').on('submit', function(e) {
                e
                    .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                var formData = $(this).serialize(); // Sérialisation des données du formulaire
                var dateDebut = new Date($('#dateDebut').val());
                var dateFin = new Date($('#dateFin').val());

                if (dateDebut > dateFin) {
                    $('#dateError3').show();
                    return;
                } else {
                    $('#dateError3').hide();
                }
                var $button = $('#AppliquerForm3');
                $button.addClass('loading');
                $button.prop('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'),
                    data: formData,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {

                        ajaxResponse = response;
                        $('#tableFactureParPeriode tbody').empty();

                        let counter = 1;
                        let totalFactures = 0;
                        let grandTotal = 0;

                        response.listeProforma.forEach(function(facture) {
                            const nap = parseFloat(facture.Net_a_payer);
                            totalFactures++;
                            grandTotal += nap;

                            const row = `<tr>
                                            <td>${counter++}</td>
                                            <td>${formatDate(facture.Date_facture)}</td>
                                            <td>${facture.Reference_facture}</td>
                                            <td>${facture.Denomination_sociale}</td>
                                            <td>${facture.Validite}</td>
                                            <td>${facture.Aib}</td>
                                            <td>${numberFormat(facture.Aib_deductible)}</td>
                                            <td>${numberFormat(nap)}</td>
                                        </tr>`;
                            $('#tableFactureParPeriode tbody').append(row);
                        });

                        const grandTotalRow = `<tr class="font-weight-bold">
                                <td colspan="6" >Total général</td>
                                <td >(${totalFactures} proforma(s))</td>
                                <td >${numberFormat(grandTotal)}</td>
                            </tr>`;
                        $('#tableFactureParPeriode tbody').append(grandTotalRow);


                        checkTable();
                        $button.removeClass('loading'); // Retire la classe .loading du bouton
                        $button.prop('disabled', false); // Réactive le bouton
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $button.removeClass('loading'); // Retire la classe .loading du bouton
                        $button.prop('disabled', false); // Réactive le bouton
                    }
                });
            });

            $('#exportExcel_Imp').off('click').on('click', function() {
                var $button = $(this);
                if (ajaxResponse) {
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableFactureParPeriodeData = [];
                    $('#tableFactureParPeriode tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            count: row.find('td').eq(0).text(),
                            Date_facture: row.find('td').eq(1).text(),
                            Reference_facture: row.find('td').eq(2).text(),
                            Denomination_sociale: row.find('td').eq(3).text(),
                            Validite: row.find('td').eq(4).text(),
                            Aib: row.find('td').eq(5).text(),
                            Aib_deductible: row.find('td').eq(6).text(),
                            Net_a_payer: row.find('td').eq(7).text(),
                        };
                        tableFactureParPeriodeData.push(rowData);
                    });

                    var exportData = {
                        tableFactureParPeriodeData: tableFactureParPeriodeData,
                        // infoMagasin: ajaxResponse.infoMagasin,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_impression_proforma_periode',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'Liste_proforma_periode.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en Excel');

                            $('#staticBackdropImp').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en Excel');
                        }
                    });
                } else {
                    console.error("Aucune donnée disponible pour l'exportation.");
                }
            });

            $('#exportPDF_Imp').off('click').on('click', function() {
                var newWindow = null;

                //chargement
                var $button = $(this);
                $button.addClass('loading');
                $button.prop('disabled', true);
                $button.text('Exportation en cours...');

                var tableFactureParPeriodeData = [];
                $('#tableFactureParPeriode tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        count: row.find('td').eq(0).text(),
                        Date_facture: row.find('td').eq(1).text(),
                        Reference_facture: row.find('td').eq(2).text(),
                        Denomination_sociale: row.find('td').eq(3).text(),
                        Validite: row.find('td').eq(4).text(),
                        Aib: row.find('td').eq(5).text(),
                        Aib_deductible: row.find('td').eq(6).text(),
                        Net_a_payer: row.find('td').eq(7).text(),
                    };
                    tableFactureParPeriodeData.push(rowData);
                });

                var exportData = {
                    tableFactureParPeriodeData: tableFactureParPeriodeData,
                    dateDebut: ajaxResponse.dateDebut,
                    dateFin: ajaxResponse.dateFin,
                    infoClient: ajaxResponse.infoClient,
                    infoAgence: ajaxResponse.infoAgence
                };


                $.ajax({
                    type: 'POST',
                    url: '/export_impression_proforma_periode_pdf',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var url = window.URL.createObjectURL(
                            blob);

                        newWindow = window.open(url, '_blank');

                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        $button.text('Oui, Exporter en PDF');

                        $('#staticBackdropImpP').modal('hide');
                    },

                    error: function(xhr, status, error) {
                        console.error(error);

                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        $button.text('Oui, Exporter en PDF');
                    }
                });

            });

            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

        });
    </script>
@endsection
