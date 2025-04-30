@extends('layouts.master', ['title' => 'Consignation'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Consignation',
        'infos2' => 'Consignation',
        'infos3' => 'Liste',
    ])

    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @can('regler-consignation')
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('regler-consignation')
                            <li><a href="{{ route('showFormRegleConsignation') }}" class="dropdown-item" type="button">Régler
                                    Consignation</a></li>
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
                    @can('consignation-situation-client')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Situation
                            Client</button>
                    @endcan
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filterConsignation') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des consignations</h3>
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
                                                        Reférence facture</th>
                                                    <th scope="col">
                                                        Dénomination Sociale</th>

                                                    <th scope="col">
                                                        Statut</th>
                                                    <th scope="col">
                                                        Enregistrer par</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($listeConsignation as $consignation)
                                                    <tr style="cursor:pointer" class="clickable-row"
                                                        data-url="{{ route('getDetailConsignation', ['id' => $consignation->id]) }}"
                                                        data-id="{{ $consignation->id }}">
                                                        <td class="entree-produit">
                                                            {{ \Carbon\Carbon::parse($consignation->created_at)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td class="entree-produit">{{ $consignation->ref_facture }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $consignation->Denomination_sociale }}</td>
                                                        <td class="entree-produit">{{ $consignation->statut }}
                                                        </td>
                                                        <td class="entree-produit">{{ $consignation->name }}
                                                        </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Détails consignation</h3>
                                </div>
                                <div class="card-body responsive-1">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered dt-responsive nowrap"
                                            id="">
                                            <thead class="table-primary">
                                                <tr>


                                                    <th scope="col">
                                                        Designation</th>

                                                    <th scope="col">
                                                        Quantité</th>
                                                    <th scope="col">
                                                        Restitué</th>

                                                    <th scope="col">
                                                        Facturé</th>
                                                    <th scope="col">
                                                        Restant</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($detailConsignation as $detail)
                                                    <tr style="cursor:pointer" id="clickable-row2">

                                                        <td class="entree-produit">
                                                            {{ $detail->Nom_emballage }} => {{ $detail->Designation }}
                                                        </td>
                                                        <td class="entree-produit">{{ $detail->Qte }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $detail->restituee }}
                                                        </td>
                                                        <td class="entree-produit">{{ $detail->facturee }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $detail->Qte - $detail->restituee - $detail->facturee }}
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Facture de déconsignation associée</h3>
                                </div>
                                <div class="card-body responsive-2">
                                    <div class="table-responsive">
                                        <table class=" table table-striped table-bordered dt-responsive nowrap"
                                            id="">
                                            <thead class="table-primary">
                                                <tr>


                                                    <th scope="col">
                                                        Date facture</th>

                                                    <th scope="col">
                                                        Référence</th>
                                                    <th scope="col">Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($listefactureDeconsignation))
                                                    @forelse($listefactureDeconsignation as $facture)
                                                        <tr style="cursor:pointer">

                                                            <td class="entree-produit">
                                                                {{ $facture->Date_facture }}
                                                            </td>
                                                            <td class="entree-produit">{{ $facture->Reference_facture }}
                                                            </td>
                                                            <td class="entree-produit">
                                                                {{ $facture->Statut_facture }}
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

                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Voir la situation des dettes d'un client</h4>
                                    </legend>
                                    <div class="col-md-12offset-md-1">
                                        <form id="listeFacturePeriodeForm" action="{{ route('situationClient') }}"
                                            method="GET"
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center ">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="" id="inputGroup-sizing-sm">Agence</label>
                                                <select name="agence" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="agence4">
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
                                            {{--        <div class="col-md-3">
                                                <label class="form-label" for="date_debut_periode">Debut Période</label>
                                                <input name="date_debut_periode" type="datetime-local" class="form-control"
                                                    max="{{ date('Y-m-d\TH:i') }}" id="date_debut_periode">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="date_fin_periode">Fin Période</label>
                                                <input name="date_fin_periode" type="datetime-local" class="form-control"
                                                    max="{{ date('Y-m-d\TH:i') }}" id="date_fin_periode">
                                            </div> --}}
                                            <div class="col-md-3">
                                                <label class="form-label" for="client">Client</label><br>
                                                <select name="client" type="text" class="form-select js-single "
                                                    style="width: 100%;" id="client">
                                                    {{--   <option value="Tous">Tous</option> --}}
                                                    @forelse ($listeClient as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->Denomination_sociale }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-2 float-left">
                                                <button id="AppliquerForm3" type="submit"
                                                    class="btn text-white w-100 mt-4"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>

                                        </form>
                                    </div>
                                    {{--  <div id="dateError3" style="display: none; color: red;">La date de début doit être antérieure à la
                                        date de fin.</div> --}}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <button id="exportButton3" data-bs-toggle="modal" data-bs-target="#staticBackdropImp"
                        class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
                    <button id="exportButton3P" data-bs-toggle="modal" data-bs-target="#staticBackdropImpP"
                        class="btn mb-2 text-white" style="{{ background_color_2() }}">Exporter en PDF</button>
                    <div class="card m-b-30 mb-5">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark"></h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="table-responsive">
                                    <table class="tableInfo2 " id="tableFactureParPeriode"
                                        style="border: 1px solid #ddd">
                                        <thead class="">
                                            <tr>
                                                <th style="width: 200px; font-size: 18px;" scope="col">
                                                    Type</th>

                                                <th style="width: 200px; font-size: 18px;" scope="col">
                                                    Quantité</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Restitué</th>

                                                <th style="width: 200px; font-size: 18px;" scope="col">Facturé</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Dette</th>


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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données actuels ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportExcel_Imp" class="btn btn-sm btn-success">Oui Exporter en
                                        Excel</button>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données en PDF ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPDF_Imp" target="_blank" class="btn btn-sm btn-success">Oui
                                        Exporter en
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
        // Soumettre le formulaire automatiquement quand un mois est sélectionné
        document.getElementById('monthSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Vous pouvez également ajouter un événement similaire pour la sélection d'une année si vous le souhaitez
        document.getElementById('anneeSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
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
                        console.log(ajaxResponse);
                        $('#tableFactureParPeriode tbody').empty();


                        const ancienneDetteRow = `<tr class="font-weight-bold">
                                <td colspan="4" >Ancienne dette</td>
                            </tr>`;
                        $('#tableFactureParPeriode tbody').append(ancienneDetteRow);

                        var restant = 0;
                        response.ancienneDette.forEach(function(item) {

                            var restant = Math.round(item.total_Qte) - Math.round(item
                                .total_restituee) - Math.round(item.total_facturee);



                            const row = `<tr>
                                            <td>${item.Libelle} Trous</td>
                                            <td>${numberFormat(item.total_Qte)}</td>
                                            <td>${numberFormat(item.total_restituee)}</td>
                                            <td>${numberFormat(item.total_facturee)}</td>
                                            <td>${numberFormat(restant)}</td>
                                        </tr>`;
                            $('#tableFactureParPeriode tbody').append(row);
                        });

                        const nouvelleDetteRow = `<tr class="font-weight-bold">
                                <td colspan="4" >Dette récente</td>
                            </tr>`;
                        $('#tableFactureParPeriode tbody').append(nouvelleDetteRow);
                        response.nouvelleDette.forEach(function(item) {

                            var restant = Math.round(item.total_Qte) - Math.round(item
                                .total_restituee) - Math.round(item.total_facturee);

                            const row = `<tr>
                                            <td>${item.Libelle} Trous</td>
                                            <td>${numberFormat(item.total_Qte)}</td>
                                            <td>${numberFormat(item.total_restituee)}</td>
                                            <td>${numberFormat(item.total_facturee)}</td>
                                            <td>${numberFormat(restant)}</td>

                                        </tr>`;
                            $('#tableFactureParPeriode tbody').append(row);
                        });

                        /*  const grandTotalRow = `<tr class="font-weight-bold">
                         <td colspan="6" >Total général</td>
                         <td >(${totalFactures} proforma(s))</td>
                         <td >${numberFormat(grandTotal)}</td>
                     </tr>`;
                         $('#tableFactureParPeriode tbody').append(grandTotalRow); */


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

                    var tableSituationClientData = [];
                    $('#tableFactureParPeriode tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Libelle: row.find('td').eq(0).text(),
                            total_Qte: row.find('td').eq(1).text(),
                            total_restituee: row.find('td').eq(2).text(),
                            total_facturee: row.find('td').eq(3).text(),
                            restant: row.find('td').eq(4).text(),
                        };
                        tableSituationClientData.push(rowData);
                    });

                    var exportData = {
                        tableSituationClientData: tableSituationClientData,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_situation_client',
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
                            a.download = 'situation_client.xlsx';
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

                var tableSituationClientData = [];
                $('#tableFactureParPeriode tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        Libelle: row.find('td').eq(0).text(),
                        total_Qte: row.find('td').eq(1).text(),
                        total_restituee: row.find('td').eq(2).text(),
                        total_facturee: row.find('td').eq(3).text(),
                        restant: row.find('td').eq(4).text(),
                    };
                    tableSituationClientData.push(rowData);
                });

                var exportData = {
                    tableSituationClientData: tableSituationClientData,
                    infoClient: ajaxResponse.infoClient,
                    infoAgence: ajaxResponse.infoAgence
                };


                $.ajax({
                    type: 'POST',
                    url: '/export_situation_client_pdf',
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


    @include('layouts.alert')
@endsection
