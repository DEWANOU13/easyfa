@extends('layouts.master', ['title' => 'Seuil de stock Produit'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Seuil du stock',
        'infos2' => 'Seuil du stock',
        'infos3' => 'Liste',
    ])

    <section>
        {{-- @canany(['creer-groupe-categorie-produit', 'modifier-groupe-categorie-produit'])
            @can('creer-groupe-categorie-produit') --}}
        <div class="col-md-{{ Auth::user()->cannot('modifier-groupe-categorie-produit') ? 12 : 4 }}">
        </div>

        <div class="row">
            <div class="col-md-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @if (isset($choix_seuil) && $choix_seuil->libelle == 'CATEGORIE')
                            <button class="nav-link @if ($value === 'categorie') active @endif  " id="nav-home-tab"
                                data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab"
                                aria-controls="nav-home" aria-selected="true">Seuil
                                Catégorie</button>
                        @endif

                        @if (isset($choix_seuil) && $choix_seuil->libelle == 'PRODUIT')
                            <button class="nav-link @if ($value === 'categorie') active @endif " id="nav-profile-tab"
                                data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab"
                                aria-controls="nav-profile" aria-selected="false">Seuil
                                Produit</button>
                        @endif



                    </div>
                </nav><br>
                <div class="tab-content" id="nav-tabContent">
                    @if (isset($choix_seuil) && $choix_seuil->libelle == 'CATEGORIE')
                        <div class="tab-pane fade @if ($value === 'categorie') show active @endif " id="nav-home"
                            role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                            <div class="col-md-12">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <fieldset class="border p-3 rounded-3">
                                            <legend class="float-none w-auto px-1">Definir le Seuil du stock</legend>
                                            <form class="row d-flex align-items-center justify-content-center "
                                                class="clickable-row"
                                                action="{{ isset($edit_seuil) && is_object($edit_seuil) ? route('updateSeuilStock', $edit_seuil->id) : route('storeSeuilStock') }}"
                                                method="POST">
                                                @csrf
                                                @if (isset($edit_seuil))
                                                    @method('PUT')
                                                @endif
                                                <div class="col-md-7 form-group">
                                                    <label for="categorie_produit_id" class="label-form">Catégorie
                                                        Produit:&nbsp;</label>
                                                    @if (isset($edit_seuil))
                                                        <select name="categorie_produit_id[]" class="js-single"
                                                            style="width: 100%" id="categorie_produit_id" required>
                                                            <option value="{{ $edit_seuil->id }}">
                                                                {{ $edit_seuil->Libelle }}</option>
                                                            @foreach ($listeCategorie as $categorieProduit)
                                                                <option value="{{ $categorieProduit->id }}">
                                                                    {{ $categorieProduit->Libelle }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select name="categorie_produit_id[]" class="js-single"
                                                            style="width: 100%" id="categorie_produit_id" multiple required>

                                                            <option value="">
                                                            </option>

                                                            @foreach ($listeCategorie as $categorieProduit)
                                                                <option value="{{ $categorieProduit->id }}">
                                                                    {{ $categorieProduit->Libelle }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif

                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label for="searchInput3" class="form-label fw-bold">Seuil
                                                        Limite</label>
                                                    <input class="form-control lg" type="number" name="quantity_seuil"
                                                        value="{{ isset($edit_seuil) ? $edit_seuil->seuil_stock : '' }}"
                                                        id="quantity_seuil">
                                                </div>

                                                @if (isset($edit_seuil))
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_2() }}">Modifier</button>
                                                    </div>
                                                @else
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_1() }}">Appliquer</button>
                                                    </div>
                                                @endif


                                            </form>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex text-start p-3">
                                <div class="col text-end">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle text-white" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="{{ background_color_1() }}">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" style="z-index: 2000;">
                                            <form action="{{ route('edit_seuil') }}" method="POST">
                                                @csrf
                                                <input  hidden id="idc" name="id" type="number" value="">
                                                <li id="modifierBtn"><button type="submit"
                                                        class="dropdown-item">Modifier</button>
                                            </form>

                                            </li>
                                            <li>
                                                <form action="{{ route('restituer') }}" method="POST">
                                                    @csrf
                                                    <input hidden id="idrc" name="id" type="number" value="">
                                                    <button matTooltip="Acion non revocable" href=""
                                                        class="dropdown-item bg bg-danger text-light"
                                                        type="submit">Retirer</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card m-b-30">

                                    <div class="card-header" style="{{ background_color_2() }}">
                                        <h3 class="mt-2  d-inline-block text-dark">Liste seuls</h3>
                                    </div>
                                    <div class="card-body responsive-2">
                                        <div class="table-responsive">
                                            <table id="proformaTable"
                                                class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead class="table-primary">

                                                    <tr>
                                                        <th hidden scope="col"></th>
                                                        <th scope="col">Catégorie Produit</th>
                                                        <th scope="col">Seuil Limite</th>
                                                    </tr>
                                                    {{-- <th style="width: 5%">Statut</th> --}}
                                                </thead>
                                                <tbody>
                                                    @if (isset($seuil_stocks))
                                                        @forelse ($seuil_stocks as $stock)
                                                            {{-- <form action="{{ route('edit_seuil', $stock->id) }}"
                                                                    method="POST"> --}}
                                                            <tr class="clickable-rows cursor-pointer"
                                                                style="cursor: pointer;">
                                                                {{-- <td>{{ }}</td> --}}
                                                                <td hidden>{{ $stock->id }}</td>
                                                                <td>{{ $stock->Libelle }}</td>
                                                                <td>{{ $stock->seuil_stock }}</td>
                                                                {{-- <td>
                                                                    <a href="{{ route('edit_seuil', $stock->id) }}"
                                                                        class="btn btn-primary">Modifier</a>
                                                                </td> --}}
                                                            </tr>
                                                            {{-- </form> --}}
                                                        @empty
                                                            <tr>
                                                                <td colspan="2">Aucune données</td>
                                                            </tr>
                                                        @endforelse
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                    @if (isset($choix_seuil) && $choix_seuil->libelle == 'PRODUIT')
                        <div class="tab-pane fade @if ($value === 'categorie') show active @endif " id="nav-profile"
                            role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                            <div class="col-md-12">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <fieldset class="border p-3 rounded-3">
                                            <legend class="float-none w-auto px-1">Definir le Seuil du stock</legend>
                                            <form class="row d-flex align-items-center justify-content-center "
                                                class="clickable-row"
                                                action="{{ isset($edit_seuil_produit) && is_object($edit_seuil_produit) ? route('updateSeuilStockProduit', ['id' => $edit_seuil_produit->id]) : route('storeSeuilStockProduit') }}"
                                                method="POST">
                                                @csrf
                                                @if (isset($edit_seuil_produit))
                                                    @method('PUT')
                                                @endif
                                                <div class="col-md-7 form-group">
                                                    <label for="produit_id" class="label-form">
                                                        Produit:&nbsp;</label>
                                                    @if (isset($edit_seuil_produit))
                                                        <select name="produits[]" class="js-single" style="width: 100%"
                                                            id="produit_id" required>
                                                            <option value="{{ $edit_seuil_produit->id }}">
                                                                {{ $edit_seuil_produit->Designation }}</option>
                                                            @foreach ($produits as $produit)
                                                                <option value="{{ $produit->id }}">
                                                                    {{ $produit->Designation }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select name="produit_id[]" class="js-single" style="width: 100%"
                                                            id="produit_id" multiple required>
                                                            <option value="">
                                                            </option>
                                                            @foreach ($produits as $produit)
                                                                <option value="{{ $produit->id }}">
                                                                    {{ $produit->Designation }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label for="searchInput3" class="form-label fw-bold">Seuil
                                                        Limite</label>
                                                    <input class="form-control lg" type="number" name="quantity_seuil"
                                                        value="{{ isset($edit_seuil_produit) ? $edit_seuil_produit->seuil_stock : '' }}"
                                                        id="quantity_seuil">
                                                </div>

                                                @if (isset($edit_seuil_produit))
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_2() }}">Modifier</button>
                                                    </div>
                                                @else
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_1() }}">Appliquer</button>
                                                    </div>
                                                @endif
                                            </form>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <fieldset class="border p-3 rounded-3">
                                            <legend class="float-none w-auto px-1">Definir le Seuil pour tous les produits</legend>
                                            <form class="row d-flex align-items-center justify-content-center"
                                                class="clickable-row"
                                                action="{{route('seuil_produit_all') }}"
                                                method="POST">
                                                @csrf
                                                @if (isset($edit_seuil_produit))
                                                    @method('PUT')
                                                @endif
                                                <div class="col-sm-3 form-group">
                                                    <label for="searchInput3" class="form-label fw-bold">Seuil
                                                        Limite</label>
                                                    <input class="form-control lg" type="number" name="quantity_seuil"
                                                        value="{{ isset($edit_seuil_produit) ? $edit_seuil_produit->seuil_stock : '' }}"
                                                        id="quantity_seuil">
                                                </div>

                                                @if (isset($edit_seuil_produit))
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_2() }}">Modifier</button>
                                                    </div>
                                                @else
                                                    <div class="col-md-auto mt-2">
                                                        <button type="submit" class="btn text-white"
                                                            style="{{ background_color_1() }}">Appliquer</button>
                                                    </div>
                                                @endif
                                            </form>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex text-start p-3">
                                <div class="col text-end">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle text-white" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"
                                            style="{{ background_color_1() }}">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" style="z-index: 2000;">
                                            <form action="{{ route('edit_seuil_produit') }}" method="POST">
                                                @csrf
                                                <input hidden id="id" name="id" type="number" value="">
                                                <li id="modifierBtn"><button type="submit"
                                                        class="dropdown-item">Modifier</button>
                                            </form>

                                            </li>
                                            <li>
                                                <form action="{{ route('restituer_produit') }}" method="POST">
                                                    @csrf
                                                    <input hidden id="idr" name="id" type="number" value="">
                                                    <button matTooltip="Acion non revocable" href=""
                                                        class="dropdown-item bg bg-danger text-light"
                                                        type="submit">Retirer</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card m-b-30">
                                    <div class="card-header" style="{{ background_color_2() }}">
                                        <h3 class="mt-2  d-inline-block text-dark">Liste des seuls produits</h3>
                                    </div>
                                    <div class="card-body responsive-2">
                                        <div class="table-responsive">
                                            <table id="proformaTable"
                                                class="tableInfo datatable table table-striped table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th hidden scope="col"></th>
                                                        <th scope="col"> Produit</th>
                                                        <th scope="col">Seuil Limite</th>
                                                    </tr>
                                                    {{-- <th style="width: 5%">Statut</th> --}}
                                                </thead>
                                                <tbody>
                                                    @if (isset($seuil_stock_produits))
                                                        @forelse ($seuil_stock_produits as $seuil_stock_produit)
                                                            {{-- <form action="{{ route('edit_seuil', $stock->id) }}"
                                                                    method="POST"> --}}
                                                            <tr class="clickable-row cursor-pointer"
                                                                style="cursor: pointer;">
                                                                {{-- <td>{{ }}</td> --}}
                                                                <td hidden>{{ $seuil_stock_produit->id }}</td>
                                                                <td>{{ $seuil_stock_produit->Designation }}</td>
                                                                <td>{{ $seuil_stock_produit->seuil_stock }}</td>
                                                                {{-- <td>
                                                                    <a href="{{ route('edit_seuil_produit', $seuil_stock_produit->id) }}"
                                                                        class="btn btn-primary">Modifier</a>
                                                                </td> --}}
                                                            </tr>
                                                            {{-- </form> --}}
                                                        @empty
                                                            <tr>
                                                                <td colspan="2">Aucune données</td>
                                                            </tr>
                                                        @endforelse
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        {{-- @endcan --}}


        <!-- Modal -->
        @if (!isset($choix_seuil))
            <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Choix Seuil</h5>
                            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button> --}}
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('choix_seuil') }}" method="POST">
                                @csrf
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="reponse"
                                                value="CATEGORIE" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                CATEGORIE
                                            </label>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="reponse"
                                                value="PRODUIT" id="flexRadioDefault2" checked>
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                PRODUIT
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">VALIDER</button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        @endif








        {{-- <div class="row"> --}}

        {{-- </div> --}}
        {{-- @endcanany --}}
        <br>
    </section>

    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la background_color_1 de votre choix */
            color: white;
            /* background_color_1 du texte sur fond bleu */
        }
    </style>

    <script>
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });

        $(document).ready(function() {
            $('.clickable-rows').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });

        $(document).ready(function() {
            // Lorsqu'une ligne du tableau est cliquée, récupérer l'ID
            $('.clickable-row').on('click', function() {
                var id = $(this).find('td:first').text().trim();
                console.log("ID sélectionné: " + id);

                // Mettre à jour l'ID dans un attribut du bouton "Modifier"
                // $('#modifierBtn').data('id', id);
                $('#id').val(id);
                $('#idr').val(id);
            });

        });

        $(document).ready(function() {
            // Lorsqu'une ligne du tableau est cliquée, récupérer l'ID
            $('.clickable-rows').on('click', function() {
                var id = $(this).find('td:first').text().trim();
                console.log("ID sélectionné: " + id);

                // Mettre à jour l'ID dans un attribut du bouton "Modifier"
                // $('#modifierBtn').data('id', id);
                $('#idc').val(id);
                $('#idrc').val(id);
            });

        });

        // document.addEventListener("DOMContentLoaded", function() {
        //     var myModal = new bootstrap.Modal(document.getElementById('myModal'), {
        //         backdrop: 'static', // Le modal ne se ferme pas quand on clique en dehors
        //         keyboard: false // Le modal ne se ferme pas quand on appuie sur la touche Échap
        //     });
        //     myModal.show();
        // });

        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('myModal'));
            myModal.show();
        });
    </script>

    <script>
        const formIds = ['storeGroupeCategorie', 'form2', 'deleteSelectedForm'];

        // Fonction pour empêcher la soumission du formulaire à l'aide de la touche "Entrée"
        function preventEnterSubmission(formId) {
            document.getElementById(formId).addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }

        // Appliquer la fonction à chaque formulaire de la liste
        formIds.forEach(preventEnterSubmission);
    </script>
    <script>
        $('#storeGroupeCategorie').on('submit', function(e) {

            var $button = $('#save-button');
            $button.addClass('loading');
            $button.prop('disabled', true);

        });

        $('#form2').on('submit', function(e) {

            var $button = $('#saveButton');
            $button.addClass('loading');
            $button.prop('disabled', true);

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.checkbox-row');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        console.log('Checkbox checked:', this);
                        row.classList.add('selected-row');
                    } else {
                        console.log('Checkbox unchecked:', this);
                        row.classList.remove('selected-row');
                    }
                });
            });
        });
    </script>

    <style>
        .selected-row>td {
            background-color: rgb(29, 9, 101) !important;
            color: white;
        }
    </style>

    @include('layouts.alert')
@endsection
