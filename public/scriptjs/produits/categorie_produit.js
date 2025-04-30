// js liste categorie produit

    $(document).ready(function() {
        $('#magasinsTable').on('click', '.clickable-row', function() {
            var ID = $(this).attr('data-id');
            $('#modifierLink').attr('wire:click.prevent', "editCategorie(" + ID + ")");
        });
    });

    $(document).ready(function() {
        $('.clickable-row').on('click', function() {
            // Supprimez la classe 'selected' de toutes les lignes
            $('.clickable-row').removeClass('selected');
            // Ajoutez la classe 'selected' à la ligne cliquée
            $(this).addClass('selected');
        });
    });

    // js fin liste categorie produit

    // js nouveau categorie produit
  $(document).ready(function() {
    $('#myForm').keypress(function(event) {
      if (event.key === 'Enter') {
        event.preventDefault(); // Empêche l'action par défaut du formulaire
      }
    });
  });

  $(document).ready(function() {
    var $input = $('input[name="libelle"]');
    var $button = $('#saveButton');

    // Fonction pour vérifier l'état du champ de saisie
    function checkInput() {
      if ($input.val().trim() !== '') {
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


    // js fin nouveau categorie produit