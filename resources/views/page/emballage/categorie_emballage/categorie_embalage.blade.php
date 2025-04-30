@extends('layouts.master', ['title' => 'Emballage'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Catégorie Emballage',
        'infos2' => 'Catégorie Emballage',
        'infos3' => 'Liste',
    ])

    @canany(['creer-categorie-emballage', 'modifier-categorie-emballage'])
        <div class="row d-flex text-start p-3">
            <div class="col text-end">
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        @can('creer-categorie-emballage')
                            <li>
                                <a type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#creeCatEmballage">Nouveau</a>
                            </li>
                        @endcan
                        @can('modifier-categorie-emballage')
                            <li>
                                <a type="button" class="dropdown-item" id="modifierCatEmballageButton">Modifier</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>
    @endcanany

    <div class="card m-b-30" wire:ignore>
        <div class="card-header" style="{{ background_color_2() }}">
            <h3 class="mt-2 d-inline-block text-dark">Liste des catégories emballages</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="magasinsTable" class="datatable table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nom catégorie</th>
                            <th>Enregistrer par</th>
                            <th>Modifier par</th>
                            <th style="width: 5%">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listeCatEmballage as $key => $CatEmballage)
                            <tr class="clickable-row" data-id="{{ $CatEmballage->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="magasin-nom">{{ $CatEmballage->Libelle }}</td>
                                <td class="magasin-nom">{{ $CatEmballage->Enregistrer_par }}</td>
                                <td class="magasin-nom">{{ $CatEmballage->Modifier_par }}</td>
                                <td>
                                    @if ($CatEmballage->Statut_cat_emballage == 1)
                                        <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-check-circle text-danger" aria-hidden="true"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Créer -->
    <div class="modal fade" id="creeCatEmballage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="creeCatEmballageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="creeEmballageForm" action="{{ route('storeCatEmballage') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="creeCatEmballageLabel">Nouvelle catégorie emballage</h1>
                        <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="creeLibelle" class="form-label fw-bold">Catégorie emballage <span class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="number" name="Libelle" class="form-control" required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="creeStatut" class="form-label fw-bold">Statut <span class="fs-5 text-danger mb-2">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_cat_emballage" value="1" required id="creeActif">
                                    <label class="form-check-label" for="creeActif">Actif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_cat_emballage" value="0" id="creeInactif">
                                    <label class="form-check-label" for="creeInactif">Inactif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifier -->
    <div class="modal fade" id="modifierCatEmballage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modifierCatEmballageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="modifierEmballageForm" action="{{ route('updateCatEmballage') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modifierCatEmballageLabel">Modifier catégorie emballage</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="categorieId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modifierLibelle" class="form-label fw-bold">Catégorie emballage <span class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="number" name="Libelle" id="modifierLibelle" class="form-control" required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="modifierStatut" class="form-label fw-bold">Statut <span class="fs-5 text-danger mb-2">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_cat_emballage" value="1" required id="modifierActif">
                                    <label class="form-check-label" for="modifierActif">Actif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_cat_emballage" value="0" id="modifierInactif">
                                    <label class="form-check-label" for="modifierInactif">Inactif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Lorsque l'utilisateur clique sur une ligne de la table
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            // Lorsque l'utilisateur clique sur le bouton Modifier
            $('#modifierCatEmballageButton').on('click', function() {
                // Récupérer l'ID de la catégorie sélectionnée
                var selectedRow = $('.clickable-row.selected');
                if (selectedRow.length > 0) {
                    var categorieId = selectedRow.data('id');
                    console.log(categorieId);
                    var categorieLibelle = selectedRow.find('td').eq(1).text().trim();
                    var categorieStatut = selectedRow.find('td').eq(4).find('i').hasClass('text-success') ? 1 : 0;

                    // Remplir les champs de la modal avec les données de la catégorie sélectionnée
                    $('#categorieId').val(categorieId);
                    $('#modifierLibelle').val(categorieLibelle);
                    if (categorieStatut === 1) {
                        $('#modifierActif').prop('checked', true);
                    } else {
                        $('#modifierInactif').prop('checked', true);
                    }

                    // Afficher la modal de modification
                    $('#modifierCatEmballage').modal('show');
                } else {
                    alert('Veuillez sélectionner une catégorie à modifier.');
                }
            });
        });
    </script>

    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            color: white;
        }
    </style>

    @include('layouts.alert')
@endsection
