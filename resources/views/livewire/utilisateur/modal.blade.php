<form action="{{ route('user.store') }}" method="POST">
  @csrf
  <div wire:ignore.self class="modal fade" id="createUtilisateur" aria-hidden="true" aria-labelledby="createMagasinLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Créer un utilisateur</h5>
          <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close" type="reset"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Email</label>
            <input type="email" wire:model="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" aria-label="file example" required>
            {!! $errors->first('email', '<p class="error">:message</p>') !!}
          </div>
          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Nom</label>
            <input type="text" wire:model="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" aria-label="file example" required>
            {!! $errors->first('name', '<p class="error">:message</p>') !!}
          </div>
          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Mot de passe</label>
            <input type="password" wire:model="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" aria-label="file example" required>
            {!! $errors->first('password', '<p class="error">:message</p>') !!}
          </div>

          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Statut</label>
            <div class="form-check">
              <input class="form-check-input {{ $errors->has('actif') ? 'is-invalid' : '' }}" type="radio" wire:model="actif" name="actif" value="1" required id="flexRadioDefault2">
              <label class="form-check-label" for="flexRadioDefault2">
                Actif
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input {{ $errors->has('actif') ? 'is-invalid' : '' }}" type="radio" wire:model="actif" name="actif" value="0" id="flexRadioDefault1">
              <label class="form-check-label" for="flexRadioDefault1">
                Inactif
              </label>
            </div>
            {!! $errors->first('actif', '<p class="error">:message</p>') !!}
          </div>
          <input class="form-check-input ml-2" type="checkbox" name="ForcePassChange" id="ForcePassChange" checked>
          <label class="form-check-label ml-4" for="ForcePassChange">
            Forcer le changement de mot de passe à la première connexion
          </label>
        </div>
        <div class="modal-footer" id="importFieldset">
          <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" type="reset">Annuler</button>
          <button type="button" id="saveButton" class="btn text-white" style="{{ background_color_1() }}" wire:click.prevent='createUtilisateur'>
            Sauvegarder
          </button>
        </div>
      </div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="confirmCreateUtilisateur" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCreateMagasinLabel" aria-hidden="true">
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
          <button type="submit" class="btn text-white" style="{{ background_color_1() }}">Oui sauvegarder</button>
        </div>
      </div>
    </div>
  </div>

</form>
<form action="{{ route('user.update', $UpIdUtilisateur ?? '') }}" method="POST">
  @csrf
  @method('PUT')

  <div wire:ignore.self class="modal fade" id="editUtilisateur" aria-hidden="true" aria-labelledby="editMagasinLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modifier l'utilisateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Email</label>
            <input type="email" wire:model="UpEmail" name="UpEmail" class="form-control {{ $errors->has('UpEmail') ? 'is-invalid' : '' }}" aria-label="file example" required>
            {!! $errors->first('UpEmail', '<p class="error">:message</p>') !!}
          </div>
          <div class="form-group">
            <label for="validationTextarea" class="form-label fw-bold">Nom</label>
            <input type="text" wire:model="UpName" name="UpName" class="form-control {{ $errors->has('UpName') ? 'is-invalid' : '' }}" aria-label="file example" required>
            {!! $errors->first('UpName', '<p class="error">:message</p>') !!}
          </div>


          <div class="form-groupq">
            <label for="validationTextarea" class="form-label fw-bold">Statut</label>
            <div class="form-check">
              <input class="form-check-input {{ $errors->has('UpActif') ? 'is-invalid' : '' }}" type="radio" wire:model="UpActif" name="UpActif" value="1" required id="flexRadioDefault2">
              <label class="form-check-label" for="flexRadioDefault2">
                Actif
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input {{ $errors->has('UpActif') ? 'is-invalid' : '' }}" type="radio" wire:model="UpActif" name="UpActif" value="0" id="flexRadioDefault1">
              <label class="form-check-label" for="flexRadioDefault1">
                Inactif
              </label>
            </div>
            {!! $errors->first('UpActif', '<p class="error">:message</p>') !!}
          </div>
        </div>
        <div class="modal-footer" id="importFieldset">
          <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
          <button type="button" id="saveButton" class="btn text-white" style="{{ background_color_1() }}" wire:click.prevent='validateEditUtilisateur'>
            Sauvegarder
          </button>
        </div>
      </div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="confirmEditUtilisateur" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmEditMagasinLabel" aria-hidden="true">
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
          <button type="submit" class="btn text-white" style="{{ background_color_1() }}">Oui sauvegarder</button>
        </div>
      </div>
    </div>
  </div>

</form>