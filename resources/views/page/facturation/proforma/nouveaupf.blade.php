@extends('layouts.master', ['title' => 'Créer Proforma'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Proforma',
'infos2' => 'Proforma',
'infos3' => 'Nouveau',
])
<section>

  <style>
    .btn-check:checked+.btn,
    .btn.active,
    .btn.show,
    .btn:first-child:active,
    :not(.btn-check)+.btn:active {
      background-color: #FFA500 !important;
      border-color: #FFA500 !important;
      color: #000 !important;
    }

    .btn_aib,
    .btn_aib:hover {
      border-color: #FFA500;
    }

  </style>

  <div class="d-flex flex-row-reverse bd-highlight">
    <div class="dropdown mb-2">
      <a href="{{ route('proforma') }}" class="btn text-white" style="{{ background_color_1() }}">
        <i class="fa fa-reply" aria-hidden="true"></i>
        Retour
      </a>
    </div>
  </div>

  <div class="card m-b-30">
    <div class="card-header rounded" style="{{background_color_2()}}">
      <h4 class="mt-2 text-dark">
        Créer un proforma
      </h4>
    </div>
  </div>
  <form id="formX" action="{{ route('storeProforma') }}" method="POST" class="my-5">
    @csrf
    <div class="card-body">
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
    </div>
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmation</h1>
            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
          </div>
          <div class="modal-body">
            Souhaitez-vous vraiment créer ce proforma ?
          </div>
          <div class="modal-footer">
            <button id="cancel-button" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" id="save-button" class="btn btn-primary">Continuer</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card m-b-30">
      <div class="card-body">
        <fieldset class="border p-3 rounded-3">
          <legend class="float-none w-auto px-1">Client</legend>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="validationTextarea" class="form-label fw-bold position-relative">Client
                <span class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*
                </span>
              </label>
              <select class="form-select js-single" name="client_id" required id="client">
                <option></option>
                @forelse ($listeClient as $client)
                <option value="{{ $client->id }}" data-ifu="{{ $client->Numero_ifu }}" data-cat_client="{{ $client->Categorie_client_id }}">{{ $client->Denomination_sociale }} ({{ $client->Numero_ifu }})
                </option>
                @empty
                @endforelse
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="validite" class="form-label fw-bold position-relative">Validité
                <span class="position-absolute top-0 start-100 translate-middle p-2 text-danger ms-1 mt-2">*</span>
              </label>
              <input type="text" id="validite" name="Validite" class="form-control" required>
              <div id="error-message" class="text-danger mt-2" style="display: none;">Veuillez remplir ce champ.</div>
            </div>

            <div class="form-group col-md-6">
              <label for="validationTextarea" class="form-label fw-bold">Objet </label>
              <textarea name="Objet_facture" class="form-control" rows="1" id=""></textarea>
            </div>
            <div class="form-group col-md-6">
              <label for="validationTextarea" class="form-label fw-bold">Commentaire</label>
              <textarea name="Commentaire" class="form-control" rows="1" id=""></textarea>
            </div>
            <div class="form-group col-md-12">
              <label for="validationTextarea" class="form-label fw-bold">Autre infomation</label>
              <textarea name="Autres_infos" class="form-control" rows="1" id=""></textarea>
            </div>
          </div>
        </fieldset>
      </div>
    </div>
    <div class="card m-b-30">
      <div class="card-body">
        <fieldset class="border p-3 rounded-3">
          <legend class="float-none  w-auto px-1">Config Proforma</legend>
          <div class="row">
            <div class="col-md-6">
              <label for="validationTextarea" class="form-label fw-bold">Facturer l'AIB</label>
              <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check AibFacturer-select" disabled name="Aib" value="0" id="Aib0" @if (aibPrecocher() == 0) checked @endif >
                <label class="btn btn_aib" for="Aib0">NON</label>

                <input type="radio" class="btn-check AibFacturer-select" disabled name="Aib" value="1" id="Aib1" @if (aibPrecocher() == 1) checked @endif>
                <label class="btn btn_aib" for="Aib1">OUI</label>
              </div>

            </div>
            <div class="col-md-6">
              <label for="validationTextarea" class="form-label fw-bold">AIB déductible</label>
              <select class="form-select" name="Aib_deductible" id="Aib_deductible" aria-label="Default select example">
                <option value=""></option>
                <option value="1">1%</option>
                <option value="3">3%</option>
                <option value="5">5%</option>
              </select>
            </div>
            <input type="hidden" name="hiddenAib_deductible" id="hiddenAib_deductible">

          </div>
        </fieldset>
      </div>
    </div>
    <div class="card m-b-30">
      <div class="card-body">
        <fieldset class="border p-3 rounded-3" id="table">
          <legend class="float-none w-auto px-1">Produit</legend>
          <div class="row">
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">produit/prestation</label>
              <div class="input-group input-group-sm mb-3">
                <select name="" type="text" class="form-select produit-select js-single" id="produit" value="{{ old('produit') }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
                  <option value="">Sélectionnez un produit</option>
                  @foreach ($produits as $produit)
                  <option value="{{ $produit->Reference }}" data-designation="{{ $produit->Designation }}" data-type="{{ $produit->Type }}" data-produit_id="{{ $produit->id }}">
                    {{ $produit->Designation }}({{ $produit->Reference }})</option>
                  @endforeach
                </select>
              </div>
              <i class="text-danger produit-message">Choisissez le client pour filtrer</i>
            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">Désignation</label>

              <div class="input-group input-group-sm mb-3">
                <input step="0.01" type="text" name="" class="form-control designation-input" value="{{ old('designation') }}" id="designation" readonly>
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">Groupe Taxation</label>
              <div class="input-group input-group-sm mb-3">

                <select class="form-select " id="taxe_id" value="{{ old('taxe_id') }}" aria-label="select example">
                  @if (isset($listeGroupeTaxation))
                  <option value=""></option>
                  @foreach ($listeGroupeTaxation as $taxe)
                  <option value="{{ $taxe->id }}-{{ $taxe->Code_lettre }}" data-codetaxe="{{ $taxe->Code_lettre }}">{{ $taxe->Code_lettre }}
                  </option>
                  @endforeach
                  @endif


                </select>
              </div>
            </div>


            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">Qte&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
              <div class="input-group input-group-sm mb-3">
                <input min="1" step="0.01" type="number" name="" class="form-control" value="{{ old('quantity') }}" id="quantity">
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">PU&nbsp;HT</label>
              <div class="input-group input-group-sm">
                <input type="number" min="1" class="form-control" value="{{ old('pu_HT') }}" id="pu_HT" aria-label="file example" >
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">%Remise</label>
              <div class="input-group input-group-sm mb-3">
                <input type="number" class="form-control " value="{{ old('Taux_remise') }}" id="Taux_remise" aria-label="file example">
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">PU Net HT </label>
              <div class="input-group input-group-sm mb-3">
                <input type="number" class="form-control" value="{{ old('pu_HT_net') }}" id="pu_HT_net" aria-label="file example" readonly>
              </div>

            </div>
            <div class="form-group col-md-3">
              <label for="validationTextarea" class="form-label fw-bold">PU Net TTC </label>
              <div class="input-group input-group-sm mb-3">
                <input type="number" class="form-control" value="{{ old('pu_TTC') }}" id="pu_TTC" aria-label="file example" readonly>
              </div>

            </div>
          </div>
          <button type="button" id="add" name="add" class="btn btn-sm w-auto float-end text-white" style="{{ background_color_1() }}"><span class="fw-bold">+</span>Ajouter</button>
        </fieldset>
      </div>
    </div>
    <div class="card m-b-30">
      <div class="card-body">
        <fieldset class="border p-1 rounded-3">
          <legend class="float-none w-auto px-1">Ligne Proforma</legend>
          <div class="table-responsive">
            <table class="table table-bordered lignePro" id="table2">
              <thead class="table-primary">
                <tr>
                    <th style="{{ background_color_2() }}" class="text-white" width="100px">
                      Réf</th>
                    <th style="{{ background_color_2() }}" class="text-white" width="200px">
                      Désignation</th>
                    <th style="{{ background_color_2() }}" class="text-white" width="100px">
                      G.&nbsp;T.</th>
                    <th style="{{ background_color_2() }}"class="text-white" width="100px">
                      Qté</th>
                    <th style="{{ background_color_2() }}" class="text-white" width="150px" >
                      PU&nbsp;HT</th>
                    <th style="{{ background_color_2() }}" class="text-white" width="50px">%&nbsp;Remise
                    </th>
                    <th style="{{ background_color_2() }}" class="text-white" width="150px">PU&nbsp;Net&nbsp;HT
                    </th>
                    <th style="{{ background_color_2() }}" class="text-white" width="150px">PU&nbsp;TTC
                    </th>
                    <th style="{{ background_color_2() }}" class="text-white" width="150px">Montant&nbsp;Net&nbsp;HT</th>
                    <th style="{{ background_color_2() }}" class="text-white"width="150px">Montant&nbsp;Net&nbsp;TTC</th>
                    <th style="{{ background_color_2() }}" class="text-white"> </th>
                  </tr>

              </thead>
              <tbody>
                <!-- Lignes du deuxième tableau seront ajoutées ici -->

              </tbody>
            </table>
          </div>
        </fieldset>
      </div>
    </div>
    <style>
        .lignePro {
            width: 1500px;
        }



        @media (min-width: 1600px) {
            .lignePro {
                width: 100%;
            }


            th {
                width: auto;
            }
        }
    </style>
    <div class="card m-b-30">
      <div class="card-body">
        <fieldset class="border p-1 rounded-3">
          <legend class="float-none w-auto px-1">Récap</legend>
          <div class="table-responsive">
            <table class="table table-bordered lignePro">
              <tr>
                <td>
                  <div style="font-size: 14px">Exonérés</div>
                  <div>
                    <input type="text" name="" id="totalExoneres" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Total&nbsp;HT&nbsp;[B]&nbsp;18%</div>
                  <div>
                    <input type="text" name="" id="totalHT_B" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Total&nbsp;TVA&nbsp;[B]&nbsp;18%</div>
                  <div>
                    <input type="text" name="" id="totalTVA_B" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Total&nbsp;HT&nbsp;[C]</div>
                  <div>
                    <input type="text" name="" id="totalHT_C" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Total&nbsp;HT&nbsp;[D]</div>
                  <div>
                    <input type="text" name="" id="totalHT_D" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Total&nbsp;TVA&nbsp;[D]&nbsp;18%</div>
                  <div>
                    <input type="text" name="" id="totalTVA_D" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Regime&nbsp;TPS&nbsp;[E]</div>
                  <div>
                    <input type="text" name="" id="totalHT_E" class="form-control form-control-sm" readonly>
                  </div>
                </td>

                <td>
                  <div style="font-size: 14px">Réservés&nbsp;[F]</div>
                  <div>
                    <input type="text" name="" id="totalHT_F" class="form-control form-control-sm" readonly>
                  </div>
                </td>



              </tr>

              <tr>

                <td>
                  <div style="font-size: 14px">Aib&nbsp;Facturé</div>
                  <div>
                    <input type="text" name="" id="aib_facturer" value="0" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Aib&nbsp;déductible</div>
                  <div>
                    <input type="text" name="" id="aib" value="0" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">TOTAL&nbsp;HT</div>
                  <div>
                    <input type="text" name="" id="totalHT_Global" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">TOTAL&nbsp;TTC</div>
                  <div>
                    <input type="text" name="" id="totalTTC" class="form-control form-control-sm" readonly>
                  </div>
                </td>
                <td>
                  <div style="font-size: 14px">Net&nbsp;à&nbsp;payer</div>
                  <div>
                    <input type="text" name="Net_a_payer" id="Net_a_payer" value="0" class="form-control form-control-sm" readonly>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </fieldset>
      </div>
    </div>
    {{-- <div class="mb-5">
      <button id="validate-button" class="btn text-white d-block ms-auto" style="{{ background_color_1() }}" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">Valider </button>
    </div> --}}
    <div class="mb-5">
      <button id="validate-button" class="btn text-white d-block ms-auto" style="{{ background_color_1() }}" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">Valider</button>
    </div>
    <br>
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Cette ligne existe déjà dans le tableau. Veuillez supprimer la ligne pour pouvoir la modifier.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Ne pas modifier</button>
            <button type="button" class="btn btn-primary" id="cancelButton">Modifier</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Ajoutez ensuite une section pour le deuxième tableau -->

</section>
<script>
    //désactivation du bouton
    $('#formX').on('submit', function(e) {

               var $button = $('#save-button');
                  $button.addClass('loading');
                  $button.prop('disabled', true);

      });
</script>
{{-- <script>
$(document).ready(function () {
  // Ajoutez l'écouteur sur le sélecteur
  $('#client').on('select2:select', function () {
    clearFormFields();
    // Réinitialisez tous les champs du formulaire sauf le client
    const form = document.getElementById('formX');
    Array.from(form.elements).forEach(element => {
      // Ne réinitialisez pas le sélecteur lui-même
      if (element.id !== 'client') {
        if (element.type === 'select-one') {
          element.selectedIndex = 0; // Réinitialise les <select>
        } else if (element.type === 'checkbox' || element.type === 'radio') {
          element.checked = false; // Décoche les cases
        } else {
          element.value = ''; // Vide les champs texte, email, etc.
        }
      }
    });
    const netAPayerField = document.getElementById('Net_a_payer');
    const aibField = document.getElementById('aib');
    const aib_facturerField = document.getElementById('aib_facturer');
    if (netAPayerField) {
      netAPayerField.value = '0';
      aibField.value = '0';
      aib_facturerField.value = '0';
    }

  });
});


  </script> --}}


<script>
  // Fonction pour vérifier et afficher/masquer le message d'erreur
  function validateInput() {
    var validiteInput = document.getElementById('validite');
    var errorMessage = document.getElementById('error-message');

    if (validiteInput.value.trim() === '') {
      errorMessage.style.display = 'block';
    } else {
      errorMessage.style.display = 'none';
    }
  }

  document.getElementById('validate-button').addEventListener('click', function() {
    var validiteInput = document.getElementById('validite');
    var errorMessage = document.getElementById('error-message');

    if (validiteInput.value.trim() === '') {
      errorMessage.style.display = 'block';
      validiteInput.focus();
    } else {
      errorMessage.style.display = 'none';
      // Ouvrir le modal seulement si le champ est rempli
      var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
      myModal.show();
    }
  });

  // Écouteur d'événement pour le champ de saisie
  document.getElementById('validite').addEventListener('input', validateInput);

  document.getElementById('cancel-button').addEventListener('click', function() {
    // Fermer le modal en manipulant directement les classes CSS
    var myModalEl = document.getElementById('staticBackdrop');
    myModalEl.classList.remove('show');
    myModalEl.style.display = 'none';
    var backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
      backdrop.remove();
    }
  });

</scrip>




<script>
  document.getElementById('formX').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
    }
  });

</script>

<script>
    //Recuperation des prix
    $(document).ready(function() {
      function getProduitPrice() {
        var produitId = $('#produit').find(':selected').data('produit_id');
        var clientCatId = $('#client').find(':selected').data('cat_client');
        //console.log('produitIdproduitIdproduitId', produitId, 'clientCatIdclientCatIdclientCatId', clientCatId)
        if (produitId != null && clientCatId != null) {

          $.ajax({
            url: '/recuperer_prix_produit/' + produitId
            , type: 'GET'
            , data: {
              clientCatId: clientCatId
            }, // Envoyer clientCatId comme données de requête
            success: function(response) {
              var prix_produit = parseFloat(response.prix_produit);
             // console.log('Prix du produit:', prix_produit);
             const appUrl = "{{ config('app.url') }}"; // ou {{ env('APP_URL') }}
        console.log('App URL5:', appUrl);
        const validUrls = [
            "http://localhost",
            "https://www.olouwafemi-dev.easyfac.net",
            "https://www.olouwafemi.easyfac.net"
        ];

        if (validUrls.includes(appUrl)) {


            //ce code concerne olouwafemi-----------------------
            var selectedClient = $('#client').find(':selected');
            var clientIFU = selectedClient.data('ifu').toString();
            var carateresIFU = clientIFU.split('');
            console.log('merdemerdemerde', carateresIFU);
            if (carateresIFU.length == 13) {
                prix_produit = prix_produit / 1.19;

            } else if (carateresIFU.length < 13) {

                prix_produit = prix_produit / 1.18;
            console.log('merdemerdemerde', prix_produit);

            }
            //----------------------------------------fin olouwafemi
        }
              $('#pu_HT').val(prix_produit.toFixed(0));
            }
            , error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      }

      $('#produit').on('change', getProduitPrice);
      $('#client').on('change', getProduitPrice);
    });

</script>

<script>
  var puHTNet = 0; // Déclarer la variable en dehors des fonctions pour la rendre accessible globalement
  //var puTTC = 0; // Déclarer la variable en dehors des fonctions pour la rendre accessible globalement

  // Fonction pour calculer le prix unitaire hors taxe net
  function calculatePUHTNet() {
    var puHT = parseFloat(document.getElementById('pu_HT').value);
    var tauxRemise = parseFloat(document.getElementById('Taux_remise').value);
    if (!isNaN(puHT) && !isNaN(tauxRemise)) {
      puHTNet = puHT * (1 - tauxRemise / 100); // Stocke la valeur calculée dans la variable puHTNet
      document.getElementById('pu_HT_net').value = puHTNet.toFixed(0);
      return puHTNet; // Retourne la valeur calculée
    }
  }

  // Appel de la fonction de calcul lorsque les valeurs changent
  document.getElementById('pu_HT').oninput = function() {
    puHTNet = calculatePUHTNet(); // Stocke la valeur retournée dans la variable puHTNet
    calculatePUTTC(); // Appelle la fonction calculatePUTTC pour mettre à jour le prix TTC
  };
  document.getElementById('Taux_remise').oninput = function() {
    puHTNet = calculatePUHTNet(); // Stocke la valeur retournée dans la variable puHTNet
    calculatePUTTC(); // Appelle la fonction calculatePUTTC pour mettre à jour le prix TTC
  };

  // Fonction pour calculer le prix unitaire TTC
  function calculatePUTTC() {
    var quantity = parseFloat(document.getElementById('quantity').value);

    var taxeOption = document.getElementById('taxe_id').value.split('-');
    var taxeID = parseInt(taxeOption[0]);
    var taxeCodeLettre = taxeOption[1];

    $.ajax({
      url: '/recuperer_valeur_taxe/' + taxeID
      , type: 'GET'
      , success: function(response) {
        var valeurTaxe = parseFloat(response.valeurTaxe);
       // var puTTC = 0;
        if (!isNaN(puHTNet) && !isNaN(valeurTaxe)) { // Utilise puHTNet récupéré de calculatePUHTNet
            var pu_HT_net = parseFloat(document.getElementById('pu_HT_net').value);
          var puTTC = pu_HT_net + (pu_HT_net * (valeurTaxe / 100));
        //   console.log('dddd', puTTC);
        //   console.log('ddddpu_HT_net', pu_HT_net);
          document.getElementById('pu_TTC').value = puTTC.toFixed(0);
          //return puTTC;
        }
      }
      , error: function(xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
  }

  // Appel de la fonction de calcul lorsque la taxe est modifiée
  document.getElementById('taxe_id').oninput = calculatePUTTC;
  document.getElementById('quantity').oninput = calculatePUHTNet;

</script>
<script>
  $(document).ready(function() {
    // Écouteur d'événement sur la sélection de produit
    $(document).on('change', '#produit', function() {
      var selectedProduct = $(this).val(); // Récupérer le produit sélectionné

      // Vérifier si le produit sélectionné existe déjà dans les lignes du tableau
      var exists = checkIfProductExists(selectedProduct);
    // console.log('Produit e sélectionné:', selectedProduct);


      // Si le produit existe, demander à l'utilisateur s'il souhaite le supprimer
      if (exists) {
        var confirmation = confirm(
          "Le produit sélectionné existe déjà dans le tableau. Veuillez supprimer la ligne pour pouvoir la modifier."
        );
        $('#produit').val(null).trigger('change');
        $('#designation').val('');
        $('#Taux_remise').val('');
        $('#quantity').val('');
        $('#pu_HT_net').val('');
        $('#pu_HT').val('');
        $('#pu_TTC').val('');
        $('#taxe_id').val('');
      }

    });

  });

  // Fonction pour vérifier si un produit existe déjà dans les lignes du tableau
  function checkIfProductExists(product) {
    var exists = false;
    $('#table2 tbody tr').each(function() {
      var existingProduct = $(this).find('td:eq(0) input').val(); // Récupérer le produit dans la ligne
      if (existingProduct === product) {
        exists = true;
        return false; // Sortir de la boucle si le produit est trouvé
      }
    });

    return exists;
  }

  // Fonction pour supprimer un produit existant du tableau

</script>


<script>
  /* fonction pour la verification des incompatibilité entre la taxe et les taxes existantes */
  $(document).ready(function() {
    // Écouteur d'événement sur la sélection de la taxe
    $(document).on('change', '#taxe_id', function() {
      var TaxeSelected = $('#taxe_id').find(':selected');
      var TaxeLettre = TaxeSelected.data('codetaxe');
    //   console.log("Taxe sélectionnée :", TaxeLettre);

      var exists = checkTaxe(TaxeLettre);

      if (exists) {
        alert("Il y a incompatibilité entre la taxe et les taxes existantes dans le tableau.");
        $('#taxe_id').val('').change();
      }
    });
  });

  function checkTaxe(taxe) {
    var incompatible = false;
    $('#table2 tbody tr').each(function() {
      var existingTaxe = $(this).find('td:eq(2) input').val().split('-');
      var taxeCodeLettre = existingTaxe[1];

      if ((taxe === 'E' && ['A', 'B', 'C', 'D'].includes(taxeCodeLettre)) ||
        (['A', 'B', 'C', 'D'].includes(taxe) && taxeCodeLettre === 'E')) {
        incompatible = true;
        return false; // Sortir de la boucle each
      }
    });

    return incompatible;
  }

</script>


<script>
  $(document).ready(function() {
    // Écouter l'événement de changement sur les boutons radio Aib
    $('input[name="Aib"]').change(function() {
      // Récupérer la valeur du bouton radio sélectionné
      var selectedAibValue = $('input[name="Aib"]:checked').val();
    //   console.log('AIB sélectionné :', selectedAibValue);
      var selectedClient = $('#client').find(':selected');
      var clientIFU = selectedClient.data('ifu').toString();
      var carateresIFU = clientIFU.split('');
    //   console.log('merde', carateresIFU);
      var totalHT_Global = $('#totalHT_Global').val();
      var Net_a_payer = $('#Net_a_payer').val();
      var aib_facturerI = $('#aib_facturer').val();


      if (selectedAibValue === '1' && carateresIFU.length == 13) {
        // console.log("AIB facturable est de 1%");
        aib_facturer = totalHT_Global * 0.01;
        $('#aib_facturer').val(aib_facturer.toFixed(0));
        Net_a_paye = parseFloat(Net_a_payer) + parseFloat(aib_facturer);
        // console.log('Net_a_paye', Net_a_paye);
        $('#Net_a_payer').val(Net_a_paye.toFixed(0));

      } else if (selectedAibValue === '1' && carateresIFU.length < 13) {
        // console.log("AIB facturable est de 5%");
        aib_facturer = totalHT_Global * 0.05;
        $('#aib_facturer').val(aib_facturer.toFixed(0));
        Net_a_paye = parseFloat(Net_a_payer) + parseFloat(aib_facturer);
        $('#Net_a_payer').val(Net_a_paye.toFixed(0));
      } else if (selectedAibValue === '0') {
        Net_a_payerr = parseFloat(Net_a_payer) - parseFloat(aib_facturerI);
        $('#Net_a_payer').val(Net_a_payerr.toFixed(0));

        aib_facturer = totalHT_Global * 0;
        $('#aib_facturer').val(aib_facturer.toFixed(0));
      }

    });

    //ici
    $('#Aib_deductible').on('change', function() {
      var selectedAi = $(this).val();
    //   console.log('AIB sélectionné aujouduihn:', selectedAi);
      var totalHT_Global = parseFloat($('#totalHT_Global').val()); // Convertir en nombre
      var aib_DEDUCTIBLEE = $('#aib').val();
      var aib_DEDUCTIBLE = parseFloat(aib_DEDUCTIBLEE);
      var Net_a_payer = $('#Net_a_payer').val();

      // Parcourir chaque ligne du tableau
      $('#table2 tbody tr').each(function() {
        var rowProduitType = $(this).find('input[name^="inputs["][name$="[TypeP]"]')
          .val(); // Récupérer le type de produit de la ligne à partir de l'élément input
        // console.log('Type de produit :', rowProduitType);

        //var rowMontantHTInput = parseFloat($(this).find('input[name^="inputs["][name$="[montantHT]"]').val()); // Récupérer le total TTC de la ligne à partir de l'élément input
        var rowpu_HT_netInput = $(this).find(
          'input[name^="inputs["][name$="[pu_HT_net]"]');
        var rowquantityInput = $(this).find(
          'input[name^="inputs["][name$="[quantity]"]');
        var rowMontantHTInput = $(this).find(
          'input[name^="inputs["][name$="[montantHT]"]');
        // console.log('montantHT', rowMontantHTInput.val());
        // Comparer les données de la ligne avec celles des lignes existantes
        if (rowProduitType === 'PRESTATION') {
          var rowpu_HT_net = parseFloat(rowpu_HT_netInput.val());
          var rowquantity = parseFloat(rowquantityInput.val());
          if (selectedAi ===
            '5') { // Comparer avec '5' pour vérifier l'égalité, pas une affectation avec '='
            var aibx = rowpu_HT_net * rowquantity * 0.05; // Calculer l'AIB

            // aib_deductible_calculer = parseFloat(aib_DEDUCTIBLEE) +  parseFloat(aibx);
            aib_DEDUCTIBLE += aibx;

            // console.log("aib_DEDUCTIBLEE", aib_DEDUCTIBLE);
            $('#aib').val(aib_DEDUCTIBLE.toFixed(0));
            Net_a_payer -= parseFloat(aibx.toFixed(0));

            //nouveau_net_a_payer = parseFloat(Net_a_payer) -  parseFloat(aibx.toFixed(0));
            $('#Net_a_payer').val(Net_a_payer.toFixed(0));

          }
          if (selectedAi ===
            '3') { // Comparer avec '5' pour vérifier l'égalité, pas une affectation avec '='
            var aibx = rowpu_HT_net * rowquantity * 0.03; // Calculer l'AIB

            aib_DEDUCTIBLE += aibx;

            // console.log("aib_DEDUCTIBLEE", aib_DEDUCTIBLE);
            $('#aib').val(aib_DEDUCTIBLE.toFixed(0));
            Net_a_payer -= parseFloat(aibx.toFixed(0));

            $('#Net_a_payer').val(Net_a_payer.toFixed(0));

          }
          if (selectedAi ===
            '1') { // Comparer avec '5' pour vérifier l'égalité, pas une affectation avec '='
            var aibx = rowpu_HT_net * rowquantity * 0.01; // Calculer l'AIB

            aib_DEDUCTIBLE += aibx;

            // console.log("aib_DEDUCTIBLEE", aib_DEDUCTIBLE);
            $('#aib').val(aib_DEDUCTIBLE.toFixed(0));
            Net_a_payer -= parseFloat(aibx.toFixed(0));

            $('#Net_a_payer').val(Net_a_payer.toFixed(0));

          }
        }
      });




    });



  });

</script>










<script>
  $(document).ready(function() {

    // Écouter l'événement de changement sur le sélecteur de clients
    $('#client').change(function() {
      // Vérifier si un client est sélectionné
      if ($(this).val() !== '') {
        // Activer le sélecteur de produits
        $('.produit-select').prop('disabled', false);
        $('.produit-message').prop('hidden', true);
        $('.AibFacturer-select').prop('disabled', false);
      } else {
        // Désactiver le sélecteur de produits
        $('.produit-select').prop('disabled', true);
      }
    });



    $('#produit').on('change', function() {
      var produitSelected = $('#produit').find(':selected');
      var produitDesignation = produitSelected.data('designation');
      var produitType = produitSelected.data('type');
      //var tau = 0;
      $('#designation').val(produitDesignation);
      $('#typeProduit').val(produitType);
      $('#Taux_remise').val('');
      $('#quantity').val('');
      $('#pu_HT_net').val('');
      $('#pu_HT').val('');
      $('#pu_TTC').val('');
      $('#taxe_id').val('');

    });

  });



  $(document).ready(function() {
    var totalTTC = 0;
    var Net_a_payer = 0;
    var totalHT = 0;
    var aib = 0;
    var totalExoneres = 0;
    var totalHT_B = 0;
    var totalTVA_B = 0;
    var totalHT_C = 0;
    var totalHT_D = 0;
    var totalTVA_D = 0;
    var totalHT_E = 0;
    var totalHT_F = 0;
    var aib_facturer = 0;
    var totalHT_Global = 0;








    $('#Aib_deductible').on('change', function() {
      var selectedAib = $(this).val();
      // Utilisez la valeur sélectionnée pour effectuer votre calcul
    //   console.log('AIB sélectionné :', selectedAib);
      // Ajoutez votre calcul ici en utilisant la valeur de l'AIB sélectionné
      if (selectedAib !== '') {
        // Activer le menu déroulant Aib_deductible
        $('#Aib_deductible').prop('disabled', true);
      }
    });
    $('#Aib_deductible').on('change', function() {
      var selectedAib = $(this).val();
    //   console.log('AIB sélectionné :', selectedAib);
      // Mettre à jour la valeur du champ caché avec la valeur sélectionnée
      $('#hiddenAib_deductible').val(selectedAib);
    });







    function clearFormFields() {
      $('#produit').val(null).trigger('change');
      $('#designation').val('');
      $('#taxe_id').val('');
      $('#quantity').val('');
      $('#pu_HT').val('');
      $('#Taux_remise').val('');
      $('#pu_HT_net').val('');
      $('#pu_TTC').val('');
      // $('#produitType').val('');
    }
    // Ajouter une nouvelle ligne au deuxième tableau
    $('#add').click(function(selectedAib) {
      var reference = $('#produit').val();
      var designation = $('#designation').val();
      var taxe_id = $('#taxe_id').val();
      var quantite = parseFloat($('#quantity').val());
      var pu_HT = parseFloat($('#pu_HT').val());
      var Taux_remise = parseFloat($('#Taux_remise').val());
      var pu_HT_net = parseFloat($('#pu_HT_net').val());
      var pu_TTC = parseFloat($('#pu_TTC').val());
      // var produitType = parseFloat($('#produitType').val());
      // var Aib_deductible = selectedAib;




      // Vérifier si la quantité et le prix sont valides
      if (!isNaN(quantite) && !isNaN(pu_HT) && !isNaN(Taux_remise)) {
        // Ajouter ou mettre à jour la ligne
        updateOrAddRow(reference, designation, taxe_id, quantite, pu_HT, Taux_remise, pu_HT_net
          , pu_TTC);
        clearFormFields();
      } else {
        // Afficher un message d'erreur si la quantité ou le prix n'est pas un nombre valide
        return alert('Veuillez entrer une quantité et un prix valides.');
      }

    });

    // Fonction pour vérifier si une ligne existe déjà et la mettre à jour si nécessaire
    function updateOrAddRow(reference, designation, taxe_id, quantite, pu_HT, Taux_remise, pu_HT_net
      , pu_TTC, ) {
      var rows = $('#table2 tbody tr');
       console.log('pu_TTC', pu_TTC);
     // var montantHT = parseFloat(quantite) * parseFloat(pu_HT) * (1 - Taux_remise / 100);
     var montantTTC = parseFloat(quantite) * parseFloat(pu_TTC);
     console.log('montantTTC', montantTTC);

              //calcul des ligne TVA A exoneres
              var taxeOption = document.getElementById('taxe_id').value.split('-'); // Sépare l'ID de la taxe et le code de la lettre
        var taxeID = parseInt(taxeOption[0]); // Récupère l'ID de la taxe sélectionnée
        var taxeCodeLettre = taxeOption[1];

        if (taxeCodeLettre === 'A') {
            var mtHt = parseFloat(montantTTC) / 1;
            var montantHT = parseFloat(mtHt.toFixed(0));
            console.log('montantHT', montantHT);
        }
        if (taxeCodeLettre === 'B') {

            var mtHt = Math.round(montantTTC / 1.18);
            var montantHT = mtHt;
            console.log('montantHT', montantHT);

        }
        if(taxeCodeLettre === 'C'){
            var mtHt = parseFloat(montantTTC) / 1;
            var montantHT = parseFloat(mtHt.toFixed(0));
            console.log('montantHT', montantHT);

        }
        if(taxeCodeLettre === 'D'){
            var mtHt = Math.round(montantTTC / 1.18);
            var montantHT = parseFloat(mtHt.toFixed(0));
            console.log('montantHT', montantHT);

        }
        if(taxeCodeLettre === 'E'){
            var mtHt = parseFloat(montantTTC) / 1;
            var montantHT = parseFloat(mtHt.toFixed(0));
            console.log('montantHT', montantHT);

        }
        if(taxeCodeLettre === 'F'){
            var mtHt = parseFloat(montantTTC) / 1;
            var montantHT = parseFloat(mtHt.toFixed(0));
            console.log('montantHT', montantHT);

        }


      var produitSelected = $('#produit').find(':selected');
      var TypeP = produitSelected.data('type');
    //   console.log('efefeeeeeeeeeeeeeeeeeeeeeeee', TypeP);





      var rowToUpdate = null;

      if (reference === '' || designation === '' || taxe_id === '' || quantite === '' || pu_HT === '' ||
        Taux_remise === '' || pu_HT_net === '' || pu_TTC === '') {
        alert('Veuillez remplir tous les champs.');
        return false;
      }

      // Vérification de la validité des valeurs numériques
      if (isNaN(parseFloat(quantite)) || isNaN(parseFloat(pu_HT)) || isNaN(parseFloat(Taux_remise))) {
        alert('Les champs quantité et prix unitaire hors taxe doivent être des nombres.');
        return false;
      }


      if (quantite <= 0 || pu_HT <= 0) {
        alert('La quantité et prix unitaire hors taxe doivent être des nombres positifs.');
        return false;
      }

      // Parcourir chaque ligne du tableau
      rows.each(function() {
        var row = $(this);

        var rowReference = row.find('td:eq(0) input').val();
        var rowDesignation = row.find('td:eq(1) input').val();
        //var rowDesignation = row.find('td:eq(1)').text();
        var rowGroupeTaxation = row.find('td:eq(2)').text();
        var rowQuantite = row.find('td:eq(3)').text();
        var rowPrixAchat = row.find('td:eq(4)').text();
        var rowTauxRemise = row.find('td:eq(5)').text();
        var rowPrixAchatNet = row.find('td:eq(6)').text();
        var rowPrixTTC = row.find('td:eq(7)').text();
        var rowMontantHT = row.find('td:eq(8)').text();
        var rowMontantTTC = row.find('td:eq(9)').text();
        var rowProduitType = row.find('td:eq(10)').text();


        // Comparer les données de la nouvelle ligne avec celles des lignes existantes
        if (rowReference === reference && rowDesignation === designation) {
          rowToUpdate = row;
          return false; // Sortir de la boucle si une correspondance est trouvée
        }
      });

      if (rowToUpdate == null) {

        // Calcule du  le montant total TTC
        totalTTC += montantTTC;
        $('#totalTTC').val(totalTTC.toFixed(0));


        var selectedAib = $('#Aib_deductible').val();
        // console.log('selectedAib', selectedAib);


        // Récupérer la valeur sélectionnée des boutons radio Aib
        var selectedAibValue = $('input[name="Aib"]:checked').val();

        // Récupérer la valeur sélectionnée du client
        var selectedClient = $('#client').find(':selected');
        var clientIFU = selectedClient.data('ifu');
        var clientIFU = selectedClient.data('ifu').toString();
        // console.log('me voici clientIFU', clientIFU);

        // Ensuite, vous pouvez appliquer la méthode split()
        var carateresIFU = clientIFU.split('');
        // console.log('merde', carateresIFU);

        // Afficher les valeurs sélectionnées dans la console
        // console.log("Valeur sélectionnée AIB :", selectedAibValue);
        // console.log("Client sélectionné :", selectedClient.val());
        // console.log("IFU du client sélectionné :", clientIFU);
        // console.log("totalTTCValue :", totalTTC);



        var produitSelected = $('#produit').find(':selected');
        var produitType = produitSelected.data('type');
        // console.log('produitDesignation avec aib', produitType)



        //calcul des aibs a déduire
        if (produitType === 'PRESTATION') {
          if (selectedAib == 5) {
            var aibInputs = $('#aib').val();
            aib = parseFloat(aibInputs) + parseFloat(pu_HT_net) * parseFloat(quantite) * 0.05;
            $('#aib').val(aib.toFixed(0));
          }

          if (selectedAib == 1) {
            var aibInputs = $('#aib').val();
            aib = parseFloat(aibInputs) + parseFloat(pu_HT_net) * parseFloat(quantite) * 0.01;
            $('#aib').val(aib.toFixed(0));
          }

          if (selectedAib == 3) {
            var aibInputs = $('#aib').val();
            aib = parseFloat(aibInputs) + parseFloat(pu_HT_net) * parseFloat(quantite) * 0.03;
            $('#aib').val(aib.toFixed(0));
          }
        }


        // console.log(aib);

        //calcul des ligne TVA A exoneres
        var taxeOption = document.getElementById('taxe_id').value.split(
          '-'); // Sépare l'ID de la taxe et le code de la lettre
        var taxeID = parseInt(taxeOption[0]); // Récupère l'ID de la taxe sélectionnée
        var taxeCodeLettre = taxeOption[1];

        if (taxeCodeLettre === 'A') {
          var ligneTVAExonere = parseFloat(montantHT) * 1;
          totalExoneres += ligneTVAExonere;
          $('#totalExoneres').val(totalExoneres.toFixed(0));
        }
        if (taxeCodeLettre === 'B') {
          var ligneTVA_B = parseFloat(montantTTC.toFixed(0)) - parseFloat(montantHT);
          var ligneHT_B = parseFloat(montantHT);
          totalTVA_B += ligneTVA_B;
          totalHT_B += ligneHT_B;
          $('#totalTVA_B').val(totalTVA_B.toFixed(0));
          $('#totalHT_B').val(totalHT_B.toFixed(0));
        }
        if (taxeCodeLettre === 'C') {
          var ligneHT_C = parseFloat(montantHT) * 1;
          totalHT_C += ligneHT_C;
          $('#totalHT_C').val(totalHT_C.toFixed(0));
        }
        if (taxeCodeLettre === 'D') {
          var ligneTVA_D =parseFloat(montantTTC.toFixed(0)) - parseFloat(montantHT);
          var ligneHT_D = parseFloat(montantHT);
          totalTVA_D += ligneTVA_D;
          totalHT_D += ligneHT_D;
          $('#totalTVA_D').val(totalTVA_D.toFixed(0));
          $('#totalHT_D').val(totalHT_D.toFixed(0));
        }

        if (taxeCodeLettre === 'E') {
          var ligneHT_E = parseFloat(montantHT);
          totalHT_E += ligneHT_E;
          $('#totalHT_E').val(totalHT_E.toFixed(0));
        }
        if (taxeCodeLettre === 'F') {
          var ligneHT_F = parseFloat(montantHT);
          totalHT_F += ligneHT_F;
          $('#totalHT_F').val(totalHT_F.toFixed(0));
        }

        //total du montant HT
        totalHT_Global = totalExoneres + totalHT_B + totalHT_C + totalHT_D + totalHT_E + totalHT_F;
        $('#totalHT_Global').val(totalHT_Global.toFixed(0));

        // Votre logique conditionnelle en fonction des valeurs sélectionnées
        if (selectedAibValue === '1' && carateresIFU.length == 13) {
        //   console.log("AIB facturable est de 1%");
          aib_facturer = totalHT_Global * 0.01;
          $('#aib_facturer').val(aib_facturer.toFixed(0));

        } else if (selectedAibValue === '1' && carateresIFU.length < 13) {
        //   console.log("AIB facturable est de 5%");
           /*  var totaldGlobalDivise = Math.round(totalHT_Global / quantite);
            var aib_facturer_unitaire = Math.round(totaldGlobalDivise * 0.05) ;
          aib_facturer =Math.round(aib_facturer_unitaire *quantite) ; */
          aib_facturer = totalHT_Global * 0.05;
          $('#aib_facturer').val(aib_facturer.toFixed(0));
        }

        var aibInput = $('#aib').val();
        var aibFloat = parseFloat(aibInput);



        //calcul du net a payer
        Net_a_payer = totalExoneres + totalHT_B + totalTVA_B + totalHT_C + totalTVA_D + totalHT_D +
          totalHT_E + totalHT_F + aib_facturer - aibFloat;
        $('#Net_a_payer').val(Net_a_payer.toFixed(0));
      }


      // Si une ligne existe, mettre à jour la quantité ou le prix
      if (rowToUpdate !== null) {

        $('#confirmationModal').modal('show');

        // Lorsque l'utilisateur clique sur "Continuer"
        $('#continueButton').click(function() {




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
        var newRow = `<tr>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][produit]" class="form-control border-0"  value="${reference}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][designation]" class="form-control border-0 designation-input" value="${designation}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][taxe_id]" class="form-control border-0" id="taxe_id" value="${taxe_id}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="inputs[${rowCount}][quantity]" class="form-control border-0" id="quantity" value="${quantite}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="inputs[${rowCount}][pu_HT]" class="form-control border-0" id="pu_HT" value="${pu_HT}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="inputs[${rowCount}][Taux_remise]" class="form-control border-0" id="Taux_remise" value="${Taux_remise}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="inputs[${rowCount}][pu_HT_net]" class="form-control border-0" id="pu_HT_net" value="${pu_HT_net}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" name="inputs[${rowCount}][pu_TTC]" class="form-control border-0" id="pu_TTC" value="${pu_TTC}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][montantHT]" class="form-control border-0" id="montantHT" value="${montantHT}" readonly>
                                </div>


                            </td>
                            <td>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][montantTTC]" class="form-control border-0" id="montantTTC" value="${montantTTC}" readonly>
                                </div>
                            </td>
                            <td hidden>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" name="inputs[${rowCount}][TypeP]" class="form-control border-0" id="TypeP" value="${TypeP}" readonly>
                                </div>
                            </td>


                            <td>
                                <button class="btn btn-danger btn-sm remove-row" id="remove-row" value="${montantTTC}">X</button> <!-- Bouton Supprimer -->
                            </td>

                        </tr> `;
        $('#table2 tbody').append(newRow);
      }
    }



    // Supprimer une ligne du deuxième tableau
    $(document).on('click', '.remove-row', function() {
      var pu_HT = parseFloat($(this).closest('tr').find('#pu_HT')
        .val()); // Récupérer la valeur de pu_HT de la ligne supprimée
      var quantity = parseFloat($(this).closest('tr').find('#quantity')
        .val()); // Récupérer la valeur de quantity de la ligne supprimée
      var pu_HT_net = parseFloat($(this).closest('tr').find('#pu_HT_net')
        .val()); // Récupération de la valeur de pu_HT_net de la ligne supprimée

      var pu_TTC = parseFloat($(this).closest('tr').find('#pu_TTC')
        .val());

      var montantHT = parseFloat($(this).closest('tr').find('#montantHT')
        .val());
      var montantTTC = parseFloat($(this).closest('tr').find('#montantTTC')
        .val());
      console.log('rrrrmontantHT', montantHT);


      var typeProduit = $(this).closest('tr').find('#TypeP')
        .val(); // Récupération de la valeur de pu_HT_net de la ligne supprimée
    //   console.log('me voici typeProduit', typeProduit);
      var selectedAib = $('#Aib_deductible').val();
      var selectedAib_Facturer = $('input[name="Aib"]:checked').val();
    //   console.log('me voici selectedAib_Facturer', selectedAib_Facturer);
      var selectedClient = $('#client').find(':selected');

      var clientIFU = selectedClient.data('ifu').toString();
    //   console.log('me voici clientIFU', clientIFU);

      // Ensuite, vous pouvez appliquer la méthode split()
      var carateresIFU = clientIFU.split('');
    //   console.log('merde', carateresIFU);





      var taxeValue = $(this).closest('tr').find('#taxe_id').val();
      var parts = taxeValue.split('-');
      var part1 = parts[0]; // Contient la première partie (1 dans votre exemple)
      var taxeLettre = parts[1];
    //   console.log('me voici taxeLettre', taxeLettre);
      var supprimerMontantTaxeA = 0;
      var supprimerMontantTaxeB = 0;
      var supprimerTVA_B = 0;
      var supprimerMontantTaxeC = 0;
      var supprimerTVA_C = 0;
      var supprimerMontantTaxeD = 0;
      var supprimerTVA_D = 0;
      var supprimerMontantTaxeE = 0;
      var supprimerMontantTaxeF = 0;
      var supprimerAibfacturer = 0;
      var supprimerAibfacturer2 = 0;
      //var voir = 0;
      var removedAmount = 0;
      // Votre logique conditionnelle en fonction des valeurs sélectionnées
      if (selectedAib_Facturer === '1' && carateresIFU.length == 13) {
        // console.log("AIB facturable est de 1%");
        //aib_facturer = totalTTC * 0.01;
        var supprimerAibfacturer = Math.round(montantHT * 0.01);
        // console.log('debogue', supprimerAibfacturer);

      } else if (selectedAib_Facturer === '1' && clientIFU.length < 13) {
        // console.log("AIB facturable est de 5%");
        var supprimerAibfacturer = Math.round(montantHT * 0.05);

        // console.log('debogue', supprimerAibfacturer2);
      }



      if (taxeLettre == 'A') {
        var supprimerMontantTaxeA = montantHT;
      }

      if (taxeLettre == 'B') {
        var supprimerMontantTaxeB = montantHT;
        var supprimerTVA_B = Math.round(montantTTC - montantHT);
      } else if (taxeLettre == 'C') {
        var supprimerMontantTaxeC = montantHT;

      } else if (taxeLettre == 'D') {
        var supprimerMontantTaxeD = montantHT;
        var supprimerTVA_D =  Math.round(montantTTC - montantHT);

      } else if (taxeLettre == 'E') {
        var supprimerMontantTaxeE = montantHT;


      } else if (taxeLettre == 'F') {
        var supprimerMontantTaxeF = montantHT;
      }



      if (typeProduit === 'PRESTATION') {

        if (selectedAib == 5) {
          var removedAmount = Math.round( pu_HT_net * quantity * 0.05); // Calculer le montant de l'AIB à soustraire
        } else if (selectedAib == 1) {
          var removedAmount = Math.round(pu_HT_net * quantity *
            0.01); // Calculer le montant de l'AIB à soustraire
        } else if (selectedAib == 3) {
          var removedAmount = Math.round(pu_HT_net * quantity *
            0.03); // Calculer le montant de l'AIB à soustraire
        } else if (selectedAib == '') {
          var removedAmount = 0;
        }
      }

      $(this).closest('tr').remove();
      totalTTC -= parseFloat($(this).val());
      $('#totalTTC').val(totalTTC.toFixed(0));
      document.getElementById('totalTTC').oninput = updateOrAddRow;

      totalHT_Global -= montantHT;
      $('#totalHT_Global').val(totalHT_Global.toFixed(0));
      document.getElementById('totalHT_Global').oninput = updateOrAddRow;

      //ici

      var aib_deductible_deja_facturer = $('#aib').val();
      aibcal = parseFloat(aib_deductible_deja_facturer) - parseFloat(removedAmount.toFixed(
        0)); // Soustraire le montant de l'AIB de l'AIB total
      $('#aib').val(aibcal.toFixed(0)); // Mettre à jour la valeur de l'AIB dans l'élément HTML
      document.getElementById('aib').oninput = updateOrAddRow;

      //fin

      totalExoneres -= supprimerMontantTaxeA;
      $('#totalExoneres').val(totalExoneres.toFixed(0));
      document.getElementById('totalExoneres').oninput = updateOrAddRow;


      totalHT_B -= supprimerMontantTaxeB;
      totalTVA_B -= supprimerTVA_B.toFixed(0);
      $('#totalHT_B').val(totalHT_B.toFixed(0));
      $('#totalTVA_B').val(totalTVA_B.toFixed(0));
      document.getElementById('totalHT_B').oninput = updateOrAddRow;
      document.getElementById('totalTVA_B').oninput = updateOrAddRow;

      totalHT_C -= supprimerMontantTaxeC;
      $('#totalHT_C').val(totalHT_C.toFixed(0));
      document.getElementById('totalHT_C').oninput = updateOrAddRow;


      totalHT_D -= supprimerMontantTaxeD;
      totalTVA_D -= supprimerTVA_D.toFixed(0);
      $('#totalHT_D').val(totalHT_D.toFixed(0));
      $('#totalTVA_D').val(totalTVA_D.toFixed(0));
      document.getElementById('totalHT_D').oninput = updateOrAddRow;
      document.getElementById('totalTVA_D').oninput = updateOrAddRow;

      totalHT_E -= supprimerMontantTaxeE;
      $('#totalHT_E').val(totalHT_E.toFixed(0));
      document.getElementById('totalHT_E').oninput = updateOrAddRow;

      totalHT_F -= supprimerMontantTaxeF;
      $('#totalHT_F').val(totalHT_F.toFixed(0));
      document.getElementById('totalHT_F').oninput = updateOrAddRow;



      //  if(taxeLettre == 'A'){


      voir = supprimerAibfacturer + supprimerAibfacturer2;
      var Net_a_payer_deja_facturer = $('#Net_a_payer').val();
       console.log('supprimerAibfacturer2', supprimerAibfacturer2);

      Net_a_payer_recalculer = Net_a_payer_deja_facturer - supprimerMontantTaxeA -
        supprimerMontantTaxeB - supprimerTVA_B -
        supprimerMontantTaxeC - supprimerMontantTaxeD - supprimerTVA_D - supprimerMontantTaxeE -
        supprimerMontantTaxeF - voir + parseFloat(removedAmount.toFixed(0));

       console.log('voirffff supprimerMontantTaxeA', supprimerMontantTaxeA);
       console.log('voirffff supprimerMontantTaxeB', supprimerMontantTaxeB);
       console.log('voirffff supprimerTVA_B', supprimerTVA_B);
       console.log('voirffff supprimerMontantTaxeC', supprimerMontantTaxeC);
       console.log('voirffff supprimerMontantTaxeD', supprimerMontantTaxeD);
       console.log('voirffff supprimerTVA_D', supprimerTVA_D);
       console.log('voirffff supprimerMontantTaxeE', supprimerMontantTaxeE);
       console.log('voirffff supprimerMontantTaxeF', supprimerMontantTaxeF);
       console.log('voirffff voir', voir);
       console.log('voirffff removedAmount', removedAmount);

       console.log('voirffff Net_a_payer_recalculer', Net_a_payer_recalculer);

    $('#Net_a_payer').val(Net_a_payer_recalculer.toFixed(0));
      document.getElementById('Net_a_payer').oninput = updateOrAddRow;

      aib_facturer -= supprimerAibfacturer +
        supprimerAibfacturer2; // Soustraire le montant de l'AIB de l'AIB total
      var aib_deja_facturer = $('#aib_facturer').val();
      calcul = parseFloat(aib_deja_facturer) - parseFloat(voir);

    //   console.log('aib_deja_facturer', aib_deja_facturer);
    //   console.log('calcule apres suppression', calcul);

      $('#aib_facturer').val(calcul.toFixed(0)); // Mettre à jour la valeur de l'AIB dans l'élément HTML
      document.getElementById('aib_facturer').oninput = updateOrAddRow;


      // }








    });




  });

</script>




{{-- <script>
        $(function() {
            $('#client').select2();
        })
    </script> --}}

@include('layouts.alert')

@endSection
