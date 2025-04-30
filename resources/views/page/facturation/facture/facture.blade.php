@extends('layouts.master', ['title' => 'Facture'])
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.entete-page', [
        'infos1' => 'Facture',
        'infos2' => 'Facture',
        'infos3' => 'Liste',
    ])


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

    <div class="row d-flex text-start">

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
        <div class="col text-end">
            @canany(['creer-facture', 'normaliser-facture', 'creer-avoir', 'annuler-facture', 'imprimer-facture-en-A4',
                'imprimer-facture-en-A5', 'imprimer-facture-en-A8', 'bordereau-livraison'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        @can('creer-facture')
                            <li><a href="{{ route('nouveauf') }}" class="dropdown-item" type="button">Nouveau</a></li>
                        @endcan
                        @can('normaliser-facture')
                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                                    type="button">Normaliser la facture</a></li>
                        @endcan
                        @can('creer-avoir')
                            <li><a data-bs-toggle="modal" data-bs-target="#staticBackdrop2" class="dropdown-item"
                                    type="button">Convertir en avoir</a></li>
                        @endcan
                        @can('annuler-facture')
                            <li><a data-bs-toggle="modal" data-bs-target="#staticBackdrop3" class="dropdown-item"
                                    type="button">Invalider la facture</a></li>
                        @endcan
                        @can('imprimer-facture-en-A4')
                            <li>
                                <a id="imprimerA4Link" target="_blank" type="button" class="btn bg-light dropdown-item">
                                    Imprimer A4
                                </a>
                            </li>
                        @endcan
                        @can('imprimer-facture-en-A5')
                            <li>
                                <a id="imprimerA5Link" target="_blank" type="button" class="btn bg-light dropdown-item">
                                    Imprimer A5
                                </a>
                            </li>
                        @endcan
                        @can('imprimer-facture-en-A8')
                            <li>
                                <a id="imprimerA8Link" target="_blank" type="button" class="btn bg-light dropdown-item">
                                    Imprimer A8
                                </a>
                            </li>
                        @endcan
                        @can('bordereau-livraison')
                            <li>
                                <a id="bordereauLink" target="_blank" type="button" class="btn bg-light dropdown-item">
                                    Imprimer BL
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment normaliser cette facture ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <a id="normaliserFactureLink" class="text-white"><button type="button" class="btn text-white"
                            style="{{ background_color_2() }}" data-bs-dismiss="modal"> Oui Normaliser la
                            facture</button></a>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment convertir cette facture en facture d'avoir ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <a id="avoirFactureLink" type="button"><button type="button" class="btn btn-success"
                            data-bs-dismiss="modal">Oui convertir</button></a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment invalider cette facture ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                    <a id="invaliderFactureLink" type="button"><button type="button" class="btn btn-success"
                            data-bs-dismiss="modal">Oui Invalider la facture</button></a>
                </div>
            </div>
        </div>
    </div>

    <div id="overlay"></div>

    <div id="loadingBar">
        <div id="loadingBarProgress"></div>
        <div>Normalisation en cours...</div>
    </div>


    <div class="row  ">
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

                    @can('voir-impression-factures')
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
                    @can('voir-statistique-globale-factures')
                        <button class="nav-link" id="nav-statGlobal-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-statGlobal" type="button" role="tab" aria-controls="nav-statGlobal"
                            aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-infographic"
                                width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M7 3v4h4" />
                                <path d="M9 17l0 4" />
                                <path d="M17 14l0 7" />
                                <path d="M13 13l0 8" />
                                <path d="M21 12l0 9" />
                            </svg>
                            Statistique
                            Globale</button>
                    @endcan

                    @can('voir-statistique-detaillee-factures')
                        <button class="nav-link" id="nav-statDetaille-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-statDetaille" type="button" role="tab"
                            aria-controls="nav-statDetaille" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-infographic"
                                width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M7 3v4h4" />
                                <path d="M9 17l0 4" />
                                <path d="M17 14l0 7" />
                                <path d="M13 13l0 8" />
                                <path d="M21 12l0 9" />
                            </svg>
                            Statistique
                            détaillés
                        </button>
                    @endcan


                </div>
            </nav>
            <div class="tab-content " id="nav-tabContent">

                <div class="tab-pane ms-2 fade show active" id="nav-home" role="tabpanel"
                    aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2 border">
                            <form class="d-flex align-items-center mt-2" id="filterForm"
                                action="{{ route('filter.facture') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des factures</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            id="proformaTable">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col">Agence</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">
                                                        Référence</th>
                                                    <th scope="col">
                                                        Statut</th>
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
                                                        data-url="{{ route('getDetailFacture', ['id' => $proforma->id]) }}">

                                                        <td>{{ $proforma->NomAgence }}</td>
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
                                                            @if ($proforma->Statut_facture == 'ANNULEE')
                                                                <span
                                                                    class="status-annulee">{{ $proforma->Statut_facture }}</span>
                                                            @endif
                                                            @if ($proforma->Statut_facture == 'INVALIDEE')
                                                                <span
                                                                    class="status-invalidee">{{ $proforma->Statut_facture }}</span>
                                                            @endif
                                                            @if ($proforma->Statut_facture == 'SOLDE')
                                                                <span
                                                                    class="status-solde">{{ $proforma->Statut_facture }}</span>
                                                            @endif
                                                            @if ($proforma->Statut_facture == 'EN COURS DE REGLEMENT')
                                                                <span
                                                                    class="status-en-cours-paiement">{{ $proforma->Statut_facture }}</span>
                                                            @endif
                                                        </td>

                                                        <td>{{ $proforma->Denomination_sociale }}</td>
                                                        <td>{{ $proforma->Objet_facture }}</td>
                                                        <td>{{ $proforma->Autres_infos }}</td>
                                                        <td>{{ $proforma->Commentaire }}</td>
                                                        @if ($proforma->Aib == 1)
                                                            @if ($proforma->Numero_ifu != null)
                                                                <td>1%</td>
                                                            @else
                                                                <td>5%</td>
                                                            @endif
                                                        @else
                                                            <td>{{ $proforma->Aib }}</td>
                                                        @endif
                                                        <td>{{ $proforma->Aib_deductible }}</td>
                                                        <td>{{ number_format($proforma->Net_a_payer, 0, ',', ' ') }}</td>
                                                        <td>{{ $proforma->user_name }}</td>
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des lignes facture</h3>
                                </div>
                                <div class="card-body responsive-1 ">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>
                                                        RéF</th>
                                                    <th>
                                                        Désignation</th>
                                                    <th>G.&nbsp;T. </th>
                                                    <th>PU HT
                                                    </th>
                                                    <th>% Remise
                                                    </th>
                                                    <th>PU NET HT
                                                    </th>
                                                    <th>PU NET
                                                        TTC</th>
                                                    <th>Qte</th>
                                                    <th>Montant
                                                        Net HT</th>
                                                    <th>Montant
                                                        Net TTC</th>
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
                                                        <td>{{ $detailFacture->Taux_remise }}</td>
                                                        <td>{{ number_format($puHT_Net, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($puNetTTC), 0, ',', ' ') }}</td>
                                                        <td>{{ number_format($detailFacture->Qte, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format(intval($MontantNetHT), 0, ',', ' ') }}</td>
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

                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Liste des factures sur une période</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form id="listeFacturePeriodeForm" action="{{ route('imprimerlisteFacture') }}"
                                            method="GET"
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center">
                                            @csrf
                                            <div class="col-md-3">
                                                <label class="" id="inputGroup-sizing-sm">Agence</label>
                                                <select name="agence" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="agence4" required>
                                                    <option></option>
                                                    @if (userAffectedSiege())
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
                                                        @forelse ($listeClient as $client)
                                                            <option value="{{ $client->id }}">
                                                                {{ $client->Denomination_sociale }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
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
                                                        <label class="form-check-label"
                                                            for="checkbox4">Normalisées</label>
                                                    </div>
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="statut[]"
                                                            value="EN COURS DE REGLEMENT" id="checkbox5">
                                                        <label class="form-check-label" for="checkbox5">En cours de
                                                            reglement</label>
                                                    </div>
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="statut[]"
                                                            value="SOLDE" id="checkbox6">
                                                        <label class="form-check-label" for="checkbox6">Soldées</label>
                                                    </div>
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="checkbox" name="statut[]"
                                                            value="ANNULEE" id="checkbox7">
                                                        <label class="form-check-label" for="checkbox7">Annulées</label>
                                                    </div>
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

                    @can('exporter-excel-factures')
                        <button id="exportButton3" data-bs-toggle="modal" data-bs-target="#staticBackdropImp"
                            class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('imprimer-factures')
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
                                    <table class="tableInfo2 " id="tableFactureParPeriode"
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
                                                <th style="width: 200px; font-size: 18px;" scope="col">Aib</th>
                                                <th style="width: 200px; font-size: 18px;" scope="col">Aib à déduire
                                                </th>
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

                <div class="tab-pane fade" id="nav-statGlobal" role="tabpanel" aria-labelledby="nav-statGlobal-tab"
                    tabindex="0">
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Statistique globale facture</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form id="form1" action="{{ route('statistiqueGlobalFacture') }}"
                                            method="GET"
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center">
                                            @csrf
                                            <div class="col-md-2">
                                                <label class="" id="inputGroup-sizing-sm">Agence</label>
                                                <select name="agence" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="agence1" required>
                                                    @if (userAffectedSiege())
                                                        <option value="Toutes">Toutes</option>
                                                    @endif
                                                    @foreach ($listeAgence as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class=" col-md-2">
                                                <label class="" id="inputGroup-sizing-sm">Debut</label>
                                                <input type="datetime-local" name="date_debut" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}">
                                            </div>
                                            <div class=" col-md-2">
                                                <label class="" id="inputGroup-sizing-sm">Fin</label>
                                                <input type="datetime-local" name="date_fin" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}">
                                            </div>


                                            <div class="col-md-2">
                                                <label class="" id="inputGroup-sizing-sm">Client</label>
                                                <select name="client" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="client2">
                                                    <option value="Tous">Tous</option>
                                                    @forelse ($listeClient as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->Denomination_sociale }}
                                                        </option>

                                                    @empty
                                                    @endforelse
                                                </select>

                                            </div>

                                            <div class="col-md-2">
                                                <button type="submit" class="btn text-white w-100 mt-4"
                                                    id="AppliquerForm1"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </form>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    @can('exporter-statistique-globale-excel-factures')
                        <button id="exportButton" data-bs-toggle="modal" data-bs-target="#staticBackdrop4"
                            class="btn mb-2 text-white" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('imprimer-statistique-globale-facture')
                        <button id="exportButtonP" data-bs-toggle="modal" data-bs-target="#staticBackdrop4P"
                            class="btn mb-2 text-white" style="{{ background_color_2() }}">Exporter en PDF</button>
                    @endcan



                    <div class="card m-b-30 mb-5">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des factures</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="table-responsive">
                                    <table class="tableInfo2 " id="tableStatGlobal" style="border: 1px solid #ddd">
                                        <thead class="">
                                            <tr>
                                                <th style="width: 20px;" scope="col">#
                                                </th>
                                                <th style="width: 500px;" scope="col">
                                                    Catégorie</th>
                                                <th style="width: 200px;" scope="col">
                                                    Vente&nbsp;(FV)</th>
                                                <th style="width: 200px;" scope="col">
                                                    Avoir&nbsp;(FA)</th>
                                                <th style="width: 150px;" scope="col">
                                                    Exportation&nbsp;(EV)</th>
                                                <th style="width: 100px;" scope="col">
                                                    Avoir&nbsp;d'exportation&nbsp;(EA)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="req_message1" style="text-align: center; color: red; display: none;">
                                    Veuillez faire une requette pour afficher les données
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                    <button id="exportExcel" class="btn btn-sm btn-success">Oui Exporter en Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="staticBackdrop4P" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données de cette statistique globale en PDF ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPDF" target="_blank" class="btn btn-sm btn-success">Oui Exporter en
                                        PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="nav-statDetaille" role="tabpanel" aria-labelledby="nav-statDetaille-tab"
                    tabindex="0">
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <fieldset class="border p-3 rounded-3">
                                    <legend class="float-none w-auto px-1">
                                        <h4>Statistique détaillée facture</h4>
                                    </legend>
                                    <div class="col-md-10 offset-md-1">
                                        <form id="form2" action="{{ route('statistiqueDetailleFacture') }}"
                                            method="GET"
                                            class="row gx-3 gy-2 d-flex align-items-center justify-content-center">
                                            @csrf
                                            <div class="col-md-2">
                                                <label class="" id="inputGroup-sizing-sm">Agence</label>
                                                <select name="agence" type="text"
                                                    class="form-select js-single  w-100" style="width: 100%;"
                                                    id="agence3" required>
                                                    <option></option>
                                                    @if (userAffectedSiege())
                                                        <option value="Toutes">Toutes</option>
                                                    @endif
                                                    @foreach ($listeAgence as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 ">
                                                <label class="" id="inputGroup-sizing-sm">Debut</label>
                                                <input type="datetime-local" name="date_debut" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="col-md-2 ">
                                                <label class="" id="inputGroup-sizing-sm">Fin</label>
                                                <input type="datetime-local" name="date_fin" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm"
                                                    max="{{ date('Y-m-d\TH:i') }}" required>
                                            </div>

                                            <div class="col-md-2 ">
                                                <label class="" id="inputGroup-sizing-sm">Taxes</label>
                                                <select name="taxe" type="text" class="form-select"
                                                    id="taxe">
                                                    <option value="Tous">Tous</option>
                                                    <option value="TVA_Taxable">TVA taxables 18%</option>
                                                    <option value="TVA_Exception">TVA régime d'exception 18%</option>
                                                    <option value="Aib_Facturee">Aib facturé</option>
                                                    <option value="Aib_Deductible">Aib déductible</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 ">
                                                <label class="" id="inputGroup-sizing-sm">Client</label>
                                                <select name="client" type="text" class="form-select js-single w-100"
                                                    style="width: 100%;" id="client3">
                                                    <option value="Tous">Tous</option>
                                                    @forelse ($listeClient as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->Denomination_sociale }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn styleBtnDownload text-white w-100 mt-4"
                                                    id="AppliquerForm2"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </form>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    @can('exporter-statistique-detaillee-excel-factures')
                        <button id="exportButton2" data-bs-toggle="modal" data-bs-target="#staticBackdrop5"
                            class="btn btn-sm btn-success mb-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('imprimer-statistique-detaillee-factures')
                        <button id="exportButton2P" data-bs-toggle="modal" data-bs-target="#staticBackdrop5P"
                            class="btn btn-sm btn-warning mb-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                    @endcan

                    <div class="card m-b-30 mb-5">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des factures</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="tableInfo2" id="tableStatDetaille">
                                        <thead class="">
                                            <tr>
                                                <th>
                                                    Taxe</th>
                                                <th>
                                                    Client</th>
                                                <th>
                                                    Date</th>
                                                <th>
                                                    N Facture</th>
                                                <th>
                                                    Objet</th>
                                                <th>
                                                    Vente (FV)</th>
                                                <th>
                                                    Avoir (FA)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="req_message2" style="text-align: center; color: red; display: none;">
                                Veuillez faire une requette pour afficher les données
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données de cette statistique détaillé en Excel ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportExcel2" class="btn btn-sm btn-success">Oui Exporter en
                                        Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="staticBackdrop5P" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données de cette statistique détaillé en PDF ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPDF2" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                        en PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const formIds = ['listeFacturePeriodeForm', 'form1', 'form2'];

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
        document.getElementById('normaliserFactureLink').addEventListener('click', function(event) {
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

                var invaliderFactureUrl = "{{ route('invaliderFacture', ['id' => ':proformaId']) }}";
                var normaliserFactureUrl = "{{ route('normaliserFacture', ['id' => ':proformaId']) }}";
                var avoirFactureUrl = "{{ route('avoirFacture', ['id' => ':proformaId']) }}";
                var imprimerA4Url = "{{ route('generatePDFA4', ['id' => ':proformaId', 'format' => 'a4']) }}";
                var imprimerA5Url = "{{ route('generatePDFA5', ['id' => ':proformaId', 'format' => 'a5']) }}";
                var imprimerA8Url = "{{ route('generatePDFA8', ['id' => ':proformaId', 'format' => 'a8']) }}";
                var bordereauUrl = "{{ route('bordereauPDF', ['id' => ':proformaId']) }}";
                invaliderFactureUrl = invaliderFactureUrl.replace(':proformaId', proformaId);
                normaliserFactureUrl = normaliserFactureUrl.replace(':proformaId', proformaId);
                avoirFactureUrl = avoirFactureUrl.replace(':proformaId', proformaId);
                imprimerA4Url = imprimerA4Url.replace(':proformaId', proformaId, ':format', 'a4');
                imprimerA5Url = imprimerA5Url.replace(':proformaId', proformaId, ':format', 'a5');
                imprimerA8Url = imprimerA8Url.replace(':proformaId', proformaId, ':format', 'a8');
                bordereauUrl = bordereauUrl.replace(':proformaId', proformaId);
                // Mettre à jour l'attribut href du lien
                $('#invaliderFactureLink').attr('href', invaliderFactureUrl);
                $('#normaliserFactureLink').attr('href', normaliserFactureUrl);
                $('#avoirFactureLink').attr('href', avoirFactureUrl);
                $('#imprimerA4Link').attr('href', imprimerA4Url);
                $('#imprimerA5Link').attr('href', imprimerA5Url);
                $('#imprimerA8Link').attr('href', imprimerA8Url);
                $('#bordereauLink').attr('href', bordereauUrl);
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

        .tableInfo3 {
            border-collapse: collapse;
            border: 1px solid #ddd;
            width: 1500px;

        }

        .tableInfo3 th,
        .tableInfo3 td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;


        }


        .tableInfo2 {
            width: 1500px;
        }

        .tableInfo2 td,
        th {
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

            $('#listeFacturePeriodeForm').on('submit', function(e) {
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
                                        <td>${facture.Reference_facture}</td>
                                        <td>${facture.Denomination_sociale}</td>
                                        <td>${numberFormat(Math.round( facture.Aib_facturee))}</td>
                                        <td>${numberFormat(facture.Aib_deductible)}</td>
                                        <td>${numberFormat(facture.TotalGlobalTVA)}</td>
                                        <td>${numberFormat(facture.TotalGlobalHT)}</td>
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
                        /*      const grandTotalRow = `<tr class="font-weight-bold">
                         <td colspan="7" class="text-right">Total général</td>
                         <td class="text-right">(${totalFactures} factures)</td>
                         <td>${numberFormat(grandTotalNetAPayer)}</td>
                     </tr>`; */
                        //$('#tableFactureParPeriode tbody').append(grandTotalRow);
                        checkTable();
                        $button.removeClass('loading');
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
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_impression_facture_periode',
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
                            a.download = `Liste_Facture_Periode_${formatedDate}.xlsx`;
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
                    url: '/export_impression_facture_periode_pdf',
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

    {{-- Script statistiques global --}}

    <script>
        $(document).ready(function() {
            var exportButton = document.getElementById("exportButton");
            var exportButtonP = document.getElementById("exportButtonP");
            var table = document.getElementById("tableStatGlobal").getElementsByTagName("tbody")[0];

            // Fonction pour vérifier si le tableau est vide
            function checkTable() {
                if (table.rows.length === 0) {
                    exportButton.disabled = true;
                    exportButtonP.disabled = true;
                    $('#req_message1').show();
                } else {
                    exportButton.disabled = false;
                    exportButtonP.disabled = false;
                    $('#req_message1').hide();
                }

            }

            function numberFormat(value) {
                return Number(value).toLocaleString('fr-FR');
            }

            // appel la fonction de vérification au chargement de la page
            checkTable();

            // Ajouter le jeton CSRF à chaque requête AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#form1').on('submit', function(e) {
                e
                    .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                var formData = $(this).serialize(); // Sérialisation des données du formulaire


                var $button = $('#AppliquerForm1');
                $button.addClass('loading');
                $button.prop('disabled', true);


                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'), // URL définie dans l'attribut action du formulaire
                    data: formData, // Données du formulaire sérialisées
                    success: function(response) {
                        ajaxResponse = response;
                        if (response.totalParTypeFacture <= 0) {
                            alert("Aucune facture n'a été trouvée.");
                            // Efface le contenu précédent du tableau
                            $('#tableStatGlobal tbody').empty();

                            // Effacer le tableau des différences précédent
                            $('#diffTable').remove();
                            $button.removeClass('loading');
                            $button.prop('disabled', false); // Réactive le bouton
                            exit;

                        }
                        // Efface le contenu précédent du tableau
                        $('#tableStatGlobal tbody').empty();

                        // Effacer le tableau des différences précédent
                        $('#diffTable').remove();

                        // Ajoute une ligne pour afficher le nombre total de factures par type de facture
                        var totalRowCount = $('#tableStatGlobal tbody tr').length + 1;
                        var totalRow = `<tr>
                        <td>${totalRowCount}</td>
                        <td>Nombre de facture(s)</td>
                        <td>${response.totalParTypeFacture.FV || 0}</td> <!-- Affiche le total pour le type FV -->
                        <td>${response.totalParTypeFacture.FA || 0}</td> <!-- Affiche le total pour le type FA -->
                        <td>${response.totalParTypeFacture.EV || 0}</td> <!-- Affiche le total pour le type EV -->
                        <td>${response.totalParTypeFacture.EA || 0}</td> <!-- Affiche le total pour le type EA -->
                    </tr>`;
                        $('#tableStatGlobal tbody').append(totalRow);

                        // Récupérer les catégories de facture
                        var categories = [
                            'totalExoneree', 'totalHT_B', 'totalHT_D', 'totalHT_C',
                            'totalHT_E', 'totalHT_F', 'AibFacturee', 'AibDeductible'
                        ];

                        // Initialiser les totaux des colonnes
                        var totals = {
                            'FV': 0,
                            'FA': 0,
                            'EV': 0,
                            'EA': 0
                        };

                        // Ajouter une ligne pour chaque catégorie
                        categories.forEach(function(categorie) {
                            var totalRowCount2 = $('#tableStatGlobal tbody tr').length +
                                1;
                            var categoryLabel = categorie === 'totalExoneree' ?
                                'Exonérés' :
                                categorie === 'totalHT_B' ? 'Taxable' :
                                categorie === 'totalHT_D' ? "Régimes d'exception" :
                                categorie === 'totalHT_C' ?
                                "Exportation des produits taxables" :
                                categorie === 'totalHT_E' ? "Régime TPS" :
                                categorie === 'totalHT_F' ? "Réservés" :
                                categorie === 'AibFacturee' ? "AIB Facturé" :
                                categorie === 'AibDeductible' ? "AIB Déductible" :
                                categorie;

                            var totalRow = `<tr>
                            <td>${totalRowCount2}</td>
                            <td>${categoryLabel}</td>`; // Affiche la catégorie

                            // Affichez le total pour chaque type de facture dans chaque catégorie
                            ['FV', 'FA', 'EV', 'EA'].forEach(function(codeTypeFacture) {
                                var value = response.totauxParCategorie[
                                        codeTypeFacture] ? response
                                    .totauxParCategorie[codeTypeFacture][
                                        categorie
                                    ] || 0 : 0;
                                totalRow +=
                                    `<td>${numberFormat(value.toFixed(0))}</td>`;
                                totals[codeTypeFacture] += parseFloat(
                                    value.toFixed(0)
                                ); // Ajouter à la somme totale
                            });

                            totalRow += `</tr>`;
                            $('#tableStatGlobal tbody').append(totalRow);
                        });

                        // Ajouter la ligne Total
                        var totalRow = `<tr>
                        <td>/</td>
                        <td colspan=""><strong>Total</strong></td>`;
                        ['FV', 'FA', 'EV', 'EA'].forEach(function(codeTypeFacture) {
                            totalRow +=
                                `<td><strong>${numberFormat(totals[codeTypeFacture])}</strong></td>`;
                        });
                        totalRow += `</tr>`;
                        $('#tableStatGlobal tbody').append(totalRow);

                        var diffTable = `<table id="diffTable" class="tableInfo2 mb-3">
                        <thead style="{{ background_color_2() }}">
                            <tr>
                                <th style="color: white;" scope="col">Catégorie</th>
                                <th style="color: white;" scope="col">Montant&nbsp;à&nbsp;l'intérieur&nbsp;(FV&nbsp;-&nbsp;FA)</th>
                                <th style="color: white;" scope="col">Montant&nbsp;à&nbsp;l'extérieur&nbsp;(EV&nbsp;-&nbsp;EA)</th>
                                <th style="color: white;" scope="col">Montant&nbsp;total</th>
                            </tr>
                        </thead>
                        <tbody>`;

                        var subtotalFV_FA = 0;
                        var subtotalEV_EA = 0;
                        var subtotalTotal = 0;
                        var subtotalTotal2FV_FA = 0;
                        var subtotalTotal2EV_EA = 0;
                        var subtotalTotal2 = 0;

                        // Calculer et ajouter les différences pour chaque catégorie
                        categories.forEach(function(categorie, index) {
                            var categoryLabel = categorie === 'totalExoneree' ?
                                'Exonérés' :
                                categorie === 'totalHT_B' ? 'Taxable' :
                                categorie === 'totalHT_D' ? "Régimes d'exception" :
                                categorie === 'totalHT_C' ?
                                "Exportation des produits taxables" :
                                categorie === 'totalHT_E' ? "Régime TPS" :
                                categorie === 'totalHT_F' ? "Réservés" :
                                categorie === 'AibFacturee' ? "AIB Facturé" :
                                categorie === 'AibDeductible' ? "AIB Déductible" :
                                categorie;

                            var fv = response.totauxParCategorie['FV'] ? response
                                .totauxParCategorie['FV'][categorie] || 0 : 0;
                            var fa = response.totauxParCategorie['FA'] ? response
                                .totauxParCategorie['FA'][categorie] || 0 : 0;
                            var ev = response.totauxParCategorie['EV'] ? response
                                .totauxParCategorie['EV'][categorie] || 0 : 0;
                            var ea = response.totauxParCategorie['EA'] ? response
                                .totauxParCategorie['EA'][categorie] || 0 : 0;

                            var diffFV_FA = parseFloat(fv.toFixed(0)) - parseFloat(fa
                                .toFixed(0));
                            var diffEV_EA = parseFloat(ev.toFixed(0)) - parseFloat(ea
                                .toFixed(0));
                            var totalDiff = parseFloat(diffFV_FA.toFixed(0)) +
                                parseFloat(
                                    diffEV_EA.toFixed(0));

                            var diffRow = `<tr>
                            <td>${categoryLabel}</td>
                            <td>${numberFormat(diffFV_FA.toFixed(0))}</td>
                            <td>${numberFormat(diffEV_EA.toFixed(0))}</td>
                            <td>${numberFormat(totalDiff.toFixed(0))}</td>
                        </tr>`;

                            if (index === 5) {
                                diffTable += `<tr>
                                <td><strong>Sous-total ligne 1 à 5</strong></td>
                                <td><strong>${numberFormat(subtotalFV_FA.toFixed(0))}</strong></td>
                                <td><strong>${numberFormat(subtotalEV_EA.toFixed(0))}</strong></td>
                                <td><strong>${numberFormat(subtotalTotal.toFixed(0))}</strong></td>
                            </tr>`;
                            }

                            // Mettre à jour les sous-totaux pour les premières cinq lignes
                            if (index < 5) {
                                subtotalFV_FA += diffFV_FA;
                                subtotalEV_EA += diffEV_EA;
                                subtotalTotal += totalDiff;
                            }
                            if (index === 7) {
                                diffTable += `<tr>
                                <td><strong>Sous-total ligne 6 à 8</strong></td>
                                <td><strong>${numberFormat(subtotalTotal2FV_FA.toFixed(0))}</strong></td>
                                <td><strong>${numberFormat(subtotalTotal2EV_EA.toFixed(0))}</strong></td>
                                <td><strong>${numberFormat(subtotalTotal2.toFixed(0))}</strong></td>
                            </tr>`;
                            }

                            // Mettre à jour les sous-totaux pour les premières cinq lignes
                            if (index < 7) {
                                subtotalTotal2FV_FA += diffFV_FA;
                                subtotalTotal2EV_EA += diffEV_EA;
                                subtotalTotal2 += totalDiff;
                            }

                            diffTable += diffRow;
                        });

                        diffTable += `</tbody></table>`;
                        $('#tableStatGlobal').after(
                            diffTable);
                        checkTable();
                        $button.removeClass('loading');
                        $button.prop('disabled', false); // Réactive le bouton

                        // Détacher les événements précédents avant de les attacher à nouveau
                        $('#exportExcel').off('click').on('click', function() {
                            var $button = $(this);
                            if (ajaxResponse) {
                                $button.addClass('loading');
                                $button.prop('disabled', true);
                                $button.text('Exportation en cours...');

                                var currentDate = new Date();
                                var formatedDate = formatDateTime(currentDate);
                                var formData = $(this).serialize();
                                var tableStatGlobalData = [];
                                $('#tableStatGlobal tbody tr').each(function() {
                                    var row = $(this);
                                    var rowData = {
                                        rowNumber: row.find('td').eq(0)
                                            .text(),
                                        category: row.find('td').eq(1)
                                            .text(),
                                        FV: row.find('td').eq(2).text(),
                                        FA: row.find('td').eq(3).text(),
                                        EV: row.find('td').eq(4).text(),
                                        EA: row.find('td').eq(5).text()
                                    };
                                    tableStatGlobalData.push(rowData);
                                });

                                // Capturer les données de diffTable
                                var diffTableData = [];
                                $('#diffTable tbody tr').each(function() {
                                    var row = $(this);
                                    var rowData = {
                                        category: row.find('td').eq(0)
                                            .text(),
                                        montantInterieur: row.find('td').eq(
                                                1)
                                            .text(),
                                        montantExterieur: row.find('td').eq(
                                                2)
                                            .text(),
                                        montantTotal: row.find('td').eq(3)
                                            .text()
                                    };
                                    diffTableData.push(rowData);
                                });

                                var exportData = {
                                    totauxParCategorie: response.totauxParCategorie,
                                    totalParTypeFacture: response
                                        .totalParTypeFacture,
                                    listeFacture: response.listeFacture,
                                    tableStatGlobalData: tableStatGlobalData,
                                    diffTableData: diffTableData,
                                    dateDebut: response.dateDebut,
                                    dateFin: response.dateFin,
                                    client: response.client,
                                    infoClient: response.infoClient,
                                    infoAgence: response.infoAgence

                                };
                                $.ajax({
                                    type: 'POST',
                                    url: '/export-excel',
                                    data: JSON.stringify(exportData),
                                    contentType: 'application/json',
                                    xhrFields: {
                                        responseType: 'blob'
                                    },
                                    success: function(response) {
                                        var blob = new Blob([response], {
                                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                        });
                                        var url = window.URL
                                            .createObjectURL(
                                                blob);
                                        var a = document.createElement('a');
                                        a.href = url;
                                        a.download =
                                            `statistique_global_facture_${formatedDate}.xlsx`;
                                        document.body.appendChild(a);
                                        a.click();
                                        window.URL.revokeObjectURL(url);
                                        $button.removeClass('loading');
                                        $button.prop('disabled', false);
                                        $button.text(
                                            'Oui, Exporter en Excel');
                                        $('#staticBackdrop4').modal('hide');
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(error);
                                        $button.removeClass('loading');
                                        $button.prop('disabled', false);
                                        $button.text(
                                            'Oui, Exporter en Excel');
                                    }
                                });

                            } else {
                                console.error(
                                    "Aucune donnée disponible pour l'exportation.");
                            }


                        });

                        // Détacher les événements précédents avant de les attacher à nouveau
                        $('#exportPDF').off('click').on('click', function() {
                            var newWindow = null;

                            //chargement
                            var $button = $(this);
                            $button.addClass('loading');
                            $button.prop('disabled', true);
                            $button.text('Exportation en cours...');


                            var tableStatGlobalData = [];
                            $('#tableStatGlobal tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    rowNumber: row.find('td').eq(0).text(),
                                    category: row.find('td').eq(1).text(),
                                    FV: row.find('td').eq(2).text(),
                                    FA: row.find('td').eq(3).text(),
                                    EV: row.find('td').eq(4).text(),
                                    EA: row.find('td').eq(5).text()
                                };
                                tableStatGlobalData.push(rowData);
                            });

                            // Capturer les données de diffTable
                            var diffTableData = [];
                            $('#diffTable tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    category: row.find('td').eq(0).text(),
                                    montantInterieur: row.find('td').eq(1)
                                        .text(),
                                    montantExterieur: row.find('td').eq(2)
                                        .text(),
                                    montantTotal: row.find('td').eq(3)
                                        .text()
                                };
                                diffTableData.push(rowData);
                            });

                            var exportData = {
                                totauxParCategorie: response.totauxParCategorie,
                                totalParTypeFacture: response.totalParTypeFacture,
                                listeFacture: response.listeFacture,
                                tableStatGlobalData: tableStatGlobalData,
                                diffTableData: diffTableData,
                                client: response.client,
                                dateDebut: response.dateDebut,
                                dateFin: response.dateFin,
                                infoClient: response.infoClient,
                                infoAgence: response.infoAgence
                            };

                            $.ajax({
                                type: 'POST',
                                url: '/export_stat_global_pdf',
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


                                    $('#staticBackdrop4P').modal('hide');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                    $button.removeClass('loading');
                                    $button.prop('disabled', false);
                                    $button.text('Oui, Exporter en PDF');

                                }
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        // Gestion des erreurs
                        console.error(error);
                        $button.removeClass('loading');
                        $button.prop('disabled', false); // Réactive le bouton
                    }
                });
                checkTable();
            });

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



    {{-- Script statistique detaille --}}
    <script>
        $(document).ready(function() {
            var exportButton2 = document.getElementById("exportButton2");
            var exportButton2P = document.getElementById("exportButton2P");
            var table = document.getElementById("tableStatDetaille").getElementsByTagName("tbody")[0];

            // Fonction pour vérifier si le tableau est vide
            function checkTable() {
                if (table.rows.length === 0) {
                    exportButton2.disabled = true;
                    exportButton2P.disabled = true;
                    $('#req_message2').show();
                } else {
                    exportButton2.disabled = false;
                    exportButton2P.disabled = false;
                    $('#req_message2').hide();
                }
            }

            function numberFormat(value) {
                return Number(value).toLocaleString('fr-FR');
            }


            // appel la fonction de vérification au chargement de la page
            checkTable();

            $('#form2').on('submit', function(e) {
                e
                    .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                var formData = $(this).serialize(); // Sérialisation des données du formulaire

                var $button = $('#AppliquerForm2');
                $button.addClass('loading');
                $button.prop('disabled', true);
                $.ajax({
                    type: 'GET',
                    url: $(this).attr('action'), // URL définie dans l'attribut action du formulaire
                    data: formData, // Données du formulaire sérialisées

                    success: function(response) {

                        ajaxResponse = response;

                        // Efface le contenu précédent du tableau
                        $('#tableStatDetaille tbody').empty();

                        let totalTVA_B = 0;
                        let totalTVA_B_venteFV = 0;
                        let totalTVA_B_avoirFA = 0;
                        let totalTVA_D = 0;
                        let totalTVA_D_venteFV = 0;
                        let totalTVA_D_avoirFA = 0;
                        let totalAibFacturee = 0;
                        let totalAibFacturee_venteFV = 0;
                        let totalAibFacturee_avoirFA = 0;
                        let totalAibDeductible = 0;
                        let totalAibDeductible_venteFV = 0;
                        let totalAibDeductible_avoirFA = 0;
                        let index = 1;
                        const taxe = $('select[name="taxe"]')
                            .val(); // Obtient la taxe sélectionnée

                        // Affiche les résultats en fonction de la taxe sélectionnée
                        if (taxe === 'TVA_Taxable' || taxe === 'Tous') {
                            $('#tableStatDetaille tbody').append(
                                '<tr><td colspan="7" style="{{ background_color_2() }}" class="text-white">TVA Taxable 18%</td></tr>'
                            );
                            response.response.forEach(function(item) {
                                if (item.TotalTVA_B > 0) {
                                    totalTVA_B += item.TotalTVA_B;
                                    var venteFV = item.Code_type_facture === 'FV' ||
                                        item.Code_type_facture === 'EV' ? parseFloat(
                                            item.TotalTVA_B) || 0 : 0;
                                    var avoirFA = item.Code_type_facture === 'FA' ||
                                        item.Code_type_facture === 'EA' ?
                                        parseFloat(item.TotalTVA_B) || 0 : 0;
                                    totalTVA_B_venteFV += venteFV;
                                    totalTVA_B_avoirFA += avoirFA;

                                    var row = `<tr>
                                <td></td>
                                <td>${item.Denomination_sociale || 'N/A'}</td>
                                <td>${formatDate(item.Date_signature) || 'N/A'}</td>
                                <td>${item.Reference_facture || 'N/A'}</td>
                                <td>${item.Objet_facture || 'N/A'}</td>
                                <td>${numberFormat(venteFV.toFixed(0))}</td>
                                <td>${numberFormat(avoirFA.toFixed(0))}</td>
                            </tr>`;
                                    $('#tableStatDetaille tbody').append(row);
                                    index++;
                                }
                            });
                            $('#tableStatDetaille tbody').append(
                                `<tr><td colspan="5">Total TVA Taxable 18%</td><td>${numberFormat(totalTVA_B_venteFV.toFixed(0))}</td><td>${numberFormat(totalTVA_B_avoirFA.toFixed(0))}</td></tr>`
                            );
                        }

                        if (taxe === 'TVA_Exception' || taxe === 'Tous') {
                            index = 1;
                            $('#tableStatDetaille tbody').append(
                                '<tr><td colspan="7" style="{{ background_color_2() }}" class="text-white">TVA régime d\'exception 18%</td></tr>'
                            );
                            response.response.forEach(function(item) {
                                if (item.TotalTVA_D > 0) {
                                    totalTVA_D += item.TotalTVA_D;
                                    var venteFV = item.Code_type_facture === 'FV' ||
                                        item.Code_type_facture === 'EV' ?
                                        parseFloat(item.TotalTVA_D) || 0 : 0;
                                    var avoirFA = item.Code_type_facture === 'FA' ||
                                        item.Code_type_facture === 'EA' ?
                                        parseFloat(item.TotalTVA_D) || 0 : 0;
                                    totalTVA_D_venteFV += venteFV;
                                    totalTVA_D_avoirFA += avoirFA;

                                    var row2 = `<tr>
                                <td></td>
                                <td>${item.Denomination_sociale || 'N/A'}</td>
                                <td>${formatDate(item.Date_signature) || 'N/A'}</td>
                                <td>${item.Reference_facture || 'N/A'}</td>
                                <td>${item.Objet_facture || 'N/A'}</td>
                                <td>${numberFormat(venteFV.toFixed(0))}</td>
                                <td>${numberFormat(avoirFA.toFixed(0))}</td>
                            </tr>`;
                                    $('#tableStatDetaille tbody').append(row2);
                                    index++;
                                }
                            });
                            $('#tableStatDetaille tbody').append(
                                `<tr><td colspan="5">Total TVA régime d'exception 18%</td><td>${numberFormat(totalTVA_D_venteFV.toFixed(0))}</td><td>${numberFormat(totalTVA_D_avoirFA.toFixed(0))}</td></tr>`
                            );
                        }

                        if (taxe === 'Aib_Facturee' || taxe === 'Tous') {
                            index = 1;
                            $('#tableStatDetaille tbody').append(
                                '<tr><td colspan="7" style="{{ background_color_2() }}" class="text-white">AIb facturé</td></tr>'
                            );
                            response.response.forEach(function(item) {
                                if (item.Aib_facturee > 0) {
                                    totalAibFacturee += item.Aib_facturee;
                                    var venteFV = item.Code_type_facture === 'FV' ||
                                        item.Code_type_facture === 'EV' ?
                                        parseFloat(item.Aib_facturee) || 0 : 0;
                                    var avoirFA = item.Code_type_facture === 'FA' ||
                                        item.Code_type_facture === 'EA' ?
                                        parseFloat(item.Aib_facturee) || 0 : 0;
                                    totalAibFacturee_venteFV += venteFV;
                                    totalAibFacturee_avoirFA += avoirFA;

                                    var row3 = `<tr>
                                <td></td>
                                <td>${item.Denomination_sociale || 'N/A'}</td>
                                <td>${formatDate(item.Date_signature) || 'N/A'}</td>
                                <td>${item.Reference_facture || 'N/A'}</td>
                                <td>${item.Objet_facture || 'N/A'}</td>
                                <td>${numberFormat(venteFV.toFixed(0))}</td>
                                <td>${numberFormat(avoirFA.toFixed(0))}</td>
                            </tr>`;
                                    $('#tableStatDetaille tbody').append(row3);
                                    index++;
                                }
                            });
                            $('#tableStatDetaille tbody').append(
                                `<tr><td colspan="5">Total AIb facturé</td><td>${numberFormat(totalAibFacturee_venteFV.toFixed(0))}</td><td>${numberFormat(totalAibFacturee_avoirFA.toFixed(0))}</td></tr>`
                            );
                        }

                        if (taxe === 'Aib_Deductible' || taxe === 'Tous') {
                            index = 1;
                            $('#tableStatDetaille tbody').append(
                                '<tr><td colspan="7" style="{{ background_color_2() }}" class="text-white">AIb deductible</td></tr>'
                            );
                            response.response.forEach(function(item) {
                                if (item.Aib_deductible > 0) {
                                    totalAibDeductible += item.Aib_deductible;
                                    var venteFV = item.Code_type_facture === 'FV' ||
                                        item.Code_type_facture === 'EV' ?
                                        parseFloat(item.Aib_deductible) || 0 : 0;
                                    var avoirFA = item.Code_type_facture === 'FA' ||
                                        item.Code_type_facture === 'EA' ?
                                        parseFloat(item.Aib_deductible) || 0 : 0;

                                    totalAibDeductible_venteFV += venteFV;
                                    totalAibDeductible_avoirFA += avoirFA;

                                    var row4 = `<tr>
                                <td></td>
                                <td>${item.Denomination_sociale || 'N/A'}</td>
                                <td>${formatDate(item.Date_signature) || 'N/A'}</td>
                                <td>${item.Reference_facture || 'N/A'}</td>
                                <td>${item.Objet_facture || 'N/A'}</td>
                                <td>${numberFormat(venteFV.toFixed(0))}</td>
                                <td>${numberFormat(avoirFA.toFixed(0))}</td>
                            </tr>`;
                                    $('#tableStatDetaille tbody').append(row4);
                                    index++;
                                }
                            });
                            $('#tableStatDetaille tbody').append(
                                `<tr><td colspan="5">Total AIb deductible</td><td>${numberFormat(totalAibDeductible_venteFV.toFixed(0))}</td><td>${numberFormat(totalAibDeductible_avoirFA.toFixed(0))}</td></tr>`
                            );
                        }
                        checkTable();
                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                    }
                });
            });

            $('#exportExcel2').off('click').on('click', function() {
                var $button = $(this);
                if (ajaxResponse) {
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableStatDetailleData = [];
                    $('#tableStatDetaille tbody tr').each(function() {

                        var row = $(this);
                        var rowData = {
                            taxe: row.find('td').eq(0).text(),
                            client: row.find('td').eq(1).text(),
                            date: row.find('td').eq(2).text(),
                            nfacture: row.find('td').eq(3).text(),
                            objet: row.find('td').eq(4).text(),
                            FV: row.find('td').eq(5).text(),
                            FA: row.find('td').eq(6).text()
                        };
                        tableStatDetailleData.push(rowData);
                    });
                    var exportData = {
                        tableStatDetailleData: tableStatDetailleData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        taxe: ajaxResponse.taxe,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence,


                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_excel_stat_detaille',
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

                            a.download = `statistique_detaille_facture_${formatedDate}.xlsx`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en Excel');
                            $('#staticBackdrop5').modal('hide');
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

            $('#exportPDF2').off('click').on('click', function() {
                var newWindow = null;

                //chargement
                var $button = $(this);
                $button.addClass('loading');
                $button.prop('disabled', true);
                $button.text('Exportation en cours...');


                var tableStatDetailleData = [];
                $('#tableStatDetaille tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        taxe: row.find('td').eq(0).text(),
                        client: row.find('td').eq(1).text(),
                        date: row.find('td').eq(2).text(),
                        nfacture: row.find('td').eq(3).text(),
                        objet: row.find('td').eq(4).text(),
                        FV: row.find('td').eq(5).text(),
                        FA: row.find('td').eq(6).text()
                    };
                    tableStatDetailleData.push(rowData);
                });

                var exportData = {
                    tableStatDetailleData: tableStatDetailleData,
                    dateDebut: ajaxResponse.dateDebut,
                    dateFin: ajaxResponse.dateFin,
                    taxe: ajaxResponse.taxe,
                    infoClient: ajaxResponse.infoClient,
                    infoAgence: ajaxResponse.infoAgence,

                };


                $.ajax({
                    type: 'POST',
                    url: '/export_excel_stat_detaille_pdf',
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

                        $('#staticBackdrop5P').modal('hide');
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










    @include('layouts.alert')
@endsection
