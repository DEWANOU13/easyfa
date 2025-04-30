@extends('layouts.master', ['title' => 'Fournisseur'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Fournisseur',
        'infos2' => 'Fournisseur',
        'infos3' => 'Liste',
    ])
    <section>
        <div class="row">
            <div class="col-12 mb-5 mt-3">
                <div class="d-flex flex-row-reverse bd-highlight">
                    <div class="dropdown mb-2">
                        <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="{{ background_color_1() }}">
                            Action
                        </button>
                        <ul class="dropdown-menu">

                            @can('creer-fournisseur')
                                <li><a href="{{ route('showFormFournisseur') }}" class="dropdown-item"
                                        type="button">Nouveau</a></li>
                            @endcan

                            @can('modifier-fournisseur')
                                <li><a id="modifierLink" class="dropdown-item" type="button">Modifier</a></li>
                            @endcan

                            <li><a id="detailLink" class="dropdown-item" type="button" data-bs-target="#showFournisseur"
                                    data-bs-toggle="modal">Détail</a>
                            </li>

                            <li>

                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                    data-bs-target="#fournisseurExcel">Exporter</button>
                            </li>

                            <li>
                                <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                    data-bs-target="#fournisseurPDF">Imprimer</button>
                            </li>

                        </ul>
                    </div>
                </div>

                {{-- Modal export liste de magasin --}}
                <div class="modal fade" id="fournisseurExcel" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de la liste des fournisseurs en Excel ?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                                <form action="{{ route('exportfournisseur') }}" method="POST">
                                    @csrf
                                    <button id="exportExcelClient" class="btn btn-sm btn-success"
                                        data-bs-dismiss="modal">Oui
                                        Exporter en Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal PDF liste de magasin --}}
                <div class="modal fade" id="fournisseurPDF" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Demande de confirmation </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment exporter les données de la liste des fournisseurs en PDF ?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Non</button>
                                <form action="{{ route('imprimerfournisseur') }}" method="Post" target="_blank">
                                    @csrf
                                    <button id="exportExcelClient" class="btn btn-sm btn-success"
                                        data-bs-dismiss="modal">Oui
                                        Exporter en Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h3 class="mt-2  d-inline-block text-dark">Liste des fournisseurs</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="fournisseursTable"
                                class="datatable table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Dénomination sociale</th>
                                        <th>Numéro</th>
                                        <th>Pays</th>
                                        <th>Adresse</th>
                                        <!-- <th style="width: 5%">Statut</th> -->
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($listeFournisseur as $key => $fournisseur)
                                        <tr class="clickable-row "
                                            data-url="{{ route('editFournisseur', $fournisseur->id) }}"
                                            data-fournisseur={{ $fournisseur }}>

                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $fournisseur->DenominationSociale }}</td>
                                            <td>{{ $fournisseur->TelephoneMobile }}</td>
                                            <td>{{ $fournisseur->Pays }}</td>
                                            <td>{{ $fournisseur->AdresseFournisseur }}</td>
                                            <!-- <td>
                                                                @if ($fournisseur->Statut_fournisseur == 1)
    <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
@else
    <i class="fa fa-check-circle text-danger" aria-hidden="true"></i>
    @endif
                                                            </td> -->
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div wire:ignore.self class="modal fade" id="showFournisseur" aria-hidden="true"
            aria-labelledby="showFournisseurLabel2" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Information fournisseur # <span id="m_id_fr"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-inverse">
                            <tbody>
                                <tr>
                                    <td>Dénomination sociale</td>
                                    <td><span id="m_denomination_sociale"></span></td>
                                </tr>
                                <tr>
                                    <td>IFU</td>
                                    <td><span id="m_ifu"></span></td>
                                </tr>
                                <tr>
                                    <td>Pays</td>
                                    <td><span id="m_pays"></span></td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td><span id="m_adresse"></span></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><span id="m_email"></span></td>
                                </tr>
                                <tr>
                                    <td>Téléphone fixe</td>
                                    <td><span id="m_tel_fixe"></span></td>
                                </tr>
                                <tr>
                                    <td>Téléphone mobile</td>
                                    <td><span id="m_tel_mobile"></span></td>
                                </tr>
                                <tr>
                                    <td>Enregistrer le</td>
                                    <td><span id="m_enreg"></span></td>
                                </tr>
                                <tr>
                                    <td>Modifier le</td>
                                    <td><span id="m_modif"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer" id="importFieldset">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close"
                            href="">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#fournisseursTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                var fournisseur = $(this).data('fournisseur');
                // Mettre à jour l'URL du lien "Modifier" avec l'ID du proforma
                var modifierUrl = "{{ route('editFournisseur', ['id' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                // Mettre à jour l'attribut href du lien
                $('#modifierLink').attr('href', modifierUrl);

                $('#m_id_fr').html(fournisseur['id']);
                $('#m_denomination_sociale').html(fournisseur['DenominationSociale']);
                $('#m_ifu').html(fournisseur['NumeroIfu']);
                $('#m_pays').html(fournisseur['Pays']);
                $('#m_adresse').html(fournisseur['AdresseFournisseur']);
                $('#m_email').html(fournisseur['AdresseMail']);
                $('#m_tel_fixe').html(fournisseur['TelephoneFixe']);
                $('#m_tel_mobile').html(fournisseur['TelephoneMobile']);
                $('#m_enreg').html(fournisseur['created_at']);
                $('#= $(this).find('
                    td: eq(5)
                    ').text();').html(fournisseur['updated_at']);

            });
        });
    </script>
    {{-- <script>
  $(document).ready(function() {
      $('#fournisseursTable').on('click', '.clickable-row', function() {
          var denomination_sociale = $(this).find('td:eq(0)').text();
          var ifu = $(this).find('td:eq(1)').text();
          var pays = $(this).find('td:eq(2)').text();
          var adresse = $(this).find('td:eq(3)').text();
          var email = $(this).find('td:eq(4)').text();
          var tel_fixe = $(this).find('td:eq(5)').text();
          var tel_mobile = $(this).find('td:eq(6)').text();
          var enregistre_par = $(this).find('td:eq(7)').text();
          var modif  = $(this).find('td:eq(8)').text();

          $('#m_id_fr').text(designation);
          $('#m_denomination_sociale').text(denomination_sociale);
          $('#m_ifu').text(ifu);
          $('#m_pays').text(pays);
          $('#m_adresse').text(adresse);
          $('#m_email').text(email);
          $('#m_tel_fixe').text(tel_fixe);
          $('#m_tel_mobile').html(tel_mobile);
          $('#m_enreg').text(enregistre_par);
          $('#m_modif').html(modif);
      });
  });
</script> --}}


    <script>
        $(document).ready(function() {
            $('#fournisseursTable').on('click', '.clickable-row', function() {
                var fournisseur = $(this).data('fournisseur');

                $('#m_id_fr').text(fournisseur.id);
                $('#m_denomination_sociale').text(fournisseur.DenominationSociale);
                $('#m_ifu').text(fournisseur.NumeroIfu);
                $('#m_pays').text(fournisseur.Pays);
                $('#m_adresse').text(fournisseur.AdresseFournisseur);
                $('#m_email').text(fournisseur.AdresseMail);
                $('#m_tel_fixe').text(fournisseur.TelephoneFixe);
                $('#m_tel_mobile').text(fournisseur.TelephoneMobile);
                $('#m_enreg').text(fournisseur.created_at);
                $('#m_modif').text(fournisseur.updated_at);
            });

            // Mettre à jour le lien du bouton "Modifier"
            $('#fournisseursTable').on('click', '.clickable-row', function() {
                var ID = $(this).data('url').split('/').pop();
                var modifierUrl = "{{ route('editFournisseur', ['id' => ':ID']) }}";
                modifierUrl = modifierUrl.replace(':ID', ID);
                $('#modifierLink').attr('href', modifierUrl);
            });
        });
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

    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la couleur de votre choix */
            color: white;
            /* Couleur du texte sur fond bleu */
        }
    </style>
    @include('layouts.alert')
@endSection
