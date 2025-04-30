@extends('layouts.master', ['title' => 'Entrées'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Entrées',
        'infos2' => 'Entrées',
        'infos3' => 'Liste',
    ])
    <style>
        .overflow-container {
            height: 300px;
            /* Définir la hauteur du conteneur */
            overflow-x: auto;
            /* Défilement horizontal si nécessaire */
            overflow-y: auto;
            /* Défilement vertical si nécessaire */
            white-space: nowrap;
            /* Empêche le retour à la ligne pour que le contenu dépasse horizontalement */
            border: 1px solid #ccc;
            /* Facultatif : ajouter une bordure pour mieux visualiser le conteneur */
        }
    </style>


    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @canany(['effectuer-entrer-produit', 'imprimer-liste-entrees-produits', 'exporter-entree-produit',
                'importer-entree-produit'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle pull-right  text-white mb-2 mt-2" style="{{ background_color_1() }}"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('effectuer-entrer-produit')
                            <li><a class="dropdown-item" type="button"
                                    href="{{ route('page.produit.entree_nouveau') }}">Nouveau</a>
                            </li>
                        @endcan

                        <form action="{{ route('imprimer-entree-action') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" id="value" name="id_entree">
                            @can('imprimer-liste-entrees-produits')
                                <li><button class="dropdown-item" type="submit" id="imprimer-button" name="reponse"
                                        value="imprimer">Imprimer</button>
                                @endcan

                                @can('exporter-entree-produit')
                                <li><button class="dropdown-item" type="submit" id="imprimer-button" name="reponse"
                                        value="exporter">Exporter</button>
                                </li>
                            @endcan

                            @can('importer-entree-produit')
                                <li><button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#importerStock" value="exporter">Importer Excel</button>
                                </li>
                            @endcan

                        </form>
                    </ul>

                </div>
            @endcanany


            <div class="modal fade" id="importerStock" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
                tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel">Importer le stock des
                                produits</h5>
                            <form action="{{ route('fiche_importation_entree_action') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 ms-3">
                                        <button class="btn btn-sm btn-primary">Télécharger</button>
                                    </div>
                                </div>
                            </form>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="formimport" action="{{ route('import.entree') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                @if (emballageActiver())
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="Vente_consignation" class="form-label fw-bold">Entrée produit avec
                                                sortie d'emballage?
                                            </label>

                                            <div class="btn-group w-100" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check Vente_consignation-select"
                                                    name="entree_consignation" checked value="0"
                                                    id="entree_consignation0" checked>
                                                <label class="btn btn_Vente_consignation"
                                                    for="entree_consignation0">NON</label>

                                                <input type="radio" class="btn-check Vente_consignation-select"
                                                    name="entree_consignation" value="1" id="entree_consignation1">
                                                <label class="btn btn_Vente_consignation"
                                                    for="entree_consignation1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label text-start">Fournisseur <span
                                            class="text-danger fw-bold">*</span>
                                    </label>
                                    <select style="width: 100%" name="fournisseur" id="id_fournisseur" class="form-select">
                                        <option value="">Sélectionnez le fournisseur</option>
                                        @foreach (fournisseurs() as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->DenominationSociale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label text-start">Sélectionner le fichier
                                        Excel</label>
                                    <input type="file" name="file" class="form-control" id="excelFile"
                                        accept=".xlsx, .xls">
                                </div>


                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="confirmimport" class="btn btn-primary">Importer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">

                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Liste</button>
                    @can('voir-impression-entree-produit')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="false">Impression</button>
                    @endcan

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filterEntree') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des entrées</h3>
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
                                                    <th class="header" style="" scope="col">Fournisseur</th>
                                                    {{-- <th class="header" style="" scope="col">Utilisateur</th> --}}
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
                                                @if (isset($entree_produits))
                                                    @forelse($entree_produits as $entree_produit)
                                                        <tr style="cursor:pointer" class="clickable-row"
                                                            data-url="{{ route('get.entrer_produit_for_entree_produit', ['id' => $entree_produit->id]) }}">
                                                            <td hidden class="entree-produit"><input type="hidden"
                                                                    value="{{ $entree_produit->id }}"></td>
                                                            <td class="entree-produit">
                                                                {{ \Carbon\Carbon::parse($entree_produit->Date_Entree)->format('d/m/Y H:i') }}
                                                            </td>
                                                            <td class="entree-produit">{{ $entree_produit->NomAgence }}
                                                            </td>
                                                            <td class="entree-produit">
                                                                {{ $entree_produit->Reference_Entree }}</td>
                                                            <td class="entree-produit">
                                                                {{ $entree_produit->DenominationSociale }}</td>
                                                            <td class="entree-produit">{{ $entree_produit->Observations }}
                                                            </td>
                                                            <td class="entree-produit">{{ $entree_produit->name }}</td>
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
                                    <h3 class="mt-2  d-inline-block text-dark">Détails de l'entrée</h3>
                                </div>
                                <div class="card-body responsive-1 ">
                                    <div class="table-responsive">
                                        <table id="magasinsTable"
                                            class="datatable table tableInfo table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="" scope="col">
                                                        Référence produit</th>
                                                    <th style="" scope="col">
                                                        Désignation</th>
                                                    <th style="" scope="col">
                                                        Magasin</th>
                                                    <th style="" scope="col">Qté
                                                    </th>
                                                    <th style="" scope="col">Prix
                                                        d'achat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($entrer_produits))
                                                    @forelse($entrer_produits as $entrer_produit)
                                                        <tr class="clickable-row">
                                                            <td class="entree-produit">{{ $entrer_produit->Reference }}
                                                            </td>
                                                            <td class="entree-produit">{{ $entrer_produit->Designation }}
                                                            </td>
                                                            <td class="entree-produit">{{ $entrer_produit->NomMagasin }}
                                                            <td class="entree-produit">{{ $entrer_produit->Qte_Entree }}
                                                            </td>
                                                            <td class="entree-produit">
                                                                {{ $entrer_produit->Prix_Achat_Net }}
                                                            </td>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">Aucune donnée </td>
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
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="row mt-4 d-flex justify-content-center">
                        <div class="col-md-8 mb-5">
                            <h3>LISTE DES ENTREES EN STOCK SUR UNE PERIODE</h3>
                            <form class="row gx-3 gy-2" action="{{ route('imprimer-entree') }}" method="POST" target="_blank">
                                @csrf
                                <div class="col-sm-6">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="date_debut_periode" type="date" class="form-control"
                                        id="date_debut_periode" value="<?php echo date('Y-m-d', strtotime('-5 days')); ?>">
                                    <div class="invalid-feedback">La date de debut est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="date_fin_periode">Fin Période</label>
                                    <input name="date_fin_periode" type="date" class="form-control"
                                        id="date_fin_periode" value="<?php echo date('Y-m-d'); ?>">
                                    <div class="invalid-feedback">La date de fin est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="magasin">Agence</label>
                                    <select name="magasin" type="text" class="form-select js-single"
                                        style="width: 100%" id="magasin">
                                        @if (session()->get('site_id') == '1')
                                            <option value="Toutes">Toutes</option>
                                        @endif
                                        @if (isset($agences))
                                            @foreach ($agences as $agence)
                                                <option value="{{ $agence->id }}">
                                                    {{ $agence->NomAgence }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Le magasin est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="fournisseur">Fournisseur</label>
                                    <select name="fournisseur" type="text" class="form-select js-single"
                                        style="width: 100%" id="fournisseur">
                                        <option value="Tous">Tous</option>
                                        @if (isset($fournisseurs))
                                            @foreach ($fournisseurs as $fournisseur)
                                                <option value="{{ $fournisseur->id }}">
                                                    {{ $fournisseur->DenominationSociale }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Le fournisseur de fin est obligatoire</div>
                                </div>
                                {{-- <div class="col-sm-6">
                                    <label class="form-label" for="categorie">Catégorie</label>
                                    <select name="categorie" type="text" class="form-select js-single"
                                        style="width: 100%" id="categorie">
                                        <option value="Toutes">Toutes</option>
                                        @if (isset($categories))
                                            @foreach ($categories as $categorie)
                                                <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                    <div class="invalid-feedback">La catégorie est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="produit">Produit</label>
                                    <select name="produit" type="text" class="form-select js-single"
                                        style="width: 100%" id="produit">
                                        <option value="Tous">Tous</option>
                                        @if (isset($produits))
                                            @foreach ($produits as $produit)
                                                <option value="{{ $produit->id }}">{{ $produit->Reference }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                    <div class="invalid-feedback">Le produit de fin est obligatoire</div>
                                </div> --}}
                                <div class="col-auto">
                                    <div class="btn-group mt-4">
                                        {{-- <button style="{{ background_color_2() }}" type="submit" name="response"
                                            value="afficher" class="btn btn text-white">Afficher</button> --}}
                                    </div>
                                </div>
                                @can('entree-produit-pdf')
                                <div class="col-auto">
                                    <div class="btn-group mt-4">
                                        <button style="{{ background_color_2() }}" type="submit" name="response"
                                            value="imprimer" class="btn btn text-white">Imprimer PDF</button>
                                    </div>
                                </div>
                                @endcan
                                @can('entree-produit-excel')
                                <div class="col-auto">
                                    <div class="btn-group mt-4">
                                        <button type="submit" name="response" value="exporter"
                                            class="btn btn text-white" style="{{ background_color_1() }}">Export
                                            Excel</button>
                                    </div>
                                </div>
                                @endcan
                            </form>
                        </div>
                    </div>

                    <div class="row overflow-container" style="margin-bottom:25px;">
                        <div class="col-md-12">
                            @if (isset($getFournisseurEntreeProduits))
                                @foreach ($getFournisseurEntreeProduits as $fournisseur_entree_produits)
                                    <div class="boite-niveau-deux box0p">
                                        <div class="r">
                                            <span class="t">Fournisseur:
                                                <strong>{{ $fournisseur_entree_produits->Denomination_sociale }}</strong></span><br>
                                        </div>
                                    </div>

                                    @foreach ($getFournisseur as $entree_produit)
                                        @if ($fournisseur_entree_produits->Id_Fournisseur === $entree_produit->Id_Fournisseur)
                                            <div class="clearfix" style="margin-bottom: 20px">
                                                <div class="box0">
                                                    <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                                                        <legend class="float-none w-auto px-1" style="font-weight: bold;">
                                                            REFERENCES</legend>
                                                        <table class="tableInfo">
                                                            <tbody>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: bold; font-style: italic; vertical-align: top;">
                                                                        Agence&nbsp;:
                                                                    </td>
                                                                    {{-- <td>{{ $entree_produit->NomAgence }}</td> --}}
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: bold; font-style: italic; vertical-align: top;">
                                                                        Date&nbsp;:</td>
                                                                    <td>{{ \Carbon\Carbon::parse($entree_produit->Date_Entree)->format('d/m/Y H:i') }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: bold; font-style: italic; vertical-align: top;">
                                                                        &nbsp;Référence&nbsp;:</td>
                                                                    <td>{{ $entree_produit->Reference_Entree }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td
                                                                        style="font-weight: bold; font-style: italic; vertical-align: top;">
                                                                        Vendeur&nbsp;:
                                                                    </td>
                                                                    <td>{{ $entree_produit->name }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        @endif

                                        @foreach ($getMagasin as $magasin_value)
                                            @php
                                                // Filtrer les entrées pour vérifier si ce magasin a au moins une entrée
                                                $hasEntree = $getEntree->contains(function ($value) use (
                                                    $magasin_value,
                                                    $entree_produit,
                                                    $fournisseur_entree_produits,
                                                ) {
                                                    return $value->Id_Magasin === $magasin_value->Id_Magasin &&
                                                        $value->Id_Fournisseur ===
                                                            $fournisseur_entree_produits->Id_Fournisseur &&
                                                        $value->Id_Entree_Produit ===
                                                            $entree_produit->Id_Entree_Produit;
                                                });
                                            @endphp

                                            @if ($hasEntree)
                                                <div class="entete-magasin">
                                                    <h3>{{ $magasin_value->NomMagasin }}</h3>
                                                </div>

                                                <div class="table" style="margin-bottom: 20px">
                                                    <table class="tableLigne">
                                                        @if (count($getEntree) > 0)
                                                            <thead>
                                                                <tr>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Reference</th>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Désignation</th>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Catégorie</th>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Qté
                                                                    </th>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Prix
                                                                    </th>
                                                                    <th
                                                                        style="width:300px; background-color: #3232df;font-weight: bold; color: white;">
                                                                        Montant</th>
                                                                </tr>
                                                            </thead>
                                                        @endif

                                                        <tbody>
                                                            @foreach ($getEntree as $value)
                                                                @if (
                                                                    $value->Id_Magasin === $magasin_value->Id_Magasin &&
                                                                        $value->Id_Fournisseur === $fournisseur_entree_produits->Id_Fournisseur &&
                                                                        $value->Id_Entree_Produit === $entree_produit->Id_Entree_Produit)
                                                                    <tr>
                                                                        <td>{{ $value->Reference }}</td>
                                                                        <td>{{ $value->Designation }}</td>
                                                                        <td>{{ $value->Libelle }}</td>
                                                                        <td>{{ $value->Qte_Entree }}</td>
                                                                        <td>{{ number_format(intval($value->Prix_Achat_Net), 0, ',', ' ') }}
                                                                        </td>
                                                                        <td>{{ number_format(intval($value->Qte_Entree * $value->Prix_Achat_Net), 0, ',', ' ') }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>

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

        .selected {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .selected>td {
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

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }

        .tableInfo2 {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo2 th,
        .tableInfo2 td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }
    </style>
    @include('layouts.alert')
    <script>
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
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-1')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-1").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
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
            // Désactivez le bouton "Valider" par défaut
            $('#imprimer-button').prop('disabled', true);

            // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
            $('.clickable-row').click(function() {
                // Activez le bouton "Valider"
                $('#imprimer-button').prop('disabled', false);
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                // Récupérer la valeur de l'input caché
                var value = $(this).find('input[type="hidden"]').val();

                // Afficher la valeur dans la console (ou effectuer une autre action)
                // console.log(value);

                // Optionnel: assigner la valeur à un autre élément input caché avec id "value"
                $('#value').val(value);
                // Optionnel: rediriger vers une URL (décommenter pour activer)
                // window.location = $(this).data("url");
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


            $('#proformaTable').on('click', '.clickable-row', function() {
                var proformaId = $(this).data('url').split('/').pop();
                // console.log("ID du proforma cliqué :", proformaId);
                var editProformaUrl = "{{ route('editProforma', ['id' => ':proformaId']) }}";
                var dupliquerProformaUrl = "{{ route('dupliquerProforma', ['id' => ':proformaId']) }}";
                var conversionProformaUrl = "{{ route('conversionProforma', ['id' => ':proformaId']) }}";
                var pdfProformaUrl = "{{ route('PDFProformaA4', ['id' => ':proformaId']) }}";
                editProformaUrl = editProformaUrl.replace(':proformaId', proformaId);
                dupliquerProformaUrl = dupliquerProformaUrl.replace(':proformaId', proformaId);
                conversionProformaUrl = conversionProformaUrl.replace(':proformaId', proformaId);
                pdfProformaUrl = pdfProformaUrl.replace(':proformaId', proformaId);
                $('#modifierProformaLink').attr('href', editProformaUrl);
                $('#dupliquerProformaLink').attr('href', dupliquerProformaUrl);
                $('#conversionProformaLink').attr('href', conversionProformaUrl);
                $('#pdfProformaLink').attr('href', pdfProformaUrl);
            });

            $('.clickable-row').on('click', function() {
                $('.clickable-row').removeClass('selected');
                $(this).addClass('selected');
            });
        }

        function clearDetailTable() {
            var detailTbody = document.querySelector('.tableInfo2 tbody');
            detailTbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">Aucune donnée (Selectionner une facture)</td>
                </tr>
            `;
        }

        $(document).ready(function() {
            applyTableEvents();
        });
    </script>
    @include('layouts.alert')
@endsection
