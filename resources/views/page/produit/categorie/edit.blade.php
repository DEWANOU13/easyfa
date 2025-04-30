@extends('layouts.layout')
@section('content')
    <style>
        .entete_tableau {
            background-color: #6104ed;
            color: white;
        }
    </style>
    <section>
        <br>
        <br>
        @include('message.erreur')
        <div class="row d-flex text-start p-3">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Produits</li>
                    <li class="breadcrumb-item"><a href="{{ route('page.produit.categorie') }}"><span class="">Catégorie</span></a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('edit.categorie', $categorie->id) }}"><span class="badge bg-primary">Modification de la catégorie</span></a></li>
                </ol>
            </nav>
        </div>
        <div class="container card">
            <h4 class="text-primary text-center">
                Modification de {{ $categorie->Libelle }}
            </h4><br>
            <div class="card-body">
                <form class="was-validated" methode="POST" action="{{ route('update.categorie', $categorie->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="validationTextarea" class="form-label fw-bold">Catégorie</label>
                        <input name="libelle" type="text" class="form-control" aria-label="file example" value="{{ $categorie->Libelle }}" required>
                        <div class="invalid-feedback">La catégorie est obligatoire</div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endSection
