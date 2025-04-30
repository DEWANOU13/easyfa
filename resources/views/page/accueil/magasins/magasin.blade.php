@extends('layouts.master', ['title' => 'Magasin'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Magasin',
'infos2' => 'Magasin',
'infos3' => 'Liste',
])

@livewire('magasin.nouveau-magasin', ['listeAgence' => $listeAgence, 'listeMagasin' => $listeMagasin])

<script>
  $(document).ready(function() {
    $('#magasinsTable').on('click', '.clickable-row', function() {
      var ID = $(this).attr('data-id');
      $('#modifierLink').attr('wire:click.prevent', "editMagasin(" + ID + ")");

    });
  });

</script>
<script>
    const formIds = ['creeEntreeForm'];

    function preventEnterSubmission(formId) {
        document.getElementById(formId).addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    }
    formIds.forEach(preventEnterSubmission);
</script>
<script>
  $(document).ready(function() {
    $('.clickable-row').on('click', function() {
      // Supprimez la classe 'selected' de toutes les lignes
      $('.clickable-row').removeClass('selected');
      // Ajoutez la classe 'selected' à la ligne cliquée
      $(this).addClass('selected');
    });
  });

</script>

<style>
  .selected>td {
    background-color: rgb(29, 9, 101);
    /* Ou la background_color_1 de votre choix */
    color: white;
    /* background_color_1 du texte sur fond bleu */
  }

</style>

@include("layouts.alert")

@endSection
