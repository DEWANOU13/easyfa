@extends('layouts.master', ['title' => 'Statistique Rapport'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Rapport',
        'infos2' => 'Rapport',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @can('statistique-rapport-vente-produit-sans-marge')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cumProSansMarge-tab" data-bs-toggle="tab"
                            data-bs-target="#cumProSansMarge" type="button" role="tab" aria-controls="cumProSansMarge"
                            aria-selected="false">Rapport vente
                            Produit sans marge </button>
                    </li>
                @endcan

                @can('statistique-rapport-vente-produit')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cumPro-tab" data-bs-toggle="tab" data-bs-target="#cumPro" type="button"
                            role="tab" aria-controls="cumPro" aria-selected="false">Rapport vente
                            Produit </button>
                    </li>
                @endcan

                @can('statistique-rapport-vente-prestation')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rappPres-tab" data-bs-toggle="tab" data-bs-target="#rappPres"
                            type="button" role="tab" aria-controls="rappPres" aria-selected="false">Rapport vente
                            Prestation </button>
                    </li>
                @endcan

            </ul>
        </div>

        <div class="tab-content" id="myTabContent">
            @can('statistique-rapport-vente-produit-sans-marge')
                <div class="tab-pane fade show active" id="cumProSansMarge" role="tabpanel"
                    aria-labelledby="cumProSansMarge-tab">
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <form id="form1SansMarge" action="{{ route('rapportVenteSansMarge') }}" method="GET">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="fw-bold">Agence</span>
                                            <select name="agence" type="text" class="form-select js-single  w-100"
                                                style="width: 100%;" id="agenceSansMarge">
                                                @forelse ($listeAgence as $agence)
                                                    @if ($agence === 'Siège')
                                                        <option value="Toutes">Toutes</option>
                                                    @endif
                                                    <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-3 ">
                                            <span class="fw-bold">Produit</span>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="produit" type="text" class="form-select js-single w-100"
                                                    style="width: 100%;" id="produitSansMarge" aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                                    <option value="">Sélectionnez un produit</option>
                                                    <option value="Tous">Tous</option>
                                                    @foreach ($produit as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->Designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fw-bold">Du</span>
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="datetime-local" name="dateDebut" class="form-control"
                                                    id="dateDebut" max="{{ date('Y-m-d\TH:i') }}" required
                                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="fw-bold">Au</span>
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="datetime-local" name="dateFin" class="form-control" id="dateFin"
                                                    max="{{ date('Y-m-d\TH:i') }}" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span>&nbsp</span>
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="btn-group">
                                                    <button type="submit" id="AppliquerFormSansMarge" class="btn text-white"
                                                        style="{{ background_color_1() }}">Appliquer</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="dateError3" class="alert alert-danger" style=" display:none;">La date de début
                                        doit être inférieure à la date de fin.</div>

                                </form>
                            </fieldset>
                        </div>
                    </div>
                    @can('excel-rapport-statistique')
                        <button id="exportButtonMPSansMarge" data-bs-toggle="modal" data-bs-target="#staticBackdrop1SansMarge"
                            class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('pdf-rapport-statistique')
                        <button id="exportButtonMPPSansMarge" data-bs-toggle="modal" data-bs-target="#staticBackdrop1PSansMarge"
                            class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                    @endcan

                    @can('excel-rapport-statistique')
                        <div class="modal fade" id="staticBackdrop1SansMarge" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabelSansMarge" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                        <button id="exportExcel_MPSansMarge" class="btn btn-sm btn-success">Oui Exporter en
                                            Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('pdf-rapport-statistique')
                        <div class="modal fade" id="staticBackdrop1PSansMarge" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelSansMarge"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                        <button id="exportPDF_MPSansMarge" target="_blank" class="btn btn-sm btn-success">Oui
                                            Exporter
                                            en PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan



                    <div class="card m-b-30 mb-5 mt-2">
                        <div class="card-body">
                            <div class="table-responsive mt-2">
                                <table id="tableMargeParProduitSansMarge" class="table  tableInfo"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="{{ background_color_2() }}" scope="col">Code</th>
                                            <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                            <th style="{{ background_color_2() }}" scope="col">Quantité stockée(solde
                                                initial)</th>
                                            <th style="{{ background_color_2() }}" scope="col">Quantité vendue</th>
                                            <th style="{{ background_color_2() }}" scope="col">Quantité sortie</th>
                                            <th style="{{ background_color_2() }}" scope="col">Quantité entrée</th>
                                            <th style="{{ background_color_2() }}" scope="col">Quantité restant</th>
                                            <th style="{{ background_color_2() }}" scope="col">Ventes</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>
                            <div id="req_message3" style="text-align: center; color: red; display: none;">
                                Veuillez faire une requette pour afficher les données
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="tab-pane fade show" id="cumPro" role="tabpanel" aria-labelledby="cumPro-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form1" action="{{ route('rapportVente') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence3">
                                            <option></option>
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Produit</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="produit" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="produit" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un produit</option>
                                                <option value="Tous">Tous</option>
                                                @foreach ($produit as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Du</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateDebut" class="form-control"
                                                id="dateDebut" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateFin" class="form-control"
                                                id="dateFin" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span>&nbsp</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm1" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError1" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                @can('statistique-rapport-vente-produit-excel')
                    <button id="exportButtonMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-rapport-vente-produit-pdf')
                    <button id="exportButtonMPP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan

                <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MP" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="staticBackdrop1P" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF_MP" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableMargeParProduit" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Code</th>
                                        <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité stockée(solde
                                            initial)</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité vendue</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité sortie</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité entrée</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité restant</th>
                                        <th style="{{ background_color_2() }}" scope="col">Ventes</th>
                                        <th style="{{ background_color_2() }}" scope="col">Cout</th>
                                        <th style="{{ background_color_2() }}" scope="col">Marge</th>
                                        <th style="{{ background_color_2() }}" scope="col">Taux</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <div id="req_message1" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="rappPres" role="tabpanel" aria-labelledby="rappPres-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form2" action="{{ route('rapportVentePrestation') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence4">
                                            <option></option>
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Prestation</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="prestation" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="prestation" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez une prestation</option>
                                                <option value="Tous">Tous</option>
                                                @foreach ($prestation as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Du</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateDebut" class="form-control"
                                                id="dateDebut" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateFin" class="form-control"
                                                id="dateFin" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span>&nbsp</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm2" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError2" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                @can('statistique-rapport-vente-prestation-excel')
                    <button id="exportButtonMPPres" data-bs-toggle="modal" data-bs-target="#staticBackdrop2"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-rapport-vente-prestation-pdf')
                    <button id="exportButtonMPresP" data-bs-toggle="modal" data-bs-target="#staticBackdrop2P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan

                <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MPres" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="staticBackdrop2P" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF_MPres" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableRapportPrestation" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Code</th>
                                        <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantité vendue</th>
                                        <th style="{{ background_color_2() }}" scope="col">Ventes</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                        <div id="req_message2" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <script>
            const formIds = ['form1', 'form2', 'form1SansMarge'];

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
        {{-- marge par produit --}}
        <script>
            $(document).ready(function() {
                var exportButtonMP = document.getElementById("exportButtonMP");
                var exportButtonMPP = document.getElementById("exportButtonMPP");
                var table = document.getElementById("tableMargeParProduit").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMP.disabled = true;
                        exportButtonMPP.disabled = true;
                        $('#req_message1').show();
                    } else {
                        exportButtonMP.disabled = false;
                        exportButtonMPP.disabled = false;
                        $('#req_message1').hide();
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



                $('#form1').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError1').show();
                        return;
                    } else {
                        $('#dateError1').hide();
                    }
                    /*
                    var $button = $('#AppliquerForm1');
                    $button.addClass('loading');
                    $button.prop('disabled', true); */


                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParProduit tbody').empty();

                            Object.keys(response.listeVente).forEach(function(categorie) {
                                var montantAchatTotalCategorie = 0;
                                var montantVenteTotalCategorie = 0;
                                var totalMargeCategorie = 0;
                                //  var totalTauxCategorie = 0;
                                var rows = response.listeVente[categorie].map(function(
                                    item) {
                                    var montantVenteArrondi = Math.round(item
                                        .Prix_vente) || 0;
                                    montantVenteTotalCategorie +=
                                        montantVenteArrondi;

                                    var montantAchatArrondi = Math.round(item
                                        .Prix_achat) || 0;
                                    montantAchatTotalCategorie +=
                                        montantAchatArrondi;

                                    var marge = (montantVenteArrondi -
                                        montantAchatArrondi) || 0;
                                    totalMargeCategorie += marge;

                                    if (montantAchatArrondi > 0) {
                                        var taux = (marge / montantAchatArrondi) *
                                            100;
                                    } else {
                                        var taux = 0; // Taux par défaut
                                    }

                                    //  var taux = marge / montantAchatArrondi * 100;

                                    var QuantiteRestant = item.SoldeInitial + item
                                        .Quantite_Entrees - item.Quantite - item
                                        .Quantite_Sorties;
                                    //totalTauxCategorie += taux;

                                    return `
                                    <tr>
                                        <td>${item.Reference}</td>
                                        <td>${item.Designation}</td>
                                        <td>${numberFormat(item.SoldeInitial)}</td>
                                        <td>${numberFormat(item.Quantite)}</td>
                                        <td>${numberFormat(item.Quantite_Sorties)}</td>
                                        <td>${numberFormat(item.Quantite_Entrees)}</td>
                                        <td>${numberFormat(QuantiteRestant)}</td>
                                        <td>${numberFormat(montantVenteArrondi)}</td>
                                        <td>${numberFormat(montantAchatArrondi)}</td>
                                        <td>${numberFormat(marge)}</td>
                                        <td>${numberFormat(taux)}%</td>
                                    </tr>`;
                                }).join('');

                                var categorieRow = `
                                <tr class="table-primary">
                                    <td colspan="11"><strong>${categorie}</strong></td>
                                </tr>`;

                                var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="7">Total pour la catégorie</td>
                                    <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                    <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                    <td>${numberFormat(totalMargeCategorie)}</td>
                                    <td>${numberFormat((totalMargeCategorie / (montantAchatTotalCategorie || 1)) * 100) || 0}%</td>
                                </tr>`;

                                $('#tableMargeParProduit tbody').append(categorieRow +
                                    rows + totalCategorieRow);
                            });

                            if ($('#tableMargeParProduit tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }
                            $('#tableMargeParProduit').DataTable();

                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        }
                    });
                });
                $('#exportExcel_MP').off('click').on('click', function() {
                    var $button = $(this);

                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParProduitData = [];
                        $('#tableMargeParProduit tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Reference: row.find('td').eq(0).text(),
                                Designation: row.find('td').eq(1).text(),
                                TotalEntree: row.find('td').eq(2).text(),
                                Quantite: row.find('td').eq(3).text(),
                                Quantite_Sorties: row.find('td').eq(4).text(),
                                Quantite_Entrees: row.find('td').eq(5).text(),
                                QuantiteRestant: row.find('td').eq(6).text(),
                                montantVente: row.find('td').eq(7).text(),
                                montantAchat: row.find('td').eq(8).text(),
                                marge: row.find('td').eq(9).text(),
                                taux: row.find('td').eq(10).text(),
                            };
                            tableMargeParProduitData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParProduitData: tableMargeParProduitData,
                            infoProduit: ajaxResponse.infoProduit,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence
                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_rapport_vente',
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
                                a.download = 'rapport_vente.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                // Réactiver le bouton et retirer le spinner après la complétion
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop1').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                // Réactiver le bouton et retirer le spinner après l'échec
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MP').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');


                    var tableMargeParProduitData = [];
                    $('#tableMargeParProduit tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Reference: row.find('td').eq(0).text(),
                            Designation: row.find('td').eq(1).text(),
                            TotalEntree: row.find('td').eq(2).text(),
                            Quantite: row.find('td').eq(3).text(),
                            Quantite_Sorties: row.find('td').eq(4).text(),
                            Quantite_Entrees: row.find('td').eq(5).text(),
                            QuantiteRestant: row.find('td').eq(6).text(),
                            montantVente: row.find('td').eq(7).text(),
                            montantAchat: row.find('td').eq(8).text(),
                            marge: row.find('td').eq(9).text(),
                            taux: row.find('td').eq(10).text(),
                        };
                        tableMargeParProduitData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParProduitData: tableMargeParProduitData,
                        infoProduit: ajaxResponse.infoProduit,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_rapport_vente_pdf',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = window.URL.createObjectURL(blob);

                            // Ouvrir un nouvel onglet avec le PDF
                            newWindow = window.open(url, '_blank');

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                            $('#staticBackdrop1P').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Masquer l'animation
                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                        }
                    });
                });


            });
        </script>
        {{--  // vente produit sans marge --}}
        <script>
            $(document).ready(function() {
                var exportButtonMPSansMarge = document.getElementById("exportButtonMPSansMarge");
                var exportButtonMPPSansMarge = document.getElementById("exportButtonMPPSansMarge");
                var table = document.getElementById("tableMargeParProduitSansMarge").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMPSansMarge.disabled = true;
                        exportButtonMPPSansMarge.disabled = true;
                        $('#req_message3').show();
                    } else {
                        exportButtonMPSansMarge.disabled = false;
                        exportButtonMPPSansMarge.disabled = false;
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



                $('#form1SansMarge').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError3').show();
                        return;
                    } else {
                        $('#dateError3').hide();
                    }

                    /*     var $button = $('#AppliquerFormSansMarge');
                        $button.addClass('loading');
                        $button.prop('disabled', true); */


                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParProduitSansMarge tbody').empty();
                            Object.keys(response.listeVente).forEach(function(categorie) {
                                var montantAchatTotalCategorie = 0;
                                var montantVenteTotalCategorie = 0;
                                var totalMargeCategorie = 0;
                                //  var totalTauxCategorie = 0;
                                var totalVenteSansMarge = 0;
                                var rows = response.listeVente[categorie].map(function(
                                    item) {
                                    var montantVenteArrondi = Math.round(item
                                        .Prix_vente) || 0;
                                    montantVenteTotalCategorie +=
                                        montantVenteArrondi;

                                    var montantAchatArrondi = Math.round(item
                                        .Prix_achat) || 0;
                                    montantAchatTotalCategorie +=
                                        montantAchatArrondi;

                                    var marge = (montantVenteArrondi -
                                        montantAchatArrondi) || 0;
                                    totalMargeCategorie += marge;

                                    totalVenteSansMarge += montantAchatArrondi

                                    if (montantAchatArrondi > 0) {
                                        var taux = (marge / montantAchatArrondi) *
                                            100;
                                    } else {
                                        var taux = 0; // Taux par défaut
                                    }

                                    //  var taux = marge / montantAchatArrondi * 100;

                                    var QuantiteRestant = item.SoldeInitial + item
                                        .Quantite_Entrees - item.Quantite - item
                                        .Quantite_Sorties;
                                    //totalTauxCategorie += taux;

                                    return `
                                    <tr>
                                        <td>${item.Reference}</td>
                                        <td>${item.Designation}</td>
                                        <td>${numberFormat(item.SoldeInitial)}</td>
                                        <td>${numberFormat(item.Quantite)}</td>
                                        <td>${numberFormat(item.Quantite_Sorties)}</td>
                                        <td>${numberFormat(item.Quantite_Entrees)}</td>
                                        <td>${numberFormat(QuantiteRestant)}</td>
                                        <td>${numberFormat(montantVenteArrondi)}</td>

                                    </tr>`;
                                }).join('');

                                var categorieRow = `
                                <tr class="table-primary">
                                    <td colspan="11"><strong>${categorie}</strong></td>
                                </tr>`;

                                /*      var agenceRow = `
                             <tr class="table-primary">
                                 <td colspan="11"><strong>${agence}</strong></td>
                             </tr>`; */

                                var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="4">Total pour la catégorie</td>
                                    <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                    <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                    <td>${numberFormat(totalMargeCategorie)}</td>
                                    <td>${numberFormat(totalVenteSansMarge) || 0}</td>
                                </tr>`;

                                $('#tableMargeParProduitSansMarge tbody').append(
                                    categorieRow +
                                    rows + totalCategorieRow);
                            });

                            if ($('#tableMargeParProduitSansMarge tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }
                            // $('#tableMargeParProduitSansMarge').DataTable();

                            checkTable();
                            button.removeClass('loading'); // Retire la classe .loading du bouton
                            button.prop('disabled', false); // Réactive le bouton
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            button.removeClass('loading'); // Retire la classe .loading du bouton
                            button.prop('disabled', false); // Réactive le bouton
                        }
                    });
                });
                $('#exportExcel_MPSansMarge').off('click').on('click', function() {
                    var $button = $(this);

                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParProduitData = [];
                        $('#tableMargeParProduitSansMarge tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Reference: row.find('td').eq(0).text(),
                                Designation: row.find('td').eq(1).text(),
                                TotalEntree: row.find('td').eq(2).text(),
                                Quantite: row.find('td').eq(3).text(),
                                Quantite_Sorties: row.find('td').eq(4).text(),
                                Quantite_Entrees: row.find('td').eq(5).text(),
                                QuantiteRestant: row.find('td').eq(6).text(),
                                montantVente: row.find('td').eq(7).text(),
                            };
                            tableMargeParProduitData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParProduitData: tableMargeParProduitData,
                            infoProduit: ajaxResponse.infoProduit,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence
                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_rapport_vente_sans_marge',
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
                                a.download = 'rapport_vente.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                // Réactiver le bouton et retirer le spinner après la complétion
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop1SansMarge').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                // Réactiver le bouton et retirer le spinner après l'échec
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MPSansMarge').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');


                    var tableMargeParProduitData = [];
                    $('#tableMargeParProduitSansMarge tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Reference: row.find('td').eq(0).text(),
                            Designation: row.find('td').eq(1).text(),
                            TotalEntree: row.find('td').eq(2).text(),
                            Quantite: row.find('td').eq(3).text(),
                            Quantite_Sorties: row.find('td').eq(4).text(),
                            Quantite_Entrees: row.find('td').eq(5).text(),
                            QuantiteRestant: row.find('td').eq(6).text(),
                            montantVente: row.find('td').eq(7).text(),
                        };
                        tableMargeParProduitData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParProduitData: tableMargeParProduitData,
                        infoProduit: ajaxResponse.infoProduit,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_rapport_vente_sans_marge_pdf',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = window.URL.createObjectURL(blob);

                            // Ouvrir un nouvel onglet avec le PDF
                            newWindow = window.open(url, '_blank');

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                            $('#staticBackdrop1PSansMarge').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Masquer l'animation
                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                        }
                    });
                });

            });
        </script>
        {{-- RAPPORT PAR PRESTATION --}}
        <script>
            $(document).ready(function() {
                var exportButtonMPPres = document.getElementById("exportButtonMPPres");
                var exportButtonMPPresP = document.getElementById("exportButtonMPPresP");
                var table = document.getElementById("tableRapportPrestation").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMPPres.disabled = true;
                        exportButtonMPresP.disabled = true;
                        $('#req_message2').show();
                    } else {
                        exportButtonMPPres.disabled = false;
                        exportButtonMPresP.disabled = false;
                        $('#req_message2').hide();
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



                $('#form2').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError2').show();
                        return;
                    } else {
                        $('#dateError2').hide();
                    }
                    /*
                    var $button = $('#AppliquerForm1');
                    $button.addClass('loading');
                    $button.prop('disabled', true); */


                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableRapportPrestation tbody').empty();
                            var montantVenteTotalCategorie = 0;
                            response.listeVente.forEach(function(item) {

                                //  var totalTauxCategorie = 0;

                                var montantVenteArrondi = Math.round(item
                                    .Prix_vente) || 0;
                                montantVenteTotalCategorie +=
                                    montantVenteArrondi;

                                const row = `<tr>
                                               <td>${item.Reference}</td>
                                                <td>${item.Designation}</td>
                                                <td>${numberFormat(item.Quantite)}</td>
                                                <td>${numberFormat(montantVenteArrondi)}</td>
                                        </tr>`;
                                $('#tableRapportPrestation tbody').append(row);

                            });
                            const grandTotalRow = `<tr class="font-weight-bold">
                                    <td colspan="3" >Total général</td>
                                    <td>${numberFormat(montantVenteTotalCategorie)}</td>
                            </tr>`;
                            $('#tableRapportPrestation tbody').append(grandTotalRow);



                            if ($('#tableRapportPrestation tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }


                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false); // Réactive le bouton
                        }
                    });
                });
                $('#exportExcel_MPres').off('click').on('click', function() {
                    var $button = $(this);

                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableRapportPrestationData = [];
                        $('#tableRapportPrestation tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Reference: row.find('td').eq(0).text(),
                                Designation: row.find('td').eq(1).text(),
                                Quantite: row.find('td').eq(2).text(),
                                montantVente: row.find('td').eq(3).text(),
                            };
                            tableRapportPrestationData.push(rowData);
                        });

                        var exportData = {
                            tableRapportPrestationData: tableRapportPrestationData,
                            infoProduit: ajaxResponse.infoProduit,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence
                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_rapport_vente_prestation',
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
                                a.download = 'rapport_vente_prestation.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                // Réactiver le bouton et retirer le spinner après la complétion
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop1').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                // Réactiver le bouton et retirer le spinner après l'échec
                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MPres').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');


                    var tableRapportPrestationData = [];
                    $('#tableRapportPrestation tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Reference: row.find('td').eq(0).text(),
                            Designation: row.find('td').eq(1).text(),
                            Quantite: row.find('td').eq(2).text(),
                            montantVente: row.find('td').eq(3).text(),
                        };
                        tableRapportPrestationData.push(rowData);
                    });

                    var exportData = {
                        tableRapportPrestationData: tableRapportPrestationData,
                        infoProduit: ajaxResponse.infoProduit,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence
                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_rapport_vente_prestation_pdf',
                        data: JSON.stringify(exportData),
                        contentType: 'application/json',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = window.URL.createObjectURL(blob);

                            // Ouvrir un nouvel onglet avec le PDF
                            newWindow = window.open(url, '_blank');

                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                            $('#staticBackdrop1P').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            // Masquer l'animation
                            $button.removeClass('loading');
                            $button.prop('disabled', false);
                            $button.text('Oui, Exporter en PDF');
                        }
                    });
                });


            });
        </script>
    @endsection
