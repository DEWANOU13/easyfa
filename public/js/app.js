/* Infobulle personnalisé */
$(function () {
    // Initialisez les infobulles
    $('[data-toggle="tooltip"]').tooltip({
        title: function () {
            return $(this).data('title');
        }
    });
});
/* Fin Infobulle personnalisé */

// Recherche sur les groupes
const searchGroupe = () => {
    let filter = $('#searchGroupe').val();

    $.ajax({
        type: 'POST',
        url: '../searchGroupe',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            $("#resultsGroupe").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};
$('#searchGroupe').on('keyup', searchGroupe);

// Recherche sur les utilisateurs
const searchUser = () => {
    let filter = $('#searchUser').val();

    $.ajax({
        type: 'POST',
        url: '../searchUser',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            $("#resultsUser").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};
$('#searchUser').on('keyup', searchUser);


// Recherche sur les équipements dans les tables
const searchClient = () => {
    let filter = $('#searchClient').val();

    $.ajax({
        type: 'POST',
        url: '../searchClient',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            $("#resultsClient").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};

// Attachez l'événement keyup à la fonction de recherche avec débouncing
$('#searchClient').on('keyup', searchClient);

// Recherche sur les agences---------------------------------------------
const searchAgence = () => {
    let filter = $('#searchAgence').val();

    $.ajax({
        type: 'POST',
        url: '../searchAgence',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            $("#resultsAgence").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};

// Attachez l'événement keyup à la fonction de recherche avec débouncing
$('#searchAgence').on('keyup', searchAgence);
// Recherche sur les agences---------------------------------------------
const searchFournisseur = () => {
    let filter = $('#searchFournisseur').val();

    $.ajax({
        type: 'POST',
        url: '../searchFournisseur',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            $("#resultsFournisseur").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};

// Attachez l'événement keyup à la fonction de recherche avec débouncing
$('#searchFournisseur').on('keyup', searchFournisseur);



// Renvoie dynamiquement les agences de l'utilisateur a qui on veut affcetre ou non un droit
const selectAgenceDynamique = () => {
    let filter = $('#userAgence_id').val();

    // Requete Ajax pour equipement
    $.ajax({
        type: 'POST',
        url: '/selectAgenceDynamique',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: filter
        },
        success: function (data) {
            // Mettez à jour la section des résultats avec les nouvelles données
            $("#agence_id").html(data);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error(status);
            console.error(error);
        }
    });
};
$('#userAgence_id').on('change', selectAgenceDynamique);
