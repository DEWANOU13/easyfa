@extends('layouts.master', ['title' => 'Statistique Vente'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Vente Par quantite ',
        'infos2' => 'Vente',
        'infos3' => 'Liste',
    ])

    <div class="d-flex justify-content-center">
        <ul class="nav nav-tabs" id="myTab" role="tablist">

            @can('statistique-vente-quantite-cumulee-categorie')
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if (old('filtered') == 'sale_by_category' ||
                            $filtered == 'sale_by_category' ||
                            ($filtered == '' && old('filtered') == '')) active @endif" id="cumCat-tab" data-bs-toggle="tab"
                        data-bs-target="#cumCat" type="button" role="tab" aria-controls="cumCat"
                        aria-selected="false">Ventes Cumulées par Categorie </button>
                </li>
            @endcan

            @can('statistique-vente-quantite-cumulee-produit')
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if (old('filtered') == 'sale_by_product' || $filtered == 'sale_by_product') active @endif" id="cumPro-tab"
                        data-bs-toggle="tab" data-bs-target="#cumPro" type="button" role="tab" aria-controls="cumPro"
                        aria-selected="false">Ventes Cumulées par Produit </button>
                </li>
            @endcan

        </ul>
    </div>
    <div class="tab-content" id="myTabContent">

        @can('statistique-vente-quantite-cumulee-categorie')
            <div class="tab-pane fade @if (old('filtered') == 'sale_by_category' ||
                    $filtered == 'sale_by_category' ||
                    ($filtered == '' && old('filtered') == '')) show active @endif" id="cumCat" role="tabpanel"
                aria-labelledby="cumCat-tab">
                <form method="POST" action="{{ route('venteStatistiquesq') }}">
                    @csrf
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <div class="row d-flex align-items-center">
                                    <input name="filtered" type="hidden" class="form-control" id="filtered"
                                        value="sale_by_category">
                                    <div class="col-md-3">
                                        <label class="form-label" for="date_debut_periode">Catégorie</label><br>
                                        <select style="width:100%" name="categorie" type="text"
                                            class="form-select {{ $errors->has('categorie') ? 'is-invalid' : '' }} js-single"
                                            id="categorie">
                                            <option value="Toutes">Toutes</option>
                                            @foreach (categories() as $key => $value)
                                                <option {{ isset($categorie) ? 'selected' : '' }} value="{{ $value->id }}">
                                                    {{ $value->Libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- <i class="text-danger">Laissez vide pour tout afficher</i> --}}
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="agence">Agence</label><br>
                                        <select style="width:100%" name="agence" class="form-select js-single" id="agence_">

                                            @if (session()->get('site_id') == '1')
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @foreach ($agences as $key => $value)
                                                @if (isset($agence) && !empty($agence) && $agence != 'Toutes' && $agence->id == $value->id)
                                                    <option value="{{ $value->id }}" selected>
                                                        {{ $value->NomAgence }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->NomAgence }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="client">Client</label><br>
                                        <select style="width:100%" name="client" class="form-select js-single" id="client_">

                                            <option value="Tous">Tous</option>
                                            @foreach (clients() as $key => $value)
                                                @if (isset($client) && !empty($client) && $client != 'Tous' && $client->id == $value->id)
                                                    <option value="{{ $value->id }}" selected>
                                                        {{ $value->Denomination_sociale }}
                                                    </option>
                                                @else
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Denomination_sociale }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="date_debut_periode">Debut Période</label>
                                        <input name="start_date_1" type="datetime-local"
                                            value="{{ isset($startDate) ? $startDate : now() }}"
                                            class="form-control {{ $errors->has('start_date_1') ? 'is-invalid' : '' }}"
                                            max="{{ date('d-m-Y') }}" id="start_date_1">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="date_debut_periode">Fin Période</label>
                                        <input name="end_date_1" type="datetime-local"
                                            value="{{ isset($endDate) ? $endDate : now() }}"
                                            class="form-control {{ $errors->has('end_date_1') ? 'is-invalid' : '' }}"
                                            max="{{ date('d-m-Y') }}" id="end_date">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mt-4">
                                            <button type="submit" class="btn text-white w-100"
                                                style="{{ background_color_1() }}">Appliquer</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex mt-4">
                                            @can('statistique-vente-quantite-cumulee-categorie-excel')
                                            <form action="">
                                                <button type="button" id="printButtonCategorieExport"
                                                    class="btn m-2 btn-success">Exporter</button>
                                            </form>
                                            @endcan

                                            @can('statistique-vente-quantite-cumulee-categorie-pdf')
                                            <form action="" method="">
                                                <button type="button" id="printButtonCategorie"
                                                    class="btn m-2 btn-warning">Impression
                                                    PDF</button>
                                            </form>
                                            @endcan

                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
                <div class="card m-b-30" wire:ignore>
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des ventes cumulées par catégorie
                            {{ $ventes_par_categorie ? 'du ' . date('d-m-Y', strtotime($startDate)) . ' au ' . date('d-m-Y', strtotime($endDate)) : '' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="list_cumule_by_category" class="list_category tableInfo table dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col">Libelle Categorie</th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Quantite </th>
                                        <th scope="col">Agence</th>
                                        <th scope="col">Magasin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($ventes_par_categorie)
                                        @foreach ($ventes_par_categorie as $vente_par_categorie)
                                            <tr>
                                                <td>{{ $vente_par_categorie->Libelle }}</td>
                                                <td>{{ $vente_par_categorie->Denomination_sociale }}</td>
                                                <td>{{ $vente_par_categorie->total_quantite }}</td>
                                                <td>{{ $vente_par_categorie->NomAgence }}</td>
                                                <td>{{ $vente_par_categorie->NomMagasin }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcan


        <div class="tab-pane fade @if (old('filtered') == 'sale_by_product' || $filtered == 'sale_by_product') show active @endif" id="cumPro" role="tabpanel"
            aria-labelledby="cumPro-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('venteStatistiquesq') }}" method="POST">
                            @csrf
                            <div class="row d-flex align-items-center">
                                <input name="filtered" type="hidden" class="form-control" id="filtered"
                                    value="sale_by_product">
                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Produit</label><br>
                                    <select style="width:100%" name="produit" type="text"
                                        class="form-select js-single" id="produit">
                                        <option value="Tous">Tous</option>
                                        @foreach (produits() as $key => $value)
                                            @if (isset($produit) && !empty($produit) && $produit != 'Tous' && $produit->id == $value->id)
                                                <option value="{{ $value->id }}" selected>
                                                    {{ $value->Designation }}
                                                </option>
                                            @else
                                                <option value="{{ $value->id }}">
                                                    {{ $value->Designation }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="agence">Agence</label><br>
                                    <select style="width:100%" name="agence" class="form-select js-single"
                                        id="agence">
                                        {{-- <option value="">Tous</option>
                                    @foreach (agences() as $agence)
                                        <option value="{{ $agence->id }}">
                                            {{ $agence->NomAgence }}
                                        </option>
                                    @endforeach --}}
                                        <option value="Toutes" {{ $agences->count() > 1 ? '' : 'hidden disabled' }}>Toutes
                                        </option>
                                        @foreach ($agences as $key => $value)
                                            @if (isset($agence) && !empty($agence) && $agence != 'Toutes' && $agence->id == $value->id)
                                                <option value="{{ $value->id }}" selected>{{ $value->NomAgence }}
                                                </option>
                                            @else
                                                <option value="{{ $value->id }}">{{ $value->NomAgence }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="client">Client</label><br>
                                    <select style="width:100%" name="client" class="form-select js-single"
                                        id="client">
                                        <option value="Tous">Tous</option>
                                        @foreach (clients() as $key => $value)
                                            @if (isset($client) && !empty($client) && $client != 'Tous' && $client->id == $value->id)
                                                <option value="{{ $value->id }}" selected>
                                                    {{ $value->Denomination_sociale }}
                                                </option>
                                            @else
                                                <option value="{{ $value->id }}">
                                                    {{ $value->Denomination_sociale }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="start_date_3" type="datetime-local"
                                        value="{{ isset($startDate) ? $startDate : now() }}"
                                        class="form-control {{ $errors->has('start_date_3') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="start_date_3">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="end_date_3" type="datetime-local"
                                        value="{{ isset($endtDate) ? $startDate : now() }}"
                                        class="form-control {{ $errors->has('end_date_3') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="end_date_3">
                                </div>
                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex mt-4">
                                        @can('statistique-vente-quantite-cumulee-produit-excel')
                                        <form action="">
                                            <button type="button" id="printButtonProductExport"
                                                @if (!$ventes_par_produit) disabled @endif
                                                class="btn m-2 btn-success">Exporter</button>
                                        </form>
                                        @endcan

                                        @can('statistique-vente-quantite-cumulee-produit-pdf')
                                        <form action="" method="">
                                            @csrf
                                            <button type="button" id="printButtonProduct"
                                                @if (!$ventes_par_produit) disabled @endif
                                                class="btn  m-2 btn-warning text-white">Impression
                                                PDF</button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des ventes cumulées par produit
                        {{ $ventes_par_produit ? 'du ' . date('d-m-Y', strtotime($startDate)) . ' au ' . date('d-m-Y', strtotime($endDate)) : '' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list_cumule_by_product" class="list_product tableInfo table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Reference</th>
                                    <th scope="col">Désignation Produit</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Quantite</th>
                                    <th scope="col">Agence </th>
                                    <th scope="col">Magasin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ventes_par_produit)
                                    @foreach ($ventes_par_produit as $vente_par_produit)
                                        <tr>
                                            <td>{{ $vente_par_produit->Reference }}</td>
                                            <td>
                                                {{ $vente_par_produit->Designation }}
                                            </td>
                                            <td>
                                                {{ $vente_par_produit->Denomination_sociale }}
                                            </td>
                                            <td>{{ number_format($vente_par_produit->total_quantite, 0, '.', ' ') }}</td>
                                            <td>{{ $vente_par_produit->NomAgence }}</td>
                                            <td>{{ $vente_par_produit->NomMagasin }}</td>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- <div class="tab-pane fade  @if (old('filtered') == 'sale_by_customer' || $filtered == 'sale_by_customer') show active @endif" id="cumClient" role="tabpanel"
            aria-labelledby="cumClient-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('venteStatistiques') }}" method="POST">
                            @csrf
                            <div class="row d-flex align-items-center">
                                <input name="filtered" type="hidden" class="form-control" id="filtered"
                                    value="sale_by_customer">
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Client</label>
                                    <select style="width:100%" name="client" type="text"
                                        class="form-select js-single" id="client">
                                        <option value="">Tous</option>
                                        @foreach (clients() as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->Denomination_sociale }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="start_date_2" type="datetime-local"
                                        class="form-control {{ $errors->has('start_date_2') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="start_date_2">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="end_date_2" type="datetime-local"
                                        class="form-control {{ $errors->has('end_date_2') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="end_date_2">
                                </div>
                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex mt-4">
                                        <form action="">
                                            <button type="button" id="printButtonClientExport"
                                                @if (!$ventes_par_client) disabled @endif
                                                class="btn m-2 btn-success">Exporter</button>
                                        </form>

                                        <form action="" method="">
                                            @csrf
                                            <button type="button" id="printButtonClient"
                                                @if (!$ventes_par_client) disabled @endif
                                                class="btn m-2 btn-warning text-white">Impression
                                                PDF</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des ventes cumulées par client
                        {{ $ventes_par_client ? 'du ' . date('d-m-Y', strtotime($startDate)) . ' au ' . date('d-m-Y', strtotime($endDate)) : '' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list_cumule_by_client"
                            class="list_client tableInfo datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Raison sociale
                                        du client
                                    </th>
                                    <th scope="col">Vente HT </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">vente TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ventes_par_client)
                                    @foreach ($ventes_par_client as $vente_par_client)
                                        <tr>
                                            <td>{{ $vente_par_client->Code_client }}</td>
                                            <td>{{ number_format($vente_par_client->total_montant_ht, 0, '.', ' ') }}</td>
                                            <td>{{ number_format($vente_par_client->total_tva, 0, '.', ' ') }}</td>
                                            <td>{{ number_format($vente_par_client->total_montant_ttc, 0, '.', ' ') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div> --}}


        {{-- <div class="tab-pane fade @if (old('filtered') == 'sale_by_agence' || $filtered == 'sale_by_agence') show active @endif" id="cumAg" role="tabpanel"
            aria-labelledby="cumAg-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('venteStatistiques') }}" method="POST">
                            @csrf
                            <div class="row d-flex align-items-center">
                                <input name="filtered" type="hidden" class="form-control" id="filtered"
                                    value="sale_by_agence">
                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Agence</label>
                                    <select style="width:100%" name="agence" type="text"
                                        class="form-select js-single" id="agence_1">
                                        <option value="">Sélectionnez une agence</option>
                                        @foreach (agences() as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->NomAgence }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Début Période</label>
                                    <input name="start_date_4" type="datetime-local"
                                        class="form-control {{ $errors->has('start_date_4') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="start_date_4">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="end_date_4" type="datetime-local"
                                        class="form-control {{ $errors->has('end_date_4') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="end_date_4">
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="d-flex mt-4">
                                        <form action="">
                                            <button type="button" id="printButtonAgenceExport"
                                                class="btn m-2 btn-success">Exporter</button>
                                        </form>

                                        <form action="" method="">
                                            @csrf
                                            <button type="button" id="printButtonAgence"
                                                class="btn  m-2 btn-warning">Impression
                                                PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>

            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2 d-inline-block text-dark">Liste des ventes cumulées par agence
                        {{ $ventes_par_agence ? 'du ' . date('d-m-Y', strtotime($startDate)) . ' au ' . date('d-m-Y', strtotime($endDate)) : '' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list_cumule_by_agence"
                            class="list_agence tableInfo datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Désignation des agences
                                    </th>
                                    <th scope="col">Quantite</th>
                                    <th scope="col">Ventes HT </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">Ventes TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ventes_par_agence)
                                    @foreach ($ventes_par_agence as $vente_par_agence)
                                        <tr>
                                            <td>{{ $vente_par_agence->NomAgence }}</td>
                                            <td>{{ number_format($vente_par_agence->total_quantite, 0, '.', ' ') }}</td>
                                            <td>{{ number_format($vente_par_agence->total_montant_ht, 0, '.', ' ') }}</td>
                                            <td>{{ number_format($vente_par_agence->total_tva, 0, '.', ' ') }}</td>
                                            <td>{{ number_format($vente_par_agence->total_montant_ttc, 0, '.', ' ') }}</td>

                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="tab-pane fade @if (old('filtered') == 'sale_by_user' || $filtered == 'sale_by_user') show active @endif" id="USER" role="tabpanel"
            aria-labelledby="USER-tab">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <form action="{{ route('venteStatistiques') }}" method="POST">
                            @csrf
                            <div class="row d-flex align-items-center">
                                <input name="filtered" type="hidden" class="form-control" id="filtered"
                                    value="sale_by_user">
                                <div class="col-md-2">
                                    <label class="form-label" for="user">Utilisateur</label><br>
                                    <select style="width:100%" name="user" type="text"
                                        class="form-select js-single" id="user">
                                        @if ($user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @else
                                            <option value="">Sélectionnez un utilisateur</option>
                                        @endif
                                        @foreach (users() as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="agence">Agence</label><br>
                                    <select style="width:100%" name="agence" type="text"
                                        class="form-select js-single" id="agence">
                                        @if (isset($agence) && $agence != 'Toutes')
                                            <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                                        @else
                                            <option value="">Sélectionnez une agence</option>
                                        @endif
                                        @foreach (agences() as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->NomAgence }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Début Période</label>
                                    <input name="start_date_8" type="datetime-local"
                                        value="{{ $startDate_new ? $startDate_new : '' }}"
                                        class="form-control {{ $errors->has('start_date_8') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="start_date_8">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="end_date_8" type="datetime-local"
                                        value="{{ $endDate_new ? $endDate_new : '' }}"
                                        class="form-control {{ $errors->has('end_date_8') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="end_date_8">
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-4">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="d-flex mt-4">
                                        <form action="">
                                            <button type="button" id="printButtonUserExport"
                                                class="btn m-2 btn-success">Exporter</button>
                                        </form>

                                        <form action="" method="">
                                            @csrf
                                            <button type="button" id="printButtonUser"
                                                class="btn  m-2 btn-warning">Impression
                                                PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>

            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2 d-inline-block text-dark">Liste des ventes par utilisateur
                        {{ $journal_ventes_user ? 'du ' . date('d-m-Y H:m:i', strtotime($startDate_new)) . ' au ' . date('d-m-Y H:m:i', strtotime($endDate_new)) : '' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="sale_by_user" class="list_user tableInfo table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Date Début
                                    </th>
                                    <th scope="col">Date Fin</th>
                                    <th scope="col">Reference Facture</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Ventes </th>
                                    <th scope="col">Avoir </th>
                                    <th scope="col">Agence </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $soldeFV = 0;
                                    $soldeFA = 0;
                                @endphp
                                @if ($journal_ventes_user)
                                    @foreach ($journal_ventes_user as $journal_ventes_user)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($startDate_new)->format('d/m/Y H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($endDate_new)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $journal_ventes_user->Reference_facture }}</td>
                                            <td>{{ $journal_ventes_user->Denomination_sociale }}</td>
                                            @if ($journal_ventes_user->Code_type_facture == 'FV' || $journal_ventes_user->Code_type_facture == 'EV')
                                                <td>{{ number_format($journal_ventes_user->Prix_revient, 0, '.', ' ') }}
                                                </td>
                                                @php
                                                    $soldeFV += $journal_ventes_user->Prix_revient;
                                                @endphp
                                            @else
                                                <td align="center">-</td>
                                            @endif
                                            @if ($journal_ventes_user->Code_type_facture == 'FA' || $journal_ventes_user->Code_type_facture == 'EA')
                                                <td>{{ number_format($journal_ventes_user->Prix_revient, 0, '.', ' ') }}
                                                </td>
                                                @php
                                                    $soldeFA += $journal_ventes_user->Prix_revient;
                                                @endphp
                                            @else
                                                <td align="center">-</td>
                                            @endif
                                            <td>{{ $journal_ventes_user->NomAgence }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="fw-bold">{{ number_format($soldeFV, 0, '.', ' ') }}</td>
                                        <td class="fw-bold">{{ number_format($soldeFA, 0, '.', ' ') }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="tab-pane fade @if (old('filtered') == 'sale_log' || $filtered == 'sale_log') show active @endif" id="jv" role="tabpanel"
            aria-labelledby="jv-tab">
            <div class="row my-3">
                <form method="POST" action="{{ route('venteStatistiques') }}">
                    @csrf
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <div class="row d-flex align-items-center">

                                    <input name="filtered" type="hidden" class="form-control" id="filtered"
                                        value="sale_log">
                                    <div class="col-md-3">
                                        <label class="form-label" for="date_debut_periode">Debut Période</label>
                                        <input name="start_date_6" type="datetime-local"
                                            class="form-control {{ $errors->has('start_date_6') ? 'is-invalid' : '' }}"
                                            max="{{ date('d-m-Y') }}" id="start_date_6">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="date_debut_periode">Fin Période</label>
                                        <input name="end_date_6" type="datetime-local"
                                            class="form-control {{ $errors->has('end_date_6') ? 'is-invalid' : '' }}"
                                            max="{{ date('d-m-Y') }}" id="end_date_6">
                                    </div>

                                    <div class="col-md-2">
                                        <div class="mt-4">
                                            <button type="submit" class="btn text-white w-100"
                                                style="{{ background_color_1() }}">Appliquer</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex mt-4">
                                            <form action="">
                                                <button type="button" id="printButtonJournalVenteExport"
                                                    class="btn m-2 btn-success">Exporter</button>
                                            </form>

                                            <form action="" method="">
                                                @csrf
                                                <button type="button" id="printButtonJournalVente"
                                                    class="btn  m-2 btn-warning">Impression
                                                    PDF</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2 d-inline-block text-dark">Journal des ventes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="journal_vente" class="journal_vente tableInfo datatable table dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Reference</th>
                                    <th scope="col">Ventes HT </th>
                                    <th scope="col">TVA </th>
                                    <th scope="col">AIB</th>
                                    <th scope="col">Ventes TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($journal_ventes)
                                    @foreach ($journal_ventes as $journal_vente)
                                        <tr>
                                            <td>{{ date('d-m-Y H:i:s', strtotime($journal_vente->Date_facture)) }}</td>
                                            <td>{{ $journal_vente->Reference_facture }}</td>
                                            <td>{{ number_format(json_decode(total_facture($journal_vente->id))->total_HT, 0, '.', ' ') }}
                                            </td>
                                            <td>{{ number_format(json_decode(total_facture($journal_vente->id))->total_TVA, 0, '.', ' ') }}
                                            </td>
                                            <td>{{ number_format(json_decode(total_facture($journal_vente->id))->total_Aib_facturee, 0, '.', ' ') }}
                                            </td>
                                            <td>{{ number_format(json_decode(total_facture($journal_vente->id))->total_TTC, 0, '.', ' ') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    {{-- <div class="tab-pane fade @if (old('filtered') == 'credit_note' || $filtered == 'credit_note') show active @endif" id="FVA" role="tabpanel"
        aria-labelledby="FVA-tab">
        <div class="row my-3">
            <form method="POST" action="{{ route('venteStatistiques') }}">
                @csrf
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <div class="row d-flex align-items-center">

                                <input name="filtered" type="hidden" class="form-control" id="filtered"
                                    value="credit_note">
                                <div class="col-md-4">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="start_date_7" type="datetime-local"
                                        class="form-control {{ $errors->has('start_date_7') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="start_date_7"
                                        value="{{ isset($startDate) ? $startDate : '' }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label" for="date_debut_periode">Fin Période</label>
                                    <input name="end_date_7" type="datetime-local"
                                        class="form-control {{ $errors->has('end_date_7') ? 'is-invalid' : '' }}"
                                        max="{{ date('d-m-Y') }}" id="end_date_7"
                                        value="{{ isset($endDate) ? $endDate : '' }}">
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-3">
                                        <button type="submit" class="btn text-white w-100"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex mt-3">
                                        <form action="">
                                            <button type="button" id="printButtonFactureAvoirExport"
                                                class="btn m-2 btn-success">Exporter</button>
                                        </form>

                                        <form action="" method="">
                                            @csrf
                                            <button type="button" id="printButtonFactureAvoir"
                                                class="btn  m-2 btn-warning">Impression
                                                PDF</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>

        <div class="card m-b-30">
            <div class="card-header" style="{{ background_color_2() }}">
                <h3 class="mt-2 d-inline-block text-dark">Liste des factures d'avoir
                    {{ $ventes_par_agence ? 'du ' . date('d-m-Y', strtotime($startDate)) . ' au ' . date('d-m-Y', strtotime($endDate)) : '' }}
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="list_facture_avoir"
                        class="list_facture_avoir tableInfo datatable table dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Date Facture Avoir
                                </th>
                                <th scope="col">Référence Facture Origine</th>
                                <th scope="col">Référence</th>
                                <th scope="col">Ventes HT </th>
                                <th scope="col">TVA </th>
                                <th scope="col">Ventes TTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($facture_avoirs)
                                @forelse ($facture_avoirs as $facture_avoir)
                                    <tr>
                                        <td>{{ date('d-m-Y H:i:s', strtotime($facture_avoir->Date_facture)) }}</td>
                                        <td>{{ $facture_avoir->reference_ancienneFacture }}</td>
                                        <td>{{ $facture_avoir->Reference_facture }}</td>
                                        <td>{{ number_format(json_decode(total_facture($facture_avoir->id))->total_HT, 0, '.', ' ') }}
                                        </td>
                                        <td>{{ number_format(json_decode(total_facture($facture_avoir->id))->total_TVA, 0, '.', ' ') }}
                                        </td>
                                        <td>{{ number_format(json_decode(total_facture($facture_avoir->id))->total_TTC, 0, '.', ' ') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Aucune facture disponible.</td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
    </div>
    @include('components.alert')
    <style>
        .selected {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 100%;
        }

        .tableInfo th,
        .tableInfo td {
            /*border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
    <script>
        $(document).ready(function() {

            function sendRequest(reponse, tableData, jsEntete) {
                // Convertir les données du tableau en JSON
                var jsonData = JSON.stringify({
                    reponse: reponse, // Ajouter la variable 'reponse' à l'objet JSON
                    tableData: tableData, // Ajouter les données du tableau
                    jsEntete: jsEntete
                }, null, 2);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-vente-quantite',
                    method: 'POST',
                    target: '_blank'

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
                    value: jsonData
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            function logTableContentClient() {
                var $table = $('.list_client');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"
                if (reponse === 'list_cumule_by_client') {
                    // console.log('Je suis bien là');

                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.raisonSociale = $($cells[0]).text().trim();
                        rowData.ventesHT = $($cells[1]).text().trim();
                        rowData.tva = $($cells[2]).text().trim();
                        rowData.ventesTTC = $($cells[3]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                    sendRequest(reponse, tableData);

                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
            }

            function logTableContentJour() {
                var $table = $('.list_day');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_day') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.periode = $($cells[0]).text().trim();
                        rowData.totalHT = $($cells[1]).text().trim();
                        rowData.tva = $($cells[2]).text().trim();
                        rowData.totalTTC = $($cells[3]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentCategory() {
                var $table = $('.list_category');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');
                var categorie = $('#categorie').val();
                var agence = $('#agence_').val();
                var client = $('#client_').val();
                var start_date_1 = $('#start_date_1').val();
                var end_date = $('#end_date').val();

                var json = {
                    "categorie": categorie,
                    "agence": agence,
                    "client": client,
                    "start_date_1": start_date_1,
                    "end_date": end_date
                }

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_category') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log(reponse)

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];
                    var jsEntete = [];


                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.libelle = $($cells[0]).text().trim();
                        rowData.client = $($cells[1]).text().trim();
                        rowData.quantite = $($cells[2]).text().trim();
                        rowData.agence = $($cells[3]).text().trim();
                        rowData.magasin = $($cells[4]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                    jsEntete.push(json)
                    console.log(tableData)

                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData, jsEntete);
            }

            function logTableContentProduct() {
                var $table = $('.list_product');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id'); // var categorie = $('categorie');
                var produit = $('#produit').val();
                var agence = $('#agence').val();
                var client = $('#client_').val();
                var start_date_1 = $('#start_date_1').val();
                var end_date = $('#end_date').val();

                var json = {
                    "produit": produit,
                    "agence": agence,
                    "client": client,
                    "start_date_1": start_date_1,
                    "end_date": end_date
                }


                console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_product') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');


                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];
                    var jsEntete = [];


                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.reference = $($cells[0]).text().trim();
                        rowData.designation = $($cells[1]).text().trim();
                        rowData.client = $($cells[2]).text().trim();
                        rowData.quantite = $($cells[3]).text().trim();
                        rowData.agence = $($cells[4]).text().trim();
                        rowData.magasin = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);

                    });
                    jsEntete.push(json)
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData, jsEntete);
            }

            function logTableContentAgence() {
                var $table = $('.list_agence');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_agence') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.libelle = $($cells[0]).text().trim();
                        rowData.quantite = $($cells[1]).text().trim();
                        rowData.venteHT = $($cells[2]).text().trim();
                        rowData.tva = $($cells[3]).text().trim();
                        rowData.ventesTTC = $($cells[4]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentUser() {
                var $table = $('.list_user');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'sale_by_user') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date_debut = $($cells[0]).text().trim();
                        rowData.date_fin = $($cells[1]).text().trim();
                        rowData.reference = $($cells[2]).text().trim();
                        rowData.client = $($cells[3]).text().trim();
                        rowData.vente = $($cells[4]).text().trim();
                        rowData.avoir = $($cells[5]).text().trim();
                        rowData.agence = $($cells[6]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentJournalVente() {
                var $table = $('.journal_vente');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'journal_vente') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date = $($cells[0]).text().trim();
                        rowData.ref_facture = $($cells[1]).text().trim();
                        rowData.venteHT = $($cells[2]).text().trim();
                        rowData.tva = $($cells[3]).text().trim();
                        rowData.aib = $($cells[4]).text().trim();
                        rowData.ventesTTC = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentFactureAvoir() {
                var $table = $('.list_facture_avoir');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_facture_avoir') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date = $($cells[0]).text().trim();
                        rowData.reference_ancienneFacture = $($cells[1]).text().trim();
                        rowData.ref_facture = $($cells[2]).text().trim();
                        rowData.venteHT = $($cells[3]).text().trim();
                        rowData.tva = $($cells[4]).text().trim();
                        rowData.ventesTTC = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            // Associer la fonction au clic sur le bouton "Imprimer PDF"
            $('#printButtonClient').click(function() {
                // Sélectionner le tableau avec la classe "liste_cleint"
                logTableContentClient();
            });

            $('#printButtonJour').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentJour();
            });

            $('#printButtonCategorie').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentCategory();
            });

            $('#printButtonProduct').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentProduct();
            });

            $('#printButtonAgence').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentAgence();
            });

            $('#printButtonUser').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentUser();
            });

            $('#printButtonJournalVente').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentJournalVente();
            });

            $('#printButtonFactureAvoir').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentFactureAvoir();
            });
        });


        $(document).ready(function() {

            function sendRequest(reponse, tableData, jsEntete) {
                // Convertir les données du tableau en JSON
                var jsonData = JSON.stringify({
                    reponse: reponse, // Ajouter la variable 'reponse' à l'objet JSON
                    tableData: tableData, // Ajouter les données du tableau
                    jsEntete: jsEntete // Ajouter les données du tableau
                }, null, 2);

                console.log('jsonData', jsonData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'export-vente-quantite',
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
                    value: jsonData
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            function logTableContentClient() {
                var $table = $('.list_client');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"
                if (reponse === 'list_cumule_by_client') {
                    // console.log('Je suis bien là');

                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.raisonSociale = $($cells[0]).text().trim();
                        rowData.ventesHT = $($cells[1]).text().trim();
                        rowData.tva = $($cells[2]).text().trim();
                        rowData.ventesTTC = $($cells[3]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                    sendRequest(reponse, tableData);

                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
            }

            function logTableContentJour() {
                var $table = $('.list_day');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_day') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.periode = $($cells[0]).text().trim();
                        rowData.totalHT = $($cells[1]).text().trim();
                        rowData.tva = $($cells[2]).text().trim();
                        rowData.totalTTC = $($cells[3]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentCategory() {
                var $table = $('.list_category');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');
                var categorie = $('#categorie').val();
                var agence = $('#agence_').val();
                var client = $('#client_').val();
                var start_date_1 = $('#start_date_1').val();
                var end_date = $('#end_date').val();

                var json = {
                    "categorie": categorie,
                    "agence": agence,
                    "client": client,
                    "start_date_1": start_date_1,
                    "end_date": end_date
                }

                // console.log('jsonEntete', jsonEntete);
                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_category') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log(reponse)

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];
                    var jsEntete = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.libelle = $($cells[0]).text().trim();
                        rowData.client = $($cells[1]).text().trim();
                        rowData.quantite = $($cells[2]).text().trim();
                        rowData.agence = $($cells[3]).text().trim();
                        rowData.magasin = $($cells[4]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });

                    jsEntete.push(json)

                    console.log(tableData, json)
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData, jsEntete);
            }

            function logTableContentProduct() {
                var $table = $('.list_product');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');
                var produit = $('#produit').val();
                var agence = $('#agence').val();
                var client = $('#client_').val();
                var start_date_1 = $('#start_date_1').val();
                var end_date = $('#end_date').val();

                var json = {
                    "produit": produit,
                    "agence": agence,
                    "client": client,
                    "start_date_1": start_date_1,
                    "end_date": end_date
                }

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_product') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];
                    var jsEntete = [];


                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.reference = $($cells[0]).text().trim();
                        rowData.designation = $($cells[1]).text().trim();
                        rowData.client = $($cells[2]).text().trim();
                        rowData.quantite = $($cells[3]).text().trim();
                        rowData.agence = $($cells[4]).text().trim();
                        rowData.magasin = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                    jsEntete.push(json)

                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData, jsEntete);

            }

            function logTableContentAgence() {
                var $table = $('.list_agence');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');
                var produit = $('#produit').val();
                var agence = $('#agence').val();
                var client = $('#client_').val();
                var start_date_1 = $('#start_date_1').val();
                var end_date = $('#end_date').val();

                var json = {
                    "produit": produit,
                    "agence": agence,
                    "client": client,
                    "start_date_1": start_date_1,
                    "end_date": end_date
                }

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_cumule_by_agence') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];
                    var jsEntete = [];


                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.libelle = $($cells[0]).text().trim();
                        rowData.quantite = $($cells[1]).text().trim();
                        rowData.venteHT = $($cells[2]).text().trim();
                        rowData.tva = $($cells[3]).text().trim();
                        rowData.ventesTTC = $($cells[4]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                    jsEntete.push(json)

                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData, jsEntete);

            }

            function logTableContentUser() {
                var $table = $('.list_user');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'sale_by_user') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date_debut = $($cells[0]).text().trim();
                        rowData.date_fin = $($cells[1]).text().trim();
                        rowData.reference = $($cells[2]).text().trim();
                        rowData.client = $($cells[3]).text().trim();
                        rowData.vente = $($cells[4]).text().trim();
                        rowData.avoir = $($cells[5]).text().trim();
                        rowData.agence = $($cells[6]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentJournalVente() {
                var $table = $('.journal_vente');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'journal_vente') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date = $($cells[0]).text().trim();
                        rowData.ref_facture = $($cells[1]).text().trim();
                        rowData.venteHT = $($cells[2]).text().trim();
                        rowData.tva = $($cells[3]).text().trim();
                        rowData.aib = $($cells[4]).text().trim();
                        rowData.ventesTTC = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            function logTableContentFactureAvoir() {
                var $table = $('.list_facture_avoir');
                // Récupérer l'ID du tableau et le stocker dans la variable 'reponse'
                var reponse = $table.attr('id');

                // console.log('ID du tableau:', reponse);

                // Vérifier si l'ID du tableau est égal à "list_cumule_by_client"

                if (reponse === 'list_facture_avoir') {
                    // Récupérer toutes les lignes du corps du tableau (tbody)
                    var $rows = $table.find('tbody tr');

                    // console.log('list_cumule_by_day')

                    // Créer une liste pour stocker les données du tableau
                    var tableData = [];

                    // Parcourir chaque ligne et construire l'objet JSON
                    $rows.each(function(index, row) {
                        var $cells = $(row).find('td');
                        var rowData = {};

                        // Définir les clés et les valeurs de l'objet rowData
                        rowData.date = $($cells[0]).text().trim();
                        rowData.reference_ancienneFacture = $($cells[1]).text().trim();
                        rowData.ref_facture = $($cells[2]).text().trim();
                        rowData.venteHT = $($cells[3]).text().trim();
                        rowData.tva = $($cells[4]).text().trim();
                        rowData.ventesTTC = $($cells[5]).text().trim();

                        // Ajouter l'objet rowData à la liste tableData
                        tableData.push(rowData);
                    });
                } else {
                    // Si l'ID du tableau n'est pas "list_cumule_by_client", afficher un message d'erreur ou ignorer l'action
                    console.error('L\'ID du tableau ne correspond pas à "list_cumule_by_client".');
                }
                sendRequest(reponse, tableData);
            }

            // Associer la fonction au clic sur le bouton "Imprimer PDF"
            $('#printButtonClientExport').click(function() {
                // Sélectionner le tableau avec la classe "liste_cleint"
                logTableContentClient();
            });

            $('#printButtonJourExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentJour();
            });

            $('#printButtonCategorieExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentCategory();
            });

            $('#printButtonProductExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentProduct();
            });

            $('#printButtonAgenceExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentAgence();
            });

            $('#printButtonUserExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentUser();
            });

            $('#printButtonJournalVenteExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentJournalVente();
            });

            $('#printButtonFactureAvoirExport').click(function() {
                // Sélectionner le tableau avec la classe "list_day"
                logTableContentFactureAvoir();
            });
        });
    </script>
@endsection
