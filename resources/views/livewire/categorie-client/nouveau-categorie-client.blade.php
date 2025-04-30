<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            <div class="d-flex flex-row-reverse bd-highlight">
                @canany(['modifier-categorie-client', 'creer-categorie-client', 'exporter-excel-categorie-client', 'imprimer-categorie-client'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-categorie-client')
                                <li>
                                    <a class="dropdown-item" type="button" data-bs-target="#createCategorieClient"
                                        data-bs-toggle="modal">Nouveau</a>
                                </li>
                            @endcan

                            @can('modifier-categorie-client')
                                <li>
                                    <a id="modifierLink" class="dropdown-item" type="button"
                                        data-bs-target="#editCategorieClient" data-bs-toggle="modal">Modifier</a>
                                </li>
                            @endcan

                            @can('exporter-excel-categorie-client')
                                <li>
                                    <a id="exportButton" class="dropdown-item" data-bs-toggle="modal" type="button"
                                        data-bs-target="#staticBackdropExcel">
                                        Exporter
                                    </a>
                                </li>
                            @endcan

                            @can('imprimer-categorie-client')
                                <li>
                                    <a id="importerLink" class="dropdown-item" type="button"
                                        data-bs-target="#imprimerCategorieClientPdf" data-bs-toggle="modal">Importer</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                @endcanany
            </div>

            {{-- Modal export categorie de client --}}
            <div class="modal fade" id="staticBackdropExcel" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la categorie des clents en Excel ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <button id="exportExcelCategorieClient" class="btn btn-sm btn-success"
                                data-bs-dismiss="modal">Oui Exporter en Excel</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal pour l'importation des categories de client en pdf --}}
            <div class="modal fade" id="imprimerCategorieClientPdf" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment imprimer les données en PDF ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                            <button id="importPDF_Imp" target="_blank" class="btn btn-sm btn-success">Oui Exporter en
                                PDF</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des catégories client</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categorieClientTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Catégorie client</th>
                                    <th>Enregistrer par</th>
                                    <th>Modifier par</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($listeCategorieClient as $key => $categorieClient)
                                    <tr class="clickable-row " data-id="{{ $categorieClient->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $categorieClient->Libelle }}</td>
                                        <td class="magasin-nom">{{ $categorieClient->Enregistrer_par }}</td>
                                        <td class="magasin-nom">{{ $categorieClient->Modifier_par }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

    @include('livewire.categorie-client.modal')

    {{-- Exportation en fichier excel de la liste des categories de client --}}
    <script>
        $(document).ready(function() {
            $('#exportExcelCategorieClient').on('click', function() {
                var tableCategorieClientData = [];
                $('#categorieClientTable tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        numero: row.find('td').eq(0).text(),
                        categorie_client: row.find('td').eq(1).text(),
                        enregistre_par: row.find('td').eq(2).text(),
                        modifie_par: row.find('td').eq(3).text()
                    };
                    tableCategorieClientData.push(rowData);
                });


                var exportData = {
                    tableCategorieClientData: tableCategorieClientData
                };
                // console.log(exportData);

                $.ajax({
                    type: 'POST',
                    url: '/export_excel_categorie_client',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'liste_categorie_client.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(status);
                        console.log(error);
                    }
                });
            });
        });
    </script>

    {{-- Importation en fichier PDF de la liste des categorie de client  --}}
    <script>
        $('#importPDF_Imp').off('click').on('click', function() {
            var newWindow = null;

            //chargement
            var $button = $(this);
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var tableCategorieClientData = [];
            $('#categorieClientTable tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    numero: row.find('td').eq(0).text(),
                    categorie_client: row.find('td').eq(1).text(),
                    enregistre_par: row.find('td').eq(2).text(),
                    modifie_par: row.find('td').eq(3).text()
                };
                tableCategorieClientData.push(rowData);
            });

            var exportData = {
                tableCategorieClientData: tableCategorieClientData,
            };


            $.ajax({
                type: 'POST',
                url: '/import_pdf_categorie_client',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
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

                    $('#imprimerCategorieClientPdf').modal('hide');
                },

                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');
                }
            });

        });
    </script>

    <script>
        window.addEventListener('actionModalcreateCategorieClient', event => {
            $("#createCategorieClient").modal('hide');
            $("#confirmcreateCategorieClient").modal('show');
        });
        window.addEventListener('actionModalUpdateCategorieClient', event => {
            $("#editCategorieClient").modal('hide');
            $("#confirmEditCategorieClient").modal('show');
        });
    </script>

    <script>
        const formIds = ['updateCategorieClient', 'createCategorieClient'];

        function preventEnterSubmission(formId) {
            document.getElementById(formId).addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }
        formIds.forEach(preventEnterSubmission);
    </script>
</section>
