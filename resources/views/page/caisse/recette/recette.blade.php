@extends('layouts.master', ['title' => 'Recette'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Recette',
        'infos2' => 'Recette',
        'infos3' => 'Liste',
    ])

    @can('effectuer-une-recette')
        <div class="row d-flex text-start p-3">
            <div class="col text-end">
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a type="button" class="dropdown-item" href="{{ route('new_recette') }}">Nouvelle Recette</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    @endcan



    <div class="modal fade" id="creeCatEmballage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="creeCatEmballageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="creeEmballageForm" action="{{ route('ouvrirCaisse') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="creeCatEmballageLabel">Nouvelle caisse</h1>
                        <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="creeLibelle" class="form-label fw-bold">Fond initial <span
                                            class="fs-5 text-danger mb-2">*</span></label>
                                    <input type="number" name="fonds_initial" class="form-control" required>
                                    <div class="invalid-feedback">Le champ est obligatoire</div>
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


    <div class="row mt-1">
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


                        @can('voir-impression-recette')
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="18"
                            height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                                action="{{ route('filterDepense') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark"> Caissse</h3>
                                </div>
                                <div class="card-body responsive-2">
                                    <div class="table-responsive">
                                        <table class="tableInfo table table-striped datatable table-bordered  "
                                            id="proformaTable">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 200px;">Agence</th>
                                                    <th style="width: 130px;">Date ouverture</th>
                                                    <th style="width: 150px;">
                                                        Montant initial</th>
                                                    <th style="width: 150px;">
                                                        Montant actuel</th>
                                                    <th style="width: 100px;">
                                                        Statut caisse</th>

                                                    <th style="width: 400px;">
                                                        Créé par</th>
                                                    <th style="width: 130px;">Date fermeture</th>


                                                </tr>
                                            </thead>
                                            <style>
                                                .status-fermee {
                                                    background-color: #f8d7da;
                                                    color: #721c24;
                                                    padding: 3px 5px;
                                                    border-radius: 5px;
                                                    border: 1px solid #f5c6cb;
                                                }

                                                .status-ouvert {
                                                    background-color: #d4edda;
                                                    color: #155724;
                                                    padding: 3px 5px;
                                                    border-radius: 5px;
                                                    border: 1px solid #c3e6cb;
                                                }
                                            </style>
                                            <tbody>
                                                @forelse ($listeCaisse as $caisse)
                                                    <tr style="cursor:pointer;" class="clickable-row "
                                                        data-url="{{ route('getDetailCaisse3', ['id' => $caisse->id]) }}">

                                                        <td>{{ $caisse->NomAgence }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($caisse->date_ouverture)->format('d/m/Y H:i:s') }}
                                                        </td>

                                                        </td>
                                                        <td>{{ number_format($caisse->fonds_initial, 0, ',', ' ') }}</td>
                                                        <td>{{ number_format($caisse->fonds_actuel, 0, ',', ' ') }}</td>
                                                        <td>
                                                            @if ($caisse->statut == '0')
                                                                <span class="status-fermee">Fermée</span>
                                                            @endif
                                                            @if ($caisse->statut == '1')
                                                                <span class="status-ouvert">Ouverte</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $caisse->user_name }}</td>
                                                        @if ($caisse->date_fermeture != null)
                                                            <td>{{ \Carbon\Carbon::parse($caisse->date_fermeture)->format('d/m/Y H:i:s') }}
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif

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
                                    <h3 class="mt-2  d-inline-block text-dark">Historique recette de la caisse</h3>
                                </div>
                                <div class="card-body responsive-1">
                                    <div class="table-responsive">
                                        <table class="tableInfo2 table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 200px;"> Date</th>

                                                    <th style="width: 200px;"> Réference opération</th>
                                                    <th>
                                                        Type</th>
                                                    <th> Montant</th>
                                                    <th> Categorie recette</th>
                                                    <th> Description</th>
                                                    <th style="width: 150px;"> Statut</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($detailCaisse as $detail)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>{{ $detail->reference_operation }}</td>
                                                        <td>{{ $detail->type }}</td>
                                                        <td>{{ number_format($detail->montant, 0, ',', ' ') }}
                                                        <td>{{ $detail->designation }}</td>
                                                        <td>{{ $detail->description }}</td>
                                                        <td>
                                                            @if ($detail->statut == 'ANNULLEE')
                                                                <span class="status-fermee">ANNULLEE</span>
                                                            @endif
                                                            @if ($detail->statut == 'EFFECTUEE')
                                                                <span class="status-ouvert">EFFECTUEE</span>
                                                            @endif
                                                        </td>
                                                        </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">Aucune donnée (Selectionner
                                                            une
                                                            caisse)</td>
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
                var caisseId = $(this).data('url').split('/').pop();
                console.log(caisseId);

                var fermerCaisse = "{{ route('fermerCaisse', ['id' => ':caisseId']) }}";
                var conversionProformaUrl = "{{ route('conversionProforma', ['id' => ':caisseId']) }}";
                var pdfProformaUrl = "{{ route('PDFProformaA4', ['id' => ':caisseId']) }}";
                fermerCaisse = fermerCaisse.replace(':caisseId', caisseId);
                conversionProformaUrl = conversionProformaUrl.replace(':caisseId', caisseId);
                pdfProformaUrl = pdfProformaUrl.replace(':caisseId', caisseId);
                $('#modifierProformaLink').attr('href', fermerCaisse);
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
    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
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
            border: 1px solid #ddd;
            width: 100%;
        }

        .tableInfo2 th,
        .tableInfo2 td {

            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }
    </style>
    <style>
        .tableInfo2 {
            width: 1500px;
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

    @include('layouts.alert')
@endsection
