<section>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Liste</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Fixation de
                prix</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
            tabindex="0">
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Filtre</legend>
                        <div class="row">
                            {{-- <div class="col-md-12"> --}}
                                <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center"
                                    method="post" action="{{ route('filterListe.gestion-prix') }}">
                                    @csrf
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold">Produit</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="produit" type="text" class="form-select" id="">
                                                <option value="">Sélectionnez un produit</option>
                                                @foreach ($produits as $key => $value)
                                                    <option value="{{ $value->Reference }}"
                                                        {{ old('produit') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->Designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold" id="inputGroup-sizing-sm">Catégorie
                                            client</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="categorie_client" type="text" class="form-select"
                                                id="">
                                                <option value="">Sélectionnez une catégorie</option>
                                                @foreach ($categorie_client as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ old('categorie_client') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->Libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold" id="inputGroup-sizing-sm">Agence</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="agence" type="text" class="form-select" id="">
                                                <option value="">Sélectionnez</option>
                                                @foreach ($agence as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->NomAgence }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button style="{{ background_color_1() }}" type="submit"
                                                    class="btn text-white w-100 mt-4">Appliquer</button>
                                                {{-- <button type="button"
                                                    class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Imprimer</a></li>
                                                </ul> --}}
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            {{-- </div> --}}
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-5 mt-3">
                    <div class="card m-b-30" wire:ignore>
                        <div class="card-header" style="{{ background_color_2() }}">
                            <h3 class="mt-2  d-inline-block text-dark">Liste des prix fixés</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="magasinsTable"
                                    class="datatable table table-striped table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">
                                                Référence
                                            </th>
                                            <th scope="col">
                                                Désignation</th>
                                            <th scope="col">
                                                Catégorie
                                            </th>
                                            <th scope="col">Agence
                                            </th>
                                            <th scope="col">
                                                Catégorie
                                                par client</th>
                                            <th scope="col">Prix de
                                                vente HT</th>
                                        </tr>
                                    </thead>

                                    @forelse($historique_prix_ventes as $historique_prix_vente)
                                        <tr style="cursor:pointer" class="clickable-row"
                                            data-url="{{ route('get.entrer_produit_for_entree_produit', ['id' => $historique_prix_vente->id]) }}">
                                            <td class="entree-produit">{{ $historique_prix_vente->Reference }}</td>
                                            <td class="entree-produit">{{ $historique_prix_vente->Designation }}</td>
                                            <td class="entree-produit">{{ $historique_prix_vente->Libelle_Categorie }}
                                            </td>
                                            <td class="entree-produit">{{ $historique_prix_vente->NomAgence }}</td>
                                            <td class="entree-produit">{{ $historique_prix_vente->Libelle }}</td>
                                            <td class="entree-produit">{{ $historique_prix_vente->prix }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <h6 class=" mt-3 text-center">Aucune donnée pour l'instant.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
            tabindex="0">
            <div class="col-sm-1">
                <div class="input-group input-group-sm mb-3">
                    {{-- <span class="input-group-text" id="designation">Désignation</span> --}}
                    <input style="width: 2px" type="text" name="" class="form-control designation-input"
                        value="{{ old('designation') }}" id="designation" readonly>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="input-group input-group-sm mb-3">
                    {{-- <span class="input-group-text" id="designation">Désignation</span> --}}
                    <input style="width: 2px" type="text" name="" class="form-control prix-actuel-input"
                        value="{{ old('prix_actuel') }}" id="prix_actuel" readonly>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="input-group input-group-sm mb-3">
                    {{-- <span class="input-group-text" id="designation">Désignation</span> --}}
                    <input style="width: 2px" type="text" name="" class="form-control nouveau-prix-input"
                        value="{{ old('nouveau_prix') }}" id="nouveau_prix" readonly>
                </div>
            </div>
            <form class="row gx-3 gy-2 d-flex align-items-center justify-content-center "
                action="{{ route('store.gestion-prix') }}" method='POST'>
                @csrf
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <div class="row">
                                {{-- <div class="col-md-12"> --}}

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold" id="inputGroup-sizing-sm">Produit</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="" type="text" class="form-select produit-select"
                                                id="produit" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un produit</option>
                                                @foreach ($produits as $key => $value)
                                                    <option value="{{ $value->Reference }}"
                                                        {{ old('produit') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->Designation }}
                                                    </option>
                                                @endforeach
                                                {{-- @if ($historique_prix_revients)
                                                    @foreach ($historique_prix_revients as $value)
                                                        <option value="{{ $value->Reference }}">
                                                            {{ $value->Reference }}
                                                        </option>
                                                    @endforeach
                                                @endif --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold" id="inputGroup-sizing-sm">Catégorie
                                            client</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="" type="text" class="form-select"
                                                id="categorie_client" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez une catégorie</option>
                                                @foreach ($categorie_client as $key => $value)
                                                    <option value="{{ $value->id }}-{{ $value->Libelle }}"
                                                        {{ old('categorie_client') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->Libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-bold"id="inputGroup-sizing-sm">Agence</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="" type="text" class="form-select" id="agence"
                                                aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez</option>
                                                @foreach ($agence as $key => $value)
                                                    <option value="{{ $value->id }}-{{ $value->NomAgence }}"
                                                        {{ old('agence') == $value->id ? 'selected' : '' }}>
                                                        {{ $value->NomAgence }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="button" id="add" name="add"
                                                    class="btn text-white w-100 mt-4"
                                                    style="{{ background_color_1() }}">Afficher</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                @can('creer-prix-vente')
                                                    <button type="button" data-bs-toggle="modal"
                                                        data-bs-target="#gestionPrix" id="bouton-valider"
                                                        class="btn text-white w-100 mt-4"
                                                        style="{{ background_color_1() }}">Appliquer</button>
                                                @endcan
                                                {{-- <button type="button"
                                                    class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Imprimer</a></li>
                                                </ul> --}}
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                            </div>

                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-5 mt-3">
                        <div class="card m-b-30" wire:ignore>
                            <div class="card-header" style="{{ background_color_2() }}">
                                <h3 class="mt-2  d-inline-block text-dark">Fixation de prix</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="table2"
                                        class=" table table-striped table-bordered dt-responsive nowrap magasinsTable"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">
                                                    Référence</th>
                                                <th scope="col">
                                                    Désignation</th>
                                                <th scope="col">
                                                    Agence
                                                </th>
                                                <th scope="col">
                                                    Catégorie client</th>
                                                <th scope="col">
                                                    Prix de
                                                    vente actuel</th>
                                                <th scope="col">
                                                    Nouveau
                                                    prix</th>
                                                <th>

                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
                <!-- Button trigger modal -->

                <!-- Modal -->
                <div class="modal fade" id="gestionPrix" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de
                                    confirmation
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment sauvegarder ces informations ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button type="submit" class="btn btn-primary">Oui
                                    sauvegarder</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    </div>
    </div>
    @include('livewire.categorie-produit.modal')
</section>
