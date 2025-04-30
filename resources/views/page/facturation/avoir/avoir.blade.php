@extends('layouts.master', ['title' => 'Avoir'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Avoir',
        'infos2' => 'Avoir',
        'infos3' => 'Liste',
    ])

    <style>
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            pointer-events: none;
            /* Empêche les événements de pointer de traverser l'overlay */
        }

        #loadingBar {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            height: 40px;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            text-align: center;
            border-radius: 5px;
            z-index: 9999;
        }

        #loadingBarProgress {
            width: 0;
            height: 4px;
            background-color: #2980b9;
            margin-top: 8px;
            animation: loadingAnimation 2s linear infinite;
        }

        #loadingBar div {
            margin-top: 8px;
        }

        @keyframes loadingAnimation {
            0% {
                width: 0;
            }

            50% {
                width: 100%;
            }

            100% {
                width: 0;
            }
        }
    </style>
    <style>
        .status-annulee {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }

        .status-normalisee {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }

        .status-en-cours {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #ffeeba;
        }

        .status-en-cours-paiement {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #bee5eb;
        }

        .status-solde {
            background-color: #2d6ef0e8;
            color: #ebeff1;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #dcddde;
        }

        .status-invalidee {
            background-color: #f5f5f5;
            color: #6c757d;
            padding: 3px 5px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
    </style>

    <div class="row d-flex text-start ">
        <div class="col text-end">
            @canany(['normaliser-avoir', 'invalider-avoir', 'imprimer-avoir-en-A4', 'imprimer-avoir-en-A5',
                'imprimer-avoir-en-A8'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        @can('normaliser-avoir')
                            <li>
                                <a type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#staticBackdrop4">
                                    Normamiser l'avoir</a>
                            </li>
                        @endcan
                        @can('invalider-avoir')
                            <li>
                                <a type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#staticBackdrop5">
                                    Invalider l'avoir</a>
                            </li>
                        @endcan
                        @can('imprimer-avoir-en-A4')
                            <li>
                                <a id="imprimerA4Link" target="_blank" type="button" class="dropdown-item">
                                    Imprimer A4</a>
                            </li>
                        @endcan
                        @can('imprimer-avoir-en-A5')
                            <li>
                                <a id="imprimerA5Link" target="_blank" type="button" class="dropdown-item">
                                    Imprimer A5</a>
                            </li>
                        @endcan
                        @can('imprimer-avoir-en-A8')
                            <li>
                                <a id="imprimerA8Link" target="_blank" type="button" class="dropdown-item">
                                    Imprimer A8</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>
    <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment normaliser cette facture en avoir ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <a id="normaliserAvoirLink" type="button"><button type="button" class="btn btn-success"
                            data-bs-dismiss="modal">Oui Normaliser en avoir</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment invalider cette facture avoir ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <a id="invaliderAvoirLink" type="button"><button type="button" class="btn btn-success"
                            data-bs-dismiss="modal">Oui invalider</button></a>
                </div>
            </div>
        </div>
    </div>

    <div id="overlay"></div>
    <div id="loadingBar">
        <div id="loadingBarProgress"></div>
        <div>Normalisation en cours...</div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="18"
                            height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l11 0" />
                            <path d="M9 12l11 0" />
                            <path d="M9 18l11 0" />
                            <path d="M5 6l0 .01" />
                            <path d="M5 12l0 .01" />
                            <path d="M5 18l0 .01" />
                        </svg>
                        Liste</button>

                    @can('Voir les impressions des avoirs')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer"
                                width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                            </svg>
                            Impression</button>
                    @endcan
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filterFactureAvoir') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des avoirs</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="proformaTable"
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th width="220px" scope="col">Agence</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">
                                                        Référence</th>
                                                    <th scope="col">
                                                        Statut</th>
                                                    <th scope="col">
                                                        Facture&nbsp;d'origine</th>
                                                    <th scope="col">
                                                        Dénomination&nbsp;sociale</th>
                                                    <th scope="col">
                                                        Objet</th>

                                                    <th scope="col">
                                                        Autres&nbsp;Infos </th>
                                                    <th scope="col">
                                                        Commentaires</th>
                                                    <th scope="col">
                                                        Taux&nbsp;AIB</th>
                                                    <th scope="col">
                                                        Taux&nbsp;AIB&nbsp;Deductible</th>
                                                    <th scope="col">
                                                        Net&nbsp;A&nbsp;payer </th>
                                                    <th scope="col">
                                                        Enregistrer&nbsp;Par </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($listeProforma as $proforma)
                                                    <tr style="cursor:pointer;" class="clickable-row "
                                                        data-url="{{ route('getDetailFactureAvoir', ['id' => $proforma->id]) }}">

                                                        <td>{{ $proforma->Nom_agence }}</td>
                                                        <td width="220px" style="width: 200px;">
                                                            {{ $proforma->created_at->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>{{ $proforma->Reference_facture }}</td>
                                                        <td>
                                                            @if ($proforma->Statut_facture == 'EN COURS')
                                                                <span
                                                                    class="status-en-cours">{{ $proforma->Statut_facture }}</span>
                                                            @endif
                                                            @if ($proforma->Statut_facture == 'NORMALISEE')
                                                                <span
                                                                    class="status-normalisee">{{ $proforma->Statut_facture }}</span>
                                                            @endif

                                                            @if ($proforma->Statut_facture == 'INVALIDEE')
                                                                <span
                                                                    class="status-invalidee">{{ $proforma->Statut_facture }}</span>
                                                            @endif

                                                        </td>
                                                        <td>{{ $proforma->reference_facture_origine }}</td>
                                                        <td>{{ $proforma->Denomination_sociale }}</td>
                                                        <td>{{ $proforma->Objet_facture }}</td>
                                                        <td>{{ $proforma->Autres_infos }}</td>
                                                        <td>{{ $proforma->Commentaire }}</td>
                                                        <td>{{ $proforma->Aib }}</td>
                                                        <td>{{ $proforma->Aib_deductible }}</td>
                                                        <td>{{ $proforma->Net_a_payer }}</td>
                                                        <td>{{ $proforma->Nom_user }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2 d-inline-block text-dark">Détails avoirs</h3>
                                </div>
                                <div class="card-body  responsive-1">
                                    <div class="table-responsive">
                                        <table class="table table-bordered tableInfo2">

                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">
                                                        Réf&nbsp;</th>
                                                    <th scope="col">
                                                        Désignation</th>
                                                    <th scope="col">
                                                        G.&nbsp;T </th>
                                                    <th scope="col">
                                                        PU&nbsp;HT
                                                    </th>
                                                    <th scope="col">
                                                        %&nbsp;Remise
                                                    </th>
                                                    <th scope="col">
                                                        PU&nbsp;NET&nbsp;HT
                                                    </th>
                                                    <th scope="col">
                                                        PU&nbsp;NET&nbsp;TTC</th>
                                                    <th scope="col">
                                                        Qte</th>
                                                    <th scope="col">
                                                        Montant&nbsp;Net&nbsp;HT</th>
                                                    <th scope="col">
                                                        Montant&nbsp;Net&nbsp;TTC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($detailFactures as $detailFacture)
                                                    @php

                                                        $puHT = $detailFacture->Prix_unitaire_HT;
                                                        $puHT_Net = $puHT - $puHT * ($detailFacture->Taux_remise / 100);
                                                        $puNetTTC = $detailFacture->Prix_revient;
                                                        $MontantNetTTC = $puNetTTC * $detailFacture->Qte;

                                                        $MontantNetHT =
                                                            $MontantNetTTC / (1 + $detailFacture->valeur_taxe / 100);
                                                    @endphp
                                                    <tr>
                                                        @if ($detailFacture->is_emballage == 1)
                                                            <td>{{ $detailFacture->Reference_emballage }}</td>
                                                            <td>{{ $detailFacture->Nom_emballage }}</td>
                                                        @else
                                                            <td>{{ $detailFacture->Reference }}</td>
                                                            <td>{{ $detailFacture->Designation }}</td>
                                                        @endif
                                                        <td>{{ $detailFacture->Code_lettre }}</td>
                                                        <td>{{ number_format($detailFacture->Prix_unitaire_HT, 0, ',', ' ') }}
                                                        </td>
                                                        <td>{{ number_format($detailFacture->Taux_remise, 0, ',', ' ') }}
                                                        </td>
                                                        <td>{{ number_format($puHT_Net, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($puNetTTC), 0, ',', ' ') }}</td>
                                                        <td>{{ number_format($detailFacture->Qte, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($MontantNetHT), 0, ',', ' ') }} </td>
                                                        <td>{{ number_format(intval($MontantNetTTC), 0, ',', ' ') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">Aucune donnée (Selectionner
                                                            une
                                                            facture)</td>
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

            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">

        <div class="card my-5">
            <div class="card-body">
                <div class="row">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">
                            <h4>Liste des factures avoir sur une période</h4>
                        </legend>
                        <div class="col-md-10 offset-md-1">
                            <form id="listeFacturePeriodeForm" action="{{ route('avoirByPeriode') }}" method="GET"
                                class="row gx-3 gy-2 d-flex align-items-center justify-content-center">
                                @csrf
                                <div class="col-md-3">
                                    <label class="" id="inputGroup-sizing-sm">Agence</label>
                                    <select name="agence" type="text" class="form-select js-single  w-100"
                                        style="width: 100%;" id="agence4">
                                        @if ($listeAgence->count() > 1)
                                            <option value="Tous">Tous</option>
                                        @endif

                                        @foreach ($listeAgence as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                {{ $value->NomAgence }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="inputGroup-sizing-sm">Debut</label>
                                    <input type="datetime-local" name="date_debut" id="dateDebut" class="form-control"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
                                        max="{{ date('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="inputGroup-sizing-sm">Fin</label>
                                    <input type="datetime-local" name="date_fin" id="dateFin" class="form-control"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
                                        max="{{ date('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="inputGroup-sizing-sm">Client</label>
                                    <select name="client" type="text" class="form-select js-single w-100"
                                        style="width: 100%;" id="client" required>
                                        <option value="Tous">Tous</option>
                                        @forelse ($listeClient as $client)
                                            <option value="{{ $client->id }}">{{ $client->Denomination_sociale }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="fw-bold">Statut facture</label>
                                    <div class="d-flex align-content-start flex-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="statut[]"
                                                value="TOUS" id="checkbox1">
                                            <label class="form-check-label" for="checkbox1">Tous</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="statut[]"
                                                value="EN COURS" id="checkbox2">
                                            <label class="form-check-label" for="checkbox2">En cours</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="statut[]"
                                                value="INVALIDEE" id="checkbox3">
                                            <label class="form-check-label" for="checkbox3">Invalidées</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="statut[]"
                                                value="NORMALISEE" id="checkbox4">
                                            <label class="form-check-label" for="checkbox4">Normalisées</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div align="right">
                                        <button id="AppliquerForm3" type="submit" class="btn text-white  mt-4"
                                            style="{{ background_color_1() }}">Appliquer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="dateError3" style="display: none; color: red;">La date de début doit être antérieure à la
                            date de fin.</div>

                    </fieldset>
                </div>
            </div>
        </div>
        @can('exporter-excel-avoirs')
            <button id="exportButton3" data-bs-toggle="modal" data-bs-target="#staticBackdropImp"
                class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
        @endcan

        @can('imprimer-avoirs')
            <button id="exportButton3P" data-bs-toggle="modal" data-bs-target="#staticBackdropImpP"
                class="btn mb-2 text-white" style="{{ background_color_2() }}">Exporter en PDF</button>
        @endcan

        <div class="card mb-8">
            <div class="car'd-header" style="{{ background_color_2() }}">
                <h3 class="mt-2  d-inline-block text-dark">Liste des factures avoir par période</h3>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="table-responsive">
                        <table class="tableInfo2 " id="tableFactureParPeriode" style="border: 1px solid #ddd">
                            <thead class="">
                                <tr>
                                    <th style="width: 20px; font-size: 18px;" scope="col">#
                                    </th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">
                                        Date</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Ref</th>
                                    <th style="width: 300px; font-size: 18px;" scope="col">
                                        Dénomination&nbsp;sociale</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Aib</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Aib à déduire</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Total TVA</th>
                                    <th style="width: 200px; font-size: 18px;" scope="col">Montant HT</th>

                                    <th style="width: 200px; font-size: 18px;" scope="col">
                                        Net&nbsp;à&nbsp;payer</th>

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
        <br>
        <br>
        <div class="modal fade" id="staticBackdropImp" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment exporter les données actuels ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        <button id="exportExcel_Imp" class="btn btn-sm btn-success">Oui Exporter en Excel</button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment exporter les données en PDF ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        <button id="exportPDF_Imp" target="_blank" class="btn btn-sm btn-success">Oui Exporter en
                            PDF</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    </div>
    </div>
    <script>
        // Liste des IDs de formulaire à cibler
        const formIds = ['listeFacturePeriodeForm'];

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
        document.getElementById('normaliserAvoirLink').addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du lien
            var loadingBar = document.getElementById('loadingBar');
            var overlay = document.getElementById('overlay');
            var loadingBarProgress = document.getElementById('loadingBarProgress');

            overlay.style.display = 'block';
            loadingBar.style.display = 'block';

            loadingBarProgress.style.animation = 'loadingAnimation 2s linear infinite';

            // Soumet le formulaire ou redirige l'utilisateur après un délai simulé
            setTimeout(function() {
                window.location.href = event.target.closest('a').href;
            }, 500);
        });
    </script>
    <script>
        // Soumettre le formulaire automatiquement quand un mois est sélectionné
        document.getElementById('monthSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Vous pouvez également ajouter un événement similaire pour la sélection d'une année si vous le souhaitez
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

                var normaliserAvoirUrl = "{{ route('normaliserAvoir', ['id' => ':proformaId']) }}";
                var avoirFactureUrl = "{{ route('avoirFacture', ['id' => ':proformaId']) }}";
                var invaliderAvoirFactureUrl = "{{ route('invaliderAvoirFacture', ['id' => ':proformaId']) }}";
                var imprimerA4Url = "{{ route('generatePDFAVOIRA4', ['id' => ':proformaId', 'format' => 'a4']) }}";
                var imprimerA5Url = "{{ route('generatePDFAVOIRA5', ['id' => ':proformaId', 'format' => 'a5']) }}";
                var imprimerA8Url = "{{ route('generatePDFAVOIRA8', ['id' => ':proformaId', 'format' => 'a8']) }}";
                normaliserAvoirUrl = normaliserAvoirUrl.replace(':proformaId', proformaId);
                avoirFactureUrl = avoirFactureUrl.replace(':proformaId', proformaId);
                invaliderAvoirFactureUrl = invaliderAvoirFactureUrl.replace(':proformaId', proformaId);
                imprimerA4Url = imprimerA4Url.replace(':proformaId', proformaId, ':format', 'a4');
                imprimerA5Url = imprimerA5Url.replace(':proformaId', proformaId, ':format', 'a5');
                imprimerA8Url = imprimerA8Url.replace(':proformaId', proformaId, ':format', 'a8');
                // Mettre à jour l'attribut href du lien
                $('#normaliserAvoirLink').attr('href', normaliserAvoirUrl);
                $('#avoirFactureLink').attr('href', avoirFactureUrl);
                $('#invaliderAvoirLink').attr('href', invaliderAvoirFactureUrl);
                $('#imprimerA4Link').attr('href', imprimerA4Url);
                $('#imprimerA5Link').attr('href', imprimerA5Url);
                $('#imprimerA8Link').attr('href', imprimerA8Url);
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


    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 3000px;
        }

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }

        /*   .tableInfo2 {
                        width: 1500px;
                    } */
        .tableInfo2 {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 100%;
        }

        .tableInfo2 th,
        .tableInfo2 td {

            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }


        @media (min-width: 1600px) {
            .tableInfo2 {
                width: 100%;
            }

            th {
                width: auto;
            }
        }
    </style>
    {{-- Impression --}}

    <script>
        $(document).ready(function() {
            var exportButton3 = document.getElementById("exportButton3");
            var exportButton3P = document.getElementById("exportButton3P");
            var table = document.getElementById("tableFactureParPeriode").getElementsByTagName("tbody")[0];

            // Déclaration d'une variable globale pour stocker la réponse AJAX
            var ajaxResponse;

            // Fonction pour vérifier si le tableau est vide
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

            // Appel la fonction de vérification au chargement de la page
            checkTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#listeFacturePeriodeForm').on('submit', function(e) {
                e
                    .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                var formData = $(this).serialize(); // Sérialisation des données du formulaire

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
                        $('#tableFactureParPeriode tbody').empty();

                        let counter = 1;
                        let grandTotalNetAPayer = 0;
                        let totalFactures = 0;
                        // Parcourir les données et insérer les lignes dans le tableau
                        for (const [statut, factures] of Object.entries(response
                                .listeProformaParStatut)) {
                            // Ajouter une ligne pour le statut
                            const statutRow = `<tr class="text-white" style="background-color: #3a82db;">
                                <td colspan="9">${statut}</td>
                            </tr>`;
                            $('#tableFactureParPeriode tbody').append(statutRow);

                            if (factures.length === 0) {
                                const noFactureRow = `<tr>
                                    <td colspan="9" class="text-center">Aucune facture pour ce statut</td>
                                </tr>`;
                                $('#tableFactureParPeriode tbody').append(noFactureRow);
                            } else {
                                let totalNetAPayer = 0;
                                factures.forEach(facture => {
                                    const nap = parseFloat(facture.Net_a_payer);
                                    totalNetAPayer += nap;
                                    grandTotalNetAPayer += nap;
                                    totalFactures++;
                                    const row = `<tr>
                                        <td>${counter++}</td>
                                        <td>${formatDate(facture.Date_facture)}</td>
                                        <td>${facture.Reference_facture} <p class="text-secondary p-1"> Ref.O: ${facture.Reference_origine}</p> </td>
                                        <td>${facture.Denomination_sociale}</td>
                                        <td>${facture.Aib}</td>
                                        <td>${numberFormat(facture.Aib_deductible.toFixed(0))}</td>
                                        <td>${numberFormat(facture.TotalGlobalTVA.toFixed(0))}</td>
                                        <td>${numberFormat(facture.TotalGlobalHT.toFixed(0))}</td>
                                        <td>${numberFormat(nap)}</td>
                                    </tr>`;
                                    $('#tableFactureParPeriode tbody').append(row);
                                });

                                const totalRow = `<tr>
                                    <td colspan="8" class="">Total</td>
                                    <td>${numberFormat(totalNetAPayer)}</td>
                                </tr>`;
                                $('#tableFactureParPeriode tbody').append(totalRow);
                            }
                        }
                        const grandTotalRow = `<tr class="font-weight-bold">
                            <td colspan="7" class="text-right">Total général</td>
                            <td class="text-right">(${totalFactures} factures)</td>
                            <td>${numberFormat(grandTotalNetAPayer.toFixed(0))}</td>
                        </tr>`;
                        $('#tableFactureParPeriode tbody').append(grandTotalRow);
                        checkTable();
                        $button.removeClass('loading'); // Retire la classe .loading du bouton
                        $button.prop('disabled', false); // Réactive le bouton
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr.responseText);
                        $button.removeClass('loading'); // Retire la classe .loading du bouton
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

                    var tableFactureParPeriodeData = [];
                    $('#tableFactureParPeriode tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            count: row.find('td').eq(0).text(),
                            Date_facture: row.find('td').eq(1).text(),
                            Reference_facture: row.find('td').eq(2).text(),
                            Denomination_sociale: row.find('td').eq(3).text(),
                            Aib: row.find('td').eq(4).text(),
                            Aib_deductible: row.find('td').eq(5).text(),
                            TotalGlobalTVA: row.find('td').eq(6).text(),
                            TotalGlobalHT: row.find('td').eq(7).text(),
                            Net_a_payer: row.find('td').eq(8).text(),
                        };
                        tableFactureParPeriodeData.push(rowData);
                    });

                    var exportData = {
                        tableFactureParPeriodeData: tableFactureParPeriodeData,
                        // infoMagasin: ajaxResponse.infoMagasin,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence,
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_impression_facture_avoir_periode',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            });
                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'statistique_stock_consolide.xlsx';
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

                var tableFactureParPeriodeData = [];
                $('#tableFactureParPeriode tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        count: row.find('td').eq(0).text(),
                        Date_facture: row.find('td').eq(1).text(),
                        Reference_facture: row.find('td').eq(2).text(),
                        Denomination_sociale: row.find('td').eq(3).text(),
                        Aib: row.find('td').eq(4).text(),
                        Aib_deductible: row.find('td').eq(5).text(),
                        TotalGlobalTVA: row.find('td').eq(6).text(),
                        TotalGlobalHT: row.find('td').eq(7).text(),
                        Net_a_payer: row.find('td').eq(8).text(),
                    };
                    tableFactureParPeriodeData.push(rowData);
                });

                var exportData = {
                    tableFactureParPeriodeData: tableFactureParPeriodeData,
                    dateDebut: ajaxResponse.dateDebut,
                    dateFin: ajaxResponse.dateFin,
                    infoClient: ajaxResponse.infoClient,
                    infoAgence: ajaxResponse.infoAgence
                };


                $.ajax({
                    type: 'POST',
                    url: '/export_impression_facture_avoir_periode_pdf',
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

        });
    </script>

    @include('layouts.alert')
@endsection
