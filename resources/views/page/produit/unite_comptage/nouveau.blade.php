@extends('layouts.layout')
@section('content')
<style>
  .entete_tableau {
    background-color: #6104ed;
    color: white;
  }

</style>
<section>
  <br>
  <br>
  @include('message.erreur')
  <div class="row d-flex text-start p-3">
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Produits</li>
        <li class="breadcrumb-item"><a href="{{ route('page.produit.unite_comptage') }}"><span class="">Unite comptage</span></a></li>
        <li class="breadcrumb-item active"><a href="{{ route('page.unite_comptage_nouveau') }}"><span class="badge bg-primary">Nouvelle unité de comptage</span></a></li>
      </ol>
    </nav>
  </div>
  <div class="container card">
    <h4 class="text-primary text-center">
      Créer une unité de comptage
    </h4><br>

    <div class="card-body">
      <form class="was-validated" method="POST" action="{{ route('store.unite_comptage')}}" id="myForm">
        @csrf
        <div class="mb-3">
          <label for="validationTextarea" class="form-label fw-bold">Code</label>
          <input name="code" type="text" class="form-control" aria-label="file example" required>
          <div class="invalid-feedback">Le code est obligatoire</div>
        </div>
        <div class="mb-3">
          <label for="validationTextarea" class="form-label fw-bold">Libellé</label>
          <input name="libelle" type="text" class="form-control" aria-label="file example" required>
          <div class="invalid-feedback">Le libellé est obligatoire</div>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" id="saveButton" disabled data-bs-toggle="modal" data-bs-target="#staticCategorie">Sauvegarder</button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="staticCategorie" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog g modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                Souhaitez-vous vraiment faire l'enregistrement?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Continuer</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  @include('layouts.alert')
</section>
<script>
  $(document).ready(function() {
    var $input = $('input[name="libelle"]');
    var $inputCode = $('input[name="code"]');
    var $button = $('#saveButton');

    // Fonction pour vérifier l'état du champ de saisie
    function checkInput() {
      if ($input.val().trim() !== '' && $inputCode.val().trim() !== '') {
        $button.prop('disabled', false);
      } else {
        $button.prop('disabled', true);
      }
    }

    // Vérifier l'état initial du champ de saisie
    checkInput();

    // Ajouter un écouteur d'événement pour surveiller les changements dans le champ de saisie
    $input.on('input', checkInput);
  });

  $(document).ready(function() {
    $('#myForm').keypress(function(event) {
      if (event.key === 'Enter') {
        event.preventDefault(); // Empêche l'action par défaut du formulaire
      }
    });
  });

</script>
@endSection
