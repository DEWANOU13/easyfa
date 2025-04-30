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
</head>

<body>
    <div class="header">
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">
        @endif
    </div><br>

    <div class="titre" style="">LISTE DES CLIENTS</div>

    <table class="tableLigne">
        <thead>
            <tr>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    N°</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Code Client</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Denonmination Sociale</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Numero Ifu</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Adresse</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Numero de telephone</th>
                <th width="70px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['tableClientData'] as $row)
                <tr>
                    <td style="text-align: right">{{ $row['numero'] }}</td>
                    <td style="text-align: right">{{ $row['code_client'] }}</td>
                    <td style="text-align: right">{{ $row['denomination_sociale'] }}</td>
                    <td style="text-align: right">{{ $row['numero_ifu'] }}</td>
                    <td style="text-align: right">{{ $row['adresse'] }}</td>
                    <td style="text-align: right">{{ $row['telephone'] }}</td>
                    <td style="text-align: right">{{ $row['email'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
    use Carbon\Carbon;
@endphp
<div align="right" style= "font-weight: bold; margin-right: 80px"></div>
<div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimé par
</div>
<div align="right" style= "  ">{{ Auth::user()->name }}</div>
<div align="right" style= "  ">{{ Carbon::now()->format('d/m/Y H:m:s') }}</div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>
        @if ($imageEntetePied != null)
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
