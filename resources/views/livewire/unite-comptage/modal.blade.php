<form id="formcreate" action="{{ route('store.unite_comptage') }}" method="POST">
    @csrf
    <div wire:ignore.self class="modal fade" id="createUniteComptage" aria-hidden="true"
        aria-labelledby="createUniteComptageLabel2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer une unité de comptage</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Code</label>
                        <span class="translate-middle text-danger mt-2">*
                        </span>
                        <input type="text" wire:model="CodeUniteComptage" name="CodeUniteComptage"
                            class="form-control {{ $errors->has('CodeUniteComptage') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('CodeUniteComptage', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">UniteComptage</label>
                        <span class="translate-middle text-danger mt-2">*
                        </span>
                        <input type="text" wire:model="NomUniteComptage" name="NomUniteComptage"
                            class="form-control {{ $errors->has('NomUniteComptage') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('NomUniteComptage', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='createUniteComptage'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmCreateUniteComptage" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCreateUniteComptageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmCreateUniteComptageLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button type="submit" id="confirmcreate" class="btn btn-success">Oui sauvegarder</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="formupdate" action="{{ route('update.unite_comptage', $UpIdUniteComptage ?? '') }}" method="POST">
    @csrf
    @method('PUT')

    <div wire:ignore.self class="modal fade" id="editUniteComptage" aria-hidden="true"
        aria-labelledby="editUniteComptageLabel2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier une uniteComptage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Code</label>
                        <input type="text" wire:model="UpCodeUniteComptage" name="UpCodeUniteComptage"
                            class="form-control {{ $errors->has('UpCodeUniteComptage') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpCodeUniteComptage', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Libelle</label>
                        <input type="text" wire:model="UpNomUniteComptage" name="UpNomUniteComptage"
                            class="form-control {{ $errors->has('UpNomUniteComptage') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpNomUniteComptage', '<p class="error">:message</p>') !!}
                    </div>
                </div>

                <div class="modal-footer" id="importFieldset">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='validateEditUniteComptage'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmEditUniteComptage" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmEditUniteComptageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmEditUniteComptageLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button type="submit" id="confirmupdate" class="btn btn-success">Oui sauvegarder</button>
                </div>
            </div>
        </div>
    </div>

</form>


<script>
    $(document).ready(function() {
        $('#formcreate').on('submit', function() {
            var $button = $('#confirmcreate');

            // Désactiver le bouton et ajouter la classe loading
            $button.prop('disabled', true);
            $button.addClass('loading');

            // Afficher le texte ou l'indicateur de chargement
            $('#loadingSpinner').show();

            // Laisser le formulaire continuer à être soumis normalement
            return true;
        });
    });

    $(document).ready(function() {
        $('#formupdate').on('submit', function() {
            var $button = $('#confirmupdate');

            // Désactiver le bouton et ajouter la classe loading
            $button.prop('disabled', true);
            $button.addClass('loading');

            // Afficher le texte ou l'indicateur de chargement
            $('#loadingSpinner').show();

            // Laisser le formulaire continuer à être soumis normalement
            return true;
        });
    });
</script>
