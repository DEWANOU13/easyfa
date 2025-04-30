@extends('layouts.master', ['title' => 'Importation de fichiers'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Importation de fichiers',
        'infos2' => 'Paramètres',
        'infos3' => 'Importation',
    ])

    <style>
        .form-check {
            margin-bottom: 0;
            /* Assure que le label est aligné verticalement avec l'input */
        }

        #uploadButton {
            white-space: nowrap;
            /* Pour éviter que le texte du bouton ne se divise en plusieurs lignes */
        }

        @media(max-width:576px) {
            .btn_delete {
                margin-top: 3px;
            }
        }

        /* Barre de progression principale */
        .progress {
            height: 30px;
            /* Hauteur de la barre de progression */
            margin-bottom: 20px;
            /* Marge inférieure pour espacement */
            overflow: hidden;
            /* Masquer le débordement */
            background-color: #f3f3f3;
            /* Couleur de fond */
            border-radius: 5px;
            /* Coins arrondis */
        }

        /* Barre de progression remplie */
        .progress-bar {
            background-color: #4caf50;
            /* Couleur de la barre de progression */
            width: 0%;
            /* Largeur initialisée à zéro */
            color: white;
            /* Couleur du texte à l'intérieur de la barre */
            text-align: center;
            /* Centrer le texte */
            line-height: 30px;
            /* Hauteur de ligne */
        }
    </style>
    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark d-fex">
                Cet assistant vous aidera à importer vos données dans votre application
            </h4>
        </div>
    </div>
    <div class="card m-b-30 sectionCard" id="card1">
        <div class="card-body">
            <fieldset class="border p-3 rounded-3">
                <legend class="float-none w-auto px-1">Etape 1</legend>
                <div class="row" id="downloadFieldset">
                    <!-- <h5 class="mb-3">1 - Fermer tous les fichiers Excels ouverts.</h5> -->
                    <h5 class="mb-3">1 - Choisir le type de fichier à importer.</h5>
                    <div class="ps-md-5 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault1"
                                data-url="/files/modeles_importation/modele_fournisseurs.xlsx"
                                route-soumission="{{ route('importFournisseurs') }}">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Fichier des Fournisseurs
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault2"
                                data-url="/files/modeles_importation/modele_clients.xlsx"
                                route-soumission="{{ route('importClients') }}">
                            <label class="form-check-label" for="flexRadioDefault2">
                                Fichier des Clients
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault3"
                                data-url="/files/modeles_importation/modele_produits.xlsx"
                                route-soumission="{{ route('importProduits') }}">
                            <label class="form-check-label" for="flexRadioDefault3">
                                Fichier des Produits
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault4"
                                data-url="/files/modeles_importation/modele_prix_produits.xlsx"
                                route-soumission="{{ route('importPrixProduits') }}">
                            <label class="form-check-label" for="flexRadioDefault4">
                                Fichier des prix des Produits
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault9"
                                data-url="/files/modeles_importation/modele_agence.xlsx"
                                route-soumission="{{ route('importAgences') }}">
                            <label class="form-check-label" for="flexRadioDefault9">
                                Fichier des agences
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault5"
                                data-url="/files/modeles_importation/modele_magasins.xlsx"
                                route-soumission="{{ route('importMagasins') }}">
                            <label class="form-check-label" for="flexRadioDefault5">
                                Fichier des magasins
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault6"
                                data-url="/files/modeles_importation/modele_categorie_produits.xlsx"
                                route-soumission="{{ route('importCategorieProduits') }}">
                            <label class="form-check-label" for="flexRadioDefault6">
                                Fichier des catégories produits
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault7"
                                data-url="/files/modeles_importation/modele_categorie_clients.xlsx"
                                route-soumission="{{ route('importCategorieClients') }}">
                            <label class="form-check-label" for="flexRadioDefault7">
                                Fichier des catégories clients
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault8"
                                data-url="/files/modeles_importation/modele_unite_comptages.xlsx"
                                route-soumission="{{ route('importUniteComptages') }}">
                            <label class="form-check-label" for="flexRadioDefault8">
                                Fichier des unités de comptages
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault10"
                                data-url="/files/modeles_importation/modele_entree_emballage.xlsx"
                                route-soumission="">
                            <label class="form-check-label" for="flexRadioDefault10">
                                Fichier des entrées emballages
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault11"
                                data-url="/files/modeles_importation/modele_stock_emballage.xlsx"
                                route-soumission="">
                            <label class="form-check-label" for="flexRadioDefault11">
                                Fichier stock importation emballage
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault12"
                                data-url="/files/modeles_importation/modele_categorie_emballages.xlsx"
                                route-soumission="{{ route('importCategorieEmballages') }}">
                            <label class="form-check-label" for="flexRadioDefault12">
                                Fichier des catégories emballages
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault13"
                                data-url="/files/modeles_importation/modele_emballages.xlsx"
                                route-soumission="{{ route('importEmballages') }}">
                            <label class="form-check-label" for="flexRadioDefault13">
                                Fichier des emballages
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="Typefichier" id="flexRadioDefault14"
                                data-url="/files/modeles_importation/modele_entreee_produits.xlsx"
                                route-soumission="{{ route('importEmballages') }}">
                            <label class="form-check-label" for="flexRadioDefault14">
                                Fichier des entrées de produit
                            </label>
                        </div>
                    </div>
                    <h5 class="mb-3">2 - Prendre le modèle correspondant.</h5>
                    <div class="ps-md-5 mb-3">
                        <a id="downloadLink" href="" class="btn btn-sm btn-primary">Télécharger le
                            modèle ici</a>
                    </div>
                    <h5 class="mb-3">3 - Remplissez vos données dans le modèle téléchargé.</h5>
                    <h5 class="mb-3">4 - Enregistrer le fichier sous format .xlsx, puis cliquez sur suivant pour
                        continuer.
                    </h5>

                    <div class="">
                        <button id="nextButton" class="btn float-right text-white"
                            style="{{ background_color_1() }}">Suivant</button>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="card m-b-30 mb-5 sectionCard" id="card2" style="display: none">
        <div class="card-body mb-5">
            <h6 class="d-flex" style="justify-content: center; color:red" id="importationChoisie"></h6>
            <fieldset class="border p-3 rounded-3">
                <legend class="float-none w-auto px-1">Etape 2</legend>
                <div class="row">
                    <h5 class="mb-3">1 - Sélectionnez le fichier Excel qui contient les données.</h5>
                    <div class="form-group d-flex align-items-center">
                        <input class="form-control mr-2" type="file" name="file" id="fileInput" accept=".xlsx">
                        <button type="button" class="btn btn-success" id="uploadButton" disabled>Charger le
                            fichier</button>
                    </div>
                    <div class="progress mt-3" style="display: none;">
                        <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <h5 class="mb-3" id="apercuText" style="display: none;">2 - Voici un aperçu des données du fichier
                        importé.</h5>
                    <div id="tableContainer" class="mt-4">
                        <!-- Tableaux de prévisualisation -->
                        <div id="previewFournisseurs" class="table-responsive" style="display: none;">
                            <table id="previewFournisseursP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom Fournisseur</th>
                                        <th>Adresse</th>
                                        <th>Pays</th>
                                        <th>IFU</th>
                                        <!-- <th>Téléphone fixe</th> -->
                                        <th>Téléphone mobile</th>
                                        <th>Adresse email</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewClients" class="table-responsive" style="display: none;">
                            <table id="previewClientsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code Client</th>
                                        <th>Nom client</th>
                                        <th>Pays</th>
                                        <th>Catégorie client</th>
                                        <th>Adresse client</th>
                                        <!-- <th>Téléphone fixe</th> -->
                                        <th>Téléphone mobile</th>
                                        <th>Adresse email</th>
                                        <th>IFU client</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewProduits" class="table-responsive" style="display: none;">
                            <table id="previewProduitsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type produit</th>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Catégorie</th>
                                        <th>Unité de comptage</th>
                                        <th>Emballage</th>
                                        <th>Type Emballage</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewPrixProduits" class="table-responsive" style="display: none;">
                            <table id="previewPrixProduitsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type prix prod</th>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Catégorie</th>
                                        <th>Unité de comptage</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewAgences" class="table-responsive" style="display: none;">
                            <table id="previewAgencesP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Agence</th>
                                        <th>Titre Signataire</th>
                                        <th>Nom Signataire</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="previewMagasins" class="table-responsive" style="display: none;">
                            <table id="previewMagasinsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Magasin</th>
                                        <th>Agence</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewCategorieProduits" class="table-responsive" style="display: none;">
                            <table id="previewCategorieProduitsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Libelle</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="previewCategorieClients" class="table-responsive" style="display: none;">
                            <table id="previewCategorieClientsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Libelle</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewUniteComptages" class="table-responsive" style="display: none;">
                            <table id="previewUniteComptagesP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Libelle</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewEntreeEmballages" class="table-responsive" style="display: none;">
                            <table id="previewEntreeEmballageP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Libelle</th>
                                        <th>Catégorie</th>
                                        <th>Magasin</th>
                                        <th>Quantité</th>
                                        <th>Prix Achat</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewStockEmballages" class="table-responsive" style="display: none;">
                            <table id="previewStockEmballagesP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Libelle</th>
                                        <th>Catégorie</th>
                                        <th>Magasin</th>
                                        <th>Quantité</th>
                                        <th>Prix Achat</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="previewCategorieEmballages" class="table-responsive" style="display: none;">
                            <table id="previewCategorieEmballagesP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Libelle</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="previewEmballages" class="table-responsive" style="display: none;">
                            <table id="previewEmballagesP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Libelle</th>
                                        <th>Catégorie</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="previewEntreeProduits" class="table-responsive" style="display: none;">
                            <table id="previewEntreeProduitsP" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Catégorie</th>
                                        <th>Magasin</th>
                                        <th>Quantité</th>
                                        <th>Prix Achat</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div id="finishImportSection" style="display: none;">
                        <h5 class="mb-3">3 - Cliquez sur "Terminer" pour terminer l'importation.</h5>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="col-6">

                            </div>
                            <div class="form-check d-flex align-items-center mr-2">
                                <form action="" method="POST" id="uploadForm">
                                    @csrf
                                    <input type="hidden" id="uploadedData" name="uploadedData">
                                    <input class="form-check-input" type="checkbox" name="nobougepage" id="stayOnPage">
                                    <label class="form-check-label ml-2" for="stayOnPage">
                                        Restez sur cette page après l'importation
                                    </label>
                            </div>
                            </form>
                            <div class="d-flex align-items-center ml-auto">

                                <button class="btn text-white" style="{{ background_color_1() }};" type="button"
                                    data-bs-toggle="modal" data-bs-target="#staticBackdrop">Terminer</button>
                            </div>
                        </div>

                    </div>

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
                                    Voulez-vous vraiment importer ces données ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button type="button" id="finishButton" class="btn btn-success">Oui
                                        importer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-secondary mr-3" onclick="reply('card1', 'card2')">Précédent</button>
                </div>
            </fieldset>
            <br>
            <br>
        </div>
    </div>
    <br>

    <script>
        document.getElementById('nextButton').addEventListener('click', function() {
            const selectedRadio = document.querySelector('input[name="Typefichier"]:checked');
            if (selectedRadio) {
                reply('card2', 'card1');
            } else {
                alert('Veuillez sélectionner un type de fichier à importer avant de continuer.');
            }
        });

        function reply(show, hide) {
            document.getElementById(show).style.display = 'block';
            document.getElementById(hide).style.display = 'none';
            localStorage.setItem('activeSection', show);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Restaurer l'état du bouton radio au chargement initial de la page
            restoreSelectedRadio();
            restoreRouteurl();

            // Fonction pour restaurer l'état du bouton radio sélectionné
            function restoreSelectedRadio() {
                const selectedRadioId = localStorage.getItem('selectedRadioId');
                const radioButtons = document.querySelectorAll('input[name="Typefichier"]');


                radioButtons.forEach(radio => {
                    if (radio.id === selectedRadioId) {
                        radio.checked = true; // Cocher le bouton radio sélectionné
                    } else {
                        radio.checked = false; // Désactiver les autres boutons radio
                    }
                });

                // Mettre à jour l'affichage en fonction du bouton radio restauré
                updateImportationChoisie();
            }

            function restoreRouteurl() {
                const url = localStorage.getItem('url');
                const route = localStorage.getItem('route');
                if (url && route) {
                    document.getElementById('downloadLink').setAttribute('href', url);
                    document.getElementById('uploadForm').setAttribute('action', route);
                }
            }

            // Fonction pour mettre à jour le texte en fonction du bouton radio sélectionné
            function updateImportationChoisie() {
                const selectedRadio = document.querySelector('input[name="Typefichier"]:checked');
                const importationChoisie = document.getElementById('importationChoisie');

                if (selectedRadio) {
                    switch (selectedRadio.id) {
                        case 'flexRadioDefault1':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les fournisseurs';
                            break;
                        case 'flexRadioDefault2':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les clients';
                            break;
                        case 'flexRadioDefault3':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les produits';
                            break;
                        case 'flexRadioDefault4':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les prix des Produits';
                            break;
                        case 'flexRadioDefault9':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les agences';
                            break;
                        case 'flexRadioDefault5':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les magasins';
                            break;
                        case 'flexRadioDefault6':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les unités de comptage';
                            break;
                        case 'flexRadioDefault7':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les unités de comptage';
                            break;
                        case 'flexRadioDefault8':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les unités de comptage';
                            break;
                            case 'flexRadioDefault10':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les entrées emballages';
                            break;
                            case 'flexRadioDefault11':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les stocks emballages';
                            break;
                            case 'flexRadioDefault12':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les catégories emballages';
                            break;
                            case 'flexRadioDefault13':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les emballages';
                            break;
                            case 'flexRadioDefault14':
                            importationChoisie.textContent = 'Vous avez choisi d\'importer les entrées de produits';
                            break;
                        default:
                            importationChoisie.textContent = '';
                    }

                    // Stocker l'ID du bouton radio sélectionné dans le localStorage
                    localStorage.setItem('selectedRadioId', selectedRadio.id);
                }
            }

            // Ajouter des écouteurs d'événements à tous les boutons radio
            const radioButtons = document.querySelectorAll('input[name="Typefichier"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateImportationChoisie();
                    const url = this.getAttribute('data-url');
                    const route = this.getAttribute('route-soumission');
                    document.getElementById('downloadLink').setAttribute('href', url);
                    document.getElementById('uploadForm').setAttribute('action', route);
                    localStorage.setItem('url', url);
                    localStorage.setItem('route', route);
                });
            });

            // Mettre à jour le texte au chargement initial de la page
            updateImportationChoisie();

            const uploadButton = document.getElementById('uploadButton');
            const fileInput = document.getElementById('fileInput');
            const progressBar = document.getElementById('progressBar');
            let uploadedDataArray = [];
            let importClass;

            const tableMapping = {
                'flexRadioDefault1': {
                    tableId: 'previewFournisseursP',
                    importClass: 'FournisseursImport'
                },
                'flexRadioDefault2': {
                    tableId: 'previewClientsP',
                    importClass: 'ClientsImport'
                },
                'flexRadioDefault3': {
                    tableId: 'previewProduitsP',
                    importClass: 'ClientsImport'
                },
                'flexRadioDefault4': {
                    tableId: 'previewPrixProduitsP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault9': {
                    tableId: 'previewAgencesP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault5': {
                    tableId: 'previewMagasinsP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault6': {
                    tableId: 'previewCategorieProduitsP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault7': {
                    tableId: 'previewCategorieClientsP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault8': {
                    tableId: 'previewUniteComptagesP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault10': {
                    tableId: 'previewEntreeEmballageP',
                    importClass: 'EntreeEmballagesImport'
                },
                'flexRadioDefault11': {
                    tableId: 'previewStockEmballagesP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault12': {
                    tableId: 'previewCategorieEmballagesP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault13': {
                    tableId: 'previewEmballagesP',
                    importClass: 'ProduitsImport'
                },
                'flexRadioDefault14': {
                    tableId: 'previewEntreeProduitsP',
                    importClass: 'ProduitsImport'
                }

            };

            fileInput.addEventListener('change', function() {
                uploadButton.disabled = fileInput.files.length === 0;
            });

            uploadButton.addEventListener('click', function() {
                let selectedTableId;
                radioButtons.forEach(function(radio) {
                    if (radio.checked) {
                        selectedTableId = tableMapping[radio.id].tableId;
                        importClass = tableMapping[radio.id].importClass;
                    }
                });

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('importClass', importClass);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('chargementFichier') }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.onprogress = function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = (event.loaded / event.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressBar.innerHTML = percentComplete.toFixed(0) + '%';
                        progressBar.parentElement.style.display =
                        'block'; // Afficher la barre de progression
                    }
                };

                xhr.onload = function() {
                    progressBar.style.width = '100%';
                    progressBar.innerHTML = 'Fichier 100% téléchargé';

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (Array.isArray(response.data)) {
                            alert('Fichier chargé avec succès');
                            uploadedDataArray = response.data;
                            localStorage.setItem('uploadedDataArray', JSON.stringify(
                            uploadedDataArray));
                            // Mettre les données dans le champ caché
                            document.getElementById('uploadedData').value = JSON.stringify(
                                uploadedDataArray);
                            localStorage.setItem('selectedTableId', selectedTableId);
                            displayTable(response.data, selectedTableId);
                        } else {
                            alert('Les données reçues ne sont pas valides.');
                            // console.log('Invalid data:', response.data);
                        }
                    } else {
                        alert('Erreur lors du chargement du fichier');
                    }
                };

                xhr.send(formData);
            });


            function displayTable(data, tableId) {
                const table = document.getElementById(tableId);
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';

                data.forEach(function(row, index) {
                    if (index === 0) return; // Ignorer la ligne d'en-tête
                    const tr = document.createElement('tr');
                    for (let cell in row) {
                        const td = document.createElement('td');
                        td.textContent = row[cell];
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                });

                // Afficher la table sélectionnée
                document.querySelectorAll('.table-responsive').forEach(function(table) {
                    table.style.display = 'none';
                });
                document.getElementById(tableId).parentElement.style.display = 'block';
                document.getElementById('finishImportSection').style.display = 'block';
                document.getElementById('apercuText').style.display = 'block';

                // Initialiser DataTable pour la table affichée
                $('#' + tableId).DataTable({
                    "pagingType": "simple_numbers",
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    "language": {
                        "search": "Rechercher:",
                        "lengthMenu": "Afficher _MENU_ entrées",
                        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        "paginate": {
                            "first": "Premier",
                            "last": "Dernier",
                            "next": "Suivant",
                            "previous": "Précédent"
                        }
                    }
                });
            }

            // Gérer la soumission du formulaire sur le bouton "Terminer"
            document.getElementById('finishButton').addEventListener('click', function() {
                const form = document.getElementById('uploadForm');
                form.submit();

                // Nettoyer les données stockées dans localStorage après soumission
                localStorage.removeItem('selectedRadioId');
                localStorage.removeItem('uploadedDataArray');
                localStorage.removeItem('activeSection');
                localStorage.removeItem('selectedTableId');
                localStorage.removeItem('url');
                localStorage.removeItem('route');
            });

            // Recharger les données depuis localStorage au chargement de la page
            function reloadStoredData() {
                const storedData = localStorage.getItem('uploadedDataArray');
                const activeSection = localStorage.getItem('activeSection');
                const storedTableId = localStorage.getItem('selectedTableId');

                if (storedData && storedTableId) {
                    uploadedDataArray = JSON.parse(storedData);
                    // Mettre les données dans le champ caché
                    document.getElementById('uploadedData').value = JSON.stringify(uploadedDataArray);
                    displayTable(uploadedDataArray, storedTableId);
                }

                if (activeSection) {
                    document.querySelectorAll('.sectionCard').forEach(function(section) {
                        section.style.display = 'none';
                    });
                    document.getElementById(activeSection).style.display = 'block';
                } else {
                    document.getElementById('card1').style.display =
                    'block'; // Afficher une section par défaut si aucune section n'est stockée
                }
            }

            reloadStoredData(); // Appeler la fonction de chargement des données au chargement initial de la page

        });
    </script>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Restaurer l'état du bouton radio au chargement initial de la page
            restoreSelectedRadio();
            // Fonction pour restaurer l'état du bouton radio sélectionné
            function restoreSelectedRadio() {
                const selectedRadioId = localStorage.getItem('selectedRadioId');
                const radioButtons = document.querySelectorAll('input[name="Typefichier"]');

                radioButtons.forEach(radio => {
                    if (radio.id === selectedRadioId) {
                        radio.checked = true; // Cocher le bouton radio sélectionné
                    } else {
                        radio.checked = false; // Désactiver les autres boutons radio
                    }
                });

                // Mettre à jour l'affichage en fonction du bouton radio restauré
                updateImportationChoisie();
            }

            // Fonction pour mettre à jour le texte
            function updateImportationChoisie() {
                console.log('updateImportationChoisie est appelé');
                // Trouver le bouton radio sélectionné
                const selectedRadio = document.querySelector('input[name="Typefichier"]:checked');
                // Trouver l'élément h3
                const importationChoisie = document.getElementById('importationChoisie');
                // Mettre à jour le texte de l'élément h3
                if (selectedRadio) {
                    if (selectedRadio.id === 'flexRadioDefault1') {
                        importationChoisie.textContent = 'Vous avez choisi d\'importer les fournisseurs';
                    } else if (selectedRadio.id === 'flexRadioDefault2') {
                        importationChoisie.textContent = 'Vous avez choisi d\'importer les clients';
                    } else if (selectedRadio.id === 'flexRadioDefault3') {
                        importationChoisie.textContent = 'Vous avez choisi d\'importer les produits';
                    } else if (selectedRadio.id === 'flexRadioDefault4') {
                        importationChoisie.textContent = 'Vous avez choisi d\'importer les prix des Produits';
                    }
                    // Stocker l'ID du bouton radio sélectionné dans le localStorage
                    localStorage.setItem('selectedRadioId', selectedRadio.id);
                }
            }

            // Ajouter des écouteurs d'événements à tous les boutons radio
            const radioButtons = document.querySelectorAll('input[name="Typefichier"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {

                    updateImportationChoisie;
                    let url = this.getAttribute('data-url');
                    let route = this.getAttribute('route-soumission');
                    document.getElementById('downloadLink').setAttribute('href', url);
                    document.getElementById('uploadForm').setAttribute('action', route);
                    console.log('url', url);
                    console.log('route', route);
                });
            });

            // Mettre à jour le texte au chargement initial de la page
            updateImportationChoisie();

        });

        function reply(show, hide) {
            document.getElementById(show).style.display = 'block';
            document.getElementById(hide).style.display = 'none';
            localStorage.setItem('activeSection', show);
        }

        // Update finishButton action
        let finishButton = document.getElementById('finishButton');
        finishButton.addEventListener('click', function() {
            let form = document.getElementById('uploadForm');
            // form.action = selectedRoute;
            form.submit();
        });

        document.addEventListener('DOMContentLoaded', function() {
            let radioButtons = document.querySelectorAll('input[name="Typefichier"]');
            let uploadButton = document.getElementById('uploadButton');
            let fileInput = document.getElementById('fileInput');
            let progressBar = document.getElementById('progressBar');
            let uploadedDataArray = [];
            let importClass;

            let tableMapping = {
                'flexRadioDefault1': {
                    tableId: 'previewFournisseursP',
                    importClass: 'FournisseursImport'
                },
                'flexRadioDefault2': {
                    tableId: 'previewClientsP',
                    importClass: 'ClientsImport'
                },
                'flexRadioDefault3': {
                    tableId: 'previewProduitsP',
                    importClass: 'ClientsImport'
                },
                'flexRadioDefault4': {
                    tableId: 'previewPrixProduitsP',
                    importClass: 'ProduitsImport'
                }
            };

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    uploadButton.disabled = false;
                } else {
                    uploadButton.disabled = true;
                }
            });

            uploadButton.addEventListener('click', function() {
                let selectedTableId;
                // let selectedRoute;
                radioButtons.forEach(function(radio) {
                    if (radio.checked) {
                        selectedTableId = tableMapping[radio.id].tableId;
                        importClass = tableMapping[radio.id].importClass;
                    }
                });

                let formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('importClass', importClass);


                let xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('chargementFichier') }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.onprogress = function(event) {
                    if (event.lengthComputable) {
                        let percentComplete = (event.loaded / event.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressBar.innerHTML = percentComplete.toFixed(0) + '%';
                        progressBar.parentElement.style.display =
                        'block'; // Affiche la barre de progression
                    }
                };

                xhr.onload = function() {
                    progressBar.style.width = '100%';
                    progressBar.innerHTML = 'Fichier 100% téléchargé';

                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        // console.log('Response Data: ', response);

                        if (Array.isArray(response.data)) {
                            alert('Fichier chargé avec succès');
                            uploadedDataArray = response.data;
                            localStorage.setItem('uploadedDataArray', JSON.stringify(
                            uploadedDataArray));
                            localStorage.setItem('selectedTableId', selectedTableId);
                            displayTable(response.data, selectedTableId);
                        } else {
                            alert('Les données reçues ne sont pas valides.');
                            console.log('Invalid data:', response.data);
                        }
                    } else {
                        alert('Erreur lors du chargement du fichier');
                    }
                };

                xhr.send(formData);

            });

            // Mettre les données dans le champ caché
            document.getElementById('uploadedData').value = JSON.stringify(uploadedDataArray);

            function displayTable(data, tableId) {
                let table = document.getElementById(tableId);
                let finishImportSection = document.getElementById('finishImportSection');
                let apercuText = document.getElementById('apercuText');
                let tbody = table.querySelector('tbody');
                tbody.innerHTML = '';

                data.forEach(function(row, index) {
                    if (index === 0) return; // Skip header row
                    let tr = document.createElement('tr');
                    for (let cell in row) {
                        let td = document.createElement('td');
                        td.textContent = row[cell];
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                });

                // Hide all tables
                document.querySelectorAll('.table-responsive').forEach(function(table) {
                    table.style.display = 'none';
                });

                apercuText.style.display = 'block';
                // Show the selected table
                document.getElementById(tableId).parentElement.style.display = 'block';

                $('#' + tableId).DataTable({
                    "pagingType": "simple_numbers",
                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    "language": {
                        "search": "Rechercher:",
                        "lengthMenu": "Afficher _MENU_ entrées",
                        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        "paginate": {
                            "first": "Premier",
                            "last": "Dernier",
                            "next": "Suivant",
                            "previous": "Précédent"
                        }
                    }
                });


                $('#' + tableId + ' tbody').on('click', 'tr', function() {
                    $(this).toggleClass('selected');
                });


                document.getElementById('finishImportSection').style.display = 'block';
            }
            document.getElementById('finishButton').addEventListener('click', function() {
                let form = document.getElementById('uploadForm');
                form.submit();
                localStorage.removeItem('selectedRadioId');
                localStorage.removeItem('uploadedDataArray');
                localStorage.removeItem('activeSection');
            });

            // Recharger les données depuis le localStorage au chargement de la page
            function reloadStoredData() {
                let storedData = localStorage.getItem('uploadedDataArray');
                let activeSection = localStorage.getItem('activeSection');
                let storedTableId = localStorage.getItem('selectedTableId');
                // console.log('storedTableId', storedTableId);

                if (storedData && storedTableId) {
                    uploadedDataArray = JSON.parse(storedData);
                    displayTable(uploadedDataArray, storedTableId);
                }

                if (activeSection) {
                    document.querySelectorAll('.sectionCard').forEach(function(section) {
                        section.style.display = 'none';
                    });
                    document.getElementById(activeSection).style.display = 'block';

                } else {
                    // Afficher une section par défaut si aucune section n'est stockée dans le localStorage
                    document.getElementById('card1').style.display = 'block';
                }
            }
            reloadStoredData();
        });
    </script> -->

    @include('layouts.alert')
@endsection
