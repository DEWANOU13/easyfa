@extends('layouts.master', ['title' => 'Gestion des prix'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Gestion des prix de produits',
        'infos2' => 'Produits',
        'infos3' => 'Gestion des prix',
    ])


    <section>
        <style>
            .progress-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: rgba(255, 255, 255, 0.8);
                /* Optional background overlay */
                z-index: 9999;
                /* Make sure it stays above other content */
            }

            .progress {
                height: 25px;
                /* Height of the progress bar */
                background-color: #e9ecef;
            }

            .progress-bar {
                background-color: #007bff;
            }
        </style>
        <div id="overlay"></div>
        <div class="progress-overlay" id="progress-bar-container" style="display:none;">
            <div class="progress" style="width: 50%;">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>


        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                    type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Prix des
                    produits</button>
            </li>

            @can('creer-prix-vente')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                        type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Fixation de
                        prix produits</button>
                </li>
            @endcan

            @can('historique-fixation-prix')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="historique-prix-tab" data-bs-toggle="tab"
                        data-bs-target="#historique-prix-tab-pane" type="button" role="tab"
                        aria-controls="historique-prix-tab-pane" aria-selected="false">Historique des prix produits</button>
                </li>
            @endcan

            {{-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="prixprix" data-bs-toggle="tab"
                    data-bs-target="#prixprix-tab-pane" type="button" role="tab"
                    aria-controls="prixprix-tab-pane" aria-selected="false">Importation fixation des prix produits</button>
            </li> --}}
        </ul>
        <br>


        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                tabindex="0">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center"
                                        method="post" action="">
                                        @csrf
                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Produit</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="produit" type="text" class="form-select js-single"
                                                    id="produit2">
                                                    <option value="">Sélectionnez un produit</option>
                                                    @foreach ($produits as $key => $value)
                                                        <option value="{{ $value->Reference }}"
                                                            {{ old('produit') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Catégorie client</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="categorie_client" type="text" class="form-select js-single"
                                                    id="categorie_client2">
                                                    <option value="">Sélectionnez une catégorie</option>
                                                    @foreach ($categorie_client as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ old('categorie_client') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Libelle }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Agence</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="agence" type="text" class="form-select js-single"
                                                    id="agence2">
                                                    <option value="">Sélectionnez une agence</option>
                                                    @foreach ($agence as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="btn-group">
                                                    <button type="button" id="filtreprixProd" name="filtreprixProd"
                                                        class="btn text-white w-100 mt-4"
                                                        style="{{ background_color_1() }}">Afficher</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @can('exporter-excel-prix-produit')
                            <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                style="{{ background_color_1() }}" data-bs-toggle="modal"
                                data-bs-target="#exporterExcelModal">Exporter en Excel</button>
                        @endcan

                        @can('exporter-format-importation')
                            <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                style="{{ background_color_1() }}" data-bs-toggle="modal"
                                data-bs-target="#exporterExcelModalImport">Exporter au format d'importation</button>
                        @endcan

                        @can('imprimer-prix-produit')
                            <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                style="{{ background_color_2() }}" data-bs-toggle="modal"
                                data-bs-target="#exporterPDFModal">Imprimer en PDF</button>
                        @endcan

                    </div>

                    @can('importer-prix-produits')
                        <div class="col-md-3">
                            <!-- Import Button -->
                            <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                                style="{{ background_color_1() }}" data-bs-toggle="modal"
                                data-bs-target="#importerStock">Importer Prix
                            </button>
                            <div class="modal fade" id="importerStock" aria-hidden="true"
                                aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalToggleLabel">Importer le prix des
                                                produits</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('importPrixProduitsViaGestion') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="excelFile" class="form-label">Sélectionner le fichier
                                                        Excel</label>
                                                    <input type="file" name="uploadedData" class="form-control"
                                                        id="excelFile" accept=".xlsx, .xls" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Importer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endcan
                </div>

                <div class="modal fade" id="exporterExcelModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="exporterExcelModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exporterExcelModal">Demande de
                                    confirmation </h1>
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button> --}}
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données au format Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel" type="submit" name="reponse" value="exporter"
                                    class="btn btn-sm btn-success">Oui, Continuer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="exporterExcelModalImport" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="exporterExcelModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exporterExcelModal">Demande de
                                    confirmation </h1>
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button> --}}
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données au format d'importation?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="formatImportation" type="submit" name="reponse" value="formatImportation"
                                    class="btn btn-sm btn-success">Oui, Continuer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="exporterPDFModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="exporterExcelModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exporterExcelModal">Demande de
                                    confirmation </h1>
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button> --}}
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données au format PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF" type="submit" name="reponse" value="exporter"
                                    class="btn btn-sm btn-success">Oui, Continuer</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12 mb-5 mt-1">
                    <div class="card m-b-30">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des prix fixés</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="defTable"
                                    class="datatable table table-striped table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Référence</th>
                                            <th scope="col">Désignation</th>
                                            <th scope="col">Catégorie Produit</th>
                                            <th scope="col">Agence</th>
                                            <th scope="col">Catégorie par client</th>
                                            <th scope="col">Prix de vente HT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($prix_ventes as $prix_vente)
                                            <tr style="cursor:pointer" class="clickable-row"
                                                data-url="{{ route('get.entrer_produit_for_entree_produit', ['id' => $prix_vente->id]) }}">
                                                <td class="td-prix">{{ $prix_vente->Reference }}</td>
                                                <td class="td-prix">{{ $prix_vente->Designation }}</td>
                                                <td class="td-prix">{{ $prix_vente->nom_categorie_client }}</td>
                                                <td class="td-prix">{{ $prix_vente->NomAgence }}</td>
                                                <td class="td-prix">{{ $prix_vente->Libelle }}</td>
                                                <td class="td-prix">{{ $prix_vente->prix }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
                                                    <h6 class="mt-3 text-center">Aucune donnée pour l'instant.</h6>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{-- <button id="printButtonExcel" class="btn btn-primary">Imprimer</button> --}}

                            </div>

                            <div class="table-responsive" style="display: none;">
                                <table id="defTableFilter" class="table table-striped table-bordered"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Référence</th>
                                            <th scope="col">Désignation</th>
                                            <th scope="col">Catégorie Produit</th>
                                            <th scope="col">Agence</th>
                                            <th scope="col">Catégorie par client</th>
                                            <th scope="col">Prix de vente HT</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                tabindex="0">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center "
                                        action="" method='post'>
                                        @csrf
                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Produit</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="produit" class="form-select js-single" style="width: 100%"
                                                    id="produit">
                                                    <option value="">Sélectionnez un produit</option>
                                                    @foreach ($produits as $key => $value)
                                                        <option value="{{ $value->Reference }}"
                                                            {{ old('produit') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Catégorie
                                                client</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="categorie_client" class="form-select js-single"
                                                    style="width: 100%" id="categorie_client">
                                                    <option value="">Sélectionnez une catégorie</option>
                                                    @foreach ($categorie_client as $key => $value)
                                                        <option value="{{ $value->id }}-{{ $value->Libelle }}"
                                                            {{ old('categorie_client') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Libelle }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Agence</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="agence" class="form-select js-single" style="width: 100%"
                                                    id="agence">
                                                    <option value="">Sélectionnez une agence </option>
                                                    @foreach ($agence as $key => $value)
                                                        <option value="{{ $value->id }}-{{ $value->NomAgence }}"
                                                            {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-auto">
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="btn-group">
                                                    <button type="button" id="add" name="add"
                                                        class="btn text-white w-100 mt-4"
                                                        style="{{ background_color_1() }}">Afficher</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-5 mt-3">
                        <div class="card m-b-30">
                            <div class="card-header" style="{{ background_color_2() }}">
                                <h3 class="mt-2  d-inline-block text-dark">Fixation de prix</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="table2" class="table table-striped table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Référence</th>
                                                <th scope="col">Désignation</th>
                                                <th scope="col">Agence</th>
                                                <th scope="col">Catégorie client</th>
                                                <th scope="col">Prix de vente actuel</th>
                                                <th scope="col">Nouveau prix</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="btn-group d-flex justify-content-end">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#gestionPrix"
                                                class="btn text-white w-100 mt-4"
                                                style="{{ background_color_1() }}">Appliquer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row -->

                    <!-- Modal de confirmation modification prix -->
                    <div class="modal fade" id="gestionPrix" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment sauvegarder ces nouveaux prix ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button type="submit" id="bouton-valider" class="btn btn-success">Oui
                                        sauvegarder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="historique-prix-tab-pane" role="tabpanel"
                aria-labelledby="historique-prix-tab" tabindex="0">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center "
                                        action="{{ route('store.gestion-prix') }}" method='POST'>
                                        @csrf
                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Produit</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="produit" class="form-select produit-select js-single"
                                                    style="width: 100%" id="produit1">
                                                    <option value="">Sélectionnez un produit</option>
                                                    @foreach ($produits as $key => $value)
                                                        <option value="{{ $value->Reference }}"
                                                            {{ old('produit') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Catégorie
                                                client</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="categorie_client" class="form-select js-single"
                                                    style="width: 100%" id="categorie_client1">
                                                    <option value="">Sélectionnez une catégorie</option>
                                                    @foreach ($categorie_client as $key => $value)
                                                        <option value="{{ $value->id }}-{{ $value->Libelle }}"
                                                            {{ old('categorie_client') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->Libelle }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="form-label fw-bold">Agence</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select name="agence" class="form-select js-single" style="width: 100%"
                                                    id="agence1">
                                                    <option value="">Sélectionnez une agence</option>
                                                    @foreach ($agence as $key => $value)
                                                        <option value="{{ $value->id }}-{{ $value->NomAgence }}"
                                                            {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                            {{ $value->NomAgence }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-auto">
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="btn-group">
                                                    <button type="button" id="filtreHprixProd" name="filtreHprixProd"
                                                        class="btn text-white w-100 mt-4"
                                                        style="{{ background_color_1() }}">Afficher</button>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-auto">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn text-white w-100 mt-4"
                                                            style="{{ background_color_1() }}">Imprimer</button>
                                                    </div>
                                                </div>
                                            </div> --}}
                                    </form>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-md-6">
                        <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                            style="{{ background_color_1() }}" data-bs-target="#exportExcelHistoryModal"
                            data-bs-toggle="modal">Exporter en
                            Excel</button>
                        <button type="button" class="btn btn-sm btn-primary m-2 border-0"
                            style="{{ background_color_2() }}" data-bs-target="#exportPDFHistoryModal"
                            data-bs-toggle="modal">Imprimer en
                            PDF</button>
                    </div> --}}

                    <div class="modal fade" id="exportExcelHistoryModal" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="exportExcelHistoryModal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exportExcelHistoryModal">Demande de
                                        confirmation </h1>
                                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button> --}}
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données au format Excel ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportExcelHistory" type="submit" name="reponse" value="exporter"
                                        class="btn btn-sm btn-success">Oui, Continuer</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="exportPDFHistoryModal" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="exportPDFHistoryModal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exportPDFHistoryModal">Demande de
                                        confirmation </h1>
                                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button> --}}
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment exporter les données au format PDF ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button id="exportPDFHistory" type="submit" name="reponse" value="exporter"
                                        class="btn btn-sm btn-success">Oui, Continuer</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mb-5 mt-1">
                        <div class="card m-b-30">
                            <div class="card-header" style="{{ background_color_2() }}">
                                <h3 class="mt-2  d-inline-block text-dark">Historique des prix de produits </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="table3" class="table table-striped table-bordered "
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Désignation</th>
                                                <th scope="col">Agence</th>
                                                <th scope="col">Catégorie client</th>
                                                <th scope="col">Prix</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>
        </div>
    </section>

    @include('layouts.alert')


    <script>
        // $(document).ready(function() {
        //     $('#filtreprixProd').click(function() {
        //         var produit = $('#produit2').val();
        //         var categorie_client = $('#categorie_client2').val();
        //         var agence = $('#agence2').val();

        //         if (produit === '' && categorie_client === '' && agence === '') {
        //             alert('Veuillez renseigner les données avant de filtrer');
        //             return;
        //         }

        //         // Ajoute ici la logique pour filtrer les données si nécessaire
        //     });
        // });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec l'ID "defTable"
                var $table = $('#defTable');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                console.log('$rows ', $rows);

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-prix');

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Pour déboguer : Afficher les données du tableau dans la console
                // console.log('Table Data:', tableData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'export-prix',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'reponse',
                    value: 'exporter'
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();

            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#exportExcel').click(function() {
                logTableContent();
                $('#exporterExcelModal').modal('hide')
            });

        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec l'ID "defTable"
                var $table = $('#defTable');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                console.log('$rows ', $rows);

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-prix');

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Pour déboguer : Afficher les données du tableau dans la console
                // console.log('Table Data:', tableData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'export-prix',
                    method: 'POST'
                });

                // Ajouter le token CSRF
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'reponse',
                    value: 'formatImportation'
                }));

                // Ajouter les données du tableau au formulaire
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'data',
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();

            }

            // Associer la fonction au clic sur le bouton "Imprimer"

            $('#formatImportation').click(function() {
                logTableContent();
                $('#exporterExcelModalImport').modal('hide')
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec l'ID "defTable"
                var $table = $('#defTable');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                console.log('$rows ', $rows);

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-prix');

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Pour déboguer : Afficher les données du tableau dans la console
                // console.log('Table Data:', tableData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-prix',
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
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#exportPDF').click(function() {
                logTableContent();
                $('#exporterPDFModal').modal('hide')
            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec l'ID "defTable"
                var $table = $('#table3');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                console.log('$rows ', $rows);

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-prix-history');

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Pour déboguer : Afficher les données du tableau dans la console
                // console.log('Table Data:', tableData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'export-prix-history',
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
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#exportExcelHistory').click(function() {
                logTableContent();
                $('#exportExcelHistoryModal').modal('hide')

            });
        });

        $(document).ready(function() {
            function logTableContent() {
                // Sélectionner le tableau avec l'ID "defTable"
                var $table = $('#table3');

                // Récupérer toutes les lignes du corps du tableau (tbody)
                var $rows = $table.find('tbody tr');
                console.log('$rows ', $rows);

                // Créer une liste pour stocker les données du tableau
                var tableData = [];

                // Parcourir chaque ligne et construire l'objet JSON
                $rows.each(function(index, row) {
                    var $cells = $(row).find('.td-prix-history');

                    var rowData = [];

                    $cells.each(function(cellIndex, cell) {
                        // Utiliser innerText pour obtenir le texte de chaque cellule
                        rowData.push(cell.innerText.trim());
                    });

                    // Vérifier si la ligne n'est pas vide
                    if (rowData.some(cellData => cellData.trim() !== "")) {
                        tableData.push(rowData);
                    }
                });

                // Pour déboguer : Afficher les données du tableau dans la console
                // console.log('Table Data:', tableData);

                // Créer un formulaire et ajouter les données à ce formulaire
                var form = $('<form>', {
                    action: 'imprimer-prix-history',
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
                    value: JSON.stringify(tableData)
                }));

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }

            // Associer la fonction au clic sur le bouton "Imprimer"
            $('#exportPDFHistory').click(function() {
                logTableContent();
                $('#exportPDFHistoryModal').modal('hide')
            });
        });

        $(document).ready(function() {
            $('#filtreprixProd').on('click', function() {
                var produit = $('#produit2').val();
                var categorie_client = $('#categorie_client2').val();
                var agence = $('#agence2').val();

                var $button = $('#filtreprixProd');
                var $progressBarContainer = $('#progress-bar-container');
                var $progressBar = $('#progress-bar');
                var progress = 0;

                // Afficher la barre de progression et démarrer à 0%
                $progressBarContainer.show();
                $progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');


                // Simuler la progression (incrémente de 10% toutes les 500ms)
                var interval = setInterval(function() {
                    progress += 10;
                    if (progress >= 90) {
                        clearInterval(interval); // Arrêter avant 100%
                    }
                    $progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(
                        progress + '%');
                }, 100); // 500 ms pour chaque incrémentation de 10%

                $.ajax({
                    url: '/filter-des-listes-prix-produits',
                    method: 'GET',
                    data: {
                        produit: produit,
                        categorie_client: categorie_client,
                        agence: agence
                    },
                    success: function(response) {
                        var tbody = $('#defTableFilter tbody');
                        tbody.empty();

                        $.each(response, function(index, item) {
                            var row = '<tr>' +
                                '<td>' + item.Reference + '</td>' +
                                '<td>' + item.Designation + '</td>' +
                                '<td>' + item.Libelle + '</td>' +
                                '<td>' + item.NomAgence + '</td>' +
                                '<td>' + item.nom_categorie_client + '</td>' +
                                '<td>' + item.prix + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                        $('#defTable').closest('.table-responsive').hide();
                        $('#defTableFilter').closest('.table-responsive').show();
                        $('#defTableFilter').DataTable();

                        // Finaliser la progression à 100% et masquer après 1 seconde
                        clearInterval(interval);
                        $progressBar.css('width', '100%').attr('aria-valuenow', 100).text(
                            '100%');
                        setTimeout(function() {
                            $progressBarContainer.hide();
                        }, 1000);
                    },
                    error: function(xhr) {
                        clearInterval(interval); // Arrêter la progression en cas d'erreur
                        $progressBarContainer.hide();
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#add').on('click', function() {
                var produit = $('#produit').val();
                var categorie_client = $('#categorie_client').val();
                var agence = $('#agence').val();

                var $button = $('#add');
                var $progressBarContainer = $('#progress-bar-container');
                var $progressBar = $('#progress-bar');
                var progress = 0;

                // Afficher la barre de progression et démarrer à 0%
                $progressBarContainer.show();
                $progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');


                // Simuler la progression (incrémente de 10% toutes les 500ms)
                var interval = setInterval(function() {
                    progress += 10;
                    if (progress >= 90) {
                        clearInterval(interval); // Arrêter avant 100%
                    }
                    $progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(
                        progress + '%');
                }, 100); // 500 ms pour chaque incrémentation de 10%

                $.ajax({
                    url: '/filter-des-prix-produits',
                    method: 'GET',
                    data: {
                        produit: produit,
                        categorie_client: categorie_client,
                        agence: agence
                    },
                    success: function(response) {
                        var tbody = $('#table2 tbody');
                        tbody.empty();

                        $.each(response, function(index, item) {
                            var row = '<tr>' +
                                '<td>' + item.Reference + '</td>' +
                                '<td>' + item.Designation + '</td>' +
                                '<td>' + item.NomAgence + '</td>' +
                                '<td>' + item.Libelle + '</td>' +
                                '<td>' + item.prix + '</td>' +
                                '<td><input type="text" class="form-control nouveau-prix" data-reference="' +
                                item.Reference + '" data-categorie-client="' + item
                                .Libelle + '" data-agence="' + item.NomAgence +
                                '" value=""></td>' +
                                '</tr>';
                            tbody.append(row);
                        });

                        $('#table2').DataTable();

                        // Finaliser la progression à 100% et masquer après 1 seconde
                        clearInterval(interval);
                        $progressBar.css('width', '100%').attr('aria-valuenow', 100).text(
                            '100%');
                        setTimeout(function() {
                            $progressBarContainer.hide();
                        }, 1000);


                    },
                    error: function(xhr) {
                        clearInterval(interval); // Arrêter la progression en cas d'erreur
                        $progressBarContainer.hide();
                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        console.error(xhr.responseText);
                    }
                });
            });
            $('#bouton-valider').on('click', function() {
                // Fermer le modal après sauvegarde
                $('#gestionPrix').modal('hide');
                var data = [];
                $('#table2 tbody tr').each(function() {
                    var reference = $(this).find('.nouveau-prix').data('reference');
                    var categorieClient = $(this).find('.nouveau-prix').data('categorie-client');
                    var agence = $(this).find('.nouveau-prix').data('agence');
                    var nouveauPrix = $(this).find('.nouveau-prix').val();
                    data.push({
                        reference: reference,
                        categorie_client: categorieClient,
                        agence: agence,
                        nouveau_prix: nouveauPrix
                    });
                });

                $.ajax({
                    url: '/update-prix-produits',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        produits: data
                    },
                    success: function(response) {
                        // console.log(response.produits);
                        // Traitement après le succès
                        alert('Les prix ont été mis à jour avec succès.');
                        document.getElementById('add').click();
                    },
                    error: function(xhr) {
                        alert('Quelque chose n\'a pas fonctionné. Réessayez!');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#filtreHprixProd').on('click', function() {
                var produit = $('#produit1').val();
                var categorie_client = $('#categorie_client1').val();
                var agence = $('#agence1').val();

                var $button = $('#filtreHprixProd');
                var $progressBarContainer = $('#progress-bar-container');
                var $progressBar = $('#progress-bar');
                var progress = 0;

                // Afficher la barre de progression et démarrer à 0%
                $progressBarContainer.show();
                $progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');


                // Simuler la progression (incrémente de 10% toutes les 500ms)
                var interval = setInterval(function() {
                    progress += 10;
                    if (progress >= 90) {
                        clearInterval(interval); // Arrêter avant 100%
                    }
                    $progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(
                        progress + '%');
                }, 100); // 500 ms pour chaque incrémentation de 10%

                $.ajax({
                    url: '/historique-des-prix-produits',
                    method: 'GET',
                    data: {
                        produit: produit,
                        categorie_client: categorie_client,
                        agence: agence
                    },
                    success: function(response) {
                        var tbody = $('#table3 tbody');
                        tbody.empty();

                        $.each(response, function(index, item) {
                            var row = '<tr>' +
                                '<td class="td-prix-history">' + item
                                .date_changement_prix + '</td>' +
                                '<td class="td-prix-history">' + item.Designation +
                                '</td>' +
                                '<td class="td-prix-history">' + item.NomAgence +
                                '</td>' +
                                '<td class="td-prix-history">' + item.Libelle +
                                '</td>' +
                                '<td class="td-prix-history">' + item.prix + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                        $('#table3').DataTable();

                        // Finaliser la progression à 100% et masquer après 1 seconde
                        clearInterval(interval);
                        $progressBar.css('width', '100%').attr('aria-valuenow', 100).text(
                            '100%');
                        setTimeout(function() {
                            $progressBarContainer.hide();
                        }, 1000);

                    },
                    error: function(xhr) {
                        clearInterval(interval); // Arrêter la progression en cas d'erreur
                        $progressBarContainer.hide();
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        // Fonction pour sauvegarder l'onglet actif dans localStorage
        function saveActiveTab() {
            var activeTab = document.querySelector('.nav-link.active').id;
            localStorage.setItem('activeTab', activeTab);
        }

        // Fonction pour restaurer l'onglet actif à partir de localStorage
        function restoreActiveTab() {
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                var tabTrigger = new bootstrap.Tab(document.getElementById(activeTab));
                tabTrigger.show();

                // Déclencher le clic sur le bouton "Afficher" de l'onglet actif
                clickAfficherButton(activeTab);
            } else {
                clickAfficherButton('home-tab');
            }
        }

        // Fonction pour déclencher le clic sur le bouton "Afficher" de l'onglet actif
        function clickAfficherButton(activeTab) {
            switch (activeTab) {
                case 'home-tab':
                    document.getElementById('filtreprixProd').click();
                    break;
                case 'profile-tab':
                    // document.getElementById('add').click();
                    // break;
                    var tbody = document.querySelector('#table2 tbody');
                    tbody.innerHTML = '';
                    break;
                case 'historique-prix-tab':
                    document.getElementById('filtreHprixProd').click();
                    break;
            }
        }

        // Sauvegarder l'onglet actif lors du changement d'onglet
        document.querySelectorAll('.nav-link').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(event) {
                saveActiveTab();
                clickAfficherButton(event.target.id);
            });
        });
    </script>
    @include('components.alert')
@endsection
