@extends('layouts.master', ['title' => 'Facture FF & FLF'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Facture FF & FLF',
        'infos2' => 'Facture FF & FLF',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="facture-tab" data-bs-toggle="tab" data-bs-target="#facture"
                        type="button" role="tab" aria-controls="facture" aria-selected="true">Facture
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="cumj-tab" data-bs-toggle="tab" data-bs-target="#cumj" type="button"
                        role="tab" aria-controls="cumj" aria-selected="true">Ligne Facture
                    </button>
                </li>



            </ul>
        </div>
        <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active " id="facture" role="tabpanel" aria-labelledby="facture-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form2" action="{{ route('factureFF') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence2">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
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
                                        <span>&nbsp;</span>
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
                <button id="exportButtonMJ" data-bs-toggle="modal" data-bs-target="#staticBackdrop2"
                    class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en CSV</button>
                {{-- <button id="exportButtonMJP" data-bs-toggle="modal" data-bs-target="#staticBackdrop2P"
                    class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button> --}}
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
                                Voulez-vous vraiment exporter les données en csv?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MJ" class="btn btn-sm btn-success">Oui Exporter en
                                    CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{--     <div class="modal fade" id="staticBackdrop2P" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportPDF_MJ" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div> --}}



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableFacture" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>

                                        <th style="{{ background_color_2() }}" scope="col">Date</th>
                                        <th style="{{ background_color_2() }}" scope="col">N°Facture</th>
                                        <th style="{{ background_color_2() }}" scope="col">Signature&nbsp;MEcef</th>
                                        <th style="{{ background_color_2() }}" scope="col">Client</th>
                                        <th style="{{ background_color_2() }}" scope="col">Adresse</th>
                                        <th style="{{ background_color_2() }}" scope="col">N°IFU</th>
                                        <th style="{{ background_color_2() }}" scope="col">Pays&nbsp;de&nbsp;livraison
                                        </th>
                                        <th style="{{ background_color_2() }}" scope="col">Date&nbsp;de&nbsp;livraison
                                        </th>
                                        <th style="{{ background_color_2() }}" scope="col">Montant&nbsp;HT</th>
                                        <th style="{{ background_color_2() }}" scope="col">Montant&nbsp;TTC</th>
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
            <div class="tab-pane fade" id="cumj" role="tabpanel" aria-labelledby="cumj-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form3" action="{{ route('facctureFLF') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence3">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
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
                                            <input type="datetime-local" name="dateFin" class="form-control"
                                                id="dateFin" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span>&nbsp;</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm3" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError3" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                <button id="exportButtonMM" data-bs-toggle="modal" data-bs-target="#staticBackdrop3"
                    class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en CSV</button>
                {{-- <button id="exportButtonMMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop3P"
                    class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button> --}}
                <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données en csv ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MM" class="btn btn-sm btn-success">Oui Exporter en
                                    CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="staticBackdrop3P" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportPDF_MM" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableFactureFlF" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>

                                        <th style="{{ background_color_2() }}" scope="col">Date</th>
                                        <th style="{{ background_color_2() }}" scope="col">N°Facture</th>
                                        <th style="{{ background_color_2() }}" scope="col">Désignation</th>
                                        <th style="{{ background_color_2() }}" scope="col">Famille</th>
                                        <th style="{{ background_color_2() }}" scope="col">Qté</th>
                                        <th style="{{ background_color_2() }}" scope="col">Prix Unitaire Net</th>
                                        <th style="{{ background_color_2() }}" scope="col">Montant HT</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <div id="req_message3" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <script>
            const formIds = ['form1', 'form2', 'form3', 'form4'];

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

        {{-- marge par jour --}}
        <script>
            $(document).ready(function() {
                var exportButtonMJ = document.getElementById("exportButtonMJ");
                //var exportButtonMJP = document.getElementById("exportButtonMJP");
                var table = document.getElementById("tableFacture").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMJ.disabled = true;
                        //exportButtonMJP.disabled = true;
                        $('#req_message2').show();
                    } else {
                        exportButtonMJ.disabled = false;
                        // exportButtonMJP.disabled = false;
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
                    var $button = $('#AppliquerForm2');
                    $button.addClass('loading');
                    $button.prop('disabled', true);

                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableFacture tbody').empty();

                            var montantAchatTotalCategorie = 0;
                            var montantVenteTotalCategorie = 0;
                            var totalMargeCategorie = 0;
                            //var totalTauxCategorie = 0;
                            response.listeFacture.forEach(function(item) {


                                var row = `
                                <tr>
                                <td>${formatDate(item.Date_facture)}</td>
                                <td>${item.Reference_facture}</td>
                                <td>${item.Code_signature}</td>
                                <td>${item.Denomination_sociale}</td>
                                <td>${item.Adresse_client}</td>
                                <td>${item.Numero_ifu}</td>
                                <td>${item.Pays}</td>
                                <td>${formatDate(item.Date_facture)}</td>
                                <td>${item.TotalGlobalHT}</td>
                                <td>${item.TotalGlobalTVA + item.TotalGlobalHT }</td>
                                </tr>`;
                                $('#tableFacture tbody').append(row);
                            });


                            if ($('#tableFacture tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();

                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MJ').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableFactureData = [];
                        $('#tableFacture tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Date_facture: row.find('td').eq(0).text(),
                                Reference_facture: row.find('td').eq(1).text(),
                                Code_signature: row.find('td').eq(2).text(),
                                Denomination_sociale: row.find('td').eq(3).text(),
                                Adresse_client: row.find('td').eq(4).text(),
                                Numero_ifu: row.find('td').eq(5).text(),
                                Pays_livraison: row.find('td').eq(6).text(),
                                Date_livraison: row.find('td').eq(7).text(),
                                MontantHt: row.find('td').eq(8).text(),
                                MontantTTC: row.find('td').eq(9).text(),
                            };
                            tableFactureData.push(rowData);
                        });

                        var exportData = {
                            tableFactureData: tableFactureData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_factureFF_csv',
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
                                a.download = 'facture_FF.csv';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en CSV');
                                $('#staticBackdrop2').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en CSV');

                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                /*    $('#exportPDF_MJ').off('click').on('click', function() {

                            var newWindow = null;

                            //chargement
                            var $button = $(this);
                            $button.addClass('loading');
                            $button.prop('disabled', true);
                            $button.text('Exportation en cours...');

                            var tableFactureData = [];
                            $('#tableFacture tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    Date_facture: row.find('td').eq(0).text(),
                                    Reference_facture: row.find('td').eq(1).text(),
                                    Code_signature: row.find('td').eq(2).text(),
                                    Denomination_sociale: row.find('td').eq(3).text(),
                                    Adresse_client: row.find('td').eq(4).text(),
                                    Numero_ifu: row.find('td').eq(5).text(),
                                    Pays_livraison: row.find('td').eq(6).text(),
                                    Date_livraison: row.find('td').eq(7).text(),
                                    TotalGlobalHT: row.find('td').eq(8).text(),
                                    TotalTTC: row.find('td').eq(9).text(),

                                };
                                tableFactureData.push(rowData);
                            });

                            var exportData = {
                                tableFactureData: tableFactureData,
                                dateDebut: ajaxResponse.dateDebut,
                                dateFin: ajaxResponse.dateFin,
                                infoAgence: ajaxResponse.infoAgence

                            };

                            $.ajax({
                                type: 'POST',
                                url: '/export_factureFF_csv',
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
                                    $('#staticBackdrop2P').modal('hide');
                                },

                                error: function(xhr, status, error) {
                                    console.error(error);

                                    $button.removeClass('loading');
                                    $button.prop('disabled', false);
                                    $button.text('Oui, Exporter en PDF');
                                }
                            });

                        });
         */


                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0

                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');
                    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                }
            });
        </script>

        {{-- marge par mois --}}
        <script>
            $(document).ready(function() {
                var exportButtonMM = document.getElementById("exportButtonMM");
                //var exportButtonMMP = document.getElementById("exportButtonMMP");
                var table = document.getElementById("tableFactureFlF").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMM.disabled = true;
                        //exportButtonMMP.disabled = true;
                        $('#req_message3').show();
                    } else {
                        exportButtonMM.disabled = false;
                        //exportButtonMMP.disabled = false;
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



                $('#form3').on('submit', function(e) {
                    e
                        .preventDefault();
                    var formData = $(this).serialize();
                    // console.log(formData);
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
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableFactureFlF tbody').empty();

                            response.listeLigneFacture.forEach(function(item) {



                                var row = `
                                <tr>
                                <td>${formatDate(item.Date_facture)}</td>
                                <td>${item.Reference_facture}</td>
                                <td>${item.Produit_designation}</td>
                                <td>${item.Code_lettre}</td>
                                <td>${item.Qte}</td>
                                <td>${item.Prix_revient}</td>
                                <td>${item.total_ht}</td>
                                </tr>`;
                                $('#tableFactureFlF tbody').append(row);
                            });


                            if ($('#tableFactureFlF tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MM').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableFactureFlFData = [];
                        $('#tableFactureFlF tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Date_facture: row.find('td').eq(0).text(),
                                Reference_facture: row.find('td').eq(1).text(),
                                Produit_designation: row.find('td').eq(2).text(),
                                Code_lettre: row.find('td').eq(3).text(),
                                Qte: row.find('td').eq(4).text(),
                                Prix_net: row.find('td').eq(5).text(),
                                total_ht: row.find('td').eq(6).text(),
                            };
                            tableFactureFlFData.push(rowData);
                        });

                        var exportData = {
                            tableFactureFlFData: tableFactureFlFData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_factureFLF_csv',
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
                                a.download = 'facture_flf.csv';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en CSV');
                                $('#staticBackdrop3').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en CSV');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

            /*     $('#exportPDF_MM').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');
                    var tableFactureFlFData = [];
                    $('#tableFactureFlF tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Date: row.find('td').eq(0).text(),
                            montantVente: row.find('td').eq(1).text(),
                            montantAchat: row.find('td').eq(2).text(),
                            marge: row.find('td').eq(3).text(),
                            taux: row.find('td').eq(4).text(),
                        };
                        tableFactureFlFData.push(rowData);
                    });

                    var exportData = {
                        tableFactureFlFData: tableFactureFlFData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence

                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_marge_par_mois_pdf',
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
                            $('#staticBackdrop3P').modal('hide');
                        },

                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                        }
                    });

                }); */



                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0

                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');
                    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                }
            });
        </script>

        {{-- marge par client --}}
        <script>
            $(document).ready(function() {
                var exportButtonMC = document.getElementById("exportButtonMC");
                var exportButtonMCP = document.getElementById("exportButtonMCP");
                var table = document.getElementById("tableMargeParClient").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMC.disabled = true;
                        exportButtonMCP.disabled = true;
                        $('#req_message4').show();
                    } else {
                        exportButtonMC.disabled = false;
                        exportButtonMCP.disabled = false;
                        $('#req_message4').hide();
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



                $('#form4').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError4').show();
                        return;
                    } else {
                        $('#dateError4').hide();
                    }
                    var $button = $('#AppliquerForm4');
                    $button.addClass('loading');
                    $button.prop('disabled', true);

                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParClient tbody').empty();

                            var montantAchatTotalCategorie = 0;
                            var montantVenteTotalCategorie = 0;
                            var totalMargeCategorie = 0;
                            //var totalTauxCategorie = 0;
                            response.listeVente.forEach(function(item) {
                                var montantVenteArrondi = Math.round(item.Prix_vente);
                                montantVenteTotalCategorie += montantVenteArrondi;

                                var montantAchatArrondi = Math.round(item.Prix_achat);
                                montantAchatTotalCategorie += montantAchatArrondi;

                                var marge = montantVenteArrondi - montantAchatArrondi;
                                totalMargeCategorie += marge;

                                var taux = montantAchatArrondi === 0 ? 0 : (marge /
                                    montantAchatArrondi * 100);

                                // totalTauxCategorie += taux;

                                var row = `
                                <tr>
                                <td>${item.Denomination_sociale}</td>
                                <td>${numberFormat(montantVenteArrondi)}</td>
                                <td>${numberFormat(montantAchatArrondi)}</td>
                                <td>${numberFormat(marge)}</td>
                                <td>${numberFormat(taux)}%</td>
                                </tr>`;
                                $('#tableMargeParClient tbody').append(row);
                            });
                            $('#tableMargeParClient tbody').append(
                                `<tr>
                                <td>TOTAL</td>
                                <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                <td>${numberFormat(totalMargeCategorie)}</td>
                                <td> ${montantAchatTotalCategorie === 0 ? '0' : numberFormat(totalMargeCategorie / montantAchatTotalCategorie * 100)}%</td>
                            </tr>`
                            );

                            if ($('#tableMargeParClient tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MC').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParClientData = [];
                        $('#tableMargeParClient tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Denomination_sociale: row.find('td').eq(0).text(),
                                montantVente: row.find('td').eq(1).text(),
                                montantAchat: row.find('td').eq(2).text(),
                                marge: row.find('td').eq(3).text(),
                                taux: row.find('td').eq(4).text(),
                            };
                            tableMargeParClientData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParClientData: tableMargeParClientData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoClient: ajaxResponse.infoClient,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_marge_par_client',
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
                                a.download = 'marge_par_client.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop4').modal('hide');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MC').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableMargeParClientData = [];
                    $('#tableMargeParClient tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Denomination_sociale: row.find('td').eq(0).text(),
                            montantVente: row.find('td').eq(1).text(),
                            montantAchat: row.find('td').eq(2).text(),
                            marge: row.find('td').eq(3).text(),
                            taux: row.find('td').eq(4).text(),
                        };
                        tableMargeParClientData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParClientData: tableMargeParClientData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence

                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_marge_par_client_pdf',
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
                            $('#staticBackdrop4P').modal('hide');
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
                    const options = {
                        month: 'long',
                        year: 'numeric'
                    };
                    const formattedDate = date.toLocaleDateString('fr-FR', options);

                    // Mettre la première lettre en majuscule
                    return formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
                }
            });
        </script>
    @endsection
