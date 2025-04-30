        $(document).ready(function() {
            // Supprimer une ligne du tableau
            $(document).on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
            });
        });

        $(document).ready(function() {
            $('#produit').on('change', function() {
                var searchText = $(this).val().toLowerCase();
                var $tableRows = $('#produitTable tbody tr');
                var $noResultsMessage = $('#noResultsMessage');

                var hasResults = false;
                $tableRows.each(function() {
                    var cellText = $(this).find('.entree-produitb').text()
                        .toLowerCase(); // Utilisation de la classe ou de l'attribut personnalisé
                    if (cellText.includes(searchText)) {
                        $(this).show();
                        hasResults = true;
                    } else {
                        $(this).hide();
                    }
                });

                // Afficher ou masquer le message si aucun résultat n'est trouvé
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            // Cacher l'élément input par défaut
            $('#designation').hide();
            $('#prix_actuel').hide();
            $('#nouveau_prix').hide();

            // $('#add').prop('disabled', false);
            gererVisibiliteBoutonAppliquer()
        });

        $(document).ready(function() {

            // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
            function updateDesignation() {
                var stockProduits = {!! json_encode($produits) !!};
                var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};
                // console.log('stocks', stockProduits, historique_prix_revient)

                $('.produit-select').each(function() {
                    var produit_id = $(this).val();
                    // console.log(produit_id)
                    var row = $(this).closest('tr');
                    var designationInput = row.find('.designation-input');
                    var prixActuelInput = row.find('.prix-actuel-input');
                    var nouveauPrixInput = row.find('.nouveau-prix-input');


                    // var magasinInput = row.find('.magasin-input');
                    // var quantiteDispoInput = row.find('.quantite-dispo-input');

                    var produit = stockProduits.find(function(element) {
                        return element.Reference === produit_id
                        // console.log(element)
                    });

                    var v = historique_prix_revient.find(function(element) {
                        return element.Reference === produit_id
                        // console.log('element.Reference', element.Reference)
                        // console.log('id pro', produit_id)
                    });

                    // console.log(produit)
                    // console.log('V', v.Designation, v.Prix_Revient)
                    // Vérifier si la référence sélectionnée existe dans les stocks
                    if (produit) {
                        $('#designation').val(produit.Designation);
                        var prix_a = '0.00'
                        var n_prix = '0.00'
                        $('#prix_actuel').val(prix_a);
                        $('#nouveau_prix').val(n_prix);

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

        function gererVisibiliteBoutonAppliquer() {
            var nombreLignes = $('#table2 tbody tr').length;
            if (nombreLignes > 0) {
                $('#bouton-valider').show(); // Afficher le bouton si des lignes existent
            } else {
                $('#bouton-valider').hide(); // Cacher le bouton sinon
            }
        }

        $(document).ready(function() {

            var rowCountAdd = 0;

            function ajouterLigne() {
                $('#table2 tbody').empty();
                var produits = @json($produits);
                var categories = @json($categorie_client);
                var agences = @json($agence);

                produits.forEach(function(produit) {
                    categories.forEach(function(categorie) {
                        agences.forEach(function(agence) {
                            var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};
                            var v = historique_prix_revient.find(function(element) {
                                return element.Reference === produit.Reference &&
                                    element.NomAgence === agence.NomAgence &&
                                    element.Libelle === categorie.Libelle
                            });

                            // console.log('V', v)
                            if (v) {
                                // Remplir les champs avec les données du produit
                                // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                                //     v.Libelle);
                                // console.log(designationInput.val(produit.Designation))
                                $('#designation').val(v.Designation);
                                $('#prix_actuel').val(v.Prix_Revient);
                                $('#nouveau_prix').val();
                                var nouveau_prix = v.Prix_Revient;
                                var prix_actuel = v.Prix_Revient;
                                // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                                // quantiteDispoInput.val(produit.Qte_stockee);
                            } else {
                                // console.log('je suis bien dans le else du click');
                                var nouveau_prix = '0.00'
                                var prix_actuel = '0.00'
                                // $('#prix_actuel').val(prix_a);
                                // $('#nouveau_prix').val(n_prix);
                            }

                            // Vérifier si une ligne avec les mêmes informations existe déjà
                            if (ligneExistante(produit.Reference, produit.Designation,
                                    agence.NomAgence, categorie.Libelle)) {
                                alert('Une ligne avec les mêmes informations existe déjà.');
                                return false;
                            }

                            // Ajouter les valeurs à la table en tant que nouvelle ligne
                            var rowCount = $('#table2 tbody tr').length + 1;
                            rowCountAdd++
                            var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit.Reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${produit.Designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence.id+'--'+agence.NomAgence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie.id+'-'+categorie.Libelle}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                            $('#table2 tbody').append(newRow);
                            gererVisibiliteBoutonAppliquer()

                        });
                    });
                });
            }

            function ajouterLigneProduit(reference, designation) {
                $('#table2 tbody').empty();
                // var produits = @json($produits);
                var categories = @json($categorie_client);
                var agences = @json($agence);

                // produits.forEach(function(produit) {
                categories.forEach(function(categorie) {
                    agences.forEach(function(agence) {

                        var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};
                        var v = historique_prix_revient.find(function(element) {
                            return element.Reference === reference &&
                                element.NomAgence === agence.NomAgence &&
                                element.Libelle === categorie.Libelle
                            // console.log('element.Reference', element.Reference)
                            // console.log('id pro', produit_id)
                        });

                        // console.log('V', v)

                        if (v) {
                            // Remplir les champs avec les données du produit
                            // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                            //     v.Libelle);
                            // console.log(designationInput.val(produit.Designation))
                            $('#designation').val(v.Designation);
                            $('#prix_actuel').val(v.Prix_Revient);
                            $('#nouveau_prix').val();
                            var nouveau_prix = v.Prix_Revient;
                            var prix_actuel = v.Prix_Revient;
                            // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                            // quantiteDispoInput.val(produit.Qte_stockee);
                        } else {
                            // console.log('je suis bien dans le else du click');
                            var nouveau_prix = '0.00'
                            var prix_actuel = '0.00'
                        }

                        // Vérifier si une ligne avec les mêmes informations existe déjà
                        if (ligneExistante(reference, produit.Designation,
                                agence.NomAgence, categorie.Libelle)) {
                            alert('Une ligne avec les mêmes informations existe déjà.');
                            return false;
                        }

                        // Ajouter les valeurs à la table en tant que nouvelle ligne
                        var rowCount = $('#table2 tbody tr').length + 1;
                        rowCountAdd++
                        var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence.id+'-'+agence.NomAgence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie.id+'-'+categorie.Libelle}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                        $('#table2 tbody').append(newRow);
                        gererVisibiliteBoutonAppliquer()
                        // var newRow = `
                    //     <tr>
                    //         <td>${produit.Reference}</td>
                    //         <td>${categorie.Libelle}</td>
                    //         <td>${agence.NomAgence}</td>
                    //         <td></td>
                    //         <td></td>
                    //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                    //     </tr>`;
                        // $('#table2 tbody').append(newRow);
                    });
                });
                // });
            }

            function ajouterLigneCategorieClient(categorie_client) {
                $('#table2 tbody').empty();
                var produits = @json($produits);
                // var categories = @json($categorie_client);
                var agences = @json($agence);

                produits.forEach(function(produit) {
                    // categories.forEach(function(categorie) {
                    agences.forEach(function(agence) {
                        var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};

                        var v = historique_prix_revient.find(function(element) {
                            return element.Reference === produit.Reference &&
                                element.NomAgence === agence.NomAgence &&
                                element.Libelle === categorie_client
                            // console.log('element.Reference', element.Reference)
                            // console.log('id pro', produit_id)
                        });

                        // console.log('V', v)

                        if (v) {
                            // Remplir les champs avec les données du produit
                            // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                            //     v.Libelle);
                            // console.log(designationInput.val(produit.Designation))
                            $('#designation').val(v.Designation);
                            $('#prix_actuel').val(v.Prix_Revient);
                            $('#nouveau_prix').val();
                            var nouveau_prix = v.Prix_Revient;
                            var prix_actuel = v.Prix_Revient;
                            // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                            // quantiteDispoInput.val(produit.Qte_stockee);
                        } else {
                            // console.log('je suis bien dans le else du click');
                            var nouveau_prix = '0.00'
                            var prix_actuel = '0.00'
                            // $('#prix_actuel').val(prix_a);
                            // $('#nouveau_prix').val(n_prix);
                        }

                        // console.log("Produit: ", reference);
                        // console.log("Catégorie client: ", designation);
                        // console.log("Agence: ", agence);


                        // Vérifier si une ligne avec les mêmes informations existe déjà
                        if (ligneExistante(produit.Reference, produit.Designation,
                                agence.NomAgence, categorie_client)) {
                            alert('Une ligne avec les mêmes informations existe déjà.');
                            return false;
                        }

                        // Ajouter les valeurs à la table en tant que nouvelle ligne
                        var rowCount = $('#table2 tbody tr').length + 1;
                        rowCountAdd++
                        var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit.Reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${produit.Designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence.id+'-'+agence.NomAgence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie_client}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                        $('#table2 tbody').append(newRow);
                        gererVisibiliteBoutonAppliquer()
                        // var newRow = `
                    //     <tr>
                    //         <td>${produit.Reference}</td>
                    //         <td>${categorie.Libelle}</td>
                    //         <td>${agence.NomAgence}</td>
                    //         <td></td>
                    //         <td></td>
                    //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                    //     </tr>`;
                        // $('#table2 tbody').append(newRow);
                    });
                    // });
                });
            }


            function ajouterLigneProduitCategorie(reference, designation, categorie_client) {
                $('#table2 tbody').empty();

                // var produits = @json($produits);
                // var categories = @json($categorie_client);
                var agences = @json($agence);
                // console.log('ah me voila',reference, designation, categorie_client)
                //produits.forEach(function(produit) {
                //categories.forEach(function(categorie) {
                var tableauCategorieClient = categorie_client.split('-')
                var _categorie_client = tableauCategorieClient[1]
                agences.forEach(function(agence) {
                    var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};

                    //  console.log(historique_prix_revient)
                    var v = historique_prix_revient.find(function(element) {
                        return element.Reference === reference &&
                            element.NomAgence === agence.NomAgence &&
                            element.Libelle === _categorie_client
                        // console.log('element.Reference', element.Reference)
                        // console.log('id pro', produit_id)
                    });

                    // console.log('V', v)

                    if (v) {
                        // Remplir les champs avec les données du produit
                        // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                        //     v.Libelle);
                        // console.log(designationInput.val(produit.Designation))
                        $('#designation').val(v.Designation);
                        $('#prix_actuel').val(v.Prix_Revient);
                        $('#nouveau_prix').val();
                        var nouveau_prix = v.Prix_Revient;
                        var prix_actuel = v.Prix_Revient;
                        // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                        // quantiteDispoInput.val(produit.Qte_stockee);
                    } else {
                        // console.log('je suis bien dans le else du click');
                        var nouveau_prix = '0.00'
                        var prix_actuel = '0.00'
                        // $('#prix_actuel').val(prix_a);
                        // $('#nouveau_prix').val(n_prix);
                    }

                    // console.log("Produit: ", reference);
                    // console.log("Catégorie client: ", designation);
                    // console.log("Agence: ", agence);


                    // Vérifier si une ligne avec les mêmes informations existe déjà
                    if (ligneExistante(reference, designation,
                            agence.NomAgence, categorie_client)) {
                        alert('Une ligne avec les mêmes informations existe déjà.');
                        return false;
                    }

                    // Ajouter les valeurs à la table en tant que nouvelle ligne
                    var rowCount = $('#table2 tbody tr').length + 1;
                    rowCountAdd++
                    var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence.id+'-'+agence.NomAgence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie_client}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                    $('#table2 tbody').append(newRow);
                    gererVisibiliteBoutonAppliquer()
                    // var newRow = `
                //     <tr>
                //         <td>${produit.Reference}</td>
                //         <td>${categorie.Libelle}</td>
                //         <td>${agence.NomAgence}</td>
                //         <td></td>
                //         <td></td>
                //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                //     </tr>`;
                    // $('#table2 tbody').append(newRow);
                });
                //      });
                // });
            }


            function ajouterLigneCategorieAgence(categorie_client, agence) {
                $('#table2 tbody').empty();

                var produits = @json($produits);
                // var categories = @json($categorie_client);
                // var agences = @json($agence);
                // console.log('ah me voila', categorie_client, agence)
                //produits.forEach(function(produit) {
                //categories.forEach(function(categorie) {
                var tableauAgence = agence.split('-')
                var agenceclient = tableauAgence[1]
                var tableauCategorieClient = categorie_client.split('-')
                categorie_client = tableauCategorieClient[1]
                // console.log(agenceclient)
                produits.forEach(function(produit) {
                    var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};

                    //  console.log(historique_prix_revient)
                    var v = historique_prix_revient.find(function(element) {
                        return element.Reference === produit.Reference &&
                            element.NomAgence === agenceclient &&
                            element.Libelle === categorie_client
                        // console.log('element.Reference', element.Reference)
                        // console.log('id pro', produit_id)
                    });

                    // console.log('V', v)

                    if (v) {
                        // Remplir les champs avec les données du produit
                        // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                        //     v.Libelle);
                        // console.log(designationInput.val(produit.Designation))
                        $('#designation').val(v.Designation);
                        $('#prix_actuel').val(v.Prix_Revient);
                        $('#nouveau_prix').val();
                        var nouveau_prix = v.Prix_Revient;
                        var prix_actuel = v.Prix_Revient;
                        // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                        // quantiteDispoInput.val(produit.Qte_stockee);
                    } else {
                        // console.log('je suis bien dans le else du click');
                        var nouveau_prix = '0.00'
                        var prix_actuel = '0.00'
                        // $('#prix_actuel').val(prix_a);
                        // $('#nouveau_prix').val(n_prix);
                    }

                    // console.log("Produit: ", reference);
                    // console.log("Catégorie client: ", designation);
                    // console.log("Agence: ", agence);


                    // Vérifier si une ligne avec les mêmes informations existe déjà
                    if (ligneExistante(produit.Reference, produit.Designation,
                            agence.NomAgence, categorie_client)) {
                        alert('Une ligne avec les mêmes informations existe déjà.');
                        return false;
                    }

                    // Ajouter les valeurs à la table en tant que nouvelle ligne
                    var rowCount = $('#table2 tbody tr').length + 1;
                    rowCountAdd++
                    var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit.Reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${produit.Designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie_client}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                    $('#table2 tbody').append(newRow);
                    gererVisibiliteBoutonAppliquer()
                    // var newRow = `
                //     <tr>
                //         <td>${produit.Reference}</td>
                //         <td>${categorie.Libelle}</td>
                //         <td>${agence.NomAgence}</td>
                //         <td></td>
                //         <td></td>
                //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                //     </tr>`;
                    // $('#table2 tbody').append(newRow);
                });
                //      });
                // });
            }

            function ajouterLigneProduitAgence(reference, designation, agence) {
                $('#table2 tbody').empty();

                // var produits = @json($produits);
                var categories = @json($categorie_client);
                // var agences = @json($agence);
                var tableauAgence = agence.split('-')
                var agenceclient = tableauAgence[1]

                // var tableauCategorieClient = categorie_client.split('-')
                // categorie_client = tableauCategorieClient[1]
                // console.log('ah me voila',agenceclient)
                //produits.forEach(function(produit) {
                categories.forEach(function(categorie) {

                    // agences.forEach(function(agence) {
                    var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};
                    // console.log(categorie)
                    //  console.log(historique_prix_revient)
                    var v = historique_prix_revient.find(function(element) {
                        return element.Reference === reference &&
                            element.NomAgence === agenceclient &&
                            element.Libelle === categorie.Libelle
                        // console.log('element.Reference', element.Libelle)
                        // console.log('id pro', produit_id)
                    });

                    // console.log('V', v)

                    if (v) {
                        // Remplir les champs avec les données du produit
                        // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                        //     v.Libelle);
                        // console.log(designationInput.val(produit.Designation))
                        $('#designation').val(v.Designation);
                        $('#prix_actuel').val(v.Prix_Revient);
                        $('#nouveau_prix').val();
                        var nouveau_prix = v.Prix_Revient;
                        var prix_actuel = v.Prix_Revient;
                        // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                        // quantiteDispoInput.val(produit.Qte_stockee);
                    } else {
                        // console.log('je suis bien dans le else du click');
                        var nouveau_prix = '0.00'
                        var prix_actuel = '0.00'
                        // $('#prix_actuel').val(prix_a);
                        // $('#nouveau_prix').val(n_prix);
                    }

                    // console.log("Produit: ", reference);
                    // console.log("Catégorie client: ", designation);
                    // console.log("Agence: ", agence);


                    // Vérifier si une ligne avec les mêmes informations existe déjà
                    if (ligneExistante(reference, designation,
                            agence.NomAgence, categorie_client)) {
                        alert('Une ligne avec les mêmes informations existe déjà.');
                        return false;
                    }

                    // Ajouter les valeurs à la table en tant que nouvelle ligne
                    var rowCount = $('#table2 tbody tr').length + 1;
                    rowCountAdd++
                    var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie.id+ '-' +categorie.Libelle}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                    $('#table2 tbody').append(newRow);
                    gererVisibiliteBoutonAppliquer()
                    // var newRow = `
                //     <tr>
                //         <td>${produit.Reference}</td>
                //         <td>${categorie.Libelle}</td>
                //         <td>${agence.NomAgence}</td>
                //         <td></td>
                //         <td></td>
                //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                //     </tr>`;
                    // $('#table2 tbody').append(newRow);
                    // });
                });
                // });
            }

            function ajouterLigneAgence(agence) {
                $('#table2 tbody').empty();
                var produits = @json($produits);
                var categories = @json($categorie_client);
                // var agences = @json($agence);

                produits.forEach(function(produit) {
                    categories.forEach(function(categorie) {
                        // agences.forEach(function(agence) {
                        var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};

                        var v = historique_prix_revient.find(function(element) {
                            return element.Reference === produit.Reference &&
                                element.NomAgence === agence &&
                                element.Libelle === categorie.Libelle
                            // console.log('element.Reference', element.Reference)
                            // console.log('id pro', produit_id)
                        });

                        // console.log('V', v)

                        if (v) {
                            // Remplir les champs avec les données du produit
                            // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                            //     v.Libelle);
                            // console.log(designationInput.val(produit.Designation))
                            $('#designation').val(v.Designation);
                            $('#prix_actuel').val(v.Prix_Revient);
                            $('#nouveau_prix').val();
                            var nouveau_prix = v.Prix_Revient;
                            var prix_actuel = v.Prix_Revient;
                            // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                            // quantiteDispoInput.val(produit.Qte_stockee);
                        } else {
                            // console.log('je suis bien dans le else du click');
                            var nouveau_prix = '0.00'
                            var prix_actuel = '0.00'
                            // $('#prix_actuel').val(prix_a);
                            // $('#nouveau_prix').val(n_prix);
                        }

                        // console.log("Produit: ", reference);
                        // console.log("Catégorie client: ", designation);
                        // console.log("Agence: ", agence);


                        // Vérifier si une ligne avec les mêmes informations existe déjà
                        if (ligneExistante(produit.Reference, produit.Designation,
                                agence, categorie.Libelle)) {
                            alert('Une ligne avec les mêmes informations existe déjà.');
                            return false;
                        }

                        // Ajouter les valeurs à la table en tant que nouvelle ligne
                        var rowCount = $('#table2 tbody tr').length + 1;
                        rowCountAdd++
                        var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit.Reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${produit.Designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${agence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${categorie.id+'-'+categorie.Libelle}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                        $('#table2 tbody').append(newRow);
                        gererVisibiliteBoutonAppliquer()

                        // var newRow = `
                    //     <tr>
                    //         <td>${produit.Reference}</td>
                    //         <td>${categorie.Libelle}</td>
                    //         <td>${agence.NomAgence}</td>
                    //         <td></td>
                    //         <td></td>
                    //         <td><button class="btn btn-sm btn-danger remove-row">Supprimer</button></td>
                    //     </tr>`;
                        // $('#table2 tbody').append(newRow);
                        // });
                    });
                });
            }
            // ajouterLigne()

            // Fonction pour vider les champs du formulaire
            function clearFormFields() {
                $('#produit').val('');
                $('#designation').val('');
                $('#categorie_client').val('');
                $('#agence').val('');
            }

            $(document).ready(function() {
                // Fonction pour vérifier si le tableau est vide et afficher/masquer le bouton "Appliquer"
                function toggleApplyButton() {
                    var tableRows = $('#table2 tbody tr');
                    if (tableRows.length === 0) {
                        $('#applyButton').hide(); // S'il n'y a pas de lignes, masquer le bouton "Appliquer"
                    } else {
                        $('#applyButton').show(); // S'il y a des lignes, afficher le bouton "Appliquer"
                    }
                }

                // Appeler la fonction au chargement de la page
                toggleApplyButton();
            });

            // Fonction pour vérifier si une ligne avec les mêmes informations existe déjà dans le tableau
            function ligneExistante(reference, designation, categorie_client, agence) {
                var existante = false;
                $('#table2 tbody tr').each(function() {
                    var produit = $(this).find('input[name^="inputs["][name$="][produit]"]').val();
                    var desig = $(this).find('input[name^="inputs["][name$="][designation]"]').val();
                    var cat_client = $(this).find('input[name^="inputs["][name$="][categorie_client]"]')
                        .val();
                    var ag = $(this).find('input[name^="inputs["][name$="][agence]"]').val();

                    if (produit === reference && desig === designation && cat_client === categorie_client &&
                        ag === agence) {
                        existante = true;
                        return false; // Sortir de la boucle each si une ligne correspondante est trouvée
                    }
                });
                return existante;
            }

            // Ajouter une nouvelle ligne au tableau lorsque le bouton "Afficher" est cliqué
            $('#add').click(function() {

                // Récupérer les valeurs sélectionnées
                var reference = $('#produit').val();
                var designation = $('#designation').val();
                var categorie_client = $('#categorie_client').val();
                var agence = $('#agence').val();
                var historique_prix_revient = {!! json_encode($historique_prix_revients) !!};

                // console.log(reference, designation, categorie_client)

                if (reference === '' && designation === '' && categorie_client === '' && agence === '') {
                    // alert('Veuillez remplir tous les champs.');
                    ajouterLigne()
                    return false;
                }

                if (reference !== '' && designation !== '' && categorie_client !== '' && agence !== '') {
                    // console.log(historique_prix_revient);
                    $('#table2 tbody').empty();

                    nomAgence = agence.split('-')
                    libelle = categorie_client.split('-')

                    // console.log(nomAgence, libelle)
                    // console.log('N', agence, reference, categorie_client)
                    var v = historique_prix_revient.find(function(element) {
                        return element.Reference === reference && element.NomAgence === nomAgence[
                            1] && element.Libelle === libelle[1]
                        // console.log('element.Reference', element.Reference)
                        // console.log('id pro', produit_id)
                    });

                    // console.log('V', v)

                    if (v) {
                        // Remplir les champs avec les données du produit
                        // console.log('je suis bien dans le click', v, v.Designation, v.Prix_Revient, v.NomAgence,
                        //     v.Libelle);
                        // console.log(designationInput.val(produit.Designation))
                        $('#designation').val(v.Designation);
                        $('#prix_actuel').val(v.Prix_Revient);
                        $('#nouveau_prix').val();
                        var nouveau_prix = v.Prix_Revient;
                        var prix_actuel = v.Prix_Revient;
                        // magasinInput.val(produit.Id_Magasin +'-'+produit.NomMagasin);
                        // quantiteDispoInput.val(produit.Qte_stockee);
                    } else {
                        // console.log('je suis bien dans le else du click');
                        var nouveau_prix = '0.00'
                        var prix_actuel = '0.00'
                        // $('#prix_actuel').val(prix_a);
                        // $('#nouveau_prix').val(n_prix);
                    }

                    // console.log("Produit: ", reference);
                    // console.log("Catégorie client: ", designation);
                    // console.log("Agence: ", agence);


                    // Vérifier si une ligne avec les mêmes informations existe déjà
                    if (ligneExistante(reference, designation, categorie_client, agence)) {
                        alert('Une ligne avec les mêmes informations existe déjà.');
                        return false;
                    }

                    // Ajouter les valeurs à la table en tant que nouvelle ligne
                    var rowCount = $('#table2 tbody tr').length + 1;
                    rowCountAdd++
                    var newRow = `
                <tr>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 categorie_client-input" value="${designation}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][categorie_client]" class="form-control border-0" value="${categorie_client}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" name="inputs[${rowCount}][agence]" class="form-control border-0" value="${agence}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][prix_actuel]" class="form-control border-0" id="prix_actuel" value="${prix_actuel}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" name="inputs[${rowCount}][nouveau_prix]" class="form-control border-0" id="prix_achat" value="${nouveau_prix}" >
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                    </td>
                </tr>`;
                    $('#table2 tbody').append(newRow);

                    // Vider les champs du formulaire après l'ajout de la ligne
                    clearFormFields();
                    gererVisibiliteBoutonAppliquer()
                    return false;
                }

                if (reference !== '' && designation !== '' && categorie_client !== '' && agence === '') {
                    // console.log(historique_prix_revient);
                    // console.log(reference,designation,categorie_client)
                    $('#table2 tbody').empty();

                    ajouterLigneProduitCategorie(reference, designation, categorie_client)
                    return false;
                }

                if (reference === '' && designation === '' && categorie_client !== '' && agence !== '') {
                    // console.log(historique_prix_revient);
                    // console.log(reference,designation,categorie_client)
                    $('#table2 tbody').empty();

                    ajouterLigneCategorieAgence(categorie_client, agence)
                    return false;
                }

                if (reference !== '' && designation !== '' && categorie_client === '' && agence !== '') {
                    // console.log(historique_prix_revient);
                    // console.log(reference,designation,categorie_client)
                    $('#table2 tbody').empty();

                    ajouterLigneProduitAgence(reference, designation, agence)
                    return false;
                }


                if (reference !== '') {
                    // return console.log('ici je suis bien la');
                    // alert('Veuillez remplir tous les champs.');
                    ajouterLigneProduit(reference, designation)
                    gererVisibiliteBoutonAppliquer()

                    return false;
                }

                if (categorie_client !== '') {
                    // alert('Veuillez remplir tous les champs.');
                    ajouterLigneCategorieClient(categorie_client)
                    gererVisibiliteBoutonAppliquer()
                    return false;
                }

                if (agence !== '') {
                    // alert('Veuillez remplir tous les champs.');
                    ajouterLigneAgence(agence)
                    gererVisibiliteBoutonAppliquer()
                    return false;
                }

            });

            // Supprimer une ligne du tableau
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                rowCountAdd--
                // if (rowCountAdd == 0) {
                //     $('#add').prop('disabled', true); // Cacher le bouton Valider s'il n'y a pas de lignes
                // }
                gererVisibiliteBoutonAppliquer()
            });
        });