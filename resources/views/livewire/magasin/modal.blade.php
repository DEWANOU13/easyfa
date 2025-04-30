<form id="createMagasinForm" action="{{ route('storeMagasin') }}" method="POST">
    @csrf
    <div wire:ignore.self class="modal fade" id="createMagasin" aria-hidden="true" aria-labelledby="createMagasinLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer un magasin</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Magasin</label>
                        <input type="text" wire:model="NomMagasin" name="NomMagasin"
                            class="form-control {{ $errors->has('NomMagasin') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('NomMagasin', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Agence</label>
                        <select class="form-select {{ $errors->has('agence_id') ? 'is-invalid' : '' }}"
                            wire:model="agence_id" name="agence_id" required>
                            <option>Choisir une agence</option>
                            @foreach ($listeAgence as $agence)
                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('agence_id', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-groupq">
                        <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('Statut_Magasin') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Statut_Magasin" name="Statut_Magasin" value="1" required
                                id="flexRadioDefault2">
                            <label class="form-check-label" for="flexRadioDefault2">
                                Actif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('Statut_Magasin') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Statut_Magasin" name="Statut_Magasin" value="0"
                                id="flexRadioDefault1">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Inactif
                            </label>
                        </div>
                        {!! $errors->first('Statut_Magasin', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary" wire:click.prevent='createMagasin'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmCreateMagasin" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCreateMagasinLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmCreateMagasinLabel">Demande de confirmation </h1>
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

<form id="magasinForm" action="{{ route('updateMagasin', $UpIdMagasin ?? '') }}" method="POST">
    @csrf
    @method('PUT')

    <div wire:ignore.self class="modal fade" id="editMagasin" aria-hidden="true" aria-labelledby="editMagasinLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un magasin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Magasin</label>
                        <input type="text" wire:model="UpNomMagasin" name="UpNomMagasin"
                            class="form-control {{ $errors->has('UpNomMagasin') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpNomMagasin', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Agence</label>
                        <select class="form-select {{ $errors->has('up_agence_id') ? 'is-invalid' : '' }}"
                            wire:model="up_agence_id" name="up_agence_id" required>
                            <option>Choisir une agence</option>
                            @foreach ($listeAgence as $agence)
                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('up_agence_id', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-groupq">
                        <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                        <div class="form-check">
                            <input
                                class="form-check-input {{ $errors->has('Up_Statut_Magasin') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Up_Statut_Magasin" name="Up_Statut_Magasin"
                                value="1" required id="flexRadioDefault2">
                            <label class="form-check-label" for="flexRadioDefault2">
                                Actif
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input {{ $errors->has('Up_Statut_Magasin') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Up_Statut_Magasin" name="Up_Statut_Magasin"
                                value="0" id="flexRadioDefault1">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Inactif
                            </label>
                        </div>
                        {!! $errors->first('Up_Statut_Magasin', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='validateEditMagasin'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmEditMagasin" data-bs-backdrop="static"
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
                    <button type="submit" id="save-button" class="btn btn-success">Oui sauvegarder</button>
                </div>
            </div>
        </div>
    </div>

</form>
<script>
    $('#magasinForm').on('submit', function(e) {

               var $button = $('#save-button');
                  $button.addClass('loading');
                  $button.prop('disabled', true);

      });
</script>
