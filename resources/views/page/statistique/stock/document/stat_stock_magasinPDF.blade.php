<!DOCTYPE html>
<html>

<head>
    <title>Statistique stock magasin Facture</title>
    <style>
        /* Ajoutez ici votre style CSS pour le PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 150px;
            /* Espace entre le haut de la page et le début du contenu */
            margin-bottom: 100px;
            /* Espace entre le bas de la page et le pied de page */
        }

        /*
        .tableLigne {
            width: 100%;
            border-collapse: collapse;
        }

        .tableLigne,
        th,
        .tableLigne td {
            border: 1px solid black;
        }

        .tableLigne th,
       .tableLigne td {
            padding: 8px;
            text-align: left;
        } */
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

        .page-number:after {
            content: counter(page);
        }

        .total-pages:after {
            content: counter(pages);
        }
    </style>
</head>

<body>
    <div class="header">
        <!-- Contenu de l'en-tête ici -->
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">
        @endif
    </div>

    <h2 style="text-align: center; text-transform: uppercase; text-decoration: underline">Statistique stock au
        {{ $dateDebut }}</h2>

    <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
            <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
            <table class="tableInfo">
                <tbody>
                    <tr>
                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">Etat du Magasin&nbsp;:
                        </td>

                        <td>{{ $NomMagasin }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
                        <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">Imprimé&nbsp;le:</td>
                        <td>{{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
                    </tr>

                </tbody>
            </table>
        </fieldset>
    </div>


    <table class="tableLigne">
        <thead>
            <tr>

                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Code</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation
                </th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Unité</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie
                </th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Magasin</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Entrée</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Sortie</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Facture_FV
                </th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Facture_FA
                </th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Transfert
                </th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Facture_Invalidee</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Solde</th>
            </tr>
        </thead>
        <tbody>
            @php
                $t_entree = 0;
                $t_sortie = 0;
                $t_facture_fv = 0;
                $t_facture_fa = 0;
                $t_facture_in = 0;
                $t_transfert = 0;
                $solde_total = 0;
            @endphp
            @foreach ($data['tableStatMagasinData'] as $index => $row)
                @if ($index < count($data['tableStatMagasinData']) - 1)
                    <tr>
                        <td>{{ $row['reference'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        <td>{{ $row['unite'] }}</td>
                        <td>{{ $row['categorie'] }}</td>
                        <td>{{ $row['magasin'] }}</td>
                        <td style="text-align: right;">{{ $row['entree'] }}</td>
                        <td style="text-align: right;">{{ $row['sortie'] }}</td>
                        <td style="text-align: right;">{{ $row['facture_fv'] }}</td>
                        <td style="text-align: right;">{{ $row['facture_fa'] }}</td>
                        <td style="text-align: right;">{{ $row['transfert'] }}</td>
                        <td style="text-align: right;">{{ $row['facture_in'] }}</td>
                        <td style="text-align: right;">
                            {{
                                (float) $row['entree'] -
                                (float) $row['sortie'] -
                                (float) $row['facture_fv'] +
                                (float) $row['facture_fa'] -
                                (float) $row['transfert'] +
                                (float) $row['facture_in']
                            }}
                        </td>
                    </tr>

                    @php
                        $t_entree += $row['entree'];
                        $t_sortie += $row['sortie'];
                        $t_facture_fv += $row['facture_fv'];
                        $t_facture_fa += $row['facture_fa'];
                        $t_transfert += $row['transfert'];
                        $t_facture_in += $row['facture_in'];

                        $solde_total = $t_entree - $t_sortie - $t_facture_fv +  $t_facture_fa -   $t_transfert + $t_facture_in;
                    @endphp
                @endif
            @endforeach
            <tr>
                <td colspan="5">VALEUR TOTAL DU STOCK</td>
                <td style="text-align: right;">{{ $t_entree }}</td>
                <td style="text-align: right;">{{ $t_sortie }}</td>
                <td style="text-align: right;">{{ $t_facture_fv }}</td>
                <td style="text-align: right;">{{ $t_facture_fa }}</td>
                <td style="text-align: right;">{{ $t_transfert }}</td>
                <td style="text-align: right;">{{ $t_facture_in }}</td>
                <td style="text-align: right;">{{ $solde_total  }}</td>
            </tr>
        </tbody>

    </table>
    <div>
        <div align="right">
            <h4 style="font-style: italic">Imprimé le {{ $dateImpression }}</h4>
            <p>{{ $user }}</p>

        </div>

    </div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>
        <!-- Contenu du pied de page ici -->
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->pied }}" style="width: 100%; ">
        @endif
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                if ($PAGE_COUNT > 1) {
                    $font = $fontMetrics->get_font("DejaVu Sans, sans-serif", "normal");
                    $size = 12;
                    $pageText = "Page " . $PAGE_NUM . " / " . $PAGE_COUNT;
                    $y = 820;
                    $x = 520;
                    $pdf->text($x, $y, $pageText, $font, $size);
                }
            ');
        }
    </script>
</body>

</html>
