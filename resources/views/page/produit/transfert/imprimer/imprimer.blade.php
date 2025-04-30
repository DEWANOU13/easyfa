<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 100px;
            margin-bottom: 50px;
            /* Marge supérieure pour commencer après l'en-tête */
        }

        .titre {
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }

        /* CSS pour l'en-tête et le pied de page */
        @page {}

        .header,
        .footer {
            position: fixed;
            width: 100%;
            text-align: center;
        }

        body::before {
            content: "EDITE PAR EASYFAC";
            position: fixed;
            top: 80%;
            left: -70px;
            /* Positionné à gauche */
            transform: translateY(-50%) rotate(-90deg);
            /* Rotation du texte */
            font-size: 8px;
            /* Ajustez la taille du texte selon vos besoins */
            color: rgb(0, 0, 0);
            /* Ajustez la couleur et l'opacité du filigrane */
            z-index: -1;
            pointer-events: none;
            /* Empêche les interactions avec le filigrane */
        }

        .header {
            top: 0;
            height: 100px;
            z-index: 1000;
            /* Assurez-vous que l'en-tête apparaît au-dessus du contenu */
        }

        .footer {
            bottom: 0;
            height: 50px;
        }

        .table-container2 {
            width: 50px;
        }

        /* Style du tableau */
        .tableLigne {
            border-collapse: collapse;
            width: 100%;

            border: 1px solid #ddd;
            margin: auto;
            /* Centrer le tableau horizontalement */
            margin-top: 20px;
            /* Marge supérieure pour l'espace entre l'en-tête et le tableau */
        }

        /* Style des cellules du tableau */
        .tableLigne th,
        .tableLigne td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        /* Style de l'entête du tableau */
        .tableLigne th {
            background-color: #f2f2f2;
            color: #333;
        }

        .tableInfodgi {
            border-collapse: collapse;
            width: 100%;
        }

        .tableInfodgi th,
        .tableInfodgi td {
            padding: 5px;
            text-align: left;
        }

        .container {}

        .box1 {
            width: 60%;
            background-color: #ffffff;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }

        .box2 {
            width: 30%;
            background-color: #ffffff;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }

        .page-number:after {
            content: counter(page);
        }

        .total-pages:after {
            content: counter(pages);
        }
    </style>
    <style>
        /* CSS pour l'en-tête et le pied de page */
        @page {
            margin-top: 0;
            margin-bottom: 10px;
        }


        .header,
        .footer {
            position: fixed;
            width: 100%;
            text-align: center;
        }

        .header {
            top: 0;
            height: 100px;
        }

        .footer {
            bottom: 0;
            height: 50px;
        }
    </style>
</head>

<body>
    <section class="section">


        <div class="header">
            <!-- Contenu de l'en-tête ici -->
            @if ($imageEntetePied != null)
                <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">
            @endif
        </div>

        {{-- @if ($statut == '1') --}}
        <div class="titre">
            @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DE PRODUITS ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }})
                    ET ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DE PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DE PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h4>LISTE DES TRANSFERTS DE PRODUITS POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU PRODUITS {{ $produit->Reference }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})
                </h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} POUR LA
                    CATEGORIE
                    {{ $categorie->Libelle }} ENTRE ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} POUR
                    LA CATEGORIE
                    {{ $categorie->Libelle }} ENTRE ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source === 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS VERS LE MAGASIN {{ $magasin_destination->NomMagasin }} DU
                    PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE
                    ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
            @if ($magasin_source !== 'Tous' && $magasin_destination !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
                <h4>LISTE DES TRANSFERTS DES PRODUITS DU MAGASIN {{ $magasin_source->NomMagasin }} VERS LE MAGASIN
                    {{ $magasin_destination->NomMagasin }} DU PRODUIT
                    {{ $produit->Reference }} ENTRE ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) ET
                    ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y') }})</h4>
            @endif
        </div>
        @foreach ($transfert_produits as $transfert_produit)
            <div class="box0">
                <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                    <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
                    <table class="">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transfert_produit->Date_Transfert)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Reference&nbsp;:</td>
                                <td>{{ $transfert_produit->Reference_Transfert }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Source&nbsp;:</td>
                                <td>{{ $transfert_produit->NomMagasinSource }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Destionation&nbsp;:</td>
                                <td>{{ $transfert_produit->NomMagasinDestination }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Enregistrer
                                    par&nbsp;:</td>
                                <td>{{ $transfert_produit->name }}</td>

                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Observations&nbsp;:</td>
                                <td>{{ $transfert_produit->Observations }}</td>

                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <br>
            <div class="table">
                <!-- Placer la section <thead> en haut du fichier PDF -->
                <div class="table" style="margin-bottom: 15px;">
                    <table class="tableLigne table1">
                        @php
                            $showHeader = false; // Initialisez la variable à false
                        @endphp
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Reference</th>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Désignation</th>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Catégorie</th>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Qté_Transferée</th>
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
                </div>
            </div>
        @endforeach
    </section>
    <br>

    <div style="text-align: right; font-weight: bold;"> Imprimer le
        {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</div>
    <div style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</div>
    </table>
    <div class="footer">
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->pied }}" style="width: 100%">
        @endif
    </div>
</body>

</html>
