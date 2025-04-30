@extends('layouts.master')
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Inventaire Emballage',
        'infos2' => 'Inventaire Emballage',
        'infos3' => 'Nouveau',
    ])

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
            <a href="{{ route('page.inventaire.inventaire') }}" class="btn text-white" style="{{ background_color_1() }}">
                <i class="fa fa-reply" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>
    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark">
                Créer un inventaire emballage
            </h4>
        </div>
    </div>
    <div class="card m-b-30 mb-5">
        <div class="card-body">
            <div class="row my-4">
                <div class="col-md-12">
                    <form id="formcreate" class="row gx-3 gy-2 d-flex align-items-center justify-content-center "
                        action="{{ route('store.inventaire_emballage') }}" methode='post' id="myForm">
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
                        <div class="row">
                            {{-- <div class="card-body"> --}}
                            <div class="col-sm-12">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text fw-bold" id="observation">Observation<span
                                            class=" fs-5 text-danger  ml-1"> *
                                        </span></span>
                                    <textarea class="form-control" name="observation" id="" cols="2" rows="2" required></textarea>
                                    <div class="invalid-feedback">L'observation est obligatoire</div>
                                </div>
                            </div>
                            {{-- </div> --}}

                            <div class="col-sm-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text fw-bold" id="inputGroup-sizing-sm">Magasin<span
                                            class=" text-danger  "> *
                                            <select style="width: 100%" name="" type="text"
                                                class="form-select produit-select js-single" id="magasin"
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un magasin</option>
                                                @foreach ($magasins as $key => $value)
                                                    <option value="{{ $value->Id_Magasin }}-{{ $value->NomMagasin }}"
                                                        {{ old('magasin') == $value->Id_Magasin ? 'selected' : '' }}>
                                                        {{ $value->NomMagasin }}
                                                    </option>
                                                @endforeach
                                            </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text fw-bold" id="inputGroup-sizing-sm">Catégorie<span
                                            class=" text-danger  "> *
                                            <select style="width: 100%" name="" type="text"
                                                class="form-select produit-select js-single" id="categorie"
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                {{-- <option value="Toutes">Toutes</option> --}}
                                                {{-- @foreach ($categories as $key => $value)
                                                    <option value="{{ $value->Id_Categorie }}-{{ $value->Libelle }}"
                                                        {{ old('categorie') == $value->Id_Categorie ? 'selected' : '' }}>
                                                        {{ $value->Libelle }}
                                                    </option>
                                                @endforeach --}}
                                            </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text fw-bold" id="inputGroup-sizing-sm">Produit<span
                                            class=" text-danger  "> *
                                            <select style="width: 100%" name="" type="text"
                                                class="form-select produit-select js-single" id="produit"
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                {{-- <option value="Tous">Tous</option> --}}
                                                {{-- @foreach ($produits as $key => $value)
                                                    <option
                                                        value="{{ $value->Id_Produit }}-{{ $value->Reference }}-({{ $value->Designation }})"
                                                        {{ old('produit') == $value->Id_Produit ? 'selected' : '' }}>
                                                        {{ $value->Designation }}
                                                    </option>
                                                @endforeach --}}
                                            </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="btn-group">
                                        <button type="button" id="add" name="add" class="btn text-white"
                                            style="{{ background_color_1() }}">+
                                            Ajouter</button>
                                    </div>
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
                                            Souhaitez-vous vraiment confirmer cette action ?
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
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value=""
                                        id="flexCheckChecked">
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Ajouter tous les magasins
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="table-responsive">
                                <table class="table" id="table2">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                                Magasin</th>
                                            <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                                Catégorie</th>
                                            <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                                Produit</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 text-end mb-3">
                                <div class="input-group input-group-sm mb-3 right">
                                    <div class="btn-group">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                                            id="bouton-valider" class="btn text-white"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>
    @include('layouts.alert')

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
            $('#add').prop('disabled', true);
            gererVisibiliteBoutonAppliquer()
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

            function clearFormFields() {
                $('#produit').val(null).trigger('change');
                $('#magasin').val(null).trigger('change');
                $('#categorie').val(null).trigger('change');

            }

            // Fonction pour vérifier si une ligne avec les mêmes informations existe déjà dans le tableau
            function ligneExistante(magasin, categorie, produit) {
                var existante = false;
                $('#table2 tbody tr').each(function() {
                    var magasin = $(this).find('input[name^="inputs["][name$="][magasin]"]').val();
                    var categorie = $(this).find('input[name^="inputs["][name$="][categorie]"]').val();
                    var produit = $(this).find('input[name^="inputs["][name$="][produit]"]').val();

                    if (magasin === magasin && categorie === categorie && produit === produit) {
                        existante = true;
                        return false; // Sortir de la boucle each si une ligne correspondante est trouvée
                    }
                });
                return existante;
            }

            var rowCountAdd = 0;
            // Ajouter une nouvelle ligne au tableau lorsque le bouton "Afficher" est cliqué
            // Fonction pour vérifier si une ligne avec les mêmes informations existe déjà dans le tableau
            function ligneExistante(magasin) {
                var existante = false;
                var rowIndex = -1;
                $('#table2 tbody tr').each(function(index) {
                    var magasinCell = $(this).find('input[name^="inputs["][name$="][magasin]"]').val();
                    var categorieCell = $(this).find('input[name^="inputs["][name$="][categorie]"]').val();
                    var produitCell = $(this).find('input[name^="inputs["][name$="][produit]"]').val();
                    if (magasin === magasinCell && categorie === categorieCell && produit === produitCell) {
                        existante = true;
                        rowIndex = index +
                            1; // Index commence à 0, mais les numéros de ligne commencent à 1
                        return false; // Sortir de la boucle each si une ligne correspondante est trouvée
                    }
                });
                return {
                    existante: existante,
                    rowIndex: rowIndex
                };
            }

            // Ajouter une nouvelle ligne au tableau lorsque le bouton "Afficher" est cliqué
            $('#add').click(function() {
                // Récupérer les valeurs sélectionnées
                var magasin = $('#magasin').val();
                var categorie = $('#categorie').val();
                var produit = $('#produit').val();

                if (magasin === '' || categorie === '' || produit === '' ) {
                    alert('Veuillez remplir tous les champs.');
                    return false;
                }

                // Vérifier si le magasin existe déjà dans le tableau
                var existingRow = ligneExistante(magasin, categorie, produit);
                if (existingRow.existante) {
                    alert("Ce magasin existe déjà dans la ligne " + existingRow.rowIndex + " du tableau.");
                } else {
                    // Ajouter les valeurs à la table en tant que nouvelle ligne
                    var rowCount = $('#table2 tbody tr').length + 1;
                    rowCountAdd++
                    var newRow = `
                    <tr>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" name="inputs[${rowCount}][magasin]" class="form-control border-0" value="${magasin}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" name="inputs[${rowCount}][categorie]" class="form-control border-0" value="${categorie}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit}" readonly>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                        </td>
                    </tr>`;
                    $('#table2 tbody').append(newRow);

                    // Vider les champs du formulaire après l'ajout de la ligne
                    clearFormFields();
                    gererVisibiliteBoutonAppliquer();
                }
            });


            // Supprimer une ligne du tableau
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                rowCountAdd--
                if (rowCountAdd == 0) {
                    $('#add').prop('disabled', true); // Cacher le bouton Valider s'il n'y a pas de lignes
                }
                // clearFormFields();
                gererVisibiliteBoutonAppliquer()
            });
        });

        $(document).ready(function() {
            // Fonction pour vérifier les champs et activer/désactiver le bouton Afficher
            function checkFields() {
                var magasin = $('#magasin').val();
                var categorie = $('#categorie').val();
                var produit = $('#produit').val();
                // Si tous les champs sont renseignés, activer le bouton Afficher
                if (magasin && categorie) {
                    $('#add').prop('disabled', false);
                } else {
                    // Sinon, désactiver le bouton Afficher
                    $('#add').prop('disabled', true);
                }
            }

            // Appeler la fonction lorsqu'un champ est modifié
            $('.form-select').change(function() {
                checkFields();
            });

            // Appeler la fonction au chargement de la page
            checkFields();
        });


        $(document).ready(function() {
            // Fonction pour gérer la visibilité du bouton "Appliquer"
            function gererVisibiliteBoutonAppliquer() {
                var rowCount = $('#table2 tbody tr').length;
                if (rowCount > 0) {
                    $('#bouton-valider').show(); // Affiche le bouton
                } else {
                    $('#bouton-valider').hide(); // Masque le bouton
                }
            }

            // Ajouter ou retirer toutes les options du select lorsque l'on coche ou décoche la case à cocher
            $('#flexCheckChecked').change(function() {
                var magasinSelect = $('#magasin'); // Sélecteur pour magasin
                var categorieSelect = $('#categorie'); // Sélecteur pour catégorie
                var produitSelect = $('#produit'); // Sélecteur pour produit
                var tableBody = $('#table2 tbody');
                tableBody.empty(); // Vide le corps du tableau

                if ($(this).is(':checked')) {
                    // Ajouter toutes les options sauf l'option "Sélectionnez un magasin"
                    magasinSelect.find('option').not(':first').each(function(index) {
                        var magasinIdNom = magasinSelect.find('option').eq(index + 1)
                    .val(); // ID et nom du magasin
                        var categorieIdNom = categorieSelect.find('option').eq(index + 1)
                    .val(); // ID et nom de la catégorie
                        var produitIdNom = produitSelect.find('option').eq(index + 1)
                    .val(); // ID et nom du produit

                        // Vérifier si la ligne existe déjà dans le tableau
                        if (!ligneExistante(magasinIdNom, categorieIdNom, produitIdNom)) {
                            var newRow = `
                        <tr>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${tableBody.children().length + 1}][magasin]" class="form-control border-0" value="${magasinIdNom}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${tableBody.children().length + 1}][categorie]" class="form-control border-0" value="Toutes" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${tableBody.children().length + 1}][produit]" class="form-control border-0" value="Tous" readonly>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
                            </td>
                        </tr>`;
                            tableBody.append(newRow); // Ajouter la nouvelle ligne
                        }
                    });
                }

                // Met à jour la visibilité du bouton "Appliquer"
                gererVisibiliteBoutonAppliquer();
            });

            // Fonction pour vérifier si une ligne avec les mêmes informations existe déjà dans le tableau
            function ligneExistante(magasinNom, categorieNom, produitNom) {
                var existante = false;
                $('#table2 tbody tr').each(function() {
                    var magasin = $(this).find('input[name^="inputs["][name$="[magasin]"]').val();
                    var categorie = $(this).find('input[name^="inputs["][name$="[categorie]"]').val();
                    var produit = $(this).find('input[name^="inputs["][name$="[produit]"]').val();

                    if (magasin === magasinNom && categorie === categorieNom && produit === produitNom) {
                        existante = true;
                        return false; // Sortir de la boucle each si une ligne correspondante est trouvée
                    }
                });

                return existante;
            }

            // Supprimer une ligne lors du clic sur le bouton "X"
            $('#table2').on('click', '.remove-row', function() {
                $(this).closest('tr').remove(); // Supprime la ligne
                gererVisibiliteBoutonAppliquer(); // Mettre à jour la visibilité du bouton après suppression
            });

            // Initialiser la visibilité du bouton au chargement de la page
            gererVisibiliteBoutonAppliquer();
        });


        $(document).ready(function() {
            $('#myForm').keypress(function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Empêche l'action par défaut du formulaire
                }
            });
        });

        $(document).ready(function() {
            // Gérer le changement de catégorie
            $('#categorie').on('change', function() {
                var selectedCategoryId = $(this).val();
                // Vider le menu déroulant des produits avant de l'actualiser
                $('#produit').empty();
                $('#produit').append('<option value="Tous">Tous</option>');

                if (selectedCategoryId) {
                    $.ajax({
                        url: '/get-products-by-category-inventaire-emballage/' +
                            selectedCategoryId, // URL à adapter selon tes routes
                        type: 'GET',
                        success: function(response) {
                            // Ajouter les produits retournés dans le menu déroulant
                            if (response.length > 0) {
                                response.forEach(function(product) {
                                    $('#produit').append('<option value="' +
                                        product.Id_Emballage + '-' + product
                                        .Designation + '" ' +
                                        '>' + product.Designation +
                                        '</option>'
                                    );

                                });
                            } else {
                                // console.log('fjhdjh')
                                $('#produit').append('<option value="Tous">Tous</option>')
                            }
                        }
                    });
                } else {
                    // $('#produit').append('<option value="Tous">Tous</option>');
                }
            });
        });

        $(document).ready(function() {
            // Gérer le changement de catégorie
            $('#magasin').on('change', function() {
                var selectedCategoryId = $(this).val();
                // Vider le menu déroulant des produits avant de l'actualiser
                $('#categorie').empty();
                $('#categorie').append('<option value="">Selectionnez une catégorie</option>');
                $('#categorie').append('<option value="Toutes">Toutes</option>');

                if (selectedCategoryId) {
                    $.ajax({
                        url: '/get-category-by-magasin-inventaire-emballage/' +
                            selectedCategoryId, // URL à adapter selon tes routes
                        type: 'GET',
                        success: function(response) {
                            // Ajouter les produits retournés dans le menu déroulant
                            if (response.length > 0) {
                                response.forEach(function(category) {
                                    $('#categorie').append('<option value="' +
                                        category.Id_Magasin + '-' +
                                        category.category_id + '-' + category
                                        .Libelle + '" ' +
                                        '>' + category.Libelle +
                                        '</option>'
                                    );

                                });
                            } else {
                                $('#categorie').append(
                                    '<option value="Toutes">Aucun produit disponible</option>'
                                );
                            }
                        },
                        error: function() {
                            alert('Erreur lors du chargement des produits.');
                        }
                    });
                } else {
                    // $('#categorie').append('<option value="Tous">Tous</option>');
                }
            });
        });
    </script>
@endsection
