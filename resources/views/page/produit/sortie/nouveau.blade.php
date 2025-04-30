@extends('layouts.master')

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Nouvelle sortie',
        'infos2' => 'Nouvelle sortie',
        'infos3' => 'Nouveau',
    ])
    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
            <a href="{{ route('page.sortie.sortie') }}" class="btn text-white" style="{{ background_color_1() }}">
                <i class="fa fa-reply" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark">
                Créer une sortie
            </h4>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-12">

            <div class="table-responsive mt-3">
                <div class="col-md-12">
                    <form id="formcreate" action="{{ route('store.sortie') }}" method="post" id="myForm">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">Observation & Type</legend>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="validationTextarea" class="form-label fw-bold">Type de sortie</label>
                                            <span class="translate-middle text-danger mt-2">*
                                            </span>
                                            <select name="type_sortie" type="text" required
                                            class="form-select js-single" id="type_sortie"
                                             aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-sm">
                                            <option value="">Sélectionnez un type de sortie</option>
                                            <option value="SORTIE">Sortie simple</option>
                                            <option value="CASSE">Casse</option>

                                        </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="validationTextarea" class="form-label fw-bold">Observation</label>
                                            <span class="translate-middle text-danger mt-2">*
                                            </span>
                                            <textarea class="form-control" id="validationDefault04" name="observation" id="" cols="2" rows="2"></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3" id="table">
                                    <legend class="float-none w-auto px-1">Produit</legend>
                                    <div class="row">
                                        <table class="table border-0">
                                            <div class="form-group col-md-4">
                                                <label class="form-label fw-bold" for="">Produit</label>
                                                {{-- <span class="input-group-text" id="produit">Produit</span> --}}
                                                <select name="" type="text"
                                                    class="form-select produit-select js-single" id="produit"
                                                    value="{{ old('produit') }}" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">Sélectionnez un produit</option>
                                                    @foreach ($stock_produits as $key => $value)
                                                        <option
                                                            value="{{ $value->id . '-' . $value->Reference }}|{{ $value->Id_Magasin . '|' . $value->NomMagasin }}">
                                                            {{ $value->Designation }}==>{{ $value->NomMagasin }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label fw-bold" for="">Désignation</label>
                                                {{-- <span class="input-group-text" id="designation">Désignation</span> --}}
                                                <input step="0.01" type="text" name=""
                                                    class="form-control designation-input" value="{{ old('designation') }}"
                                                    id="designation" placeholder="Désignation..." readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label fw-bold" for="">Magasin</label>
                                                {{-- <span class="input-group-text" id="magasin">Magasin</span> --}}
                                                <input type="text" name="" type="text"
                                                    value="{{ old('magasin') }}" id="magasin"
                                                    class="form-control magasin-input" placeholder="Magasin..." readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label fw-bold" for="">QuantitéDisponible</label>
                                                {{-- <span class="input-group-text" id="quantity">Qté</span> --}}
                                                <input type="text" name=""
                                                    class="form-control quantite-dispo-input" value="{{ old('quantity') }}"
                                                    id="quantity" readonly placeholder="Quantite disponible...">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-label fw-bold" for="">QuantitéSortie</label>
                                                {{-- <span class="input-group-text" id="quantity_out">Prix d'achat</span> --}}
                                                <input type="number" name="" class="form-control"
                                                    placeholder="Qté sortie..." value="{{ old('quantity_out') }}"
                                                    id="quantity_out">
                                            </div>
                                        </table>
                                    </div>
                                    <button type="button" id="add" name="add" class="btn text-white float-end"
                                        style="{{ background_color_1() }}"><span class="fw-bold">+</span>Ajouter</button>
                                </fieldset>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog g modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Souhaitez-vous vraiment faire cette sortie?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" id="confirmcreate"
                                            class="btn btn-primary">Continuer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">Ligne sortie</legend>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table2">
                                            <thead>
                                                <tr>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Référence </th>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Désignation</th>
                                                    <th style="{{ background_color_2() }}" scope="col">
                                                        Magasin</th>
                                                    <th style="{{ background_color_2() }}" scope="col">
                                                        Qté à sortie</th>
                                                    <th style="{{ background_color_2() }}" scope="col">

                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Lignes du deuxième tableau seront ajoutées ici -->
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">Total</legend>
                                    <!-- Ajoutez ensuite une section pour le deuxième tableau -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="" class="fw-bold">Nombre Total de stock à sortie</label>
                                            <input class="form-control border-0" type="text" id="prixAchatTotal"
                                                readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="fw-bold">Nombre de ligne à sortie</label>
                                            <input class="form-control border-0" type="text" id="nb_entree" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <!-- Button trigger modal -->
                        <div class="mb-5 d-flex">
                            <button type="button" class="btn text-white ms-auto float-end"
                                style="{{ background_color_1() }}" id="bouton-valider" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" class="btn btn-sm btn-primary text-end">VALIDER</button>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Cette ligne existe déjà dans le tableau. Aimeriez-vous modifier la quantité ou le prix?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Ne pas
                        modifier</button>
                    <button type="button" class="btn btn-primary" id="continueButton">Modifier</button>
                </div>
            </div>
        </div>
    </div>


    </div>
    </div>
    @include('components.alert')
    <script>
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

            // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
            function updateDesignation() {
                var stockProduits = {!! json_encode($stock_produits) !!};
                // console.log(stockProduits)
                $('.produit-select').each(function() {
                    var produit_magasin = $(this).val();
                    var parties = produit_magasin.split("|");
                    var selectedReference = parties[0]; // Contiendra "P002"
                    // var magasinWithId = parties[1]; // Contiendra "MAGASIN-GANHI"
                    // console.log('proreferrence', selectedReference, magasin)
                    var row = $(this).closest('tr');
                    var referenceProduit = selectedReference.split('-')[1]
                    var idStock = selectedReference.split('-')[0]
                    magasin = parties[2]
                    idMagasin = parties[1]
                    // console.log(idMagasin, magasin)
                    // console.log(magasinWithId.split('|'))
                    var designationInput = $('.designation-input');
                    var magasinInput = $('.magasin-input');
                    var quantiteDispoInput = $('.quantite-dispo-input');

                    var produit = stockProduits.find(function(element) {
                        return element.Reference === referenceProduit && element.NomMagasin ===
                            magasin;
                    });
                    // console.log(produit)
                    // Vérifier si la référence sélectionnée existe dans les stocks
                    if (produit) {
                        // Remplir les champs avec les données du produit
                        designationInput.val(produit.Designation);
                        // console.log(designationInput.val(produit.Designation))
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
        $('#quantity_out').on('input', function() {
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
        // $('#quantity_out').on('input', function() {
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
                $('#produit').val(null).trigger('change');;
                $('#designation').val('');
                $('#magasin').val('');
                $('#quantity').val('');
                $('#quantity_out').val('');
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
                var quantity_out = parseFloat($('#quantity_out').val());
                // Vérifier si la quantité et le prix sont valides
                if (!isNaN(quantite) && !isNaN(quantity_out)) {
                    // Ajouter ou mettre à jour la ligne
                    updateOrAddRow(reference, designation, magasin, quantite, quantity_out);
                    clearFormFields();
                    toggleSubmitButton();
                } else {
                    // Afficher un message d'erreur si la quantité ou le prix n'est pas un nombre valide
                    return alert('Veuillez entrer une quantité et un prix valides.');
                }

            });

            // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
            function updateOrAddRow(reference, designation, magasin, quantite, quantity_out) {
                //   console.log(quantite, quantity_out)
                var rows = $('#table2 tbody tr');
                var rowToUpdate = null;




                //   console.log('rowCount', rowCountAdd)

                //   console.log(totalPrixAchat)

                if (reference === '' || designation === '' || magasin === '' || quantite === '' || quantity_out ===
                    '') {
                    alert('Veuillez remplir tous les champs.');
                    return false;
                }

                // Vérification de la validité des valeurs numériques
                if (isNaN(parseFloat(quantite)) || isNaN(parseFloat(quantity_out))) {
                    alert('Les champs quantité et prix d\'achat doivent être des nombres.');
                    return false;
                }

                if (quantite <= 0 || quantity_out <= 0) {
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
                    // totalPrixAchat = parseFloat(quantity_out);
                    // $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                    // var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) + parseFloat(
                    //     quantite);
                    // var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(4) input').val() * 0) + parseFloat(
                    //     quantity_out);
                    // rowToUpdate.find('td:eq(3) input').val(updatedQuantite.toFixed(2));
                    // rowToUpdate.find('td:eq(4) input').val(updatedPrixAchat.toFixed(2));

                    $('#confirmationModal').modal('show');

                    // Lorsque l'utilisateur clique sur "Continuer"
                    $('#continueButton').click(function() {
                        totalPrixAchat = parseFloat(quantity_out);
                        $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                        var updatedQuantite = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) +
                            parseFloat(quantite);
                        var updatedPrixAchat = parseFloat(rowToUpdate.find('td:eq(3) input').val() * 0) +
                            parseFloat(quantity_out);
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
                    console.log(designation);

                    $('#nb_entree').val(rowCount);
                    totalPrixAchat += parseFloat(quantity_out);
                    $('#prixAchatTotal').val(totalPrixAchat.toFixed(2));
                    rowCountAdd++;
                    var newRow = `<tr>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input style="width: 250px;" type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input style="width: 250px;" type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 " value="${designation}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input style="width: 250px;" type="text" name="inputs[${rowCount}][magasin]" class="form-control border-0" value="${magasin}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input style="width: 250px;" type="number" name="inputs[${rowCount}][quantity_out]" class="form-control border-0" id="quantity_out" value="${quantity_out}" readonly>
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
                // var fournisseur = $('select[name="fournisseur"]').val();
                var observation = $('textarea[name="observation"]').val();

                // Si le fournisseur et l'observation sont remplis, activer le bouton Valider, sinon le désactiver
                if (observation) {
                    $('#bouton-valider').prop('disabled', false);
                } else {
                    $('#bouton-valider').prop('disabled', true);
                }
            }

            // Surveiller les événements de changement dans les champs fournisseur et observation
            $('textarea[name="observation"]').on('input', function() {
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
    </script>

@endsection
