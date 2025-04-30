@extends('layouts.master', ['title' => 'Emballage'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Emballage',
        'infos2' => 'Emballage',
        'infos3' => 'Liste',
    ])
    @canany(['creer-emballage', 'modifier-emballage', 'importer-liste-emballage'])
        <div class="row d-flex text-start p-3">
            <div class="col text-end">
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        @can('creer-emballage')
                            <li>
                                <a type="button" class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#creeCatEmballage">Nouveau</a>
                            </li>
                        @endcan
                        @can('modifier-emballage')
                            <li>
                                <a type="button" class="dropdown-item" id="modifierEmballageButton">Modifier</a>
                            </li>
                        @endcan
                        @can('importer-liste-emballage')
                            <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#importerStock"
                                    value="exporter">Importer Excel</button>
                            </li>
                        @endcan


                    </ul>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importerStock" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel">Importer le stock des
                            produits</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formimport" action="{{ route('importer_emballage') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="excelFile" class="form-label text-start">Sélectionner le fichier
                                    Excel</label>
                                <input type="file" name="file" class="form-control" id="excelFile" accept=".xlsx, .xls"
                                    required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="confirmimport" class="btn btn-primary">Importer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcanany

    <!-- Modal Créer -->
    <div class="modal fade" id="creeCatEmballage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="creeCatEmballageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="creeEmballageForm" action="{{ route('storeEmballage') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="creeCatEmballageLabel">Nouvelle emballage</h1>
                        <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="creeLibelle" class="form-label fw-bold">Réference emballage <span
                                            class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="text" name="Reference" class="form-control" required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="creeLibelle" class="form-label fw-bold">Nom emballage <span
                                            class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="text" name="Nom_emballage" class="form-control" required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="categorie">Catégorie emballage <span
                                        class="fs-5 text-danger mb-2">*</span></label>
                                <select name="Categorie_emballage_id" type="text" class="form-select" id="categorie"
                                    required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @foreach ($listeCatEmballage as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="categorie">Type emballage <span
                                        class="fs-5 text-danger mb-2">*</span></label>
                                <select name="type_emb" type="text" class="form-select" id="type_emb" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="recuperable">Recupérable</option>
                                    <option value="non_recuperable">Non Recupérable</option>
                                </select>
                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                            </div>
                            <div class="col-md-6">
                                <label for="creeStatut" class="form-label fw-bold">Statut <span
                                        class="fs-5 text-danger mb-2">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_emballage"
                                        value="1" required id="creeActif">
                                    <label class="form-check-label" for="creeActif">Actif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_emballage"
                                        value="0" id="creeInactif">
                                    <label class="form-check-label" for="creeInactif">Inactif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Modifier -->
    <div class="modal fade" id="modifierEmballage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modifierCatEmballageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="modifierEmballageForm" action="{{ route('updateEmballage') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modifierCatEmballageLabel">Modifier emballage</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="emballageId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modifierReference" class="form-label fw-bold">Réference emballage <span
                                            class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="text" name="Reference" id="modifierReference" class="form-control"
                                        required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modifierLibelle" class="form-label fw-bold">Nom emballage <span
                                            class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="text" name="Nom_emballage" id="modifierLibelle" class="form-control"
                                        required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="categorie">Catégorie emballage</label>
                                <select name="Categorie_emballage_id" type="text" class="form-select"
                                    id="modifierIdCategorie">
                                    <option value=""></option>
                                    @foreach ($listeCatEmballage as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="categorie">Type emballage <span
                                        class="fs-5 text-danger mb-2">*</span></label>
                                <select name="type_emb" type="text" class="form-select" id="modifierTypeEmb"
                                    required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="recuperable">Recupérable</option>
                                    <option value="non_recuperable">Non Recupérable</option>
                                </select>
                                <div class="invalid-feedback">La catégorie est obligatoire</div>
                            </div>
                            <div class="col-md-6">
                                <label for="modifierStatut" class="form-label fw-bold">Statut <span
                                        class="fs-5 text-danger mb-2">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_emballage"
                                        value="1" required id="modifierActif">
                                    <label class="form-check-label" for="modifierActif">Actif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="Statut_emballage"
                                        value="0" id="modifierInactif">
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




    <div class="card m-b-30" wire:ignore>
        <div class="card-header" style="{{ background_color_2() }}">
            <h3 class="mt-2 d-inline-block text-dark">Liste des emballages</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="magasinsTable" class="datatable table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Réfrence emballage</th>
                            <th>Nom emballage</th>
                            <th>Catégorie emballage</th>
                            <th>Type emballage</th>
                            <th>Enregistrer par</th>
                            <th>Modifier par</th>
                            <th style="width: 5%">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listeEmballage as $key => $Emballage)
                            <tr class="clickable-row" data-id="{{ $Emballage->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="magasin-nom">{{ $Emballage->Reference }}</td>
                                <td class="magasin-nom">{{ $Emballage->Nom_emballage }}</td>
                                <td class="magasin-nom">{{ $Emballage->Categorie_emballage }}</td>
                                <td class="magasin-nom">{{ $Emballage->type_emb }}</td>
                                <td class="magasin-nom">{{ $Emballage->Enregistrer_par }}</td>
                                <td class="magasin-nom">{{ $Emballage->Modifier_par }}</td>
                                <td hidden class="magasin-nom">{{ $Emballage->Categorie_emballage_id }}</td>
                                <td>
                                    @if ($Emballage->Statut_emballage == 1)
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


    <script>
        $(document).ready(function() {
            // Lorsque l'utilisateur clique sur une ligne de la table
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            $('#modifierEmballageButton').on('click', function() {
                // Récupérer l'ID de la catégorie sélectionnée
                var selectedRow = $('.clickable-row.selected');
                if (selectedRow.length > 0) {
                    var emballageId = selectedRow.data('id');
                    console.log('tytytr', emballageId);
                    var referenceEmballage = selectedRow.find('td').eq(1).text().trim();
                    var nomEmballage = selectedRow.find('td').eq(2).text().trim();
                    var idCategorie = selectedRow.find('td').eq(7).text().trim();
                    var typeEmb = selectedRow.find('td').eq(4).text().trim();
                    var categorieStatut = selectedRow.find('td').eq(8).find('i').hasClass('text-success') ?
                        1 : 0;

                    console.log('idCategorie', idCategorie);

                    // Remplir les champs de la modal avec les données de la catégorie sélectionnée
                    $('#emballageId').val(emballageId);
                    $('#modifierLibelle').val(nomEmballage);
                    $('#modifierReference').val(referenceEmballage);
                    if ($('#modifierIdCategorie option[value="' + idCategorie + '"]').length) {
                        $('#modifierIdCategorie').val(idCategorie);
                    } else {
                        $('#modifierIdCategorie').val(''); // Réinitialise si la valeur n'est pas valide
                    }

                    $('#modifierTypeEmb').val(typeEmb);

                    if (typeEmb == 'recuperable') {
                        $('#modifierTypeEmbRecuperable').prop('checked', true);
                    } else {
                        $('#modifierTypeEmbNonRecuperable').prop('checked', true);
                    }

                    if (categorieStatut === 1) {
                        $('#modifierActif').prop('checked', true);
                    } else {
                        $('#modifierInactif').prop('checked', true);
                    }

                    // Afficher la modal de modification
                    $('#modifierEmballage').modal('show');
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
