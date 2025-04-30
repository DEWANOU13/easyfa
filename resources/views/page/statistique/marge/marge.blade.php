@extends('layouts.master', ['title' => 'Statistique Marge'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Marge',
        'infos2' => 'Marge',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @can('statistique-marge-cumulee-produit')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cumPro-tab" data-bs-toggle="tab" data-bs-target="#cumPro"
                            type="button" role="tab" aria-controls="cumPro" aria-selected="false">Marges Cumulées par
                            Produit </button>
                    </li>
                @endcan

                @can('statistique-marge-cumulee-jour')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="cumMois-tab" data-bs-toggle="tab" data-bs-target="#cumMois" type="button"
                            role="tab" aria-controls="cumMois" aria-selected="true">Marges Cumulées par Jour
                        </button>
                    </li>
                @endcan

                @can('statistique-marge-cumulee-mois')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="cumj-tab" data-bs-toggle="tab" data-bs-target="#cumj" type="button"
                            role="tab" aria-controls="cumj" aria-selected="true">Marges Cumulées par mois
                        </button>
                    </li>
                @endcan

                @can('statistique-marge-cumulee-client')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cumClient-tab" data-bs-toggle="tab" data-bs-target="#cumClient"
                            type="button" role="tab" aria-controls="cumClient" aria-selected="false">Marges Cumulées par
                            Client </button>
                    </li>
                @endcan

            </ul>
        </div>
        <div class="tab-content" id="myTabContent">
            @can('statistique-marge-cumulee-produit')
                <div class="tab-pane fade show active" id="cumPro" role="tabpanel" aria-labelledby="cumPro-tab">
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Filtre</legend>
                                <form id="form1" action="{{ route('margeParProduit') }}" method="GET">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="fw-bold">Agence</span>

                                            <select name="agence" type="text" class="form-select js-single  w-100"
                                                style="width: 100%;" id="agence1">
                                                <option></option>
                                                @if (userAffectedSiege())
                                                    <option value="Toutes">Toutes</option>
                                                @endif
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
                                                <input type="datetime-local" name="dateFin" class="form-control" id="dateFin"
                                                    max="{{ date('Y-m-d\TH:i') }}" required aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-sm">
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
                    @can('statistique-marge-cumulee-produit-excel')
                        <button id="exportButtonMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1"
                            class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                    @endcan

                    @can('statistique-marge-cumulee-produit-pdf')
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
                                            <th style="{{ background_color_2() }}" scope="col">Quantite</th>
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
            @endcan

            <div class="tab-pane fade  " id="cumMois" role="tabpanel" aria-labelledby="cumMois-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form2" action="{{ route('margeParJour') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence2">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
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
                @can('statistique-marge-cumulee-jour-excel')
                    <button id="exportButtonMJ" data-bs-toggle="modal" data-bs-target="#staticBackdrop2"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-marge-cumulee-jour-pdf')
                    <button id="exportButtonMJP" data-bs-toggle="modal" data-bs-target="#staticBackdrop2P"
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
                                <button id="exportExcel_MJ" class="btn btn-sm btn-success">Oui Exporter en
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
                                <button id="exportPDF_MJ" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableMargeParJour" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Date</th>
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
                        <div id="req_message2" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="cumj" role="tabpanel" aria-labelledby="cumj-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form3" action="{{ route('margeParMois') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence3">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Du</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="month" name="dateDebut" class="form-control" id="dateDebut"
                                                max="{{ date('Y-m') }}" required aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="month" name="dateFin" class="form-control" id="dateFin"
                                                max="{{ date('Y-m') }}" required aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span>&nbsp;</span>
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
                @can('statistique-marge-cumulee-mois-excel')
                    <button id="exportButtonMM" data-bs-toggle="modal" data-bs-target="#staticBackdrop3"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-marge-cumulee-mois-pdf')
                    <button id="exportButtonMMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop3P"
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
                                <button id="exportExcel_MM" class="btn btn-sm btn-success">Oui Exporter en
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
                                <button id="exportPDF_MM" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableMargeParMois" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Mois</th>
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
                        <div id="req_message3" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="cumClient" role="tabpanel" aria-labelledby="cumClient-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>

                            <form id="form4" action="{{ route('margeParClient') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100"
                                            style="width: 100%;" id="agence4">
                                            <option></option>
                                            @if (userAffectedSiege())
                                                <option value="Toutes">Toutes</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Client</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="client" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="client" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un client</option>
                                                <option value="Tous">Tous</option>
                                                @foreach ($client as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->Denomination_sociale }}
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
                                                <button type="submit" id="AppliquerForm4" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div id="dateError4" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                @can('statistique-marge-cumulee-client-excel')
                    <button id="exportButtonMC" data-bs-toggle="modal" data-bs-target="#staticBackdrop4"
                        class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                @endcan

                @can('statistique-marge-cumulee-client-pdf')
                    <button id="exportButtonMCP" data-bs-toggle="modal" data-bs-target="#staticBackdrop4P"
                        class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button>
                @endcan

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
                                Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MC" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
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
                                Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF_MC" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table id="tableMargeParClient" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="{{ background_color_2() }}" scope="col">Dénomination sociale</th>
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
                        <div id="req_message4" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script>
            const formIds = ['form1', 'form2', 'form3', 'form4'];

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
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError1').show();
                        return;
                    } else {
                        $('#dateError1').hide();
                    }

                    var $button = $('#AppliquerForm1');
                    $button.addClass('loading');
                    $button.prop('disabled', true);


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
                                        .Prix_vente);
                                    montantVenteTotalCategorie +=
                                        montantVenteArrondi;

                                    var montantAchatArrondi = Math.round(item
                                        .Prix_achat);
                                    montantAchatTotalCategorie +=
                                        montantAchatArrondi;

                                    var marge = montantVenteArrondi -
                                        montantAchatArrondi;
                                    totalMargeCategorie += marge;

                                    var taux = montantAchatArrondi === 0 ? 0 : (
                                        marge / montantAchatArrondi * 100);

                                    //totalTauxCategorie += taux;

                                    return `
                                    <tr>
                                        <td>${item.Reference}</td>
                                        <td>${item.Designation}</td>
                                        <td>${numberFormat(item.Quantite)}</td>
                                        <td>${numberFormat(montantVenteArrondi)}</td>
                                        <td>${numberFormat(montantAchatArrondi)}</td>
                                        <td>${numberFormat(marge)}</td>
                                        <td>${numberFormat(taux)}%</td>
                                    </tr>`;
                                }).join('');

                                var categorieRow = `
                                <tr class="table-primary">
                                    <td colspan="7"><strong>${categorie}</strong></td>
                                </tr>`;

                                var totalCategorieRow = `
                                <tr class="table-secondary">
                                    <td colspan="3">Total pour la catégorie</td>
                                    <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                    <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                    <td>${numberFormat(totalMargeCategorie)}</td>
                                    <td> ${montantAchatTotalCategorie === 0 ? '0' : numberFormat(totalMargeCategorie / montantAchatTotalCategorie * 100)}%</td>
                                </tr>`;

                                $('#tableMargeParProduit tbody').append(categorieRow +
                                    rows + totalCategorieRow);
                            });

                            if ($('#tableMargeParProduit tbody tr').length === 0) {
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
                                Quantite: row.find('td').eq(2).text(),
                                montantVente: row.find('td').eq(3).text(),
                                montantAchat: row.find('td').eq(4).text(),
                                marge: row.find('td').eq(5).text(),
                                taux: row.find('td').eq(6).text(),
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
                            url: '/export_excel_marge_par_produit',
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
                                a.download = 'marge_par_produit.xlsx';
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
                            Quantite: row.find('td').eq(2).text(),
                            montantVente: row.find('td').eq(3).text(),
                            montantAchat: row.find('td').eq(4).text(),
                            marge: row.find('td').eq(5).text(),
                            taux: row.find('td').eq(6).text(),
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
                        url: '/export_marge_par_produit_pdf',
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
        {{-- marge par jour --}}
        <script>
            $(document).ready(function() {
                var exportButtonMJ = document.getElementById("exportButtonMJ");
                var exportButtonMJP = document.getElementById("exportButtonMJP");
                var table = document.getElementById("tableMargeParJour").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMJ.disabled = true;
                        exportButtonMJP.disabled = true;
                        $('#req_message2').show();
                    } else {
                        exportButtonMJ.disabled = false;
                        exportButtonMJP.disabled = false;
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
                    var $button = $('#AppliquerForm2');
                    $button.addClass('loading');
                    $button.prop('disabled', true);

                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParJour tbody').empty();

                            var montantAchatTotalCategorie = 0;
                            var montantVenteTotalCategorie = 0;
                            var totalMargeCategorie = 0;
                            //var totalTauxCategorie = 0;
                            response.listeVente.forEach(function(item) {
                                var montantVenteArrondi = Math.round(item.Prix_vente);
                                montantVenteTotalCategorie += montantVenteArrondi;

                                var montantAchatArrondi = Math.round(item.Prix_achat);
                                montantAchatTotalCategorie += montantAchatArrondi;

                                var marge = montantVenteArrondi - montantAchatArrondi;
                                totalMargeCategorie += marge;

                                var taux = montantAchatArrondi === 0 ? 0 : (marge /
                                    montantAchatArrondi * 100);

                                // totalTauxCategorie += taux;

                                var row = `
                                <tr>
                                <td>${formatDate(item.Date)}</td>
                                <td>${numberFormat(montantVenteArrondi)}</td>
                                <td>${numberFormat(montantAchatArrondi)}</td>
                                <td>${numberFormat(marge)}</td>
                                <td>${numberFormat(taux)}%</td>
                                </tr>`;
                                $('#tableMargeParJour tbody').append(row);
                            });
                            $('#tableMargeParJour tbody').append(
                                `<tr>
                                <td>TOTAL POUR CETTE PERIODE</td>
                                <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                <td>${numberFormat(totalMargeCategorie)}</td>
                                <td> ${montantAchatTotalCategorie === 0 ? '0' : numberFormat(totalMargeCategorie / montantAchatTotalCategorie * 100)}%</td>
                            </tr>`
                            );

                            if ($('#tableMargeParJour tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();

                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MJ').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParJourData = [];
                        $('#tableMargeParJour tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Date: row.find('td').eq(0).text(),
                                montantVente: row.find('td').eq(1).text(),
                                montantAchat: row.find('td').eq(2).text(),
                                marge: row.find('td').eq(3).text(),
                                taux: row.find('td').eq(4).text(),
                            };
                            tableMargeParJourData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParJourData: tableMargeParJourData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_marge_par_jour',
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
                                a.download = 'marge_par_jour.xlsx';
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

                $('#exportPDF_MJ').off('click').on('click', function() {
                    /* var newWindow = window.open('',
                    '_blank'); // */
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableMargeParJourData = [];
                    $('#tableMargeParJour tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Date: row.find('td').eq(0).text(),
                            montantVente: row.find('td').eq(1).text(),
                            montantAchat: row.find('td').eq(2).text(),
                            marge: row.find('td').eq(3).text(),
                            taux: row.find('td').eq(4).text(),
                        };
                        tableMargeParJourData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParJourData: tableMargeParJourData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence

                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_marge_par_jour_pdf',
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



                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                }
            });
        </script>

        {{-- marge par mois --}}
        <script>
            $(document).ready(function() {
                var exportButtonMM = document.getElementById("exportButtonMM");
                var exportButtonMMP = document.getElementById("exportButtonMMP");
                var table = document.getElementById("tableMargeParMois").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMM.disabled = true;
                        exportButtonMMP.disabled = true;
                        $('#req_message3').show();
                    } else {
                        exportButtonMM.disabled = false;
                        exportButtonMMP.disabled = false;
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
                    // console.log(formData);
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
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParMois tbody').empty();

                            var montantAchatTotalCategorie = 0;
                            var montantVenteTotalCategorie = 0;
                            var totalMargeCategorie = 0;
                            //var totalTauxCategorie = 0;
                            response.listeVente.forEach(function(item) {
                                var montantVenteArrondi = Math.round(item.Prix_vente);
                                montantVenteTotalCategorie += montantVenteArrondi;

                                var montantAchatArrondi = Math.round(item.Prix_achat);
                                montantAchatTotalCategorie += montantAchatArrondi;

                                var marge = montantVenteArrondi - montantAchatArrondi;
                                totalMargeCategorie += marge;


                                var taux = montantAchatArrondi === 0 ? 0 : (marge /
                                    montantAchatArrondi * 100);

                                // totalTauxCategorie += taux;

                                var row = `
                                <tr>
                                <td>${formatDate(item.Date)}</td>
                                <td>${numberFormat(montantVenteArrondi)}</td>
                                <td>${numberFormat(montantAchatArrondi)}</td>
                                <td>${numberFormat(marge)}</td>
                                <td>${numberFormat(taux)}%</td>
                                </tr>`;
                                $('#tableMargeParMois tbody').append(row);
                            });
                            $('#tableMargeParMois tbody').append(
                                `<tr>
                                <td>TOTAL</td>
                                <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                <td>${numberFormat(totalMargeCategorie)}</td>
                                <td> ${montantAchatTotalCategorie === 0 ? '0' : numberFormat(totalMargeCategorie / montantAchatTotalCategorie * 100)}%</td>
                            </tr>`
                            );

                            if ($('#tableMargeParMois tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MM').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParMoisData = [];
                        $('#tableMargeParMois tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Date: row.find('td').eq(0).text(),
                                montantVente: row.find('td').eq(1).text(),
                                montantAchat: row.find('td').eq(2).text(),
                                marge: row.find('td').eq(3).text(),
                                taux: row.find('td').eq(4).text(),
                            };
                            tableMargeParMoisData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParMoisData: tableMargeParMoisData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_marge_par_mois',
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
                                a.download = 'marge_par_mois.xlsx';
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

                $('#exportPDF_MM').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');
                    var tableMargeParMoisData = [];
                    $('#tableMargeParMois tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Date: row.find('td').eq(0).text(),
                            montantVente: row.find('td').eq(1).text(),
                            montantAchat: row.find('td').eq(2).text(),
                            marge: row.find('td').eq(3).text(),
                            taux: row.find('td').eq(4).text(),
                        };
                        tableMargeParMoisData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParMoisData: tableMargeParMoisData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoAgence: ajaxResponse.infoAgence

                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_marge_par_mois_pdf',
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
                    const options = {
                        month: 'long',
                        year: 'numeric'
                    };
                    const formattedDate = date.toLocaleDateString('fr-FR', options);

                    // Mettre la première lettre en majuscule
                    return formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
                }
            });
        </script>

        {{-- marge par client --}}
        <script>
            $(document).ready(function() {
                var exportButtonMC = document.getElementById("exportButtonMC");
                var exportButtonMCP = document.getElementById("exportButtonMCP");
                var table = document.getElementById("tableMargeParClient").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMC.disabled = true;
                        exportButtonMCP.disabled = true;
                        $('#req_message4').show();
                    } else {
                        exportButtonMC.disabled = false;
                        exportButtonMCP.disabled = false;
                        $('#req_message4').hide();
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



                $('#form4').on('submit', function(e) {
                    e
                        .preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
                    var formData = $(this).serialize(); // Sérialisation des données du formulaire
                    // console.log(formData);
                    var dateDebut = new Date($('#dateDebut').val());
                    var dateFin = new Date($('#dateFin').val());

                    if (dateDebut > dateFin) {
                        $('#dateError4').show();
                        return;
                    } else {
                        $('#dateError4').hide();
                    }
                    var $button = $('#AppliquerForm4');
                    $button.addClass('loading');
                    $button.prop('disabled', true);

                    $.ajax({
                        type: 'GET',
                        url: $(this).attr('action'),
                        data: formData, // Données du formulaire sérialisées
                        success: function(response) {
                            // console.log(response.listeVente);

                            ajaxResponse = response;

                            $('#tableMargeParClient tbody').empty();

                            var montantAchatTotalCategorie = 0;
                            var montantVenteTotalCategorie = 0;
                            var totalMargeCategorie = 0;
                            //var totalTauxCategorie = 0;
                            response.listeVente.forEach(function(item) {
                                var montantVenteArrondi = Math.round(item.Prix_vente);
                                montantVenteTotalCategorie += montantVenteArrondi;

                                var montantAchatArrondi = Math.round(item.Prix_achat);
                                montantAchatTotalCategorie += montantAchatArrondi;

                                var marge = montantVenteArrondi - montantAchatArrondi;
                                totalMargeCategorie += marge;

                                var taux = montantAchatArrondi === 0 ? 0 : (marge /
                                    montantAchatArrondi * 100);

                                // totalTauxCategorie += taux;

                                var row = `
                                <tr>
                                <td>${item.Denomination_sociale}</td>
                                <td>${numberFormat(montantVenteArrondi)}</td>
                                <td>${numberFormat(montantAchatArrondi)}</td>
                                <td>${numberFormat(marge)}</td>
                                <td>${numberFormat(taux)}%</td>
                                </tr>`;
                                $('#tableMargeParClient tbody').append(row);
                            });
                            $('#tableMargeParClient tbody').append(
                                `<tr>
                                <td>TOTAL</td>
                                <td>${numberFormat(montantVenteTotalCategorie)}</td>
                                <td>${numberFormat(montantAchatTotalCategorie)}</td>
                                <td>${numberFormat(totalMargeCategorie)}</td>
                                <td> ${montantAchatTotalCategorie === 0 ? '0' : numberFormat(totalMargeCategorie / montantAchatTotalCategorie * 100)}%</td>
                            </tr>`
                            );

                            if ($('#tableMargeParClient tbody tr').length === 0) {
                                alert("Aucune donnée disponible pour l'exportation.");
                            }

                            checkTable();
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            $button.removeClass('loading'); // Retire la classe .loading du bouton
                            $button.prop('disabled', false);
                        }
                    });
                });
                $('#exportExcel_MC').off('click').on('click', function() {
                    var $button = $(this);
                    if (ajaxResponse) {
                        $button.addClass('loading');
                        $button.prop('disabled', true);
                        $button.text('Exportation en cours...');

                        var tableMargeParClientData = [];
                        $('#tableMargeParClient tbody tr').each(function() {
                            var row = $(this);
                            var rowData = {
                                Denomination_sociale: row.find('td').eq(0).text(),
                                montantVente: row.find('td').eq(1).text(),
                                montantAchat: row.find('td').eq(2).text(),
                                marge: row.find('td').eq(3).text(),
                                taux: row.find('td').eq(4).text(),
                            };
                            tableMargeParClientData.push(rowData);
                        });

                        var exportData = {
                            tableMargeParClientData: tableMargeParClientData,
                            dateDebut: ajaxResponse.dateDebut,
                            dateFin: ajaxResponse.dateFin,
                            infoClient: ajaxResponse.infoClient,
                            infoAgence: ajaxResponse.infoAgence

                        };

                        $.ajax({
                            type: 'POST',
                            url: '/export_excel_marge_par_client',
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
                                a.download = 'marge_par_client.xlsx';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                            },
                            error: function(xhr, status, error) {
                                console.error(error);

                                $button.removeClass('loading');
                                $button.prop('disabled', false);
                                $button.text('Oui, Exporter en Excel');
                                $('#staticBackdrop4').modal('hide');
                            }
                        });
                    } else {
                        console.error("Aucune donnée disponible pour l'exportation.");
                    }
                });

                $('#exportPDF_MC').off('click').on('click', function() {
                    var newWindow = null;

                    //chargement
                    var $button = $(this);
                    $button.addClass('loading');
                    $button.prop('disabled', true);
                    $button.text('Exportation en cours...');

                    var tableMargeParClientData = [];
                    $('#tableMargeParClient tbody tr').each(function() {
                        var row = $(this);
                        var rowData = {
                            Denomination_sociale: row.find('td').eq(0).text(),
                            montantVente: row.find('td').eq(1).text(),
                            montantAchat: row.find('td').eq(2).text(),
                            marge: row.find('td').eq(3).text(),
                            taux: row.find('td').eq(4).text(),
                        };
                        tableMargeParClientData.push(rowData);
                    });

                    var exportData = {
                        tableMargeParClientData: tableMargeParClientData,
                        dateDebut: ajaxResponse.dateDebut,
                        dateFin: ajaxResponse.dateFin,
                        infoClient: ajaxResponse.infoClient,
                        infoAgence: ajaxResponse.infoAgence

                    };

                    $.ajax({
                        type: 'POST',
                        url: '/export_marge_par_client_pdf',
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



                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const options = {
                        month: 'long',
                        year: 'numeric'
                    };
                    const formattedDate = date.toLocaleDateString('fr-FR', options);

                    // Mettre la première lettre en majuscule
                    return formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
                }
            });
        </script>
    @endsection
