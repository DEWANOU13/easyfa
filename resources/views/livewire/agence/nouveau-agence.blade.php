<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            @php
                $nbrAgence = auth()->user()->agences->count() > 1;
                $agencesUser = auth()->user()->agences->all('*');
            @endphp

            <div class="d-flex {{ $nbrAgence ? 'justify-content-between align-items-center' : 'flex-row-reverse bd-highlight' }}">
                @if (auth()->user()->agences->count() > 1)
                    @if (session('site_id'))
                        <form action="" method="GET" class="d-flex gap-3 mb-2">
                            <div>
                                <select name="agenceFilter_id" class="js-single" style="width: 150px;" id="groupe_id" required>
                                    <option value="">Filtre Agence</option>
                                    @if (session('site_id'))
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

                @canany(['creer-agence', 'modifier-agence'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white"
                            style="height: 28px; line-height: 1; background: #0d6efd;" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-agence')
                                <li><a class="dropdown-item" type="button" data-bs-target="#createAgence"
                                        data-bs-toggle="modal">Nouveau</a></li>
                            @endcan
                            @can('modifier-agence')
                                <li><a id="modifierLink" class="dropdown-item" type="button" data-bs-target="#editAgence"
                                        data-bs-toggle="modal">Modifier</a></li>
                            @endcan
                        </ul>
                    </div>
                @endcanany
            </div>

            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des agences</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Nom agence</th>
                                    <th>Titre signataire</th>
                                    <th>Nom signataire</th>
                                    <th>Enregistrer par</th>
                                    <th>Modifié par</th>
                                    <th style="width: 5%">Statut</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($listeAgences as $key => $agence)
                                    <tr class="clickable-row " data-id="{{ $agence->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $agence->NomAgence }}</td>
                                        <td class="magasin-nom">{{ $agence->titre_signataire_facture }}</td>
                                        <td class="magasin-nom">{{ $agence->nom_signataire }}</td>
                                        <td class="magasin-nom">{{ $agence->userCree ? $agence->userCree->name : '' }}</td>
                                        <td class="magasin-nom">{{ $agence->userMod ? $agence->userMod->name : '' }}</td>
                                        <td>
                                            @if ($agence->EnActivite == 1)
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
        </div> <!-- end col -->
    </div> <!-- end row -->

    @include('livewire.agence.modal')

    <script>
        window.addEventListener('actionModalcreateAgence', event => {
            $("#createAgence").modal('hide');
            $("#confirmcreateAgence").modal('show');
        });
        window.addEventListener('actionModalUpdateAgence', event => {
            $("#editAgence").modal('hide');
            $("#confirmEditAgence").modal('show');
        });
    </script>
</section>
