<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            <div class="d-flex flex-row-reverse bd-highlight">
                @canany(['creer-unite-comptage', 'modifier-unite-comptage', 'exporter-excel-unite-comptage', 'imprimer-unite-comptage'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-unite-comptage')
                                <li>
                                    <a class="dropdown-item" type="button" data-bs-target="#createUniteComptage"
                                        data-bs-toggle="modal">Nouveau</a>
                                </li>
                            @endcan
                            @can('modifier-unite-comptage')
                                <li>
                                    <a id="modifierLink" class="dropdown-item" type="button"
                                        data-bs-target="#editUniteComptage" data-bs-toggle="modal">Modifier</a>
                                </li>
                            @endcan
                            @can('exporter-excel-unite-comptage')
                                <li>
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#confirmExportUnite">Exporter</button>
                                </li>
                            @endcan
                            @can('imprimer-unite-comptage')
                                <li>
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#confirmImprimerUnite">Imprimer</button>
                                </li>
                            @endcan
                        </ul>
                        <div wire:ignore.self class="modal fade" id="confirmExportUnite" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCreateCategorieLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="confirmCreateCategorieLabel">Demande de
                                            confirmation </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment sauvegarder ces informations ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Non</button>
                                        <form action="{{ route('exportUniteComptage') }}" method="POST">
                                            @csrf
                                            <button id="validerExport" type="submit" class="btn btn-success">Oui, je
                                                valide</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcanany
            </div>


            {{-- Modal export liste de categorie produit --}}
            <div class="modal fade" id="confirmExportUnite" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la liste des unités de comptage en Excel ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('exportUniteComptage') }}" method="Post" target="_blank">
                                @csrf
                                <button id="exportExcelClient" class="btn btn-sm btn-success"
                                    data-bs-dismiss="modal">Oui
                                    Exporter en Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal PDF liste de categorie produit --}}
            <div class="modal fade" id="confirmImprimerUnite" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la liste des unités de comptage en PDF ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('imprimerUniteComptage') }}" method="Post" target="_blank">
                                @csrf
                                <button id="exportExcelClient" class="btn btn-sm btn-success"
                                    data-bs-dismiss="modal">Oui
                                    Exporter en Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des unités de comptage</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Enregister par </th>
                                    <th>Modifier par </th>
                                    {{-- <th style="width: 5%">Statut</th> --}}
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($listeUniteComptage as $key => $uniteComptage)
                                    <tr class="clickable-row" data-id="{{ $uniteComptage->id }}">

                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $uniteComptage->Code }}</td>
                                        <td class="magasin-nom">{{ $uniteComptage->Libelle }}</td>
                                        <td class="magasin-nom">{{ $uniteComptage->name }}</td>
                                        <td class="magasin-nom">{{ $uniteComptage->_name }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

    @include('livewire.unite-comptage.modal')

    <script>
        window.addEventListener('actionModalCreateUniteComptage', event => {
            $("#createUniteComptage").modal('hide');
            $("#confirmCreateUniteComptage").modal('show');
        });
        window.addEventListener('actionModalUpdateUniteComptage', event => {
            $("#editUniteComptage").modal('hide');
            $("#confirmEditUniteComptage").modal('show');
        });
    </script>
</section>
