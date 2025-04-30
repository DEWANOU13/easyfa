@extends('layouts.master', ['title' => 'Statistique Stock'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Statistiques stock',
        'infos2' => 'Statistiques stock',
        'infos3' => 'Liste',
    ])

    <div class="row d-flex text-start p-3">

        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">

                @can('statistique-stock-magasin')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="mag-tab" data-bs-toggle="tab" data-bs-target="#mag" type="button"
                            role="tab" aria-controls="mag" aria-selected="true">Stock du magasin</button>
                    </li>
                @endcan
                @can('statistique-stock-consolide')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stc-tab" data-bs-toggle="tab" data-bs-target="#stc" type="button"
                            role="tab" aria-controls="stc" aria-selected="false">Stock consolide </button>
                    </li>
                @endcan

                @can('statistique-stock-fiche-consolide')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fsc-tab" data-bs-toggle="tab" data-bs-target="#fsc" type="button"
                            role="tab" aria-controls="fsc" aria-selected="false">fiche Stock Consolide</button>
                    </li>
                @endcan

            </ul>

        </div>
        <hr>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active " id="mag" role="tabpanel" aria-labelledby="mag-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>

                            <form id="form1" action="{{ route('statistiqueStockMagasin') }}" method="GET"
                                class="">

                                @csrf
                                <div class="row">
                                    <div class="col-md-3 ">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="fw-bold">Magasin </span>
                                            <select name="magasin" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="magasin1" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un magasin</option>
                                                @if (session()->get('site_id') == 1)
                                                    <option value="Tous">Tous</option>
                                                @endif
                                                @foreach ($magasin as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->NomMagasin }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 ">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="fw-bold">Categorie </span>
                                            <select name="categorie" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="categorie1" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez une catégorie</option>
                                                <option value="Toutes">Toutes</option>
                                                @foreach ($categorie as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="fw-bold">Produit </span>
                                            <select name="produit_" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="produit_1" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
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
                                        <span>&nbsp;</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm1" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="dateError" class="alert alert-danger" style=" display:none;">La date de début
                                    doit
                                    être inférieure à la date de fin.</div>
                            </form>
                        </fieldset>
                    </div>
                </div>

                @can('statistique-stock-magasin-excel')
                    <button id="exportButtonSM" data-bs-toggle="modal" data-bs-target="#staticBackdrop1"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-stock-magasin-pdf')
                    <button id="exportButtonSMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1P"
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
                                <button id="exportExcel_SM" class="btn btn-sm btn-success">Oui Exporter en
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
                                <button id="exportPDF_SM" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table class="table tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="tableStatMagasin">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Code</th>
                                        <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                        <th style="{{ background_color_2() }}" scope="col">Unite</th>
                                        <th style="{{ background_color_2() }}" scope="col">Catégorie</th>
                                        <th style="{{ background_color_2() }}" scope="col">Magasin</th>
                                        <th style="{{ background_color_2() }}" scope="col">Entrée</th>
                                        <th style="{{ background_color_2() }}" scope="col">Sortie</th>
                                        <th style="{{ background_color_2() }}" scope="col">Facture_FV</th>
                                        <th style="{{ background_color_2() }}" scope="col">Facture_FA</th>
                                        <th style="{{ background_color_2() }}" scope="col">Tranfert</th>
                                        <th style="{{ background_color_2() }}" scope="col">Facture_Invalidee</th>
                                        <th style="{{ background_color_2() }}" scope="col">Solde</th>
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
            <div class="tab-pane fade" id="stc" role="tabpanel" aria-labelledby="stc-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form2" action="{{ route('statistiqueStockConsolide') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 ">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="fw-bold">Magasin </span>
                                            <select name="magasin" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="magasin1" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un magasin</option>
                                                @if (session()->get('site_id') == 1)
                                                    <option value="Tous">Tous</option>
                                                @endif
                                                @foreach ($magasin as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->NomMagasin }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Produit</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="produit" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="produit1" aria-label="Sizing example input"
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
                                        <span>&nbsp;</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm2"class="btn text-white"
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
                @can('statistique-stock-consolide-excel')
                    <button id="exportButtonSC" data-bs-toggle="modal" data-bs-target="#staticBackdrop2"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-stock-consolide-pdf')
                    <button id="exportButtonSCP" data-bs-toggle="modal" data-bs-target="#staticBackdrop2P"
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
                                <button id="exportExcel_SC" class="btn btn-sm btn-success">Oui Exporter en
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
                                <button id="exportPDF_SC" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableStatStockConsolide" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Code</th>
                                        <th style="{{ background_color_2() }}" scope="col">Designation</th>
                                        <th style="{{ background_color_2() }}" scope="col">Unite</th>
                                        <th style="{{ background_color_2() }}" scope="col">Magasin</th>
                                        <th style="{{ background_color_2() }}" scope="col">Quantite</th>
                                        <th style="{{ background_color_2() }}" scope="col">Prix</th>
                                        <th style="{{ background_color_2() }}" scope="col">Montant</th>
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
            <div class="tab-pane fade" id="fsc" role="tabpanel" aria-labelledby="fsc-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form3" action="{{ route('ficheStockConsolide') }}" method="GET">
                                @csrf
                                <div class="row">

                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Magasin</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="magasin" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="magasin" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                @if (session()->get('site_id') == 1)
                                                    <option value="Tous">Tous</option>
                                                @endif
                                                @foreach ($magasin as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->NomMagasin }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Produit</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="produit" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="produit2" aria-label="Sizing example input"
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
                                                id="dateDebut3" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateFin" class="form-control"
                                                id="dateFin3" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm3" class="btn text-white"
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
                @can('statistique-stock-fiche-consolide-excel')
                    <button id="exportButtonFSC" data-bs-toggle="modal" data-bs-target="#staticBackdrop3"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-stock-fiche-consolide-pdf')
                    <button id="exportButtonFSCP" data-bs-toggle="modal" data-bs-target="#staticBackdrop3P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan
                <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportExcel_FSC" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="staticBackdrop3P" data-bs-backdrop="static" data-bs-keyboard="false"
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
                                <button id="exportPDF_FSC" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">

                            <table id="tableFicheStockConsolide" class="table tableInfo "
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Date</th>
                                        <th style="{{ background_color_2() }}" scope="col">Désignation </th>
                                        {{-- <th style="{{ background_color_2() }}" scope="col">Réference </th> --}}
                                        <th style="{{ background_color_2() }}" scope="col">Type opération </th>
                                        <th style="{{ background_color_2() }}" scope="col">Magasin</th>
                                        <th style="{{ background_color_2() }}" scope="col">Entrée </th>
                                        <th style="{{ background_color_2() }}" scope="col">Sortie</th>
                                        <th style="{{ background_color_2() }}" scope="col">Stock</th>
                                        <th style="{{ background_color_2() }}" scope="col">Observation</th>
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

            <style>
                .selected {
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
            </style>

            <script>
                const formIds = ['form1', 'form2', 'form3'];

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
            {{-- Statistiques stock Magasin --}}
            <script>
                $(document).ready(function() {
                    $('#formcreate').on('submit', function() {
                        var $button = $('#confirmcreate');

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
                    var exportButtonSM = document.getElementById("exportButtonSM");
                    var exportButtonSMP = document.getElementById("exportButtonSMP");
                    var table = document.getElementById("tableStatMagasin").getElementsByTagName("tbody")[0];

                    // Déclaration d'une variable globale pour stocker la réponse AJAX
                    var ajaxResponse;

                    // Fonction pour vérifier si le tableau est vide
                    function checkTable() {
                        if (table.rows.length === 0) {
                            exportButtonSM.disabled = true;
                            exportButtonSMP.disabled = true;
                            $('#req_message1').show();
                        } else {
                            exportButtonSM.disabled = false;
                            exportButtonSMP.disabled = false;
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

                        // console.log('formData', formData)

                        var dateDebut = new Date($('#dateDebut').val());
                        var dateFin = new Date($('#dateFin').val());

                        // if (dateDebut > dateFin) {
                        //     $('#dateError').show();
                        //     return;
                        // } else {
                        //     $('#dateError').hide();
                        // }

                        var $button = $('#AppliquerForm1');
                        $button.addClass('loading');
                        $button.prop('disabled', true);


                        $.ajax({
                            type: 'GET',
                            url: $(this).attr('action'), // URL définie dans l'attribut action du formulaire
                            data: formData, // Données du formulaire sérialisées
                            success: function(response) {

                                // Stocker la réponse AJAX dans la variable globale
                                ajaxResponse = response;

                                // Efface le contenu précédent du tableau
                                $('#tableStatMagasin tbody').empty();
                                var montantTotal = 0;
                                var _total_entree = 0;
                                var _total_sortie = 0;
                                var _total_facture_fv = 0;
                                var _total_facture_fa = 0;
                                var _total_transfert = 0;
                                var _total_facture_in = 0;

                                // Ajoute les nouvelles lignes au tableau
                                //response.listeStock.forEach(function(item) {
                                Object.keys(response.listeStock).forEach(function(categorie) {
                                    var rows = response.listeStock[categorie].map(function(
                                        item) {
                                        var montant = item.Qte_stockee * item
                                            .Prix_Achat_Net;
                                        montantTotal += montant;

                                        _total_entree += item.total_entree;
                                        _total_sortie += item.total_sortie;
                                        _total_facture_fv += item.total_facture_fv;
                                        _total_facture_fa += item.total_facture_fa;
                                        _total_transfert += item.total_transfert;
                                        _total_facture_in += item.total_facture_in;

                                        return `
                                    <tr>
                                        <td>${item.reference}</td>
                                    <td>${item.designation}</td>
                                    <td>${item.unite}</td>
                                    <td>${item.categorie}</td>
                                    <td>${item.nom_magasin}</td>
                                    <td>${item.total_entree}</td>
                                    <td>${item.total_sortie}</td>
                                    <td>${item.total_facture_fv}</td>
                                    <td>${item.total_facture_fa}</td>
                                    <td>${item.total_transfert}</td>
                                    <td>${item.total_facture_in}</td>
                                    <td>${item.total_entree - item.total_sortie - item.total_transfert - item.total_facture_fv + item.total_facture_fa + item.total_facture_in}</td>

                                    </tr>`;
                                    }).join('');
                                    var categorieRow = `
                                <tr class="table-primary">
                                    <td colspan="12"><strong>${categorie}</strong></td>
                                </tr>`;

                                    var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="5">VALEUR TOTAL DU STOCK</td>
                                        <td>${_total_entree}</td>
                                        <td>${_total_sortie}</td>
                                        <td>${_total_facture_fv}</td>
                                        <td>${_total_facture_fa}</td>
                                        <td>${_total_transfert}</td>
                                        <td>${_total_facture_in}</td>
                                        <td>${_total_entree - _total_sortie - _total_facture_fv + _total_facture_fa - _total_transfert + _total_facture_in}</td>
                                </tr>`;

                                    $('#tableStatMagasin tbody').append(
                                        categorieRow +
                                        rows + totalCategorieRow);

                                });
                                if ($('#tableStatMagasin tbody tr').length === 0) {
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

                    $('#exportExcel_SM').off('click').on('click', function() {
                        var $button = $(this);
                        if (ajaxResponse) {
                            $button.addClass('loading');
                            $button.prop('disabled', true);
                            $button.text('Exportation en cours...');
                            var tableStatMagasinData = [];

                            $('#tableStatMagasin tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    reference: row.find('td').eq(0).text(),
                                    designation: row.find('td').eq(1).text(),
                                    unite: row.find('td').eq(2).text(),
                                    categorie: row.find('td').eq(3).text(),
                                    magasin: row.find('td').eq(4).text(),
                                    entree: row.find('td').eq(5).text(),
                                    sortie: row.find('td').eq(6).text(),
                                    facture_fv: row.find('td').eq(7).text(),
                                    facture_fa: row.find('td').eq(8).text(),
                                    transfert: row.find('td').eq(9).text(),
                                    facture_in: row.find('td').eq(10).text(),
                                    solde: row.find('td').eq(11).text(),
                                };
                                tableStatMagasinData.push(rowData);
                            });

                            var exportData = {
                                tableStatMagasinData: tableStatMagasinData,
                                infoMagasin: ajaxResponse.infoMagasin,
                                dateDebut: ajaxResponse.dateDebut,
                                dateFin: ajaxResponse.dateFin
                            };

                            $.ajax({
                                type: 'POST',
                                url: '/export_excel_stat_stock_magasin',
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
                                    a.download = 'statistique_stock_magasin.xlsx';
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);

                                    $button.removeClass('loading');
                                    $button.prop('disabled', false);
                                    $button.text('Oui, Exporter en Excel');
                                    $('#staticBackdrop1').modal('hide');
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

                    $('#exportPDF_SM').off('click').on('click', function() {
                        var newWindow = null;

                        //chargement
                        var $button = $(this);
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableStatMagasinData = [];
                        $('#tableStatMagasin tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                reference: row.find('td').eq(0).text(),
                                designation: row.find('td').eq(1).text(),
                                unite: row.find('td').eq(2).text(),
                                categorie: row.find('td').eq(3).text(),
                                magasin: row.find('td').eq(4).text(),
                                entree: row.find('td').eq(5).text(),
                                sortie: row.find('td').eq(6).text(),
                                facture_fv: row.find('td').eq(7).text(),
                                facture_fa: row.find('td').eq(8).text(),
                                transfert: row.find('td').eq(9).text(),
                                facture_in: row.find('td').eq(10).text(),

                            };
                            tableStatMagasinData.push(rowData);
                        });

                        var exportData = {
                            tableStatMagasinData: tableStatMagasinData,
                            infoMagasin: ajaxResponse.infoMagasin,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin
                        };


                        $.ajax({
                            type: 'POST',
                            url: '/export_stat_stock_magasin_pdf',
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
                                $('#staticBackdrop1P').modal('hide');
                            },

                            error: function(xhr, status, error) {
                                console.error(error);


                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en PDF');
                            }
                        });

                    });

                });
            </script>
            {{-- Statistiques stock consolide --}}
            <script>
                $(document).ready(function() {
                    var exportButtonSC = document.getElementById("exportButtonSC");
                    var exportButtonSCP = document.getElementById("exportButtonSCP");
                    var table = document.getElementById("tableStatStockConsolide").getElementsByTagName("tbody")[0];

                    // Déclaration d'une variable globale pour stocker la réponse AJAX
                    var ajaxResponse;

                    // Fonction pour vérifier si le tableau est vide
                    function checkTable() {
                        if (table.rows.length === 0) {
                            exportButtonSC.disabled = true;
                            exportButtonSCP.disabled = true;
                            $('#req_message2').show();
                        } else {
                            exportButtonSC.disabled = false;
                            exportButtonSCP.disabled = false;
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

                        var dateDebut = new Date($('#dateDebut').val());
                        var dateFin = new Date($('#dateFin').val());

                        if (dateDebut > dateFin) {
                            $('#dateError2').show();
                            return;
                        } else {
                            $('#dateError2').hide();
                        }
                        var $button = $('#AppliquerForm2');
                        $button.addClass('loading');
                        $button.prop('disabled', true);


                        $.ajax({
                            type: 'GET',
                            url: $(this).attr('action'),
                            data: formData, // Données du formulaire sérialisées
                            success: function(response) {

                                // Stocker la réponse AJAX dans la variable globale
                                ajaxResponse = response;

                                // Efface le contenu précédent du tableau
                                $('#tableStatStockConsolide tbody').empty();

                                Object.keys(response.listeStock).forEach(function(categorie) {
                                    var montantTotalCategorie = 0;
                                    var rows = response.listeStock[categorie].map(function(
                                        item) {
                                        var prixAchatNetArrondi = Math.round(item
                                            .Prix_Achat_Net);
                                        var montant = item.Qte_stockee * item
                                            .Prix_Achat_Net;
                                        var montantArrondi = Math.round(montant);
                                        montantTotalCategorie += montantArrondi;

                                        return `
                                    <tr>
                                        <td>${item.Reference}</td>
                                        <td>${item.Designation}</td>
                                        <td>${item.Unite}</td>
                                        <td>${item.NomMagasin}</td>
                                        <td>${numberFormat(item.Qte_stockee)}</td>
                                        <td>${numberFormat(prixAchatNetArrondi)}</td>
                                        <td>${montantArrondi}</td>
                                    </tr>`;
                                    }).join('');

                                    var categorieRow = `
                                <tr class="table-primary">
                                    <td colspan="7"><strong>${categorie}</strong></td>
                                </tr>`;

                                    var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="6">Total pour la catégorie</td>
                                    <td>${numberFormat(montantTotalCategorie)}</td>
                                </tr>`;

                                    $('#tableStatStockConsolide tbody').append(categorieRow +
                                        rows + totalCategorieRow);
                                });

                                if ($('#tableStatStockConsolide tbody tr').length === 0) {
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

                    $('#exportExcel_SC').off('click').on('click', function() {
                        var $button = $(this);
                        if (ajaxResponse) {

                            console.log(ajaxResponse);
                            $button.addClass('loading');
                            $button.prop('disabled', true);
                            $button.text('Exportation en cours...');

                            var tableStatStockConsolideData = [];
                            $('#tableStatStockConsolide tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    Reference: row.find('td').eq(0).text(),
                                    Designation: row.find('td').eq(1).text(),
                                    Unite: row.find('td').eq(2).text(),
                                    Magasin: row.find('td').eq(3).text(),
                                    Qte_stockee: row.find('td').eq(4).text(),
                                    Prix_Achat_Net: row.find('td').eq(5).text(),
                                    montant: row.find('td').eq(6).text(),
                                };
                                tableStatStockConsolideData.push(rowData);
                            });

                            var exportData = {
                                tableStatStockConsolideData: tableStatStockConsolideData,
                                infoMagasin: ajaxResponse.infoMagasin,
                                infoProduit: ajaxResponse.infoProduit,
                                dateDebut: ajaxResponse.dateDebut,
                                dateFin: ajaxResponse.dateFin
                            };

                            $.ajax({
                                type: 'POST',
                                url: '/export_excel_stat_stock_consolide',
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
                                    $('#staticBackdrop2').modal('hide');
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

                    $('#exportPDF_SC').off('click').on('click', function() {
                        var newWindow = null;

                        //chargement
                        var $button = $(this);
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableStatStockConsolideData = [];
                        $('#tableStatStockConsolide tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Reference: row.find('td').eq(0).text(),
                                Designation: row.find('td').eq(1).text(),
                                Unite: row.find('td').eq(2).text(),
                                Magasin: row.find('td').eq(3).text(),
                                Qte_stockee: row.find('td').eq(4).text(),
                                Prix_Achat_Net: row.find('td').eq(5).text(),
                                montant: row.find('td').eq(6).text(),
                            };
                            tableStatStockConsolideData.push(rowData);
                        });

                        var exportData = {
                            tableStatStockConsolideData: tableStatStockConsolideData,
                            infoMagasin: ajaxResponse.infoMagasin,
                            infoProduit: ajaxResponse.infoProduit,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin
                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_stat_stock_consolide_pdf',
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
                                $('#staticBackdrop2P').modal('hide');
                            },

                            error: function(xhr, status, error) {
                                console.error(error);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en PDF');
                            }
                        });

                    });

                });
            </script>

            {{-- Statistiques fiche stock consolide --}}
            <script>
                $(document).ready(function() {
                    var exportButtonFSC = document.getElementById("exportButtonFSC");
                    var exportButtonFSCP = document.getElementById("exportButtonFSCP");
                    var table = document.getElementById("tableFicheStockConsolide").getElementsByTagName("tbody")[0];

                    // Déclaration d'une variable globale pour stocker la réponse AJAX
                    var ajaxResponse;

                    // Fonction pour vérifier si le tableau est vide
                    function checkTable() {
                        if (table.rows.length === 0) {

                            exportButtonFSC.disabled = true;
                            exportButtonFSCP.disabled = true;
                            $('#req_message3').show();

                        } else {
                            exportButtonFSC.disabled = false;
                            exportButtonFSCP.disabled = false;
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

                    $('#form3').on('submit', function(e) {
                        e
                            .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                        var formData = $(this).serialize(); // Sérialisation des données du formulaire

                        var dateDebut = new Date($('#dateDebut3').val());
                        var dateFin = new Date($('#dateFin3').val());

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
                            data: formData, // Données du formulaire sérialisées
                            success: function(response) {


                                // Stocker la réponse AJAX dans la variable globale
                                ajaxResponse = response;

                                $('#tableFicheStockConsolide tbody').empty();

                                var stock = 0; // Initialisez le stock à 0
                                var totalEntree = 0;
                                var totalSortie = 0;

                                response.mouvementStock.forEach(function(item) {
                                    var entree = item.operation === 'ENTREE' ? parseFloat(item
                                        .Quantite) : 0;
                                    var sortie = item.operation === 'SORTIE' ? parseFloat(item
                                        .Quantite) : 0;

                                    totalEntree += entree;
                                    totalSortie += sortie;
                                    // Mise à jour du stock
                                    stock += entree - sortie;

                                    // <td>${item.Justificatif}</td>
                                    var row = `
                                <tr>
                                    <td>${formatDate(item.Date)}</td>
                                    <td>${item.Designation}</td>
                                    <td>${item.type_operation}</td>
                                    <td>${item.NomMagasin}</td>
                                    <td>${numberFormat(entree)}</td>
                                    <td>${numberFormat(sortie)}</td>
                                    <td>${numberFormat(stock)}</td>
                                    <td>${item.Motif}</td>
                                </tr>`;

                                    $('#tableFicheStockConsolide tbody').append(row);
                                });
                                var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="4">Total</td>
                                    <td>${numberFormat(totalEntree)}</td>
                                    <td>${numberFormat(totalSortie)}</td>
                                    <td>${numberFormat(totalEntree - totalSortie)}</td>
                                    <td></td>
                                </tr>`;
                                $('#tableFicheStockConsolide tbody').append(totalCategorieRow);


                                if (table.rows.length == 0) {
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

                    $('#exportExcel_FSC').off('click').on('click', function() {
                        var $button = $(this);
                        if (ajaxResponse) {

                            $button.addClass('loading');
                            $button.prop('disabled', true);
                            $button.text('Exportation en cours...');

                            var tableFicheStockConsolideData = [];
                            $('#tableFicheStockConsolide tbody tr').each(function() {
                                var row = $(this);
                                var rowData = {
                                    Date: row.find('td').eq(0).text(),
                                    Justificatif: row.find('td').eq(1).text(),
                                    type_operation: row.find('td').eq(2).text(),
                                    NomMagasin: row.find('td').eq(3).text(),
                                    entree: row.find('td').eq(4).text(),
                                    sortie: row.find('td').eq(5).text(),
                                    stock: row.find('td').eq(6).text(),
                                    Motif: row.find('td').eq(7).text(),
                                };
                                tableFicheStockConsolideData.push(rowData);
                            });

                            var exportData = {
                                tableFicheStockConsolideData: tableFicheStockConsolideData,
                                dateDebut: ajaxResponse.dateDebut,
                                dateFin: ajaxResponse.dateFin,
                                infoProduit: ajaxResponse.infoProduit,
                                infoMagasin: ajaxResponse.infoMagasin
                            };

                            $.ajax({
                                type: 'POST',
                                url: '/export_excel_fiche_stock_consolide',
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
                                    a.download = 'fiche_stock_consolide.xlsx';
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);

                                    $button.removeClass('loading');
                                    $button.prop('disabled', false);
                                    $button.text('Oui, Exporter en Excel');
                                    $('#staticBackdrop3').modal('hide');
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

                    $('#exportPDF_FSC').off('click').on('click', function() {
                        var newWindow = null;

                        //chargement
                        var $button = $(this);
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');
                        var tableFicheStockConsolideData = [];
                        $('#tableFicheStockConsolide tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Date: row.find('td').eq(0).text(),
                                Justificatif: row.find('td').eq(1).text(),
                                type_operation: row.find('td').eq(2).text(),
                                NomMagasin: row.find('td').eq(3).text(),
                                entree: row.find('td').eq(4).text(),
                                sortie: row.find('td').eq(5).text(),
                                stock: row.find('td').eq(6).text(),
                                Motif: row.find('td').eq(7).text(),
                            };
                            tableFicheStockConsolideData.push(rowData);
                        });

                        var exportData = {
                            tableFicheStockConsolideData: tableFicheStockConsolideData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoProduit: ajaxResponse.infoProduit,
                            infoMagasin: ajaxResponse.infoMagasin
                        };


                        $.ajax({
                            type: 'POST',
                            url: '/export_fiche_stock_consolide_pdf',
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
                                $('#staticBackdrop3P').modal('hide');
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

                $(document).ready(function() {
                    // Gérer le changement de catégorie
                    $('#categorie1').on('change', function() {
                        var selectedCategoryId = $(this).val();
                        // Vider le menu déroulant des produits avant de l'actualiser
                        $('#produit_1').empty();
                        $('#produit_1').append('<option value="Tous">Tous</option>');

                        if (selectedCategoryId) {
                            $.ajax({
                                url: '/get-products-by-category-stock/' +
                                    selectedCategoryId, // URL à adapter selon tes routes
                                type: 'GET',
                                success: function(response) {
                                    // Ajouter les produits retournés dans le menu déroulant
                                    if (response.length > 0) {
                                        response.forEach(function(product) {
                                            $('#produit_1').append('<option value="' +
                                                product.id + '"' +
                                                '>' + product.Designation +
                                                '</option>'
                                            );
                                        });
                                    } else {
                                        // console.log('fjhdjh')
                                        $('#produit').append('<option value="Tous">Tous</option>')
                                    }
                                }
                            });
                        } else {
                            // $('#produit').append('<option value="Tous">Tous</option>');
                        }
                    });
                });
            </script>
            @include('components.alert')
        @endsection
