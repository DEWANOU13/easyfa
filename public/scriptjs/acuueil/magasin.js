// js liste magasin 

  $(document).ready(function() {
    $('#magasinsTable').on('click', '.clickable-row', function() {
      var ID = $(this).attr('data-id');
      $('#modifierLink').attr('wire:click.prevent', "editMagasin(" + ID + ")");

    });
  });


    const formIds = ['creeEntreeForm'];

    function preventEnterSubmission(formId) {
        document.getElementById(formId).addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    }
    formIds.forEach(preventEnterSubmission);

  $(document).ready(function() {
    $('.clickable-row').on('click', function() {
      // Supprimez la classe 'selected' de toutes les lignes
      $('.clickable-row').removeClass('selected');
      // Ajoutez la classe 'selected' à la ligne cliquée
      $(this).addClass('selected');
    });
  });


// js fin liste magasin 