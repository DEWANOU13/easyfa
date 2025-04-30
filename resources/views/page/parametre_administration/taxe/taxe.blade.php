@extends('layouts.master', ['title' => 'Parametre'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Paramètres taxe',
        'infos2' => 'taxe',
        'infos3' => 'liste',
    ])

<section>
    <div class="row">
        <div class="col-12 mb-5 mt-3">
            <div class="d-flex flex-row-reverse bd-highlight">
                @canany(['creer-taxe', 'modifier-taxe'])
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">
                            @can('creer-taxe')
                                <li><a href="{{ route('taxeNouveau') }}" class="dropdown-item"
                                        type="button">Nouveau</a></li>
                            @endcan
                            @can('modifier-taxe')
                                <li><a id="modifierLink" class="dropdown-item" type="button">Modifier</a></li>
                            @endcan
                        </ul>
                    </div>
                @endcanany
            </div>
            <div class="card m-b-30">
                <div class="card-header" style="{{ background_color_2() }}">
                    <h3 class="mt-2  d-inline-block text-dark">Liste des taxes </h3>
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
                                    <th>Unité de comptage</th>
                                    <!-- <th>Enregisté par</th> -->
                                    <th style="width: 5%">Statut</th>
                                </tr>
                            </thead>
                         @foreach ($produits as $key => $produit)
                                <tr class="clickable-row " data-url="{{ route('edit.produit', $produit->id) }}">
                                    <td>{{ $produit->Reference }}</td>
                                    <td>{{ $produit->Designation }}</td>
                                    <td>{{ $produit->Type }}</td>
                                    <td>{{ $produit->Libelle_categorie_produit }}</td>
                                    <td>{{ $produit->Libelle }}</td>
                                    <!-- <td>{{ $produit->name }}</td> -->
                                    <td>
                                        @if ($produit->Statut == 1)
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
                            <tr>
                                <td>Unité de comptage </td>
                                <td><span id="Libelle"></span></td>
                            </tr>
                            <tr>
                                <td>Enregisté par</td>
                                <td><span id="name"></span></td>
                            </tr>
                            {{-- <tr>
                <td>Enregistrer le</td>
                <td><span id="m_enreg"></span></td>
              </tr>
              <tr>
                <td>Modifier le</td>
                <td><span id="m_modif"></span></td>
              </tr> --}}
                            <tr>
                                <td>Statut</td>
                                <td><span id="m_Statut"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" id="importFieldset">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#ProduitsTable').on('click', '.clickable-row', function() {
                var reference = $(this).find('td:eq(0)').text();
                var designation = $(this).find('td:eq(1)').text();
                var type = $(this).find('td:eq(2)').text();
                var categorie = $(this).find('td:eq(3)').text();
                var unite = $(this).find('td:eq(4)').text();
                var enregistre_par = $(this).find('td:eq(5)').text();
                var statut = $(this).find('td:eq(6)').html();

                $('#m_id_fr').text(designation);
                $('#Reference').text(reference);
                $('#Designation').text(designation);
                $('#Type').text(type);
                $('#Libelle_categorie_produit').text(categorie);
                $('#Libelle').text(unite);
                $('#name').text(enregistre_par);
                $('#m_Statut').html(statut);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#ProduitsTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                var fournisseur = $(this).data('produit');
                // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                var modifierUrl = "{{ route('editTaxe', ['id' => ':ID']) }}";
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


@include('layouts.alert')
@endSection
