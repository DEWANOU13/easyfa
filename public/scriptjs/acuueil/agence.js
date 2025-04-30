$(document).ready(function() {
    $('#magasinsTable').on('click', '.clickable-row', function() {
        var ID = $(this).attr('data-id');
        $('#modifierLink').attr('wire:click.prevent', "editAgence(" + ID + ")");

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