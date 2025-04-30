@extends('layouts.layout', ['title' => 'Acceuil'])
@section('content')
<style>
    .gradient-1 {
        background: linear-gradient(to right, #397bf6, #2ea5d4, #31aee0);
        border: none;
        transition: transform 0.3s ease;
    }

    .gradient-2 {
        background: linear-gradient(to right, #de9292, #f69797, #ffcccc);
        border: none;
        transition: transform 0.3s ease;
    }

    .gradient-3 {
        background: linear-gradient(to right, #ef9629, #f4c66c, #f6d067);
        border: none;
        transition: transform 0.3s ease;
    }

    .gradient-4 {
        background: linear-gradient(to right, #60f13c, #aff16c, #bff48b);
        border: none;
        transition: transform 0.3s ease;
    }

    .gradient-1:hover,
    .gradient-2:hover,
    .gradient-3:hover,
    .gradient-4:hover {
        transform: scale(1.05, 1.05);
    }
</style>
<section>
    @include('page.components.shared')
    <div class="container-fluid">
        <h5 class="text-primary">Tableau de Bord</h5>
        <div>
            <div class="container-fluid py-1">
                <div class="row">
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{ route('client') }}" style="text-decoration: none;">
                            <div class="card gradient-1">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total clients</p>
                                        <h4 class="mb-0">{{ $TotalClient }}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{ route('page.produit.produit') }}" style="text-decoration: none;">
                            <div class="card gradient-4">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total produits </p>
                                        <h4 class="mb-0">{{$TotalProduit}}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{ route('fournisseur') }}" style="text-decoration: none;">
                            <div class="card gradient-3">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total fournisseurs</p>
                                        <h4 class="mb-0">{{$TotalFournisseur}}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <div class="card gradient-2">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="bi bi-shop-window"></i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Total magasins </p>
                                    <h4 class="mb-0">{{$Totalmagasin}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <div class="card gradient-3">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="bi bi-shop-window"></i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Total magasins </p>
                                    <h4 class="mb-0">{{$Totalmagasin}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-4 mb-xl-0 mb-4">
                        <div class="card gradient-4">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="bi bi-shop-window"></i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Total magasins </p>
                                    <h4 class="mb-0">{{$Totalmagasin}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="dark horizontal my-2">
                    <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{ route('proforma') }}" style="text-decoration: none;">
                            <div class="card gradient-4">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z" />
                                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z" />
                                            </svg></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total proformas</p>
                                        <h4 class="mb-0">{{ $totalProformats }}</h4>
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0">Montant total: <span class="text-success text-sm font-weight-bolder">{{ $totalMontantProformats }}
                                        </span></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{ route('facture') }}" style="text-decoration: none;">
                            <div class="card gradient-1">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                            </svg></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total factures/Avoirs</p>
                                        <h4 class="mb-0">{{$totalFactures}} / {{$totalAvoirs}}</h4>
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0">Chiffre d'Affaire: <span class="text-success text-sm font-weight-bolder"> {{$totalMontantFacturesV}}
                                        </span></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
                        <a href="{{route('reglement')}}" style="text-decoration: none;">
                            <div class="card gradient-2">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                                                <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                                                <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                                                <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                                            </svg></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Reglements</p>
                                        <h4 class="mb-0">{{$totalMontantFacN}}</h4>
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">{{$totalMontantReglements}}
                                            ({{$pourcentageReglements}} %) </span>réglé</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
                        <a href="" style="text-decoration: none;">
                            <div class="card gradient-3">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet" viewBox="0 0 16 16">
                                                <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a2 2 0 0 1-1-.268M1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1" />
                                            </svg></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Produits en stock</p>
                                        <h4 class="mb-0">{{$TotalStocks}}</h4>
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">10 </span>
                                        produits presque finis
                                    </p>
                                </div>
                            </div>
                        </a>

                    </div>

                    <div class="col-xl-6 col-sm-12 p-3">
                        Diagramme des entrées et des sorties
                        <div class="card card-body">
                            <div class="">
                            <div class="btn-group d-flex flex-wrap" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" id="btn7" onclick="updateGraph(7)">7 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" id="btn14" onclick="updateGraph(14)">14 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" id="btn30" onclick="updateGraph(30)">30 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" id="btnPersonnaliser" onclick="afficherPersonnalisation()">Personnaliser</button>
                            </div>
                            </div>

                            <div id="personnalisation" class="">
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <label for="dateDebut" class="col-form-label">Date de début:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" class="form-control" id="dateDebut">
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center mt-2">
                                    <div class="col-auto">
                                        <label for="dateFin" class="col-form-label">Date de fin:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" class="form-control" id="dateFin">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-auto">
                                        <button class="btn btn-primary" onclick="personnaliser()">Mise à jour</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header p-3 pt-2 mt-1">
                                <canvas id="graphiqueEntrees"></canvas>
                            </div>

                        </div>
                    </div>

                    <div class="col-xl-6 col-sm-12 p-3">
                        Diagramme des règlements
                        <div class="card card-body">
                            <div class="btn-group d-flex flex-wrap" role="group" aria-label="Basic outlined example">
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" >7 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" >14 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" >30 derniers jours</button>
                                <button type="button" class="btn btn-outline-primary flex-grow-1 mb-1" >Personnaliser</button>
                            </div>

                            <div id="personnalisation" style="display: none;">
                                <label for="dateDebut">Date de début:</label>
                                <input type="date" id="dateDebut">
                                <label for="dateFin">Date de fin:</label>
                                <input type="date" id="dateFin">
                                <button>Mise à jour</button>
                            </div>
                            <div class="card-header p-3 pt-2">
                                <canvas id="graphiqueReglements"></canvas>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 p-3">
                        Top des 10 meilleurs clients du mois
                        <div class="card card-body">
                            <div class="card-header p-3 pt-2">
                                <canvas id="graphiqueTop10Clients"></canvas>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 p-3">
                        Top des 10 meilleurs produits du mois
                        <div class="card card-body">
                            <div class="card-header p-3 pt-2">
                                <canvas id="graphiqueTop10Produits"></canvas>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Graphe des top 10 clients -->
    <script>
        // Récupérer les données transmises par le contrôleur Laravel
        var topClients = <?php echo json_encode($topClients); ?>;

        // Extraire les noms des clients et leur chiffre d'affaires dans des tableaux séparés
        var clientNames = [];
        var chiffreAffaires = [];

        topClients.forEach(function(client) {
            clientNames.push(client.Denomination_sociale);
            chiffreAffaires.push(client.chiffre_affaires);
        });

        // Créer un graphique à barres
        var ctx = document.getElementById('graphiqueTop10Clients').getContext('2d');
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: clientNames,
                datasets: [{
                    label: 'Chiffre d\'affaires',
                    data: chiffreAffaires,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
    <!-- Graphe des top 10 produits -->
    <script>
        // Récupérer les données transmises par le contrôleur Laravel
        var topProduits = <?php echo json_encode($topProduits); ?>;

        // Extraire les noms des clients et leur chiffre d'affaires dans des tableaux séparés
        var produitName = [];
        var QuantiteVendue = [];
        var codeProduit = [];

        topProduits.forEach(function(produit) {
            produitName.push(produit.Designation);
            QuantiteVendue.push(produit.total_sales);
            codeProduit.push(produit.Reference);
        });

        // Créer un graphique à barres
        var ctx = document.getElementById('graphiqueTop10Produits').getContext('2d');
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: produitName,
                datasets: [{
                    label: 'Quantité vendues',
                    data: QuantiteVendue,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <script>
        var myChart; // Variable globale pour stocker l'instance du graphique
        // Fonction pour mettre à jour le graphique en fonction de l'intervalle sélectionné
        function updateGraph(interval) {
            var divPersonnalisation = document.getElementById('personnalisation');
            divPersonnalisation.style.display = 'none';
            // Supprimer le graphique existant s'il y en a un
            if (myChart) {
                myChart.destroy();
            }

            // Supprimer la classe "active" de tous les boutons
            document.querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Ajouter la classe "active" au bouton cliqué
            document.getElementById('btn' + interval).classList.add('active');

            // Votre code pour mettre à jour le graphique ici
            var ctx = document.getElementById('graphiqueEntrees').getContext('2d');
            var totalesEntrees = <?php echo json_encode($totalesEntrees); ?>;
            var datesEntree = <?php echo json_encode($dates); ?>;
            var datesSortie = <?php echo json_encode($datesSorties); ?>;
            var totalesSorties = <?php echo json_encode($totalesSorties); ?>;

            // Fonction pour filtrer les dates en fonction de l'intervalle de jours
            function filterDatesByInterval(dates, interval) {
                var currentDate = new Date(); // Date actuelle
                var endDate = new Date(currentDate); // Date de fin (aujourd'hui)
                endDate.setHours(0, 0, 0, 0); // Réinitialiser l'heure à minuit pour inclure toute la journée actuelle
                var startDate = new Date(endDate); // Date de début

                // Définir la date de début en fonction de l'intervalle
                startDate.setDate(startDate.getDate() - interval + 1); // Décalage en arrière de "interval - 1" jours pour inclure la journée actuelle
                startDate.setHours(0, 0, 0, 0); // Réinitialiser l'heure à minuit pour inclure toute la journée de début

                // Filtrer les dates dans l'intervalle spécifié
                return dates.filter(date => {
                    var currentDate = new Date(date);
                    return currentDate >= startDate && currentDate <= endDate;
                });
            }

            // Filtrer les dates dans l'intervalle spécifié et les rendre uniques
            var filteredDates = [...new Set(filterDatesByInterval(datesEntree.concat(datesSortie), interval))];
            var allDatesSorted = filteredDates.sort((a, b) => new Date(a) - new Date(b));

            // Créer des ensembles de données avec des valeurs cumulées pour les dates identiques
            var mergedData1 = [];
            var mergedData2 = [];

            var cumulativeTotal1 = 0;
            var cumulativeTotal2 = 0;

            allDatesSorted.forEach(date => {
                var cumulativeTotal1 = 0;
                var cumulativeTotal2 = 0;
                // Calculer le total des entrées pour la date actuelle
                var totalEntreesForDate = totalesEntrees.reduce((acc, currentValue, index) => {
                    if (datesEntree[index] === date) {
                        acc += currentValue;
                    }
                    return acc;
                }, 0);

                // Calculer le total des sorties pour la date actuelle
                var totalSortiesForDate = totalesSorties.reduce((acc, currentValue, index) => {
                    if (datesSortie[index] === date) {
                        acc += currentValue;
                    }
                    return acc;
                }, 0);

                cumulativeTotal1 += totalEntreesForDate;
                cumulativeTotal2 += totalSortiesForDate;

                mergedData1.push(cumulativeTotal1);
                mergedData2.push(cumulativeTotal2);
            });

            // Créer un nouveau graphique
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: allDatesSorted,
                    datasets: [{
                        label: 'Entrées',
                        data: mergedData1,
                        borderColor: 'blue',
                        backgroundColor: 'transparent',
                    }, {
                        label: 'Sorties',
                        data: mergedData2,
                        borderColor: 'red',
                        backgroundColor: 'transparent',
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        // Appeler la fonction updateGraph pour afficher les données des 7 derniers jours par défaut
        document.addEventListener('DOMContentLoaded', function() {
            updateGraph(7);
        });

        function afficherPersonnalisation() {
            var divPersonnalisation = document.getElementById('personnalisation');
            divPersonnalisation.style.display = 'block';

            // Supprimer la classe "active" de tous les boutons d'intervalle prédéfinis
            document.querySelectorAll('.intervalle-button').forEach(btn => {
                btn.classList.remove('active');
            });
        }
        // Fonction pour modifier l'ID du bouton "Personnaliser" en fonction de l'intervalle
        function modifierIdBoutonPersonnaliser(interval) {
            var btnPersonnaliser = document.getElementById('btnPersonnaliser');
            btnPersonnaliser.id = 'btn' + interval;
        }

        function personnaliser() {
            var dateDebutInput = document.getElementById('dateDebut');
            var dateFinInput = document.getElementById('dateFin');

            // Vérifier si les champs de date sont vides
            if (!dateDebutInput.value || !dateFinInput.value) {
                alert("Veuillez entrer une date de début et une date de fin.");
                return;
            }

            var dateDebut = new Date(dateDebutInput.value);
            var dateFin = new Date(dateFinInput.value);

            // Vérifier si les dates sont valides
            if (isNaN(dateDebut.getTime()) || isNaN(dateFin.getTime())) {
                alert("Veuillez entrer des dates valides.");
                return;
            }

            // Vérifier si la date de fin est antérieure à la date de début
            if (dateFin < dateDebut) {
                alert("La date de fin doit être postérieure à la date de début.");
                return;
            }

            // Calculer l'intervalle en jours entre la date de début et la date de fin
            var interval = Math.ceil((dateFin - dateDebut) / (1000 * 60 * 60 * 24));
            // console.log('interval', interval);

            // Modifier l'ID du bouton "Personnaliser" en fonction de l'intervalle
            modifierIdBoutonPersonnaliser(interval);
            // Mettre à jour le graphique avec l'intervalle calculé
            updateGraph(interval);
        }
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('graphiqueSorties').getContext('2d');
            var totalesEntrees = <?php echo json_encode($totalesEntrees); ?>;
            var designations = <?php echo json_encode($designations); ?>;

            var totalEntrees = totalesEntrees.reduce((a, b) => a + b, 0); // Calculer le total des entrées

            var graphique = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: designations,
                    datasets: [{
                        label: 'Variation des quantités en fonction des produits',
                        data: totalesEntrees
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var percent = Math.round((value / totalEntrees) *
                                        100); // Calculer le pourcentage
                                    return label + ': ' + value + ' (' + percent + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('graphiqueStock').getContext('2d');
            var quantiteEnStock = <?php echo json_encode($quantiteEnStock); ?>;
            var produitStock = <?php echo json_encode($produitStock); ?>;

            var quantiteEnStocks = quantiteEnStock.reduce((a, b) => a + b,
                0); // Calculer le total des quantités en stock

            var graphique = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: produitStock,
                    datasets: [{
                        label: 'Quantités des produits en stock',
                        data: quantiteEnStock
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var percent = Math.round((value / quantiteEnStocks) *
                                        100); // Calculer le pourcentage
                                    return label + ': ' + value + ' (' + percent + '%)';
                                }
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Graphe du stock' // Titre du graphe
                    }
                }
            });
        });
    </script>
    <script>
        var ctx = document.getElementById('graphiqueReglements').getContext('2d');
        var datesR = <?php echo json_encode($datesR); ?>;
        var heuresR = <?php echo json_encode($heuresR); ?>;
        var totalReglementsParDate = <?php echo json_encode($totalReglementsParDate); ?>;

        var graphique = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datesR,
                datasets: [{
                    label: 'Total des règlements par jour',
                    data: totalReglementsParDate,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</section>
@endSection
