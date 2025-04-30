@extends('layouts.master', ['title' => 'Modifier Produit'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Taxe',
        'infos2' => 'Paxe',
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
                            <form class="was-validated" method="get"
                                action="{{ route('update.produit', $produit[0]->id) }}">
                                @csrf
                                @method('get')
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="type">Type</label><br>
                                    @if ($produit[0]->Type === 'TAXE_SIMPLE')
                                        <div class="form-check form-check-inline">
                                            <input name="type" type="radio" id="TAXE_SIMPLE" value="TAXE_SIMPLE" checked>
                                            <label class="form-check-label" for="TAXE_SIMPLE">TAXE_SIMPLE</label>
                                        </div>

                                    @else
                                        <div class="form-check form-check-inline">
                                            <input name="type" class="form-check-input" type="radio" id="TAXE_SIMPLE"
                                                value="TAXE_SIMPLE" checked>
                                            <label class="form-check-label" for="TAXE_SIMPLE">TAXE_SIMPLE</label>
                                        </div>

                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="reference">Référence</label>
                                    <input name="reference" class="form-control" id="reference" type="text"
                                        value="{{ $produit[0]->Reference }}" required>
                                    <div class="invalid-feedback">La référence est obligatoire</div>
                                </div>
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
                                                ({{ $unite_comptage->Code }})</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">L'unité de comptage est obligatoire</div>
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
                                <button class="btn btn-primary" type="submit">Valider</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </section>
    @include('layouts.alert')
@endsection





