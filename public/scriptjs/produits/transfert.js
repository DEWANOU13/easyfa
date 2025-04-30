// js liste transfert

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

// js fin liste tansfert


// js nouveau transfert

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
                        if(response.success) {

                            alert('Magasin ajoutée avec succès');

                            $('#creeMagasin').modal('hide');

                            $('#creeMagasinForm')[0].reset();

                            // Ajoutez le nouvel élément au <select>
                            $('.magasinCreateimput').append(new Option(response.newMagasinName, response.newMagasinId));

                            $('.magasinCreateimput').val(response.newMagasinId);
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

        $(document).ready(function() {

            // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
            function updateDesignation() {
                var stockProduits = {!! json_encode($stock_produits) !!};
                $('.produit-select').each(function() {
                    var produit_magasin = $(this).val();
                    var parties = produit_magasin.split(" ");
                    var selectedReference = parties[1]; // Contiendra "P002"
                    var magasin = parties[2];

                    var parentDiv = $(this).closest('.row');
                    var designationInput = parentDiv.find('.designation-input');
                    var magasinInput = parentDiv.find('.magasin-input');
                    var quantiteDispoInput = parentDiv.find('.quantite-dispo-input');

                    var produit = stockProduits.find(function(element) {
                        return element.Reference === selectedReference && element.Id_Magasin ===
                            parseInt(magasin);
                    });

                    // Vérifier si la référence sélectionnée existe dans les stocks
                    if (produit) {
                        // Remplir les champs avec les données du produit
                        designationInput.val(produit.Designation);
                        magasinInput.val(produit.Id_Magasin + '-' + produit.NomMagasin);
                        quantiteDispoInput.val(produit.Qte_stockee);
                    } else {
                        // Si la référence n'existe pas, vider les champs
                        designationInput.val('');
                        magasinInput.val('');
                        quantiteDispoInput.val('');
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
        // function get() {
        //     // Ajoutez cet événement input pour mettre à jour la quantité disponible et vérifier la quantité de sortie automatiquement
        //     var row = $(this).closest('tr');
        //     var i = row.find('.quantite-dispo-input');
        //     var ip = parseFloat(i.val());
        //     // quantiteDispoInput.val(quantiteDispoInitiale);
        //     $('.quantite-dispo-input').val(ip.toFixed(2))
        // }
        $('#quantity_transfer').on('input', function() {
            var row = $(this).closest('tr');
            var quantiteSortie = parseFloat($(this).val());
            var quantiteDispoInput = row.find('.quantite-dispo-input');
            var quantiteDispo = parseFloat(quantiteDispoInput.val());
            var quantiteDispoInitiale = parseFloat(quantiteDispoInput.data(
                'initial-value')); // Valeur initiale de la quantité disponible

            if (!isNaN(quantiteSortie) && !isNaN(quantiteDispo)) {
                if (quantiteSortie > quantiteDispo) {
                    alert("La quantité sortie ne peut pas être supérieure à la quantité disponible.");
                    $(this).val(
                        quantiteDispo); // Réinitialiser la valeur de la quantité sortie à la quantité disponible
                } else {
                    // var nouvelleQuantiteDispo = quantiteDispo - quantiteSortie;
                    // quantiteDispoInput.val(nouvelleQuantiteDispo);
                }
            }
        });
        // // Gérer les événements de changement sur l'entrée de quantité de sortie
        // $('#quantity_transfer').on('input', function() {
        //     var quantityAvailable = parseFloat($('#quantite-dispo-input').val());
        //     var quantityOut = parseFloat($(this).val());

        //     if (!isNaN(quantityOut)) {
        //         // Mettre à jour la quantité disponible en soustrayant la quantité de sortie
        //         var newQuantityAvailable = quantityAvailable - quantityOut;
        //         $('#quantite-dispo-input').val(newQuantityAvailable.toFixed(2));

        //         // Si la quantité de sortie est réinitialisée à zéro, restaurer la quantité disponible
        //         if (quantityOut === 0) {
        //             $('#quantite-dispo-input').val(quantityAvailable.toFixed(2));
        //         }
        //     }
        // });

        $(document).ready(function() {
            var totalPrixAchat = 0;
            var rowCountAdd = 0;

            function clearFormFields() {
                $('#produit').val(null).trigger('change');
                $('#designation').val('');
                $('#magasin').val('');
                $('#quantity').val('');
                $('#quantity_transfer').val('');
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
                var quantity_transfer = parseFloat($('#quantity_transfer').val());
                // Vérifier si la quantité et le prix sont valides
                if (!isNaN(quantite) && !isNaN(quantity_transfer)) {
                    // Ajouter ou mettre à jour la ligne
                    updateOrAddRow(reference, designation, magasin, quantite, quantity_transfer);
                    clearFormFields();
                    toggleSubmitButton();
                } else {
                    // Afficher un message d'erreur si la quantité ou le prix n'est pas un nombre valide
                    return alert('Veuillez entrer une quantité et un prix valides.');
                }
            });

            // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
            function updateOrAddRow(reference, designation, magasin, quantite, quantity_transfer) {
                // console.log(quantite, quantity_transfer)
                var rows = $('#table2 tbody tr');
                var rowToUpdate = null;



                // console.log('rowCount', rowCountAdd)

                // console.log(totalPrixAchat)

                if (reference === '' || designation === '' || magasin === '' || quantite === '' ||
                    quantity_transfer ===
                    '') {
                    alert('Veuillez remplir tous les champs.');
                    return false;
                }

                // Vérification de la validité des valeurs numériques
                if (isNaN(parseFloat(quantite)) || isNaN(parseFloat(quantity_transfer))) {
                    alert('Les champs quantité et prix d\'achat doivent être des nombres.');
                    return false;
                }

                if (quantite <= 0 || quantity_transfer <= 0) {
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
                    // totalPrixAchat = parseFloat(quantity_transfer);
                    // $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                    // var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) + parseFloat(
                    //     quantite);
                    // var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(4) input').val() * 0) + parseFloat(
                    //     quantity_transfer);
                    // rowToUpdate.find('td:eq(3) input').val(updatedQuantite.toFixed(2));
                    // rowToUpdate.find('td:eq(4) input').val(updatedPrixAchat.toFixed(2));

                    $('#confirmationModal').modal('show');

                    // Lorsque l'utilisateur clique sur "Continuer"
                    $('#continueButton').click(function() {
                        totalPrixAchat = parseFloat(quantity_transfer);
                        $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                        var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) +
                            parseFloat(quantite);
                        var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) +
                            parseFloat(quantity_transfer);
                        rowToUpdate.find('td:eq(3) input').val(updatedQuantite.toFixed(2));
                        rowToUpdate.find('td:eq(3) input').val(updatedPrixAchat.toFixed(2));

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
                    totalPrixAchat += parseFloat(quantity_transfer);
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
                                    <input type="number" name="inputs[${rowCount}][quantity_transfer]" class="form-control border-0" id="quantity_transfer" value="${quantity_transfer}" readonly>
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
                var prixAchat = parseFloat($(this).closest('tr').find('td:eq(3) input').val());
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
                totalPrixAchat -= prixAchat;
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
                var magasin_source = $('select[name="magasin_source"]').val();
                var magasin_destination = $('select[name="magasin_destination"]').val();
                var observation = $('textarea[name="observation"]').val();

                // Si le fournisseur et l'observation sont remplis, activer le bouton Valider, sinon le désactiver
                if (magasin_source && magasin_destination && observation) {
                    $('#bouton-valider').prop('disabled', false);
                } else {
                    $('#bouton-valider').prop('disabled', true);
                }
            }

            // Surveiller les événements de changement dans les champs fournisseur et observation
            $('select[name="magasin_source"], select[name="magasin_destination"], textarea[name="observation"]').on(
                'input',
                function() {
                    toggleSubmitButton();
                });

            // Appeler la fonction une fois que le document est prêt pour initialiser l'état du bouton
            toggleSubmitButton();
        });

        $(document).ready(function() {
            // Fonction pour activer/désactiver les sélections de magasin en fonction l'une de l'autre
            function desabledButtonSelectMagasin() {
                var magasin_destination = $('select[name="magasin_destination"]').val();
                var magasin_source = $('select[name="magasin_source"]').val();
                var magasin_destination_select = $('select[name="magasin_destination"]');
                var magasin_source_select = $('select[name="magasin_source"]');
                var magasin_destination_options = magasin_destination_select.find('option');
                var magasin_source_options = magasin_source_select.find('option');

                // Réinitialiser l'affichage de toutes les options
                magasin_source_options.show();
                magasin_destination_options.show();

                // Filtrer les options du magasin source pour masquer le magasin destination sélectionné
                magasin_source_options.each(function() {
                    if ($(this).val() === magasin_destination) {
                        $(this).hide();
                    }
                });

                // Filtrer les options du magasin de destination pour masquer le magasin source sélectionné
                magasin_destination_options.each(function() {
                    if ($(this).val() === magasin_source) {
                        $(this).hide();
                    }
                });

                // Si le magasin de destination est sélectionné, masquer le magasin source
                if (magasin_destination) {
                    $('#produit').prop('disabled', false);
                    $('#magasin_source').prop('disabled', false);
                    $('#magasin_source option[value="' + magasin_destination + '"]').hide();
                } else {
                    $('#magasin_source').prop('disabled', true);
                    $('#produit').prop('disabled', true);
                }

                // Si le magasin source est sélectionné, masquer le magasin de destination
                if (magasin_source) {
                    $('#magasin_destination').prop('disabled', false);
                    $('#produit').prop('disabled', false);
                    $('#magasin_destination option[value="' + magasin_source + '"]').hide();
                } else {
                    $('#magasin_destination').prop('disabled', true);
                    $('#produit').prop('disabled', true);
                }
            }

            // Surveiller les événements de changement dans les champs magasin source et magasin destination
            $('select[name="magasin_destination"], select[name="magasin_source"]').on('change', function() {
                desabledButtonSelectMagasin();
            });

            // Appeler la fonction une fois que le document est prêt pour initialiser l'état des sélections
            desabledButtonSelectMagasin();
        });
        $(document).ready(function() {
            $('#magasin_source').change(function() {
                var selectedMagasinId = $(this).val();
                var produitSelect = $('#produit');
                var stockProduits =
                    {!! json_encode($stock_produits) !!}; // Assurez-vous que $stock_produits est correctement formaté en tant que tableau JSON dans votre contrôleur Laravel.
                // console.log('id magasin', selectedMagasinId)
                // console.log('liststockPro', stockProduits)

                // Filtrer les produits en fonction de l'ID du magasin sélectionné
                var filteredProduits = $.grep(stockProduits, function(produit) {
                    return produit.Id_Magasin == selectedMagasinId;
                });
                // console.log('filteredProduits', filteredProduits)

                // Effacer les options actuelles du sélecteur de produit
                produitSelect.empty().append('<option value="">Sélectionnez un produit</option>');

                // Ajouter les options filtrées au sélecteur de produit
                $.each(filteredProduits, function(index, produit) {
                    produitSelect.append('<option value="' + produit.id + ' ' + produit.Reference +
                        ' ' + produit.Id_Magasin +
                        '">' + produit.Designation + ' ==> ' + produit.NomMagasin + '</option>');
                });
            });
        });

        $(document).ready(function() {
            $('#myForm').keypress(function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Empêche l'action par défaut du formulaire
                }
            });
        });

        // $(document).ready(function() {
        //     // Fonction pour activer/désactiver les sélections de magasin en fonction l'une de l'autre
        //     function desabledButtonSelectMagasin() {
        //         var magasin_destination = $('select[name="magasin_destination"]').val();
        //         var magasin_source = $('select[name="magasin_source"]').val();
        //         var magasin_destination_select = $('select[name="magasin_destination"]');
        //         var magasin_source_select = $('select[name="magasin_source"]');
        //         var magasin_destination_options = magasin_destination_select.find('option');
        //         var magasin_source_options = magasin_source_select.find('option');

        //         // Filtrer les options du magasin source pour exclure le magasin destination sélectionné
        //         magasin_source_options.each(function() {
        //             if ($(this).val() === magasin_destination) {
        //                 $(this).prop('disabled', true);
        //             } else {
        //                 $(this).prop('disabled', false);
        //             }
        //         });
        //         // Filtrer les options du magasin de destination pour exclure le magasin source sélectionné
        //         magasin_destination_options.each(function() {
        //             if ($(this).val() === magasin_source) {
        //                 $(this).prop('disabled', true);
        //             } else {
        //                 $(this).prop('disabled', false);
        //             }
        //         });
        //         // Si le magasin de destination est sélectionné, activer le magasin source
        //         if (magasin_destination) {
        //             $('#produit').prop('disabled', false);
        //             $('#magasin_source').prop('disabled', false);
        //         } else {
        //             $('#magasin_source').prop('disabled', true);
        //             $('#produit').prop('disabled', true);
        //         }
        //         // Si le magasin source est sélectionné, activer le magasin de destination
        //         if (magasin_source) {
        //             $('#magasin_destination').prop('disabled', false);
        //             $('#produit').prop('disabled', false);
        //         } else {
        //             $('#magasin_destination').prop('disabled', true);
        //             $('#produit').prop('disabled', true);
        //         }
        //     }
        //     // Surveiller les événements de changement dans les champs magasin source et magasin destination
        //     $('select[name="magasin_destination"], select[name="magasin_source"]').on('change', function() {
        //         desabledButtonSelectMagasin();
        //     });
        //     // Appeler la fonction une fois que le document est prêt pour initialiser l'état des sélections
        //     desabledButtonSelectMagasin();
        // });

// js fin nouveau transfert