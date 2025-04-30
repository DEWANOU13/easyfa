// js liste reglement 

$(document).ready(function() {
    // Désactiver les boutons par défaut
    $('#imprimer-button').prop('disabled', true);
    $('#imprimer-button5').prop('disabled', true);
    $('#imprimer-button8').prop('disabled', true);

    // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
    $('.clickable-row').click(function() {
        // Activer les boutons
        $('#imprimer-button').prop('disabled', false);
        $('#imprimer-button5').prop('disabled', false);
        $('#imprimer-button8').prop('disabled', false);
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

    // Lorsque le bouton d'impression est cliqué
    $('#showModal').on('click', function() {
        // Vérifiez si une ligne a été sélectionnée
        if ($('#reponse').val() !== '') {
            // Soumettez le formulaire
            $('#impressionForm').submit();
        } else {
            alert('Veuillez sélectionner un règlement avant de procéder à l\'impression.');
        }
    });
});

$(document).ready(function() {
    $('#showModal').click(function() {
        var selectedId = $('input[name="flexRadioDefault"]:checked').val();
        if (selectedId !== 'on') {
            $('#confirmationModal').modal('show');
        } else {
            alert("Veuillez sélectionner une ligne à modifier.");
        }
    })
});

$(document).ready(function() {
    $('.clickable-row').on('click', function() {
        // Supprimez la classe 'selected' de toutes les lignes
        $('.clickable-row').removeClass('selected');
        // Ajoutez la classe 'selected' à la ligne cliquée
        $(this).addClass('selected');


        var reglementId = $(this).data('id');
        //   console.log(reglementId)
        // Placez l'ID dans l'input caché
        $('#reponse').val(reglementId);

    });
});

// $(document).ready(function() {
//     $('.entree-produit').on('click', function() {
//         // Supprimez la classe 'selected-row' de toutes les lignes
//         $('.entree-produit').removeClass('selected-row');
//         // Ajoutez la classe 'selected-row' à la ligne cliquée
//         $(this).addClass('selected-row');
//         // Trouvez la case à cocher à l'intérieur de la ligne et la cochez
//         $(this).find('input[type="radio"]').prop('checked', true);
//     });
// });


$(document).ready(function() {
    $('#modifierBtn').click(function() {
        var selectedId = $('input[name="flexRadioDefault"]:checked').val();
        // console.log(selectedId);
        if (selectedId !== 'on') {
            $.ajax({
                url: "{{ route('modifier_statut_detail_reglement') }}",
                type: "GET",
                data: {
                    id: selectedId
                },
                success: function(response) {
                    // Redirection vers la route obtenue depuis le serveur
                    if (response.status === 404) {
                        // alert()
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "error",
                            title: response.error
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 4000);
                        $('#confirmationModal').modal('hide');
                    } else {
                        window.location.reload();
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: "Statut modifier avec succès"
                        });
                        $('#confirmationModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            alert("Veuillez sélectionner une ligne de detail réglement à modifier.");
        }
    });
});

    document.getElementById('monthSelect').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('anneeSelect').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });






function updateTable(reglements) {
    var tbody = document.querySelector('#proformaTable tbody');
    tbody.innerHTML = generateTableRows(reglements);
}

function generateTableRows(reglements) {
    return reglements.map(reglement => {
        return `
        <tr style="cursor:pointer;" class="clickable-row" data-url="/get-detail-reglement/${reglement.id}">
            <td hidden>${reglement.id}</td>
            <td>${reglement.Date_Reglement}</td>
            <td>${reglement.Reference_Reglement}</td>
            <td>${reglement.Code_client}</td>
            <td>${reglement.Denomination_sociale}</td>
            <td>${reglement.Montant_Regle}</td>
            <td>${reglement.Statut_Operation}</td>
            <td>${reglement.Observations}</td>

        </tr>
    `;
    }).join('');
}

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
{{-- Impression --}}

$(document).ready(function() {
    var exportButton3 = document.getElementById("exportButton3");
    var exportButton3P = document.getElementById("exportButton3P");
    var table = document.getElementById("tableReglementParPeriode").getElementsByTagName("tbody")[0];

    var ajaxResponse;

    function checkTable() {
        if (table.rows.length === 0) {
            exportButton3.disabled = true;
            exportButton3P.disabled = true;
            $('#req_message3').show();
        } else {
            exportButton3.disabled = false;
            exportButton3P.disabled = false;
            $('#req_message3').hide();
        }
    }

    function numberFormat(value) {
        return Number(value).toLocaleString('fr-FR');
    }

    checkTable();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#reglementParPeriodeForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var dateDebut = new Date($('#dateDebut').val());
        var dateFin = new Date($('#dateFin').val());

        if (dateDebut > dateFin) {
            $('#dateError3').show();
            return;
        } else {
            $('#dateError3').hide();
        }
        var $button = $('#AppliquerForm3');
        $button.addClass('loading');
        $button.prop('disabled', true);

        $.ajax({
            type: 'GET',
            url: $(this).attr('action'),
            data: formData,
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {

                ajaxResponse = response;
                console.log(response);
                $('#tableReglementParPeriode tbody').empty();

                let counter = 1;
                let grandTotal = 0;
                let total_annuler = 0;

                response.listeReglement.forEach(function(reglement) {

                    if(reglement.Statut_Operation == 'EFFECTUE'){
                        const nap = parseFloat(reglement.Montant_Regle);
                        grandTotal += nap;
                    }
                    if(reglement.Statut_Operation == 'ANNULE'){
                        const montantannuler = parseFloat(reglement.Montant_Regle);
                        total_annuler += montantannuler;
                    }

                    const row = `<tr>
                                    <td>${counter++}</td>
                                    <td>${formatDate(reglement.Date_Reglement)}</td>
                                    <td>${reglement.Reference_Reglement}</td>
                                    <td>${reglement.Denomination_sociale}</td>
                                    <td>${reglement.Statut_Operation}</td>
                                    <td>${reglement.NomAgence}</td>
                                    <td>${numberFormat(reglement.Montant_Regle)}</td>
                                </tr>`;
                    $('#tableReglementParPeriode tbody').append(row);
                });

                 const grandTotalRow = `<tr class="font-weight-bold">
                        <td colspan="6" >Total général</td>
                        <td >${numberFormat(grandTotal - total_annuler)}</td>
                    </tr>`;
                $('#tableReglementParPeriode tbody').append(grandTotalRow);


                checkTable();
                $button.removeClass('loading'); // Retire la classe .loading du bouton
                $button.prop('disabled', false); // Réactive le bouton
                },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                $button.removeClass('loading');
                $button.prop('disabled', false); // Réactive le bouton
            }
        });
    });

     $('#exportExcel_Imp').off('click').on('click', function() {
        var $button = $(this);
        if (ajaxResponse) {
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var tableReglementParPeriodeData = [];
            $('#tableReglementParPeriode tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    count: row.find('td').eq(0).text(),
                    Date_Reglement: row.find('td').eq(1).text(),
                    Reference_Reglement: row.find('td').eq(2).text(),
                    Denomination_sociale: row.find('td').eq(3).text(),
                    Statut_Operation: row.find('td').eq(4).text(),
                    NomAgence: row.find('td').eq(5).text(),
                    Montant_Regle: row.find('td').eq(6).text(),
                };
                tableReglementParPeriodeData.push(rowData);
            });

            var exportData = {
                tableReglementParPeriodeData: tableReglementParPeriodeData,
                // infoMagasin: ajaxResponse.infoMagasin,
                dateDebut: ajaxResponse.dateDebut,
                dateFin: ajaxResponse.dateFin,
                infoClient: ajaxResponse.infoClient,
                infoAgence: ajaxResponse.infoAgence
            };

            $.ajax({
                type: 'POST',
                url: '/export_excel_impression_reglement_periode',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    var currentDate = new Date();
                    var formatedDate = formatDateTime(currentDate);

                    var blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = `Liste_Reglement_Periode_${formatedDate}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en Excel');
                    $('#staticBackdropImp').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en Excel');
                }
            });
        } else {
            console.error("Aucune donnée disponible pour l'exportation.");
        }
    });

    $('#exportPDF_Imp').off('click').on('click', function() {
        var newWindow = null;

        //chargement
        var $button = $(this);
        $button.addClass('loading');
        $button.prop('disabled', true);
        $button.text('Exportation en cours...');

        var tableReglementParPeriodeData = [];
            $('#tableReglementParPeriode tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    count: row.find('td').eq(0).text(),
                    Date_Reglement: row.find('td').eq(1).text(),
                    Reference_Reglement: row.find('td').eq(2).text(),
                    Denomination_sociale: row.find('td').eq(3).text(),
                    Statut_Operation: row.find('td').eq(4).text(),
                    NomAgence: row.find('td').eq(5).text(),
                    Montant_Regle: row.find('td').eq(6).text(),
                };
                tableReglementParPeriodeData.push(rowData);
        });

        var exportData = {
            tableReglementParPeriodeData: tableReglementParPeriodeData,
            dateDebut: ajaxResponse.dateDebut,
            dateFin: ajaxResponse.dateFin,
            infoClient: ajaxResponse.infoClient,
            infoAgence: ajaxResponse.infoAgence
        };


        $.ajax({
            type: 'POST',
            url: '/export_impression_reglement_periode_pdf',
            data: JSON.stringify(exportData),
            contentType: 'application/json',
            xhrFields: {
                responseType: 'blob'
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
                $('#staticBackdropImpP').modal('hide');
            },

            error: function(xhr, status, error) {
                console.error(error);

                $button.removeClass('loading');
                $button.prop('disabled', false);
                $button.text('Oui, Exporter en PDF');
            }
        });

    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    }


});

// js fin liste reglement 

// js nouveau reglement 
$(document).ready(function() {
    $('#formcreate').on('submit', function() {
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
    // Utiliser la variable PHP $factures dans un script jQuery
    var factures = @json($factures); // Convertir la variable PHP en JSON

    // Ajouter un écouteur d'événements au champ de sélection du client
    $('select[name="client"]').change(function() {
        var clientId = $(this).val(); // Récupérer l'ID du client sélectionné
        var numFactureSelect = $('#num_facture'); // Sélecteur du champ select des factures


        // Filtrer les factures en fonction du client sélectionné
        var facturesClient = factures.filter(function(facture) {
            return facture.client_id == clientId;
        });

        // console.log('facturesClient',facturesClient);
        // Vider le champ select des factures
        numFactureSelect.empty();
        // Ajouter une option par défaut
        numFactureSelect.append('<option value="">N° Facture</option>');

        // Ajouter chaque facture correspondant au client sélectionné comme option dans le champ select
        $.each(facturesClient, function(index, facture) {
            numFactureSelect.append('<option value="' + facture.id + '-' + facture
                .Reference_facture + '">' + facture.Reference_facture + '</option>');
        });
    });
});

$(document).ready(function() {
    // Désactiver le champ N° Facture au chargement de la page
    $('#num_facture').prop('disabled', true);

    // Ajouter un écouteur d'événements au champ de sélection du client
    $('select[name="client"]').change(function() {
        var clientId = $(this).val(); // Récupérer l'ID du client sélectionné
        var numFactureSelect = $('#num_facture'); // Sélecteur du champ select des factures

        // Vérifier si un client est sélectionné
        if (clientId) {
            // Activer le champ N° Facture si un client est sélectionné
            numFactureSelect.prop('disabled', false);
        } else {
            // Désactiver le champ N° Facture si aucun client n'est sélectionné
            numFactureSelect.prop('disabled', true);
        }
    });
});
$(document).ready(function() {
    // Fonction pour activer/désactiver le bouton Valider en fonction des champs fournisseur et observation
    function toggleSubmitButton() {
        var client = $('select[name="client"]').val();
        var observation = $('textarea[name="observation"]').val();

        // Si le fournisseur et l'observation sont remplis, activer le bouton Valider, sinon le désactiver
        if (observation && client) {
            $('#bouton-valider').prop('disabled', false);
        } else {
            $('#bouton-valider').prop('disabled', true);
        }
    }

    // Surveiller les événements de changement dans les champs fournisseur et observation
    $('textarea[name="observation"], select[name="client"]').on('input', function() {
        toggleSubmitButton();
    });

    // Appeler la fonction une fois que le document est prêt pour initialiser l'état du bouton
    toggleSubmitButton();
});



$(document).ready(function() {

    // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
    // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
    function updateDesignation() {
        var factures = {!! json_encode($factures) !!};
        var detail_reglements = {!! json_encode($detail_reglements) !!};

        $('.produit-select').each(function() {
            var produit_magasin = $(this).val();
            var parties = produit_magasin.split("-");
            var selectedReference = parties[1];
            var idFacture = parties[0];

            var row = $(this).closest('.row');
            var netAPayer = row.find('.net-a-payer-input');
            var montantRegle = row.find('.montant-regle-input');
            var resteAPayer = row.find('.reste-a-payer-input');

            var getFacture = factures.find(function(element) {
                return element.Reference_facture === selectedReference;
            });

            if (detail_reglements.length > 0) {
                var reglementsFacture = detail_reglements.filter(function(element) {
                    return element.Id_Facture === parseInt(idFacture);
                });

                var sommeMontantsRegles = reglementsFacture.reduce(function(total, reglement) {
                    return total + parseFloat(reglement.Montant_Regle);
                }, 0);

                if (getFacture) {
                    netAPayer.val(getFacture.Net_a_payer);
                    montantRegle.val(sommeMontantsRegles.toFixed(2));
                    resteAPayer.val((getFacture.Net_a_payer - sommeMontantsRegles).toFixed(2));
                } else {
                    netAPayer.val('');
                    montantRegle.val('');
                    resteAPayer.val('');
                }
            } else {
                if (getFacture) {
                    netAPayer.val(getFacture.Net_a_payer);
                    montantRegle.val('0.00');
                    resteAPayer.val(getFacture.Net_a_payer);
                } else {
                    netAPayer.val('');
                    montantRegle.val('');
                    resteAPayer.val('');
                }
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
    var totalMontantPayer = 0;
    var rowCountAdd = 0;

    function clearFormFields() {
        $('#num_facture').val(null).trigger('change');
        $('#net_a_payer').val('');
        $('#montant_regle').val('');
        $('#reste_a_payer').val('');
        $('#mode_reglement').val(null).trigger('change');
        $('#montant').val('');
    }

    function toggleSubmitButton() {
        if ($('#table2 tbody tr').length > 0) {
            $('#bouton-valider').show(); // Afficher le bouton Valider
        } else {
            $('#bouton-valider').hide(); // Cacher le bouton Valider
        }
    }
    toggleSubmitButton()

    var totalMontant = [];
    // Ajouter une nouvelle ligne au deuxième tableau
    $('#add').click(function() {
        var numFacture = $('#num_facture').val();
        var netAPayer = $('#net_a_payer').val();
        var montantRegle = $('#montant_regle').val();
        var resteAPayer = $('#reste_a_payer').val();
        var modeReglement = $('#mode_reglement').val();
        var montant = parseFloat($('#montant').val());

        totalMontant.push(montant);

        // Calculer la somme des montants
        var sum = totalMontant.reduce(function(acc, val) {
            return acc + val;
        }, 0);

        console.log('sum', totalMontant.push(montant))

        if (numFacture === '' || modeReglement === '' || montant === '' || netAPayer === '' ||
            resteAPayer ===
            '') {
            alert('Veuillez remplir tous les champs.');
            return false;
        }

        // Vérifier si le montant est valide
        if (!isNaN(montant)) {
            if (sum > resteAPayer) {
                // Afficher une modal de confirmation
                if (confirm(
                        'Le montant est supérieur au montant restant à payer. Voulez-vous autoriser le surplus ?'
                    )) {
                    // Si l'utilisateur confirme, continuer le processus
                    updateOrAddRow(numFacture, netAPayer, montantRegle, resteAPayer, modeReglement,
                        montant);
                    console.log()

                    clearFormFields();
                    toggleSubmitButton();
                } else {
                    // Si l'utilisateur ne confirme pas, ne rien faire
                    // Vous pouvez ajouter ici une action si nécessaire
                    // Mettre à jour le tableau totalMontant en retirant le montant supprimé
                    var index = totalMontant.indexOf(montant);
                    if (index !== -1) {
                        totalMontant.splice(index, 1); // Retirer le montant du tableau
                    }

                    // Recalculer le total des montants restants
                    var sum = totalMontant.reduce(function(acc, val) {
                        return acc + val;
                    }, 0);
                    return false;

                }
            } else {
                // Montant valide et inférieur ou égal au reste à payer
                updateOrAddRow(numFacture, netAPayer, montantRegle, resteAPayer, modeReglement,
                    montant);
                clearFormFields();
                toggleSubmitButton();
            }
        } else {
            // Afficher un message d'erreur si le montant n'est pas valide
            alert('Veuillez entrer un montant valide.');
        }
    });

    // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
    function updateOrAddRow(numFacture, netAPayer, montantRegle, resteAPayer, modeReglement,
        montant) {
        var rows = $('#table2 tbody tr');
        var rowToUpdate = null;



        // Parcourir chaque ligne du tableau
        rows.each(function() {
            var row = $(this);
            var rowNumFacture = row.find('td:eq(0) input').val();
            var rowModeReglement = row.find('td:eq(1) input').val();

            // Comparer les données de la nouvelle ligne avec celles des lignes existantes
            if (rowNumFacture === numFacture && rowModeReglement === modeReglement) {
                rowToUpdate = row;
                return false; // Sortir de la boucle si une correspondance est trouvée
            }
        });


        // Si une ligne existe, mettre à jour les données
        if (rowToUpdate !== null) {
            $('#confirmationModal').modal('show');

            // Lorsque l'utilisateur clique sur "Continuer"
            $('#continueButton').click(function() {
                totalMontantPayer = parseFloat(montant);
                console.log(totalMontant.push(montant));

                $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));
                var updatedMontant = parseFloat(rowToUpdate.find('td:eq(2) input').val() *
                        0) +
                    parseFloat(montant);
                rowToUpdate.find('td:eq(2) input').val(updatedMontant.toFixed(2));

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
            totalMontantPayer += parseFloat(montant);
            $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));
            rowCountAdd++;
            var newRow = `<tr>
        <td>
            <div class="input-group input-group-sm mb-3">
                <input type="text" name="inputs[${rowCount}][num_facture]" class="form-control border-0" value="${numFacture}" readonly>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm mb-3">
                <input type="text" name="inputs[${rowCount}][mode_reglement]" class="form-control border-0" value="${modeReglement}" readonly>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm mb-3">
                <input type="text" name="inputs[${rowCount}][montant]" class="form-control border-0" value="${montant}" readonly>
            </div>
        </td>
        <td>
            <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
        </td>
    </tr>`;
            $('#table2 tbody').append(newRow);
        }
    }

    // Supprimer une ligne du deuxième tableau
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        var montant = parseFloat($(this).closest('tr').find('td:eq(2) input').val());

        // Mettre à jour le tableau totalMontant en retirant le montant supprimé
        var index = totalMontant.indexOf(montant);
        if (index !== -1) {
            totalMontant.splice(index, 1); // Retirer le montant du tableau
        }

        // Recalculer le total des montants restants
        var sum = totalMontant.reduce(function(acc, val) {
            return acc + val;
        }, 0);

        // Mettre à jour l'affichage du total si nécessaire
        // console.log('Total des montants restants:', sum);



        // Mise à jour du nombre de lignes restantes
        rowCountAdd--;
        $('#nb_entree').val(rowCountAdd);

        // Mise à jour du total du montant à payer
        totalMontantPayer -= montant;
        $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));

        // Vérifier s'il n'y a plus de lignes dans le tableau
        if (rowCountAdd == 0) {
            $('#bouton-valider').hide(); // Cacher le bouton Valider s'il n'y a pas de lignes
        }
    });
});

// js fin nouveau reglement 

