<!DOCTYPE html>
<html>
<head>
    <title>Statistique Global Facture</title>
    <style>
        /* Ajoutez ici votre style CSS pour le PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 100px;
            margin-bottom: 50px;
        }
      /*   .tableInfo {
            width: 100%;
            border-collapse: collapse;
        }
        .tableInfo, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        } */
         /* Style du tableau */
        .tableLigne {
            border-collapse: collapse;
            width: 100%;

            border: 1px solid #ddd;
            margin: auto; /* Centrer le tableau horizontalement */
            margin-top: 20px; /* Marge supérieure pour l'espace entre l'en-tête et le tableau */
        }

        /* Style des cellules du tableau */
        .tableLigne th,
        .tableLigne td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        @page {
            /* margin-top: 0px; */ /* Espace entre le haut de la page et le début du contenu */
            /* margin-bottom: 50px; */ /* Espace entre le bas de la page et le pied de page */
        }
        .header, .footer {
            position: fixed;
            width: 100%;
            text-align: center;
        }
        body::before {
            content: "EDITE PAR EASYFAC";
            position: fixed;
            top: 80%;
            left: -70px; /* Positionné à gauche */
            transform: translateY(-50%) rotate(-90deg); /* Rotation du texte */
            font-size: 8px; /* Ajustez la taille du texte selon vos besoins */
            color: rgb(0, 0, 0); /* Ajustez la couleur et l'opacité du filigrane */
            z-index: -1;
            pointer-events: none; /* Empêche les interactions avec le filigrane */
        }
        .header {
            top: 0;
            height: 100px;
        }
        .footer {
            bottom: 0;
            height: 50px;
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
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px" >
        @endif
    </div>
    <h2 style="text-align: center; text-transform: uppercase; text-decoration: underline; margin-top: 100px">Statistique Global des Factures sur une période</h2>
    <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
          <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
          <table class="">
            <tbody>
                <tr>
                    <td style="font-weight: bold">AGENCE</td>
                    @if($infoAgence != null)
                    <td>{{ $infoAgence['NomAgence'] }}</td>
                    @else
                    <td>Toutes les agences</td>
                    @endif

                </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Client&nbsp;:</td>
             @if($Code_client != null)
                <td>{{$Code_client}}</td>
                @elseif($Code_client == '')
                <td>Tous les clients</td>
                @endif
              </tr>
              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
                <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Imprimé&nbsp;le:</td>
                <td>{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
              </tr>

            </tbody>
          </table>
        </fieldset>
      </div>




    <h3>Total par Type de Facture</h3>
    <table class="tableLigne">
        <thead>
            <tr>
                <th style="background-color: #3232df; color: white">Type de Facture</th>
                <th style="background-color: #3232df; color: white">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>FV</td>
                <td>{{ $data['totalParTypeFacture']['FV'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>FA</td>
                <td>{{ $data['totalParTypeFacture']['FA'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>EV</td>
                <td>{{ $data['totalParTypeFacture']['EV'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>EA</td>
                <td>{{ $data['totalParTypeFacture']['EA'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Table Statistique</h3>
    <table class="tableLigne">
        <thead>
            <tr>
                <th style="background-color: #3232df; color: white">#</th>
                <th style="background-color: #3232df; color: white">Catégorie</th>
                <th style="background-color: #3232df; color: white">FV</th>
                <th style="background-color: #3232df; color: white">FA</th>
                <th style="background-color: #3232df; color: white">EV</th>
                <th style="background-color: #3232df; color: white">EA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['tableStatGlobalData'] as $row)
                <tr>
                    <td>{{ $row['rowNumber'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['FV'] }}</td>
                    <td>{{ $row['FA'] }}</td>
                    <td>{{ $row['EV'] }}</td>
                    <td>{{ $row['EA'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Table des différences</h3>
    <table class="tableLigne">
        <thead>
            <tr>
                <th style="background-color: #3232df; color: white">Catégorie</th>
                <th style="background-color: #3232df; color: white">Montant à l'intérieur</th>
                <th style="background-color: #3232df; color: white">Montant à l'extérieur</th>
                <th style="background-color: #3232df; color: white">Montant total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['diffTableData'] as $row)
                <tr>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['montantInterieur'] }}</td>
                    <td>{{ $row['montantExterieur'] }}</td>
                    <td>{{ $row['montantTotal'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
            <div  style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</div>
            <div style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</div>

    <div class="footer" >
     <footer>
        Page <span class="page-number"></span>
    </footer>

        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->pied }}" style="width: 100%">
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

