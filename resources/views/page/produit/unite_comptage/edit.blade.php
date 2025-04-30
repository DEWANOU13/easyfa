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
                    <li class="breadcrumb-item"><a href="{{ route('page.produit.unite_comptage') }}"><span class="">Unite comptage</span></a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('edit.unite_comptage', $unite_comptage->id) }}"><span class="badge bg-primary">Modification de l'unité de comptage</span></a></li>
                </ol>
            </nav>
        </div>
        <div class="container card">
            <h4 class="text-primary text-center">
                Modification de {{ $unite_comptage->Libelle }}
            </h4><br>
            <div class="card-body">
                <form class="was-validated" method="POST" action="{{ route('update.unite_comptage', $unite_comptage->id)}}">
                    @csrf
                    <div class="mb-3">
                        <label for="validationTextarea" class="form-label fw-bold">Code</label>
                        <input name="code" type="text" class="form-control" aria-label="file example" value="{{ $unite_comptage->Code }}" required>
                        <div class="invalid-feedback">Le code est obligatoire</div>
                    </div>
                    <div class="mb-3">
                        <label for="validationTextarea" class="form-label fw-bold">Libellé</label>
                        <input name="libelle" type="text" class="form-control" aria-label="file example" value="{{ $unite_comptage->Libelle }}" required>
                        <div class="invalid-feedback">Le libellé est obligatoire</div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endSection
