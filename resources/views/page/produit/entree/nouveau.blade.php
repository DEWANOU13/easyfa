@extends('layouts.master')
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Entrées',
        'infos2' => 'Entrées',
        'infos3' => 'Nouveau',
    ])

    <section>
        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="dropdown mb-2">
                <a href="{{ route('page.entree.entree') }}" class="btn text-white" style="{{ background_color_1() }}">
                    <i class="fa fa-reply" aria-hidden="true"></i>
                    Retour
                </a>
            </div>
        </div>
        <div class="card m-b-30">
            <div class="card-header rounded" style="{{ background_color_2() }}">
                <h4 class="mt-2 text-dark">
                    Créer une entrée
                </h4>
            </div>
        </div>
        <form id="creeEntreeForm" class="" action="{{ route('store.entree') }}" method="post" class="my-5">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (emballageActiver())
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Consignation</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="Vente_consignation" class="form-label fw-bold">Entrée par
                                    consignation</label>

                                <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check Vente_consignation-select"
                                        name="entree_consignation" checked value="0" id="entree_consignation0" checked>
                                    <label class="btn btn_Vente_consignation" for="entree_consignation0">NON</label>

                                    <input type="radio" class="btn-check Vente_consignation-select"
                                        name="entree_consignation" value="1" id="entree_consignation1">
                                    <label class="btn btn_Vente_consignation" for="entree_consignation1">OUI</label>
                                </div>

                            </div>
                        </div>
                    </fieldset>

                </div>
            </div>
            @endif
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Fournisseur</legend>
                        <div class="col-sm-12">
                            <label class="fw-bold">Fournisseur <span class="  text-danger mb-2 ml-1"> *
                                </span></label>
                            <div class="input-group input-group-sm mb-3">
                                <select name="fournisseur" type="text" class="form-select js-single" id="fournisseur"
                                    required aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                    <option value="">Sélectionnez un fournisseur</option>
                                    @foreach ($fournisseurs as $fournisseur)
                                        <option value="{{ $fournisseur->id }}">
                                            {{ $fournisseur->DenominationSociale }}
                                        </option>
                                    @endforeach
                                </select>
                                <button data-bs-toggle="modal" data-bs-target="#creeFournisseur"
                                    class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#creeCategorie">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M9 12h6" />
                                        <path d="M12 9v6" />
                                    </svg>Créer</button>
                                {{-- <div class="invalid-feedback">Le fournisseur est obligatoire</div> --}}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="fw-bold" id="observation">Observation<span class="  text-danger mb-2 ml-1"> *
                                </span></label>
                            <div class="input-group input-group-sm mb-3">
                                <textarea class="form-control" name="observation" id="" cols="2" rows="2" required></textarea>
                                {{-- <div class="invalid-feedback">L'observation est obligatoire</div> --}}
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
                            <table class="table mt-4 border-0">
                                {{-- <tr class="border border-light"> --}}
                                <div class="form-group col-md-4">
                                    <label class="form-label fw-bold" for="">Produit</label>
                                    {{-- <span class="input-group-text" id="produit">Produit</span> --}}
                                    <select name="" type="text" class="form-select produit-select js-single"
                                        id="produit" value="{{ old('produit') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                        <option value="">Sélectionnez un produit</option>
                                        @foreach ($produits as $reference => $designation)
                                            <option value="{{ $reference }}">{{ $designation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label fw-bold" for="">Référence du produit</label>
                                    {{-- <span class="input-group-text" id="designation">Désignation</span> --}}
                                    <input step="0.01" type="text" name=""
                                        class="form-control designation-input js-single" value="{{ old('designation') }}"
                                        id="designation" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label fw-bold" for="">Magasin</label>
                                    <div class="input-group input-group-sm mb-3">
                                        {{-- <span class="input-group-text" id="magasin">Magasin</span> --}}
                                        <select name="" type="text" class="form-select js-single"
                                            value="{{ old('magasin') }}" id="magasin"
                                            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">

                                            <option value="">Sélectionnez un magasin</option>
                                            @foreach ($magasins as $magasin)
                                                <option value="{{ $magasin->id }} - {{ $magasin->NomMagasin }}">
                                                    {{ $magasin->NomMagasin }} ==> {{ $magasin->NomAgence }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button data-bs-toggle="modal" data-bs-target="#creeMagasin"
                                            class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#creeMagasin">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-circle-plus" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                <path d="M9 12h6" />
                                                <path d="M12 9v6" />
                                            </svg>Créer</button>

                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="form-label fw-bold" for="">Quantité</label>
                                    {{-- <span class="input-group-text" id="quantity">Qté</span> --}}
                                    <input min="1" step="0.01" type="number" name=""
                                        class="form-control" value="{{ old('quantity') }}" id="quantity"
                                        placeholder="Quantite...">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label fw-bold" for="">PrixAchatNet</label>
                                    {{-- <span class="input-group-text" id="prix_achat">Prix d'achat</span> --}}
                                    <input min="1" type="number" name="" class="form-control"
                                        placeholder="Prix d'achat..." value="{{ old('prix_achat') }}" id="prix_achat">
                                </div>
                                {{-- </tr> --}}
                            </table>
                        </div>
                        <button type="button" id="add" name="add" class="btn text-white w-auto float-end"
                            style="{{ background_color_1() }}"><span class="fw-bold ">+</span
                                class="fw-bold">Ajouter</button>
                    </fieldset>
                </div>
            </div>

            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Ligne entrée</legend>
                        <!-- Modal -->
                        <div class="modal fade" id="confirm-validermodal" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog g modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Souhaitez-vous vraiment faire l'entée?
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
                        <!-- Ajoutez ensuite une section pour le deuxième tableau -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table2">
                                <thead>
                                    <tr>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Référence </th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Désignation</th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Magasin</th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Qté</th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Prix d'achat</th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
                                            Montant </th>
                                        <th style=" {{ background_color_2() }}" class="text-white" scope="col">
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
                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Total</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="" class="fw-bold">Total Prix Achat</label>
                                <input class="form-control border-0" type="text" id="prixAchatTotal" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="fw-bold">Nombre Entrées</label>
                                <input class="form-control border-0" type="text" id="nb_entree" readonly>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <!-- Button trigger modal -->
            <div class="mb-5 d-flex">
                <button id="confirm-valider"type="button" class="btn text-white d-block ms-auto float-end"
                    style="{{ background_color_1() }}" id="bouton-valider">VALIDER</button>
            </div>
            <br>
        </form>

        <div class="modal fade" id="creeFournisseur" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-lg">
                <div class="modal-content">
                    <form id="creeFournisseurForm" action="{{ route('storeFournisseur-entree') }}" method="POST"
                        class="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouveau fournisseur</h1>
                            <button type="reset" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Dénomination
                                            sociale<span class="fs-5 text-danger mb-2">*
                                            </span></label>
                                        <input type="text" name="DenominationSociale" class="form-control" required>
                                        <div class="invalid-feedback">La dénomination sociale est obligatoire</div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Pays<span
                                                class="fs-5 text-danger mb-2">*
                                            </span></label>

                                        <!-- Select menu rempli avec la liste des pays -->
                                        <select class="form-select {{ $errors->has('Pays') ? 'is-invalid' : '' }}"
                                            name="Pays" aria-label="Default select example" id="countrySelect">
                                            <option></option>
                                            <option
                                                value="{{ old('Pays', isset($fournisseur) ? $fournisseur->Pays : '') }}">
                                                {{ old('Pays', '') }}</option>

                                        </select>
                                        {!! $errors->first('Pays', '<p class="error">:message</p>') !!}
                                        <div class="invalid-feedback">Obligatoire</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">IFU</label>
                                        <input type="number" name="NumeroIfu" class="form-control" minlength="13"
                                            id="ifuInput" maxlength="13" aria-label="file example">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Adresse</label>
                                        <input type="text" name="AdresseFournisseur" class="form-control"
                                            aria-label="file example">
                                        <div class="invalid-feedback">Adresse du fournisseur est obligatoire</div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Téléphone
                                            fixe</label>
                                        <input type="text" name="TelephoneFixe" class="form-control"
                                            aria-label="file example">
                                        <div class="invalid-feedback"> Le telephone fixe est obligatoire</div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Téléphone
                                            modile</label>
                                        <input type="text" name="TelephoneMobile" class="form-control"
                                            aria-label="file example">
                                        <div class="invalid-feedback">Le telephone mobile est obligatoire</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationTextarea" class="form-label fw-bold">Adresse
                                            email</label>
                                        <input type="email" name="AdresseMail" class="form-control"
                                            placeholder="name@gmail.com" aria-label="file example">
                                        <div class="invalid-feedback">Le mail est obligatoire</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="validationTextarea" class="form-label fw-bold">Statut<span
                                            class="fs-5 text-danger mb-2">*
                                        </span></label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Statut_fournisseur"
                                            value="1" required id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Actif
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Statut_fournisseur"
                                            value="0" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Inactif
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">

                                </div>
                            </div>
                            <div class="modal-footer" id="importFieldset">
                                <button type="reset" class="btn btn-secondary text-end" data-bs-dismiss="modal"
                                    aria-label="Close">Annuler</button>
                                <button type="submit" id="saveButton" class="btn btn-primary"
                                    wire:click.prevent='validateEditCategorieClient'>
                                    Sauvegarder
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <div class="modal fade" id="creeMagasin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-lg">
                <div class="modal-content">
                    <form id="creeMagasinForm" action="{{ route('storeMagasin-entree') }}" method="POST"
                        class="">
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
                                        <select class="form-select {{ $errors->has('agence_id') ? 'is-invalid' : '' }}"
                                            wire:model="agence_id" name="agence_id" required>
                                            <option>Choisir une agence</option>
                                            @foreach ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                                            @endforeach
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
        <style>
            .btn_Vente_consignation,
            .btn_Vente_consignation:hover {
                border-color: #FFA500;
            }

            .btn-check:checked+.btn,
            .btn.active,
            .btn.show,
            .btn:first-child:active,
            :not(.btn-check)+.btn:active {
                background-color: #FFA500 !important;
                border-color: #FFA500 !important;
                color: #000 !important;
            }
        </style>
    </section>
    @include('layouts.alert')

    <script>
        const formIds = ['creeFournisseurForm', 'creeEntreeForm', 'creeMagasinForm'];

        function preventEnterSubmission(formId) {
            document.getElementById(formId).addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }
        formIds.forEach(preventEnterSubmission);
    </script>
    <script>
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
    </script>
    <script>
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
    </script>
    <script>
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
    </script>
    <script>
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
    </script>
    <script>
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
    </script>

    <script>
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
                                <input style="width: 250px;" type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${reference}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input style="width: 250px;" type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 designation-input" value="${designation}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input style="width: 250px;" type="text" name="inputs[${rowCount}][magasin]" class="form-control border-0" value="${magasin}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input style="width: 250px;" type="number" name="inputs[${rowCount}][quantity]" class="form-control border-0" id="quantity" value="${quantite}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input style="width: 250px;" type="number" name="inputs[${rowCount}][prix_achat]" class="form-control border-0" id="prix_achat" value="${prix_achat}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-3">
                                <input style="width: 250px;" type="number" name="inputs[${rowCount}][montant]" class="form-control border-0" id="montant" value="${quantite * prix_achat}" readonly>
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
    </script>

@endsection
