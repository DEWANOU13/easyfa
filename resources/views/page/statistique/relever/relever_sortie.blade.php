@extends('layouts.master', ['title' => 'Statistique relever sortie'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Relevé sortie',
        'infos2' => 'Relevé sortie',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cumPro-tab" data-bs-toggle="tab" data-bs-target="#cumPro"
                        type="button" role="tab" aria-controls="cumPro" aria-selected="false">Relevé sortie
                    </button>
                </li>


            </ul>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="cumPro" role="tabpanel" aria-labelledby="cumPro-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form1" action="{{ route('releverSortiereq') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence3">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Tous">Tous</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Categorie produit</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="categorie" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="categorie" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez une catégorie</option>
                                                <option value="Tous">Tous</option>
                                                @foreach ($categorie as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Libelle }}
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
                                                <button type="submit" id="AppliquerForm1" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError1" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                @can('excel-releve-sorti')
                    <button id="exportButtonMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('pdf-releve-sorti')
                    <button id="exportButtonMPP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan

                <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportExcel_MP" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="staticBackdrop1P" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportPDF_MP" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableReleverSortie" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <th style="{{ background_color_2() }}" scope="col">Réference</th>
                                    <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                    <th style="{{ background_color_2() }}" scope="col">Lundi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Mardi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Mercredi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Jeudi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Vendredi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Samedi</th>
                                    <th style="{{ background_color_2() }}" scope="col">Dimanche</th>
                                    <th style="{{ background_color_2() }}" scope="col">Sortie Moy. Jour.</th>
                                    <th style="{{ background_color_2() }}" scope="col">Sortie Moy. Sem.</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <div id="req_message1" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <script>
        $(document).ready(function() {
            var exportButtonMP = document.getElementById("exportButtonMP");
            var exportButtonMPP = document.getElementById("exportButtonMPP");
            var table = document.getElementById("tableReleverSortie").getElementsByTagName("tbody")[0];

            // Déclaration d'une variable globale pour stocker la réponse AJAX
            var ajaxResponse;

            // Fonction pour vérifier si le tableau est vide
            function checkTable() {
                if (table.rows.length === 0) {
                    exportButtonMP.disabled = true;
                    exportButtonMPP.disabled = true;
                    $('#req_message1').show();
                } else {
                    exportButtonMP.disabled = false;
                    exportButtonMPP.disabled = false;
                    $('#req_message1').hide();
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



            $('#form1').on('submit', function(e) {
                e
                    .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                var formData = $(this).serialize(); // Sérialisation des données du formulaire
                // console.log(formData);
                var dateDebut = new Date($('#dateDebut').val());
                var dateFin = new Date($('#dateFin').val());

                if (dateDebut > dateFin) {
                    $('#dateError1').show();
                    return;
                } else {
                    $('#dateError1').hide();
                }

                var $button = $('#AppliquerForm1');
                $button.addClass('loading');
                $button.prop('disabled', true);


                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'),
                    data: formData, // Données du formulaire sérialisées
                    success: function(response) {
                        console.log(response.listeReleverSortie);

                        ajaxResponse = response;

                        $('#tableReleverSortie tbody').empty();

                        response.listeReleverSortie.forEach(function(sortie) {


                            const row = `<tr style ="border: 1px solid #000; ">


                                            <td>${sortie.Reference}</td>
                                            <td>${sortie.Designation}</td>
                                            <td>${sortie.Lundi}</td>
                                            <td>${sortie.Mardi}</td>
                                            <td>${sortie.Mercredi}</td>
                                            <td>${sortie.Jeudi}</td>
                                            <td>${sortie.Vendredi}</td>
                                            <td>${sortie.Samedi}</td>
                                            <td>${sortie.Dimanche}</td>
                                            <td>${Math.round(sortie.SortieMoyenneParJour)}</td>
                                            <td>${Math.round(sortie.SortieHebdo)}</td>
                                        </tr>`;
                            $('#tableReleverSortie tbody').append(row);
                        });



                        if ($('#tableReleverSortie tbody tr').length === 0) {
                            alert("Aucune donnée disponible pour l'exportation.");
                        }
                        // $('#tableReleverSortie').DataTable();

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
            $('#exportExcel_MP').off('click').on('click', function() {
                var $button = $(this);

                if (ajaxResponse) {
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableReleverSortieData = [];
                    $('#tableReleverSortie tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Reference: row.find('td').eq(0).text(),
                            Designation: row.find('td').eq(1).text(),
                            Lundi: row.find('td').eq(2).text(),
                            Mardi: row.find('td').eq(3).text(),
                            Mercredi: row.find('td').eq(4).text(),
                            Jeudi: row.find('td').eq(5).text(),
                            Vendredi: row.find('td').eq(6).text(),
                            Samedi: row.find('td').eq(7).text(),
                            Dimanche: row.find('td').eq(8).text(),
                            SortieMoyenneParJour: row.find('td').eq(9).text(),
                            SortieHebdo: row.find('td').eq(10).text(),
                        };
                        tableReleverSortieData.push(rowData);
                    });

                    var exportData = {
                        tableReleverSortieData: tableReleverSortieData,
                        infoCategorie: ajaxResponse.infoCategorie,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_relever_sortie',
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
                            a.download = 'releve_sortie.xlsx';
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

            $('#exportPDF_MP').off('click').on('click', function() {
                var newWindow = null;

                //chargement
                var $button = $(this);
                $button.addClass('loading');
                $button.prop('disabled', true);
                $button.text('Exportation en cours...');


                var tableReleverSortieData = [];
                $('#tableReleverSortie tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        Reference: row.find('td').eq(0).text(),
                        Designation: row.find('td').eq(1).text(),
                        Lundi: row.find('td').eq(2).text(),
                        Mardi: row.find('td').eq(3).text(),
                        Mercredi: row.find('td').eq(4).text(),
                        Jeudi: row.find('td').eq(5).text(),
                        Vendredi: row.find('td').eq(6).text(),
                        Samedi: row.find('td').eq(7).text(),
                        Dimanche: row.find('td').eq(8).text(),
                        SortieMoyenneParJour: row.find('td').eq(9).text(),
                        SortieHebdo: row.find('td').eq(10).text(),
                    };
                    tableReleverSortieData.push(rowData);
                });

                var exportData = {
                    tableReleverSortieData: tableReleverSortieData,
                    infoCategorie: ajaxResponse.infoCategorie,
                    dateDebut: ajaxResponse.dateDebut,
                    dateFin: ajaxResponse.dateFin,
                    infoAgence: ajaxResponse.infoAgence
                };

                $.ajax({
                    type: 'POST',
                    url: '/export_relever_sortie_pdf',
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


        });
    </script>
@endsection
