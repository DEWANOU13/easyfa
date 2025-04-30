<!DOCTYPE html>
<html>

<head>
    <title>Rapport vente</title>
    <style>
        /* Ajoutez ici votre style CSS pour le PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 150px; /* Espace entre le haut de la page et le début du contenu */
            margin-bottom: 100px; /* Espace entre le bas de la page et le pied de page */
        }

        .tableListe {
            width: 100%;
            border-collapse: collapse;
        }

        .tableListe,
        th,
        .tableListe td {
            border: 1px solid rgb(178, 178, 178);
        }

        .tableListe th,
        .tableListe td {
            padding: 5px;
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
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">

        @endif
    </div>

    <h2 style="text-align: center; text-transform: uppercase; text-decoration: underline">RAPPORT VENTE</h2>

    <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
          <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
          <table class="tableInfo">
            <tbody>
                <tr>
                    <td style="font-weight: bold">Utilisateur</td>
                    @if($infoUser != null)
                    <td style="font-weight: bold">{{$infoUser['name']}}</td>

                    @else
                    <td style="font-weight: bold">Tous les utilisateurs</td>
                    @endif
                </tr>

                <tr>
                    <td style="font-weight: bold">AGENCE</td>

                     @if($infoAgence != null)
                    <td>{{$infoAgence['NomAgence']}}</td>

                    @else
                    <td>Tous les Agences</td>
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

    <br>


    <table class="tableListe">
        <thead>
            <tr>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Date</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Ref</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Statut</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Type</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Montant</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    D/R</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Ref règlement</th>

            </tr>
        <tbody>
            @foreach ($data['tableRapportCaisseData'] as $row)
            @if (in_array($row['created_at'], [
                'Total dépense',

            ]))
            <tr>
                <td colspan="" style="font-weight: bold;">{{ $row['created_at'] }}</td>
                <td style="text-align: right;">{{ $row['reference_operation'] }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $row['type'] }}</td>
                <td style="text-align: right;">{{ $row['statut'] }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @else
            <tr>

                <td >{{ $row['created_at'] }}</td>
                <td  style="">{{ $row['reference_operation'] }}</td>
                <td  style="">{{ $row['type'] }}</td>
                <td  style="">{{ $row['statut'] }}</td>
                <td  style="text-align: right;">{{ $row['montant'] }}</td>
                <td  style="">{{ $row['designation_recette'] }}</td>
                <td  style="">{{ $row['reference_reglement'] }}</td>
            </tr>

            @endif
            @endforeach
        </tbody>
    </table>
    <div>
        <div align="right">
            <h4 style="font-style: italic">Imprimé le {{date('d/m/Y à H:i:s')}}</h4>
            <p>{{ Auth::user()->name }}</p>

        </div>

    </div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>
        <!-- Contenu du pied de page ici -->
        @if($imageEntetePied != null)
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
