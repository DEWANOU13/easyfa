// js liste categorie client 
$(document).ready(function() {
    $('#categorieClientTable').on('click', '.clickable-row', function() {
        var ID = $(this).attr('data-id');

        $('#modifierLink').attr('wire:click.prevent', "editCategorieClient(" + ID + ")");

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

// js fin liste categorie client 

// js nouveau categorie client 

  document.getElementById('LibelleInput').addEventListener('input', function() {
    var value = this.value.toUpperCase();
    this.value = value;
  });


  document.getElementById('formCatClient').addEventListener('input', function() {
    var form = document.getElementById('formCatClient');
    var saveButton = document.getElementById('saveButton');
    if (form.checkValidity()) {
      saveButton.removeAttribute('disabled');
    } else {
      saveButton.setAttribute('disabled', 'disabled');
    }
  });

  document.getElementById('formCatClient').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
    }
  });


// js fin nouveau categorie client 