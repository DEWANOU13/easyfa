// js liste entrée

$(document).ready(function() {
    $('#formimport').on('submit', function() {
        var $button = $('#confirmimport');

        // Désactiver le bouton et ajouter la classe loading
        $button.prop('disabled', true);
        $button.addClass('loading');

        // Afficher le texte ou l'indicateur de chargement
        $('#loadingSpinner').show();

        // Laisser le formulaire continuer à être soumis normalement
        return true;
    });
});
$(document).ready(function() {
    $(".clickable-row").click(function() {
        var url = $(this).data("url");
        $.get(url, function(data) {
            var tableContent = $(data).find('.responsive-1')
                .html(); // Sélectionnez le contenu du premier tableau
            $(".responsive-1").html(
                tableContent); // Injectez le contenu dans le deuxième tableau
        });
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

$(document).ready(function() {
    // Désactivez le bouton "Valider" par défaut
    $('#imprimer-button').prop('disabled', true);

    // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
    $('.clickable-row').click(function() {
        // Activez le bouton "Valider"
        $('#imprimer-button').prop('disabled', false);
    });
});

$(document).ready(function() {
    $(".clickable-row").click(function() {
        // Récupérer la valeur de l'input caché
        var value = $(this).find('input[type="hidden"]').val();

        // Afficher la valeur dans la console (ou effectuer une autre action)
        // console.log(value);

        // Optionnel: assigner la valeur à un autre élément input caché avec id "value"
        $('#value').val(value);
        // Optionnel: rediriger vers une URL (décommenter pour activer)
        // window.location = $(this).data("url");
    });
});

document.getElementById('monthSelect').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

document.getElementById('anneeSelect').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

function number_format(number, decimals, dec_point, thousands_sep) {
    number = parseFloat(number).toFixed(decimals);
    var parts = number.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
    return parts.join(dec_point);
}

function applyTableEvents() {
    $(".clickable-row").click(function() {
        var url = $(this).data("url");
        $.get(url, function(data) {
            var tableContent = $(data).find('.responsive-1').html();
            $(".responsive-1").html(tableContent);
        });
        $('.clickable-row').removeClass('active');
        $(this).addClass('active');
    });


    $('#proformaTable').on('click', '.clickable-row', function() {
        var proformaId = $(this).data('url').split('/').pop();
        // console.log("ID du proforma cliqué :", proformaId);
        var editProformaUrl = "{{ route('editProforma', ['id' => ':proformaId']) }}";
        var dupliquerProformaUrl = "{{ route('dupliquerProforma', ['id' => ':proformaId']) }}";
        var conversionProformaUrl = "{{ route('conversionProforma', ['id' => ':proformaId']) }}";
        var pdfProformaUrl = "{{ route('PDFProformaA4', ['id' => ':proformaId']) }}";
        editProformaUrl = editProformaUrl.replace(':proformaId', proformaId);
        dupliquerProformaUrl = dupliquerProformaUrl.replace(':proformaId', proformaId);
        conversionProformaUrl = conversionProformaUrl.replace(':proformaId', proformaId);
        pdfProformaUrl = pdfProformaUrl.replace(':proformaId', proformaId);
        $('#modifierProformaLink').attr('href', editProformaUrl);
        $('#dupliquerProformaLink').attr('href', dupliquerProformaUrl);
        $('#conversionProformaLink').attr('href', conversionProformaUrl);
        $('#pdfProformaLink').attr('href', pdfProformaUrl);
    });

    $('.clickable-row').on('click', function() {
        $('.clickable-row').removeClass('selected');
        $(this).addClass('selected');
    });
}

function clearDetailTable() {
    var detailTbody = document.querySelector('.tableInfo2 tbody');
    detailTbody.innerHTML = `
        <tr>
            <td colspan="10" class="text-center">Aucune donnée (Selectionner une facture)</td>
        </tr>
    `;
}

$(document).ready(function() {
    applyTableEvents();
});

// js fin liste entrée


// js nouveau entrée
const formIds = ['creeFournisseurForm', 'creeEntreeForm', 'creeMagasinForm'];

function preventEnterSubmission(formId) {
    document.getElementById(formId).addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
}
formIds.forEach(preventEnterSubmission);
$(document).ready(function() {
    $('#creeEntreeForm').on('submit', function() {
        var $button = $('#confirmcreate');

        // Désactiver le bouton et ajouter la classe loading
        $button.prop('disabled', true);
        $button.addClass('loading');

        // Afficher le texte ou l'indicateur de chargement
        $('#loadingSpinner').show();

        // Laisser le formulaire continuer à être soumis normalement
        return true;
    });
});
$(document).ready(function() {
    $('#confirm-valider').click(function() {
        var rows = $('#table2 tbody tr');
        if (rows.length === 0) {
            alert('Aucune donnée trouvée dans le tableau.');
        } else {
            // Si des lignes existent, afficher la modale
            $('#confirm-validermodal').modal('show');
        }
    });

    // Action à exécuter si l'utilisateur clique sur "Modifier" dans la modale
    $('#continueButton').click(function() {
        alert('Ligne modifiée');
        // Ajouter votre logique ici pour modifier la quantité ou le prix
    });
});


$(document).ready(function() {
    $('#creeFournisseurForm').on('submit', function(event) {
        event.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    alert('Fournisseur ajoutée avec succès');

                    $('#creeFournisseur').modal('hide');

                    $('#creeFournisseurForm')[0].reset();

                    // Ajoutez le nouvel élément au <select>
                    $('#fournisseur').append(new Option(response.newCategoryName,
                        response.newCategoryId));

                    $('#fournisseur').val(response.newCategoryId);
                } else {
                    alert('Un fournisseur existe déjà avec cette dénomination');
                }
            },
            error: function(xhr, status, error) {
                alert('Un fournisseur existe déjà avec cette dénomination');
            }
        });
    });
});
$(document).ready(function() {
    $('#creeMagasinForm').on('submit', function(event) {
        event.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    alert('Magasin ajoutée avec succès');

                    $('#creeMagasin').modal('hide');

                    $('#creeMagasinForm')[0].reset();

                    // Ajoutez le nouvel élément au <select>
                    $('#magasin').append(new Option(response.newMagasinName, response
                        .newMagasinId));

                    $('#magasin').val(response.newMagasinId);
                } else {
                    alert('Un magasin existe déjà avec ce nom');
                }
            },
            error: function(xhr, status, error) {
                alert('Un magasin existe déjà avec ce nom');
            }
        });
    });
});
document.querySelector('form').addEventListener('submit', function(e) {
    var input = document.querySelector('input[name="NumeroIfu"]');
    if (input.value.length > 0 && input.value.length !== 13) {
        input.value = ''; // Vider le champ si la longueur n'est pas 13
    }
});

document.querySelector('input[name="NumeroIfu"]').addEventListener('input', function(e) {
    var value = e.target.value;
    if (value.length > 13) {
        e.target.value = value.slice(0, 13);
    }
});
document.getElementById('creeFournisseurForm').addEventListener('input', function() {
    var form = document.getElementById('creeFournisseurForm');
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

document.getElementById('creeFournisseurForm').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
    }
});
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
$(document).ready(function() {

    // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
    function updateDesignation() {
        var produits = {!! json_encode($produits) !!};
        $('.produit-select').each(function() {
            var selectedReference = $(this).val();
            // console.log(selectedReference)
            // var designationInput = $(this).closest('table').find('.designation-input');
            var designationInput = $('#designation');
            if (produits[selectedReference]) {
                designationInput.val(selectedReference + '|' + produits[selectedReference]);
                // console.log('des--', designationInput.val(selectedReference))
            } else {
                designationInput.val('');
            }
        });
    }
    // Gérer les événements de changement sur les sélecteurs de produits
    $(document).on('change', '.produit-select', function() {
        updateDesignation();
    });
    // Appeler la fonction pour mettre à jour la désignation lorsque le document est prêt
    updateDesignation();
});

$(document).ready(function() {
    $('.clickable-row').on('click', function() {
        // Supprimez la classe 'selected' de toutes les lignes
        $('.clickable-row').removeClass('selected');
        // Ajoutez la classe 'selected' à la ligne cliquée
        $(this).addClass('selected');
    });
});

$(document).ready(function() {

    var totalPrixAchat = 0;
    var rowCountAdd = 0;

    function clearFormFields() {
        $('#produit').val(null).trigger('change');
        $('#designation').val('');
        $('#magasin').val('');
        $('#quantity').val('');
        $('#prix_achat').val('');
    }

    function toggleSubmitButton() {
        if ($('#table2 tbody tr').length > 0) {
            $('#bouton-valider').show(); // Afficher le bouton Valider
        } else {
            $('#bouton-valider').hide(); // Cacher le bouton Valider
        }
    }
    toggleSubmitButton()

    // Ajouter une nouvelle ligne au deuxième tableau
    $('#add').click(function() {

        var reference = $('#produit').val();
        var designation = $('#designation').val();
        var magasin = $('#magasin').val();
        var quantite = parseFloat($('#quantity').val());
        var prix_achat = parseFloat($('#prix_achat').val());

        // console.log(reference, designation)
        // Vérifier si la quantité et le prix sont valides
        if (!isNaN(quantite) && !isNaN(prix_achat)) {
            // Ajouter ou mettre à jour la ligne
            updateOrAddRow(reference, designation, magasin, quantite, prix_achat);
            clearFormFields();
            toggleSubmitButton();
        } else {
            // Afficher un message d'erreur si la quantité ou le prix n'est pas un nombre valide
            return alert('Veuillez entrer une quantité et un prix valides.');
        }

    });

    // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
    function updateOrAddRow(reference, designation, magasin, quantite, prix_achat) {
        // console.log(quantite, prix_achat)
        var rows = $('#table2 tbody tr');
        var rowToUpdate = null;



        // console.log('rowCount', rowCountAdd)

        // console.log(totalPrixAchat)

        if (reference === '' || designation === '' || magasin === '' || quantite === '' || prix_achat ===
            '') {
            alert('Veuillez remplir tous les champs.');
            return false;
        }

        // Vérification de la validité des valeurs numériques
        if (isNaN(parseFloat(quantite)) || isNaN(parseFloat(prix_achat))) {
            alert('Les champs quantité et prix d\'achat doivent être des nombres.');
            return false;
        }

        if (quantite <= 0 || prix_achat <= 0) {
            alert('La quantité et le prix doivent être des nombres positifs.');
            return false;
        }

        // Parcourir chaque ligne du tableau
        rows.each(function() {
            var row = $(this);
            // var rowReference = row.find('td:eq(0)').text();
            // var rowDesignation = row.find('td:eq(1)').text();
            // var rowMagasin = row.find('td:eq(2)').text();
            // var rowQuantite = row.find('td:eq(3)').text();
            // var rowPrixAchat = row.find('td:eq(4)').text();

            var rowReference = row.find('td:eq(0) input').val();
            // console.log('je suis ici', rowReference)
            var rowDesignation = row.find('td:eq(1) input').val();
            var rowMagasin = row.find('td:eq(2) input').val();
            // VAR rowQuantite = row.find('td:eq(3) input').val()
            // VAR rowPrixAchat = row.find('td:eq(4) input').val()

            // Comparer les données de la nouvelle ligne avec celles des lignes existantes
            if (rowReference === reference && rowDesignation === designation && rowMagasin ===
                magasin) {
                rowToUpdate = row;
                // console.log('rowUpdate', rowToUpdate);()
                return false; // Sortir de la boucle si une correspondance est trouvée
            }
        });

        // Si une ligne existe, mettre à jour la quantité ou le prix
        if (rowToUpdate !== null) {
            // totalPrixAchat = parseFloat(prix_achat);
            // $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
            // var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) + parseFloat(
            //     quantite);
            // var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(4) input').val() * 0) + parseFloat(
            //     prix_achat);
            // rowToUpdate.find('td:eq(3) input').val(updatedQuantite.toFixed(2));
            // rowToUpdate.find('td:eq(4) input').val(updatedPrixAchat.toFixed(2));

            $('#confirmationModal').modal('show');

            // Lorsque l'utilisateur clique sur "Continuer"
            $('#continueButton').click(function() {
                totalPrixAchat = parseFloat(prix_achat * quantite);
                $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) +
                    parseFloat(quantite);
                var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(4) input').val() * 0) +
                    parseFloat(prix_achat);
                rowToUpdate.find('td:eq(3) input').val(updatedQuantite.toFixed(2));
                rowToUpdate.find('td:eq(4) input').val(updatedPrixAchat.toFixed(2));

                // Fermer le modal
                $('#confirmationModal').modal('hide');
            });

            // Lorsque l'utilisateur clique sur "Annuler" ou ferme le modal
            $('#cancelButton').click(function() {
                // Ne rien faire
                // Fermer le modal
                $('#confirmationModal').modal('hide');
            });

        } else {

            // Ajouter la nouvelle ligne si aucune correspondance n'est trouvée
            var rowCount = $('#table2 tbody tr').length + 1;
            $('#nb_entree').val(rowCount);
            totalPrixAchat += parseFloat(prix_achat * quantite);
            $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
            rowCountAdd++;
            var newRow = `<tr>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 designation-input" value="${designation}" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" name="inputs[${rowCount}][magasin]" class="form-control border-0" value="${magasin}" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="number" name="inputs[${rowCount}][quantity]" class="form-control border-0" id="quantity" value="${quantite}" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="number" name="inputs[${rowCount}][prix_achat]" class="form-control border-0" id="prix_achat" value="${prix_achat}" readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm mb-3">
                        <input type="number" name="inputs[${rowCount}][montant]" class="form-control border-0" id="montant" value="${quantite * prix_achat}" readonly>
                    </div>
                </td>
                <td>
                    <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                </td>
            </tr> `;
            $('#table2 tbody').append(newRow);
        }
    }



    // // Supprimer une ligne du deuxième tableau
    // $(document).on('click', '.remove-row', function() {
    //     $(this).closest('tr').remove();
    //     var prixAchat = parseFloat($(this).closest('tr').find('td:eq(4) input').val())

    //     // console.log('prix', prixAchat)
    //     // Vérifier si le nombre de lignes est égal à zéro
    //     if ($('#table2 tbody tr').length == 0) {
    //         $('#bouton-valider').hide(); // Cacher le bouton Valider s'il n'y a pas de lignes
    //     }
    //     // console.log('avant', totalPrixAchat)
    //     totalPrixAchat -= prixAchat;
    //     console.log(totalPrixAchat)
    //     $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
    //     document.getElementById('prixAchatTotal').oninput = updateOrAddRow;
    //     rowCount -= 1
    //     console.log('-1', rowCount)
    //     $('#nb_entree').val(rowCount);
    //     document.getElementById('nb_entree').oninput = updateOrAddRow;

    //     // document.getElementById('nb_entree').oninput = updateOrAddRow;

    // });

    var rowCount = $('#table2 tbody tr')
        .length; // Initialisation de rowCount à la valeur actuelle du nombre de lignes

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        var prixAchat = parseFloat($(this).closest('tr').find('td:eq(4) input').val());
        var quantite = parseFloat($(this).closest('tr').find('td:eq(3) input').val());



        // Mise à jour de l'affichage du nombre d'entrées

        rowCount -= 1;
        // console.log('sup row count', rowCount)
        // Mise à jour du nombre de lignes restantes
        rowCountAdd--;
        $('#nb_entree').val(rowCountAdd);

        // console.log('mise a jour', rowCountAdd)
        // Vérifier s'il n'y a plus de lignes dans le tableau
        if (rowCountAdd == 0) {
            $('#bouton-valider').hide(); // Cacher le bouton Valider s'il n'y a pas de lignes
        }

        // Mise à jour du total du prix d'achat
        totalPrixAchat -= prixAchat * quantite;
        $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
    });

});



// Masquer le bouton Valider au chargement de la page si le tableau est vide initialement
$(document).ready(function() {
    if ($('#table2 tbody tr').length == 0) {
        $('#bouton-valider').hide(); // Cacher le bouton Valider s'il n'y a pas de lignes
    }
});


$(document).ready(function() {
    // Fonction pour activer/désactiver le bouton Valider en fonction des champs fournisseur et observation
    function toggleSubmitButton() {
        var fournisseur = $('select[name="fournisseur"]').val();
        var observation = $('textarea[name="observation"]').val();

        // Si le fournisseur et l'observation sont remplis, activer le bouton Valider, sinon le désactiver
        if (fournisseur && observation) {
            $('#bouton-valider').prop('disabled', false);
        } else {
            $('#bouton-valider').prop('disabled', true);
        }
    }

    // Surveiller les événements de changement dans les champs fournisseur et observation
    $('select[name="fournisseur"], textarea[name="observation"]').on('input', function() {
        toggleSubmitButton();
    });

    // Appeler la fonction une fois que le document est prêt pour initialiser l'état du bouton
    toggleSubmitButton();
});

$(document).ready(function() {
    $('#myForm').keypress(function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Empêche l'action par défaut du formulaire
        }
    });
});


// js fin nouveau entrée







