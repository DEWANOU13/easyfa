<!DOCTYPE html>
<html>

<head>
    <title>Statistique détaillée Facture</title>
    <style>
        /* Ajoutez ici votre style CSS pour le PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 80%;
            margin-top: 200px; /* Espace entre le haut de la page et le début du contenu */
            margin-bottom: 100px; /* Espace entre le bas de la page et le pied de page */
        }

      /*   table {
            width: 100%;
            border-collapse: collapse;
        } */

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
            margin-top: 0;
            margin-bottom: 0;
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
    </style>
</head>

<body>
    <div class="header">
        <!-- Contenu de l'en-tête ici -->
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">

        @endif
    </div>

    <h2 style="text-align: center; text-transform: uppercase; text-decoration: underline">Statistique Détailée des Factures</h2>
    <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
          <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
          <table class="">
            <tbody>
                <tr>
                    <td style="font-weight: bold">Agence</td>
                    @if($infoAgence != null)
                    <td>{{ $infoAgence['NomAgence'] }}</td>
                    @endif
                </tr>
             <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Client&nbsp;:</td>
             @if($infoClient != null)
                <td>{{$infoClient['Denomination_sociale']}}</td>
                @else
                <td>Tous les clients</td>
                @endif
              </tr>
              <tr>
                <td style="font-weight: bold">TAXE</td>
                @if($taxe != 'Tous')
                <td >{{ $taxe}}</td>
                @else
                <td>Toutes les taxes</td>
                @endif
              </tr>
              <tr>
                <td>Date&nbsp;:</td>
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

    <table class="tableLigne">
        <thead>
            <tr>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Taxe</th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Client</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Date</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">N° facture</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Objet</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Vente (FV)</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">Avoir (FA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['tableStatDetailleData'] as $row)
                @if (in_array($row['taxe'], [
                        'Total TVA Taxable 18%',
                        'Total TVA régime d\'exception 18%',
                        'Total AIb facturé',
                        'Total AIb deductible',
                    ]))
                    <tr>
                        <td colspan="5" style="font-weight: bold;">{{ $row['taxe'] }}</td>
                        <td>{{ $row['client'] }}</td>
                        <td>{{ $row['date'] }}</td>
                    </tr>
                @elseif(in_array($row['taxe'], [
                    'TVA Taxable 18%',
                    'TVA régime d\'exception 18%',
                    'AIb facturé',
                    'AIb deductible',
                ]))
                 <tr>
                    <td colspan="7" style="font-weight: bold;">{{ $row['taxe'] }}</td>

                </tr>
                @else
                    <tr>
                        <td style="font-weight: bold;">{{ $row['taxe'] }}</td>
                        <td>{{ $row['client'] }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['nfacture'] }}</td>
                        <td>{{ $row['objet'] }}</td>
                        <td>{{ $row['FV'] }}</td>
                        <td>{{ $row['FA'] }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <!-- Contenu du pied de page ici -->
        @if($imageEntetePied != null)
        <img src="{{ $imageEntetePied->pied }}" style="width: 100%">

        @endif
    </div>
</body>

</html>
