@extends('layouts.master', ['title' => $fournisseur->exists ? 'Modifier Fournisseur' : 'Creer Fournisseur'])
@section('content')

@include('layouts.partials.entete-page', [
'infos1' => 'Fournisseur',
'infos2' => 'Fournisseur',
'infos3' => isset($fournisseur) ? 'Modification' : 'Nouveau',
])

<section>
  <div class="row">
    <!-- end col -->

    <div class="col-md-8 offset-md-2 mb-5">
      <div class="d-flex flex-row-reverse bd-highlight">
        <div class="dropdown mb-2">
          <a href="{{ route('fournisseur') }}" class="btn text-white" style="{{ background_color_1() }}">
            <i class="fa fa-reply" aria-hidden="true"></i>
            Retour
          </a>
        </div>
      </div>
      <div class="card m-b-30">
        <div class="card-header" style="{{background_color_2()}}">
          {{-- <h4 class="mt-2 text-dark">
            @if (isset($fournisseur))
            Modification de {{ $fournisseur->DenominationSociale }}
            @else
            Enregistrement d'un fournisseur
            @endif
          </h4> --}}
          <h4 class="mt-2 text-dark">
            @if (isset($fournisseur) && $fournisseur->exists)
                Modification de {{ $fournisseur->DenominationSociale }}
            @else
                Enregistrement d'un fournisseur
            @endif
        </h4>
        </div>
        <div class="card-body">
          <form id="formFournisseur" action="{{ $fournisseur->id ? route('updateFournisseur', $fournisseur->id) : route('storeFournisseur') }}" method="POST">
            @csrf
            @if (isset($fournisseur->id))
            @method('PUT')
            @endif
            <div class="form-group">
              <label for="DenominationSociale" class="fw-bold">Dénomination sociale</label>
              <input type="text" class="form-control {{ $errors->has('DenominationSociale') ? 'is-invalid' : '' }}" value="{{ old('DenominationSociale', isset($fournisseur) ? $fournisseur->DenominationSociale : '') }}" id="DenominationSociale" name="DenominationSociale">
              {!! $errors->first('DenominationSociale', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="validationTextarea" class="form-label fw-bold">Pays</label>

              <!-- Select menu rempli avec la liste des pays -->
              <select class="form-select js-single {{ $errors->has('Pays') ? 'is-invalid' : '' }}" name="Pays" aria-label="Default select example" id="countrySelect">
                @if (isset($fournisseur) && $fournisseur->Pays != null)
                <option value="{{ $fournisseur->Pays}}">{{ $fournisseur->Pays}}</option>

                @endif
                <option value="{{ old('Pays', isset($fournisseur) ? $fournisseur->Pays : '') }}">{{ old('Pays', isset($fournisseur) ? $fournisseur->Pays : '') }}</option>

              </select>
              {!! $errors->first('Pays', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="NumeroIfu" class="fw-bold">IFU</label>
              <input type="number" class="form-control {{ $errors->has('NumeroIfu') ? 'is-invalid' : '' }}" value="{{ old('NumeroIfu', isset($fournisseur) ? $fournisseur->NumeroIfu : '') }}" id="ifuInput" name="NumeroIfu">
              {!! $errors->first('NumeroIfu', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="AdresseFournisseur" class="fw-bold">Adresse</label>
              <input type="text" class="form-control {{ $errors->has('AdresseFournisseur') ? 'is-invalid' : '' }}" value="{{ old('AdresseFournisseur', isset($fournisseur) ? $fournisseur->AdresseFournisseur : '') }}" id="AdresseFournisseur" name="AdresseFournisseur">
              {!! $errors->first('AdresseFournisseur', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="TelephoneFixe" class="fw-bold">Téléphone fixe</label>
              <input type="text" class="form-control {{ $errors->has('TelephoneFixe') ? 'is-invalid' : '' }}" value="{{ old('TelephoneFixe', isset($fournisseur) ? $fournisseur->TelephoneFixe : '') }}" id="TelephoneFixe" name="TelephoneFixe">
              {!! $errors->first('TelephoneFixe', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="TelephoneMobile" class="fw-bold">Téléphone modile</label>
              <input type="text" class="form-control {{ $errors->has('TelephoneMobile') ? 'is-invalid' : '' }}" value="{{ old('TelephoneMobile', isset($fournisseur) ? $fournisseur->TelephoneMobile : '') }}" id="TelephoneMobile" name="TelephoneMobile">
              {!! $errors->first('TelephoneMobile', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="AdresseMail" class="fw-bold">Adresse email</label>
              <input type="email" class="form-control {{ $errors->has('AdresseMail') ? 'is-invalid' : '' }}" value="{{ old('AdresseMail', isset($fournisseur) ? $fournisseur->AdresseMail : '') }}" id="AdresseMail" name="AdresseMail">
              {!! $errors->first('AdresseMail', '<p class="error">:message</p>') !!}
            </div>
            <div class="form-group">
              <label for="Statut_fournisseur" class="fw-bold">Statut</label>
              <div class="form-group">
                <div class="form-check form-check-inline">
                  <input class="form-check-input {{  $errors->has('Statut_fournisseur') ? 'is-invalid' : ''  }}" type="radio" id="activeStatut" value="1" name='Statut_fournisseur' {{ old('Statut_fournisseur',isset($fournisseur->id) && $fournisseur->Statut_fournisseur == 1 ? 'checked' : 'checked') }}>
                  <label class="form-check-label" for="activeStatut">Actif</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input {{  $errors->has('Statut_fournisseur') ? 'is-invalid' : ''  }}" type="radio" id="disableStatut" value="0" name='Statut_fournisseur' {{ old('Statut_fournisseur', isset($fournisseur->id) && $fournisseur->Statut_fournisseur == 0 ? 'checked' : '') }}>
                  <label class="form-check-label" for="disableStatut">Inactif</label>
                </div>
              </div>
              {!! $errors->first('Statut_fournisseur', '<p class="error">:message</p>') !!}
            </div>

            <button type="button" class="btn btn-secondary btn-lg waves-effect waves-light" onclick="window.location.href='{{ route('fournisseur') }}'">
              Annuler
            </button>
            <button type="button" id="saveButton" class="btn btn-lg pull-right waves-effect waves-light text-white btn-send" data-bs-toggle="modal" data-bs-target="#staticBackdrop" style="{{background_color_1()}}">
              Sauvegarder
            </button>

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        </div>
      </div>

    </div> <!-- end col -->
  </div> <!-- end row -->

</section>
<!-- Script pour remplir le select avec les pays -->

<script>
    $('#formFournisseur').on('submit', function(e) {

               var $button = $('#save-button');
                  $button.addClass('loading');
                  $button.prop('disabled', true);

      });
</script>
<script>
  document.addEventListener('DOMContentLoaded', (event) => {
    const countries = [
      "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola"
      , "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche"
      , "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize"
      , "Bénin", "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana"
      , "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada"
      , "Cap-Vert", "République centrafricaine", "Chili", "Chine", "Chypre", "Colombie", "Comores"
      , "République du Congo", "République démocratique du Congo", "Îles Cook", "Corée du Nord"
      , "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti"
      , "République dominicaine", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée"
      , "Espagne", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon", "Gambie"
      , "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guinée équatoriale"
      , "Guyana", "Haïti", "Honduras", "Hongrie", "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande"
      , "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan"
      , "Kiribati", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", "Libye", "Liechtenstein"
      , "Lituanie", "Luxembourg", "Macédoine", "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali"
      , "Malte", "Maroc", "Îles Marshall", "Maurice", "Mauritanie", "Mexique", "Micronésie", "Moldavie"
      , "Monaco", "Mongolie", "Monténégro", "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua"
      , "Niger", "Nigeria", "Niue", "Norvège", "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan"
      , "Pakistan", "Palaos", "Palestine", "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas"
      , "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "Roumanie", "Royaume-Uni", "Russie"
      , "Rwanda", "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Marin", "Saint-Vincent-et-les Grenadines"
      , "Salomon", "Salvador", "Samoa", "São Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles"
      , "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud"
      , "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", "Eswatini", "Tadjikistan", "Tanzanie"
      , "Tchad", "République tchèque", "Thaïlande", "Timor-Oriental", "Togo", "Tonga", "Trinité-et-Tobago"
      , "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay", "Vanuatu", "Vatican"
      , "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
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
   document.querySelector('form').addEventListener('submit', function(e) {
     var input = document.querySelector('input[name="NumeroIfu"]');
     if (input.value.length > 0 && input.value.length !== 13) {
       input.value = ''; // Vider le champ si la longueur n'est pas 13
     }
   });

   document.querySelector('input[name="NumeroIfu"]').addEventListener('input', function(e) {
     var value = e.target.value;
     if (value.length > 13) {
       e.target.value = value.slice(0, 13);
     }
   });

</script>
<script>
    document.getElementById('formFournisseur').addEventListener('input', function() {
      var form = document.getElementById('formFournisseur');
      var saveButton = document.getElementById('saveButton');
      var ifuInput = document.getElementById('ifuInput'); // Ajoutez cette ligne pour récupérer l'élément d'entrée du numéro IFU

      // Vérifiez si le formulaire est valide et si le numéro IFU a une longueur entre 1 et 12 caractères
      if (form.checkValidity() && (ifuInput.value.length === 0 || ifuInput.value.length > 13 || (ifuInput.value.length >= 13 && ifuInput.value.length <= 13))) {
        saveButton.removeAttribute('disabled'); // Activer le bouton de sauvegarde
      } else {
        saveButton.setAttribute('disabled', 'disabled'); // Désactiver le bouton de sauvegarde
      }
    });

    document.getElementById('formFournisseur').addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
      }
    });

</script>




@include('layouts.alert')
@endSection
