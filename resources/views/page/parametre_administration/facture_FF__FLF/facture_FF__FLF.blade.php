@extends('layouts.master', ['title' => 'facture_FF__FLF'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Factures et Lignes Factures',
        'infos2' => 'facture_FF__FLF',
        'infos3' => 'Liste',
    ])

    <section>

        @if ($errors->any())
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
                    icon: "error",
                    title: "{{ 'La date de fin doit être postérieure à la date de début' }}"
                });
            </script>

            {{-- Quand il s'agira de personnalise en fonctio de du formulaire soumis --}}
            {{-- @foreach ($errors->getBag('default')->toArray() as $field => $fieldErrors)
                @foreach ($fieldErrors as $error)
                    @if ($field == 'fin1')
                        <li>{{ $field }}: {{ $error }}</li>
                    @endif
                @endforeach
            @endforeach --}}
        @endif

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link @if (!$isFiltered) active @endif" id="nav-home-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                    aria-selected="true">Facture</button>
                <button class="nav-link @if ($isFiltered) active @endif" id="nav-profile-tab"
                    data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                    aria-controls="nav-profile" aria-selected="false">Ligne de Factures</button>
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade @if (!$isFiltered) show active @endif" style="margin-bottom: 150px" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">

                <div class="row mt-4">
                    <div class="col-md-12">
                        <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center " method="GET"
                            action="{{ route('reqFacture_ff') }}">
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Debut</span>
                                    <input name="debut1" type="date" class="form-control" id="debut"
                                        value="{{ $requestFacture->get('debut1') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">

                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Fin</span>
                                    <input name="fin1" type="date" class="form-control" id="fin"
                                        value="{{ $requestFacture->get('fin1') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-sm btn-primary">Appliquer</button>
                                    </div>
                                </div>

                            </div>
                            <div class="col-auto">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="btn-group">
                                        <a href="{{ route('facturesCsv.export') }}" class="btn btn-sm btn-success">Exporter
                                            en CSV</a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $nbrAgence = auth()->user()->agences->count() > 1;
                    $agencesUser = auth()->user()->agences->all('*');
                @endphp

                <div class="d-flex bd-highlight">
                    @if (auth()->user()->agences->count() > 1)
                        <form action="" method="GET" class="d-flex gap-3 mb-2">
                            <div>
                                <select name="agenceFilter_id1" class="js-single" style="width: 150px;" id="agenceFilter_id1"
                                    required>
                                    <option value="">Filtre Agence</option>
                                    @if ($nbrAgence)
                                        <option value="0">Tous</option>
                                    @endif
                                    @foreach ($agencesUser as $agenceUser)
                                        <option
                                            {{ isset($_GET['agenceFilter_id1']) && $_GET['agenceFilter_id1'] == $agenceUser->agence->id ? 'selected' : '' }}
                                            value="{{ $agenceUser->agence->id }}">{{ $agenceUser->agence->NomAgence }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary" style="height: 28px; line-height: 1;">Filtre</button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des Factures</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ProduitsTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>N°Facture</th>
                                        <th>Signature MEcef</th>
                                        <th>Client</th>
                                        <th>Adresse</th>
                                        <th>N°IFU</th>
                                        <th>Pays de livraison</th>
                                        <th>Date de livraison</th>
                                        <th>Montant HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($factures as $facture)
                                        <tr>
                                            <td>{{ $facture->Date_facture }}</td>
                                            <td>{{ $facture->reference_facture }}</td>
                                            <td>{{ $facture->Code_signature }}</td>
                                            <td>{{ $facture->client->Denomination_sociale }}</td>
                                            <td>{{ $facture->client->Adresse_client }}</td>
                                            <td>{{ $facture->client->Numero_ifu }}</td>
                                            <td>{{ $facture->client->Pays }}</td>
                                            <td>{{ $facture->Date_signature }}</td>
                                            <td>{{ $facture->Net_a_payer }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade @if ($isFiltered) show active @endif" style="margin-bottom: 150px"
                id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">

                <div class="row mt-4">
                    <div class="col-md-12">
                        <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center" method="GET"
                            action="">
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Debut</span>
                                    <input name="debut2" type="date" value="{{ $requestLigneFacture->get('debut2') }}"
                                        class="form-control" id="debut" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Fin</span>
                                    <input name="fin2" type="date" class="form-control" id="fin"
                                        value="{{ $requestLigneFacture->get('fin2') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-sm btn-primary">Appliquer</button>
                                    </div>
                                </div>

                            </div>
                            <div class="col-auto">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="btn-group">
                                        <a href="{{ route('lignesFacturesCsv.export') }}"
                                            class="btn btn-sm btn-success">Exporter en CSV</a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $nbrAgence = auth()->user()->agences->count() > 1;
                    $agencesUser = auth()->user()->agences->all('*');
                @endphp

                <div class="d-flex bd-highlight }}">
                    @if (auth()->user()->agences->count() > 1)
                        <form action="" method="GET" class="d-flex gap-3 mb-2">
                            <div>
                                <select name="agenceFilter_id2" class="js-single" style="width: 150px;" id="agenceFilter_id2"
                                    required>
                                    <option value="">Filtre Agence</option>
                                    @if ($nbrAgence)
                                        <option value="0">Tous</option>
                                    @endif
                                    @foreach ($agencesUser as $agenceUser)
                                        <option
                                            {{ isset($_GET['agenceFilter_id2']) && $_GET['agenceFilter_id2'] == $agenceUser->agence->id ? 'selected' : '' }}
                                            value="{{ $agenceUser->agence->id }}">{{ $agenceUser->agence->NomAgence }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-primary" style="height: 28px; line-height: 1;">Filtre</button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des lignes factures </h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ProduitsTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>N°Facture</th>
                                        <th>Code Article</th>
                                        <th>Désignation</th>
                                        <th>Famille</th>
                                        <th>Qté</th>
                                        <th>Prix Unitaire Net</th>
                                        <th>Montant HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lignesFactures as $lignesFacture)
                                        <tr>
                                            <td>{{ $lignesFacture->created_at }}</td>
                                            <td>{{ $lignesFacture->facture->Reference_facture }}</td>
                                            <td>{{ $lignesFacture->stock->produit->Reference }}</td>
                                            <td>{{ $lignesFacture->stock->produit->Designation }}</td>
                                            <td>{{ $lignesFacture->stock->produit->categorieProduit->Libelle }}</td>
                                            <td>{{ $lignesFacture->Qte }}</td>
                                            <td>{{ $lignesFacture->Prix_unitaire_HT }}</td>
                                            <td>{{ $lignesFacture->Qte * $lignesFacture->Prix_unitaire_HT }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#clientTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                var modifierUrl = "{{ route('editClient', ['id' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                // Mettre à jour l'attribut href du lien
                $('#modifierLink').attr('href', modifierUrl);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#clientTable').on('click', '.clickable-row', function() {
                $('#clientTable .clickable-row td').css({
                    'background-color': '',
                    'color': 'black'
                });
                $(this).find('td').css({
                    'background-color': 'rgb(29, 9, 101)',
                    'color': 'white'
                });

                var ID = $(this).data('id');

                $('#clientDetail').attr('data-bs-target', '#exampleModal_' + ID);
            });
        });
    </script>

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
            width: 2000px;
        }

        .tableInfo th,
        .tableInfo td {
            /*border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>

    @include('layouts.alert')
@endSection
