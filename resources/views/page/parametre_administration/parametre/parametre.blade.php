@extends('layouts.master', ['title' => 'Parametre'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Paramètres Généraux',
        'infos2' => 'Paramètres',
        'infos3' => 'Paramètre',
    ])

    <section>
        <style>
            .btn-check:checked+.btn,
            .btn.active,
            .btn.show,
            .btn:first-child:active,
            :not(.btn-check)+.btn:active {
                background-color: #FFA500 !important;
                border-color: #FFA500 !important;
                color: #000 !important;
            }

            .btn_aib,
            .btn_aib:hover {
                border-color: #FFA500;
            }
        </style>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="my-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $activeTab = request('active_tab', 'parametre');
        @endphp

        <nav class="mt-4 mb-2">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link {{ $activeTab == 'parametre' ? 'active' : '' }}" id="nav-general"
                    data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="nav-home"
                    aria-selected="true">General</button>

                    @can('voir-entete-pied-excel')
                    <button class="nav-link {{ $activeTab == 'Excel' ? 'active' : '' }}" id="nav-impressionTexte"
                    data-bs-toggle="tab" data-bs-target="#impressionTexte" type="button" role="tab"
                    aria-controls="nav-profile" aria-selected="false">Impression(Texte)</button>
                    @endcan

                    @can('voir-entete-pied-excel')
                    <button class="nav-link {{ in_array($activeTab, ['A4', 'A5', 'A8']) ? 'active' : '' }}"
                    id="nav-impressionImage" data-bs-toggle="tab" data-bs-target="#impressionImage" type="button"
                    role="tab" aria-controls="nav-profile" aria-selected="false">Impression(Image)</button>
                    @endcan

                <button class="nav-link {{ $activeTab == 'seuil_stock' ? 'active' : '' }}" id="nav-seuilStock"
                    data-bs-toggle="tab" data-bs-target="#seuilStock" type="button" role="tab"
                    aria-controls="nav-profile" aria-selected="false">Seuil Stock</button>
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade {{ $activeTab == 'parametre' ? 'show active' : '' }}" id="general" role="tabpanel"
                aria-labelledby="nav-home-tab" tabindex="0">
                <form action="{{ route('parametre.store.prefixeReference') }}" method="POST" class="my-5">
                    @csrf
                    <input type="text" hidden name="parametre">

                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Stock</legend>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Entrée de produits</label>
                                        <input type="text" name="entre_produit"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->entre_produit : null }}"
                                            id="entre_produit" required class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Sortie de produits</label>
                                        <input type="text" name="sortie_produit"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->sortie_produit : null }}"
                                            id="sortie_produit" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Transfert de produits</label>
                                        <input type="text" name="transfert_produit"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->transfert_produit : null }}"
                                            id="transfert_produit" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Inventaire</label>
                                        <input type="text" name="inventaire" id="inventaire"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->inventaire : null }}"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label colspan="">Approvisionnement</label>
                                        <input type="text" name="approvisionnement"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->approvisionnement : null }}"
                                            id="approvisionnement" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Réception Approvisionnement</label>
                                        <input type="text" name="reception_approvisionnement"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->reception_approvisionnement : null }}"
                                            id="reception_approvisionnement" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Importation Stock </label>
                                        <input type="text" name="acheminement"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->acheminement : null }}"
                                            id="acheminement" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Reception Acheminement</label>
                                        <input type="text" name="reception_acheminement"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->reception_acheminement : null }}"
                                            id="reception_acheminement" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <label for="inputPassword6" class="col-form-label">Prise en compte de la gestion
                                            du stock</label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="btn-group" role="group"
                                            aria-label="Basic radio toggle button group">
                                            <input type="radio" class="btn-check" name="prise_en_compte_stocke"
                                                id="stockes0" value="0" autocomplete="off">
                                            <label class="btn btn_aib" for="stockes0">NON</label>

                                            <input type="radio" class="btn-check" name="prise_en_compte_stocke"
                                                id="stockes1" value="1" autocomplete="off" checked>
                                            <label class="btn btn_aib" for="stockes1">OUI</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Facture</legend>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Proforma</label>
                                        <input type="text" name="proforma" id="proforma"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->proforma : null }}"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Facture</label>
                                        <input type="text" name="facture"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->facture : null }}"
                                            id="facture" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Avoir</label>
                                        <input type="text" name="avoir"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->avoir : null }}"
                                            id="avoir" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Règlement</label>
                                        <input type="text" name="reglement"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->reglement : null }}"
                                            id="reglement" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Libellé [F] RESERVES</label>
                                        <input type="text" name="libelle_reserves"
                                            value="{{ $prefixeReferenceExists ? $prefixeReference->libelle_reserves : null }}"
                                            id="libelle_reserves" class="form-control" required>
                                    </div>

                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Prise en compte des
                                                règlements</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="prise_en_compte_reglement"
                                                    id="prise_en_compte0" value="0" autocomplete="off"
                                                    {{ !($prefixeReferenceExists && $prefixeReference->prise_en_compte_reglement == '1') ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="prise_en_compte0">NON</label>

                                                <input type="radio" class="btn-check" name="prise_en_compte_reglement"
                                                    id="prise_en_compte1" value="1" autocomplete="off"
                                                    {{ $prefixeReferenceExists && $prefixeReference->prise_en_compte_reglement == '1' ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="prise_en_compte1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Autoriser le surplus de
                                                règlement</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="surplus_reglement"
                                                    id="surplus0" value="0" autocomplete="off"
                                                    {{ !($prefixeReferenceExists && $prefixeReference->surplus_reglement == '1') ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="surplus0">NON</label>

                                                <input type="radio" class="btn-check" name="surplus_reglement"
                                                    id="surplus1" value="1" autocomplete="off"
                                                    {{ $prefixeReferenceExists && $prefixeReference->surplus_reglement == '1' ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="surplus1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Type de
                                                normalisation</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="type_normalisation"
                                                    id="E-MECEF" value="E-MECEF" autocomplete="off"
                                                    {{ !($prefixeReferenceExists && $prefixeReference->type_normalisation == 'MCF') ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="E-MECEF">E-MECEF</label>

                                                <input type="radio" class="btn-check" name="type_normalisation"
                                                    id="MCF" value="MCF" autocomplete="off"
                                                    {{ $prefixeReferenceExists && $prefixeReference->type_normalisation == 'MCF' ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="MCF">MCF</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card m-b-30 mb-5">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Impression</legend>
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Mode d'impression entête et
                                                pied de page</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="mode_impression"
                                                    value="Texte" id="mode_impression0" autocomplete="off"
                                                    {{ !($prefixeReferenceExists && $prefixeReference->mode_impression == 'Image') ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="mode_impression0">Texte</label>

                                                <input type="radio" class="btn-check" name="mode_impression"
                                                    value="Image" id="mode_impression1" autocomplete="off"
                                                    {{ $prefixeReferenceExists && $prefixeReference->mode_impression == 'Image' ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="mode_impression1">Image</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="card m-b-30 mb-5">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Emballage</legend>
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Prise en compte d'emballage
                                                dans l'application ?</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="emballage" value="0"
                                                    id="emballage0"
                                                    {{ $prefixeReference->emballage == 0 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="emballage0">NON</label>

                                                <input type="radio" class="btn-check" name="emballage" value="1"
                                                    id="emballage1" autocomplete="off"
                                                    {{ $prefixeReference->emballage == 1 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="emballage1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="card m-b-30 mb-5">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Caisse</legend>
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Prise en compte de Caisse
                                                dans l'application ?</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="caisse" value="0"
                                                    id="caisse0"
                                                    {{ $prefixeReference->caisse == 0 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="caisse0">NON</label>

                                                <input type="radio" class="btn-check" name="caisse" value="1"
                                                    id="caisse1" autocomplete="off"
                                                    {{ $prefixeReference->caisse == 1 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="caisse1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="card m-b-30 mb-5">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Précocher l'Aib facturé dans la facture</legend>
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Voulez-vous précocher <b class="fw-bold fs-5">l'Aib facturé</b> dans la facture ?</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="pre_cocher_aib" value="0"
                                                    id="pre_cocher_aib0"
                                                    {{ $prefixeReference->pre_cocher_aib == 0 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="pre_cocher_aib0">NON</label>

                                                <input type="radio" class="btn-check" name="pre_cocher_aib" value="1"
                                                    id="pre_cocher_aib1" autocomplete="off"
                                                    {{ $prefixeReference->pre_cocher_aib == 1 ? 'checked' : '' }}>
                                                <label class="btn btn_aib" for="pre_cocher_aib1">OUI</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>


                    <div class="d-flex justify-content-end ">
                        <a class="btn text-white" style="{{ background_color_1() }}" data-bs-toggle="modal"
                            data-bs-target="#staticParametreGeneral" (click)="openModal()" href="#">Sauvegarder</a>
                    </div>
                    <!-- Modal Sauvegarde parametre general-->
                    <div class="modal fade" id="staticParametreGeneral" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticParametreGeneral"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Sauvegarder</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment sauvegarder ces données ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Annuler</button>
                                    <button class="btn text-white" style="{{ background_color_1() }}"
                                        type="submit">Sauvegarder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form><br>
            </div>

            <div class="tab-pane fade {{ $activeTab == 'Excel' ? 'show active' : '' }}" id="impressionTexte"
                role="tabpanel" aria-labelledby="nav-impressionTexte" tabindex="0">

                <h5 class="mt-4 text-dark">Information entête et pied Excel</h5>
                <form action="{{ route('storeEntetePiedExcel') }}" method="POST" class="mb-5 mt-2">
                    @csrf
                    <input type="text" hidden name="enteteExcel">

                    <div class="card m-b-30">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Formulaire</legend>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Entête</label>
                                        <input type="text" name="entete"
                                            value="{{ $entetePiedExcel ? $entetePiedExcel->entete : null }}"
                                            id="entre_produit" required class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pied</label>
                                        <input type="text" name="pied"
                                            value="{{ $entetePiedExcel ? $entetePiedExcel->pied : null }}"
                                            id="sortie_produit" class="form-control" required>
                                    </div>

                            </fieldset>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end ">
                        <a class="btn text-white" style="{{ background_color_1() }}" data-bs-toggle="modal"
                            data-bs-target="#entpiedExcel" (click)="openModal()" href="#">Sauvegarder</a>
                    </div>
                    <!-- Modal Sauvegarde parametre general-->
                    <div class="modal fade" id="entpiedExcel" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="entpiedExcel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="entpiedExcel">Sauvegarder</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment sauvegarder ces données ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Annuler</button>
                                    <button class="btn text-white" style="{{ background_color_1() }}"
                                        type="submit">Sauvegarder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade {{ in_array($activeTab, ['A4', 'A5', 'A8']) ? 'show active' : '' }}"
                style="margin-bottom: 150px" id="impressionImage" role="tabpanel" aria-labelledby="nav-impressionImage"
                tabindex="0">
                <nav class="mt-2 mb-2">
                    <div class="nav nav-tabs d-inline-block" id="nav-tab1" role="tablist">
                        <button
                            class="nav-link {{ in_array($activeTab, ['A4', 'parametre', 'Excel', 'seuil_stock']) ? 'active' : '' }} d-inline-block"
                            id="nav-A4" data-bs-toggle="tab" data-bs-target="#A4" type="button" role="tab"
                            aria-controls="nav-home" aria-selected="true">A4</button>
                        <button class="nav-link {{ $activeTab == 'A5' ? 'active' : '' }} d-inline-block" id="nav-A5"
                            data-bs-toggle="tab" data-bs-target="#A5" type="button" role="tab"
                            aria-controls="nav-profile" aria-selected="false">A5</button>
                        <button class="nav-link {{ $activeTab == 'A8' ? 'active' : '' }} d-inline-block" id="nav-A8"
                            data-bs-toggle="tab" data-bs-target="#A8" type="button" role="tab"
                            aria-controls="nav-profile" aria-selected="false">A8</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent1">
                    <div class="tab-pane fade {{ in_array($activeTab, ['A4', 'parametre', 'Excel', 'seuil_stock']) ? 'show active' : '' }} mt-4"
                        id="A4" role="tabpanel" aria-labelledby="nav-A4" tabindex="0">
                        <form action="{{ route('upload.entete-pied-a4') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="active_tab" id="active_tab" value="A4">
                            <div class="row">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Entête de page A4</h6>
                                    <button type="button" class="btn text-white ms-auto"
                                        style="{{ background_color_1() }}" data-bs-toggle="modal"
                                        data-bs-target="#modalA4">Sauvegarder</button>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-5">
                                @if (!$imageA4 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA4->entete }}" class="img-fluid img-thumbnail"
                                            alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour l'entête de page A4</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="entete" type="file" id="enteteA4  " required>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Pied de page A4</h6>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @if (!$imageA4 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA4->pied }}" class="img-fluid img-thumbnail" alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour le pied de page A4</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="pied" type="file" id="piedA4">
                                </div>
                            </div>

                            <!-- Modal Sauvegarde A4-->
                            <div class="modal fade" id="modalA4" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Entete et Pied de page A4
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous sauvegarder l'entête et le pied de page format A4
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Annuler</button>
                                            <button class="btn text-white" style="{{ background_color_1() }}"
                                                type="submit">Sauvegarder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade {{ $activeTab == 'A5' ? 'show active' : '' }}" id="A5"
                        role="tabpanel" aria-labelledby="nav-A5" tabindex="0">
                        <form action="{{ route('upload.entete-pied-a5') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="active_tab" id="active_tab" value="A4">
                            <div class="row">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Entête de page A5</h6>
                                    <button type="button" class="btn text-white ms-auto"
                                        style="{{ background_color_1() }}" data-bs-toggle="modal"
                                        data-bs-target="#modalA5">Sauvegarder</button>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-5">
                                @if (!$imageA5 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA5->entete }}" class="img-fluid img-thumbnail"
                                            alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour l'entête de page A5</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="entete" type="file" id="enteteA5" required>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Pied de page A5</h6>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @if (!$imageA5 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA5->pied }}" class="img-fluid img-thumbnail" alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour le pied de page A5</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="pied" type="file" id="piedA5">
                                </div>
                            </div>

                            <!-- Modal Sauvegarde A5-->
                            <div class="modal fade" id="modalA5" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Entete et pied de page A5
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous sauvegarder l'entête et le pied de page format A5
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Annuler</button>
                                            <button class="btn text-white" style="{{ background_color_1() }}"
                                                type="submit">Sauvegarder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade {{ $activeTab == 'A8' ? 'show active' : '' }}" id="A8"
                        role="tabpanel" aria-labelledby="nav-A8" tabindex="0">
                        <form action="{{ route('upload.entete-pied-a8') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="active_tab" id="active_tab" value="A4">
                            <div class="row">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Entête de page A8</h6>
                                    <button type="button" class="btn text-white ms-auto"
                                        style="{{ background_color_1() }}" data-bs-toggle="modal"
                                        data-bs-target="#modalA8">Sauvegarder</button>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-5">
                                @if (!$imageA8 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA8->entete }}" class="img-fluid img-thumbnail"
                                            alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour l'entête de page A8</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="entete" type="file" id="enteteA8" required>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 d-flex">
                                    <h6 class="" style="{{ text_color_1() }}">Pied de page A8</h6>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @if (!$imageA8 == null)
                                    <div class="border rounded p-1">
                                        <img src="{{ $imageA8->pied }}" class="img-fluid img-thumbnail" alt="...">
                                    </div>
                                @else
                                    <h5>Aucune image pour le pied de page A8</h5>
                                @endif
                                <div class="align-self-end">
                                    <input class="form-control" name="pied" type="file" id="piedA8">
                                </div>
                            </div>

                            <!-- Modal Sauvegarde A8-->
                            <div class="modal fade" id="modalA8" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Entete et pied de page A8
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous sauvegarder l'entête et le pied de page format A8
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Annuler</button>
                                            <button class="btn text-white" style="{{ background_color_1() }}"
                                                type="submit">Sauvegarder</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade {{ $activeTab == 'seuil_stock' ? 'show active' : '' }}"
                style="margin-bottom: 150px" id="seuilStock" role="tabpanel" aria-labelledby="nav-seuilStock"
                tabindex="0">
                <form action="{{ route('choix_seuil_parametre') }}" method="POST">
                    @csrf

                    <div class="card m-b-30 mb-5">
                        <div class="card-body">
                            <fieldset class="border p-3 rounded-3">
                                <legend class="float-none w-auto px-1">Seuil Stock</legend>
                                <div class="row">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <label for="inputPassword6" class="col-form-label">Aimeriez-vous gérer le
                                                seuil par catégorie ou par seuil?</label>
                                        </div>
                                        <div class="col-auto">
                                                <div class="btn-group" role="group"
                                                    aria-label="Basic radio toggle button group">
                                                    <input type="radio" class="btn-check" name="reponse"
                                                        value="CATEGORIE" id="cat"
                                                        {{ isset($choix_seuil) &&  $choix_seuil->libelle == 'CATEGORIE' ? 'checked' : '' }}>
                                                    <label class="btn btn_aib" for="cat">CATEGORIE</label>

                                                    <input type="radio" class="btn-check" name="reponse"
                                                        value="PRODUIT" id="prod" autocomplete="off"
                                                        {{isset($choix_seuil) &&  $choix_seuil->libelle == 'PRODUIT' ? 'checked' : '' }}>
                                                    <label class="btn btn_aib" for="prod">PRODUIT</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end ">
                            <a class="btn text-white" style="{{ background_color_1() }}" data-bs-toggle="modal"
                            data-bs-target="#staticParametreGeneralSeuil" (click)="openModal()" href="#">Sauvegarder</a>
                        </div>
                        <!-- Modal Sauvegarde parametre general-->
                        <div class="modal fade" id="staticParametreGeneralSeuil" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticParametreGeneralSeuil"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Sauvegarder</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Voulez-vous vraiment sauvegarder ces données ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Annuler</button>
                                    <button class="btn text-white" style="{{ background_color_1() }}"
                                        type="submit">Sauvegarder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form><br>




            </div>
        </div>
    </section>
    @include('layouts.alert')
@endSection
