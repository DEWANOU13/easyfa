@extends('layouts.master', ['title' => 'Stock-Emballage'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Stock Emballage',
        'infos2' => 'Stock Emballage',
        'infos3' => 'Liste',
    ])


    <div class="row mt-5">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link @if (!$tabToShow) active @endif" id="nav-home-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                        aria-selected="true">Stock</button>

                    @can('historique-stock-emballage')
                        <button class="nav-link @if ($tabToShow === 'historique_stock') active @endif" id="nav-profile-tab"
                            data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                            aria-controls="nav-profile" aria-selected="false">Historique</button>
                    @endcan

                    @can('historique-mise-a-jour-stock-emballage')
                        <button class="nav-link @if ($tabToShow === 'historique_import_stock') active @endif" id="nav-profile1-tab"
                            data-bs-toggle="tab" data-bs-target="#nav-profile1" type="button" role="tab"
                            aria-controls="nav-profile1" aria-selected="false">Historique Mise a jour Import Stock</button>
                    @endcan


                </div>
            </nav><br>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade @if (!$tabToShow) show active @endif" id="nav-home"
                    role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="row d-flex text-end">

                    </div>
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <form class="row d-flex align-items-center justify-content-center " class="clickable-row"
                                    action="{{ route('filter_stock_emballage') }}" method="POST">
                                    @csrf
                                    <div class="col-sm-3">
                                        <label for="" class="form-label fw-bold">Magasin</label>
                                        <div class="input-group input-group-default">
                                            {{-- <span class="input-group-text" id="inputGroup-sizing-sm"></span> --}}
                                            <select name="magasin" type="text" class="form-select js-single"
                                                id="searchInput1" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                @if ($mg)
                                                    <option value="" disabled selected>
                                                        {{ $mg->NomMagasin }}( {{ $mg->NomAgence }})
                                                    </option>
                                                @else
                                                    <option value="">Selectionnez une agence</option>
                                                @endif
                                                @foreach ($magasin as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->NomMagasin }}( {{ $value->NomAgence }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="searchInput2" class="form-label fw-bold">Catégorie</label>
                                        <div class="input-group input-group-default">
                                            <select name="categorie" class="form-select js-single" id="searchInput2">
                                                <option value="">Sélectionnez une catégorie</option>
                                                <option value="Toutes">Toutes</option>
                                                @foreach ($categorie as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->Libelle }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="searchInput3" class="form-label fw-bold">Emballage</label>
                                        <div class="input-group input-group-default">
                                            <select name="produit" class="form-select js-single" id="searchInput3">
                                                <option value="">Sélectionnez un enballage</option>
                                                <option value="Tous">Tous</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-auto mt-4">
                                        <div class="form-check">
                                            <div class="input-group input-group-default">
                                                <input name="valoriser_stock" class="form-check-input" type="checkbox"
                                                    id="valoriser_stock">
                                                <label class="form-check-label ms-2" for="valoriser_stock">
                                                    Valoriser le stock
                                                </label>
                                            </div>
                                            <div class="input-group input-group-default">
                                                <input name="Convertir_en_casier" class="form-check-input" type="checkbox"
                                                    id="Convertir_en_casier">
                                                <label class="form-check-label ms-2" for="Convertir_en_casier">
                                                    Convertir en casier
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto mt-4">
                                        <button type="submit" class="btn text-white"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </form>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('imprimerStockEmballage') }}" method="POST" style="display: contents"
                                target="_blank">
                                @csrf
                                @can('exporter-excel-inventaire-stock-emballage')
                                    <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                        style="{{ background_color_1() }}" data-bs-toggle="modal"
                                        data-bs-target="#exporterExcelModal">Exporter en Excel</button>
                                @endcan


                                <div class="modal fade" id="exporterExcelModal" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="exporterExcelModal"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exporterExcelModal">Demande de
                                                    confirmation </h1>
                                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button> --}}
                                            </div>
                                            <div class="modal-body">
                                                Voulez-vous vraiment exporter les données en Excel ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Non</button>
                                                <button id="exportExcel" type="submit" name="reponse" value="exporter"
                                                    class="btn btn-sm btn-success">Oui, Continuer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                @can('exporter-format-importation-stock-emballage')
                                    <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                        style="{{ background_color_1() }}" data-bs-toggle="modal"
                                        data-bs-target="#exporterExcelModal2">Exporter au format d'importation</button>
                                @endcan

                                <div class="modal fade" id="exporterExcelModal2" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="exporterExcelModal"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exporterExcelModal">Demande de
                                                    confirmation </h1>
                                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button> --}}
                                            </div>
                                            <div class="modal-body">
                                                Voulez-vous vraiment exporter les données au format d'importation ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Non</button>
                                                <button id="formatImportation" type="submit" name="reponse"
                                                    value="formatImportation" class="btn btn-sm btn-success">Oui,
                                                    Continuer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @can('exporter-pdf-inventaire-stock-emballage')
                                    <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                        style="{{ background_color_2() }}" data-bs-toggle="modal"
                                        data-bs-target="#imprimerPDFModal">Imprimer PDF</button>
                                @endcan


                                <div class="modal fade" id="imprimerPDFModal" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="imprimerPDFModal"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="imprimerPDFModal">Demande de
                                                    confirmation </h1>
                                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button> --}}
                                            </div>
                                            <div class="modal-body">
                                                Voulez-vous vraiment exporter les données en PDF ?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Non</button>
                                                <button type="submit" name="reponse" value="imprimer" id="exportExcel"
                                                    class="btn btn-sm btn-success exportExcel">Oui, Continuer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        @can('importer-stock-emballage')
                            <div class="col-md-3">
                                <!-- Import Button -->
                                <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                    style="{{ background_color_1() }}" data-bs-toggle="modal"
                                    data-bs-target="#importerStock">Importer
                                </button>
                                <div class="modal fade" id="importerStock" aria-hidden="true"
                                    aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalToggleLabel">Importer le stock des
                                                    produits</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form id="formimport" action="{{ route('import.stock_emballage') }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="excelFile" class="form-label">Sélectionner le fichier
                                                            Excel</label>
                                                        <input type="file" name="file" class="form-control"
                                                            id="excelFile" accept=".xlsx, .xls">
                                                    </div>
                                                    <divb class="text-danger"> NB: Veuillez verifier la liste et supprimer les
                                                        produits de type prestation, si vous les avez ajouté</divb>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" id="confirmimport"
                                                        class="btn btn-primary">Importer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endcan

                    </div>
                    <div class="card m-b-30" wire:ignore>
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-0 d-inline-block text-dark">Gestion stock emballage</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="magasinsTable"
                                    class="datatable table table-striped table-bordered nowrap magasinsTable"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Référence</th>
                                            <th>Désignation</th>
                                            {{-- <th>Catégorie</th>
                                            <th>Unité de comptage</th> --}}
                                            <th>Agence</th>
                                            <th>Magasin</th>
                                            <th class="text-end">Qté en stock</th>
                                            <th class="text-end casier_col">Casier</th>
                                            <th class="text-end prix-unitaire-col">Prix Unitaire</th>
                                            <th class="text-end montant-col">Montant</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- @if (@isset($stock_emballages)) --}}
                                        @forelse($stock_produits as $stock_produit)
                                            <tr class="clickable-row">
                                                <td class="produit">{{ $stock_produit->Reference }}</td>
                                                <td class="stock">{{ $stock_produit->Designation }}</td>
                                                {{-- <td class="categorie">{{ $stock_produit->Libelle_Categorie }}</td>
                                             <td class="stock">{{ $stock_produit->Libelle_Unite_Comptage }}</td> --}}
                                                <td class="agence">{{ $stock_produit->NomAgence }}</td>
                                                <td class="magasin">{{ $stock_produit->NomMagasin }}</td>
                                                <td class="stock text-end">{{ $stock_produit->Qte_stockee }}</td>
                                                <td class="stock text-end casier_col">
                                                    @php
                                                        // Vérifier si Libelle_Categorie est un nombre et n'est pas vide
                                                        if (
                                                            !empty($stock_produit->Libelle_Categorie) &&
                                                            is_numeric($stock_produit->Libelle_Categorie)
                                                        ) {
                                                            // Calculer le nombre de casiers et de bouteilles
                                                            $nombreCasier =
                                                                $stock_produit->Qte_stockee /
                                                                $stock_produit->Libelle_Categorie;
                                                            $partieEntiere = floor($nombreCasier);
                                                            $partieDecimale = $nombreCasier - $partieEntiere;
                                                            $nombreBouteille =
                                                                $partieDecimale * $stock_produit->Libelle_Categorie;
                                                        } else {
                                                            // Si Libelle_Categorie est vide ou non numérique, définir des valeurs par défaut
                                                            $partieEntiere = 0;
                                                            $nombreBouteille = 0;
                                                        }

                                                    @endphp
                                                    {{ $partieEntiere }} Casier(s) et {{ round($nombreBouteille) }}
                                                    Bouteille(s)
                                                </td>
                                                <td class="stock text-end prix-unitaire-col">
                                                    {{ $stock_produit->Prix_Achat_Net }}
                                                </td>
                                                <td class="stock text-end montant-col">
                                                    {{ $stock_produit->Prix_Achat_Net * $stock_produit->Qte_stockee }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Aucune donnée</td>
                                            </tr>
                                        @endforelse
                                        {{-- @endif --}}

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if ($tabToShow === 'historique_stock') show active @endif " id="nav-profile"
                    role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    <div class="card m-b-30 mb-5">
                        <div class="card-body">

                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <div class="row mt-4 d-flex align-items-center">
                                    <div class="col-md-12">
                                        <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center"
                                            id="formH" class="clickable-row"
                                            action="{{ route('filter_stock_emballage_historique') }}" method="POST">
                                            @csrf
                                            <div class="col-sm-2">
                                                <label for="" class="form-label fw-bold">Debut
                                                    Période</label>
                                                <div class="input-group input-group-default">
                                                    {{-- <span class="input-group-text" id="inputGroup-sizing-sm"></span> --}}
                                                    <input name="date_debut_periode" type="datetime-local"
                                                        class="form-control"
                                                        value="{{ $dp ? $dp : date('Y-m-d\TH:i', strtotime('-5 days')) }}"
                                                        id="date_debut_periode">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="" class="form-label fw-bold">Fin
                                                    Période</label>
                                                <div class="input-group input-group-default">
                                                    {{-- <span class="input-group-text" id="inputGroup-sizing-sm"></span> --}}
                                                    <input name="date_fin_periode" type="datetime-local"
                                                        class="form-control" value="{{ $df ? $df : date('Y-m-d\TH:i') }}"
                                                        id="date_fin_periode">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="" class="form-label fw-bold">Magasin</label>
                                                <div class="input-group input-group-default">
                                                    {{-- <span class="input-group-text"
                                                            id="inputGroup-sizing-sm"></span> --}}
                                                    <select name="magasin" type="text" class="form-select js-single"
                                                        id="magasin" aria-label="Sizing example input"
                                                        aria-describedby="inputGroup-sizing-sm">
                                                        @if ($mg)
                                                            <option value="" disabled selected>
                                                                {{ $mg->NomMagasin }}( {{ $mg->NomAgence }})
                                                            </option>
                                                        @else
                                                            <option value="">Selectionnez un magasin</option>
                                                        @endif
                                                        @foreach ($magasin as $keym => $valuem)
                                                            <option value="{{ $valuem->id }}"
                                                                {{ old('magasin') == $valuem->id ? 'selected' : '' }}>
                                                                {{ $valuem->NomMagasin }}( {{ $valuem->NomAgence }})
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="" class="form-label fw-bold">Emballage</label>
                                                <div class="input-group input-group-default">
                                                    {{-- <span class="input-group-text"
                                                            id="inputGroup-sizing-sm"></span> --}}
                                                    <select name="produit" type="text" class="form-select js-single"
                                                        id="produit" aria-label="Sizing example input"
                                                        aria-describedby="inputGroup-sizing-sm">
                                                        @if ($prod)
                                                            <option value="" disabled selected>
                                                                {{ $prod->Nom_emballage }}
                                                            </option>
                                                        @else
                                                            <option value="">Sélectionnez un emballage</option>
                                                        @endif
                                                        @foreach ($produit as $keyp => $valuep)
                                                            <option value="{{ $valuep->id }}"
                                                                {{ old('produit') == $valuep->id ? 'selected' : '' }}>
                                                                {{ $valuep->Nom_emballage }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="" class="form-label fw-bold">Actions</label>
                                                <div class="input-group input-group-default">
                                                    <div class="btn-group">
                                                        <button type="submit" id="appliquerHistorique"
                                                            class="btn text-white"
                                                            style="{{ background_color_1() }}">Appliquer</button>
                                                        <button type="button"
                                                            class="btn text-white dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            style="{{ background_color_1() }}">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a id="printButton" class="dropdown-item"
                                                                    href="#">Imprimer</a>
                                                                <a id="printButtonExcel" class="dropdown-item"
                                                                    href="#">Exporter en Excel</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card m-b-30" wire:ignore>
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Historique des stocks d'emballage</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="magasinsTable"
                                    class=" table table-striped table-bordered dt-responsive nowrap magasinsTable tableInfo"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Date</th>
                                            <th>Agence</th>
                                            <th>Référence</th>
                                            <th>Entrée</th>
                                            <th>Sortie</th>
                                            <th>Importation stock</th>
                                            <th>Inventaire</th>
                                            <th>Solde</th>
                                            <th>Motif</th>
                                            <th>Emballage</th>
                                            <th>Magasin</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $solde = $solde_initial; // Utiliser le solde initial calculé dans le contrôleur
                                        @endphp


                                        <tr class="bg bg-dark" style="background-color: black">
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;"
                                                class="stock td-historique">
                                                {{ \Carbon\Carbon::parse($date_veille_debut)->format('d/m/Y H:i:s') }}</td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" colspan="6"
                                                class="stock td-historique">SOLDE INITIAL</td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;"
                                                class="stock td-historique">{{ $solde }}</td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;"
                                                class="stock td-historique"></td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;"
                                                class="stock td-historique"></td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;"
                                                class="stock td-historique"></td>
                                        </tr>


                                        @if ($tabToShow)
                                            @php
                                                $solde = $solde_initial; // Initialiser le solde à 0
                                            @endphp
                                            @forelse ($historiques_stock as $item)
                                                <tr class="clickable-row">
                                                    <td class="stock td-historique">
                                                        {{ \Carbon\Carbon::parse($item->Date)->format('d/m/Y H:i:s') }}
                                                    </td>
                                                    <td class="stock td-historique">{{ $item->NomAgence }}</td>
                                                    <td class="stock td-historique">{{ $item->Justificatif }}
                                                    </td>
                                                    <td class="stock td-historique">
                                                        @if ($item->operation === 'ENTREE')
                                                            {{ $item->Quantite }}
                                                            @php
                                                                $solde += $item->Quantite; // Initialiser le solde à 0
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td class="stock td-historique">
                                                        @if ($item->operation === 'SORTIE')
                                                            {{ $item->Quantite }}
                                                            @php
                                                                $solde -= $item->Quantite; // Initialiser le solde à 0
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td class="stock td-historique">
                                                        @if ($item->operation === 'IMPORTATION_STOCK')
                                                            {{ $item->Quantite }}
                                                            @php
                                                                $solde = 0;
                                                                $solde += $item->Quantite; // Initialiser le solde à 0
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td class="stock td-historique">
                                                        @if ($item->operation === 'INVENTAIRE')
                                                            {{ $item->Quantite }}
                                                            @php
                                                                $solde = 0;
                                                                $solde += $item->Quantite; // Initialiser le solde à 0
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td class="stock td-historique">{{ $solde }}</td>
                                                    <td class="stock td-historique">{{ $item->Motif }}</td>
                                                    <td class="stock td-historique">{{ $item->Designation }}</td>
                                                    <td class="stock td-historique">{{ $item->NomMagasin }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <!-- <td colspan="9" class="text-center">Veuillez choisir un produit et une agence ou un magasin pour afficher</td> -->
                                                    <td colspan="11" class="text-center">Aucune donnée disponible</td>
                                                </tr>
                                            @endforelse
                                        @else
                                            <td colspan="11" class="text-center">Veuillez choisir ou moins un produit et
                                                un magasin pour afficher</td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if ($tabToShow === 'historique_import_stock') show active @endif " id="nav-profile1"
                    role="tabpanel" aria-labelledby="nav-profile1-tab" tabindex="0">
                    <div class="card m-b-30 my-3">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des importations de stock</h3>
                        </div>
                        <div class="card-body responsive-2">
                            <div class="table-responsive">
                                <table id="proformaTable"
                                    class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th hidden class="header" style="" scope="col"></th>
                                            <th class="header" style="" scope="col">Date</th>
                                            <th class="header" style="" scope="col">Agence</th>
                                            <th class="header" style="" scope="col">Référence</th>
                                            <th class="header" style="" scope="col">Observations
                                            </th>
                                            <th class="header" style="" scope="col">Enregistré par
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $index = 0;
                                        @endphp
                                        @if (isset($import_stock_emballages))
                                            @forelse($import_stock_emballages as $import_stock_emballage)
                                                <tr style="cursor:pointer" class="clickable-row1"
                                                    data-url="{{ route('get.detail_import_stock_emballage') }}">
                                                    <td hidden class="entree-produit"><input type="hidden"
                                                            name="id" value="{{ $import_stock_emballage->id }}">
                                                    </td>
                                                    <td class="entree-produit">
                                                        {{ \Carbon\Carbon::parse($import_stock_emballage->Date_Entree)->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td class="entree-produit">{{ $import_stock_emballage->NomAgence }}
                                                    </td>
                                                    <td class="entree-produit">
                                                        {{ $import_stock_emballage->Reference_Import_Stock }}</td>
                                                    <td class="entree-produit">{{ $import_stock_emballage->Observations }}
                                                    </td>
                                                    <td class="entree-produit">{{ $import_stock_emballage->name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <h6 class="mt-3 text-center">Aucune donnée pour l'instant.
                                                        </h6>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        @endif

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="card m-b-30 my-3">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Detail des importations de stock</h3>
                        </div>
                        <div class="card-body resp-1">
                            <div class="table-responsive resp-1">
                                <table id="proformaTable"
                                    class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th hidden class="header" style="" scope="col"></th>
                                            <th class="header" style="" scope="col">Référence produit</th>
                                            <th class="header" style="" scope="col">Désignation</th>
                                            <th class="header" style="" scope="col">Qté</th>
                                            <th class="header" style="" scope="col">Prix
                                            </th>
                                            <th class="header" style="" scope="col">Magasin
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $index = 0;
                                        @endphp
                                        @if (isset($details_import_stocks))
                                            @forelse($details_import_stocks as $details_import_stock)
                                                <tr style="cursor:pointer">
                                                    <td class="entree-produit">{{ $details_import_stock->Reference }}
                                                    </td>
                                                    <td class="entree-produit">
                                                        {{ $details_import_stock->Designation }}</td>
                                                    <td class="entree-produit">{{ $details_import_stock->Qte_Importee }}
                                                    </td>
                                                    <td class="entree-produit">{{ $details_import_stock->Prix_Achat }}
                                                    </td>
                                                    <td class="entree-produit">{{ $details_import_stock->NomMagasin }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <h6 class="mt-3 text-center">Aucune donnée pour l'instant.
                                                        </h6>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        @endif

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isset($success))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Importation réussie."
            });
        </script>
    @endif

    <br>
    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la background_color_1 de votre choix */
            color: white;
            /* background_color_1 du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }

        .prix-unitaire-col,
        .montant-col {
            display: none;
        }
    </style>
    @include('layouts.alert')
    <script>
        $(document).ready(function() {
            // Gérer le changement de catégorie
            $('#searchInput2').on('change', function() {
                var selectedCategoryId = $(this).val();

                // Vider le menu déroulant des produits avant de l'actualiser
                $('#searchInput3').empty();
                $('#searchInput3').append('<option value="">Sélectionnez un produit</option>');

                if (selectedCategoryId) {
                    $.ajax({
                        url: '/get-products-by-category-emballage/' +
                            selectedCategoryId, // URL à adapter selon tes routes
                        type: 'GET',
                        success: function(response) {
                            // Ajouter les produits retournés dans le menu déroulant
                            if (response.length > 0) {
                                response.forEach(function(product) {
                                    $('#searchInput3').append('<option value="' +
                                        product.id + '">' + product.Nom_emballage +
                                        '</option>');
                                });
                            } else {
                                $('#searchInput3').append(
                                    '<option value="Tous">Aucun produit disponible</option>'
                                );
                            }
                        },
                        error: function() {
                            alert('Erreur lors du chargement des produits.');
                        }
                    });
                } else {
                    $('#searchInput3').append('<option value="Tous">Tous</option>');
                }
            });
        });



        $(document).ready(function() {
            $('#formimport').on('submit', function() {
                var $button = $('#confirmimport');

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
            $(".clickable-row1").click(function() {
                var url = $(this).data("url");
                var id = $(this).find("input[name='id']").val(); // Récupère l'id caché de la ligne

                // Récupère le token CSRF nécessaire pour les requêtes POST
                var token = $('meta[name="csrf-token"]').attr('content');

                $.post(url, {
                        _token: token, // Ajoute le token CSRF
                        id: id // Envoie l'id du produit
                    })
                    .done(function(data) {
                        var tableContent = $(data).find('.resp-1').html(); // Sélectionne le contenu
                        $(".resp-1").html(tableContent); // Injecte le contenu dans le tableau cible
                    })
                    .fail(function(xhr, status, error) {
                        console.error("Erreur lors de l'import : " + error);
                    });
            });
        });


        $(document).ready(function() {
            $('.clickable-row1').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row1').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });


        $(document).ready(function() {
            const $valoriserCheckbox = $('#valoriser_stock');
            const $prixUnitaireCols = $('.prix-unitaire-col');
            const $montantCols = $('.montant-col');

            $valoriserCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    // Afficher les colonnes du prix unitaire et du montant
                    $prixUnitaireCols.show();
                    $montantCols.show();
                } else {
                    // Masquer les colonnes du prix unitaire et du montant
                    $prixUnitaireCols.hide();
                    $montantCols.hide();
                }
            });

            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            // Masquer les colonnes par défaut
            $prixUnitaireCols.hide();
            $montantCols.hide();
        });
        $(document).ready(function() {
            const $Convertir_en_casierCheckbox = $('#Convertir_en_casier');
            const $casier_col = $('.casier_col');

            $Convertir_en_casierCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    // Afficher les colonnes du prix unitaire et du montant
                    $casier_col.show();
                } else {
                    // Masquer les colonnes du prix unitaire et du montant
                    $casier_col.hide();
                }
            });

            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });

            // Masquer les colonnes par défaut
            $casier_col.hide();
        });

        $(document).ready(function() {
            $('#exportExcel').click(function() {
                $('#exporterExcelModal').modal('hide')
            });
        });


        $(document).ready(function() {
            $('#formatImportation').click(function() {
                $('#exporterExcelModal2').modal('hide')
            });
        });

        $(document).ready(function() {
            $('.exportExcel').click(function() {
                $('#imprimerPDFModal').modal('hide')
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.post(url, function(data) {
                    var tableContent = $(data).find('.responsive-1')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-1").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-2')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-2").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
        });

        $(document).ready(function() {
            $('#searchInput').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput1').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput2').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#searchInput3').on('change', function() { // Lorsque le contenu du champ est modifié
                var searchText = $(this).val()
                    .toLowerCase(); // Récupérer le texte saisi et le convertir en minuscules
                var $noResultsMessage = $('#noResultsMessage');
                var hasResults = false;
                $('#produitTable tbody tr').each(function() { // Pour chaque ligne dans le corps du tableau
                    var rowData = $(this).text()
                        .toLowerCase(); // Récupérer le texte de la ligne et le convertir en minuscules
                    if (rowData.indexOf(searchText) !== -
                        1) { // Si le texte de la ligne contient le texte recherché
                        $(this).show(); // Afficher la ligne
                        hasResults = true;
                    } else {
                        $(this).hide(); // Sinon, masquer la ligne
                    }
                });
                if (hasResults) {
                    $noResultsMessage.hide();
                } else {
                    $noResultsMessage.show();
                }
            });
        });

        $(document).ready(function() {
            $('#appliquerHistorique').click(function() {
                // Ajouter la classe "show active" à l'onglet "Liste"
                $('#nav-home').addClass('show active');
                // Retirer la classe "show active" des autres onglets
                $('#nav-profile').removeClass('show active');
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec la classe "tableInfo"
                var $table = $('.tableInfo');

                // console.log(' $table ', $table)
                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                // console.log('$rows ', $rows)


                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-historique');

                    // console.log('$cells ', $cells)

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });


                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }


                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimerStockEmballageHistorique',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#printButton').click(function() {
                logTableContent();
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec la classe "tableInfo"
                var $table = $('.tableInfo');

                // console.log(' $table ', $table)
                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                // console.log('$rows ', $rows)


                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-historique');

                    // console.log('$cells ', $cells)

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });


                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }


                });

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'exporterStockEmballageHistorique',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#printButtonExcel').click(function() {
                logTableContent();
            });
        });
    </script>
@endsection
