<form action="{{ route('storeAgence') }}" method="POST">
    @csrf
    <div wire:ignore.self class="modal fade" id="createAgence" aria-hidden="true" aria-labelledby="createMagasinLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer une agence</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Nom Agence</label>
                        <input type="text" wire:model="NomAgence" name="NomAgence" autocomplete="off"
                            class="form-control {{ $errors->has('NomAgence') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('NomAgence', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Adresse</label>
                        <input type="text" wire:model="adresseAgence" name="adresseAgence" autocomplete="off"
                            class="form-control {{ $errors->has('adresseAgence') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('adresseAgence', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Numéro de telephone 1</label>
                        <input type="text" wire:model="numero_telephone_1" name="numero_telephone_1" autocomplete="off"
                            class="form-control {{ $errors->has('numero_telephone_1') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('numero_telephone_1', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Numéro de telephone 2</label>
                        <input type="text" wire:model="numero_telephone_2" name="numero_telephone_2" autocomplete="off"
                            class="form-control"
                            aria-label="file example" >
                    </div>

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Titre Signataire Facture</label>
                        <input type="text" wire:model="titre_signataire_facture" name="titre_signataire_facture" autocomplete="off"
                            class="form-control {{ $errors->has('titre_signataire_facture') ? 'is-invalid' : '' }}"
                            aria-label="file example">
                        {!! $errors->first('titre_signataire_facture', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Nom Signataire</label>
                        <input type="text" wire:model="nom_signataire" name="nom_signataire" autocomplete="off"
                            class="form-control {{ $errors->has('nom_signataire') ? 'is-invalid' : '' }}"
                            aria-label="file example">
                        {!! $errors->first('nom_signataire', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-groupq">
                        <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                        <div class="d-flex gap-5">
                            <div class="form-check">
                                <input class="form-check-input {{ $errors->has('EnActivite') ? 'is-invalid' : '' }}"
                                    type="radio" wire:model="EnActivite" name="EnActivite" value="1" required
                                    id="flexRadioDefault2">
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Actif
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input {{ $errors->has('EnActivite') ? 'is-invalid' : '' }}"
                                    type="radio" wire:model="EnActivite" name="EnActivite" value="0"
                                    id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Inactif
                                </label>
                            </div>
                        </div>
                        {!! $errors->first('EnActivite', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary" wire:click.prevent='createAgence'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmcreateAgence" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="confirmcreateAgenceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmcreateAgenceLabel">Demande de confirmation </h1>
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

<form action="{{ route('updateAgence', $UpIdAgence ?? '') }}" method="POST">
    @csrf
    @method('PUT')

    <div wire:ignore.self class="modal fade" id="editAgence" aria-hidden="true" aria-labelledby="editMagasinLabel2"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier agence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Nom agence</label>
                        <input type="text" wire:model="UpNomAgence" name="UpNomAgence"
                            class="form-control {{ $errors->has('UpNomAgence') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('UpNomAgence', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Adresse</label>
                        <input type="text" wire:model="adresseAgence" name="adresseAgence"
                            class="form-control {{ $errors->has('adresseAgence') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('adresseAgence', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Numéro de telephone 1</label>
                        <input type="text" wire:model="numero_telephone_1" name="numero_telephone_1"
                            class="form-control {{ $errors->has('numero_telephone_1') ? 'is-invalid' : '' }}"
                            aria-label="file example" required>
                        {!! $errors->first('numero_telephone_1', '<p class="error">:message</p>') !!}
                    </div>
                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Numéro de telephone 2</label>
                        <input type="text" wire:model="numero_telephone_2" name="numero_telephone_2"
                            class="form-control {{ $errors->has('numero_telephone_2') ? 'is-invalid' : '' }}"
                            aria-label="file example" >
                        {!! $errors->first('numero_telephone_2', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Titre Signataire Facture</label>
                        <input type="text" wire:model="titre_signataire_facture" name="titre_signataire_facture"
                            class="form-control {{ $errors->has('titre_signataire_facture') ? 'is-invalid' : '' }}"
                            aria-label="file example">
                        {!! $errors->first('titre_signataire_facture', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-group">
                        <label for="validationTextarea" class="form-label fw-bold">Nom Signataire</label>
                        <input type="text" wire:model="nom_signataire" name="nom_signataire"
                            class="form-control {{ $errors->has('nom_signataire') ? 'is-invalid' : '' }}"
                            aria-label="file example">
                        {!! $errors->first('nom_signataire', '<p class="error">:message</p>') !!}
                    </div>

                    <div class="form-groupq">
                        <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('Up_EnActivite') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Up_EnActivite" name="Up_EnActivite" value="1"
                                required id="flexRadioDefault2">
                            <label class="form-check-label" for="flexRadioDefault2">
                                Actif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input {{ $errors->has('Up_EnActivite') ? 'is-invalid' : '' }}"
                                type="radio" wire:model="Up_EnActivite" name="Up_EnActivite" value="0"
                                id="flexRadioDefault1">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Inactif
                            </label>
                        </div>
                        {!! $errors->first('Up_EnActivite', '<p class="error">:message</p>') !!}
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="button" id="saveButton" class="btn btn-primary"
                        wire:click.prevent='validateEditAgence'>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmEditAgence" data-bs-backdrop="static"
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
