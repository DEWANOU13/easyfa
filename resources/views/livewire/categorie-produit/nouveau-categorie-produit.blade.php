<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            <div class="d-flex flex-row-reverse bd-highlight">
                @canany(['creer-categorie-produit', 'modifier-categorie-produit'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-categorie-produit')
                                <li><a class="dropdown-item" type="button" data-bs-target="#createCategorie"
                                        data-bs-toggle="modal">Nouveau</a></li>
                            @endcan
                            @can('modifier-categorie-produit')
                                <li>
                                    <a id="modifierLink" class="dropdown-item" type="button" data-bs-target="#editCategorie"
                                        data-bs-toggle="modal">Modifier</a>
                                </li>
                            @endcan
                            @can('exporter-categorie-produit')
                                <li>
                                    {{-- <form action="{{ route('exportCategorieProduit') }}" method="POST">
                                    @csrf --}}
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#confirmExportCategorie">Exporter</button>
                                    {{-- <button c!lass="dropdown-item" type="submit">Exporter</button> --}}
                                    {{-- </form> --}}
                                </li>
                            @endcan

                            @can('imprimer-categorie-produit')
                                <li>
                                    {{-- <form action="{{ route('imprimerCategorieProduit') }}" method="POST" target="_blank">
                                    @csrf --}}
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#confirmPDFCategorie">Imprimer</button>
                                    {{-- <button class="dropdown-item" type="submit">Imprimer</button> --}}
                                    {{-- </form> --}}
                                </li>
                            @endcan
                        </ul>
                        <div wire:ignore.self class="modal fade" id="confirmExportCategorie" data-bs-backdrop="static"
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
                                        <form action="{{ route('exportCategorieProduit') }}" method="POST">
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
            <div class="modal fade" id="confirmExportCategorie" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la liste des catégories en Excel ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('exportCategorieProduit') }}" method="Post" target="_blank">
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
            <div class="modal fade" id="confirmPDFCategorie" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la liste des catégories en PDF ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('imprimerCategorieProduit') }}" method="Post" target="_blank">
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
                    <h3 class="mt-2  d-inline-block text-dark">Liste des catégories produits</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Categorie</th>
                                    <th>Enregister_par</th>
                                    <th>Modifier_par</th>
                                    {{-- <th style="width: 5%">Statut</th> --}}
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($listeCategorieProduit as $key => $categorieProduit)
                                    <tr class="clickable-row" data-id="{{ $categorieProduit->id }}">

                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $categorieProduit->Libelle }}</td>
                                        <td class="magasin-nom">{{ $categorieProduit->name }}</td>
                                        <td class="magasin-nom">{{ $categorieProduit->_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @include('livewire.categorie-produit.modal')
    <script>
        window.addEventListener('actionModalCreateCategorie', event => {
            $("#createCategorie").modal('hide');
            $("#confirmCreateCategorie").modal('show');
        });
        window.addEventListener('actionModalUpdateCategorie', event => {
            $("#editCategorie").modal('hide');
            $("#confirmEditCategorie").modal('show');
        });
    </script>
</section>
