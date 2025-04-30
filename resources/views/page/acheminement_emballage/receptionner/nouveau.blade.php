@extends('layouts.master', ['title' => 'Creer Réception'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Nouveau Réception',
        'infos2' => 'Nouveau Réception',
        'infos3' => 'Liste',
    ])
    <style>
        .entete_tableau {
            background-color: #6104ed;
            color: white;
        }
    </style>

    <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
            <a href="{{ route('reception_acheminement') }}" class="btn text-white" style="{{ background_color_1() }}">
                <i class="fa fa-reply" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="card m-b-30">
        <div class="card-header rounded" style="{{ background_color_2() }}">
            <h4 class="mt-2 text-dark">
                Créer une Réception d'acheminement
            </h4>
        </div>
    </div>
    <section>
        <form id="formcreate" action="{{ route('store_reception_acheminementt_emballage') }}" method="post">
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
                        <legend class="float-none w-auto px-1">Agence</legend>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="validationTextarea" class="form-label fw-bold position-relative">Agence
                                        Source
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                        </span>
                                    </label>

                                    <div class="input-group input-group-md mb-3">
                                        <select name="client" type="text" class="form-select js-single"
                                            id="client-input" required>
                                            <option value="">Sélectionnez une agence</option>
                                            @foreach ($agences as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->NomAgence }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">L'agence est obligatoire</div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <label for="validationTextarea" class="form-label fw-bold position-relative">Observation
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                                        </span>
                                    </label>

                                    <div class="input-group input-group-md mb-3">
                                        <textarea class="form-control" name="observation" id="" cols="2" rows="2" required></textarea>
                                        <div class="invalid-feedback">L'observation est obligatoire</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3" id="table">
                        <legend class="float-none w-auto px-1">Acheminement</legend>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="num_facture">Acheminement</label>
                                <div class="input-group input-group-sm mb-3">
                                    <select name="" type="text" class="form-select produit-select js-single"
                                        id="num_facture" value="{{ old('num_facture') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                        <option value="">N° Acheminement</option>
                                        @foreach ($acheminements as $key => $value)
                                            <option value="{{ $value->id }}-{{ $value->Reference_acheminement }}">
                                                {{ $value->Reference_acheminement }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="produit">Produit</label>
                                <div class="input-group input-group-sm mb-3">
                                    <select name="" type="text"
                                        class="form-select produit-select-produit js-single" id="produit"
                                        value="{{ old('produit') }}" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                        <option value="">Produit</option>
                                        {{-- @foreach ($approvisionnements as $key => $value)
                                            <option value="{{ $value->id }}-{{ $value->Reference_Approvisionnement }}">
                                                {{ $value->Reference_Approvisionnement }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="net_a_payer">Qte Approvisionnée</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input step="0.01" type="text" name=""
                                        class="form-control net-a-payer-input" value="{{ old('net_a_payer') }}"
                                        id="net_a_payer" placeholder="Net à payer..." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="montant_regle">Qté Réceptionnée</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="" value="{{ old('montant_regle') }}"
                                        id="montant_regle" class="form-control montant-regle-input"
                                        placeholder="Montant réglé..." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="reste_a_payer">Reste à Réceptionner</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="" class="form-control reste-a-payer-input"
                                        value="{{ old('reste_a_payer') }}" id="reste_a_payer" readonly
                                        placeholder="Reste à payer...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="mode_reglement">Magasin</label>
                                <div class="input-group input-group-sm mb-3">
                                    <select name="" type="text" class="form-select js-single"
                                        id="mode_reglement" value="{{ old('mode_reglement') }}"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                        <option value="">Magasin</option>
                                        @foreach (magasins() as $key => $value)
                                            <option value="{{ $value->id }}-{{ $value->NomMagasin }}">
                                                {{ $value->NomMagasin }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="montant">Quatite</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="" class="form-control" placeholder="Montant"
                                        value="{{ old('montant') }}" id="montant">
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add" name="add" class="btn text-white w-auto float-end"
                            style="{{ background_color_1() }}"><span class="fw-bold">+</span> Ajouter</button>
                    </fieldset>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog g modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                        <legend class="float-none w-auto px-1">Récap</legend>
                        <div class="table-responsive mt-3 overflow-y-scroll " style="max-height: 270px;">
                            <div class="col-md-12">
                                <table class="table table-bordered" id="table2">
                                    <thead class="table-primary" style="position: sticky; top:0%; z-index:1;">
                                        <tr>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                N° Approv </th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Produit</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Magasin</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                                Quantite à réceptionner</th>
                                            <th style="{{ background_color_2() }}" scope="col">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Lignes du deuxième tableau seront ajoutées ici -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </fieldset>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <fieldset class="border p-3 rounded-3">
                        <legend class="float-none w-auto px-1">Total</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="" class="fw-bold">Montant total</label>
                                <input class="form-control border-0" type="text" id="prixAchatTotal" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="fw-bold">Nombre de ligne</label>
                                <input class="form-control border-0" type="text" id="nb_entree" readonly>
                            </div>
                        </div>
                    </fieldset>
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
            $('#agence_input').change(function() {
                var agenceId = $(this).val();

                // Vérifier si un client est sélectionné
                if (agenceId) {
                    $.ajax({
                        url: '/getAcheminementDeAgence/' + agenceId,
                        type: 'GET',
                        success: function(data) {
                            $('#acheminement_imput').empty(); // Vider le select
                            $('#acheminement_imput').append(
                                '<option value="">Sélectionnez  un acheminement</option>'
                            );

                            // Parcourir les données et remplir le select
                            $.each(data, function(key, acheminements) {
                                $('#acheminement_imput').append('<option value="' +
                                acheminements.id + '">' + acheminements
                                    .Reference_acheminement + '</option>');
                            });

                        }
                    });
                } else {
                    $('#acheminement_imput').empty();
                    $('#acheminement_imput').append(
                        '<option value="">Sélectionnez un acheminement</option>');
                }
            });
        });
    </script>
  <script>
    $(document).ready(function() {
        $('#formcreate').on('submit', function() {
            var $button = $('#confirmcreate');

            // Désactiver le bouton et ajouter la classe loading
            $button.prop('disabled', true);
            $button.addClass('loading');

            // Afficher le texte ou l'indicateur de chargement
            $('#loadingSpinner').show();

            // Laisser le formulaire continuer à être soumis normalement
            return true;
        });
    });

    $(document).ready(function() {
        // Utiliser la variable PHP $factures dans un script jQuery
        var acheminements = @json($acheminements); // Convertir la variable PHP en JSON

        // Ajouter un écouteur d'événements au champ de sélection du client
        $('select[name="client"]').change(function() {
            var agenceId = $(this).val(); // Récupérer l'ID du client sélectionné
            var numFactureSelect = $('#num_facture'); // Sélecteur du champ select des factures


            // Filtrer les factures en fonction du client sélectionné
            var approvAgence = acheminements.filter(function(approv) {
                return approv.Id_Agence_Source == agenceId;
            });

            // console.log('approvAgence',approvAgence);
            // Vider le champ select des factures
            numFactureSelect.empty();
            // Ajouter une option par défaut
            numFactureSelect.append('<option value="">N° Facture</option>');

            // Ajouter chaque facture correspondant au client sélectionné comme option dans le champ select
            $.each(approvAgence, function(index, facture) {
                numFactureSelect.append('<option value="' + facture.id + '-' + facture
                    .Reference_acheminement + '">' + facture
                    .Reference_acheminement + '</option>');
            });
        });
    });

    $(document).ready(function() {
        // Désactiver le champ N° Facture au chargement de la page
        $('#num_facture').prop('disabled', true);

        // Ajouter un écouteur d'événements au champ de sélection du client
        $('select[name="client"]').change(function() {
            var clientId = $(this).val(); // Récupérer l'ID du client sélectionné
            var numFactureSelect = $('#num_facture'); // Sélecteur du champ select des factures

            // Vérifier si un client est sélectionné
            if (clientId) {
                // Activer le champ N° Facture si un client est sélectionné
                numFactureSelect.prop('disabled', false);
            } else {
                // Désactiver le champ N° Facture si aucun client n'est sélectionné
                numFactureSelect.prop('disabled', true);
            }
        });
    });
    $(document).ready(function() {
        // Fonction pour activer/désactiver le bouton Valider en fonction des champs fournisseur et observation
        function toggleSubmitButton() {
            var client = $('select[name="client"]').val();
            var observation = $('textarea[name="observation"]').val();

            // Si le fournisseur et l'observation sont remplis, activer le bouton Valider, sinon le désactiver
            if (observation && client) {
                $('#bouton-valider').prop('disabled', false);
            } else {
                $('#bouton-valider').prop('disabled', true);
            }
        }

        // Surveiller les événements de changement dans les champs fournisseur et observation
        $('textarea[name="observation"], select[name="client"]').on('input', function() {
            toggleSubmitButton();
        });

        // Appeler la fonction une fois que le document est prêt pour initialiser l'état du bouton
        toggleSubmitButton();
    });



    $(document).ready(function() {

        // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
        // Fonction pour mettre à jour la désignation en fonction de la référence sélectionnée
        function updateDesignation() {
            var acheminements = {!! json_encode($acheminers) !!};
            var receptionners = {!! json_encode($receptionners) !!};

            $('.produit-select-produit').each(function() {
                var produit_magasin = $(this).val();
                var parties = produit_magasin.split("|");
                var selectedReference = parties[1];
                var idApprovisionner = parties[0];

                // console.log('receptionners', receptionners)
                // console.log('acheminements', acheminements)
                // console.log('idApprovisionner', idApprovisionner)

                var row = $(this).closest('.row');
                var netAPayer = row.find('.net-a-payer-input');
                var montantRegle = row.find('.montant-regle-input');
                var resteAPayer = row.find('.reste-a-payer-input');

                var getFacture = acheminements.find(function(element) {
                    return element.id === parseInt(idApprovisionner);
                });

                // console.log('getFacture iciii', getFacture)

                if (receptionners.length > 0) {
                    var reglementsFacture = receptionners.filter(function(element) {
                        return element.Id_Acheminer === parseInt(idApprovisionner);
                    });

                    // console.log('reglementsFacture', reglementsFacture)

                    var sommeMontantsRegles = reglementsFacture.reduce(function(total, reglement) {
                        return total + parseFloat(reglement.Qte_Receptionnee);
                    }, 0);

                    console.log('sommeMontantsRegles', sommeMontantsRegles)

                    if (getFacture) {
                        netAPayer.val(getFacture.Qte_acheminee);
                        montantRegle.val(sommeMontantsRegles.toFixed(2));
                        resteAPayer.val((getFacture.Qte_acheminee - sommeMontantsRegles).toFixed(
                            2));
                    } else {
                        netAPayer.val('');
                        montantRegle.val('');
                        resteAPayer.val('');
                    }
                } else {
                    if (getFacture) {
                        netAPayer.val(getFacture.Qte_acheminee);
                        montantRegle.val('0.00');
                        resteAPayer.val(getFacture.Qte_acheminee);
                    } else {
                        netAPayer.val('');
                        montantRegle.val('');
                        resteAPayer.val('');
                    }
                }
            });
        }


        // Gérer les événements de changement sur les sélecteurs de produits
        $(document).on('change', '.produit-select-produit', function() {
            updateDesignation();
        });
        // Appeler la fonction pour mettre à jour la désignation lorsque le document est prêt
        updateDesignation();
    });

    $(document).ready(function() {
        var totalMontantPayer = 0;
        var rowCountAdd = 0;

        function clearFormFields() {
            $('#num_facture').val(null).trigger('change');
            $('#net_a_payer').val('');
            $('#montant_regle').val('');
            $('#reste_a_payer').val('');
            $('#mode_reglement').val(null).trigger('change');
            $('#montant').val('');
        }

        function toggleSubmitButton() {
            if ($('#table2 tbody tr').length > 0) {
                $('#bouton-valider').show(); // Afficher le bouton Valider
            } else {
                $('#bouton-valider').hide(); // Cacher le bouton Valider
            }
        }
        toggleSubmitButton()

        var totalMontant = [];
        // Ajouter une nouvelle ligne au deuxième tableau
        $('#add').click(function() {
            var numFacture = $('#num_facture').val();
            var produit = $('#produit').val();
            var netAPayer = $('#net_a_payer').val();
            var montantRegle = $('#montant_regle').val();
            var resteAPayer = $('#reste_a_payer').val();
            var modeReglement = $('#mode_reglement').val();
            var montant = parseFloat($('#montant').val());



            // console.log('quantitejksj', montant)

                // Variable pour stocker les lignes du tableau
                let lignesTableau = [];

                // Parcourir chaque ligne du tbody du tableau avec id 'table2'
                $('#table2 tbody tr').each(function() {
                    // Récupérer les cellules de la ligne actuelle
                    let ligne = {
                        numeroApprov: $(this).find('td:eq(0) input').val(),
                        produit: $(this).find('td:eq(1) input').val(),
                        magasin: $(this).find('td:eq(2) input').val(),
                        quantiteReceptionnee: parseFloat($(this).find('td:eq("3") input')
                            .val()) || montant
                        // Conversion en nombre
                    };

                    // Ajouter la ligne au tableau
                    lignesTableau.push(ligne);
                });

                // Afficher les données dans la console (facultatif)
                // console.log('lignesTableau', lignesTableau);
                // console.log('lignesTableau.produit', lignesTableau.produit);

                // Filtrer et faire la somme des quantités réceptionnées pour un produit spécifique
                let produitRecherche = $('#produit').val(); // Nom du produit à rechercher
                // console.log('produitRecherche:', produitRecherche);

                // Filtrer les lignes pour obtenir uniquement celles qui correspondent au produit recherché
                let lignesFiltrees = lignesTableau.filter(ligne => ligne.produit === produitRecherche);

                // Variable pour la première somme
                let premierAjout = true;

                // Calcul de la somme avec vérification du premier ajout
                let sommeQuantites = lignesFiltrees.reduce((total, ligne) => {
                    if (resteAPayer < montant) {
                        alert(
                            'La quantité réceptionnée est supérieur à la quantité approvisionnée.');
                        return sommeQuantites;
                    }

                    if (premierAjout) {
                        // Ajouter la valeur de 'montant' comme première valeur au total
                        total += montant;
                        premierAjout = false; // Désactiver le contrôle après le premier ajout
                    }


                    return total + ligne
                        .quantiteReceptionnee;


                    // Ajouter la quantité réceptionnée des lignes filtrées
                }, 0);

                // // Afficher pour vérification (optionnel)
                // console.log('Somme des quantités réceptionnées:', sommeQuantites);


                // // Afficher pour vérification (optionnel)
                // console.log('Lignes filtrées:', lignesFiltrees);
                // console.log('Lignes QUANTITE:', lignesFiltrees['quantiteReceptionnee']);
                // console.log('Somme des quantités réceptionnées:', sommeQuantites);


                // Afficher la somme dans la console ou sur la page
                // console.log('Somme des quantités pour le produit', produitRecherche, ':',
                //     sommeQuantites);

                if (sommeQuantites === 0) {
                    if (resteAPayer < montant) {
                        alert('La quantité réceptionnée est supérieur à la quantité approvisionnée.');
                        return 0;
                    } else {
                        sommeQuantites = montant;
                    }
                }

                // console.log('nOUVELLE VALEUR', produitRecherche, ':',
                //     sommeQuantites);

            totalMontant.push(montant);

            // Calculer la somme des montants
            var sum = totalMontant.reduce(function(acc, val) {
                return acc + val;
            }, 0);

            // console.log('sum', totalMontant.push(montant))

            if (numFacture === '' || produit === '' || modeReglement === '' || montant === '' || netAPayer === '' ||
                resteAPayer ===
                '') {
                alert('Veuillez remplir tous les champs.');
                return false;
            }
            console.log('resteAPayer', resteAPayer)

            // Vérifier si le montant est valide
            if (!isNaN(montant)) {
                if (sommeQuantites > resteAPayer) {
                    // Afficher une modal de confirmation
                    if (alert(
                            'La quantité réceptionnée est supérieur à la quantité acheminé.'
                        )) {
                        // Si l'utilisateur confirme, continuer le processus
                        // updateOrAddRow(numFacture, produit, netAPayer, montantRegle, resteAPayer, modeReglement,
                        //     montant);
                        // console.log()

                        clearFormFields();
                        toggleSubmitButton();
                    } else {
                        // Si l'utilisateur ne confirme pas, ne rien faire
                        // Vous pouvez ajouter ici une action si nécessaire
                        // Mettre à jour le tableau totalMontant en retirant le montant supprimé
                        var index = totalMontant.indexOf(montant);
                        if (index !== -1) {
                            totalMontant.splice(index, 1); // Retirer le montant du tableau
                        }

                        // Recalculer le total des montants restants
                        var sum = totalMontant.reduce(function(acc, val) {
                            return acc + val;
                        }, 0);
                        return false;

                    }
                } else {
                    // Montant valide et inférieur ou égal au reste à payer
                    updateOrAddRow(numFacture, produit, netAPayer, montantRegle, resteAPayer, modeReglement,
                        montant);
                    clearFormFields();
                    toggleSubmitButton();
                }
            } else {
                // Afficher un message d'erreur si le montant n'est pas valide
                alert('Veuillez entrer un montant valide.');
            }
        });

        // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
        function updateOrAddRow(numFacture, produit,  netAPayer, montantRegle, resteAPayer, modeReglement,
            montant) {
            var rows = $('#table2 tbody tr');
            var rowToUpdate = null;

            // Parcourir chaque ligne du tableau
            rows.each(function() {
                var row = $(this);
                var rowNumFacture = row.find('td:eq(0) input').val();
                var rowModeReglement = row.find('td:eq(1) input').val();

                // Comparer les données de la nouvelle ligne avec celles des lignes existantes
                if (rowNumFacture === numFacture && rowModeReglement === modeReglement) {
                    rowToUpdate = row;
                    return false; // Sortir de la boucle si une correspondance est trouvée
                }
            });

            // Si une ligne existe, mettre à jour les données
            if (rowToUpdate !== null) {
                $('#confirmationModal').modal('show');

                // Lorsque l'utilisateur clique sur "Continuer"
                $('#continueButton').click(function() {
                    totalMontantPayer = parseFloat(montant);
                    console.log(totalMontant.push(montant));

                    $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));
                    var updatedMontant = parseFloat(rowToUpdate.find('td:eq(2) input').val() *
                            0) +
                        parseFloat(montant);
                    rowToUpdate.find('td:eq(2) input').val(updatedMontant.toFixed(2));

                    // Fermer le modal
                    $('#confirmationModal').modal('hide');
                });

                // Lorsque l'utilisateur clique sur "Annuler" ou ferme le modal
                $('#cancelButton').click(function() {
                    // Ne rien faire
                    // Fermer le modal
                    $('#confirmationModal').modal('hide');
                });
            } else {
                // Ajouter la nouvelle ligne si aucune correspondance n'est trouvée
                var rowCount = $('#table2 tbody tr').length + 1;
                $('#nb_entree').val(rowCount);
                totalMontantPayer += parseFloat(montant);
                $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));
                rowCountAdd++;
                var newRow = `<tr>
            <td>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" name="inputs[${rowCount}][num_facture]" class="form-control border-0" value="${numFacture}" readonly>
                </div>
            </td>
             <td>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0" value="${produit}" readonly>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" name="inputs[${rowCount}][mode_reglement]" class="form-control border-0" value="${modeReglement}" readonly>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm mb-3">
                    <input type="text" name="inputs[${rowCount}][montant]" class="form-control border-0" value="${montant}" readonly>
                </div>
            </td>
            <td>
                <button class="btn btn-danger btn-sm remove-row">X</button> <!-- Bouton Supprimer -->
            </td>
        </tr>`;
                $('#table2 tbody').append(newRow);
            }
        }

        // Supprimer une ligne du deuxième tableau
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            var montant = parseFloat($(this).closest('tr').find('td:eq(2) input').val());

            // Mettre à jour le tableau totalMontant en retirant le montant supprimé
            var index = totalMontant.indexOf(montant);
            if (index !== -1) {
                totalMontant.splice(index, 1); // Retirer le montant du tableau
            }

            // Recalculer le total des montants restants
            var sum = totalMontant.reduce(function(acc, val) {
                return acc + val;
            }, 0);

            // Mettre à jour l'affichage du total si nécessaire
            // console.log('Total des montants restants:', sum);

            // Mise à jour du nombre de lignes restantes
            rowCountAdd--;
            $('#nb_entree').val(rowCountAdd);

            // Mise à jour du total du montant à payer
            totalMontantPayer -= montant;
            $('#prixAchatTotal').val(totalMontantPayer.toFixed(2));

            // Vérifier s'il n'y a plus de lignes dans le tableau
            if (rowCountAdd == 0) {
                $('#bouton-valider').hide(); // Cacher le bouton Valider s'il n'y a pas de lignes
            }
        });
    });

    $(document).ready(function() {
        // Gérer le changement de catégorie
        $('#num_facture').on('change', function() {
            var selectedCategoryId = $(this).val();

            // Vider le menu déroulant des produits avant de l'actualiser
            $('#produit').empty();
            $('#produit').append('<option value="">Selectionnez un produit</option>');

            if (selectedCategoryId) {
                $.ajax({
                    url: '/get-acheminement-reception-emballage',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_id: selectedCategoryId
                    },
                    success: function(response) {
                        console.log('Réponse:', response);
                        if (response.length > 0) {
                            response.forEach(function(produit) {
                                $('#produit').append('<option value="' +
                                    produit.id + '|' +
                                    produit.Id_Stock_Emballage + '|' + produit.Designation +
                                    '">' +
                                    produit.Reference + '(' + produit.Designation + ')' +
                                    '</option>'
                                );
                            });
                        } else {
                            $('#produit').append(
                                '<option value="">Aucun produit disponible</option>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des produits.');
                    }
                });
            } else {
                $('#produit').append('<option value="">Tous</option>');
            }
        });
    });
</script>

<script>
    const formIds = ['formcreate'];

    function preventEnterSubmission(formId) {
        document.getElementById(formId).addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    }
    formIds.forEach(preventEnterSubmission);
</script>

@endSection
