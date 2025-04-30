@extends('layouts.master', ['title' => 'AFFECTATION DE DROIT'])
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
                title: "{{ 'Cet utilisateur avec ce groupe existe déja' }}"
            });
        </script>
    @endif

    @include('layouts.partials.entete-page', [
        'infos1' => 'AFFECTATION DE DROIT',
        'infos2' => 'AFFECTATION DE DROIT',
        'infos3' => 'Liste',
    ])

    <style>
        @media (max-width: 1050px) {
            .my-table-wrapper {
                overflow-x: auto;
            }
        }
    </style>

    <section style="margin-bottom: 150px;">
        <nav class="d-flex justify-content-between align-items-center">
            <div class="nav nav-tabs w-100" id="nav-tab" role="tablist">
                <button class="nav-link {{ $activeTab == 'affectation_groupe' ? 'active' : '' }}" id="nav-profile-tab"
                    data-bs-toggle="tab" data-bs-target="#nav-compte" type="button" role="tab" aria-controls="nav-profile"
                    aria-selected="false">Affectation Par Groupe
                </button>

                @can('affectation-groupe-utilisateur')
                    <button class="nav-link {{ $activeTab == 'groupe_utilisateur' ? 'active' : '' }}" id="nav-home-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-liste" type="button" role="tab" aria-controls="nav-home"
                        aria-selected="true">Groupe \ Utilisateur
                    </button>
                @endcan

                @can('affectation-droit-utilisateur')
                    <button class="nav-link {{ $activeTab == 'affectation_user' ? 'active' : '' }}" id="nav-affectation-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-affectation" type="button" role="tab"
                        aria-controls="nav-affectation" aria-selected="false">Affectation Par Utilisateur
                    </button>
                @endcan

            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade {{ $activeTab == 'affectation_groupe' ? 'show active' : '' }}" id="nav-compte"
                role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                <div class="d-flex justify-content-between align-items-center table-responsive gap-5">
                    <form action="" method="GET"
                        class="d-flex justify-content-between align-items-center gap-3 mt-3 mb-2">
                        @csrf
                        <input type="text" hidden name="active_tab" value="affectation_groupe">
                        <div class="d-flex form-group">
                            <label for="groupe_id1" class="label-form">Groupe:&nbsp;</label>
                            <select name="groupe_id1" class="form-select js-single" style="width: 150px;" id="groupe_id1"
                                required aria-required="true">
                                @foreach ($groupes as $groupe)
                                    <option
                                        {{ isset($input['groupe_id1']) && $input['groupe_id1'] == $groupe->id ? 'selected' : '' }}
                                        value="{{ $groupe->id }}">{{ $groupe->nom_groupe }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex form-group">
                            <label for="module" class="label-form">Module:&nbsp;</label>
                            <select name="module" class="js-single" style="width: 150px;" style="min-width: 150px;"
                                id="module" required>
                                @foreach ($modules as $module)
                                    <option
                                        {{ isset($input['module']) && $input['module'] == $module->id ? 'selected' : '' }}
                                        value="{{ $module->id }}">{{ $module->nom_module }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top: -20px">
                            <button type="submit" class="btn btn-primary btn-sm">Afficher</button>
                        </div>
                    </form>
                    <div class="d-flex" id="selectAllActionGroupe">
                        <label class="form-check-label d-flex" for="affecterToutGroupe">Tout affecter
                            <input class="form-check-input checkboxToutGroupe" type="checkbox" value=""
                                id="affecterToutGroupe"
                                {{ isAllActionAffectedToGroupe($request->input('module'), $request->input('groupe_id1')) }}>
                        </label>
                    </div>
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2 d-inline-block text-dark">Liste des affectations des actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="droitGroupe"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Droit</th>
                                        <th>Autorisation</th>
                                    </tr>
                                </thead>
                                <tbody id="groupeActionDynamiqueTable">
                                    @forelse ($actions as $action)
                                        <tr scope="row" for="affectationDroit_{{ $action->id }}"
                                            class="clickable-row">
                                            <td>{{ $action->nom_action }}</td>
                                            <td class="text-end">
                                                <input type="checkbox"
                                                    {{ isActionAffectedToGroupe($action->id, $request->input('groupe_id1')) }}
                                                    id="affectationDroit_{{ $action->id }}"
                                                    class="user-checkbox checkbox2"
                                                    data-affectationDroit="{{ $action->id }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                Veuillez sélectionner un groupe et un module et soumettre
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade {{ $activeTab == 'groupe_utilisateur' ? 'show active' : '' }}" id="nav-liste"
                role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                <div class="d-flex justify-content-between align-items-center my-table-wrapper gap-5">
                    <form action="{{ route('groupeUser.store') }}" method="POST"
                        class="d-flex justify-content-between align-items-center gap-4 mt-3 mb-2">
                        @csrf
                        <div class="d-flex form-group">
                            <label for="user_id" class="label-form">Utilisateur: </label>
                            <select name="user_id" class="js-single" style="width: 150px;" id="user_id1" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex form-group">
                            <label for="groupe_id" class="label-form">Groupe: </label>
                            <select name="groupe_id" class="js-single" style="width: 150px;" id="groupe_id" required>
                                @foreach ($groupes as $groupe)
                                    <option value="{{ $groupe->id }}">{{ $groupe->nom_groupe }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top: -20px;">
                            <button type="submit" class="btn btn-primary btn-sm">Associer</button>
                        </div>
                    </form>
                    <div class="d-flex flex-row-reverse bd-highlight">
                        <div class="dropdown mb-2">
                            <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false" style="{{ background_color_1() }}">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li><a id="supAffectationGroupe" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#retireAfectation" href="#">Supprimer</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2 d-inline-block text-dark">Droits par groupe</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="affectationGroupeTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Adresse email</th>
                                        <th>Groupe</th>
                                    </tr>
                                </thead>
                                <tbody id="gorupeUserTbody">
                                    @php $count = 0; @endphp
                                    @forelse ($groupeUsers as $groupeUser)
                                        <tr id="affectation_{{ $groupeUser->id }}"
                                            data-idaffectationgroupe="{{ $groupeUser->id }}" class="clickable-row">
                                            <td>{{ ++$count }}</td>
                                            <td>{{ $groupeUser->user->name }}</td>
                                            <td>{{ $groupeUser->user->email }}</td>
                                            <td>{{ $groupeUser->groupe->nom_groupe }}</td>
                                        </tr>

                                        <!-- Modal Droit d'accès-->
                                        <div class="modal fade" id="retireAfectation_{{ $groupeUser->id }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="retireAfectation_{{ $groupeUser->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Dorit
                                                            d'accès</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Voulez-vous vraiment retirer {{ $groupeUser->user->name }} du
                                                        groupe {{ $groupeUser->groupe->nom_groupe }} ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('groupeUser.destroy', $groupeUser) }}"
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
                                            <td colspan="4" class="text-center">
                                                Aucune affectation de droit trouvée
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade {{ $activeTab == 'affectation_user' ? 'show active' : '' }}" id="nav-affectation"
                role="tabpanel" aria-labelledby="nav-affectation-tab" tabindex="0">
                <div class="d-flex justify-content-between align-items-center my-table-wrapper gap-5">
                    <form action="" method="GET"
                        class="d-flex justify-content-between align-items-center gap-3 mt-3 mb-2">
                        @csrf
                        <input type="text" hidden name="active_tab" value="affectation_user">
                        <div class="d-flex form-group">
                            <label for="user_id" class="label-form">Utilisateur: </label>
                            <select name="user_id" class="js-single" style="width: 150px;" id="userAgence_id" required>
                                @foreach ($users as $user)
                                    <option
                                        {{ $request->filled('user_id') && $request->input('user_id') == $user->id ? 'selected' : '' }}
                                        value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex form-group">
                            <label for="agence_id" class="label-form">Agence:&nbsp;</label>
                            <select name="agence_id" class="form-select js-single" style="width: 150px;" id="agence_id"
                                required aria-required="true">
                                @if ($agencesUser != null)
                                    @foreach ($agencesUser as $agenceUser)
                                        <option
                                            {{ $request->filled('agence_id') && $request->input('agence_id') == $agenceUser->agence->id ? 'selected' : '' }}
                                            value="{{ $agenceUser->agence->id }}">
                                            {{ $agenceUser->agence->NomAgence }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="d-flex form-group">
                            <label for="module" class="label-form">Module:&nbsp;</label>
                            <select name="module1" class="js-single" style="width: 150px;" style="min-width: 150px;"
                                id="module1" required>
                                <option value=""></option>
                                @foreach ($modules as $module)
                                    <option
                                        {{ $request->filled('module1') && $request->input('module1') == $module->id ? 'selected' : '' }}
                                        value="{{ $module->id }}">{{ $module->nom_module }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top: -20px">
                            <button type="submit" class="btn btn-primary btn-sm">Afficher</button>
                        </div>
                    </form>
                    <div class="d-flex" id="selectAllActionUser">
                        <label class="form-check-label d-flex" for="affecterToutGroupe">Tout affecter
                            <input class="form-check-input checkboxToutUser" type="checkbox" value=""
                                id="checkboxToutUser"
                                {{ isAllActionAffectedToUser($request->input('module1'), $request->input('user_id'), $request->input('agence_id')) }}>
                        </label>
                    </div>
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2 d-inline-block text-dark">Droits par utilisateur</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="droitUtilisateur"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Droit</th>
                                        <th>Autorisation</th>
                                    </tr>
                                </thead>
                                <tbody id="userActionDynamiqueTable">
                                    @forelse ($actions1 as $action)
                                        <tr scope="row" for="affectationDroit_{{ $action->id }}"
                                            class="clickable-row">
                                            <td>{{ $action->nom_action }}</td>
                                            <td class="text-end">
                                                <input type="checkbox"
                                                    {{ isActionAffectedToUser($action->id, $request->input('user_id'), $request->input('agence_id')) }}
                                                    id="affectationDroit_{{ $action->id }}"
                                                    class="user-checkbox checkbox3"
                                                    data-affectationDroitUser="{{ $action->id }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                Veuillez sélectionner un utilisateur et un module et soumettre
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <script>
        $(document).ready(function() {

            // Fonction pour le sweetalert
            function showToast(icon, title) {
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
                    icon: icon,
                    title: title
                });
            }

            // Suppresion affectation groupe
            $('#affectationGroupeTable').on('click', '.clickable-row', function() {
                $('#affectationGroupeTable .clickable-row td').css({
                    'background-color': '',
                    'color': 'black'
                });
                $(this).find('td').css({
                    'background-color': 'rgb(29, 9, 101)',
                    'color': 'white'
                });

                var ID = $(this).data('idaffectationgroupe');
                $('#supAffectationGroupe').attr('data-bs-target', '#retireAfectation_' + ID);
            });

            // Affceter des actions a un groupe
            $(document).on('change', '.checkbox2', function() {
                var checkbox = $(this);

                var actionId = $(this).attr('data-affectationDroit'); // Récupérez l'ID de l'action
                let getGroupe = $('meta[name="getGroupe"]').attr('content');

                // vérifie si la case à cocher (checkbox) est actuellement cochée ou non.
                var isChecked = checkbox.is(':checked');

                // Empêcher l'action par défaut jusqu'à la réponse AJAX
                event.preventDefault();

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
                        success: function(data) {
                            if (data.attention) {
                                showToast("error", data.attention);

                                // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                                checkbox.prop('checked', !isChecked);
                            } else {
                                showToast("success",
                                    "Droit d\'accès affecté au groupe avec succès");
                                $("#selectAllActionGroupe").html(data.view);
                            }
                        },
                        error: function(xhr, status, error) {
                            // console.error(xhr.responseText);
                            // console.error(status);
                            // console.log(error);

                            // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                            checkbox.prop('checked', !isChecked);
                            showToast("error", "Une erreur s'est produite !");
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
                        success: function(data) {
                            if (data.attention) {
                                showToast("error", data.attention);
                                checkbox.prop('checked', !isChecked);
                            } else {
                                showToast("success",
                                    "Droit d\'accès retiré au groupe avec succès");
                                $("#selectAllActionGroupe").html(data.view);
                            }
                        },
                        error: function(xhr, status, error) {
                            // console.error(xhr.responseText);
                            // console.error(status);
                            // console.log(error);

                            // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                            checkbox.prop('checked', !isChecked);
                            showToast("error", "Une erreur s'est produite !");
                        }
                    });
                }
            });

            // Affecter des action à un utilisateur
            $(document).on('change', '.checkbox3', function() {
                var checkbox = $(this);

                var actionId = $(this).attr('data-affectationDroitUser'); // Récupérez l'ID de l'action
                var getUser = $('meta[name="getUser"]').attr('content');
                var getAgence = $('meta[name="getAgence"]').attr('content');

                // vérifie si la case à cocher (checkbox) est actuellement cochée ou non.
                var isChecked = checkbox.is(':checked');

                // Empêcher l'action par défaut jusqu'à la réponse AJAX
                event.preventDefault();

                // Exécutez une requête AJAX vers votre contrôleur pour activer ou désactiver l'action
                if (this.checked) {
                    // Si la case à cocher est cochée, exécutez votre action pour l'activer
                    $.ajax({
                        type: 'POST',
                        url: '/affecterDroitUser/' + actionId,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            getUser: getUser,
                            getAgence: getAgence,
                            actionId: actionId,
                        },
                        success: function(data) {
                            if (data.attention) {
                                showToast("error", data.attention);

                                // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                                checkbox.prop('checked', !isChecked);
                            } else {
                                showToast("success", "Droit affecté avec succès");
                                $("#selectAllActionUser").html(data.view);
                            }
                        },
                        error: function(xhr, status, error) {
                            // console.error(xhr.responseText);
                            // console.error(status);
                            // console.log(error);

                            // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                            checkbox.prop('checked', !isChecked);
                            showToast("error", "Une erreur s'est produite !");
                        }
                    });
                } else {
                    // Si la case à cocher est décochée, exécutez votre action pour la désactiver
                    $.ajax({
                        type: 'POST',
                        url: '/retirerDroitUser/' + actionId,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            getUser: getUser,
                            getAgence: getAgence,
                            actionId: actionId,
                        },
                        success: function(data) {
                            if (data.attention) {
                                showToast("error", data.attention);

                                // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                                checkbox.prop('checked', !isChecked);
                            } else {
                                showToast("success", "Droit retiré avec succès");
                                $("#selectAllActionUser").html(data.view);
                            }
                        },
                        error: function(xhr, status, error) {
                            // console.error(xhr.responseText);
                            // console.error(status);
                            // console.log(error);

                            // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                            checkbox.prop('checked', !isChecked);
                            showToast("error", "Une erreur s'est produite !");
                        }
                    });
                }
            });

            // Affecter l'ensemble des actions d'un module a un groupe
            $(document).on('change', '.checkboxToutGroupe', function() {
                var checkbox = $(this);

                let getGroupe = $('meta[name="getGroupe"]').attr('content');
                let getModule = $('meta[name="getModule"]').attr('content');

                // vérifie si la case à cocher (checkbox) est actuellement cochée ou non.
                var isChecked = checkbox.is(':checked');

                // Exécutez une requête AJAX vers votre contrôleur pour activer ou désactiver l'action
                if (this.checked) {

                    // Verifier si le la liste des action du module choisi est affiché
                    if (getGroupe != '' || getModule != '') {

                        // Si la case à cocher est cochée, exécutez votre action pour l'activer
                        $.ajax({
                            type: 'POST',
                            url: '/affecterToutGroupe/' + getGroupe,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getGroupe: getGroupe,
                                getModule: getModule
                            },
                            success: function(data) {
                                showToast("success",
                                    "Les Droits d\'accès de ce module ont été affectés au groupe avec succès"
                                    );
                                $("#droitGroupe").replaceWith(data);
                                $("#hiddenScript").html('');
                            },
                            error: function() {
                                // console.error(xhr.responseText);
                                // console.error(status);
                                // console.log(error);

                                // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                                // checkbox.prop('checked', !isChecked);
                                // showToast("error", "Une erreur s'est produite. Veuillez verifier si vous afficher les actions du groupe et du module.");
                            }
                        });
                    } else {
                        checkbox.prop('checked', !isChecked);
                        showToast("error",
                            "<h6 style='text-align: justify'>Une erreur s'est produite. Veuillez verifier si vous avez soumis le formulaire pour afficher les actions du module.</h6>"
                            );
                    }
                } else {
                    // Verifier si le la liste des action du module choisi est affiché
                    if (getGroupe != '' || getModule != '') {
                        // Si la case à cocher est décochée, exécutez votre action pour la désactiver
                        $.ajax({
                            type: 'POST',
                            url: '/retirerToutGroupe/' + getGroupe,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getGroupe: getGroupe,
                                getModule: getModule
                            },
                            success: function(data) {
                                showToast("success",
                                    "Les Droits d\'accès de ce module ont été retirés au groupe avec succès"
                                    );
                                $("#droitGroupe").replaceWith(data);
                                $("#hiddenScript").html('');
                            },
                            error: function(xhr, status, error) {
                                // console.error(xhr.responseText);
                                // console.error(status);
                                // console.log(error);

                                // changer l'état de la case à cocher (checkbox) en inversant son état actuel.
                                checkbox.prop('checked', !isChecked);
                                showToast("error",
                                    "Une erreur s'est produite. Veuillez verifier si vous afficher les actions du groupe et du module."
                                    );
                            }
                        });
                    } else {
                        checkbox.prop('checked', !isChecked);
                        showToast("error",
                            "Une erreur s'est produite. Veuillez verifier si vous avez soumis le formulaire pour afficher les actions du module."
                            );
                    }
                }
            });

            // Affecter l'ensemble  des actions d'un module a un utilisateur
            $(document).on('change', '.checkboxToutUser', function() {
                var checkbox = $(this);

                let getModule = $('meta[name="getModule1"]').attr('content');
                let getUser = $('meta[name="getUser"]').attr('content');
                let getAgence = $('meta[name="getAgence"]').attr('content');

                // vérifie si la case à cocher (checkbox) est actuellement cochée ou non.
                var isChecked = checkbox.is(':checked');

                // Exécutez une requête AJAX vers votre contrôleur pour activer ou désactiver l'action
                if (this.checked) {
                    if (getModule != '' || getUser != '' || getAgence != '') {
                        // Si la case à cocher est cochée, exécutez votre action pour l'activer
                        $.ajax({
                            type: 'POST',
                            url: '/affecterToutUser/' + getUser,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getUser: getUser,
                                getModule: getModule,
                                getAgence: getAgence,
                            },
                            success: function(data) {
                                showToast("success",
                                    "Les Droits d\'accès de ce module ont été affectés à l'utilisateur avec succès"
                                    );
                                $("#userActionDynamiqueTable").html(data);
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                console.error(status);
                                console.log(error);
                            }
                        });
                    } else {
                        checkbox.prop('checked', !isChecked);
                        showToast("error",
                            "<h6 style='text-align: justify'>Une erreur s'est produite. Veuillez verifier si vous afficher les actions du module.</h6>"
                            );
                    }
                } else {
                    if (getModule != '' || getUser != '' || getAgence != '') {
                        // Si la case à cocher est décochée, exécutez votre action pour la désactiver
                        $.ajax({
                            type: 'POST',
                            url: '/retirerToutUser/' + getUser,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                getUser: getUser,
                                getModule: getModule,
                                getAgence: getAgence,
                            },
                            success: function(data) {
                                showToast("success",
                                    "Les Droits d\'accès de ce module ont été retirés à l'utilisateur avec succès"
                                    );
                                $("#userActionDynamiqueTable").html(data);
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                console.error(status);
                                console.log(error);
                            }
                        });
                    } else {
                        checkbox.prop('checked', !isChecked);
                        showToast("error",
                            "Une erreur s'est produite. Veuillez verifier si vous afficher les actions du module."
                            );
                    }
                }
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
