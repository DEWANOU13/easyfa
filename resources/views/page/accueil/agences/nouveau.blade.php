@extends('layouts.layout')
@section('content')
<style>
  .entete_tableau {
    background-color: #6104ed;
    color: white;
  }

</style>
<section>
  <div class="d-flex justify-content-between align-items-center p-3">
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Accueil</li>
        <li class="breadcrumb-item "><a href="{{ route('agences') }}">Agence</a></li>
        @if (isset($infoAgence))
        <li class="breadcrumb-item active"><span class="badge bg-primary">Modification d'agence</span></li>
        @else
        <li class="breadcrumb-item active"><a href="{{ route('showFormAgence') }}"><span class="badge bg-primary">Nouvelle agence</span></a></li>
        @endif
      </ol>
    </nav>
  </div>

  <div class="d-flex justify-content-between align-items-center">
    <h4 class="text-primary">
      @if (isset($infoAgence))
      Modification de {{ $infoAgence->NomAgence }}
      @else
      Créer une agence
      @endif
    </h4>
    <a href="{{ route('agences') }}" class="btn btn-primary btn-block btn-sm d-sm-inline-block d-md-inline-block d-lg-inline-block d-xl-inline-block d-xxl-inline-block btn-responsive">Retour
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-backspace-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M20 5a2 2 0 0 1 1.995 1.85l.005 .15v10a2 2 0 0 1 -1.85 1.995l-.15 .005h-11a1 1 0 0 1 -.608 -.206l-.1 -.087l-5.037 -5.04c-.809 -.904 -.847 -2.25 -.083 -3.23l.12 -.144l5 -5a1 1 0 0 1 .577 -.284l.131 -.009h11zm-7.489 4.14a1 1 0 0 0 -1.301 1.473l.083 .094l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.403 1.403l.094 -.083l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.403 -1.403l-.083 -.094l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.403 -1.403l-.094 .083l-1.293 1.292l-1.293 -1.292l-.094 -.083l-.102 -.07z" stroke-width="0" fill="currentColor" />
      </svg>
    </a>
  </div><br>

  <div class="container-fluid card">
    <div class="card-body">
      <form id="formAgence" action="{{ isset($infoAgence) ? route('updateAgence', $infoAgence->id) : route('storeAgence') }}" method="POST" class="was-validated">
        @csrf
        @if (isset($infoAgence))
        @method('PUT')
        @endif
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="validationTextarea" class="form-label fw-bold">Agence</label>
              <input type="text" name="NomAgence" class="form-control" aria-label="file example" required value="{{ isset($infoAgence) ? $infoAgence->NomAgence : '' }}">
              <div class="invalid-feedback">Le nom est obligatoire</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="validationTextarea" class="form-label fw-bold">En activité</label>
              <select class="form-select" name="EnActivite" required aria-label="select example">
                <option value="1" {{ isset($infoAgence) && $infoAgence->EnActivite == 1 ? 'selected' : '' }}>Oui</option>
                <option value="0" {{ isset($infoAgence) && $infoAgence->EnActivite == 0 ? 'selected' : '' }}>Non</option>
              </select>
              <div class="invalid-feedback">Obligatoire</div>
            </div>
          </div>
        </div>



        <div class="mb-3">
          <button type="button" id="saveButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" disabled>
            Sauvegarder
          </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
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
    </div>
  </div>

</section>
<script>
  document.getElementById('formAgence').addEventListener('input', function() {
    var form = document.getElementById('formAgence');
    var saveButton = document.getElementById('saveButton');
    if (form.checkValidity()) {
      saveButton.removeAttribute('disabled');
    } else {
      saveButton.setAttribute('disabled', 'disabled');
    }
  });

  document.getElementById('formAgence').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
    }
  });

</script>
@include('layouts.alert')
@endSection
