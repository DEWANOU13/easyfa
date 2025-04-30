<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="web-site-name" content="{{ WEB_SITE_NAME }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if (isset($title) && $title === 'AFFECTATION DE DROIT')
  <meta name="getGroupe" content="{{ (isset($title) && $title == 'AFFECTATION DE DROIT') ? $request->input('groupe_id1') : '' }}">
  @endif
  <title>ESAYFAC {{ isset($title) ? ' | '.$title : '' }}</title>
  <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/fichiercss.css') }}">
  <link rel="stylesheet" href="{{ WEB_SITE_NAME }}/css/select2.min.css">
  <script src="{{ asset('jquery.min.js') }}"></script>

  <script src="{{ asset('sweetalert2-11.10.8/package/dist/sweetalert2.all.min.js') }}"></script>
  @vite(['resources/js/app.js'])
</head>

<body>
  <main class="">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle  " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Accueil
              </button>
              <ul class="dropdown-menu">
                <li><a href="{{ route('home') }}" class="dropdown-item" type="button">Accueil</a>
                </li>
                <li><a href="{{ route('magasin') }}" class="dropdown-item" type="button">Magasins</a>
                </li>
                <li><a href="{{ route('fournisseur') }}" class="dropdown-item" type="button">Fournisseurs</a></li>
                <li><a href="{{ route('categorieclient') }}" class="dropdown-item" type="button">Catégories Clients</a></li>
                <li><a href="{{ route('client') }}" class="dropdown-item" type="button">Clients</a>
                </li>
              </ul>
            </div>
            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle   " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Produits
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item " type="button" href="{{ route('page.produit.categorie') }}">Catégories</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.produit.unite_comptage') }}">Unités de comptage</a></a>
                </li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.produit.produit') }}">Produits</a></li>
                {{-- <li><a class="dropdown-item " type="button"
                                        href="{{ route('page.nouveau.nouveau') }}">Nouveau</a></li> --}}
                <li><a class="dropdown-item " type="button" href="{{ route('page.stock.stock') }}">Stock</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.gestion_prix.gestion_prix') }}">Gestion des prix</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.entree.entree') }}">Entrées</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.sortie.sortie') }}">Sorties</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.transfert.transfert') }}">Transferts</a></li>
                <li><a class="dropdown-item " type="button" href="{{ route('page.inventaire.inventaire') }}">Inventaires</a></li>
              </ul>
            </div>


            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle   " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Facturations
              </button>
              <ul class="dropdown-menu">
                <li><a href="{{ route('proforma') }}" class="dropdown-item" type="button">Proformas</a></li>
                <li><a href="{{ route('facture') }}" class="dropdown-item" type="button">Facture</a>
                </li>
                <li><a href="{{ route('avoir') }}" class="dropdown-item" type="button">avoir</a></li>
              </ul>
            </div>
            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle   " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Règlements
              </button>
              <ul class="dropdown-menu">
                <li><a href="{{ route('reglement') }}" class="dropdown-item" type="button">Règlements</a></li>
              </ul>
            </div>
            @can('droit-acces')
            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle   " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Paramètre et Administration
              </button>
              <ul class="dropdown-menu">
                <li><a href="{{ route('parametre') }}" class="dropdown-item" type="button">Paramètres</a></li>
                <li><button class="dropdown-item" type="button" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Importation</button></li>
                <li><a href="{{ route('agences') }}" class="dropdown-item" type="button">Agences</a></li>
                <li><a href="{{ route('user.index') }}" class="dropdown-item" type="button">Utilisateur</a></li>
                <li><a href="{{ route('groupe.index') }}" class="dropdown-item" type="button">Groupes</a></li>
                <li><a href="{{ route('groupeUser.index') }}" class="dropdown-item" type="button">Affectations et droits d'accès</a></li>
                <li><a href="{{ route('agenceUtilisateur.index') }}" class="dropdown-item" type="button">Affectations d'agence</a></li>
                <li><a href="{{ route('facture_FF__FLF') }}" class="dropdown-item" type="button">Factures (FF) et lignes de factures (FLF)</a></li>
              </ul>
            </div>
            @endcan
            <div class="dropdown">
              <button class="btn btn-light dropdown-toggle    " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Statistiques
              </button>
              <ul class="dropdown-menu">
                <li><a href="{{route('statistiques') }}" class="dropdown-item" type="button">Achat</a></li>
                <li><a href="{{ route('vente') }}" class="dropdown-item" type="button">vente</a></li>
                <li><a href="{{  route('stocks') }}" class="dropdown-item" type="button">stock</a></li>
                <li><a href="{{  route('marge') }}" class="dropdown-item" type="button">Marge</a></li>

              </ul>






            </div>

          </div>
        </div>
        <div class="d-flex justify-content-end">
          <div class="fw-bold d-flex">
            <span style="margin-right: 10px;" class="pt-2">Agence {{ getAgenceById(auth()->user()->user_id) }}</span>
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <span><img src="{{ asset('images/faces/face28.jpg') }}" class="img-fluid" style="border-radius: 50%; width: 40px;" alt=""></span>
              <span class="d-none d-md-block dropdown-toggle ps-2"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li>
                <button class="dropdown-item d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                  <span>Mon Profil</span>
                </button>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('changePassword') }}">
                  <span>Changer le mot de passe</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#staticDeconnexion" (click)="openModal()" href="#">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Se déconnecter</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <!-- Modal Deconnexion-->
    <div class="modal fade" id="staticDeconnexion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticDeconnexion" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Déconnexion</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Voulez-vous vraiment vous déconnecter
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <form action="{{ route('logout') }}" method="post">
              @method('delete')
              @csrf
              <button class="btn btn-danger">Déconnexion</button>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal mon profil -->
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Mon profil</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="card ">
          <div class="card-body">
            <div class="card-text">
              <div class="row">
                <span class="text-center">
                  <img src="{{ asset('images/faces/face28.jpg') }}" alt="">
                </span>
              </div>
              <div class="row">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <tbody>
                      <tr>
                        <th scope="row">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" stroke-width="0" fill="currentColor" />
                            <path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" stroke-width="0" fill="currentColor" />
                          </svg>
                        </th>
                        <td>{{ Auth::user() != null ? Auth::user()->name : '' }}</td>
                      </tr>

                      <tr>
                        <th scope="row">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M22 7.535v9.465a3 3 0 0 1 -2.824 2.995l-.176 .005h-14a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-9.465l9.445 6.297l.116 .066a1 1 0 0 0 .878 0l.116 -.066l9.445 -6.297z" stroke-width="0" fill="currentColor" />
                            <path d="M19 4c1.08 0 2.027 .57 2.555 1.427l-9.555 6.37l-9.555 -6.37a2.999 2.999 0 0 1 2.354 -1.42l.201 -.007h14z" stroke-width="0" fill="currentColor" />
                          </svg>
                        </th>
                        <td>{{ Auth::user() != null ? Auth::user()->email : '' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="container-fluid">
      @yield('content')
    </section>
  </main>

  <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Cet assistant vous aidera à importer vos
            données dans votre logiciel</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <span class="fs-6">1 - Fermer tous les fichiers Excels ouverts </span>
              <hr class="border-2">
              <form action="">
                <fieldset class="ms-1">
                  <legend><span class="fs-6">2 - Choisir le type de fichier à importer</span></legend>
                  <div class="border border-raduis p-2">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fileType" id="flexRadioDefault1" checked>
                      <label class="form-check-label" for="flexRadioDefault1">
                        Fichier des Fournisseurs
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fileType" id="flexRadioDefault2">
                      <label class="form-check-label" for="flexRadioDefault2">
                        Fichier des Clients
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fileType" id="flexRadioDefault3">
                      <label class="form-check-label" for="flexRadioDefault3">
                        Fichier des Produits
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="fileType" id="flexRadioDefault4">
                      <label class="form-check-label" for="flexRadioDefault4">
                        Fichier des prix des Produits
                      </label>
                    </div>
                  </div>
                </fieldset>
                <fieldset class="ms-1 mb-2" id="downloadFieldset">
                  <legend><span class="fs-6">3 - Prendre le modèle correspondant.</span></legend>
                  <div class="border border-raduis p-2">
                    <button type="button" class="btn btn-sm btn-primary">Télécharger le modele</button>
                  </div>
                </fieldset>
                <fieldset class="ms-1 mb-2">
                  <legend><span class="fs-6">4 - Disposer vos données selon le modèle.</span></legend>
                  <hr class="border-2">
                </fieldset>
                <fieldset class="ms-1 mb-2">
                  <legend><span class="fs-6">5 - Enregistrer le fichier sous format .xlsx, puis cliquez sur suivant pour continuer.</span></legend>
                </fieldset>
              </form>

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal">Suivant</button>
          <button class="btn btn-secondary" aria-label="Close" data-bs-toggle="modal">Annuler</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('importProduits') }}" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <fieldset class="ms-1" id="fileImportFieldset">
                  <legend> <span class="fs-6">1 - Sélectionnez le fichier Excel qui contient les
                      données.</span>
                  </legend>
                  @csrf
                  <input class="form-control" type="file" name="file" id="">
                  <button type="button" class="btn btn-success" name="filedownload">Charger le fichier</button>

                </fieldset>
                <fieldset class="ms-1 mb-2">
                  <legend> <span class="fs-6">2 - Cliquez sur <strong>Terminer</strong> pour
                      terminer l'importation.</span></legend>
                  <hr class="border-2">
                </fieldset>
                <div class="text-end mb-3">
                  <button type="button" class="btn btn-sm btn-primary">Rétablir la couleur</button>
                  <button type="button" class="btn btn-sm btn-primary">Supprimer les
                    lignes</button>
                </div>

                <!-- Champ pour afficher le tableau correspondant au type de fichier -->
                <fieldset class="ms-1 mb-2">
                  <legend><span class="fs-6">3 - Aperçu des données du fichier importé</span></legend>
                  <!-- Div pour afficher le tableau des fournisseurs -->
                  <div id="tableFournisseurs" style="display: none;">
                    <table id="tabFournisseurs" class="table table-striped">
                      <!-- Entêtes de tableau -->
                      <thead>
                        <tr>
                          <th>Type produit</th>
                          <th>Référence</th>
                          <th>Désignation</th>
                          <th>Catégorie</th>
                          <th>Unité de comptage</th>
                        </tr>
                      </thead>
                      <!-- Corps de tableau (les données seront ajoutées dynamiquement) -->
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                  <!-- Div pour afficher le tableau des clients -->
                  <div id="tableClients" style="display: none;">
                    <table id="tabClients" class="table table-striped">
                      <!-- Entêtes de tableau -->
                      <thead>
                        <tr>
                          <th>Type produit</th>
                          <th>Référence</th>
                          <th>Désignation</th>
                          <th>Catégorie</th>
                          <th>Unité de comptage</th>
                        </tr>
                      </thead>
                      <!-- Corps de tableau (les données seront ajoutées dynamiquement) -->
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                  <!-- Div pour afficher le tableau des produits -->
                  <div id="tableProduits" style="display: block;">
                    <table id="tableProduits" class="table table-striped">
                      <!-- Entêtes de tableau -->
                      <thead>
                        <tr>
                          <th>Type produit</th>
                          <th>Référence</th>
                          <th>Désignation</th>
                          <th>Catégorie</th>
                          <th>Unité de comptage</th>
                        </tr>
                      </thead>
                      <!-- Corps de tableau (les données seront ajoutées dynamiquement) -->
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                  <!-- Ajoutez d'autres div pour les autres types de fichier au besoin -->
                </fieldset>

              </div>
            </div>
          </div>
          <div class="modal-footer" id="importFieldset">
            <button class="btn btn-info" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Précedent</button>
            <button class="btn btn-primary" type="submit" data-bs-toggle="modal">Terminer</button>
            <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="{{ asset('bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ WEB_SITE_NAME }}/js/app.js"></script>
  {{-- <script src="{{asset('htmx/htmx.min.js') }}"></script> --}}
  <script src="{{ WEB_SITE_NAME }}/js/select2.min.js"></script>
  <script>
    // Initialiser Selectize sur les éléments de sélection avec la classe 'selectize'
    $(document).ready(function() {
      $('.js-single').select2();
    });

  </script>

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
                downloadButton.setAttribute('onclick', 'window.location.href = "lien_vers_fichier_fournisseurs"');
                break;
              case 'flexRadioDefault2':
                downloadButton.setAttribute('onclick', 'window.location.href = "lien_vers_fichier_clients"');
                break;
              case 'flexRadioDefault3':
                downloadButton.setAttribute('onclick', 'window.location.href = "lien_vers_fichier_produits"');
                break;
              case 'flexRadioDefault4':
                downloadButton.setAttribute('onclick', 'window.location.href = "lien_vers_fichier_prix_produits"');
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
                // Traitement pour le fichier des Fournisseurs
                // Implémentez le traitement nécessaire ici
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
        // Supprimez la classe 'selected' de toutes les lignes
        $('.clickable-row').removeClass('selected');
        // Ajoutez la classe 'selected' à la ligne cliquée
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

</body>

</html>
