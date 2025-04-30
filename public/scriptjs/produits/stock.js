        $(document).ready(function() {
            // Gérer le changement de catégorie
            $('#searchInput2').on('change', function() {
                var selectedCategoryId = $(this).val();

                // Vider le menu déroulant des produits avant de l'actualiser
                $('#searchInput3').empty();
                $('#searchInput3').append('<option value="">Sélectionnez un produit</option>');

                if (selectedCategoryId) {
                    $.ajax({
                        url: '/get-products-by-category/' +
                            selectedCategoryId, // URL à adapter selon tes routes
                        type: 'GET',
                        success: function(response) {
                            // Ajouter les produits retournés dans le menu déroulant
                            if (response.length > 0) {
                                response.forEach(function(product) {
                                    $('#searchInput3').append('<option value="' +
                                        product.id + '">' + product.Designation +
                                        '</option>');
                                });
                            } else {
                                $('#searchInput3').append(
                                    '<option value="Tous">Aucun produit disponible</option>'
                                );
                            }
                        },
                        error: function() {
                            alert('Erreur lors du chargement des produits.');
                        }
                    });
                } else {
                    $('#searchInput3').append('<option value="Tous">Tous</option>');
                }
            });
        });



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
            $(".clickable-row1").click(function() {
                var url = $(this).data("url");
                var id = $(this).find("input[name='id']").val(); // Récupère l'id caché de la ligne

                // Récupère le token CSRF nécessaire pour les requêtes POST
                var token = $('meta[name="csrf-token"]').attr('content');

                $.post(url, {
                        _token: token, // Ajoute le token CSRF
                        id: id // Envoie l'id du produit
                    })
                    .done(function(data) {
                        var tableContent = $(data).find('.resp-1').html(); // Sélectionne le contenu
                        $(".resp-1").html(tableContent); // Injecte le contenu dans le tableau cible
                    })
                    .fail(function(xhr, status, error) {
                        console.error("Erreur lors de l'import : " + error);
                    });
            });
        });

        // $(document).ready(function() {
        //     $(".clickable-row1").click(function() {
        //         var url = $(this).data("url");
        //         $.post(url, function(data) {
        //             var tableContent = $(data).find('.resp-1')
        //                 .html(); // Sélectionnez le contenu du premier tableau
        //             $(".resp-1").html(
        //                 tableContent); // Injectez le contenu dans le deuxième tableau
        //         });
        //     });
        // });


        $(document).ready(function() {
            $('.clickable-row1').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row1').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });


        $(document).ready(function() {
            const $valoriserCheckbox = $('#valoriser_stock');
            const $prixUnitaireCols = $('.prix-unitaire-col');
            const $montantCols = $('.montant-col');

            $valoriserCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    // Afficher les colonnes du prix unitaire et du montant
                    $prixUnitaireCols.show();
                    $montantCols.show();
                } else {
                    // Masquer les colonnes du prix unitaire et du montant
                    $prixUnitaireCols.hide();
                    $montantCols.hide();
                }
            });

            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            // Masquer les colonnes par défaut
            $prixUnitaireCols.hide();
            $montantCols.hide();
        });

        $(document).ready(function() {
            $('#exportExcel').click(function() {
                $('#exporterExcelModal').modal('hide')
            });
        });

        $(document).ready(function() {
            const $Convertir_en_casierCheckbox = $('#Convertir_en_casier');
            const $casier_col = $('.casier_col');

            $Convertir_en_casierCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    // Afficher les colonnes du prix unitaire et du montant
                    $casier_col.show();
                } else {
                    // Masquer les colonnes du prix unitaire et du montant
                    $casier_col.hide();
                }
            });

            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            // Masquer les colonnes par défaut
            $casier_col.hide();
        });


        $(document).ready(function() {
            $('#formatImportation').click(function() {
                $('#exporterExcelModal2').modal('hide')
            });
        });

        $(document).ready(function() {
            $('.exportExcel').click(function() {
                $('#imprimerPDFModal').modal('hide')
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.post(url, function(data) {
                    var tableContent = $(data).find('.responsive-1')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-1").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-2')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-2").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
        });

        $(document).ready(function() {
            $('#searchInput').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput1').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput2').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput3').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#appliquerHistorique').click(function() {
                // Ajouter la classe "show active" à l'onglet "Liste"
                $('#nav-home').addClass('show active');
                // Retirer la classe "show active" des autres onglets
                $('#nav-profile').removeClass('show active');
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec la classe "tableInfo"
                var $table = $('.tableInfo');

                // console.log(' $table ', $table)
                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                // console.log('$rows ', $rows)


                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-historique');

                    // console.log('$cells ', $cells)

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });


                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }


                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-stock-historique',
                    method: 'POST',
                    target: '_blank'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#printButton').click(function() {
                logTableContent();
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec la classe "tableInfo"
                var $table = $('.tableInfo');

                // console.log(' $table ', $table)
                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                // console.log('$rows ', $rows)


                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-historique');

                    // console.log('$cells ', $cells)

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });


                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }


                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'exporter-stock-historique',
                    method: 'POST',
                    target: '_blank'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#printButtonExcel').click(function() {
                logTableContent();
            });
        });