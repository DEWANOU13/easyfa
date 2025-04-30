@extends('layouts.master', ['title' => 'Inventaire'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Inventaires',
        'infos2' => 'Inventaires',
        'infos3' => 'Liste',
    ])

    <div class="row d-flex text-start p-3">
        <div class="col text-end2">
            @canany(['effectuer-inventaire', 'boucler-inventaire'])
                <div class="dropdown">
                    <button class="btn text-white dropdown-toggle pull-right mb-2 mt-2" style="{{ background_color_1() }}"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('effectuer-inventaire')
                            <li><a href="{{ route('page.inventaire.inventaire_nouveau') }}" class="dropdown-item"
                                    type="button">Nouveau</a></li>
                        @endcan
                        @can('boucler-inventaire')
                            <li><button id="modifierBtn" class="dropdown-item" type="submit">Boucler</button></li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Liste</button>
                        @canany(['exporter-impression-inventaire-produit', 'imprimer-impression-inventaire-produit'])

                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Impression</button>
                        @endcanany
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                             action="{{ route('filterInventaire') }}" method="GET">
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                    <label class="m-1" for="annee">Année</label>
                                    <select name="annee" id="anneeSelect" class="form-select">
                                        <option value="">Sélectionner une année</option>
                                        @foreach ($annees as $annee)
                                            <option value="{{ $annee }}">{{ $annee }}</option>
                                        @endforeach
                                    </select>

                                </div>


                                <div class="col-md-12 mb-3">
                                    <select class="form-select" id="monthSelect" size="12" name="month"
                                        aria-label="Size 3 select example">
                                        <option value="">{{ __('Sélectionnez un mois') }}</option>
                                        @php
                                            $months = [
                                                1 => 'Janvier',
                                                2 => 'Février',
                                                3 => 'Mars',
                                                4 => 'Avril',
                                                5 => 'Mai',
                                                6 => 'Juin',
                                                7 => 'Juillet',
                                                8 => 'Août',
                                                9 => 'Septembre',
                                                10 => 'Octobre',
                                                11 => 'Novembre',
                                                12 => 'Décembre',
                                            ];
                                        @endphp
                                        @foreach ($months as $key => $month)
                                            <option value="{{ $key }}"
                                                @if (request()->query('month') == $key) selected style="color: blue;" @endif>
                                                {{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="col-md-10">
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des inventaires</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            id="proformaTable"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="" scope="col">#
                                                    </th>
                                                    <th style="" scope="col">
                                                        Agence</th>
                                                    <th style="" scope="col">Date
                                                    </th>
                                                    <th style="" scope="col">
                                                        Référence</th>
                                                    <th style="" scope="col">
                                                        Statut</th>
                                                    <th style="" scope="col">
                                                        Observations</th>
                                                    <th style="" scope="col">
                                                        Enregistré par</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($inventaire_produits as $inventaire_produit)
                                                    <tr style="cursor:pointer" class="clickable-row" id="2"
                                                        data-url="{{ route('get.inventaire_magasin_for_inventaire_produit', ['id' => $inventaire_produit->id]) }}">
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    for="2" name="flexRadioDefault"
                                                                    id="flexRadioDefault2"
                                                                    value="{{ $inventaire_produit->id }}">
                                                                <label class="form-check-label" for="flexRadioDefault2">
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td class="entree-produit">{{ $inventaire_produit->NomAgence }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ \Carbon\Carbon::parse($inventaire_produit->Date_Inventaire)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $inventaire_produit->Reference_Inventaire }}</td>
                                                        <td class="entree-produit">
                                                            {{ $inventaire_produit->Statut_Inventaire }}</td>
                                                        <td class="entree-produit">{{ $inventaire_produit->Observations }}
                                                        </td>
                                                        <td class="entree-produit">{{ $inventaire_produit->name }}</td>
                                                        {{-- <td class="entree-produit"><a  class="btn btn-danger" href="">btn</a></td> --}}
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">
                                                            <h6 class=" mt-3 text-center">Aucune donnée pour l'instant.
                                                            </h6>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <div class="table-responsive responsive-1">
                                        <div class="col-md-12">
                                            <table class="tableInfo tableInfo2" id="magasintable"
                                                class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th style=" " scope="col">
                                                            Magasin</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($inventaire_magasins as $inventaire_magasin)
                                                        <tr style="cursor:pointer">
                                                            <td class="inventaire-magasin">
                                                                {{ $inventaire_magasin->NomMagasin . '|' . $inventaire_magasin->NomAgence }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="1">
                                                                <h6 class=" mt-3 text-center">Aucune donnée pour l'instant.
                                                                </h6>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <form class="row gx-3 gy-2 my-2 d-flex">
                                        {{-- <div class="col-sm-12"> --}}
                                        <div class="col-sm-4">
                                            <div class="input-group input-group-sm mb-3">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Inventaire</span>
                                                <select style="width: 100%" name="magasin" type="text"
                                                    class="form-select js-single" id="searchInput"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">-----Sélectionnez un magasin----
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group input-group-sm mb-3">
                                                <span class="input-group-text" id="inputGroup-sizing-sm_cp">Catégorie
                                                    produit</span>
                                                <select style="width: 100%" name="categorie_produit" type="text"
                                                    class="form-select js-single" id="searchInputCategorie"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">-----Sélectionnez une catégorie----
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group input-group-sm mb-3">
                                                <span class="input-group-text" id="inputGroup-sizing-sm_p">
                                                    Produit</span>
                                                <select style="width: 100%" name="produit" type="text"
                                                    class="form-select js-single" id="selectProduit"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">-----Sélectionnez une catégorie----
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- </div> --}}
                                    </form>
                                    {{-- <div class="row col-md-12"> --}}
                                    {{-- </div> --}}
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Détails de l'inventaire</h3>
                                </div>
                                <div class="card-body">
                                    <form id="formcreate" action="{{ route('modifier_inventorier') }}" method="post">
                                        <div class="table-responsive responsive-2">
                                            <div class="col-md-12">
                                                @csrf
                                                <table class="tableInfo dataTable tableInfo-green tableInfo2"
                                                    id="produitTable">
                                                    <thead class="table-primary"
                                                        style="position: sticky;top: 0;z-index: 1000;">
                                                        <tr>
                                                            <th hidden style=" " scope="col">
                                                            </th>
                                                            <th style=" " scope="col">
                                                                Référence produit</th>
                                                            <th style=" " scope="col">
                                                                Désignation</th>
                                                            <th style=" " scope="col">
                                                                Catégorie</th>
                                                            <th style=" " scope="col">
                                                                Unité de compatge</th>
                                                            <th style=" " scope="col">
                                                                Magasin</th>
                                                            <th style=" " scope="col">
                                                                Qté
                                                                initiale</th>
                                                            <th style=" " scope="col">
                                                                Qté
                                                                comptée</th>
                                                            <th style=" " scope="col">
                                                                Ecart</th>
                                                            <th style=" " scope="col">
                                                                Justificatif</th>
                                                            <th style=" " scope="col">
                                                                Qté
                                                                justifiée</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 0; // Initialiser le compteur
                                                        @endphp
                                                        @forelse($inventoriers as $inventorier)
                                                            <tr>
                                                                <td hidden><input name="inputs[{{ $index }}][id]"
                                                                        id="id" class="form-control border-0"
                                                                        readonly type="hidden"
                                                                        value="{{ $inventorier->id }}"></td>
                                                                <td><input style="width: 300px" name="inputs[{{ $index }}][produit]"
                                                                        id="produit" class="form-control border-0"
                                                                        readonly type="text"
                                                                        value="{{ $inventorier->Reference }}">
                                                                </td>
                                                                <td><input style="width: 300px" name="inputs[{{ $index }}][designation]"
                                                                        id="designation" class="form-control border-0"
                                                                        readonly type="text"
                                                                        value="{{ $inventorier->Designation }}">
                                                                </td>
                                                                <td><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][categorie_produit]"
                                                                        id="categorie_produit"
                                                                        class="form-control border-0" readonly
                                                                        type="text"
                                                                        value="{{ $inventorier->Libelle }}"></td>
                                                                <td><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][unite_compatge]"
                                                                        id="unite_compatge" class="form-control border-0"
                                                                        readonly type="text"
                                                                        value="{{ $inventorier->Libelle_Comptage }}"></td>
                                                                <td class="produit"><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][magasin]"
                                                                        id="magasin" class="form-control border-0"
                                                                        readonly type="text"
                                                                        value="{{ $inventorier->NomMagasin }}">
                                                                </td>
                                                                {{-- @if ($inventorier->Qte_Initiale === 0.0) --}}
                                                                <td><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][quantite_initiale]"
                                                                        id="quantite_initiale"
                                                                        class="form-control border-0" readonly
                                                                        type="number"
                                                                        value="{{ $inventorier->Qte_Initiale }}"></td>
                                                                {{-- @endif --}}

                                                                {{-- <td><input
                                                                        name="inputs[{{ $index }}][quantite_initiale]"
                                                                        id="quantite_initiale"
                                                                        class="form-control border-0" readonly
                                                                        type="number"
                                                                        value="{{ $inventorier->Qte_Initiale }}"></td> --}}
                                                                <td><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][quantite_comptee]"
                                                                        id="quantite_comptee"
                                                                        class="form-control border-0" type="number"
                                                                        value="{{ $inventorier->Qte_Comptee }}">
                                                                </td>
                                                                <td><input style="width: 300px" name="inputs[{{ $index }}][ecart]"
                                                                        id="ecart" class="form-control border-0"
                                                                        readonly type="number"
                                                                        value="{{ $inventorier->Qte_Ecart }}">
                                                                </td>
                                                                <td><input style="width: 300px"
                                                                        name="inputs[{{ $index }}][justificatif]"
                                                                        id="justificatif" class="form-control border-0"
                                                                        type="text"
                                                                        value="{{ $inventorier->Justificatif }}">
                                                                </td>
                                                                <td><input style="width: 300px" name="inputs[{{ $index }}][justifiee]"
                                                                        id="justifiee" class="form-control border-0"
                                                                        readonly type="text"
                                                                        value="{{ $inventorier->Qte_Ajustee }}">
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $index++; // Incrémenter le compteur
                                                            @endphp
                                                        @empty
                                                            <tr>
                                                                <td colspan="10">
                                                                    <h6 class=" mt-3 text-center">Aucune donnée pour
                                                                        l'instant.
                                                                    </h6>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                                <!-- Modal -->
                                                <div class="modal fade" id="inventorierModal" data-bs-backdrop="static"
                                                    data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                    Confirmation</h1>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Souhaitez-vous vraiment effectué cette action?
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


                                                <div id="noResultsMessage" class="alert alert-info"
                                                    style="display: none;">
                                                    Aucun résultat trouvé pour la recherche.
                                                </div>
                                            </div>
                                        </div>
                                        <button id="validerButton" data-bs-toggle="modal"
                                            data-bs-target="#inventorierModal" type="button"
                                            class="btn text-white float-end w-auto mt-3"
                                            style="{{ background_color_1() }}">VALIDER</button>
                                    </form>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="row mt-4 d-flex justify-content-center">
                        <div class="col-md-8 mb-5">
                            <h3 class="fw-bold">Que voulez-vous imprimer?</h3>
                            <form class="row gx-3 gy-2" action="{{ route('impimer-inventaire') }}" target="_blank"
                                method="POST">
                                @csrf
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="1"
                                            value="fiche_stock">
                                        <label class="form-check-label" for="1">
                                            Les fichiers de stock
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="2"
                                            value="fiche_comptage">
                                        <label class="form-check-label" for="2">
                                            Les fichiers de comptage
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="3"
                                            value="ecart_stock">
                                        <label class="form-check-label" for="3">
                                            Les écarts de stock
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="4"
                                            value="valorisation_ecart">
                                        <label class="form-check-label" for="4">
                                            La valorisation des écarts
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="5"
                                            value="bilan_inventaire">
                                        <label class="form-check-label" for="5">
                                            Le bilan de l'inventaire
                                        </label>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reponse" id="6"
                                            value="valorisation_detail_inventaire">
                                        <label class="form-check-label" for="6">
                                            La valorisation de détaillée de l'inventaire
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label for="inputEmail3" class="col-sm-2 col-form-label">Inventaire</label>
                                        <div class="col-sm-6">
                                            <select type="text" name="reference_inventaire" class="form-select"
                                                id="inputEmail3">
                                                <option value="">-----Selectionnez----</option>
                                                @foreach ($inventaire_produits as $inventaire_produit)
                                                    <option value="{{ $inventaire_produit->id }}">
                                                        {{ $inventaire_produit->Reference_Inventaire }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="d-grid gap-2 d-md-block">
                                        @can('imprimer-impression-inventaire-produit')
                                        <button type="submit" name="submit" value="PDF" class="btn text-white "
                                        style="{{ background_color_2() }}">Imprimer PDF</button>
                                        @endcan

                                        @can('exporter-impression-inventaire-produit')
                                        <button type="submit" name="submit" value="EXCEL" class="btn text-white "
                                        style="{{ background_color_1() }}">Exporter EXCEL</button>
                                        @endcan
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <style>
                    .selected {
                        background-color: rgb(29, 9, 101);
                        /* Ou la couleur de votre choix */
                        color: white;
                        /* Couleur du texte sur fond bleu */
                    }

                    .tableInfo {
                        border-collapse: collapse;
                        width: 100%;
                        border: 1px solid #ddd;
                    }

                    .tableInfo-green {
                        border-collapse: collapse;
                        width: 150%;
                        border: 1px solid #ddd;
                    }

                    .tableInfo th,
                    .tableInfo td {
                        /*             border: 1px solid #ddd;*/
                        padding: 4px;
                        text-align: left;
                        border: 1px solid #ddd;

                    }

                    .selected>td {
                        background-color: rgb(29, 9, 101);
                        /* Ou la couleur de votre choix */
                        color: white;
                        /* Couleur du texte sur fond bleu */
                    }

                    .disabled-input {
                        background-color: #e9ecef;
                        /* Couleur grisée */
                        cursor: not-allowed;
                        /* Changer le curseur pour indiquer que c'est désactivé */
                        opacity: 0.65;
                        /* Rendre l'input visuellement désactivé */
                    }
                </style>
            </div>
        </div>
    </div>

    @include('layouts.alert')
    <script>
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
    </script>
           <script>
            document.getElementById('monthSelect').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('anneeSelect').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        </script>
    <script>

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
    </script>
@endsection
