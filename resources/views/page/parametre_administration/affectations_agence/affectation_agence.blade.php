@extends('layouts.master', ['title' => 'AFFECTATION DE AGENCE'])
@section('content')
    @if ($errors->any())
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "error",
                title: "{{ 'Cette agence est déja attribuée a cet utilisateur' }}"
            });
        </script>
    @endif

    <style>
        @media (max-width: 1050px) {
            .my-table-wrapper {
                overflow-x: auto;
            }
        }
    </style>

    @include('layouts.partials.entete-page', [
        'infos1' => 'AFFECTATION DE AGENCE',
        'infos2' => 'AFFECTATION DE AGENCE',
        'infos3' => 'Liste',
    ])

    <div class="d-flex justify-content-between align-items-center my-table-wrapper gap-5">
        <form action="{{ route('affecterAgenceUser') }}" method="POST"
            class="d-flex justify-content-between align-items-center gap-4">
            @csrf
            <div class="d-flex form-group">
                <label for="user_id" class="label-form">Utilisateur:&nbsp;</label>
                <select name="user_id" class="js-single" style="width: 150px;" id="user_id" required>
                    <option value=""></option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex form-group">
                <label for="agence_id" class="label-form">Agence:&nbsp;</label>
                <select name="agence_id" class="js-single" style="width: 150px;" id="agence_id" required>
                    <option value=""></option>
                    @foreach ($agences as $agence)
                        <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: -19px">Associer</button>
            </div>
        </form>
        <div class="d-flex">
            <div class="dropdown">
                <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" style="{{ background_color_1() }}">
                    Action
                </button>
                <ul class="dropdown-menu">
                    <li><a id="supAffectationAgence" class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#retirerAffectationAgence" href="#">Supprimer</a></li>
                </ul>
            </div>
        </div>
    </div>

    <section style="margin-bottom: 150px;">

        <div class="card m-b-30">
            <div class="card-header" style="{{ background_color_2() }}">
                <h3 class="mt-2 d-inline-block text-dark">Liste des affectations d'agence aux utilisateurs</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="affectationAgenceTable"
                        class="datatable table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Nom Utilisateur</th>
                                <th>Agence</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 0; @endphp
                            @forelse ($agenceUsers as $agenceUser)
                                <tr id="affectation_{{ $agenceUser->id }}" class="clickable-row"
                                    data-idaffectationagence="{{ $agenceUser->id }}">
                                    <td scope="col" data-id="{{ $agenceUser->id }}">{{ ++$count }}</td>
                                    <td scope="col">{{ $agenceUser->user->name }}</td>
                                    <td scope="col">{{ $agenceUser->user->email }}</td>
                                    <td scope="col">{{ $agenceUser->agence->NomAgence }}</td>
                                    {{-- <td>
                                        <div class="d-flex justify-content-end">
                                            <a class="btn" style="background-color: #f77" data-bs-toggle="modal"
                                                data-bs-target="#retireAgence_{{ $agenceUser->id }}" (click)="openModal()"
                                                href="#" title="retirer cette agence à l'utilisateur"
                                                data-toggle="tooltip">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-eraser-off">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 3l18 18" />
                                                    <path
                                                        d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l5 -4.993m2.009 -2.01l3 -3a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41c-1.417 1.431 -2.406 2.432 -2.97 3m-2.02 2.043l-4.211 4.256" />
                                                    <path d="M18 13.3l-6.3 -6.3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td> --}}
                                </tr>

                                <!-- Modal Droit d'accès-->
                                <div class="modal fade" id="retirerAffectationAgence_{{ $agenceUser->id }}"
                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                    aria-labelledby="retireAgence_{{ $agenceUser->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Dorit d'accès</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Voulez-vous vraiment retirer cette agence a l'utilisateur ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
                                                <form action="{{ route('retirerAgenceUser', $agenceUser) }}"
                                                    method="post">
                                                    @method('delete')
                                                    @csrf
                                                    <button class="btn btn-primary">Retirer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td scope="row" colspan="5" class="text-center">
                                        Aucune affectation de droit trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

    {{--  --}}
    {{-- <script>
        $(document).ready(function() {
            $('#affectationAgenceTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                Mettre à jour l 'URL du lien "Modifier" avec l'
                ID du proforma
                var modifierUrl = "{{ route('editClient', ['id' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                // Mettre à jour l'attribut href du lien
                $('#modifierLink').attr('href', modifierUrl);
            });
        });
    </script> --}}

    {{-- Suppresion affectation groupe --}}
    <script>
        $('#affectationAgenceTable').on('click', '.clickable-row', function() {
            $('#affectationAgenceTable .clickable-row td').css({
                'background-color': '',
                'color': 'black'
            });
            $(this).find('td').css({
                'background-color': 'rgb(29, 9, 101)',
                'color': 'white'
            });

            var ID = $(this).data('idaffectationagence');
            $('#supAffectationAgence').attr('data-bs-target', '#retirerAffectationAgence_' + ID);
        });
    </script>

    <!-- Affecter un ensemble d'action d'un module a un groupe -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkbox = document.querySelector('.checkbox3');

            checkbox.addEventListener('change', function() {
                var actionId = this.getAttribute('data-affectationDroit'); // Récupérez l'ID de l'action
                let getGroupe = $('meta[name="getGroupe"]').attr('content');

                // Exécutez une requête AJAX vers votre contrôleur pour activer ou désactiver l'action
                if (this.checked) {
                    // Si la case à cocher est cochée, exécutez votre action pour l'activer
                    $.ajax({
                        type: 'POST',
                        url: '/affecterTousDroits',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            getGroupe: getGroupe,
                            actionId: actionId
                        },
                        success: function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: "success",
                                title: "Droit d\'accès affecté au groupe avec succès"
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            console.error(status);
                            // console.log(error);
                        }
                    });
                } else {
                    // Si la case à cocher est décochée, exécutez votre action pour la désactiver
                    $.ajax({
                        type: 'POST',
                        url: '/retirerDroit/' + actionId,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            getGroupe: getGroupe,
                            actionId: actionId
                        },
                        success: function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: "success",
                                title: "Droit d\'accès retiré au groupe avec succès"
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            console.error(status);
                            // console.log(error);
                        }
                    });
                }
            });
        });
    </script>


    <!-- Affecter des actions a un groupe -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkboxes = document.querySelectorAll('.checkbox2');

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var actionId = this.getAttribute(
                        'data-affectationDroit'); // Récupérez l'ID de l'action
                    let getGroupe = $('meta[name="getGroupe"]').attr('content');

                    // Exécutez une requête AJAX vers votre contrôleur pour activer ou désactiver l'action
                    if (this.checked) {
                        // Si la case à cocher est cochée, exécutez votre action pour l'activer
                        $.ajax({
                            type: 'POST',
                            url: '/affecterDroit/' + actionId,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getGroupe: getGroupe,
                                actionId: actionId
                            },
                            success: function() {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal
                                            .resumeTimer;
                                    }
                                });
                                Toast.fire({
                                    icon: "success",
                                    title: "Droit d\'accès affecté au groupe avec succès"
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                console.error(status);
                                // console.log(error);
                            }
                        });
                    } else {
                        // Si la case à cocher est décochée, exécutez votre action pour la désactiver
                        $.ajax({
                            type: 'POST',
                            url: '/retirerDroit/' + actionId,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getGroupe: getGroupe,
                                actionId: actionId
                            },
                            success: function() {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: "top-end",
                                    showConfirmButton: false,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal
                                            .resumeTimer;
                                    }
                                });
                                Toast.fire({
                                    icon: "success",
                                    title: "Droit d\'accès retiré au groupe avec succès"
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                console.error(status);
                                // console.log(error);
                            }
                        });
                    }
                });
            });
        });
    </script>

    <style>
        .selected {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 100%;
        }

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>

    @include('layouts.alert')
@endSection
