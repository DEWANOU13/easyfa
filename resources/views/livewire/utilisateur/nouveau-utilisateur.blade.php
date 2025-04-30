<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            <div class="d-flex flex-row-reverse bd-highlight">
                @canany(['creer-user', 'modifier-user'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-user')
                            <li><a class="dropdown-item" type="button" data-bs-target="#createUtilisateur"
                                data-bs-toggle="modal">Nouveau</a></li>
                            @endcan
                            @can('modifier-user')
                            <li><a id="modifierLink" class="dropdown-item" type="button" data-bs-target="#editUtilisateur"
                                data-bs-toggle="modal">Modifier</a></li>
                            @endcan  
                        </ul>
                    </div>
                @endcanany
            </div>
            <div class="card m-b-30" wire:ignore>
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des utilisateurs</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="magasinsTable"
                            class="datatable table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Nom</th>
                                    <th style="width: 5%">Statut</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $key => $user)
                                    <tr class="clickable-row " data-id="{{ $user->id }}">

                                        <td>{{ $key + 1 }}</td>
                                        <td class="magasin-nom">{{ $user->email }}</td>
                                        <td class="">{{ $user->name }}</td>
                                        <td>
                                            @if ($user->actif == 1)
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
    @include('livewire.utilisateur.modal')
    <script>
        window.addEventListener('actionModalCreateUtilisateur', event => {
            $("#createUtilisateur").modal('hide');
            $("#confirmCreateUtilisateur").modal('show');
        });
        window.addEventListener('actionModalUpdateMagasin', event => {
            $("#editUtilisateur").modal('hide');
            $("#confirmEditUtilisateur").modal('show');
        });
    </script>
</section>
