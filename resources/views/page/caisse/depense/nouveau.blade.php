@extends('layouts.master', ['title' => 'Dépense'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Nouvelle dépense',
        'infos2' => 'Dépense',
        'infos3' => 'nouveau',
    ])
        <style>
            .entete_tableau {
                background-color: #6104ed;
                color: white;
            }
        </style>

        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="dropdown mb-2">
                <a href="{{ route('depense') }}" class="btn text-white" style="{{ background_color_1() }}">
                    <i class="fa fa-reply" aria-hidden="true"></i>
                    Retour
                </a>
            </div>
        </div>

        <div class="card m-b-30">
            <div class="card-header rounded" style="{{ background_color_2() }}">
                <h4 class="mt-2 text-dark">
                 Nouvelle dépense
                </h4>
            </div>
        </div>
        <section>
            <form id="formcreate" action="{{ route('storeDepense') }}" method="post">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Caisse</legend>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="validationTextarea" class="form-label fw-bold position-relative">Caisse
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                        </span>
                                    </label>

                                    <div class="input-group input-group-md mb-3">
                                        <select name="caisse_id" type="text" class="form-select js-single" id="caisse_imput"
                                            required>
                                                <option value="{{ $caisse->id }}">
                                                   Caisse de {{ $caisse->user_name }}
                                                </option>
                                        </select>
                                        <div class="invalid-feedback">La caisse est obligatoire</div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="validationTextarea" class="form-label fw-bold position-relative">Categorie dépense
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                        </span>
                                    </label>

                                    <div class="input-group input-group-md mb-3">
                                        <select name="categorie_depense_id" type="text" class="form-select js-single" id="consignation_imput"
                                            required>
                                            <option value="">Sélectionnez</option>
                                            @foreach ($listeCategorieDepense as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->designation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">La depense est obligatoire</div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="validite" class="form-label fw-bold position-relative">Montant
                                      <span class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*</span>
                                    </label>
                                    <input type="number" id="validite" name="montant" class="form-control" required>
                                    <div id="error-message" class="text-danger mt-2" style="display: none;">Veuillez remplir ce champ.</div>
                                  </div>
                                <div class="form-group col-md-6">
                                    <label for="validationTextarea" class="form-label fw-bold">Description </label>
                                    <textarea name="description" class="form-control" rows="3" id=""></textarea>
                                  </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog g modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Souhaitez-vous vraiment faire cette action ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" id="confirmcreate" class="btn btn-primary">Continuer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Confirmation de la
                                    modification</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous sûr de vouloir modifier le montant ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="button" class="btn btn-primary" id="continueButton">Confirmer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" mt-2">
                    <div class="col-md-12">
                        <div class="d-flex p-2">

                        </div>
                    </div>
                </div>


                <div class="mt-3" style="margin-bottom: 145px;">
                    <div class="card-body">
                        <button type="button" class="btn text-white d-block ms-auto" id="bouton-valider"
                            data-bs-toggle="modal" style="{{ background_color_1() }}" data-bs-target="#staticBackdrop"
                            class="btn btn-sm btn-primary text-end">VALIDER</button>
                    </div>
                </div>
            </form>
        </section>








    @include('layouts.alert')
@endsection
