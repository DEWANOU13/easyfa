@extends('layouts.master', ['title' => 'Approvisionnement'])
@section('content')

    <style>
        .hover-pers:hover{
            background-color: #ddd;
        }
    </style>

    @include('layouts.partials.entete-page', [
        'infos1' => 'Nouveau Approvisionnement',
        'infos2' => 'Nouveau Approvisionnement',
        'infos3' => 'Nouveau',
    ])

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
            <a href="{{ route('approvisionner') }}" class="btn text-white" style="{{ background_color_1() }}">
                <i class="fa fa-reply" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark">
                Créer un approvisionnement
            </h4>
        </div>
    </div>

    <div class="row mt-2 mb-5">
        <div class="col-md-12">

            <div class="table-responsive mt-3">
                <div class="col-md-12">
                    <form action="{{ route('store.approvisionnement') }}" method="post" id="myForm">
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
                                    <legend class="float-none w-auto px-1">Agence</legend>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="form-label fw-bold" for="magasin_source">Agence</label>
                                            <span class="translate-middle text-danger mt-2">*
                                            </span>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="magasin_source" type="text" class="form-select js-single"
                                                    id="magasin_source" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">Sélectionnez un magasin source</option>
                                                    @foreach ($agence_source as $key => $agence)
                                                        <option value="{{ $agence->id }}">
                                                            {{ $agence->NomAgence}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Le magasin source est obligatoire</div>
                                            </div>
                                        </div>

                                        <div class="form-group col-sm-12">
                                            <label class="form-label fw-bold" id="magasin_destination">Agence de
                                                destination</label>
                                                <span class="translate-middle text-danger mt-2">*
                                                </span>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="magasin_destination" type="text" class="form-select magasinCreateimput js-single"
                                                    id="magasin_destination" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">Sélectionnez un magasin de destination</option>
                                                    @foreach ($agence_destination as $key => $agence_destination)
                                                        <option value="{{ $agence_destination->id }}">
                                                            {{ $agence_destination->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- <button data-bs-toggle="modal" data-bs-target="#creeMagasin"
                                                    class="btn btn-outline-secondary hover-pers" title="Créer un nouveau magasin" style="height: 28px; color: #0d6efd; border: 2px solid #0d6efd;"" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#creeMagasin">
                                                    <span style="position: relative; top: -1px">Créer</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="icon icon-tabler icon-tabler-circle-plus"
                                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M9 12h6" />
                                                        <path d="M12 9v6" />
                                                    </svg>
                                                </button> --}}

                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <label class="form-label fw-bold" id="observation">Observation</label>
                                            <span class="translate-middle text-danger mt-2">*
                                            </span>
                                            <div class="input-group input-group-sm mb-3">
                                                <textarea class="form-control" name="observation" id="" cols="2" rows="2" required></textarea>
                                                <div class="invalid-feedback">L'observation est obligatoire</div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3" id="table">
                                    <legend class="float-none w-auto px-1">Produit</legend>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold" for="produit">Produit</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="" type="text" class="form-select produit-select js-single"
                                                    id="produit" value="{{ old('produit') }}"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" >
                                                    <option value="">Sélectionnez un produit</option>
                                                    @foreach ($stock_produits as $key => $value)
                                                        <option
                                                            value="{{ $value->id . '-' . $value->Reference }}|{{ $value->Id_Magasin . '|' . $value->NomMagasin }}">
                                                            {{ $value->Designation }}==>{{ $value->NomMagasin }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold" for="designation">Désignation</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <input step="0.01" type="text" name=""
                                                    class="form-control designation-input" value="{{ old('designation') }}"
                                                    id="designation" placeholder="Désignation..." readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold" for="magasin">Magasin</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="text" name="" class="form-control magasin-input"
                                                    value="{{ old('magasin') }}" id="magasin" placeholder="Magasin..."
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold" for="quantity">Quantité Disponible</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="text" name=""
                                                    class="form-control quantite-dispo-input"
                                                    value="{{ old('quantity') }}" id="quantity" readonly
                                                    placeholder="Quantité disponible...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold" for="quantity_transfer">Quantité à
                                                Approv</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <input min="0" type="number" name="" class="form-control"
                                                    placeholder="Qté à approv..."
                                                    value="{{ old('quantity_transfer') }}" id="quantity_transfer">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 d-flex align-items-end"> --}}
                                        {{-- </div> --}}
                                    </div>
                                    <button type="button" id="add" name="add"
                                        class="btn text-white float-end" style="{{ background_color_1() }}"><span
                                            class="fw-bold">+</span> Ajouter</button>
                                </fieldset>
                            </div>
                        </div>

                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3" id="table">
                                    <legend class="float-none w-auto px-1">Détails Approvisionnement</legend>
                                    <div class="table-responsive mt-3 overflow-y-scroll "
                                    style="max-height: 270px;">
                                    <div class="col-md-12">
                                        <table class="table table-bordered" id="table2">
                                            <thead class="table-primary"
                                                style="position: sticky; top:0%; z-index:1;">
                                                <tr>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Référence </th>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Désignation</th>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Magasin</th>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                        Qté à tranférer</th>
                                                    <th style=" {{ background_color_2() }}" scope="col">
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Lignes du deuxième tableau seront ajoutées ici -->
                                            </tbody>
                                        </table>
                                        {{-- <table class="table table-bordered">
                                        <tr>
                                            <td>Total Prix Achat
                                                <div>
                                                    <input type="text" type="text" id="prixAchatTotal"
                                                        readonly>
                                                </div>
                                            </td>
                                            <td>Nombre d'entre
                                                <div>
                                                    <input type="text" type="text" id="nb_entree" readonly>
                                                </div>
                                            </td>
                                        </tr>
                                    </table> --}}
                                    </div>
                                </div>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="confirm-validermodal" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog g modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Souhaitez-vous vraiment confirmer ce transfert?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Continuer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Ajoutez ensuite une section pour le deuxième tableau -->

                        <div class="card m-b-30">
                            <div class="card-body">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">Total</legend>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="" class="fw-bold">Total Nombre Qté à approv</label>
                                            <input class="form-control border-0" type="text" id="prixAchatTotal"
                                                readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="fw-bold">Nombre à approv</label>
                                            <input class="form-control border-0" type="text" id="nb_entree" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="mt-3" style="margin-bottom: 145px;">
                            <div class="card-body">
                                <!-- Button trigger modal -->
                                <div class="col-md-12 text-end mb-3">
                                    <button type="button" class="btn btn-primary" id="confirm-valider"
                                        class="btn btn-sm btn-primary text-end">VALIDER</button>
                                </div>
                            </div>
                        </div>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Ne
                        pas modifier</button>
                    <button type="button" class="btn btn-primary" id="continueButton">Modifier</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="modal fade" id="creeMagasin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-lg">
    <div class="modal-content">
        <form id="creeMagasinForm" action="{{ route('storeMagasin-entree') }}" method="POST" class="">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouveau Magasin</h1>
                <button type="reset" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="validationTextarea" class="form-label fw-bold">Magasin
                                <span class="fs-5 text-danger mb-2">*
                                </span></label>
                            <input type="text" name="NomMagasin" class="form-control" required>
                            <div class="invalid-feedback">Le champ est obligatoire</div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="validationTextarea" class="form-label fw-bold">Agence<span
                                    class="fs-5 text-danger mb-2">*
                                </span></label>

                            <!-- Select menu rempli avec la liste des pays -->
                            <select class="form-select {{ $errors->has('agence_id') ? 'is-invalid' : '' }}" wire:model="agence_id" name="agence_id" required>
                                <option>Choisir une agence</option>
                                {{-- @foreach ($listeAgence as $agence)
                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                                @endforeach --}}
                              </select>
                            <div class="invalid-feedback">Obligatoire</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="validationTextarea" class="form-label fw-bold">Statut<span
                                class="fs-5 text-danger mb-2">*
                            </span></label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Statut_Magasin"
                                value="1" required id="flexRadioDefault2">
                            <label class="form-check-label" for="flexRadioDefault2">
                                Actif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Statut_Magasin"
                                value="0" id="flexRadioDefault1">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Inactif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button type="reset" class="btn btn-secondary text-end" data-bs-dismiss="modal"
                        aria-label="Close">Annuler</button>
                    <button type="submit" id="saveButton2" class="btn btn-primary"
                        wire:click.prevent='validateEditCategorieClient'>
                        Sauvegarder
                    </button>
                </div>
        </form>
    </div>
</div>
</div>
    @include('components.alert')
    <script>
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
    </script>
    <script>
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

                if (quantite < quantity_transfer) {
                    alert('La quantité à approvisionner est supérieure à la quantité disponible en stock.');
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


                // Si le magasin source est sélectionné, masquer le magasin de destination
                if (magasin_source) {
                    $('#produit').prop('disabled', false);
                    $('#magasin_destination option[value="' + magasin_source + '"]').hide();
                } else {
                    $('#magasin_destination').prop('disabled', true);
                    $('#produit').prop('disabled', true);
                }
            }

            // Surveiller les événements de changement dans les champs magasin source et magasin destination
            $('select[name="magasin_source"]').on('change', function() {
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
                    return produit.agence_id == selectedMagasinId;
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
    </script>

@endsection
