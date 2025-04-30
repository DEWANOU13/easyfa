@php
    use App\Models\Client;

    if (request('client') != null) {
        $nomClient = Client::where('id', request('client'))->first()->Denomination_sociale;
    }

    $debut_periode = request('debut_periode') == null ? 'Debut' : request('debut_periode');
    $fin_periode = request('fin_periode') == null ? 'Fin' : request('fin_periode');
    $clientRecherche = request('client') == null ? 'Tous les clients' : $nomClient;
@endphp

@extends('layouts.master', ['title' => 'Client'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Client',
        'infos2' => 'Client',
        'infos3' => 'Liste',
    ])

    <section style="margin-bottom: 150px">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link @if (!$isFiltered) active @endif" id="nav-home-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-liste" type="button" role="tab" aria-controls="nav-home"
                    aria-selected="true">Liste</button>
                @can('voir-compte-client')
                    <button class="nav-link @if ($isFiltered) active @endif" id="nav-profile-tab"
                        data-bs-toggle="tab" data-bs-target="#nav-compte" type="button" role="tab"
                        aria-controls="nav-profile" aria-selected="false">Compte</button>
                @endcan
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade @if (!$isFiltered) show active @endif" id="nav-liste" role="tabpanel"
                aria-labelledby="nav-home-tab" tabindex="0">
                <div class="row">
                    <div class="col-12 mb-5 mt-3">
                        <div class="d-flex flex-row-reverse bd-highlight">
                            <div class="dropdown mb-2">
                                <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false" style="{{ background_color_1() }}">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    @can('creer-client')
                                        <li>
                                            <a href="{{ route('showFormClient') }}" class="dropdown-item"
                                                type="button">Nouveau</a>
                                        </li>
                                    @endcan
                                    @can('modifier-client')
                                        <li><a id="modifierLink" class="dropdown-item" type="button">Modifier</a></li>
                                    @endcan
                                    <li>
                                        <a type="button" class="dropdown-item" id="clientDetail" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            Details
                                        </a>
                                    </li>
                                    @can('exporter-client')
                                        <li>
                                            <a id="exportButton" class="dropdown-item" data-bs-toggle="modal" type="button"
                                                data-bs-target="#staticBackdropClientExcel">
                                                Exporter
                                            </a>
                                        </li>
                                    @endcan

                                    @can('imprimer-client')
                                        <li>
                                            <a id="importerLink" class="dropdown-item" type="button"
                                                data-bs-target="#imprimerClientPdf" data-bs-toggle="modal">Imprimer</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </div>

                        {{-- Modal export liste de client --}}
                        <div class="modal fade" id="staticBackdropClientExcel" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment exporter les données de la liste des clients en Excel ?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                                        <button id="exportExcelClient" class="btn btn-sm btn-success"
                                            data-bs-dismiss="modal">Oui Exporter en Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal pour l'importation de la liste des clients en pdf --}}
                        <div class="modal fade" id="imprimerClientPdf" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Non</button>
                                        <button id="importClientPDF_Imp" target="_blank"
                                            class="btn btn-sm btn-success">Oui
                                            Importer en
                                            PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card m-b-30">
                            <div class="card-header" style="{{ background_color_2() }}">
                                <h3 class="mt-2  d-inline-block text-dark">Liste des clients</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="clientTable"
                                        class="datatable table table-striped table-bordered dt-responsive nowrap tableInfo"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Code Client</th>
                                                <th>Dénomination sociale</th>
                                                <th>Numero IFU</th>
                                                <th>Adresse</th>
                                                <th>Téléphone mobile</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php $counter = 1; @endphp
                                            @forelse ($listeClient as $client)
                                                <tr class="clickable-row"
                                                    data-url="{{ route('editClient', $client->id) }}"
                                                    data-id="{{ $client->id }}">
                                                    <td>{{ $counter }}</td>
                                                    <td>{{ $client->Code_client }}</td>
                                                    <td>{{ $client->Denomination_sociale }}</td>
                                                    <td>{{ $client->Numero_ifu }}</td>
                                                    <td>{{ $client->Adresse_client }}</td>
                                                    <td>{{ $client->Telephone_mobile }}</td>
                                                    <td>{{ $client->Adresse_mail }}</td>
                                                </tr>
                                                @php $counter++; @endphp

                                                {{-- Modal details --}}
                                                <div class="modal fade" id="exampleModal_{{ $client->id }}"
                                                    tabindex="-1" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                                    {{ 'Information du client ' . $client->Denomination_sociale }}
                                                                </h1>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="font-size: 16px">
                                                                <div class="table table-striped">
                                                                    <div class="d-flex">
                                                                        <span>Code Client :&nbsp;</span>
                                                                        <span>{{ $client->Code_client }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex group-form">
                                                                        <span>Denomination Sociale :&nbsp;</span>
                                                                        <span>{{ $client->Denomination_sociale }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Adresse Client :&nbsp;</span>
                                                                        <span>{{ $client->Adresse_client }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Telephone Mobile :&nbsp;</span>
                                                                        <span>{{ $client->Telephone_mobile }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Telephone Fixe :&nbsp;</span>
                                                                        <span>{{ $client->Telephone_fixe }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Adresse Mail :&nbsp;</span>
                                                                        <span>{{ $client->Adresse_mail }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Pays :&nbsp;</span>
                                                                        <span>{{ $client->Pays }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>IFU :&nbsp;</span>
                                                                        <span>{{ $client->Numero_ifu }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Categorie Client :&nbsp;</span>
                                                                        <span>{{ $client->categorie_client->Libelle }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Créé par :&nbsp;</span>
                                                                        <span>{{ $client->user->name }}</span>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex">
                                                                        <span>Statut :&nbsp;</span>
                                                                        <span>
                                                                            @if ($client->Statut_client == 1)
                                                                                <span
                                                                                    class="form-check d-flex justify-content-end">
                                                                                    <span class="bg-primary  rounded">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            class="icon icon-tabler icon-tabler-check text-white"
                                                                                            width="24" height="24"
                                                                                            viewBox="0 0 24 24"
                                                                                            stroke-width="1.5"
                                                                                            stroke="currentColor"
                                                                                            fill="none"
                                                                                            stroke-linecap="round"
                                                                                            stroke-linejoin="round">
                                                                                            <path stroke="none"
                                                                                                d="M0 0h24v24H0z"
                                                                                                fill="none" />
                                                                                            <path d="M5 12l5 5l10 -10" />
                                                                                        </svg>
                                                                                    </span>
                                                                                </span>
                                                                            @else
                                                                                <span
                                                                                    class="form-check d-flex justify-content-end">
                                                                                    <div
                                                                                        class="btn btn-primary bg-light  rounded">
                                                                                    </div>
                                                                                </span>
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Fermer</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td colspan="13" class="text-center">Aucun client trouvé</td>
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

            @can('voir-compte-client')
                <div class="tab-pane @if ($isFiltered) show active @endif fade" id="nav-compte"
                    role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    @can('importer-compte-client')
                        <div class="d-flex flex-row-reverse bd-highlight mt-2">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false" style="{{ background_color_1() }}">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    @can('importer-compte-client')
                                        <li>
                                            <a id="exportButton" class="dropdown-item" data-bs-toggle="modal" type="button"
                                                data-bs-target="#staticBackdropClientCompteExcel">
                                                Exporter
                                            </a>
                                        </li>
                                    @endcan
                                    <li>
                                        <a id="exportButton" class="dropdown-item" data-bs-toggle="modal" type="button"
                                            data-bs-target="#staticBackdropClientComptePdf">
                                            Imprimer
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endcan

                    {{-- Modal export compte de client --}}
                    <div class="modal fade" id="staticBackdropClientCompteExcel" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données des compte des clients en Excel ?
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportExcelClientCompte" class="btn btn-sm btn-success"
                                        data-bs-dismiss="modal">Oui
                                        Exporter en Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal importer compte de client --}}
                    <div class="modal fade" id="staticBackdropClientComptePdf" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment importer les données des comptes des clients en Pdf ?
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPdfClientCompte" class="btn btn-sm btn-success"
                                        data-bs-dismiss="modal">Oui
                                        Exporter en Pdf</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fw-bold @cannot('importer-compte-client') mt-3 @endcannot mb-3">Compte Client</div>
                    <form action="{{ route('filtre-compte') }}" method="POST">
                        @csrf
                        <div class="d-flex gap-5">
                            <div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Debut</span>
                                    <input name="debut_periode" type="date" class="form-control"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
                                        value="{{ $date_debut ?? null }}">
                                </div>
                            </div>
                            <div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Fin</span>
                                    <input name="fin_periode" type="date" class="form-control"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
                                        value="{{ $date_fin ?? null }}">
                                </div>
                            </div>
                            <div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">Client</span>
                                    <select class="form-select js-single" name="client" id="client">
                                        @if (isset($client_filtre))
                                            <option value="{{ $client_filtre->id }}">
                                                {{ $client_filtre->Denomination_sociale }}</option>
                                        @else
                                            <option value="">Sélectionnez un client</option>
                                        @endif
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">
                                                {{ $client->Denomination_sociale }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-sm"
                                    style="{{ background_color_1() }}; color: white;">Appliquer</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <div class="card m-b-30">
                            <div class="card-header" style="{{ background_color_2() }}">
                                <h3 class="mt-2 d-inline-block text-dark">Liste des règlements et factures</h3>
                            </div>
                            <table id="clientsTable" class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>Opération</th>
                                        <th>Justificatif</th>
                                        <th>Débit</th>
                                        <th>Crédit</th>
                                        <th>Solde</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @if ($solde_initial && $date_initiale) --}}


                                    {{-- @endif --}}
                                    @php
                                        $solde = 0;
                                        // Convertir les tableaux en collections
                                        $historique_reglements = collect($historique_reglements ?? []);
                                        $factures = collect($factures ?? []);

                                        // Fusionner et trier les deux ensembles de données
                                        $merged_data = $historique_reglements
                                            ->map(function ($item) {
                                                // Vérifier que les propriétés nécessaires existent
                                                if (
                                                    !isset($item->Date_Reglement) ||
                                                    !isset($item->Reference_Reglement) ||
                                                    !isset($item->Reference_Facture) ||
                                                    !isset($item->Montant_Regle)
                                                ) {
                                                    return null; // Ignorer cette entrée si elle est incomplète
                                                }

                                                return [
                                                    'date' => $item->Date_Reglement,
                                                    'operation' => $item->Operation ?? 'Inconnu',
                                                    'justificatif' =>
                                                        $item->Reference_Reglement . ' - ' . $item->Reference_Facture,
                                                    'debit' =>
                                                        isset($item->Operation) && $item->Operation === 'REGLEMENT ANNULE'
                                                            ? $item->Montant_Regle
                                                            : '-',
                                                    'credit' =>
                                                        isset($item->Operation) && $item->Operation === 'REGLEMENT'
                                                            ? $item->Montant_Regle
                                                            : '-',
                                                    'source' => 'reglement',
                                                ];
                                            })
                                            ->filter() // Filtrer les valeurs nulles
                                            ->merge(
                                                $factures
                                                    ->map(function ($item) {
                                                        // Vérifier que les propriétés nécessaires existent
                                                        if (
                                                            !isset($item->Date_facture) ||
                                                            !isset($item->Reference_facture) ||
                                                            !isset($item->Net_a_payer)
                                                        ) {
                                                            return null; // Ignorer cette entrée si elle est incomplète
                                                        }

                                                        return [
                                                            'date' => $item->Date_facture,
                                                            'operation' =>
                                                                isset($item->Code_type_facture) &&
                                                                $item->Code_type_facture === 'FV'
                                                                    ? 'FACTURE VENTE'
                                                                    : 'FACTURE AVOIR',
                                                            'justificatif' => $item->Reference_facture,
                                                            'debit' =>
                                                                isset($item->Code_type_facture) &&
                                                                $item->Code_type_facture === 'FV'
                                                                    ? $item->Net_a_payer
                                                                    : '-',
                                                            'credit' =>
                                                                isset($item->Code_type_facture) &&
                                                                $item->Code_type_facture === 'FA'
                                                                    ? $item->Net_a_payer
                                                                    : '-',
                                                            'source' => 'facture',
                                                        ];
                                                    })
                                                    ->filter(), // Filtrer les valeurs nulles
                                            );

                                        // Trier par date
                                        $sorted_data = $merged_data->sortBy('date');
                                    @endphp

                                    @if (isset($solde_initial))
                                        <tr>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;">
                                                {{ \Carbon\Carbon::parse($date_initiale)->format('d/m/Y H:i:s') }}</td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" class="fw-bold">
                                                RAPPORT</td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" class="fw-bold">
                                            </td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" class="fw-bold">
                                            </td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" class="fw-bold">
                                            </td>
                                            <td style="background-color: rgb(46, 0, 124); color:aliceblue;" class="fw-bold">
                                                {{  number_format($solde_initial, 0, ',', ' ')}}</td>
                                            @php
                                                $solde += $solde_initial;
                                            @endphp
                                        </tr>
                                    @endif


                                    @foreach ($sorted_data as $data)
                                        <tr>
                                            {{-- Date formatée --}}
                                            <td>{{ \Carbon\Carbon::parse($data['date'])->format('d/m/Y H:i:s') }}</td>

                                            {{-- Opération (REGLEMENT, REGLEMENT ANNULE, FACTURE VENTE, FACTURE AVOIR) --}}
                                            <td>{{ $data['operation'] }}</td>

                                            {{-- Justificatif (références) --}}
                                            <td>{{ $data['justificatif'] }}</td>
                                            {{-- number_format($t_quantity, 0, ',', ' ') --}}
                                            {{-- Débit --}}
                                            <td>{{ $data['debit'] !== '-' ? number_format($data['debit'], 0, ',', ' ') : '-' }}</td>

                                            {{-- Crédit --}}
                                            <td>{{ $data['credit'] !== '-' ? number_format($data['credit'], 0, ',', ' ') : '-' }}</td>

                                            {{-- Calcul du solde en fonction du débit et du crédit --}}
                                            @php
                                                if ($data['debit'] !== '-') {
                                                    $solde -= $data['debit'];
                                                } elseif ($data['credit'] !== '-') {
                                                    $solde += $data['credit'];
                                                }
                                            @endphp

                                            {{-- Affichage du solde actuel --}}
                                            <td>{{ number_format($solde, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>


                            </table>
                        </div>
                    </div>

                </div>
            @endcan
        </div>
    </section>

    {{-- Exportation en fichier excel de la liste des client --}}
    <script>
        $(document).ready(function() {
            $('#exportExcelClient').on('click', function() {
                var tableClientData = [];
                $('#clientTable tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        numero: row.find('td').eq(0).text(),
                        code_client: row.find('td').eq(1).text(),
                        denomination_sociale: row.find('td').eq(2).text(),
                        numero_ifu: row.find('td').eq(3).text(),
                        adresse: row.find('td').eq(4).text(),
                        telephone: row.find('td').eq(5).text(),
                        email: row.find('td').eq(6).text()
                    };
                    tableClientData.push(rowData);
                });


                var exportData = {
                    tableClientData: tableClientData
                };
                // console.log(exportData);

                $.ajax({
                    type: 'POST',
                    url: '/export_excel_client',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'liste_client.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(status);
                        console.log(error);
                    }
                });
            });
        });
    </script>

    {{-- Importation en fichier PDF de la liste des client  --}}
    <script>
        $('#importClientPDF_Imp').off('click').on('click', function() {
            var newWindow = null;

            //chargement
            var $button = $(this);
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var tableClientData = [];
            $('#clientTable tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    numero: row.find('td').eq(0).text(),
                    code_client: row.find('td').eq(1).text(),
                    denomination_sociale: row.find('td').eq(2).text(),
                    numero_ifu: row.find('td').eq(3).text(),
                    adresse: row.find('td').eq(4).text(),
                    telephone: row.find('td').eq(5).text(),
                    email: row.find('td').eq(6).text()
                };
                tableClientData.push(rowData);
            });

            var exportData = {
                tableClientData: tableClientData,
            };


            $.ajax({
                type: 'POST',
                url: '/import_pdf_client',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
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

                    $('#imprimerClientPdf').modal('hide');
                },

                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');
                }
            });
        });
    </script>

    {{-- Exportation en fichier excel de la liste des comptes des client --}}
    <script>
        $(document).ready(function() {
            $('#exportExcelClientCompte').on('click', function() {
                var tableClientCompteData = [];
                $('#clientsTable tbody tr').each(function() {
                    var row = $(this);
                    var rowData = {
                        date: row.find('td').eq(0).text(),
                        client: row.find('td').eq(1).text(),
                        operation: row.find('td').eq(2).text(),
                        justificatif: row.find('td').eq(3).text(),
                        debit: row.find('td').eq(4).text(),
                        credit: row.find('td').eq(5).text(),
                        compte: row.find('td').eq(6).text()
                    };
                    tableClientCompteData.push(rowData);
                });


                var exportData = {
                    tableClientCompteData: tableClientCompteData
                };

                $.ajax({
                    type: 'POST',
                    url: '/export_excel_client_compte',
                    data: JSON.stringify(exportData),
                    contentType: 'application/json',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var blob = new Blob([response], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'compte_client.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(status);
                        console.log(error);
                    }
                });
            });
        });
    </script>

    {{-- Importation en fichier PDF de la liste des comptes des clients --}}
    <script>
        var ajaxResponse;

        $('#exportPdfClientCompte').off('click').on('click', function() {
            var newWindow = null;

            //chargement
            var $button = $(this);
            $button.addClass('loading');
            $button.prop('disabled', true);
            $button.text('Exportation en cours...');

            var clientsTableData = [];
            $('#clientsTable tbody tr').each(function() {
                var row = $(this);
                var rowData = {
                    date: row.find('td').eq(0).text(),
                    client: row.find('td').eq(1).text(),
                    operation: row.find('td').eq(2).text(),
                    justificatif: row.find('td').eq(3).text(),
                    debit: row.find('td').eq(4).text(),
                    credit: row.find('td').eq(5).text(),
                    compte: row.find('td').eq(6).text()
                };
                clientsTableData.push(rowData);
            });

            var exportData = {
                clientsTableData: clientsTableData,
                debut_periode: @json($debut_periode),
                fin_periode: @json($fin_periode),
                clientRecherche: @json($clientRecherche),
            };


            $.ajax({
                type: 'POST',
                url: '/import_pdf_compte_client',
                data: JSON.stringify(exportData),
                contentType: 'application/json',
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
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

                    $('#staticBackdropClientComptePdf').modal('hide');
                },

                error: function(xhr, status, error) {
                    console.error(error);

                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    $button.text('Oui, Exporter en PDF');
                }
            });

        });
    </script>

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
        .selected>td {
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
