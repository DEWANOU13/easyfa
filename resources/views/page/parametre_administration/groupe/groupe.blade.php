@extends('layouts.master', ['title' => 'Groupe'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Groupe',
        'infos2' => 'Groupe',
        'infos3' => 'Liste',
    ])

    <section>
        <div class="row">
            <div class="col-12 mb-5 mt-3">
                <div class="d-flex flex-row-reverse bd-highlight">
                    @canany(['creer-groupe', 'modifier-groupe'])
                        <div class="dropdown mb-2">
                            <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false" style="{{ background_color_1() }}">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                @can('creer-groupe')
                                    <li><a href="{{ route('groupe.create') }}" class="dropdown-item" type="button">Nouveau</a></li>
                                @endcan
                                @can('modifier-groupe')
                                    <li><a id="modifierLink" class="dropdown-item" type="button">Modifier</a></li>
                                @endcan
                            </ul>
                        </div>
                    @endcanany
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des Groupes</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="groupeTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <td>#</td>
                                        <th>Nom du groupe</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $count = 0; @endphp
                                    @forelse ($groupes as $groupe)
                                        <tr class="clickable-row" data-id="{{ $groupe->id }}">
                                            <td scope="col">{{ ++$count }}</td>
                                            <td scope="col">{{ $groupe->nom_groupe }}</td>
                                            <td scope="col">{{ $groupe->description }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td scope="row" colspan="4" class="text-center">Aucun groupe trouvé</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#groupeTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('id');
                // console.log(ID);

                // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                var modifierUrl = "{{ route('groupe.edit', ['groupe' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                // Mettre à jour l'attribut href du lien
                $('#modifierLink').attr('href', modifierUrl);
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
@endSection
