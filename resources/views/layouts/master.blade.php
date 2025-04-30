@php
    inMaintenance(); verifAccessAgenceUser();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>EASY-FAC {{ isset($title) ? ' | ' . $title : '' }}</title>

    <meta name="web-site-name" content="{{ WEB_SITE_NAME }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (isset($title) && $title === 'AFFECTATION DE DROIT')
        <meta name="getGroupe"
            content="{{ isset($title) && $title == 'AFFECTATION DE DROIT' ? $request->input('groupe_id1') : '' }}">
        <meta name="getUser"
            content="{{ isset($title) && $title == 'AFFECTATION DE DROIT' ? $request->input('user_id') : '' }}">
        <meta name="getAgence"
            content="{{ isset($title) && $title == 'AFFECTATION DE DROIT' ? $request->input('agence_id') : '' }}">
        <meta name="getModule"
            content="{{ isset($title) && $title == 'AFFECTATION DE DROIT' ? $request->input('module') : '' }}">
        <meta name="getModule1"
            content="{{ isset($title) && $title == 'AFFECTATION DE DROIT' ? $request->input('module1') : '' }}">
    @endif

    <meta content="EASYFAC Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('images/logo_easyfac.png') }}">

    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">

    <link href="{{ asset('dashboard/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet" type="text/css">

    <!-- DataTables -->
    <link href="{{ asset('dashboard/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('dashboard/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('dashboard/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="{{ asset('jquery.min.js') }}"></script>

    {{-- Loading sur les bouttons important --}}
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">

    <script src="{{ asset('sweetalert2-11.10.8/package/dist/sweetalert2.all.min.js') }}"></script>
    <link rel="stylesheet" href="{{ WEB_SITE_NAME }}/css/select2.min.css">
    <link rel="stylesheet" href="{{ WEB_SITE_NAME }}/css/style.css">

    {{-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> --}}

    @livewireStyles

    @yield('head')

</head>

<body class="fixed-left">
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">

        @include('layouts.partials.sidebar')

        <div class="content-page">
            <!-- Start content -->
            <div class="content">

                @include('layouts.partials.header')

                <div class="page-content-wrapper ">
                    <div class="container-fluid">

                        <div class="responseMaintenance"></div>
                        @yield('content')

                    </div>
                </div>
            </div>

            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- jQuery  -->
    <script src="{{ asset('dashboard/js/jquery.min.js') }}"></script>

    @yield('js')
    {{-- tiny scrip --}}

    <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.slimscroll.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('dashboard/js/app-drixo.js') }}"></script>
    <script src="{{ WEB_SITE_NAME }}/js/app.js"></script>


    <!-- Required datatable js -->
    <script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('dashboard/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dashboard/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script id="hiddenScript" src="{{ asset('dashboard/pages/datatables.init.js') }}"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />


    @livewireScripts


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélection du champ contenant le bouton de téléchargement
            var downloadFieldset = document.querySelector('#downloadFieldset');

            // Sélection du bouton de téléchargement à l'intérieur du champ
            var downloadButton = downloadFieldset.querySelector('.btn-primary');

            // Sélection des boutons radio
            var radioButtons = document.querySelectorAll('input[name="fileType"]');

            // Fonction pour mettre à jour le lien de téléchargement
            function updateDownloadLink() {
                // Parcours des boutons radio pour trouver celui qui est sélectionné
                radioButtons.forEach(function(radioButton) {
                    if (radioButton.checked) {
                        // Modifier le lien du bouton de téléchargement en fonction du bouton radio sélectionné
                        switch (radioButton.id) {
                            case 'flexRadioDefault1':
                                downloadButton.setAttribute('onclick',
                                    'window.location.href = "lien_vers_fichier_fournisseurs"');
                                break;
                            case 'flexRadioDefault2':
                                downloadButton.setAttribute('onclick',
                                    'window.location.href = "lien_vers_fichier_clients"');
                                break;
                            case 'flexRadioDefault3':
                                downloadButton.setAttribute('onclick',
                                    'window.location.href = "lien_vers_fichier_produits"');
                                break;
                            case 'flexRadioDefault4':
                                downloadButton.setAttribute('onclick',
                                    'window.location.href = "lien_vers_fichier_prix_produits"');
                                break;
                            default:
                                break;
                        }
                    }
                });
            }

            // Appel de la fonction pour mettre à jour le lien de téléchargement lors du chargement de la page
            updateDownloadLink();

            // Ajout d'un écouteur d'événement pour mettre à jour le lien de téléchargement lorsqu'un bouton radio est sélectionné
            radioButtons.forEach(function(radioButton) {
                radioButton.addEventListener('change', function() {
                    updateDownloadLink();
                });
            });
        });
    </script>
    <!-- Inclure la bibliothèque SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélection des éléments importants
            var fileImportFieldset = document.querySelector('#fileImportFieldset');

            // Gestionnaire d'événements pour le clic sur le bouton "Charger le fichier"
            fileImportFieldset.addEventListener('click', function(event) {
                // Vérifier si le clic est sur le bouton "Charger le fichier"
                var targetButton = event.target.closest('.btn-success[name="filedownload"]');
                if (targetButton) {
                    console.log('Bouton chargé cliqué');
                    // Récupérer le fichier sélectionné par l'utilisateur
                    var fileInput = fileImportFieldset.querySelector('input[type="file"]');
                    var selectedFile = fileInput.files[0];
                    console.log('selectedFile', selectedFile);
                    // Vérifier si un fichier est sélectionné
                    if (selectedFile) {
                        console.log('selectedFile vérification ok');
                        // Récupérer le bouton radio sélectionné
                        var selectedRadioButton = document.querySelector('input[name="fileType"]:checked');

                        // Traiter le fichier en fonction du bouton radio sélectionné
                        switch (selectedRadioButton.id) {
                            case 'flexRadioDefault1':
                                break;
                            case 'flexRadioDefault2':
                                // Traitement pour le fichier des Clients
                                // Implémentez le traitement nécessaire ici
                                break;
                            case 'flexRadioDefault3':
                                console.log('flexRadioDefault3 bien détecté');
                                // Traitement pour le fichier des Produits
                                handleProduitsFile(selectedFile);
                                break;
                            case 'flexRadioDefault4':
                                // Traitement pour le fichier des prix des Produits
                                // Implémentez le traitement nécessaire ici
                                break;
                            default:
                                break;
                        }
                    } else {
                        // Afficher un message d'erreur si aucun fichier n'est sélectionné
                        alert('Veuillez sélectionner un fichier Excel.');
                    }
                }
            });
        });

        // Fonction pour traiter le fichier des Produits
        function handleProduitsFile(file) {
            // Créer un objet FileReader pour lire le fichier
            var reader = new FileReader();

            // Gestionnaire d'événements pour la fin de la lecture du fichier
            reader.onload = function(event) {
                // Récupérer le contenu du fichier Excel
                var data = new Uint8Array(event.target.result);
                var workbook = XLSX.read(data, {
                    type: 'array'
                });
                console.log('data', data);
                console.log('workbook', workbook);
                // Accéder à la première feuille de calcul (worksheet)
                var firstSheetName = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[firstSheetName];

                // Convertir les données de la feuille de calcul en tableau JSON
                var jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    header: 1
                });
                console.log('jsonData', jsonData);

                // Afficher les données dans le tableau correspondant
                var tableProduits = document.getElementById('tableProduits');
                var tbody = tableProduits.querySelector('tbody');
                tbody.innerHTML = ''; // Effacer le contenu existant du tableau

                // Parcourir les données JSON et les afficher dans le tableau
                jsonData.forEach(function(rowData) {
                    var row = document.createElement('tr');
                    rowData.forEach(function(cellData) {
                        var cell = document.createElement('td');
                        cell.textContent = cellData;
                        row.appendChild(cell);
                    });
                    tbody.appendChild(row);
                });
                // Afficher le tableau une fois les données ajoutées
                tableProduits.style.display = 'table'; // Pour afficher en tant que tableau
                // Lire le contenu du fichier en tant que tableau binaire
            };
            reader.readAsArrayBuffer(file);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                $('.clickable-row').removeClass('selected');
                $(this).addClass('selected');
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('#myForm input, #myForm textarea').forEach(element => {
                element.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        });
    </script>

    <script src="{{ WEB_SITE_NAME }}/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-single').each(function() {
                $(this).select2({
                    placeholder: 'Sélectionner une option',
                    allowClear: true
                });
            });
        });
    </script>

    {{-- Notification pour la mise en maintenance du site --}}
    {{-- <script> const idUser = @json(auth()->user()->id);</script>
    <script src="{{ asset('jsPerso/maintenance.js') }}"></script> --}}

</body>

</html>
