@extends('layouts.master', ['title' => 'Transfert'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Transfert',
        'infos2' => 'Transfert',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @canany(['imprimer-liste-transfert', 'effectuer-transfert-produit'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('effectuer-transfert-produit')
                            <li><a class="dropdown-item" type="button"
                                    href="{{ route('page.produit.transfert_nouveau') }}">Nouveau</a></li>
                        @endcan

                        <form action="{{ route('transfert-imprimer') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" id="value" name="id_transfert">
                            @can('imprimer-liste-transfert')
                                <li><button class="dropdown-item" name="reponse" value="imprimer" type="submit"
                                        id="imprimer-button">Imprimer</button>
                                @endcan
                                @can('transfert-produit-excel')
                                <li><button class="dropdown-item" name="reponse" value="exporter" type="submit"
                                        id="imprimer-button">Exporter</button>
                                @endcan
                            </li>
                        </form>

                    </ul>
                </div>
            @endcanany
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">Liste</button>

                    @canany(['exporter-impression-transfert-produit', 'imprimer-impression-transfert-produit'])
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Impression</button>
                    @endcanany

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                    tabindex="0">
                    <div class="row mt-4 mb-5">
                        <div class="col-md-2">
                            <div class="row border">
                                <form class="d-flex align-items-center mt-2" id="filterForm"
                                    action="{{ route('filterTransfert') }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">

                                            <label class="m-1" for="annee">Année</label>
                                            <select name="annee" id="anneeSelect" class="form-select">
                                                <option value="">Sélectionner une année</option>
                                                @foreach ($annees as $annee)
                                                    <option value="{{ $annee }}">{{ $annee }}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                        <div class="col-md-12 mb-3">
                                            <select class="form-select" id="monthSelect" size="12" name="month"
                                                aria-label="Size 3 select example">
                                                <option value="">{{ __('Sélectionnez un mois') }}</option>
                                                @php
                                                    $months = [
                                                        1 => 'Janvier',
                                                        2 => 'Février',
                                                        3 => 'Mars',
                                                        4 => 'Avril',
                                                        5 => 'Mai',
                                                        6 => 'Juin',
                                                        7 => 'Juillet',
                                                        8 => 'Août',
                                                        9 => 'Septembre',
                                                        10 => 'Octobre',
                                                        11 => 'Novembre',
                                                        12 => 'Décembre',
                                                    ];
                                                @endphp
                                                @foreach ($months as $key => $month)
                                                    <option value="{{ $key }}"
                                                        @if (request()->query('month') == $key) selected style="color: blue;" @endif>
                                                        {{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>

                        <div class="col-md-10">
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des transferts</h3>
                                </div>
                                <div class="card-body responsive-2">
                                    <div class="table-responsive">
                                        <table id="proformaTable"
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th hidden scope="col">
                                                    </th>
                                                    <th scope="col">
                                                        Agence</th>
                                                    <th scope="col">Date
                                                    </th>
                                                    <th scope="col">
                                                        Référence</th>
                                                    <th scope="col">
                                                        Magasin source</th>
                                                    <th scope="col">
                                                        Magasin de destination</th>
                                                    <th scope="col">
                                                        Observations</th>
                                                    <th scope="col">
                                                        Enregistré par</th>
                                                </tr>
                                                {{-- <th style="width: 5%">Statut</th> --}}

                                            </thead>

                                            <tbody>
                                                @forelse($transfert_produits as $transfert_produit)
                                                    <tr style="cursor:pointer" class="clickable-row"
                                                        data-url="{{ route('get.transferer_for_transfert_produit', ['id' => $transfert_produit->id]) }}">
                                                        <td hidden class="entree-produit"><input type="hidden"
                                                                value="{{ $transfert_produit->id }}"></td>
                                                        <td class="entree-produit">{{ $transfert_produit->NomAgence }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ \Carbon\Carbon::parse($transfert_produit->Date_Transfert)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $transfert_produit->Reference_Transfert }}</td>
                                                        <td class="entree-produit">
                                                            {{ $transfert_produit->NomMagasinSource }}
                                                            <strong>({{ $transfert_produit->NomAgence }})</strong>
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $transfert_produit->NomMagasinDestination }} <strong>(
                                                                {{ $transfert_produit->NomAgenceDestination }})</strong>
                                                        </td>
                                                        <td class="entree-produit">
                                                            {{ $transfert_produit->Observations }}</td>
                                                        <td class="entree-produit">{{ $transfert_produit->name }}</td>
                                                        {{-- <td class="entree-produit"><form action="" ><button type="submit">Afficher</button></form></td> --}}
                                                    </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card m-b-30 my-3">
                                <div class="card-header" style="{{ background_color_2() }}">
                                    <h3 class="mt-2  d-inline-block text-dark">Détails transferts</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive responsive-1">
                                        <div class="col-md-12">
                                            <table id=""
                                                class="datatable tableInfo2 table table-striped table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th scope="col">Référence produit</th>
                                                        <th scope="col">Désignation</th>
                                                        <th scope="col">Qté</th>
                                                        {{-- <th scope="col">Prix</th> --}}
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @forelse($transferers as $transferer)
                                                        <tr style="cursor:pointer" class="clickable-row">
                                                            <td class="entree-produit">{{ $transferer->Reference }}</td>
                                                            <td class="entree-produit">{{ $transferer->Designation }}</td>
                                                            <td class="entree-produit">{{ $transferer->Qte_transferee }}
                                                            </td>
                                                            {{-- <td class="entree-produit">{{ $transferer->Qte_stockee }} --}}
                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="row my-4">
                        <div class="col-md-8 offset-md-2 mb-5">
                            <h3>LISTE DES TRANSFERT DES PRODUITS SUR UNE PERIODE</h3>
                            <form class="row gx-3 gy-2 pb-4" method="POST" action="{{ route('imprimer-transfert') }}"
                                target="_blank">
                                @csrf
                                <div class="col-sm-6">
                                    <label class="form-label" for="date_debut_periode">Debut Période</label>
                                    <input name="date_debut_periode" type="date" class="form-control"
                                        id="date_debut_periode" value="<?php echo date('Y-m-d', strtotime('-5 days')); ?>">
                                    <div class="invalid-feedback">La date de debut est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="date_fin_periode">Fin Période</label>
                                    <input name="date_fin_periode" type="date" class="form-control"
                                        id="date_fin_periode" value="<?php echo date('Y-m-d'); ?>">
                                    <div class="invalid-feedback">La date de fin est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="magasin">Source</label>
                                    <select name="magasin_source" type="text" class="form-select"
                                        id="magasin_source">
                                        <option value="Tous">Tous</option>
                                        @foreach ($magasins as $magasin)
                                            <option value="{{ $magasin->id }}">{{ $magasin->NomMagasin }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">La source est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="fournisseur">Destination</label>
                                    <select name="magasin_destination" type="text" class="form-select"
                                        id="magasin_destination">
                                        <option value="Tous">Tous</option>
                                        @foreach ($magasins as $magasin)
                                            <option value="{{ $magasin->id }}">{{ $magasin->NomMagasin }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">La destination de fin est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="categorie">Catégorie</label>
                                    <select name="categorie" type="text" class="form-select" id="categorie">
                                        <option value="Toutes">Toutes</option>
                                        @foreach ($categories as $categorie)
                                            <option value="{{ $categorie->id }}">{{ $categorie->Libelle }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">La catégorie est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="produit">Produit</label>
                                    <select name="produit" type="text" class="form-select" id="produit">
                                        <option value="Tous">Tous</option>
                                        @foreach ($produits as $produit)
                                            <option value="{{ $produit->id }}">{{ $produit->Reference }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Le produit de fin est obligatoire</div>
                                </div>
                                <div class="">
                                    <div class="d-grid gap-2 d-md-block">

                                        @can('imprimer-impression-transfert-produit')
                                            <button type="submit" name="submit" value="PDF" class="btn text-white "
                                                style="{{ background_color_1() }}">Imprimer PDF</button>
                                        @endcan

                                        @can('exporter-impression-transfert-produit')
                                            <button type="submit" name="submit" value="EXCEL" class="btn text-white "
                                                style="{{ background_color_2() }}">Imprimer EXCEL</button>
                                        @endcan
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.alert')
    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }

        .tableInfo {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo th,
        .tableInfo td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }

        .tableInfo2 {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableInfo2 th,
        .tableInfo2 td {
            /*             border: 1px solid #ddd;*/
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;

        }
    </style>
    <script>
        $(document).ready(function() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-1')
                        .html(); // Sélectionnez le contenu du premier tableau
                    $(".responsive-1").html(
                        tableContent); // Injectez le contenu dans le deuxième tableau
                });
            });
        });
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });

        $(document).ready(function() {
            // Désactivez le bouton "Valider" par défaut
            $('#imprimer-button').prop('disabled', true);

            // Ajoutez un gestionnaire de clic aux lignes avec la classe clickable-row
            $('.clickable-row').click(function() {
                // Activez le bouton "Valider"
                $('#imprimer-button').prop('disabled', false);
            });
        });

        $(document).ready(function() {
            $(".clickable-row").click(function() {
                // Récupérer la valeur de l'input caché
                var value = $(this).find('input[type="hidden"]').val();

                // Afficher la valeur dans la console (ou effectuer une autre action)
                // console.log(value);

                // Optionnel: assigner la valeur à un autre élément input caché avec id "value"
                $('#value').val(value);

                // Optionnel: rediriger vers une URL (décommenter pour activer)
                // window.location = $(this).data("url");
            });
        });
    </script>
    <script>
        document.getElementById('monthSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('anneeSelect').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
    <script>
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = parseFloat(number).toFixed(decimals);
            var parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
            return parts.join(dec_point);
        }

        function applyTableEvents() {
            $(".clickable-row").click(function() {
                var url = $(this).data("url");
                $.get(url, function(data) {
                    var tableContent = $(data).find('.responsive-1').html();
                    $(".responsive-1").html(tableContent);
                });
                $('.clickable-row').removeClass('active');
                $(this).addClass('active');
            });




            $('.clickable-row').on('click', function() {
                $('.clickable-row').removeClass('selected');
                $(this).addClass('selected');
            });
        }

        function clearDetailTable() {
            var detailTbody = document.querySelector('.tableInfo2 tbody');
            detailTbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">Aucune donnée (Selectionner une facture)</td>
                </tr>
            `;
        }

        $(document).ready(function() {
            applyTableEvents();
        });
    </script>
@endsection
