<form id="formCategorieClient" action="{{ route('storeCategorieClient') }}" method="POST">
    @csrf
    <div wire:ignore.self class="modal fade" id="createCategorieClient" aria-hidden="true"
        aria-labelledby="createCategorieClientLabel2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer une catégorie client</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Categorie client</label>
                        <input type="text" wire:model="Libelle" name="Libelle"
                            class="form-control {{ $errors->has('Libelle') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('Libelle', '<p class="error">:message</p>') !!}
                    </div>


                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
                    <button type="submit" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='createCategorieClient'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmcreateCategorieClient" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmcreateCategorieClientLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmcreateCategorieClientLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button type="submit" id="save-button" class="btn btn-success">Oui sauvegarder</button>
                </div>
            </div>
        </div>
    </div>

</form>
<script>
    $('#formCategorieClient').on('submit', function(e) {

               var $button = $('#save-button');
                  $button.addClass('loading');
                  $button.prop('disabled', true);

      });
</script>

<form id="updateCategorieClient" action="{{ route('updateCategorieClient', $UpIdCategorieClient ?? '') }}" method="POST">
    @csrf
    @method('PUT')

    <div wire:ignore.self class="modal fade" id="editCategorieClient" aria-hidden="true"
        aria-labelledby="editCategorieClientLabel2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier une catégorie client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Catégorie client</label>
                        <input type="text" wire:model="UpLibelle"  name="UpLibelle" class="form-control {{ $errors->has('UpLibelle') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpLibelle', '<p class="error">:message</p>') !!}
                    </div>


                    <div class="modal-footer" id="importFieldset">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
                        <button type="submit" id="saveButton" class="btn btn-primary"
                            wire:click.prevent='validateEditCategorieClient'>
                            Sauvegarder
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="confirmEditCategorieClient" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmEditMagasinLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmEditMagasinLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment sauvegarder ces informations ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <button type="submit" class="btn btn-success">Oui sauvegarder</button>
                </div>
            </div>
        </div>
    </div>


</form>


