// js liste inventaire

$(document).ready(function() {
    $(".clickable-row").click(function() {
        // Supprimer la classe 'selected' de toutes les lignes
        $('.clickable-row').removeClass('selected');

        // Ajouter la classe 'selected' à la ligne cliquée
        $(this).addClass('selected');

        // Récupérer la valeur du statut dans la 5e colonne (index 4)
        var statut = $(this).find('td:eq(4)').text().trim();

        // Désactiver le bouton et les inputs en fonction du statut
        if (statut.toUpperCase() === "BOUCLER") {
            $('#validerButton').prop('disabled', true); // Désactiver le bouton
            alert("Inventaire bouclé, vous ne pouvez pas valider.");
            $('#message').show(); // Afficher le message

            // Désactiver et griser les inputs de 'quantite_comptee' de la ligne cliquée
            $(this).find('input[name^="inputs["][name$="[quantite_comptee]"]').prop('disabled', true).addClass(
                'disabled-input');
        } else {
            $('#validerButton').prop('disabled', false); // Activer le bouton
            $('#message').hide(); // Cacher le message

            // Activer les inputs de 'quantite_comptee' de la ligne cliquée
            $(this).find('input[name$="[quantite_comptee]"]').prop('disabled', false).removeClass(
                'disabled-input');
        }
    });
});

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

// Affichage des données dans la console
$(document).ready(function() {
    // Lorsque la ligne est cliquée
    $('.clickable-row').click(function() {
        // Trouver la case à cocher à l'intérieur de la ligne et l'activer
        $(this).find('input[type="radio"]').prop('checked', true);
    });
});

$(document).ready(function() {
    $(".clickable-row").click(function() {
        $('.clickable-row').removeClass('selected');
        $(this).addClass('selected');
        var url = $(this).data("url");

        $.get(url, function(data) {
            var tableContent1 = $(data).find('.responsive-1').html();
            var tableContent2 = $(data).find('.responsive-2').html();
            $(".responsive-1").html(tableContent1);
            $(".responsive-2").html(tableContent2);

            $('#searchInput').empty();
            $('#searchInputCategorie').empty();
            $('#selectProduit').empty();

            var categoriesSet = new Set();
            var produitsMap = {};

            $('#selectProduit').append('<option value="Tous">Tous</option>');
            $('#searchInputCategorie').append('<option value="Toutes">Toutes</option>');
            $('#searchInputCategorie').append(
                '<option value="">Sélectionnez une catégorie</option>');
            $('#searchInput').append('<option value="">Sélectionnez un magasin</option>');

            $(".responsive-2 tbody tr").each(function() {
                var categorieProduit = $(this).find(
                    'input[name^="inputs["][name$="[categorie_produit]"]').val();
                var produit = $(this).find(
                    'input[name^="inputs["][name$="[produit]"]').val();
                var designation = $(this).find(
                        'input[name^="inputs["][name$="[designation]"]')
                    .val(); // Récupère la désignation

                if (categorieProduit && !categoriesSet.has(categorieProduit)) {
                    categoriesSet.add(categorieProduit);
                    $('#searchInputCategorie').append('<option value="' +
                        categorieProduit + '">' + categorieProduit + '</option>'
                    );
                }

                if (!produitsMap[categorieProduit]) {
                    produitsMap[categorieProduit] = [];
                }
                produitsMap[categorieProduit].push({
                    produit: produit,
                    designation: designation
                }); // Associe produit et désignation
            });

            // Ajouter tous les produits dans le menu déroulant au début
            Object.values(produitsMap).forEach(function(produits) {
                produits.forEach(function(item) {
                    $('#selectProduit').append('<option value="' + item
                        .produit + '">' + item.produit + ' (' + item
                        .designation + ')</option>');
                });
            });

            $("#magasintable tbody tr").each(function() {
                var magasinText = $(this).find('.inventaire-magasin').text();
                var nomMagasin = magasinText.split('|')[0].trim();
                $('#searchInput').append('<option value="' + nomMagasin + '">' +
                    nomMagasin + '</option>');
            });

            $('#searchInputCategorie').on('change', function() {
                var selectedCategory = $(this).val();
                $('#selectProduit').empty();
                $('#selectProduit').append('<option value="Tous">Tous</option>');

                if (selectedCategory === "" || selectedCategory === "Toutes") {
                    Object.values(produitsMap).forEach(function(produits) {
                        produits.forEach(function(item) {
                            $('#selectProduit').append(
                                '<option value="' + item
                                .produit + '">' + item.produit +
                                ' (' + item.designation +
                                ')</option>');
                        });
                    });
                } else {
                    produitsMap[selectedCategory].forEach(function(item) {
                        $('#selectProduit').append('<option value="' + item
                            .produit + '">' + item.produit + ' (' + item
                            .designation + ')</option>');
                    });
                }
            });

            $('.responsive-2').on('change',
                'input[name^="inputs["][name$="[quantite_comptee]"]',
                function() {
                    var quantiteComptee = $(this).val();
                    var quantiteInitiale = $(this).closest('tr').find(
                        'input[name^="inputs["][name$="[quantite_initiale]"]').val();
                    var ecart = quantiteInitiale - quantiteComptee;

                    $(this).closest('tr').find(
                        'input[name^="inputs["][name$="[ecart]"]').val(ecart);
                    $(this).closest('tr').find(
                        'input[name^="inputs["][name$="[justifiee]"]').val(
                        quantiteComptee);
                });
        });
    });
});


$(document).ready(function() {
    $('#searchInputCategorie').on('change', function() {
        var searchText = $(this).val().toLowerCase(); // Récupère le texte sélectionné
        var $tableRows = $('#produitTable tbody tr'); // Sélectionne toutes les lignes du tableau
        var $noResultsMessage = $('#noResultsMessage'); // Message à afficher si aucun résultat

        var hasResults = false; // Variable pour vérifier si des résultats existent

        // Si l'option "Tous" est sélectionnée ou si aucun produit n'est spécifiquement choisi
        if (searchText === 'toutes' || searchText === '') {
            $tableRows.show(); // Affiche toutes les lignes du tableau
            hasResults = true;
        } else {
            // Parcourt toutes les lignes du tableau pour filtrer en fonction du texte de recherche
            $tableRows.each(function() {
                var produitValue = $(this).find(
                        'input[name^="inputs["][name$="[categorie_produit]"]')
                    .val().toLowerCase(); // Récupère la valeur du produit dans la cellule

                // Vérifie si le produit correspond à la recherche
                if (produitValue.includes(searchText)) {
                    $(this).show(); // Affiche la ligne si elle correspond
                    hasResults = true;
                } else {
                    $(this).hide(); // Masque la ligne si elle ne correspond pas
                }
            });
        }

        // Affiche ou masque le message "aucun résultat" en fonction des résultats de la recherche
        if (hasResults) {
            $noResultsMessage.hide();
        } else {
            $noResultsMessage.show();
        }
    });
});


// $(document).ready(function() {
//     // Sur le changement de la catégorie de produit
//     $('#searchInputCategorie').on('change', function() {
//         var selectedCategorie = $(this).val(); // Récupère la catégorie sélectionnée

//         // Faire une requête AJAX vers le backend pour récupérer les produits filtrés
//         $.ajax({
//             url: '/produits-par-categorie/' + selectedCategorie,
//             type: 'GET',
//             dataType: 'json',
//             success: function(data) {
//                 var tableBody = $('#produitTable tbody');
//                 tableBody.empty(); // Vide le tableau avant d'ajouter de nouvelles données

//                 if (data.length > 0) {
//                     $.each(data, function(index, produit) {
//                         // Ajouter chaque produit dans une nouvelle ligne de tableau
//                         var newRow = `
//                             <tr>
//                                 <td>${produit.id}</td>
//                                 <td>${produit.nom}</td>
//                                 <td>${produit.categorie}</td>
//                                 <td>${produit.stock}</td>
//                                 <!-- Autres colonnes si nécessaire -->
//                             </tr>`;
//                         tableBody.append(newRow);
//                     });
//                 } else {
//                     // Si aucun produit n'est trouvé, afficher un message
//                     var noResultsMessage = `<tr><td colspan="4">Aucun produit trouvé.</td></tr>`;
//                     tableBody.append(noResultsMessage);
//                 }
//             },
//             error: function(xhr, status, error) {
//                 console.error('Erreur lors de la récupération des produits:', error);
//             }
//         });
//     });
// });



$(document).ready(function() {
    $('#selectProduit').on('change', function() {
        var searchText = $(this).val().toLowerCase(); // Récupère le texte sélectionné
        var $tableRows = $('#produitTable tbody tr'); // Sélectionne toutes les lignes du tableau
        var $noResultsMessage = $('#noResultsMessage'); // Message à afficher si aucun résultat

        var hasResults = false; // Variable pour vérifier si des résultats existent

        // Si l'option "Tous" est sélectionnée ou si aucun produit n'est spécifiquement choisi
        if (searchText === 'tous' || searchText === '') {
            $tableRows.show(); // Affiche toutes les lignes du tableau
            hasResults = true;
        } else {
            // Parcourt toutes les lignes du tableau pour filtrer en fonction du texte de recherche
            $tableRows.each(function() {
                var produitValue = $(this).find('input[name^="inputs["][name$="[produit]"]')
                    .val().toLowerCase(); // Récupère la valeur du produit dans la cellule

                // Vérifie si le produit correspond à la recherche
                if (produitValue.includes(searchText)) {
                    $(this).show(); // Affiche la ligne si elle correspond
                    hasResults = true;
                } else {
                    $(this).hide(); // Masque la ligne si elle ne correspond pas
                }
            });
        }

        // Affiche ou masque le message "aucun résultat" en fonction des résultats de la recherche
        if (hasResults) {
            $noResultsMessage.hide();
        } else {
            $noResultsMessage.show();
        }
    });
});

$(document).ready(function() {
    $('#searchInput').on('change', function() {
        var searchText = $(this).val().toLowerCase(); // Récupère le texte sélectionné
        var $tableRows = $('#produitTable tbody tr'); // Sélectionne toutes les lignes du tableau
        var $noResultsMessage = $('#noResultsMessage'); // Message à afficher si aucun résultat

        var hasResults = false; // Variable pour vérifier si des résultats existent

        // Si l'option "Tous" est sélectionnée ou si aucun produit n'est spécifiquement choisi
        if (searchText === 'tous' || searchText === '') {
            $tableRows.show(); // Affiche toutes les lignes du tableau
            hasResults = true;
        } else {
            // Parcourt toutes les lignes du tableau pour filtrer en fonction du texte de recherche
            $tableRows.each(function() {
                var produitValue = $(this).find('input[name^="inputs["][name$="[magasin]"]')
                    .val().toLowerCase(); // Récupère la valeur du produit dans la cellule

                // Vérifie si le produit correspond à la recherche
                if (produitValue.includes(searchText)) {
                    $(this).show(); // Affiche la ligne si elle correspond
                    hasResults = true;
                } else {
                    $(this).hide(); // Masque la ligne si elle ne correspond pas
                }
            });
        }

        // Affiche ou masque le message "aucun résultat" en fonction des résultats de la recherche
        if (hasResults) {
            $noResultsMessage.hide();
        } else {
            $noResultsMessage.show();
        }
    });
});

$(document).ready(function() {
    $('#modifierBtn').click(function() {
        var selectedId = $('input[name="flexRadioDefault"]:checked').val();
        // console.log(selectedId);
        if (selectedId !== 'on') {
            $.ajax({
                url: "{{ route('modifier_statut_inventaire_produit') }}",
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
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            alert("Veuillez sélectionner une ligne à modifier.");
        }
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
    $(".clickable-row").click(function() {
        var url = $(this).data("url");
        $.get(url, function(data) {
            var tableContent = $(data).find('.responsive-2').html();
            $(".responsive-2").html(tableContent);
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
    var detailTbody_green = document.querySelector('.tableInfo-green tbody');
    detailTbody.innerHTML = `
        <tr>
            <td colspan="1" class="text-center">Aucune donnée (Selectionner une facture)</td>
        </tr>
    `;
    detailTbody_green.innerHTML = `
        <tr>
            <td colspan="10" class="text-center">Aucune donnée (Selectionner une facture)</td>
        </tr>
    `;
}

$(document).ready(function() {
    applyTableEvents();
});


// js fin liste inventaire