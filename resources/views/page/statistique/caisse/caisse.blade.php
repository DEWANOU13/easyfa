@extends('layouts.master', ['title' => 'Statistique caisse'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Caisse',
        'infos2' => 'Caisse',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="rappPres-tab" data-bs-toggle="tab" data-bs-target="#rappPres"
                        type="button" role="tab" aria-controls="rappPres" aria-selected="false">Rapport Caisse
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="rappPres" role="tabpanel" aria-labelledby="rappPres-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form2" action="{{ route('rapportCaisseReq') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence4">
                                            <option value="Tous">Tous</option>

                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Utilisateur</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="user" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="user" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez une utilisateur</option>
                                                <option value="Tous">Tous</option>
                                                @foreach ($utilisateur as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Du</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateDebut" class="form-control"
                                                id="dateDebut" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateFin" class="form-control" id="dateFin"
                                                max="{{ date('Y-m-d\TH:i') }}" required aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span>&nbsp</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm2" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError2" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                @can('statistique-caisse-excel')
                    <button id="exportButtonMPPres" data-bs-toggle="modal" data-bs-target="#staticBackdrop2"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-caisse-pdf')
                    <button id="exportButtonMPresP" data-bs-toggle="modal" data-bs-target="#staticBackdrop2P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan

                <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MPres" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="staticBackdrop2P" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF_MPres" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableRapportPrestation" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">

                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Date</th>
                                        <th style="{{ background_color_2() }}" scope="col">Ref</th>
                                        <th style="{{ background_color_2() }}" scope="col">Type</th>
                                        <th style="{{ background_color_2() }}" scope="col">Statut</th>
                                        <th style="{{ background_color_2() }}" scope="col">Montant</th>
                                        <th style="{{ background_color_2() }}" scope="col">D/R</th>
                                        <th style="{{ background_color_2() }}" scope="col">Ref reglement</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <div id="req_message2" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <script>
            const formIds = ['form2'];

            // Fonction pour empêcher la soumission du formulaire à l'aide de la touche "Entrée"
            function preventEnterSubmission(formId) {
                document.getElementById(formId).addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });
            }

            // Appliquer la fonction à chaque formulaire de la liste
            formIds.forEach(preventEnterSubmission);
        </script>

        {{-- RAPPORT PAR PRESTATION --}}
        <script>
            $(document).ready(function() {
                var exportButtonMPPres = document.getElementById("exportButtonMPPres");
                var exportButtonMPPresP = document.getElementById("exportButtonMPPresP");
                var table = document.getElementById("tableRapportPrestation").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMPPres.disabled = true;
                        exportButtonMPresP.disabled = true;
                        $('#req_message2').show();
                    } else {
                        exportButtonMPPres.disabled = false;
                        exportButtonMPresP.disabled = false;
                        $('#req_message2').hide();
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



                $('#form2').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError2').show();
                        return;
                    } else {
                        $('#dateError2').hide();
                    }
                    /*
                    var $button = $('#AppliquerForm1');
                    $button.addClass('loading');
                    $button.prop('disabled', true); */


                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableRapportPrestation tbody').empty();
                            var total_recette = 0;
                            var total_depense = 0;
                            var total_reglement = 0;
                            response.listerpportCaisse.forEach(function(item) {

                                if (item.designation_depense != null) {

                                    if (item.statut != 'ANNULEE') {
                                        total_depense += Math.round(item.montant);
                                    }
                                }
                                if (item.designation_recette != null) {

                                    if (item.statut != 'ANNULEE') {
                                        total_recette += Math.round(item.montant);
                                    }
                                }


                                const row = `
                                            <tr>
                                                <td>${formatDate(item.created_at)}</td>
                                                <td>${item.reference_operation}</td>
                                                <td>${item.type}</td>
                                                <td>${item.statut}</td>
                                                <td>${numberFormat(item.montant)}</td>
                                                <td>
                                                    ${item.designation_recette
                                                        ? `Recette - ${item.designation_recette}`
                                                        : item.designation_depense
                                                            ? `Depense - ${item.designation_depense}`
                                                            : ''}
                                                </td>
                                                <td>${item.reference_reglement ? item.reference_reglement : ''}</td>
                                            </tr>`;
                                $('#tableRapportPrestation tbody').append(row);


                            });
                            const totaldepenseRow = `<tr class="font-weight-bold">
                                    <td colspan="" >Total dépense</td>
                                    <td colspan="" >${numberFormat(total_depense)}</td>
                                    <td colspan="" >Total Recette</td>
                                    <td colspan="" >${numberFormat(total_recette)}</td>


                            </tr>`;
                            $('#tableRapportPrestation tbody').append(totaldepenseRow);



                            if ($('#tableRapportPrestation tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }


                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        }
                    });
                });
                $('#exportExcel_MPres').off('click').on('click', function() {
                    var $button = $(this);

                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableRapportCaisseData = [];
                        $('#tableRapportPrestation tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                created_at: row.find('td').eq(0).text(),
                                reference_operation: row.find('td').eq(1).text(),
                                type: row.find('td').eq(2).text(),
                                statut: row.find('td').eq(3).text(),
                                montant: row.find('td').eq(4).text(),
                                designation_recette: row.find('td').eq(5).text(),
                                reference_reglement: row.find('td').eq(6).text(),
                            };
                            tableRapportCaisseData.push(rowData);
                        });

                        var exportData = {
                            tableRapportCaisseData: tableRapportCaisseData,
                            infoUser: ajaxResponse.infoUser,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence
                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_rapportCaisse',
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
                                a.download = 'Rapport_caisse_.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                // Réactiver le bouton et retirer le spinner après la complétion
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop1').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                // Réactiver le bouton et retirer le spinner après l'échec
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MPres').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');


                    var tableRapportCaisseData = [];
                    $('#tableRapportPrestation tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            created_at: row.find('td').eq(0).text(),
                            reference_operation: row.find('td').eq(1).text(),
                            type: row.find('td').eq(2).text(),
                            statut: row.find('td').eq(3).text(),
                            montant: row.find('td').eq(4).text(),
                            designation_recette: row.find('td').eq(5).text(),
                            reference_reglement: row.find('td').eq(6).text(),
                        };
                        tableRapportCaisseData.push(rowData);
                    });

                    var exportData = {
                        tableRapportCaisseData: tableRapportCaisseData,
                        infoUser: ajaxResponse.infoUser,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_rapport_caisse_pdf',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = window.URL.createObjectURL(blob);

                            // Ouvrir un nouvel onglet avec le PDF
                            newWindow = window.open(url, '_blank');

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                            $('#staticBackdrop1P').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Masquer l'animation
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
