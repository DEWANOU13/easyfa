@extends('layouts.master', ['title' => 'Modifier Produit'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Produit',
        'infos2' => 'Produit',
        'infos3' => isset($produit) ? 'Modification' : 'Nouveau',
    ])
    <section>
        <div class="row">
            <div class="col-md-8 offset-md-2 mb-5">
                <div class="d-flex flex-row-reverse bd-highlight">
                    <div class="dropdown mb-2">
                        <a href="{{ route('page.produit.produit') }}" class="btn text-white"
                            style="{{ background_color_1() }}">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="container d-flex justify-content-center">
                    <div class="card  w-100 ">
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h4 class="mt-2 text-dark">
                                Modification de {{ $produit[0]->Designation }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formupdate" class="was-validated" method="get"
                                action="{{ route('update.produit', $produit[0]->id) }}">
                                @csrf
                                @method('get')
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="type">Type</label><br>
                                    @if ($produit[0]->Type === 'AUCUN')
                                        <div class="form-check form-check-inline">
                                            <input name="type" type="radio" id="aucun" value="AUCUN" checked>
                                            <label class="form-check-label" for="aucun">AUCUN</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="prestation"
                                                value="PRESTATION">
                                            <label class="form-check-label" for="prestation">PRESTATION</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="produit"
                                                value="PRODUIT">
                                            <label class="form-check-label" for="produit">PRODUIT</label>
                                        </div>
                                    @elseif ($produit[0]->Type === 'PRESTATION')
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="prestation"
                                                value="PRESTATION" checked>
                                            <label class="form-check-label" for="prestation">PRESTATION</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="aucun"
                                                value="AUCUN">
                                            <label class="form-check-label" for="aucun">AUCUN</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="produit"
                                                value="PRODUIT">
                                            <label class="form-check-label" for="produit">PRODUIT</label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="produit"
                                                value="PRODUIT" checked>
                                            <label class="form-check-label" for="produit">PRODUIT</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="prestation"
                                                value="PRESTATION">
                                            <label class="form-check-label" for="prestation">PRESTATION</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="aucun"
                                                value="AUCUN">
                                            <label class="form-check-label" for="aucun">AUCUN</label>
                                        </div>
                                    @endif
                                </div>
                                {{-- <div class="form-group">
                                    <label class="form-label fw-bold" for="reference">Référence</label>
                                    <input name="reference" class="form-control" id="reference" type="text"
                                        value="{{ $produit[0]->Reference }}" required>
                                    <div class="invalid-feedback">La référence est obligatoire</div>
                                </div> --}}
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="designation">Désignation</label>
                                    <input name="designation" class="form-control" id="designation" type="text"
                                        value="{{ $produit[0]->Designation }}" required>
                                    <div class="invalid-feedback">La désignation est obligatoire</div>
                                </div>
                                <div class="form-group">
                                    <label for="categorie" class="form-label fw-bold">Catégorie</label>
                                    <select name="categorie" class="form-select" id="categorie" required>
                                        <option value="{{ $produit[0]->Id_Categorie }}">
                                            {{ $produit[0]->Libelle_categorie }}</option>
                                        @foreach ($categorie_produits as $categorie)
                                            <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">La catégorie est obligatoire</div>
                                </div>
                                <div class="form-group">
                                    <label for="unite_comptage" class="form-label fw-bold">Unité de comptage</label>
                                    <select name="unite_comptage" class="form-select" id="unite_comptage" required>
                                        <option value="{{ $produit[0]->Id_Unite_Comptage }}">{{ $produit[0]->Libelle }}
                                        </option>
                                        @foreach ($unite_comptages as $unite_comptage)
                                            <option value="{{ $unite_comptage->id }}">{{ $unite_comptage->Libelle }}
                                                ({{ $unite_comptage->Code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">L'unité de comptage est obligatoire</div>
                                </div>


                                <div class="form-group" {{ emballageActiver() ? '' : 'hidden' }}>
                                    <label for="embalage" class="form-label fw-bold">Emballage </label>

                                    <div class="input-group">
                                        <select name="Emballage_id" class="form-select" id="Emballage_id">
                                            <option value="{{ $produit[0]->Emballage_id }}">
                                                {{ $produit[0]->Nom_emballage }}</option>
                                            <option value="">Sélectionnez un emballage</option>

                                            @foreach ($emballages as $emballage)
                                                <option value="{{ $emballage->id }}"
                                                    {{ old('Emballage_id') == $emballage->id ? 'selected' : '' }}>
                                                    {{ $emballage->Nom_emballage }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button data-bs-toggle="modal" data-bs-target="#creeEmballage"
                                            class="btn hover-pers" title="Créer une nouvelle unité de comptage"
                                            style="height: 28px; color: #0d6efd; border: 2px solid #0d6efd;"
                                            type="button">
                                            <span style="position: relative; top: -3px">Créer</span>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-circle-plus" style="margin-top: -10px"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                <path d="M9 12h6" />
                                                <path d="M12 9v6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group" id="typeEmballageGroup" style="display: none; width: 100%;">
                                    <label for="type_emballage" class="form-label fw-bold">Type emballage <span
                                            class="text-danger">*</span></label>

                                    <div class="input-group">
                                        <select name="type_emballage" class="form-select" style="width: 100%;"
                                            id="type_emballage">
                                            <option value="{{ $produit[0]->type_emballage }}">
                                                {{ $produit[0]->type_emballage }}</option>
                                            <option value="EMBALLAGE_RECUPERABLE">EMBALLAGE_RECUPERABLE</option>
                                            <option value="EMBALLAGE_NON_RECUPERABLE">EMBALLAGE_NON_RECUPERABLE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="statut">Statut</label><br>
                                    @if ($produit[0]->Statut === 'INACTIF')
                                        <div class="form-check form-check-inline">
                                            <input name="statut" class="form-check-input" type="radio" id="inactif"
                                                value="INACTIF" checked>
                                            <label class="form-check-label" for="inactif">INACTIF</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="statut" class="form-check-input" type="radio" id="actif"
                                                value="ACTIF">
                                            <label class="form-check-label" for="actif">ACTIF</label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input name="statut" class="form-check-input" type="radio" id="actif"
                                                value="ACTIF" checked>
                                            <label class="form-check-label" for="actif">ACTIF</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="statut" class="form-check-input" type="radio" id="inactif"
                                                value="INACTIF">
                                            <label class="form-check-label" for="inactif">INACTIF</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <a type="reset" href="{{ route('page.produit.produit') }}"
                                        class="btn btn-secondary waves-effect waves-light">Annuler</a>
                                    <button class="btn btn-primary" id="confirmupdate" type="submit">Valider</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <script>
                $(document).ready(function() {
                    $('#formupdate').on('submit', function() {
                        var $button = $('#confirmupdate');

                        // Désactiver le bouton et ajouter la classe loading
                        $button.prop('disabled', true);
                        $button.addClass('loading');

                        // Afficher le texte ou l'indicateur de chargement
                        $('#loadingSpinner').show();

                        // Laisser le formulaire continuer à être soumis normalement
                        return true;
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('#Emballage_id').on('change', function() {
                        var emballageSelected = $(this).val();

                        if (emballageSelected) {
                            // Afficher le select "Type emballage" et le rendre requis
                            $('#typeEmballageGroup').show();
                            $('#type_emballage').prop('required', true);
                        } else {
                            // Cacher le select "Type emballage" et le rendre non requis
                            $('#typeEmballageGroup').hide();
                            $('#type_emballage').prop('required', false);
                            $('#type_emballage').val(''); // Réinitialiser la sélection
                        }
                    });
                });
            </script>

    </section>
    @include('layouts.alert')
@endsection





{{-- @extends('layouts.master', ['title' => 'Produits'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Produit',
        'infos2' => 'Produit',
        'infos3' => isset($produit) ? 'Modification' : 'Nouveau',
    ])
    <div class="row">
        <div class="col-md-8 offset-md-2 mb-5">
            <div class="d-flex flex-row-reverse bd-highlight">
                <div class="dropdown mb-2">
                    <a href="{{ route('page.produit.produit') }}" class="btn text-white"
                        style="{{ background_color_1() }}">
                        <i class="fa fa-reply" aria-hidden="true"></i>
                        Retour
                    </a>
                </div>
            </div>
            <div class="container d-flex justify-content-center  ">
                <div class=" card">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h4 class="mt-2 text-dark">
                            @if (isset($produit))
                                Modification de {{ $produit[0]->Designation }}
                            @else
                                Enregistrement d'un produit
                            @endif
                        </h4>
                    </div>
        <div class="col-md-12">
            <form class="was-validated" methode="post" action="{{ route('update.produit', $produit[0]->id) }}">
                @csrf
                @method('PUT')
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="type">Type</label><br>
                        @if ($produit[0]->Type === 'AUCUN')
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="aucun" value="AUCUN"
                                    checked>
                                <label class="form-check-label" for="aucun">AUCUN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="prestation"
                                    value="PRESTATION">
                                <label class="form-check-label" for="prestation">PRESTATION</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="produit"
                                    value="PRODUIT">
                                <label class="form-check-label" for="produit" >PRODUIT</label>
                            </div>
                        @endif
                        @if ($produit[0]->Type === 'PRESTATION')
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="prestation"
                                    value="PRESTATION" checked>
                                <label class="form-check-label" for="prestation">PRESTATION</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="aucun" value="AUCUN"
                                    >
                                <label class="form-check-label" for="aucun">AUCUN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="produit"
                                    value="PRODUIT">
                                <label class="form-check-label" for="produit" >PRODUIT</label>
                            </div>
                        @endif
                        @if ($produit[0]->Type === 'PRODUIT')
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="produit"
                                    value="PRODUIT" checked>
                                <label class="form-check-label" for="produit" >PRODUIT</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="prestation"
                                    value="PRESTATION" >
                                <label class="form-check-label" for="prestation">PRESTATION</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input name="type" class="form-check-input" type="radio" id="aucun" value="AUCUN"
                                    >
                                <label class="form-check-label" for="aucun">AUCUN</label>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="validationTextarea">Référence</label><br>
                        <input name="reference" class="form-control" id="reference" type="text" aria-label="file example"
                            value="{{ $produit[0]->Reference }}" required>
                        <div class="invalid-feedback">La référence est obligatoire</div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="designation">Désignation</label><br>
                        <input name="designation" class="form-control" id="designation" type="text"
                            value="{{ $produit[0]->Designation }}" aria-label="file example" required>
                        <div class="invalid-feedback">La désignation est obligatoire</div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="categorie">Catégorie</label><br>
                        <select name="categorie" class="form-select" id="categorie" type="text" aria-label="file example"
                            required>
                            <option value="{{ $produit[0]->Id_Categorie }}">{{ $produit[0]->Libelle_categorie }}</option>
                            @foreach ($categorie_produits as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">La catégorie est obligatoire</div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="unite_comptage">Unité de comptage</label><br>
                        <select name="unite_comptage" class="form-select" id="unite_comptage" type="text"
                            aria-label="file example" required>
                            <option value="{{ $produit[0]->Id_Unite_Comptage }}">{{ $produit[0]->Libelle }} </option>
                            @foreach ($unite_comptages as $unite_comptage)
                                <option value=" {{ $unite_comptage->id }}">{{ $unite_comptage->Libelle }}
                                    ({{ $unite_comptage->Code }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">L'unité de comptage est obligatoire</div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="type">Statut</label><br>
                        @if ($produit[0]->Statut === 'INACTIF')
                        <div class="form-check form-check-inline">
                            <input name="statut" class="form-check-input" type="radio" id="inactif" value="INACTIF"
                                checked>
                            <label class="form-check-label" for="inactif">INACTIF</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="statut" class="form-check-input" type="radio" id="actif" value="ACTIF" >
                            <label class="form-check-label" for="actif">ACTIF</label>
                        </div>
                        @endif

                        @if ($produit[0]->Statut === 'ACTIF')
                        <div class="form-check form-check-inline">
                            <input name="statut" class="form-check-input" type="radio" id="actif" value="ACTIF" checked>
                            <label class="form-check-label" for="actif">ACTIF</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="statut" class="form-check-input" type="radio" id="inactif" value="INACTIF"
                                >
                            <label class="form-check-label" for="inactif">INACTIF</label>
                        </div>
                        @endif


                    </div>

                    <div class="col-md-8 mb-3">
                        <button class="btn btn-primary" type="submit">VALIDER</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection --}}
