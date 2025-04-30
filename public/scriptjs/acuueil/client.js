// js liste client 

        $(document).ready(function() {
            $('#exportExcelClient').on('click', function() {
                var tableClientData = [];
                $('#clientTable tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        numero: row.find('td').eq(0).text(),
                        code_client: row.find('td').eq(1).text(),
                        denomination_sociale: row.find('td').eq(2).text(),
                        numero_ifu: row.find('td').eq(3).text(),
                        adresse: row.find('td').eq(4).text(),
                        telephone: row.find('td').eq(5).text(),
                        email: row.find('td').eq(6).text()
                    };
                    tableClientData.push(rowData);
                });


                var exportData = {
                    tableClientData: tableClientData
                };
                // console.log(exportData);

                $.ajax({
                    type: 'POST',
                    url: '/export_excel_client',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'liste_client.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(status);
                        console.log(error);
                    }
                });
            });
        });

    // {{-- Importation en fichier PDF de la liste des client  --}}

        $('#importClientPDF_Imp').off('click').on('click', function() {
            var newWindow = null;

            //chargement
            var $button = $(this);
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var tableClientData = [];
            $('#clientTable tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    numero: row.find('td').eq(0).text(),
                    code_client: row.find('td').eq(1).text(),
                    denomination_sociale: row.find('td').eq(2).text(),
                    numero_ifu: row.find('td').eq(3).text(),
                    adresse: row.find('td').eq(4).text(),
                    telephone: row.find('td').eq(5).text(),
                    email: row.find('td').eq(6).text()
                };
                tableClientData.push(rowData);
            });

            var exportData = {
                tableClientData: tableClientData,
            };


            $.ajax({
                type: 'POST',
                url: '/import_pdf_client',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    var url = window.URL.createObjectURL(
                        blob);

                    newWindow = window.open(url, '_blank');

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');

                    $('#imprimerClientPdf').modal('hide');
                },

                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');
                }
            });
        });

    // {{-- Exportation en fichier excel de la liste des comptes des client --}}
        $(document).ready(function() {
            $('#exportExcelClientCompte').on('click', function() {
                var tableClientCompteData = [];
                $('#clientsTable tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        date: row.find('td').eq(0).text(),
                        client: row.find('td').eq(1).text(),
                        operation: row.find('td').eq(2).text(),
                        justificatif: row.find('td').eq(3).text(),
                        debit: row.find('td').eq(4).text(),
                        credit: row.find('td').eq(5).text(),
                        compte: row.find('td').eq(6).text()
                    };
                    tableClientCompteData.push(rowData);
                });


                var exportData = {
                    tableClientCompteData: tableClientCompteData
                };

                $.ajax({
                    type: 'POST',
                    url: '/export_excel_client_compte',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'compte_client.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(status);
                        console.log(error);
                    }
                });
            });
        });

    // {{-- Importation en fichier PDF de la liste des comptes des clients --}}
        var ajaxResponse;

        $('#exportPdfClientCompte').off('click').on('click', function() {
            var newWindow = null;

            //chargement
            var $button = $(this);
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var clientsTableData = [];
            $('#clientsTable tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    date: row.find('td').eq(0).text(),
                    client: row.find('td').eq(1).text(),
                    operation: row.find('td').eq(2).text(),
                    justificatif: row.find('td').eq(3).text(),
                    debit: row.find('td').eq(4).text(),
                    credit: row.find('td').eq(5).text(),
                    compte: row.find('td').eq(6).text()
                };
                clientsTableData.push(rowData);
            });

            var exportData = {
                clientsTableData: clientsTableData,
                debut_periode: @json($debut_periode),
                fin_periode: @json($fin_periode),
                clientRecherche: @json($clientRecherche),
            };


            $.ajax({
                type: 'POST',
                url: '/import_pdf_compte_client',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    var url = window.URL.createObjectURL(
                        blob);

                    newWindow = window.open(url, '_blank');

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');

                    $('#staticBackdropClientComptePdf').modal('hide');
                },

                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');
                }
            });

        });

        $(document).ready(function() {
            $('#clientTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                var modifierUrl = "{{ route('editClient', ['id' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                // Mettre à jour l'attribut href du lien
                $('#modifierLink').attr('href', modifierUrl);
            });
        });

        $(document).ready(function() {
            $('#clientTable').on('click', '.clickable-row', function() {
                $('#clientTable .clickable-row td').css({
                    'background-color': '',
                    'color': 'black'
                });
                $(this).find('td').css({
                    'background-color': 'rgb(29, 9, 101)',
                    'color': 'white'
                });

                var ID = $(this).data('id');

                $('#clientDetail').attr('data-bs-target', '#exampleModal_' + ID);
            });
        });

    // js fin liste client 


    // js nouveau client 

 
    document.querySelector('[name="Categorie_client_id"]').addEventListener('change', function() {
        if (this.value === 'nouvelle') {
            window.location.href = "{{ route('ShowFormCategorieClient') }}";
        }
    });

    $('#formClient').on('submit', function(e) {

               var $button = $('#save-button');
                  $button.addClass('loading');
                  $button.prop('disabled', true);

      });

    document.getElementById('LibelleInput').addEventListener('input', function() {
        var value = this.value.toUpperCase();
        this.value = value;
    });

$(document).ready(function() {
    $('#categorieForm').on('submit', function(event) {
        event.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {

                    alert('Catégorie ajoutée avec succès');

                    $('#creeCategorie').modal('hide');

                    $('#categorieForm')[0].reset();

                    // Ajoutez le nouvel élément au <select>
                    $('#inputGroupSelect04').append(new Option(response.newCategoryName, response.newCategoryId));

                    $('#inputGroupSelect04').val(response.newCategoryId);
                } else {
                    alert('Erreur lors de l\'ajout de la catégorie');
                }
            },
            error: function(xhr, status, error) {
                alert('Erreur lors de l\'ajout de la catégorie');
            }
        });
    });
});


// <!-- Script pour remplir le select avec les pays -->
    document.addEventListener('DOMContentLoaded', (event) => {
        const countries = [
            "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola",
            "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche",
            "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin",
            "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil",
            "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada", "Cap-Vert",
            "République centrafricaine", "Chili", "Chine", "Chypre", "Colombie", "Comores",
            "République du Congo", "République démocratique du Congo", "Îles Cook", "Corée du Nord",
            "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti",
            "République dominicaine", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée",
            "Espagne", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon",
            "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau",
            "Guinée équatoriale", "Guyana", "Haïti", "Honduras", "Hongrie", "Inde", "Indonésie", "Irak",
            "Iran", "Irlande", "Islande", "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan",
            "Kenya", "Kirghizistan", "Kiribati", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban",
            "Liberia", "Libye", "Liechtenstein", "Lituanie", "Luxembourg", "Macédoine", "Madagascar",
            "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Îles Marshall", "Maurice",
            "Mauritanie", "Mexique", "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro",
            "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", "Niger", "Nigeria", "Niue", "Norvège",
            "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Palestine",
            "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", "Philippines",
            "Pologne", "Portugal", "Qatar", "Roumanie", "Royaume-Uni", "Russie", "Rwanda",
            "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Marin", "Saint-Vincent-et-les Grenadines",
            "Salomon", "Salvador", "Samoa", "São Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles",
            "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud",
            "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", "Eswatini", "Tadjikistan", "Tanzanie",
            "Tchad", "République tchèque", "Thaïlande", "Timor-Oriental", "Togo", "Tonga",
            "Trinité-et-Tobago", "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay",
            "Vanuatu", "Vatican", "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
        ];

        const select = document.getElementById('countrySelect');
        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.text = country;
            select.appendChild(option);
        });
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        var input = document.querySelector('input[name="Numero_ifu"]');
        if (input.value.length > 0 && input.value.length !== 13) {
            input.value = ''; // Vider le champ si la longueur n'est pas 13
        }
    });

    document.querySelector('input[name="Numero_ifu"]').addEventListener('input', function(e) {
        var value = e.target.value;
        if (value.length > 13) {
            e.target.value = value.slice(0, 13);
        }
    });  

    document.getElementById('formClient').addEventListener('input', function() {
        var form = document.getElementById('formClient');
        var saveButton = document.getElementById('saveButton');
        var ifuInput = document.getElementById(
            'ifuInput'); // Ajoutez cette ligne pour récupérer l'élément d'entrée du numéro IFU

        // Vérifiez si le formulaire est valide et si le numéro IFU a une longueur entre 1 et 12 caractères
        if (form.checkValidity() && (ifuInput.value.length === 0 || ifuInput.value.length > 13 || (ifuInput
                .value.length >= 13 && ifuInput.value.length <= 13))) {
            saveButton.removeAttribute('disabled'); // Activer le bouton de sauvegarde
        } else {
            saveButton.setAttribute('disabled', 'disabled'); // Désactiver le bouton de sauvegarde
        }
    });

    document.getElementById('formClient').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
// </script> --}}
 /*    document.getElementById('formClient').addEventListener('input', function() {
var form = document.getElementById('formClient');
var saveButton = document.getElementById('saveButton');
var ifuInput = document.getElementById('ifuInput'); // Récupérer l'élément IFU

// Vérifiez si Select2 a bien sélectionné une valeur
var categorieClient = $('select[name="Categorie_client_id"]').val(); // Pour la catégorie client
var pays = $('select[name="Pays"]').val(); // Pour le pays
var Denomination_sociale = $('input[name="Denomination_sociale"]').val();
var Code_client = $('input[name="Code_client"]').val();
var Statut_client = $('input[name="Statut_client"]').val();

// Valider si le formulaire est valide et si Select2 a une valeur sélectionnée
if (Denomination_sociale && Code_client && Statut_client && categorieClient && pays && (ifuInput.value.length === 0 || (ifuInput.value.length === 13))) {
    saveButton.removeAttribute('disabled'); // Activer le bouton de sauvegarde
} else {
    saveButton.setAttribute('disabled', 'disabled'); // Désactiver le bouton de sauvegarde
}
});

document.getElementById('formClient').addEventListener('keydown', function(event) {
if (event.key === 'Enter') {
    event.preventDefault();
}
}); */

$(document).ready(function() {
$('.js-single').select2({
    placeholder: 'Sélectionnez une option',
    allowClear: true
});

// Validation au changement de Select2
$('.js-single').on('change', function() {
    var select = $(this);
    // Vérifie si une option est sélectionnée
    if (select.val() === "" || select.val() === null) {
        select.addClass('is-invalid'); // Ajoute une classe Bootstrap pour l'erreur
    } else {
        select.removeClass('is-invalid'); // Supprime l'erreur si une option est sélectionnée
    }

    // Active ou désactive le bouton de sauvegarde en fonction de la validité du formulaire
    checkFormValidity();
});
});

// Fonction pour vérifier si tout le formulaire est valide
function checkFormValidity() {
var form = document.getElementById('formClient');
var saveButton = document.getElementById('saveButton');
var ifuInput = document.getElementById('ifuInput'); // Récupérer l'élément IFU
var isValid = form.checkValidity(); // Vérifier la validité du formulaire natif

// Vérifier manuellement les champs Select2
$('.js-single').each(function() {
    if ($(this).val() === "" || $(this).val() === null) {
        isValid = false;
    }
});

// Vérifiez si le numéro IFU est valide (longueur 13 ou vide)
if (ifuInput.value.length > 0 && ifuInput.value.length !== 13) {
    isValid = false;
}

if (isValid) {
    saveButton.removeAttribute('disabled'); // Activer le bouton de sauvegarde
} else {
    saveButton.setAttribute('disabled', 'disabled'); // Désactiver le bouton de sauvegarde
}
}


    // js fin nouveau client 