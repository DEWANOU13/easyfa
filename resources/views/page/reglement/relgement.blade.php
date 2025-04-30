@extends('layouts.master', ['title' => 'Règlement'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Règlement',
        'infos2' => 'Règlement',
        'infos3' => 'Liste',
    ])

    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @canany(['enregistrer-reglement', 'annuler-reglement', 'imprimer-reglement'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('enregistrer-reglement')
                            <li><a href="{{ route('showFormReglement') }}" class="dropdown-item" type="button">Nouveau</a></li>
                        @endcan
                        @can('annuler-reglement')
                            <li><button class="dropdown-item" id="showModal" type="button">Annuler le règlement</button></li>
                        @endcan
                        @canany(['imprimer-reglement', 'exporter-reglement'])
                            <form id="impressionFormA4" action="{{ route('impression-reglement-A4') }}" method="POST"
                                target="_blank">
                                @csrf
                                <input hidden type="text" value="A4" name="type">
                                <input hidden type="text" name="reponse" id="reponse">
                                @can('imprimer-reglement')
                                    <li><button class="dropdown-item" name="value" value="imprimer" id="imprimer-button"
                                            type="submit">Impression</button></li>
                                @endcan
                                @can('exporter-reglement')
                                    <li><button class="dropdown-item" name="value" value="exporter" id="imprimer-button"
                                            type="submit">Exporter</button></li>
                                @endcan
                            </form>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Vous êtes sur le point d'annuler des règlements. Cette opération est irréversible <br>
                    <strong><em>Voulez-vous continuer?</em></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="modifierBtn">Continue</button>
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

                    @can('voir-impression-reglement')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Impression</button>
                    @endcan

                    @can('voir-impression-reglement-periode')
                        <button class="nav-link" id="nav-liste-tab" data-bs-toggle="tab" data-bs-target="#nav-liste"
                            type="button" role="tab" aria-controls="nav-liste" aria-selected="false">Liste
                            reglement</button>
                    @endcan

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filterReglement') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des règlements</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="proformaTable"
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapsed ; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">
                                                        Date
                                                    </th>
                                                    <th scope="col">
                                                        N°Règlement</th>
                                                    <th scope="col">
                                                        Code
                                                        Client</th>
                                                    <th scope="col">
                                                        Dénomination Sociale</th>
                                                    <th scope="col">
                                                        Montant règlement</th>
                                                    <th scope="col">
                                                        Statut Opération</th>
                                                    <th scope="col">Agence</th>
                                                    <th scope="col">
                                                        Observation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reglements as $reglement)
                                                    <tr style="cursor:pointer" class="clickable-row"
                                                        data-url="{{ route('get.reglement_for_detail_reglement', ['id' => $reglement->id]) }}"
                                                        data-id="{{ $reglement->id }}">
                                                        <td class="entree-produit">
                                                            @if (\Carbon\Carbon::hasFormat($reglement->Date_Reglement, 'Y-m-d H:i:s'))
                                                                {{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}
                                                            @else
                                                                {{ $reglement->Date_Reglement }}
                                                            @endif
                                                        </td>
                                                        <td class="entree-produit">{{ $reglement->Reference_Reglement }}
                                                        </td>

                                                        <td class="entree-produit">{{ $reglement->Code_client }}</td>
                                                        <td class="entree-produit">{{ $reglement->Denomination_sociale }}
                                                        </td>
                                                        <td class="entree-produit">{{ $reglement->Montant_Regle }}</td>
                                                        <td class="entree-produit fw-bold text-indigo-700 ">
                                                            {{ $reglement->Statut_Operation }}</td>
                                                        <td class="entree-produit">{{ $reglement->NomAgence }}
                                                        </td>
                                                        <td class="entree-produit">{{ $reglement->Observations }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">
                                                            <h6 class="mt-3 text-center">Aucune donnée pour
                                                                l'instant.</h6>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Détails règlements</h3>
                                </div>
                                <div class="card-body responsive-1">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered dt-responsive nowrap"
                                            id="">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">
                                                    </th>
                                                    <th scope="col">
                                                        N°
                                                        Facture</th>
                                                    <th scope="col">
                                                        Net
                                                        à payer</th>
                                                    <th scope="col">
                                                        Mode
                                                        de Règlement</th>
                                                    <th scope="col">
                                                        Montant réglé</th>
                                                    <th scope="col">
                                                        Compensation</th>
                                                    <th scope="col">
                                                        Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($detail_reglements as $detail_reglement)
                                                    <tr style="cursor:pointer" id="clickable-row2">
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="flexRadioDefault" id="flexRadioDefault2"
                                                                    value="{{ $detail_reglement->id }}">
                                                            </div>
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $detail_reglement->Reference_facture }}
                                                        </td>
                                                        <td class="entree-produit">{{ $detail_reglement->Net_a_payer }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $detail_reglement->Libelle_Operation }}
                                                        </td>
                                                        <td class="entree-produit">{{ $detail_reglement->Montant_Regle }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            @if ($detail_reglement->Compensation == 0)
                                                                <div class="form-check">
                                                                    <input class="form-check-input border border-3"
                                                                        type="checkbox" value=""
                                                                        id="flexCheckChecked" disabled>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="entree-produit">
                                                            @if ($detail_reglement->Statut_Reglement == 1)
                                                                <div class="form-check">
                                                                    <input class="form-check-input border-3"
                                                                        type="checkbox" value=""
                                                                        id="flexCheckChecked" checked disabled>
                                                                </div>
                                                            @endif
                                                            @if ($detail_reglement->Statut_Reglement == 0)
                                                                <div class="form-check">
                                                                    <input class="form-check-input border-3"
                                                                        type="checkbox" value=""
                                                                        id="flexCheckChecked" disabled>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8">
                                                            <h6 class="mt-3 text-center">Aucune donnée pour
                                                                l'instant.</h6>
                                                        </td>
                                                    </tr>
                                                @endforelse
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
                    <form action="{{ route('script') }}" method="POST">
                        @csrf
                        {{--                         <button type="submit" class="btn btn-sm btn-danger">Script</button>
                        --}}
                    </form>
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Liste des règlements sur une période</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form action="{{ route('get.imprimerReglement') }}" method="POST" target="_blank"
                                            class="row gx-3 gy-2 ">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="form-label" id="inputGroup-sizing-sm">Debut</label>
                                                <input name="debut_periode" type="datetime-local" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="<?php echo date('Y-m-d', strtotime('-5 days')); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" id="inputGroup-sizing-sm">Fin</label>
                                                <input name="fin_periode" type="datetime-local" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="client">Client</label><br>
                                                <select name="client" type="text" class="form-select js-single w-100 " style="width: 100%;"
                                                    id="client-input" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    {{-- <option value="">Sélectionnez un client</option> --}}
                                                    <option value="Tous">Tous</option>
                                                    @foreach ($clients as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->Denomination_sociale }} ({{ $value->Numero_ifu }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Le client est obligatoire</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" id="produit">Mode règlement</label>
                                                <select name="mode_reglement" type="text" class="form-select js-single" style="width: 100%;"
                                                    id="mode_reglement" value="{{ old('mode_reglement') }}"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    {{-- <option value="">Mode de réglement</option> --}}
                                                    <option value="Tous">Tous</option>
                                                    @foreach ($mode_paiements as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->Libelle_Operation }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" id="agence_">Agence</label>
                                                <select name="agence" type="text" class="form-select js-single" id="agence_" style="width: 100%;"
                                                    value="{{ old('agence') }}" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    {{-- <option value="">Mode de réglement</option> --}}
                                                    {{-- <option value="Toutes">Toutes</option> --}}
                                                    @if (session()->get('site_id') == '1')
                                                        <option value="Toutes">Toutes</option>
                                                    @endif
                                                    @foreach ($listeAgence as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationTextarea" class="form-label fw-bold">Statut</label>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        id="flex1" checked value="1">
                                                    <label class="form-check-label" for="flex1">
                                                        La liste des règlements par client et par mode de paiement sur la
                                                        période
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        value="2" id="flex2">
                                                    <label class="form-check-label" for="flex2">
                                                        La liste des règlements par mode de paiement et par client sur la
                                                        période
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="statut" class="form-check-input" type="radio"
                                                        value="3" id="flex3">
                                                    <label class="form-check-label" for="flex3">
                                                        La liste des règlements par mode de paiement sur la période
                                                    </label>
                                                </div>


                                            </div>

                                            <div class="row ">
                                                <div class="col-md-6 d-flex ">
                                                    @can('exporter-excel-impression-reglement')
                                                        <div class="mt-4 me-3">
                                                            <button type="submit" name="reponse" value="imprimer"
                                                                class="btn text-white"
                                                                style="{{ background_color_2() }}">Imprimer en PDF</button>
                                                        </div>
                                                    @endcan

                                                    @can('imprimer-impression-reglement')
                                                        <div class="mt-4">
                                                            <button type="submit" name="reponse" value="exporter"
                                                                class="btn text-white"
                                                                style="{{ background_color_1() }}">Exporter en Excel</button>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-liste" role="tabpanel" aria-labelledby="nav-liste-tab"
                    tabindex="0">
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Liste des règlements sur une période</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form id="reglementParPeriodeForm"
                                            action="{{ route('imprimerReglement_periode') }}" method="GET"
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="" id="inputGroup-sizing-sm">Agence</label>
                                                <select name="agence" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="agence4" required>

                                                    @if (session()->get('site_id') == '1')
                                                        <option value="Tous">Toutes</option>
                                                    @endif
                                                    @forelse ($listeAgence as $agence)
                                                        <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="inputGroup-sizing-sm">Debut</label>
                                                <input type="datetime-local" name="date_debut" id="dateDebut"
                                                    class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="inputGroup-sizing-sm">Fin</label>
                                                <input type="datetime-local" name="date_fin" id="dateFin"
                                                    class="form-control" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label" for="inputGroup-sizing-sm">Client</label>
                                                <div>

                                                    <select name="client" type="text"
                                                        class="form-select js-single w-100" style="width: 100%"
                                                        id="client1" required>
                                                        <option value="Tous">Tous</option>
                                                        @forelse ($clients as $client)
                                                            <option value="{{ $client->id }}">
                                                                {{ $client->Denomination_sociale }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 ">
                                                <div align="right">

                                                    <button id="AppliquerForm3" type="submit"
                                                        class="btn text-white  mt-4"
                                                        style="{{ background_color_1() }}">Appliquer</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="dateError3" style="display: none; color: red;">La date de début doit être
                                        antérieure à la date de fin.</div>

                                </fieldset>
                            </div>
                        </div>
                    </div>

                    @can('exporter-excel-impression-reglement-periode')
                        <button id="exportButton3" data-bs-toggle="modal" data-bs-target="#staticBackdropImp"
                            class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('imprimer-impression-reglement-periode')
                        <button id="exportButton3P" data-bs-toggle="modal" data-bs-target="#staticBackdropImpP"
                            class="btn mb-2 text-white" style="{{ background_color_2() }}">Exporter en PDF</button>
                    @endcan

                    <div class="card m-b-30 mb-5">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des factures</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="table-responsive">
                                    <table class="tableInfo2 " id="tableReglementParPeriode"
                                        style="border: 1px solid #ddd">
                                        <thead class="">
                                            <tr>
                                                <th style="width: 20px; font-size: 18px;" scope="col">#
                                                </th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">
                                                    Date</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Ref</th>
                                                <th style="width: 300px; font-size: 18px;" scope="col">
                                                    Dénomination&nbsp;sociale</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Statut opération
                                                </th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Agence</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Montant réglé
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <div id="req_message3" style="text-align: center; color: red; display: none;">
                                    Veuillez faire une requette pour afficher les données
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="staticBackdropImp" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données actuels ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportExcel_Imp" class="btn btn-sm btn-success">Oui Exporter en
                                        Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="staticBackdropImpP" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données en PDF ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPDF_Imp" target="_blank" class="btn btn-sm btn-success">Oui
                                        Exporter en
                                        PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <style>
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

        .selected-row {
            background-color: #d84b4b;
            /* Ou la couleur de votre choix */
        }
    </style>
    @includeIf('layouts.alert')
    <script>
        $(document).ready(function() {
            // Désactiver les boutons par défaut
            $('#imprimer-button').prop('disabled', true);
            $('#imprimer-button5').prop('disabled', true);
            $('#imprimer-button8').prop('disabled', true);

            // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
            $('.clickable-row').click(function() {
                // Activer les boutons
                $('#imprimer-button').prop('disabled', false);
                $('#imprimer-button5').prop('disabled', false);
                $('#imprimer-button8').prop('disabled', false);
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

            // Lorsque le bouton d'impression est cliqué
            $('#showModal').on('click', function() {
                // Vérifiez si une ligne a été sélectionnée
                if ($('#reponse').val() !== '') {
                    // Soumettez le formulaire
                    $('#impressionForm').submit();
                } else {
                    alert('Veuillez sélectionner un règlement avant de procéder à l\'impression.');
                }
            });
        });

        $(document).ready(function() {
            $('#showModal').click(function() {
                var selectedId = $('input[name="flexRadioDefault"]:checked').val();
                if (selectedId !== 'on') {
                    $('#confirmationModal').modal('show');
                } else {
                    alert("Veuillez sélectionner une ligne à modifier.");
                }
            })
        });

        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');


                var reglementId = $(this).data('id');
                //   console.log(reglementId)
                // Placez l'ID dans l'input caché
                $('#reponse').val(reglementId);

            });
        });

        // $(document).ready(function() {
        //     $('.entree-produit').on('click', function() {
        //         // Supprimez la classe 'selected-row' de toutes les lignes
        //         $('.entree-produit').removeClass('selected-row');
        //         // Ajoutez la classe 'selected-row' à la ligne cliquée
        //         $(this).addClass('selected-row');
        //         // Trouvez la case à cocher à l'intérieur de la ligne et la cochez
        //         $(this).find('input[type="radio"]').prop('checked', true);
        //     });
        // });


        $(document).ready(function() {
            $('#modifierBtn').click(function() {
                var selectedId = $('input[name="flexRadioDefault"]:checked').val();
                // console.log(selectedId);
                if (selectedId !== 'on') {
                    $.ajax({
                        url: "{{ route('modifier_statut_detail_reglement') }}",
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
                                setTimeout(() => {
                                    window.location.reload();
                                }, 4000);
                                $('#confirmationModal').modal('hide');
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
                                $('#confirmationModal').modal('hide');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    alert("Veuillez sélectionner une ligne de detail réglement à modifier.");
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
        function updateTable(reglements) {
            var tbody = document.querySelector('#proformaTable tbody');
            tbody.innerHTML = generateTableRows(reglements);
        }

        function generateTableRows(reglements) {
            return reglements.map(reglement => {
                return `
                <tr style="cursor:pointer;" class="clickable-row" data-url="/get-detail-reglement/${reglement.id}">
                    <td hidden>${reglement.id}</td>
                    <td>${reglement.Date_Reglement}</td>
                    <td>${reglement.Reference_Reglement}</td>
                    <td>${reglement.Code_client}</td>
                    <td>${reglement.Denomination_sociale}</td>
                    <td>${reglement.Montant_Regle}</td>
                    <td>${reglement.Statut_Operation}</td>
                    <td>${reglement.Observations}</td>

                </tr>
            `;
            }).join('');
        }

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
    {{-- Impression --}}

    <script>
        $(document).ready(function() {
            var exportButton3 = document.getElementById("exportButton3");
            var exportButton3P = document.getElementById("exportButton3P");
            var table = document.getElementById("tableReglementParPeriode").getElementsByTagName("tbody")[0];

            var ajaxResponse;

            function checkTable() {
                if (table.rows.length === 0) {
                    exportButton3.disabled = true;
                    exportButton3P.disabled = true;
                    $('#req_message3').show();
                } else {
                    exportButton3.disabled = false;
                    exportButton3P.disabled = false;
                    $('#req_message3').hide();
                }
            }

            function numberFormat(value) {
                return Number(value).toLocaleString('fr-FR');
            }

            checkTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#reglementParPeriodeForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var dateDebut = new Date($('#dateDebut').val());
                var dateFin = new Date($('#dateFin').val());

                if (dateDebut > dateFin) {
                    $('#dateError3').show();
                    return;
                } else {
                    $('#dateError3').hide();
                }
                var $button = $('#AppliquerForm3');
                $button.addClass('loading');
                $button.prop('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'),
                    data: formData,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {

                        ajaxResponse = response;
                        console.log(response);
                        $('#tableReglementParPeriode tbody').empty();

                        let counter = 1;
                        let grandTotal = 0;
                        let total_annuler = 0;

                        response.listeReglement.forEach(function(reglement) {

                            if (reglement.Statut_Operation == 'EFFECTUE') {
                                const nap = parseFloat(reglement.Montant_Regle);
                                grandTotal += nap;
                            }
                            if (reglement.Statut_Operation == 'ANNULE') {
                                const montantannuler = parseFloat(reglement
                                    .Montant_Regle);
                                total_annuler += montantannuler;
                            }

                            const row = `<tr>
                                            <td>${counter++}</td>
                                            <td>${formatDate(reglement.Date_Reglement)}</td>
                                            <td>${reglement.Reference_Reglement}</td>
                                            <td>${reglement.Denomination_sociale}</td>
                                            <td>${reglement.Statut_Operation}</td>
                                            <td>${reglement.NomAgence}</td>
                                            <td>${numberFormat(reglement.Montant_Regle)}</td>
                                        </tr>`;
                            $('#tableReglementParPeriode tbody').append(row);
                        });

                        const grandTotalRow = `<tr class="font-weight-bold">
                                <td colspan="6" >Total général</td>
                                <td >${numberFormat(grandTotal - total_annuler)}</td>
                            </tr>`;
                        $('#tableReglementParPeriode tbody').append(grandTotalRow);


                        checkTable();
                        $button.removeClass('loading'); // Retire la classe .loading du bouton
                        $button.prop('disabled', false); // Réactive le bouton
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $button.removeClass('loading');
                        $button.prop('disabled', false); // Réactive le bouton
                    }
                });
            });

            $('#exportExcel_Imp').off('click').on('click', function() {
                var $button = $(this);
                if (ajaxResponse) {
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableReglementParPeriodeData = [];
                    $('#tableReglementParPeriode tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            count: row.find('td').eq(0).text(),
                            Date_Reglement: row.find('td').eq(1).text(),
                            Reference_Reglement: row.find('td').eq(2).text(),
                            Denomination_sociale: row.find('td').eq(3).text(),
                            Statut_Operation: row.find('td').eq(4).text(),
                            NomAgence: row.find('td').eq(5).text(),
                            Montant_Regle: row.find('td').eq(6).text(),
                        };
                        tableReglementParPeriodeData.push(rowData);
                    });

                    var exportData = {
                        tableReglementParPeriodeData: tableReglementParPeriodeData,
                        // infoMagasin: ajaxResponse.infoMagasin,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_impression_reglement_periode',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var currentDate = new Date();
                            var formatedDate = formatDateTime(currentDate);

                            var blob = new Blob([response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = `Liste_Reglement_Periode_${formatedDate}.xlsx`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en Excel');
                            $('#staticBackdropImp').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en Excel');
                        }
                    });
                } else {
                    console.error("Aucune donnée disponible pour l'exportation.");
                }
            });

            $('#exportPDF_Imp').off('click').on('click', function() {
                var newWindow = null;

                //chargement
                var $button = $(this);
                $button.addClass('loading');
                $button.prop('disabled', true);
                $button.text('Exportation en cours...');

                var tableReglementParPeriodeData = [];
                $('#tableReglementParPeriode tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        count: row.find('td').eq(0).text(),
                        Date_Reglement: row.find('td').eq(1).text(),
                        Reference_Reglement: row.find('td').eq(2).text(),
                        Denomination_sociale: row.find('td').eq(3).text(),
                        Statut_Operation: row.find('td').eq(4).text(),
                        NomAgence: row.find('td').eq(5).text(),
                        Montant_Regle: row.find('td').eq(6).text(),
                    };
                    tableReglementParPeriodeData.push(rowData);
                });

                var exportData = {
                    tableReglementParPeriodeData: tableReglementParPeriodeData,
                    dateDebut: ajaxResponse.dateDebut,
                    dateFin: ajaxResponse.dateFin,
                    infoClient: ajaxResponse.infoClient,
                    infoAgence: ajaxResponse.infoAgence
                };


                $.ajax({
                    type: 'POST',
                    url: '/export_impression_reglement_periode_pdf',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var url = window.URL.createObjectURL(
                            blob);

                        newWindow = window.open(url, '_blank');

                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        $button.text('Oui, Exporter en PDF');
                        $('#staticBackdropImpP').modal('hide');
                    },

                    error: function(xhr, status, error) {
                        console.error(error);

                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        $button.text('Oui, Exporter en PDF');
                    }
                });

            });

            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

            function formatDateTime(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }


        });
    </script>
@endsection
