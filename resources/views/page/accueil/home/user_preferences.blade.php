@extends('layouts.master', ['title' => 'Préférences utilisateur'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Mes préférences',
'infos2' => 'Paramètres',
'infos3' => 'Mes préférences',
])

<style>
    .form-check-input+.form-check-label {
        margin-right: 40px;
    }
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <form>
                <!-- Section des préférences générales -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Préférences générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="language" class="form-label">Langue</label>
                            <select class="form-select" id="language" name="language">
                                <option value="fr">Français</option>
                                <option value="en">Anglais</option>
                                <option value="es">Espagnol</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="darkMode" name="darkMode">
                            <label class="form-check-label" for="darkMode">Mode sombre</label>
                        </div>
                    </div>
                </div>

                <!-- Section pour organiser le tableau de bord -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Mon tableau de bord</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Les éléments disponibles</label>
                            <div id="actionsContainer" class="d-flex flex-wrap">
                                <!-- Actions seront chargées ici dynamiquement -->
                            </div>
                            <div class="mt-2">
                                <button id="selectAllActions" type="button" class="btn btn-outline-primary">Tout sélectionner</button>
                                <button id="unSelectAllActions" type="button" class="btn btn-outline-secondary">Tout désélectionner</button>
                            </div>
                        </div>
                        <button id="save-preferences" class="btn btn-primary">Enregistrer</button>
                        <span id="messageSave1"></span>
                    </div>
                </div>

                <!-- Section des notifications -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" name="emailNotifications">
                            <label class="form-check-label" for="emailNotifications">Notifications par email</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="smsNotifications" name="smsNotifications">
                            <label class="form-check-label" for="smsNotifications">Notifications par SMS</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<br>
<br>
<br>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fixedWidgets = [11, 8, 10,6,7]; 

        fetch('{{ route('user.preferences.choice') }}')
            .then(response => response.json())
            .then(data => {
                let form = '';
                data.widgets.forEach(widget => {
                    const isChecked = data.userWidgets.includes(widget.id) ? 'checked' : '';
                    const isDisabled = fixedWidgets.includes(widget.id) ? 'disabled' : '';
                    form += `
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" name="widgets[]" value="${widget.id}" ${isChecked} ${isDisabled} id="widget-${widget.id}">
                        <label class="form-check-label" for="widget-${widget.id}">${widget.name}</label>
                    </div>
                `;
                });
                document.getElementById('actionsContainer').innerHTML = form;
            });

        // Tout sélectionner
        document.getElementById('selectAllActions').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="widgets[]"]');
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = true;
                }
            });
        });

        // Tout désélectionner
        document.getElementById('unSelectAllActions').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="widgets[]"]');
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = false;
                }
            });
        });

        // Enregistrer les préférences via AJAX
        document.getElementById('save-preferences').addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page

            const checkboxes = document.querySelectorAll('input[name="widgets[]"]:checked');
            const widgets = Array.from(checkboxes).map(checkbox => checkbox.value);
            const messageSave1 = document.getElementById('messageSave1');

            fetch('{{ route('user.preferences.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            widgets
                        })
                    })
                .then(response => response.json())
                .then(data => {
                    messageSave1.textContent = data.success;
                    // Affiche le message pendant 3 secondes
                    setTimeout(function() {
                        messageSave1.textContent = '';
                    }, 3000); // 3000 ms = 3 secondes
                });
        });
    });
</script>

@include('layouts.alert')
@endSection