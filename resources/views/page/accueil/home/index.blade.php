@extends('layouts.master', ['title' => 'Accueil'])

@section('content')
<link rel="stylesheet" href="{{ asset('dashboard/plugins/chartist/css/chartist.min.css') }}">


@include('layouts.partials.entete-page', [
'infos1' => 'Tableau de bord',
'infos2' => 'EASY-FAC',
'infos3' => '',
])

<br>
<div class="row">
<div class="col-6">
<div class="row">
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="1" style="{{ $userWidgets->contains(1) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_1() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-solid fa-layer-group float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Produits</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Catégories : {{$TotalCategorieProduit}} </span><br>
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Produits : </span> {{$TotalProduit}}
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="2" style="{{ $userWidgets->contains(2) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_2() }}">
        <div class="mini-stat-icon">
          <i class=" mdi mdi-account-network float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Fournisseurs</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted">Fournisseurs : </span> {{$TotalFournisseur}}
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="3" style="{{ $userWidgets->contains(3) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_1() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-solid fa-store float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Magasins</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Magasins : </span>{{$Totalmagasin}}<br>
          <span class="badge" style="">
          </span> <span class="ml-2 text-muted"></span>
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>

        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="4" style="{{ $userWidgets->contains(4) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_2() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-solid fa-cubes  float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Stock</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted">Stock : {{$TotalStocks}} </span><br>
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted">Entrée / Sortie : {{$totalesEntrees}}/{{$totalesSorties}}</span>
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>

        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="5" style="{{ $userWidgets->contains(5) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_1() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-group float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Clients</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Catégories : {{$totalCategorieClient}} </span><br>
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Clients : </span> {{ $TotalClient }}
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="6" style="{{ $userWidgets->contains(6) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_1() }}">
        <div class="mini-stat-icon">
          <i class=" fa fa-solid fa-file-invoice-dollar float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Factures</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted">Factures : </span>{{$nbreFacture}}<br>
          <span class="badge" style="{{background_color_1()}}">
        </span> <span class="ml-2 text-muted">Factures Avoir : </span>{{$nbreAvoir}}<br>
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted">ProFormas : </span>{{ $nbreProformas }}
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="7" style="{{ $userWidgets->contains(7) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_1() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-solid fa-credit-card float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Reglements</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">En cours : {{$nbrFactureEncourdereglement}}</span><br>
          <span class="badge" style="{{background_color_2()}}">
          </span> <span class="ml-2 text-muted">Terminé : {{$nbrFacturesolde}} </span>
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12 widget-container" data-widget-id="8" style="{{ $userWidgets->contains(8) ? '' : 'display: none;' }}">
    <div class="card mini-stat m-b-30">
      <div class="p-3 text-white" style="{{ background_color_2() }}">
        <div class="mini-stat-icon">
          <i class="fa fa-line-chart float-end mb-0"></i>
        </div>
        <h6 class="text-uppercase mb-0">Chiffre d'affaire</h6>
      </div>
      <div class="card-body">
        <div class="border-bottom pb-4">
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted"> Total: </span> {{number_format($sommeMontantRegler, 0, ',', ' ')}}<br>
          <span class="badge" style="{{background_color_1()}}">
          </span> <span class="ml-2 text-muted"> Recette - 1 mois: {{number_format($sommeMoisEnCours, 0, ',', ' ')}}</span>
        </div>
        <div class="mt-4 text-muted">
          <div class="float-right">
            <p class="m-0"></p>
          </div>
          <h5 class="m-0"></h5>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="col-6">
  <div class="col-lg-12 widget-container" data-widget-id="9" style="{{ $userWidgets->contains(9) ? '' : 'display: none;' }}">
    <div class="card m-b-30">
      <div class="card-body">
        <h4 class="mt-0 header-title">Top des 10 meilleurs clients du mois</h4>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nom client</th>
                <!-- <th>Adresse client</th> -->
                <th>Chiffre d'Affaire</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($topClients as $key => $client)
              <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $client->Denomination_sociale }}</td>
                <!-- <td>{{ $client->Adresse_client }}</td> -->
                <td>{{ $client->chiffre_affaires }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> <!-- end col -->
  <div class="col-lg-12 widget-container" data-widget-id="10" style="{{ $userWidgets->contains(10) ? '' : 'display: none;' }}">
    <div class="card m-b-30">
      <div class="card-body">

        <h4 class="mt-0 header-title">Top des 10 meilleurs produits du mois</h4>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Nom produit</th>
                <th>Vente total</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($topProduits as $key => $produit)
              <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $produit->Reference }}</td>
                <td>{{ $produit->Designation }}</td>
                <td>{{ $produit->total_sales }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> <!-- end col -->
  <div class="col-lg-12 widget-container" data-widget-id="11" style="{{ $userWidgets->contains(11) ? '' : 'display: none;' }}">
    <div class="card m-b-30">
      <div class="card-body">
        <h4 class="mt-0 header-title">Historique de vos 5 dernières connexions</h4>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Connexions</th>
                <th>Déconnexions</th>
                <th>Adresse IP</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($loginHistories as $key => $clientHistories)
              @if ($key < 5) <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{$clientHistories->login_at }}</td>
                <td>{{$clientHistories->logout_at }}</td>
                <td>{{ $clientHistories->ip_address }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> <!-- end col -->
</div>

</div>

<div class="row">
  <div class="col-xl-6 widget-container" data-widget-id="50" style="{{ $userWidgets->contains(50) ? '' : 'display: none;' }}">
    <div class="card m-b-30">
      <div class="card-body">

        <h4 class="mt-0 header-title">Diagramme des entrées et des sorties</h4>

        <ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
          <li>
            <h4 class=""><b>3654</b></h4>
            <p class="text-muted">Entrées</p>
          </li>
          <li>
            <h4 class=""><b>954</b></h4>
            <p class="text-muted">Sorties</p>
          </li>
        </ul>

        <div id="animating-donut" class="ct-chart ct-golden-section"></div>

      </div>
    </div>
  </div> <!-- end col -->

  <div class="col-xl-6 widget-container" data-widget-id="51" style="{{ $userWidgets->contains(51) ? '' : 'display: none;' }}">
    <div class="card m-b-30">
      <div class="card-body">

        <h4 class="mt-0 header-title">Diagramme des règlements</h4>
        <ul class="list-inline widget-chart m-t-20 m-b-15 text-center">
          <li>
            <h4 class=""><b>33%</b></h4>
            <p class="text-muted">Marketplace</p>
          </li>
          <li>
            <h4 class=""><b>42%</b></h4>
            <p class="text-muted">Last week</p>
          </li>
          <li>
            <h4 class=""><b>25%</b></h4>
            <p class="text-muted">Last Month</p>
          </li>
        </ul>

        <div id="simple-pie" class="ct-chart ct-golden-section simple-pie-chart-chartist"></div>

      </div>
    </div>
  </div> <!-- end col -->
</div> <!-- end row -->
<br>
<div class="row pb-5">

</div>
<br>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les préférences utilisateur via AJAX
    fetch('{{ route('api.user.widgets') }}')
      .then(response => response.json())
      .then(data => {
        // Récupérer tous les widgets sur la page
        const widgets = document.querySelectorAll('.widget-container');

        // Parcourir les widgets et ajuster l'affichage en fonction des préférences de l'utilisateur
        widgets.forEach(widget => {
          const widgetId = parseInt(widget.getAttribute('data-widget-id'));

          // Vérifier si l'utilisateur préfère afficher ce widget
          if (data.userWidgets.includes(widgetId)) {
            widget.style.display = 'block'; // Afficher le widget
          } else {
            widget.style.display = 'none'; // Masquer le widget
          }
        });
      });
  });
</script>

@endsection
