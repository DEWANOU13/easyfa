@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
    <table>
        <tr>
            <td colspan="20" style="font-weight: bold; font-size: 20px">
                @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DE PRODUITS ENTRE ({{ $debut_periode }})
                    ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DE PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} ENTRE
                    ({{ $debut_periode }})
                    ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DE PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} ENTRE
                    ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h2>LISTE DES TRANSFERTS DE PRODUITS POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU PRODUITS {{ $produit->Reference }} ENTRE
                    ({{ $debut_periode }})
                    ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})
                </h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} POUR LA
                    CATEGORIE
                    {{ $categorie->Libelle }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} POUR
                    LA CATEGORIE
                    {{ $categorie->Libelle }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} DU
                    PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h2>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h2>
            @endif
            </td>
        </tr>
    </table>
    @foreach ($transfert_produits as $transfert_produit)
            <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
            <table class="">
                <tbody>
                    <tr>
                        <td colspan="2" style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
                        </td>
                        <td colspan="4" >{{ \Carbon\Carbon::parse($transfert_produit->Date_Transfert)->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">
                            Reference&nbsp;:</td>
                        <td colspan="4" >{{ $transfert_produit->Reference_Transfert }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">
                            Source&nbsp;:</td>
                        <td colspan="4" >{{ $transfert_produit->NomMagasinSource }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">
                            Destionation&nbsp;:</td>
                        <td colspan="4" >{{ $transfert_produit->NomMagasinDestination }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">Enregistrer
                            par&nbsp;:</td>
                        <td colspan="4" >{{ $transfert_produit->name }}</td>

                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">
                            Observations&nbsp;:</td>
                        <td colspan="4" >{{ $transfert_produit->Observations }}</td>

                    </tr>
                </tbody>
            </table>
    <br>
        <!-- Placer la section <thead> en haut du fichier PDF -->
            <table class="table1">
                @php
                    $showHeader = false; // Initialisez la variable à false
                @endphp
                <thead>
                    <tr>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; "> Designation</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Transferée</th>
                    </tr>

                </thead>
                <tbody>
                    @foreach ($getTransferer as $value)
                        @if ($transfert_produit->Id_Transfert_Produit === $value->Id_Transfert_Produit)
                            @if (!$showHeader)
                                @php
                                    $showHeader = true; // Définissez la variable à true si au moins une ligne est affichée
                                @endphp
                            @endif
                            <tr>
                                <td>{{ $value->Reference }}</td>
                                <td>{{ $value->Designation }}</td>
                                <td>{{ $value->Libelle }}</td>
                                <td>{{ $value->Qte_transferee }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>
@endforeach

<table>
    <tr>
        <th colspan="4" style="text-align: right; font-weight: bold;"> Imprimer le {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</th>
    </tr>
    <tr>
        <td  colspan="4" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
    </tr>
</table>

<table>
    <tr>
        <td colspan="1"
            style="font-weight: bold; background-color: #3232df;font-weight: bold; text-align: center; color: white;">
            Editer par:</td>
    </tr>
    <tr>
        <td colspan="1" style="font-weight: bold;text-align: center;">EASYFAC</td>
    </tr>
</table>
