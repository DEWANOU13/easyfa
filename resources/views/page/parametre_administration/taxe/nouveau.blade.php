@extends('layouts.master', ['title' => $produit->exists ? 'Modifier Taxe' : 'Creer Taxe'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Taxe',
        'infos2' => 'Taxe',
        'infos3' => isset($produit) ? 'Modification' : 'Nouveau',
    ])

    <style>
        .hover-pers:hover {
            background-color: #ddd;
        }
    </style>

    <section>
        <div class="row">
            <div class="col-md-8 offset-md-2 mb-5">
                <div class="d-flex flex-row-reverse bd-highlight">
                    <div class="dropdown mb-2">
                        <a href="{{ route('taxe') }}" class="btn text-white" style="{{ background_color_1() }}">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h4 class="mt-2 text-dark">
                            {{-- @if (isset($produit))
                            Modification de {{ $produit->Designation }}
                        @else
                            Enregistrement d'un produit
                        @endif --}}
                            Enregistrement d'une taxe
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('store.produit') }}" method="GET" id="myForm">
                            @csrf
                            <div class="form-group">
                                <label class="form-label fw-bold" for="type">Type</label><br>

                                <div class="form-check form-check-inline">
                                    <input name="type" class="form-check-input" type="radio" id="produit"
                                        value="TAXE_SIMPLE">
                                    <label class="form-check-label" for="produit">TAXE_SIMPLE</label>
                                </div>
                            </div>
                            <div class="form-group">
                                {{-- <label class="form-label fw-bold" for="reference">Référence</label> --}}
                                <label for="reference" class="form-label fw-bold">Référence<span
                                        class="text-danger">*</span></label>

                                <input name="reference" class="form-control" id="reference" type="text"
                                    value="{{ old('reference') }}" required>
                            </div>
                            <div class="form-group">
                                {{-- <label class="form-label fw-bold" for="designation">Désignation</label> --}}
                                <label for="designation" class="form-label fw-bold">Désignation<span
                                        class="text-danger">*</span></label>
                                <input name="designation" class="form-control" id="designation" type="text"
                                    value="{{ old('designation') }}" required>
                            </div>
                            <div class="form-group">
                                {{-- <label for="categorie" class="form-label fw-bold">Catégorie produit</label> --}}
                                <label for="categorie" class="form-label fw-bold">Catégorie produit<span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="categorie" class="form-select js-single" id="categorie" required>
                                        <option value="">Sélectionnez une catégorie</option>
                                        @foreach ($categorie_produits as $categorie)
                                            <option value="{{ $categorie->id }}"
                                                {{ old('categorie') == $categorie->id ? 'selected' : '' }}>
                                                {{ $categorie->Libelle }}</option>
                                        @endforeach
                                    </select>
                                    <button data-bs-toggle="modal" data-bs-target="#creeCategorieProduit"
                                        class="btn btn-outline-secondary hover-pers" title="Créer une nouvelle categorie de produit" style="height: 28px; color: #0d6efd; border: 2px solid #0d6efd;" type="button">
                                        <span style="position: relative; top: -3px">Créer</span>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-circle-plus" style="margin-top: -10px;" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M9 12h6" />
                                            <path d="M12 9v6" />
                                        </svg></button>
                                </div>

                            </div>
                            <div class="form-group">
                                {{-- <label for="unite_comptage" class="form-label fw-bold" >Unité de comptage</label> --}}
                                <label for="unite_comptage" class="form-label fw-bold">Unité de comptage <span
                                        class="text-danger">*</span></label>

                                <div class="input-group">
                                    <select name="unite_comptage" class="form-select js-single" id="unite_comptage" required>
                                        <option value="">Sélectionnez une unité de comptage </option>
                                        @foreach ($unite_comptages as $unite_comptage)
                                            <option value="{{ $unite_comptage->id }}"
                                                {{ old('unite_comptage') == $unite_comptage->id ? 'selected' : '' }}>
                                                {{ $unite_comptage->Libelle }} ({{ $unite_comptage->Code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button data-bs-toggle="modal" data-bs-target="#creeUniteComptage"
                                        class="btn btn-outline-secondary hover-pers" title="Créer une nouvelle categorie de produit" style="height: 28px; color: #0d6efd; border: 2px solid #0d6efd;" type="button">
                                        <span style="position: relative; top: -3px">Créer</span>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-circle-plus" style="margin-top: -10px;" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M9 12h6" />
                                            <path d="M12 9v6" />
                                        </svg></button>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="form-label fw-bold" for="statut">Statut</label><br>
                                <div class="form-check form-check-inline">
                                    <input name="statut" class="form-check-input" type="radio" id="inactif"
                                        value="INACTIF">
                                    <label class="form-check-label" for="inactif">INACTIF</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="statut" class="form-check-input" type="radio" id="actif"
                                        value="ACTIF" checked>
                                    <label class="form-check-label" for="actif">ACTIF</label>
                                </div>
                            </div>
                            <a type="reset" href="{{ route('page.produit.produit') }}"
                                class="btn btn-secondary btn-lg waves-effect waves-light">Annuler</a>
                            <button type="button" id="saveButton"
                                class="btn btn-lg pull-right waves-effect waves-light text-white btn-send"
                                data-bs-toggle="modal" data-bs-target="#staticProduit"
                                style="{{ background_color_1() }}">
                                Sauvegarder
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="staticProduit" data-bs-backdrop="static"
                                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous vraiment sauvegarder ces informations ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Non</button>
                                            <button type="submit" class="btn btn-primary">Oui sauvegarder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Modal CATEGORIE PRODUIT -->
                        <div class="modal fade" id="creeCategorieProduit" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form id="categorieForm" method="GET"
                                        action="{{ route('store.produit_categorie') }}">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouvelle Catégorie</h1>
                                            <button type="reset" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="libelle" class="form-label fw-bold">Catégorie</label>
                                                <input name="libelle" type="text" class="form-control" required>
                                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="reset" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Fermer</button>
                                                <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal UNITE COMPTAGE -->
                    <div class="modal fade" id="creeUniteComptage" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="uiniteComptageForm" method="POST"
                                    action="{{ route('store.produit_unite_comptage') }}">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouvelle Unité comptage</h1>
                                        <button type="reset" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="code" class="form-label fw-bold">Code</label>
                                            <input name="code" type="text" class="form-control" required>
                                            <div class="invalid-feedback">Le code est obligatoire</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="libelle" class="form-label fw-bold">Libellé</label>
                                            <input name="libelle" type="text" class="form-control" required>
                                            <div class="invalid-feedback">Le libellé est obligatoire</div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="reset" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Fermer</button>
                                            <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @include('layouts.alert')
            </div>
        </div>
        </div>
        </div>
    </section>

    @include('layouts.alert')
    <script>
        $(document).ready(function() {
            $('#myForm').on('submit', function(event) {
                const typeSelected = $('input[name="type"]:checked').val();
                if (typeSelected !== 'TAXE_SIMPLE') {
                    alert('Veuillez sélectionner "TAXE_SIMPLE" comme type.');
                    event.preventDefault(); // Empêche la soumission du formulaire
                }
            });
        });

        $(document).ready(function() {
            $('#myForm').keypress(function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Empêche l'action par défaut du formulaire
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#categorieForm').on('submit', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {

                            alert('Catégorie produit ajoutée avec succès');

                            $('#creeCategorieProduit').modal('hide');

                            $('#categorieForm')[0].reset();

                            // Ajoutez le nouvel élément au <select>
                            $('#categorie').append(new Option(response.newCategoryName, response
                                .newCategoryId));

                            $('#categorie').val(response.newCategoryId);
                        } else {
                            alert('Erreur lors de l\'ajout de la catégorie');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Erreur lors de l\'ajout de la catégorie');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#uiniteComptageForm').on('submit', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {

                            alert('Unité de comptage ajoutée avec succès');

                            $('#creeUniteComptage').modal('hide');

                            $('#uiniteComptageForm')[0].reset();

                            // Ajoutez le nouvel élément au <select>
                            $('#unite_comptage').append(new Option(response.newCategoryName,
                                response.newCategoryId));

                            $('#unite_comptage').val(response.newCategoryId);
                        } else {
                            alert('Erreur lors de l\'ajout de l\'unite de comptage');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Erreur lors de l\'ajout de l\'unite de comptage');
                    }
                });
            });
        });
    </script>
@endsection

{{--
<SECtion>

    <div class="row">
        <!-- end col -->

        <div class="col-md-8 offset-md-2 mb-5">
            <div class="d-flex flex-row-reverse bd-highlight">
                <div class="dropdown mb-2">
                    <a href="{{ route('page.produit.produit') }}" class="btn text-white"
                        style="{{ background_color_1() }}">
                        <i class="fa fa-reply" aria-hidden="true"></i>
                        Retour
                    </a>
                </div>
            </div>
            <div class="container d-flex justify-content-center  ">
                <div class=" card">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h4 class="mt-2 text-dark">
                            @if (isset($produit))
                                Modification de {{ $produit->Designation }}
                            @else
                                Enregistrement d'un produit
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form methode="post" action="{{ route('store.produit') }}"
                                id="myForm">
                                @csrf
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold" for="type">Type</label><br>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="aucun"
                                                value="AUCUN" checked>
                                            <label class="form-check-label" for="aucun">AUCUN</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="prestation"
                                                value="PRESTATION">
                                            <label class="form-check-label" for="prestation">PRESTATION</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="produit"
                                                value="PRODUIT">
                                            <label class="form-check-label" for="produit">PRODUIT</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label  class="fw-bold">Référence</label><br>
                                        <input name="reference" class="form-control" id="reference" type="text"
                                            value="{{ old('reference') }}" aria-label="file example" required>

                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="designation">Désignation</label><br>
                                        <input name="designation" class="form-control" id="designation" type="text"
                                            value="{{ old('designation') }}" aria-label="file example" required>

                                    </div>
                                    <div class="col-md-6">
                                        <label for="validationTextarea" class="form-label fw-bold">Catégorie
                                            produit</label>

                                        <div class="input-group">
                                            <select name="categorie" class="form-select" id="categorie" type="text"
                                                aria-label="file example" required>
                                                <option value="">Sélectionnez une catégorie</option>
                                                @foreach ($categorie_produits as $categorie)
                                                    <option value="{{ $categorie->id }}"
                                                        {{ old('categorie') == $categorie->id ? 'selected' : '' }}>
                                                        {{ $categorie->Libelle }}</option>
                                                @endforeach
                                            </select>
                                            <button data-bs-toggle="modal" data-bs-target="#creeCategorieProduit"
                                                class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                                data-bs-target="#creeCategorie">
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
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="unite_comptage">Unité de
                                            comptage</label><br>
                                        <div class="input-group">
                                            <select name="unite_comptage" class="form-select" id="unite_comptage"
                                                type="text" aria-label="file example" required>
                                                <option value="">Sélectionnez une unité de comptage </option>
                                                @foreach ($unite_comptages as $unite_comptage)
                                                    <option value=" {{ $unite_comptage->id }}"
                                                        {{ old('unite_comptage') == $unite_comptage->id ? 'selected' : '' }}>
                                                        {{ $unite_comptage->Libelle }}
                                                        ({{ $unite_comptage->Code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button data-bs-toggle="modal" data-bs-target="#creeUniteComptage"
                                                class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                                data-bs-target="#creeCategorie">
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

                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="type">Statut</label><br>
                                    <div class="form-check form-check-inline">
                                        <input name="statut" class="form-check-input" type="radio" id="inactif"
                                            value="INACTIF">
                                        <label class="form-check-label" for="inactif">INACTIF</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="statut" class="form-check-input" type="radio" id="actif"
                                            value="ACTIF" checked>
                                        <label class="form-check-label" for="actif">ACTIF</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#staticProduit" id="saveButton">Sauvegarder</button>
                                </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="staticProduit" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog g modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Souhaitez-vous vraiment faire l'enregistrement?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Continuer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- Modal CATEGORIE PRODUIT -->
                        <div class="modal fade" id="creeCategorieProduit" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouvelle Catégorie</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="was-validated" methode="POST"
                                            action="{{ route('store.produit_categorie') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="validationTextarea"
                                                    class="form-label fw-bold">Catégorie</label>
                                                <input name="libelle" type="text" class="form-control"
                                                    aria-label="file example" required>
                                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                                            </div>
                                            <div class="mb-3">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Fermer</button>
                                                <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Modal UNITE COMPTAGE -->
                        <div class="modal fade" id="creeUniteComptage" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Nouvelle Unité comptage</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="was-validated" method="POST"
                                            action="{{ route('store.produit_unite_comptage') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="validationTextarea" class="form-label fw-bold">Code</label>
                                                <input name="code" type="text" class="form-control"
                                                    aria-label="file example" required>
                                                <div class="invalid-feedback">Le code est obligatoire</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="validationTextarea" class="form-label fw-bold">Libellé</label>
                                                <input name="libelle" type="text" class="form-control"
                                                    aria-label="file example" required>
                                                <div class="invalid-feedback">Le libellé est obligatoire</div>
                                            </div>
                                            <div class="mb-3">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Fermer</button>
                                                <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section> --}}
