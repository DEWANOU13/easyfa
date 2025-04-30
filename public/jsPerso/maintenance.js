$(document).ready(function () {

    // const idUser = idUser;

    let maintenance;
    let preMaintenance;

    function updateMaintenanceStatus(data) {
        const dateNow = new Date();
        const maintenanceStartDate = new Date(data.maitenanceTimeStartFormated);
        let diffInMilliseconds = maintenanceStartDate - dateNow;

        // Calcul de la différence en jours, heures, minutes, secondes
        const diffInDays = Math.floor(diffInMilliseconds / (1000 * 60 * 60 * 24));
        diffInMilliseconds %= (1000 * 60 * 60 * 24);
        const diffInHours = Math.floor(diffInMilliseconds / (1000 * 60 * 60));
        diffInMilliseconds %= (1000 * 60 * 60);
        const diffInMinutes = Math.floor(diffInMilliseconds / (1000 * 60));
        const diffInSeconds = Math.floor((diffInMilliseconds % (1000 * 60)) / 1000);

        // Créez un message de temps restant
        const timeParts = [];
        if (diffInDays > 0) timeParts.push(`${diffInDays} jours`);
        if (diffInHours > 0) timeParts.push(`${diffInHours} heures`);
        if (diffInMinutes > 0) timeParts.push(`${diffInMinutes} minutes`);
        if (diffInSeconds > 0) timeParts.push(`${diffInSeconds} secondes`);
        const timeLeftMessage = timeParts.join(' : ') || '0 seconde';

        // Classe et style commune à ajouter
        const responseBox = $('.responseMaintenance').css({
            'margin-top': '85px', 'padding-top': '8px', 'padding-bottom': '8px'
        }).addClass('alert-box');

        if (data.maintenance) {
            maintenance = data.maintenance['maintenance'];
            preMaintenance = data.maintenance['pre_maintenance'];

            if (maintenance == 1) {
                responseBox.html('<strong>Le site est mis en maintenance</strong>');
                // Faire une redirection quand il ne s'agit pas de l'admin
                if (![1, 3].includes(idUser)) {
                    window.location.href = '/site-en-maintenance';
                }
            } else if (preMaintenance == 0) {
                // Retire les styles si la maintenance n'est pas active
                responseBox.removeClass('alert-box').css({
                    'margin-top': '', 'padding-top': '', 'padding-bottom': ''
                }).html('');
            } else {
                if (timeLeftMessage == '0 seconde') {
                    responseBox.html(`<strong>Le site est mis en maintenance</strong>`);
                } else {
                    responseBox.html(`Le site sera mis en maintenance dans : <strong>${timeLeftMessage}</strong>`);
                }

                // Vérifie si la page a déjà été rechargée
                if (!sessionStorage.getItem('pageRefreshed')) {
                    // Définit un indicateur dans sessionStorage
                    sessionStorage.setItem('pageRefreshed', 'true');
                    // Recharge la page
                    location.reload();
                }
            }
        }
    }

    function fetchMaintenanceData() {
        $.ajax({
            url: '/responseMaintenance',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                updateMaintenanceStatus(data);
                setInterval(() => updateMaintenanceStatus(data), 1000); // Met à jour le message chaque seconde
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchMaintenanceData();
    setInterval(fetchMaintenanceData, 15000); // Requête server toutes les 15 secondes
});
