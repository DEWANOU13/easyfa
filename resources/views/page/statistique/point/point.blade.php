@extends('layouts.master', ['title' => 'Statistique'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Point de Vente',
        'infos2' => 'Point de vente',
        'infos3' => 'Liste',
    ])
    <div class="row d-flex text-start p-3">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cumPro-tab" data-bs-toggle="tab" data-bs-target="#cumPro"
                        type="button" role="tab" aria-controls="cumPro" aria-selected="false">Point de Vente
                    </button>
                </li>


            </ul>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="cumPro" role="tabpanel" aria-labelledby="cumPro-tab">
                <div class="card m-b-30">
                    <div class="card-body">
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-1">Filtre</legend>
                            <form id="form1" action="{{ route('pointVentePeriode') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="fw-bold">Agence</span>

                                        <select name="agence" type="text" class="form-select js-single  w-100" style="width: 100%;" id="agence3">
                                            {{-- @if (isset($agenceConnect) && $agenceConnect->id == 1)
                                                <option value="{{ $agenceConnect->id }}">{{ $agenceConnect->NomAgence }}</option>
                                            @endif
                                            @forelse ($listeAgence as $agence)
                                                <option value="{{ $agence->id }}">{{ $agence->NomAgence }}</option>
                                            @empty
                                            @endforelse --}}
                                            @foreach ($agences as $key=>$value)
                                                <option value="{{ $value->id }}">{{$value->NomAgence }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 ">
                                        <span class="fw-bold">Utilisateur</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <select name="utilisateur" type="text" class="form-select js-single w-100"
                                                style="width: 100%;" id="utilisateur" aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                                <option value="">Sélectionnez un utilisateur</option>

                                                @foreach ($utilisateur as $key => $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Du</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateDebut" class="form-control"
                                                id="dateDebut" max="{{ date('Y-m-d\TH:i') }}" required
                                                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="fw-bold">Au</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="datetime-local" name="dateFin" class="form-control" id="dateFin"
                                                max="{{ date('Y-m-d\TH:i') }}" required aria-label="Sizing example input"
                                                aria-describedby="inputGroup-sizing-sm">
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <span>&nbsp</span>
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="btn-group">
                                                <button type="submit" id="AppliquerForm1" class="btn text-white"
                                                    style="{{ background_color_1() }}">Appliquer</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div id="dateError1" class="alert alert-danger" style=" display:none;">La date de début
                                    doit être inférieure à la date de fin.</div>

                            </form>
                        </fieldset>
                    </div>
                </div>
                {{--   <button id="exportButtonMP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1"
                    class="btn text-white mt-2" style="{{ background_color_1() }}">Exporter en Excel</button>
                <button id="exportButtonMPP" data-bs-toggle="modal" data-bs-target="#staticBackdrop1P"
                    class="btn text-white mt-2" style="{{ background_color_2() }}">Exporter en PDF</button> --}}
                <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportExcel_MP" class="btn btn-sm btn-success">Oui Exporter en
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="staticBackdrop1P" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de cette statistique en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <button id="exportPDF_MP" target="_blank" class="btn btn-sm btn-success">Oui Exporter
                                    en PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{--
                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2" id="vente_de">


                        </div>

                    </div>
                </div> --}}



                <div class="card m-b-30 mb-5 mt-2">
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            {{--  <table id="tablePointProduit" class="table  tableInfo"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                            </table> --}}
                            <table id="tablePointProduit"
                                class="table datatable  table-striped table-bordered dt-responsive nowrap tableInfo">
                                <thead>
                                    <!-- L'en-tête sera rempli dynamiquement par JavaScript -->
                                </thead>
                                <tbody>
                                    <!-- Le corps du tableau sera rempli dynamiquement par JavaScript -->
                                </tbody>
                            </table>



                        </div>
                        <div id="req_message1" style="text-align: center; color: red; display: none;">
                            Veuillez faire une requette pour afficher les données
                        </div>
                    </div>
                </div>
            </div>
            <!-- Conteneur où le tableau sera injecté par le JS -->
            <div id="tableContainer"></div>



        </div>
        <script>
            const formIds = ['form1'];

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


        {{-- <script>
            $(document).ready(function() {
                var exportButtonMP = document.getElementById("exportButtonMP");
                var exportButtonMPP = document.getElementById("exportButtonMPP");
                var table = document.getElementById("tablePointProduit").getElementsByTagName("tbody")[0];

                // Déclaration d'une variable globale pour stocker la réponse AJAX
                var ajaxResponse;

                // Fonction pour vérifier si le tableau est vide
                function checkTable() {
                    if (table.rows.length === 0) {
                        exportButtonMP.disabled = true;
                        exportButtonMPP.disabled = true;
                        $('#req_message1').show();
                    } else {
                        exportButtonMP.disabled = false;
                        exportButtonMPP.disabled = false;
                        $('#req_message1').hide();
                    }
                }

                // Intercepter la soumission du formulaire
                $('#form1').on('submit', function(e) {
                    e.preventDefault(); // Empêcher le comportement par défaut du formulaire

                    var $form = $(this);
                    var $button = $form.find('button[type="submit"]');
                    $button.addClass('loading').prop('disabled', true);

                    $.ajax({
                        type: 'GET',
                        url: $form.attr('action'),
                        data: $form.serialize(),
                        dataType: 'json',
                        success: function(response) {
                            ajaxResponse = response; // Stocker la réponse
                            remplirTableauPointVenteOrdonne(response);
                            /*
                                            const utilisateur = response.utilisateur;

                                            const vente_de = document.querySelector('#vente_de');
                                            enteteTableau.innerHTML = `
                                       <h2>Vente de </h2>
                                    `; */
                            $button.removeClass('loading').prop('disabled', false);
                            checkTable();
                        },
                        error: function(xhr, status, error) {
                            console.error("Erreur AJAX:", error);
                            alert("Une erreur s'est produite lors de la récupération des données.");
                            $button.removeClass('loading').prop('disabled', false);
                        }
                    });
                });
            });

            function remplirTableauPointVenteOrdonne(donnees) {
                const corpsTableau = document.querySelector('#tablePointProduit tbody');
                corpsTableau.innerHTML = ''; // Effacer le contenu existant

                // Utiliser listeGroupeCat pour l'ordre des groupes
                const groupes = donnees.listeGroupeCat;

                // Extraire et trier les dates uniques
                const dates = [...new Set(donnees.pointVente.map(item => item.date_jour))].sort();

                // Créer l'en-tête du tableau
                const enteteTableau = document.querySelector('#tablePointProduit thead');
                enteteTableau.innerHTML = `
        <tr>
            <th  style="{{ background_color_2() }}">Date</th>
            ${groupes.map(groupe => `<th  style="{{ background_color_2() }}">${groupe.libelle}</th>`).join('')}
            <th  style="{{ background_color_2() }}">Total</th>
        </tr>
    `;

                // Remplir le tableau
                let grandTotal = 0;
                dates.forEach(date => {
                    const ligne = document.createElement('tr');
                    ligne.innerHTML = `<td>${date}</td>`;

                    let totalLigne = 0;
                    groupes.forEach(groupe => {
                        const vente = donnees.pointVente.find(item =>
                            item.date_jour === date &&
                            item.groupe_id === groupe.id
                        );
                        const montantVente = vente ? (vente.total_vente || 0) : 0;
                        totalLigne += montantVente;
                        ligne.innerHTML += `<td>${montantVente.toLocaleString()} </td>`;
                    });

                    ligne.innerHTML += `<td>${totalLigne.toLocaleString()} </td>`;
                    grandTotal += totalLigne;
                    corpsTableau.appendChild(ligne);
                });

                // Ajouter la ligne de total général
                const ligneTotalGeneral = document.createElement('tr');
                ligneTotalGeneral.innerHTML = `
        <td><strong>Total Général</strong></td>
        ${groupes.map(groupe => {
            const totalGroupe = donnees.pointVente
                .filter(item => item.groupe_id === groupe.id)
                .reduce((somme, item) => somme + (parseFloat(item.total_vente) || 0), 0);
            return `<td><strong>${totalGroupe.toLocaleString()} </strong></td>`;
        }).join('')}
        <td><strong>${grandTotal.toLocaleString()} </strong></td>
    `;
                corpsTableau.appendChild(ligneTotalGeneral);
            }

            function verifierTableau() {
                if ($('#tablePointProduit tbody tr').length === 0) {
                    alert("Aucune donnée disponible pour l'exportation.");
                }
            }
        </script> --}}

        <script>
        $(document).ready(function() {
    var exportButtonMP = document.getElementById("exportButtonMP");
    var exportButtonMPP = document.getElementById("exportButtonMPP");
    var table = document.getElementById("tablePointProduit").getElementsByTagName("tbody")[0];

    // Fonction pour vérifier si le tableau est vide
    function checkTable() {
        if (table.rows.length === 0) {
            exportButtonMP.disabled = true;
            exportButtonMPP.disabled = true;
            $('#req_message1').show();
        } else {
            exportButtonMP.disabled = false;
            exportButtonMPP.disabled = false;
            $('#req_message1').hide();
        }
    }

    // Intercepter la soumission du formulaire
    $('#form1').on('submit', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut du formulaire

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        $button.addClass('loading').prop('disabled', true);

        $.ajax({
            type: 'GET',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                console.log(response);  // Afficher la réponse dans la console pour déboguer
                remplirTableauPointVenteOrdonne(response); // Remplir le tableau avec les données reçues
                $button.removeClass('loading').prop('disabled', false);
                checkTable();
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX:", error);
                alert("Une erreur s'est produite lors de la récupération des données.");
                $button.removeClass('loading').prop('disabled', false);
            }
        });
    });
});

function remplirTableauPointVenteOrdonne(donnees) {
    const corpsTableau = document.querySelector('#tablePointProduit tbody');
    corpsTableau.innerHTML = ''; // Effacer le contenu existant

    // Utiliser listeGroupeCat pour l'ordre des groupes
    const groupes = donnees.listeGroupeCat;



    // Créer l'en-tête du tableau
    const enteteTableau = document.querySelector('#tablePointProduit thead');
    enteteTableau.innerHTML = `
        <tr>
             <th style="{{ background_color_2() }}; width: 20%">Période</th>
            ${groupes.map(groupe => `<th style="{{ background_color_2() }}">${groupe.libelle}</th>`).join('')}
            <th style="{{ background_color_2() }}">Total</th>
        </tr>
    `;

    // Remplir le tableau avec les ventes
    let grandTotal = 0;
    const ligne = document.createElement('tr');
    ligne.innerHTML = `
        <td>${donnees.dateDebut} au ${donnees.dateFin}</td>

    `;

    groupes.forEach(groupe => {
        const vente = donnees.pointVente.find(item => item.groupe_id === groupe.id);
        const montantVente = vente ? (vente.total_vente || 0) : 0;
        grandTotal += montantVente;
        ligne.innerHTML += `<td>${montantVente.toLocaleString()}</td>`;
    });

    ligne.innerHTML += `<td><strong>${grandTotal.toLocaleString()}</strong></td>`;
    corpsTableau.appendChild(ligne);
}


        </script>
    @endsection
