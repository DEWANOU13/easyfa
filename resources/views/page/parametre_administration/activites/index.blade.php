@extends('layouts.master', ['title' => 'Gestionnaire des activités'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Centre de gestion des activités',
'infos2' => 'Paramètres',
'infos3' => 'Gestion des activités',
])

<div class="container">
    <h1><i class="fas fa-tachometer-alt"></i> Centre des activités</h1>

    <div class="row">
        <!-- Section Gestion des Produits -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-box"></i> Gestion des Produits</h5>
                    <p class="card-text">Ajouter, modifier ou supprimer des produits.</p>
                    <a href="" class="btn btn-primary">Gérer les Produits</a>
                </div>
            </div>
        </div>

        <!-- Section Gestion des Commandes -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h5>
                    <p class="card-text">Voir et gérer les commandes des clients.</p>
                    <a href="" class="btn btn-primary">Gérer les Commandes</a>
                </div>
            </div>
        </div>

        <!-- Section Gestion des Utilisateurs -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Gestion des Utilisateurs</h5>
                    <p class="card-text">Gérer les comptes et les rôles des utilisateurs.</p>
                    <a href="" class="btn btn-primary">Gérer les Utilisateurs</a>
                </div>
            </div>
        </div>

        <!-- Section Statistiques et Rapports -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-line"></i> Statistiques et Rapports</h5>
                    <p class="card-text">Voir les statistiques de ventes et les rapports.</p>
                    <a href="" class="btn btn-primary">Voir les Rapports</a>
                </div>
            </div>
        </div>

        <!-- Section Paramètres -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-cogs"></i> Paramètres</h5>
                    <p class="card-text">Configurer les paramètres de l'application.</p>
                    <a href="" class="btn btn-primary">Configurer les Paramètres</a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.alert')
@endSection