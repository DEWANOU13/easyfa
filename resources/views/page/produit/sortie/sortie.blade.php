@extends('layouts.master', ['title' => 'Sorties'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Sortie',
        'infos2' => 'Sortie',
        'infos3' => 'Liste',
    ])
    {{-- @section('content') --}}
    <div class="row d-flex text-start p-3">
        <div class="col text-end">
            @canany(['imprimer-liste-sorties-produits', 'effectuer-sortie-produit'])
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="{{ background_color_1() }}">
                        Action
                    </button>
                    <ul class="dropdown-menu" style="z-index: 2000;">
                        @can('effectuer-sortie-produit')
                            <li><a href="{{ route('page.produit.sortie_nouveau') }}" class="dropdown-item"
                                    type="button">Nouveau</a></li>
                        @endcan

                        <form action="{{ route('imprimer-sortie-action') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" id="value" name="id_sortie">
                            @can('imprimer-liste-sorties-produits')
                                <li><button class="dropdown-item" value="imprimer" name="reponse" type="submit"
                                        id="imprimer-button">Imprimer</button>
                                @endcan
                                @can('sortie-produit-excel')
                                <li><button class="dropdown-item" value="exporter" name="reponse" type="submit"
                                        id="imprimer-button">Exporter</button>
                                </li>
                            @endcan

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
                    @canany(['exporter-impression-sortie-produit', 'imprimer-impression-sortie-produit'])
                        {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Impression</button> --}}
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
                                    action="{{ route('filterSortie') }}" method="GET">
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
                                    <h3 class="mt-2  d-inline-block text-dark">Liste des sorties</h3>
                                </div>
                                <div class="card-body responsive-2">
                                    <div class="table-responsive">
                                        <table id="proformaTable"
                                            class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-primary">

                                                <tr>
                                                    <th hidden scope="col"></th>
                                                    <th scope="col">Agence</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Référence</th>
                                                    <th scope="col">Type sortie</th>
                                                    <th scope="col">Observations</th>
                                                    <th scope="col">Enregistré par</th>
                                                </tr>
                                                {{-- <th style="width: 5%">Statut</th> --}}

                                            </thead>

                                            <tbody>
                                                @forelse($sortie_produits as $sortie_produit)
                                                    <tr style="cursor:pointer" class="clickable-row"
                                                        data-url="{{ route('get.sortir_produit_for_sortie_produit', ['id' => $sortie_produit->id]) }}">
                                                        <td hidden class="entree-produit"><input type="hidden"
                                                                value="{{ $sortie_produit->id }}"></td>
                                                        <td class="entree-produit">{{ $sortie_produit->NomAgence }}</td>
                                                        <td class="entree-produit">
                                                            {{ \Carbon\Carbon::parse($sortie_produit->Date_Sortie)->format('d/m/Y H:i') }}
                                                        </td>
                                                        <td class="entree-produit">{{ $sortie_produit->Reference_Sortie }}
                                                        </td>
                                                        <td class="entree-produit">{{ $sortie_produit->type_sortie }}</td>
                                                        <td class="entree-produit">{{ $sortie_produit->Observations }}
                                                        </td>
                                                        <td class="entree-produit">{{ $sortie_produit->name }}</td>
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
                                    <h3 class="mt-2  d-inline-block text-dark">Détails sorties</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive responsive responsive-1 ">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-striped table-bordered dt-responsive nowrap tableInfo2">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th style="" scope="col">Référence produit</th>
                                                        <th style="" scope="col">Désignation</th>
                                                        <th style="" scope="col">Qté</th>
                                                        <th style="" scope="col">Prix</th>
                                                        <th style="" scope="col">Magasin</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($sortir_produits as $sortir_produit)
                                                        <tr>
                                                            <td class="entree-produit">{{ $sortir_produit->Reference }}
                                                            </td>
                                                            <td class="entree-produit">{{ $sortir_produit->Designation }}
                                                            </td>
                                                            <td class="entree-produit">{{ $sortir_produit->Qte_Sortie }}
                                                            </td>
                                                            <td class="entree-produit">
                                                                {{ number_format($sortir_produit->Prix_Achat_Net, 0, '.', ' ') }}
                                                            </td>
                                                            <td class="entree-produit">{{ $sortir_produit->NomMagasin }}
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">Aucune donnée </td>
                                                        </tr>
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
                    <div class="row mt-1 d-flex justify-content-center">
                        <div class="col-md-8">
                            <h3>LISTE DES SORTIES EN STOCK SUR UNE PERIODE</h3>
                            <form class="row gx-3 gy-2 mb-5" action="{{ route('imprimer-sortie') }}" target="_blank"
                                method="POST">
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
                                    <label class="form-label" for="magasin">Magasin</label>
                                    <select name="magasin" type="text" class="form-select js-single"
                                        style="width: 100%" id="magasin">
                                        {{-- <option value="">Sélectionnez un magasin</option> --}}
                                        <option value="Tous">Tous</option>
                                        @foreach ($magasins as $magasin)
                                            <option value="{{ $magasin->id }}">
                                                {{ $magasin->NomMagasin }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Le magasin est obligatoire</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="categorie">Catégorie</label>
                                    <select name="categorie" type="text" class="form-select js-single"
                                        style="width: 100%" id="categorie">
                                        {{-- <option value="">Sélectionnez une catégorie</option> --}}
                                        <option value="Toutes">Toutes</option>
                                        @foreach ($categories as $categorie)
                                            <option value="{{ $categorie->id }}">
                                                {{ $categorie->Libelle }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">La catégorie est obligatoire</div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label" for="produit">Produit</label>
                                    <select name="produit" type="text" class="form-select js-single"
                                        style="width: 100%" id="produit">
                                        {{-- <option value="">Sélectionnez un produit</option> --}}
                                        <option value="Tous">Tous</option>
                                        @foreach ($produits as $produit)
                                            <option value="{{ $produit->id }}">
                                                {{ $produit->Designation }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Le produit est obligatoire</div>
                                </div>
                                <div class="col-auto mb-5">
                                    <div class="btn-group mt-4">
                                        <div class="d-grid gap-2 d-md-block">
                                            @can('imprimer-impression-sortie-produit')
                                                <button type="submit" name="submit" value="PDF" class="btn text-white "
                                                    style="{{ background_color_1() }}">Imprimer PDF</button>
                                            @endcan

                                            @can('exporter-impression-sortie-produit')
                                                <button type="submit" name="submit" value="EXCEL" class="btn text-white "
                                                    style="{{ background_color_2() }}">Imprimer EXCEL</button>
                                            @endcan

                                        </div>
                                    </div>
                                </div>
                            </form>
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
        </style>
        <style>
            .selected>td {
                background-color: rgb(29, 9, 101);
                /* Ou la couleur de votre choix */
                color: white;
                /* Couleur du texte sur fond bleu */
            }

            .tableInfo {
                border-collapse: collapse;
                border: 1px solid #ddd;
                width: 100%;
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
                border: 1px solid #ddd;
                width: 100%;
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


                $('#proformaTable').on('click', '.clickable-row', function() {
                    var proformaId = $(this).data('url').split('/').pop();
                    // console.log("ID du proforma cliqué :", proformaId);
                    var editProformaUrl = "{{ route('editProforma', ['id' => ':proformaId']) }}";
                    var dupliquerProformaUrl = "{{ route('dupliquerProforma', ['id' => ':proformaId']) }}";
                    var conversionProformaUrl = "{{ route('conversionProforma', ['id' => ':proformaId']) }}";
                    var pdfProformaUrl = "{{ route('PDFProformaA4', ['id' => ':proformaId']) }}";
                    editProformaUrl = editProformaUrl.replace(':proformaId', proformaId);
                    dupliquerProformaUrl = dupliquerProformaUrl.replace(':proformaId', proformaId);
                    conversionProformaUrl = conversionProformaUrl.replace(':proformaId', proformaId);
                    pdfProformaUrl = pdfProformaUrl.replace(':proformaId', proformaId);
                    $('#modifierProformaLink').attr('href', editProformaUrl);
                    $('#dupliquerProformaLink').attr('href', dupliquerProformaUrl);
                    $('#conversionProformaLink').attr('href', conversionProformaUrl);
                    $('#pdfProformaLink').attr('href', pdfProformaUrl);
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
