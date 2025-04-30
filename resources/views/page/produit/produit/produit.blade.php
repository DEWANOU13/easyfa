@extends('layouts.master', ['title' => 'Produit'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Produit',
        'infos2' => 'Produit',
        'infos3' => 'Liste',
    ])
    <section>
        <div class="row">
            <div class="col-12 mb-5 mt-3">
                <div class="d-flex flex-row-reverse bd-highlight">
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-produit')
                                <li><a href="{{ route('page.nouveau.nouveau') }}" class="dropdown-item"
                                        type="button">Nouveau</a></li>
                            @endcan
                            @can('modifier-produit')
                                <li><a id="modifierLink" class="dropdown-item" type="button">Modifier</a></li>
                            @endcan
                            <li><a id="detailLink" class="dropdown-item" type="button" data-bs-target="#showProduit"
                                    data-bs-toggle="modal">Détail</a></li>
                            @can('exporter-produits')
                                <li>
                                    <form action="{{ route('exportProduit') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item" type="submit">Exporter</button>
                                    </form>
                                </li>
                            @endcan
                            @can('importer-produits-actifs')
                                <li>
                                    <form action="{{ route('imprimer-produit') }}" method="POST" target="_blank">
                                        @csrf
                                        <input hidden type="text" value="ACTIF" name="reponse">
                                        <button class="dropdown-item" type="submit">Imprimer des produits actif</button>
                                    </form>
                                </li>
                            @endcan
                            @can('importer-produits-inactifs')
                                <li>
                                    <form action="{{ route('imprimer-produit') }}" method="POST">
                                        @csrf
                                        <input hidden type="text" value="INACTIF" name="reponse">
                                        <button class="dropdown-item" type="submit">Imprimer des produits inactif</button>
                                    </form>
                                </li>
                            @endcan
                            @can('imprimer-prestations-actives')
                                <li>
                                    <form action="{{ route('imprimer-produit') }}" method="POST">
                                        @csrf
                                        <input name="reponse" type="hidden" value="PRESTATION">
                                        <button type="submit" class="dropdown-item" value="in">
                                            Imprimer des prestations actives
                                        </button>
                                    </form>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </div>
                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des Produits </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ProduitsTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Type</th>
                                        <th>Catégorie</th>
                                        @if (emballageActiver())
                                            <th>Emballage</th>
                                            <th>Type Emballage</th>
                                        @endif
                                        <th>Unité de comptage</th>
                                         <th>Enregisté par</th>
                                         <th>Modifier par</th>
                                        <th style="width: 5%">Statut</th>
                                    </tr>
                                </thead>
                                @foreach ($produits as $key => $produit)
                                    <tr class="clickable-row " data-url="{{ route('edit.produit', $produit->id) }}">
                                        <td>{{ $produit->Reference }}</td>
                                        <td>{{ $produit->Designation }}</td>
                                        <td>{{ $produit->Type }}</td>
                                        <td>{{ $produit->Libelle_categorie_produit }}</td>
                                        @if (emballageActiver())
                                            <td>{{ $produit->emballage }}</td>
                                            <td>{{ $produit->type_emballage }}</td>
                                        @endif
                                        <td>{{ $produit->Libelle }}</td>
                                        <td>{{ $produit->name }}</td>
                                        <td>{{ $produit->_name }}</td>
                                        <td>
                                            @if ($produit->Statut == 'INACTIF')
                                                <i class="fa fa-check-circle text-danger" aria-hidden="true"></i>
                                            @else
                                                <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div wire:ignore.self class="modal fade" id="showProduit" aria-hidden="true" aria-labelledby="showProduitLabel2"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Information produit <span id="m_id_fr"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-inverse">
                            <tbody>
                                <tr>
                                    <td>Reference</td>
                                    <td><span id="Reference"></span></td>
                                </tr>
                                <tr>
                                    <td>Designation</td>
                                    <td><span id="Designation"></span></td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td><span id="Type"></span></td>
                                </tr>
                                <tr>
                                    <td>Categorie</td>
                                    <td><span id="Libelle_categorie_produit"></span></td>
                                </tr>

                                @if (emballageActiver())
                                    <tr>
                                        <td>Emballage</td>
                                        <td><span id="emballage"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Type Emballage</td>
                                        <td><span id="type_emballage"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Unité de comptage </td>
                                        <td><span id="Libelle"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Enregisté par</td>
                                        <td><span id="name"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Modfier par</td>
                                        <td><span id="modifier_par"></span></td>
                                    </tr>

                                    <tr>
                                        <td>Statut</td>
                                        <td><span id="m_Statut"></span></td>
                                    </tr>
                                    @else

                                    <tr>
                                        <td>Unité de comptage </td>
                                        <td><span id="Libelle"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Enregisté par</td>
                                        <td><span id="name"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Modfier par</td>
                                        <td><span id="modifier_par"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Statut</td>
                                        <td><span id="m_Statut"></span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer" id="importFieldset">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <input hidden type="text" id="emballageActiver" value="{{ emballageActiver() }}">

        <script>
            $(document).ready(function() {

                 var emballageActiver= $('#emballageActiver').val();
                 console.log('eeee',emballageActiver)

                $('#ProduitsTable').on('click', '.clickable-row', function() {
                    var reference = $(this).find('td:eq(0)').text();
                    var designation = $(this).find('td:eq(1)').text();
                    var type = $(this).find('td:eq(2)').text();
                    var categorie = $(this).find('td:eq(3)').text();
                    if (emballageActiver == 1) {
                        var emballage = $(this).find('td:eq(4)').text();
                        var type_emballage = $(this).find('td:eq(5)').text();
                        var unite = $(this).find('td:eq(6)').text();
                        var enregistre_par = $(this).find('td:eq(7)').text();
                        var modifier_par = $(this).find('td:eq(8)').text();
                        var statut = $(this).find('td:eq(9)').html();
                    }else{

                        var unite = $(this).find('td:eq(4)').text();
                        var enregistre_par = $(this).find('td:eq(5)').text();
                        var modifier_par = $(this).find('td:eq(6)').text();
                        var statut = $(this).find('td:eq(7)').html();
                    }

                    $('#m_id_fr').text(designation);
                    $('#Reference').text(reference);
                    $('#Designation').text(designation);
                    $('#Type').text(type);
                    $('#Libelle_categorie_produit').text(categorie);
                    if (emballageActiver == 1) {
                        $('#emballage').text(emballage);
                        $('#type_emballage').text(type_emballage);
                        $('#Libelle').text(unite);
                        $('#name').text(enregistre_par);
                        $('#modifier_par').text(modifier_par);
                        $('#m_Statut').html(statut);


                    }else{

                        $('#Libelle').text(unite);
                        $('#name').text(enregistre_par);
                        $('#modifier_par').text(modifier_par);
                        $('#m_Statut').html(statut);
                    }
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#ProduitsTable').on('click', '.clickable-row', function() {
                    var ID = $(this).data('url').split('/').pop();
                    var fournisseur = $(this).data('produit');
                    // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                    var modifierUrl = "{{ route('edit.produit', ['id' => ':ID']) }}";
                    modifierUrl = modifierUrl.replace(':ID', ID);
                    // Mettre à jour l'attribut href du lien
                    $('#modifierLink').attr('href', modifierUrl);
                    $('#m_id_fr').html(produit['id']);
                    $('#Reference').html(produit['Reference']);
                    $('#Designation').html(produit['Designation']);
                    $('#Type').html(produit['Type']);
                    $('#Libelle_categorie_produit').html(produit['Libelle_categorie_produit']);
                    $('#m_enreg').html(produit['created_at']);
                    $('#m_modif').html(produit['updated_at']);

                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.clickable-row').on('click', function() {
                    // Supprimez la classe 'selected' de toutes les lignes
                    $('.clickable-row').removeClass('selected');
                    // Ajoutez la classe 'selected' à la ligne cliquée
                    $(this).addClass('selected');
                });
            });
        </script>
        <style>
            .selected>td {
                background-color: rgb(29, 9, 101);
                /* Ou la couleur de votre choix */
                color: white;
                /* Couleur du texte sur fond bleu */
            }
        </style>

        @include('layouts.alert')
    </section>
@endsection
