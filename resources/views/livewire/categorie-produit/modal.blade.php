<form id="formcreate" action="{{ route('store.categorie') }}" method="POST">
    @csrf
    <div wire:ignore.self class="modal fade" id="createCategorie" aria-hidden="true" aria-labelledby="createCategorieLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer une catégorie de produit</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Categorie</label>
                        <span class="translate-middle text-danger mt-2">*
                        </span>
                        <input type="text" wire:model="NomCategorie" name="NomCategorie"
                            class="form-control {{ $errors->has('NomCategorie') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('NomCategorie', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary" wire:click.prevent='createCategorie'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmCreateCategorie" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCreateCategorieLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmCreateCategorieLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button type="submit" id="confirmcreate" class="btn btn-success">Oui sauvegarder</button>
                    {{-- <span id="loadingSpinner" style="display:none;">Chargement...</span> --}}
                    <!-- Spinner ou texte de chargement -->
                </div>
            </div>
        </div>
    </div>

</form>

<form id="formupdate" action="{{ route('update.categorie', $UpIdCategorie ?? '') }}" method="POST">
    @csrf
    @method('PUT')

    <div wire:ignore.self class="modal fade" id="editCategorie" aria-hidden="true" aria-labelledby="editCategorieLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier une categorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Categorie</label>
                        <input type="text" wire:model="UpNomCategorie" name="UpNomCategorie"
                            class="form-control {{ $errors->has('UpNomCategorie') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpNomCategorie', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='validateEditCategorie'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmEditCategorie" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmEditCategorieLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmEditCategorieLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button id="confirmupdate" type="submit" class="btn btn-success">Oui sauvegarder</button>
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
