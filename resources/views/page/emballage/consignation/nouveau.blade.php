@extends('layouts.master', ['title' => 'Régler Consignation'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Régler Consignation',
        'infos2' => 'Régler Consignation',
        'infos3' => 'Consignation',
    ])
    <style>
        .entete_tableau {
            background-color: #6104ed;
            color: white;
        }
    </style>

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
            <a href="{{ route('consignation' )}}" class="btn text-white" style="{{ background_color_1() }}">
                <i class="fa fa-reply" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark">
                Créer une consignation
            </h4>
        </div>
    </div>
    <section>
        <form id="formcreate" action="{{ route('storeDeconsignation') }}" method="post">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Client</legend>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="validationTextarea" class="form-label fw-bold position-relative">Client
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                    </span>
                                </label>

                                <div class="input-group input-group-md mb-3">
                                    <select name="client_id" type="text" class="form-select js-single" id="client_input"
                                        required>
                                        <option value="">Sélectionnez un client</option>
                                        @foreach ($clients as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->Denomination_sociale }} ({{ $value->Numero_ifu }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Le client est obligatoire</div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="validationTextarea" class="form-label fw-bold position-relative">Consignation
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                    </span>
                                </label>

                                <div class="input-group input-group-md mb-3">
                                    <select name="consignation_id" class="form-select js-single" id="consignation_imput"
                                        required>
                                        <option value="">Sélectionnez une facture de consignation</option>

                                    </select>
                                    <div class="invalid-feedback">La facture est obligatoire</div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog g modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Souhaitez-vous vraiment faire cette action ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" id="confirmcreate" class="btn btn-primary">Continuer</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirmation de la
                                modification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Êtes-vous sûr de vouloir modifier le montant ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="continueButton">Confirmer</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" mt-2">
                <div class="col-md-12">
                    <div class="d-flex p-2">

                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Lignes</legend>
                        <div class="table-responsive mt-3 overflow-y-scroll " style="max-height: 270px;">
                            <div class="col-md-12">
                                <table class="table table-bordered" id="tableLignes">
                                    <thead class="table-primary" style="position: sticky; top:0%; z-index:1;">
                                        <tr>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                #</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Designation</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Quantité</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Restitué</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Facturé
                                            </th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Nouvelle Qte à restituer
                                            </th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Nouvelle Qte à facturer
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">

                    <div class="table-responsive mt-3 overflow-y-scroll " style="max-height: 270px;">
                        <table id="secondTable" class="table">
                            <thead>
                                <tr>
                                    <th>Nom de l'emballage</th>
                                    <th>Quantité facturée</th>
                                    <th>Groupe de Taxation</th>
                                    <th>Prix HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Les nouvelles lignes seront ajoutées ici dynamiquement -->
                            </tbody>
                        </table>

                    </div>


                </div>
            </div>

            <div class="mt-3" style="margin-bottom: 145px;">
                <div class="card-body">
                    <button type="button" class="btn text-white d-block ms-auto" id="bouton-valider"
                        data-bs-toggle="modal" style="{{ background_color_1() }}" data-bs-target="#staticBackdrop"
                        class="btn btn-sm btn-primary text-end">VALIDER</button>
                </div>
            </div>
        </form>
    </section>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#client_input').change(function() {
                var clientId = $(this).val();

                // Vérifier si un client est sélectionné
                if (clientId) {
                    $.ajax({
                        url: '/getConsignationDuClient/' + clientId,
                        type: 'GET',
                        success: function(data) {
                            $('#consignation_imput').empty(); // Vider le select
                            $('#consignation_imput').append(
                                '<option value="">Sélectionnez une facture de consignation</option>'
                            );

                            // Parcourir les données et remplir le select
                            $.each(data, function(key, consignation) {
                                $('#consignation_imput').append('<option value="' +
                                    consignation.id + '">' + consignation
                                    .ref_facture + '</option>');
                            });

                        }
                    });
                } else {
                    $('#consignation_imput').empty();
                    $('#consignation_imput').append(
                        '<option value="">Sélectionnez une facture de consignation</option>');
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#client_input, #consignation_imput').change(function() {
                // Vider les tableaux à chaque changement
                $('#tableLignes tbody').empty();  // Vider le tableau principal
                $('#secondTable tbody').empty();  // Vider le second tableau
            });

            $('#consignation_imput').change(function() {
                var consignationId = $(this).val();

                // Vérifier si un client est sélectionné
                if (consignationId) {
                    $.ajax({
                        url: '/getLigneConsignationSelectionnee/' + consignationId,
                        type: 'GET',
                        success: function(data) {
                            var tbody = $('#tableLignes tbody');
                            tbody.empty(); // Vider le corps du tableau


                            $.each(data.ligneConsignation, function(key, ligneConsignation) {

                                var quantiteRestante = ligneConsignation.Qte -
                                    ligneConsignation.restituee - ligneConsignation
                                    .facturee;
                                tbody.append(
                                    '<tr>' +
                                    '<td>' +
                                    '<input type="number" name="id_ligne_consignation[]" class="form-control " style="width:50px" readonly  value="' +
                                    ligneConsignation.id + '" />' +
                                    '</td>' +
                                    '<td>' + ligneConsignation.Nom_emballage +
                                    '=>' + ligneConsignation.Designation +
                                    '</td>' +
                                    '<td>' + ligneConsignation.Qte + '</td>' +
                                    '<td>' + ligneConsignation.restituee + '</td>' +
                                    '<td>' + ligneConsignation.facturee + '</td>' +
                                    '<td>' +
                                    '<input type="number" name="nouvelle_qte_restituer[]" class="form-control" placeholder="Qte à restituer" data-quantite-restante="' +
                                    quantiteRestante + '" max="' +
                                    quantiteRestante + '" />' +
                                    '<small class="text-danger quantite-restituer-error" style="display:none;">La somme de la qte à restituer et facturer ne doivent pas dépasser ' +
                                    quantiteRestante + '</small>' +
                                    '</td>' +

                                    '<td><input type="number" name="nouvelle_qte_facturer[]" class="form-control" value="0" placeholder="Qte à facturer"data-quantite-restante="' +
                                    quantiteRestante + '" max="' +
                                    quantiteRestante + '" />' +
                                    '<small class="text-danger quantite-restituer-error" style="display:none;">La somme de la qte à restituer et facturer ne doivent pas dépasser ' +
                                    quantiteRestante + '</small>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                            $(document).on('input',
                                'input[name="nouvelle_qte_restituer[]"], input[name="nouvelle_qte_facturer[]"]',
                                function() {
                                    // Récupérer la ligne actuelle
                                    var row = $(this).closest('tr');

                                    // Récupérer les valeurs des deux champs
                                    var qteRestituer = parseFloat(row.find(
                                            'input[name="nouvelle_qte_restituer[]"]')
                                        .val()) || 0;
                                    var qteFacturer = parseFloat(row.find(
                                            'input[name="nouvelle_qte_facturer[]"]')
                                        .val()) || 0;
                                    console.log('ici', qteRestituer, qteFacturer);

                                    // Récupérer la quantité restante depuis l'attribut data
                                    var quantiteRestante = parseFloat(row.find(
                                            'input[name="nouvelle_qte_restituer[]"]')
                                        .data('quantite-restante')) || 0;

                                    var somme = qteRestituer + qteFacturer;
                                    console.log('somme', somme);

                                    // Vérifier si la somme est égale à la quantité restante
                                    if (somme > quantiteRestante) {
                                        // Si la somme est incorrecte, afficher l'erreur
                                        row.find('.quantite-restituer-error').show();
                                        row.find(
                                            'input[name="nouvelle_qte_restituer[]"], input[name="nouvelle_qte_facturer[]"]'
                                        ).addClass('is-invalid');
                                    } else {
                                        // Si la somme est correcte, masquer l'erreur
                                        row.find('.quantite-restituer-error').hide();
                                        row.find(
                                            'input[name="nouvelle_qte_restituer[]"], input[name="nouvelle_qte_facturer[]"]'
                                        ).removeClass('is-invalid');
                                    }
                                });

                            // Au lieu d'utiliser 'input', j'utilise 'change' pour déclencher lors du changement de valeur
                            $(document).on('change', 'input[name="nouvelle_qte_facturer[]"]',
                                function() {
                                    var row = $(this).closest(
                                    'tr'); // Récupérer la ligne du tableau
                                    var qteFacturer = parseInt($(this).val()) || 0;
                                    var ligneConsignationId = row.find(
                                        'input[name="id_ligne_consignation[]"]')
                                .val(); // Id de la ligne consignation
                                    var nomEmballage = row.find('td:nth-child(2)')
                                .text(); // Nom de l'emballage

                                    // Si la quantité facturée est > 0
                                    if (qteFacturer > 0) {
                                        // Vérifier si la ligne existe déjà dans le second tableau
                                        var existingRow = $('#secondTable tbody').find(
                                            'tr[data-ligne-id="' + ligneConsignationId +
                                            '"]');

                                        if (existingRow.length > 0) {
                                            // Si la ligne existe, on la met à jour
                                            existingRow.find('.qte-facturer').text(
                                                qteFacturer);
                                        } else {
                                            // Si la ligne n'existe pas, on l'ajoute
                                            var newRow = generateNewRowForSecondTable(
                                                ligneConsignationId, nomEmballage,
                                                qteFacturer);
                                            $('#secondTable tbody').append(newRow);
                                        }
                                    } else {
                                        // Si la quantité facturée est 0, retirer la ligne du second tableau si elle existe
                                        $('#secondTable tbody').find('tr[data-ligne-id="' +
                                            ligneConsignationId + '"]').remove();
                                    }
                                });

                            // Fonction pour générer une nouvelle ligne pour le second tableau
                            function generateNewRowForSecondTable(ligneConsignationId,
                                nomEmballage, qteFacturer) {
                                var newRowHtml = '<tr data-ligne-id="' + ligneConsignationId +
                                    '">' +
                                    '<td>' + nomEmballage + '</td>' +
                                    '<td class="qte-facturer"><input type="number" name="nouvelle_qte_facturer_x[' + ligneConsignationId +']" class="form-control" value="' + qteFacturer + '" readonly required /></td>' +
                                    '<td>' +
                                    '<select name="groupe_taxation[' + ligneConsignationId + ']" class="form-control" required>';

                                    // Boucler à travers les groupes de taxation pour remplir les options du select
                                    $.each(data.groupesTaxation, function(index, groupe) {
                                        newRowHtml += '<option value="' + groupe.id + '">' + groupe.Code_lettre + '</option>';
                                    });

                                    newRowHtml += '</select>' +
                                    '</td>' +
                                    '<td><input type="number" name="prix_ht[' +ligneConsignationId +']" class="form-control" placeholder="Prix HT" required /></td>' +
                                    '</tr>';
                                return newRowHtml;
                            }



                        }
                    });
                } else {
                    $('#consignation_imput').empty();
                    $('#consignation_imput').append(
                        '<option value="">Sélectionnez une facture de consignation</option>');
                }
            });
        });
    </script>
@endSection
