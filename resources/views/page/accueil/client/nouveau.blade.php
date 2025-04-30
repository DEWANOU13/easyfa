@extends('layouts.master', ['title' => $client->exists ? 'Modifier Client' : 'Creer Client'])
@section('content')

    @include('layouts.partials.entete-page', [
        'infos1' => 'Client',
        'infos2' => 'Client',
        'infos3' => isset($client) ? 'Modification' : 'Nouveau',
    ])

    <style>
        .hover-pers:hover {
            background-color: #ddd;
        }
    </style>

    <section style="margin-bottom: 150px;">
        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="dropdown mb-2">
                <a href="{{ route('client') }}" class="btn text-white" style="{{ background_color_1() }}">
                    <i class="fa fa-reply" aria-hidden="true"></i>
                    Retour
                </a>
            </div>
        </div>

        <div class="card m-b-30">
            <div class="card-header rounded" style="{{ background_color_2() }}">
                <h4 class="mt-2 text-dark">
                    @if (isset($fournisseur))
                        Modification de {{ $client->DenominationSociale }}
                    @else
                        Enregistrement d'un client
                    @endif
                </h4>
            </div>
        </div>

        <form id="formClient" class="my-5" style="margin-bottom:50px;"
            action="{{ isset($client->id) ? route('updateClient', $client->id) : route('storeClient') }}" method="POST">
            @csrf
            @if (isset($client->id))
                @method('PUT')
            @endif

            <div class="card m-b-30 mb-5">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Client</legend>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="validationTextarea" class="form-label fw-bold">Code client<span
                                        class="text-danger position-absolute" style="line-height: 1;">*</span></label>
                                <input type="text" name="Code_client"
                                    value="{{ old('Code_client', isset($client) ? $client->Code_client : '') }}"
                                    class="form-control" aria-label="file example" required
                                    @if ($client->exists) readonly @endif>
                                <div class="invalid-feedback">Le code est obligatoire</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationTextarea" class="form-label fw-bold">Dénomination sociale<span
                                        class="text-danger position-absolute" style="line-height: 1;">*</span></label>
                                <input type="text" name="Denomination_sociale"
                                    value="{{ old('Denomination_sociale', isset($client) ? $client->Denomination_sociale : '') }}"
                                    class="form-control" aria-label="file example" required>
                                <div class="invalid-feedback">La dénomination sociale est obligatoire</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validationTextarea" class="form-label fw-bold">Adresse</label>
                                    <input type="text" name="Adresse_client" maxlength="13"
                                        value="{{ isset($client) ? $client->Adresse_client : '' }}" class="form-control"
                                        aria-label="file example">
                                    {{-- <div class="invalid-feedback"></div> --}}
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Numero_ifu" class="form-label fw-bold">IFU</label>
                                    <input type="number" name="Numero_ifu" class="form-control" id="ifuInput"
                                        value="{{ old('Numero_ifu', isset($client) ? $client->Numero_ifu : '') }}">
                                    @error('Numero_ifu')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="validationTextarea" class="form-label fw-bold">Catégorie client<span
                                        class="text-danger">*</span></label>

                                <div class="input-group">
                                    <select class="form-select" required name="Categorie_client_id"
                                        id="inputGroupSelect04" aria-label="Example select with button addon">
                                        <option value="">Sélectionnez une catégorie</option>
                                        @if (isset($listeCategorieClient) && count($listeCategorieClient) > 0)
                                            @foreach ($listeCategorieClient as $categorieClient)
                                                <option value="{{ $categorieClient->id }}"
                                                    {{ isset($client) && $client->Categorie_client_id == $categorieClient->id ? 'selected' : '' }}>
                                                    {{ $categorieClient->Libelle }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button class="btn hover-pers" title="Créer une nouvelle categorie client"
                                        style="height: 28px; color: #0d6efd; border: 2px solid #0d6efd;" type="button"
                                        data-bs-toggle="modal" data-bs-target="#creeCategorie"><span
                                            style="position: relative; top: -3px">Créer</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -10px;"
                                            class="icon icon-tabler icon-tabler-circle-plus" width="20" height="24"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M9 12h6" />
                                            <path d="M12 9v6" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Obligatoire</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validationTextarea" class="form-label fw-bold">Pays<span
                                            class="text-danger position-absolute" style="line-height: 1;">*</span></label>

                                    <!-- Select menu rempli avec la liste des pays -->
                                    <select class="form-select " name="Pays"
                                        aria-label="Default select example" id="countrySelect" required>
                                        @if (isset($client) && $client->Pays != null)
                                            <option value="{{ $client->Pays }}">{{ $client->Pays }}</option>
                                        @endif
                                        <option></option>
                                    </select>
                                    <div class="invalid-feedback">Obligatoire</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="validationTextarea" class="form-label fw-bold">Téléphone fixe</label>
                                    <input type="text" name="Telephone_fixe"
                                        value="{{ isset($client) ? $client->Telephone_fixe : '' }}" class="form-control"
                                        aria-label="file example">
                                    <div class="invalid-feedback">La dénomination sociale est obligatoire</div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="validationTextarea" class="form-label fw-bold">Téléphone
                                        mobile
                                        {{-- <span class="text-danger position-absolute" style="line-height: 1;">*</span> --}}
                                    </label>
                                    <input type="text" name="Telephone_mobile"
                                        value="{{ isset($client) ? $client->Telephone_mobile : '' }}"
                                        class="form-control" aria-label="file example">
                                    <div class="invalid-feedback">Le telephone mobile est obligatoire</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="validationTextarea" class="form-label fw-bold">Adresse email</label>
                                    <input type="email" name="Adresse_mail"
                                        value="{{ isset($client) ? $client->Adresse_mail : '' }}" class="form-control"
                                        placeholder="name@gmail.com" aria-label="file example">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="validationTextarea" class="form-label fw-bold">Statut</label>

                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Statut_client"
                                            value="1" required id="flexRadioDefault2"
                                            {{ isset($client->id) && $client->Statut_client == 1 ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Actif
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="Statut_client"
                                            value="0" id="flexRadioDefault1"
                                            {{ isset($client->id) && $client->Statut_client == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Inactif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('client') }}" type="reset" class="btn btn-secondary"> Annuler </a>
                            <button type="button" id="saveButton" class="btn btn-primary" data-bs-toggle="modal"
                                disabled data-bs-target="#staticBackdrop">
                                Sauvegarder
                            </button>
                        </div>
                    </fieldset>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Voulez-vous vraiment sauvegarder ces informations ?
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                            <button type="submit" id="save-button" class="btn btn-success">Oui sauvegarder</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>


    <div class="modal fade" id="creeCategorie" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Création de catégorie client</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categorieForm" action="{{ route('storeCategorieClientR') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="validationTextarea" class="form-label fw-bold">Catégorie<span
                                    class="text-danger position-absolute" style="line-height: 1;">*</span></label>
                            <input type="text" name="Libelle" id="LibelleInput" class="form-control"
                                aria-label="file example" required>
                            <div class="invalid-feedback">Le nom est obligatoire</div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-primary" type="submit">Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.querySelector('[name="Categorie_client_id"]').addEventListener('change', function() {
            if (this.value === 'nouvelle') {
                window.location.href = "{{ route('ShowFormCategorieClient') }}";
            }
        });
    </script>
    <script>
        $('#formClient').on('submit', function(e) {

            var $button = $('#save-button');
            $button.addClass('loading');
            $button.prop('disabled', true);

        });
    </script>

    <script>
        document.getElementById('LibelleInput').addEventListener('input', function() {
            var value = this.value.toUpperCase();
            this.value = value;
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#categorieForm').on('submit', function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {

                            alert('Catégorie ajoutée avec succès');

                            $('#creeCategorie').modal('hide');

                            $('#categorieForm')[0].reset();

                            // Ajoutez le nouvel élément au <select>
                            $('#inputGroupSelect04').append(new Option(response.newCategoryName,
                                response.newCategoryId));

                            $('#inputGroupSelect04').val(response.newCategoryId);
                        } else {
                            alert('Erreur lors de l\'ajout de la catégorie');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Erreur lors de l\'ajout de la catégorie');
                    }
                });
            });
        });
    </script>


    <!-- Script pour remplir le select avec les pays -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const countries = [
                "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola",
                "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche",
                "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin",
                "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil",
                "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada", "Cap-Vert",
                "République centrafricaine", "Chili", "Chine", "Chypre", "Colombie", "Comores",
                "République du Congo", "République démocratique du Congo", "Îles Cook", "Corée du Nord",
                "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti",
                "République dominicaine", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée",
                "Espagne", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon",
                "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau",
                "Guinée équatoriale", "Guyana", "Haïti", "Honduras", "Hongrie", "Inde", "Indonésie", "Irak",
                "Iran", "Irlande", "Islande", "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan",
                "Kenya", "Kirghizistan", "Kiribati", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban",
                "Liberia", "Libye", "Liechtenstein", "Lituanie", "Luxembourg", "Macédoine", "Madagascar",
                "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Îles Marshall", "Maurice",
                "Mauritanie", "Mexique", "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro",
                "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", "Niger", "Nigeria", "Niue", "Norvège",
                "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Palestine",
                "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", "Philippines",
                "Pologne", "Portugal", "Qatar", "Roumanie", "Royaume-Uni", "Russie", "Rwanda",
                "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Marin", "Saint-Vincent-et-les Grenadines",
                "Salomon", "Salvador", "Samoa", "São Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles",
                "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud",
                "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", "Eswatini", "Tadjikistan", "Tanzanie",
                "Tchad", "République tchèque", "Thaïlande", "Timor-Oriental", "Togo", "Tonga",
                "Trinité-et-Tobago", "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay",
                "Vanuatu", "Vatican", "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
            ];

            const select = document.getElementById('countrySelect');
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country;
                option.text = country;
                select.appendChild(option);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formClient');
    const saveButton = document.getElementById('saveButton');
    const codeClient = document.querySelector('input[name="Code_client"]');
    const denominationSociale = document.querySelector('input[name="Denomination_sociale"]');
    const categorieClient = document.querySelector('select[name="Categorie_client_id"]');
    const pays = document.querySelector('select[name="Pays"]');
    const numeroIfu = document.querySelector('input[name="Numero_ifu"]');

    // Initialiser select2 pour les champs Pays et Categorie_client_id
    $(categorieClient).select2();
    $(pays).select2();

    // Fonction pour valider le formulaire
    function validateForm() {
        const isCodeClientValid = codeClient.value.trim() !== '';
        const isDenominationSocialeValid = denominationSociale.value.trim() !== '';
        const isCategorieClientValid = categorieClient.value !== '';
        const isPaysValid = pays.value !== '';
        const isNumeroIfuValid = numeroIfu.value.length === 0 || (numeroIfu.value.length === 13 && /^\d+$/.test(numeroIfu.value));

        if (isCodeClientValid && isDenominationSocialeValid && isCategorieClientValid && isPaysValid && isNumeroIfuValid) {
            saveButton.disabled = false;
        } else {
            saveButton.disabled = true;
        }
    }

    // Écouter les événements de modification pour les champs standards
    codeClient.addEventListener('input', validateForm);
    denominationSociale.addEventListener('input', validateForm);
    numeroIfu.addEventListener('input', validateForm);

    // Écouter les événements de modification pour les champs select2
    $(categorieClient).on('change', validateForm);
    $(pays).on('change', validateForm);

    // Valider le formulaire au chargement initial
    validateForm();
});

    </script>



    @include('layouts.alert')
@endSection
