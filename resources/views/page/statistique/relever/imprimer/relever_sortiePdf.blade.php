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
            margin-bottom: 50px; /* Marge supérieure pour commencer après l'en-tête */
        }

        .titre {
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }

        /* CSS pour l'en-tête et le pied de page */
        @page {

        }

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
            z-index: 1000; /* Assurez-vous que l'en-tête apparaît au-dessus du contenu */
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
</head>
<body>
    <div class="header">
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">

        @endif

    </div>

    <div class="titre" style="">RELEVE DES SORTIES</div>

    <br><br>
     <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
          <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
          <table class="tableInfo">
            <tbody>
                <tr>
                    <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:</td>
                        @if($infoAgence != null)
                            <td>{{$infoAgence['NomAgence']}}</td>
                            @elseif($infoAgence == null)
                            <td>Toutes les agences</td>
                            @endif
                </tr>
              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Client&nbsp;:</td>
                    @if($infoCategorie != null)
                        <td>{{$infoCategorie['Denomination_sociale']}}</td>
                        @elseif($infoCategorie == null)
                        <td>Tous les catégories produits</td>
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

  <table class="tableLigne">
        <thead>
                <tr>
                    <th style="width: 50px;background-color: #3232df;color: white;">Ref</th>
                    <th style="width: 150px;background-color: #3232df;color: white;">Désignation</th>
                    <th style="background-color: #3232df;color: white;">Lundi</th>
                    <th style="background-color: #3232df;color: white;">Mardi</th>
                    <th style="background-color: #3232df;color: white;">Mercredi</th>
                    <th style="background-color: #3232df;color: white;">Jeudi</th>
                    <th style="background-color: #3232df;color: white;">Vendredi</th>
                    <th style="background-color: #3232df;color: white;">Samedi</th>
                    <th style="background-color: #3232df;color: white;">Dimanche</th>
                    <th style="background-color: #3232df;color: white;">Sortie Moy. Jour.</th>
                    <th style="background-color: #3232df;color: white;">Sortie Moy. Hebdo</th>
                </tr>
        </thead>
        <tbody>


            @foreach($data['tableReleverSortieData'] as $item)

            <tr>
                <td>{{$item['Reference']}}</td>
                <td  class="bold">{{ $item['Designation'] }}</td>
                <td style="text-align: right">{{ $item['Lundi'] }}</td>
                <td style="text-align: right">{{ $item['Mardi'] }}</td>
                <td style="text-align: right">{{ $item['Mercredi'] }}</td>
                <td style="text-align: right">{{ $item['Jeudi'] }}</td>
                <td style="text-align: right">{{ $item['Vendredi'] }}</td>
                <td style="text-align: right">{{ $item['Samedi'] }}</td>
                <td style="text-align: right">{{ $item['Dimanche'] }}</td>
                <td style="text-align: right">{{ $item['SortieMoyenneParJour'] }}</td>
                <td style="text-align: right">{{ $item['SortieHebdo'] }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>


    <div class="footer">
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
