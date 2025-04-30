@extends('layouts.master', ['title' => 'Categorie Produit'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Groupe Categorie',
        'infos2' => 'Groupe Categorie',
        'infos3' => 'Liste',
    ])

    <section>
        @canany(['creer-groupe-categorie-produit', 'modifier-groupe-categorie-produit'])
            <div class="row">
                @can('creer-groupe-categorie-produit')
                    <div class="col-md-{{ Auth::user()->cannot('modifier-groupe-categorie-produit') ? 12 : 4 }}">

                        <div class="card ">
                            <div class="card-body">
                                <h5 class="card-title">Création de Groupe Categorie </h5>
                                <div class="d-flex justify-content-between align-items-center my-table-wrapper gap-5">
                                    <form id="storeGroupeCategorie" action="{{ route('storeGroupeCategorie') }}" method="POST"
                                        class=" align-items-center gap-4">
                                        @csrf
                                        <div>

                                            <label for="libelle" class="label-form">Nom Groupe Categorie&nbsp;</label>
                                        </div>
                                        <div class="d-flex justify-content-between ">
                                            <div class="d-flex form-group">

                                                <input type="text" class="form-control" name="libelle" id="libelle">
                                            </div>
                                            <div class="d-flex form-group">
                                                <button type="button" class="btn btn-primary btn-sm ml-5" data-bs-toggle="modal"
                                                    data-bs-target="#staticBackdropAjouter">Ajouter</button>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="staticBackdropAjouter" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de
                                                            confirmation
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Voulez-vous vraiment ajouter le groupe ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn text-white" id="save-button"
                                                            style="{{ background_color_2() }}" data-bs-dismiss="modal"> Oui
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>


                    </div>
                @endcan

                @can('modifier-groupe-categorie-produit')
                    <div class="col-md-{{ Auth::user()->cannot('creer-groupe-categorie-produit') ? 12 : 8 }}">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Modification de Groupe Categorie </h5>
                                <div class=" gap-5">
                                    <form action="{{ route('updateGroupeCategorie') }}" method="POST" class=" gap-4">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="groupe_categorie_ids" class="label-form">Groupe&nbsp;</label>
                                                <select name="groupe_categorie_ids" class="js-single "
                                                    style="width: 100%; padding: 10px" id="groupe_categorie_ids" required>
                                                    <option value=""></option>
                                                    @foreach ($listeGroupeCat as $groupeCat)
                                                        <option value="{{ $groupeCat->id }}">{{ $groupeCat->libelle }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="categorie_produit_id" class="label-form">Nouveau Nom&nbsp;</label>
                                                <input type="text" class="form-control" name="libelle" id="libelle" required>

                                            </div>

                                            <div class="col-md-4 form-group mt-4">
                                                <button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#staticBackdropModifier" class="btn  btn-sm"
                                                    style="{{ background_color_2() }}" style="">Modifier</button>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="staticBackdropModifier" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de
                                                            confirmation
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Voulez-vous vraiment modifier le groupe ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Non</button>
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_2() }}" data-bs-dismiss="modal"> Oui
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                @endcan
            </div>
        @endcanany
        <br>

        @can('associer-categorie-et-groupe-categorie-produit')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Associer d'une catégorie à un Groupe Categorie </h5>
                    <div class=" gap-5">
                        <form id="form2" action="{{ route('storeAssociationGroupeCategorieProduit') }}" method="POST" class=" gap-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="groupe_categorie_id" class="label-form">Groupe:&nbsp;</label>
                                    <select name="groupe_categorie_id" class="js-single" style="width: 100%"
                                        id="groupe_categorie_id" required>
                                        <option value=""></option>
                                        @foreach ($listeGroupeCat as $groupeCat)
                                            <option value="{{ $groupeCat->id }}">{{ $groupeCat->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="categorie_produit_id" class="label-form">Catégorie Produit:&nbsp;</label>
                                    <select name="categorie_produit_id[]" class="js-single" style="width: 100%"
                                        id="categorie_produit_id" multiple required>
                                        <option value=""></option>
                                        @foreach ($listeCategorieNonLiee as $categorieProduit)
                                            <option value="{{ $categorieProduit->id }}">{{ $categorieProduit->Libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div>
                                <button type="submit" id="saveButton" class="btn btn-primary btn-sm"
                                    style="margin-top: -19px">Associer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

    </section>
    <section>
        <div class="row">
            <div
                class="col-12 mb-5 {{ Auth::user()->can('associer-categorie-et-groupe-categorie-produit') ? ' mt-3' : '' }}">

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2 d-inline-block text-dark">Liste des associations</h3>
                    </div>
                    <div class="card-body">
                        <form id="deleteSelectedForm" action="{{ route('deleteSelectedAssociations') }}" method="POST">
                            @csrf
                        @can('revoquer-associtaion-categorie-et-groupe-categorie-produit')
                                <div class="d-flex justify-content-end mb-3">
                                    <div>
                                        <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop">Révoquer les associations sélectionnés</button>

                                    </div>

                                </div>

                        @endcan

                        <div class="table-responsive">
                            <table id="magasinsTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Groupe Categorie</th>
                                        <th>Catégorie Produit</th>
                                        <th>Cocher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listeAssociation as $key => $association)
                                        <tr data-row-id="{{ $association->id }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $association->libelle_groupe }}</td>
                                            <td>{{ $association->libelle_cat_produit }}</td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input checkbox-row" type="checkbox"
                                                        name="ids[]" value="{{ $association->id }}"
                                                        id="flexCheckChecked{{ $association->id }}">
                                                    <label class="form-check-label"
                                                        for="flexCheckChecked{{ $association->id }}">
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment révoquer les associations sélectionnées ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Non</button>
                                        <button type="submit" class="btn text-white" style="{{ background_color_2() }}"
                                            data-bs-dismiss="modal"> Oui Révoquer
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>



    {{--
<script>
    $(document).ready(function() {
        $('#magasinsTable').on('click', '.clickable-row', function() {
            var ID = $(this).attr('data-id');
            $('#modifierLink').attr('wire:click.prevent', "editCategorie(" + ID + ")");
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.clickable-row').on('click', function() {
            // Supprimez la classe 'selected' de toutes les lignes
            $('.clickable-row').removeClass('selected');
            // Ajoutez la classe 'selected' à la ligne cliquée
            $(this).addClass('selected');
        });
    });
</script> --}}

    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la background_color_1 de votre choix */
            color: white;
            /* background_color_1 du texte sur fond bleu */
        }
    </style>

    <script>


        const formIds = ['storeGroupeCategorie', 'form2', 'deleteSelectedForm'];

        // Fonction pour empêcher la soumission du formulaire à l'aide de la touche "Entrée"
        function preventEnterSubmission(formId) {
            document.getElementById(formId).addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }

        // Appliquer la fonction à chaque formulaire de la liste
        formIds.forEach(preventEnterSubmission);
    </script>
    <script>
        $('#storeGroupeCategorie').on('submit', function(e) {

                   var $button = $('#save-button');
                      $button.addClass('loading');
                      $button.prop('disabled', true);

          });

          $('#form2').on('submit', function(e) {

            var $button = $('#saveButton');
            $button.addClass('loading');
            $button.prop('disabled', true);

            });
  </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.checkbox-row');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        console.log('Checkbox checked:', this);
                        row.classList.add('selected-row');
                    } else {
                        console.log('Checkbox unchecked:', this);
                        row.classList.remove('selected-row');
                    }
                });
            });
        });
    </script>

    <style>
        .selected-row>td {
            background-color: rgb(29, 9, 101) !important;
            color: white;
        }
    </style>

    @include('layouts.alert')
@endsection
