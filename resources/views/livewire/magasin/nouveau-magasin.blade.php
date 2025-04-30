<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            @php
                $nbrAgence = auth()->user()->agences->count() > 1;
                $agencesUser = auth()->user()->agences->all('*');
            @endphp

            <div
                class="d-flex {{ session('site_id') == 1 ? 'justify-content-between align-items-center' : 'flex-row-reverse bd-highlight' }}">
                @if (auth()->user()->agences->count() > 1)
                    @if (session('site_id') == 1)
                        <form id="magasinnForm" action="" method="GET" class="d-flex gap-3 mb-2">
                            <div>
                                <select name="agenceFilter_id" class="js-single" style="width: 150px;" id="groupe_id"
                                    required>
                                    <option value="">Filtre Agence</option>
                                    @if (session('site_id') == 1)
                                        <option value="0">Tous</option>
                                    @endif
                                    @foreach ($agencesUser as $agenceUser)
                                        <option
                                            {{ isset($_GET['agenceFilter_id']) && $_GET['agenceFilter_id'] == $agenceUser->agence->id ? 'selected' : '' }}
                                            value="{{ $agenceUser->agence->id }}">{{ $agenceUser->agence->NomAgence }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary" style="height: 28px; line-height: 1;">Filtre</button>
                            </div>
                        </form>
                    @endif
                @endif

                @canany(['creer-magasin', 'modifier-magasin'])
                    <div class="dropdown mb-2">

                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-magasin')
                                <li><a class="dropdown-item" type="button" data-bs-target="#createMagasin"
                                        data-bs-toggle="modal">Nouveau</a></li>
                            @endcan
                            @can('modifier-magasin')
                                <li><a id="modifierLink" class="dropdown-item" type="button" data-bs-target="#editMagasin"
                                        data-bs-toggle="modal">Modifier</a></li>
                            @endcan

                            <li>
                                <button class="dropdown-item" data-bs-toggle="modal" type="button"
                                    data-bs-target="#staticBackdropMagasinExcel"> Exporter</button>
                            </li>
                            <li>
                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                    data-bs-target="#staticBackdropMagasinImprimer">Imprimer</button>
                            </li>
                        </ul>
                    </div>
                @endcanany
            </div>

            {{-- Modal export liste de magasin --}}
            <div class="modal fade" id="staticBackdropMagasinExcel" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment exporter les données de la liste des magasins en Excel ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('exportMagasins') }}" method="Post">
                                @csrf
                                <button id="exportExcelClient" class="btn btn-sm btn-success"
                                    data-bs-dismiss="modal">Oui
                                    Exporter en Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal imprimer liste de magasin --}}
            <div class="modal fade" id="staticBackdropMagasinImprimer" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropMagasinImprimer" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment imprimer les données de la liste des magasins en PDF ?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form action="{{ route('imprimerMagasins') }}" method="Post" target="_blank">
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
                    <h3 class="mt-2  d-inline-block text-dark">Liste des magasins</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Magasin</th>
                                    <th>Agence</th>
                                    <th>Créé par</th>
                                    <th>Modifié par</th>
                                    <th style="width: 5%">Statut</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($listeMagasin as $key => $magasin)
                                    <tr class="clickable-row" data-id="{{ $magasin->id }}">

                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $magasin->NomMagasin }}</td>
                                        <td class="">{{ $magasin->agence->NomAgence }}</td>
                                        <td class="">{{ $magasin->userCree ? $magasin->userCree->name : '' }}
                                        </td>
                                        <td class="">
                                            {{ $magasin->userModifi ? $magasin->userModifi->name : '' }}
                                        </td>
                                        <td>
                                            @if ($magasin->Statut_Magasin == 1)
                                                <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                                            @else
                                                <i class="fa fa-check-circle text-danger" aria-hidden="true"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.magasin.modal')

    <script>
        window.addEventListener('actionModalCreateMagasin', event => {
            $("#createMagasin").modal('hide');
            $("#confirmCreateMagasin").modal('show');
        });
        window.addEventListener('actionModalUpdateMagasin', event => {
            $("#editMagasin").modal('hide');
            $("#confirmEditMagasin").modal('show');
        });
    </script>
    <script>
        const formIds = ['createMagasinForm'];

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
