@extends('layouts.master', ['title' => 'Centre de contrôle'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Centre de contrôle',
'infos2' => 'Paramètres',
'infos3' => 'Centre de contrôle',
])


<div class="container">
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="connected-users-tab" data-bs-toggle="tab" href="#connected-users" role="tab" aria-controls="connected-users" aria-selected="true">Utilisateurs Connectés</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="login-history-tab" data-bs-toggle="tab" href="#login-history" role="tab" aria-controls="login-history" aria-selected="false">Historique de Connexion</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="activity-tracking-tab" data-bs-toggle="tab" href="#activity-tracking" role="tab" aria-controls="activity-tracking" aria-selected="false">Suivi des Activités</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="user-roles-tab" data-bs-toggle="tab" href="#historiqueName" role="tab" aria-controls="historiqueName" aria-selected="false">Historique Nom d'utilisateur</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="history-produit-tab" data-bs-toggle="tab" href="#produitHistorique" role="tab" aria-controls="produitHistorique" aria-selected="false">Historiques Produits</a>
        </li>
       <!-- <li class="nav-item" role="presentation">
            <a class="nav-link" id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab" aria-controls="notifications" aria-selected="false">Notifications</a>
        </li> -->
    </ul>
    <div class="tab-content" id="adminTabsContent">
        <!-- Section Utilisateurs Connectés -->
        <div class="tab-pane fade show active" id="connected-users" role="tabpanel" aria-labelledby="connected-users-tab">
            @include('admin.partials.connected_users')
        </div>
        <!-- Section Historique de Connexion -->
        <div class="tab-pane fade" id="login-history" role="tabpanel" aria-labelledby="login-history-tab">
            @include('admin.partials.login_history')
        </div>
        <!-- Section Suivi des Activités -->
        <div class="tab-pane fade" id="activity-tracking" role="tabpanel" aria-labelledby="activity-tracking-tab">
            @include('admin.partials.activity_tracking')
        </div>
        <!-- Section Rôles et Permissions -->
        <div class="tab-pane fade" id="user-roles" role="tabpanel" aria-labelledby="user-roles-tab">
            @include('admin.partials.user_roles')
        </div>
        <!-- Section Notifications -->
        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
            @include('admin.partials.notifications')
        </div>
          <!-- Section Notifications -->
          <div class="tab-pane fade" id="historiqueName" role="tabpanel" aria-labelledby="historiqueName">
           @include('admin.partials.historique_nom_user')
        </div>
        <div class="tab-pane fade" id="produitHistorique" role="tabpanel" aria-labelledby="produitHistorique">
            @include('admin.partials.historique_produit')
         </div>
    </div>
</div>
<br><br>

@include('layouts.alert')

@endSection
