<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        /* Définir la police pour l'ensemble du document */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 70%;
            margin-top: 100px;
            /* Marge supérieure pour commencer après l'en-tête */
            margin-bottom: 50px;
        }

        .table-container2 {
            width: 50px;
        }

        .titre {
            font-weight: bold;
            font-size: 25px;
            text-align: center;
        }

        .tableLigne {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }

        .tableLigne th,
        .tableLigne td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .tableLigne th {
            background-color: #f2f2f2;
            color: #333;
        }

        .tableLigne tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .tableLigne tr:hover {
            background-color: #ddd;
        }

        .tableInfo {
            border-collapse: collapse;
            /*  width: 100%; */
            /*  border: 1px solid #ddd; */
        }

        .tableInfo th,
        .tableInfo td {
            /*border: 1px solid #ddd;*/
            text-align: left;
        }

        .tableInfodgi {
            border-collapse: collapse;
        }

        .tableInfodgi th,
        .tableInfodgi td {

            padding: 5px;
            text-align: left;
        }

        @page {
            /* margin-top: 0px; */
            /* Espace entre le haut de la page et le début du contenu */
            /* margin-bottom: 50px; */
            /* Espace entre le bas de la page et le pied de page */
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
        }

        .footer {
            bottom: 0;
            height: 50px;
        }

        .box0 {
            width: 40%;
            display: inline-block;
            vertical-align: top;
        }

        .box0p {
            width: 59%;
            display: inline-block;
            vertical-align: top;
        }

        .border {
            border: 1px solid #000;
            /* Assurez-vous d'avoir une bordure visible */
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>

</head>

<body>
    @php
    use Carbon\Carbon;
@endphp
    <div class="header">
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">
        @endif
    </div>
    <div class="titre" style="padding-top: 20px;">LISTES DES MAGASINS </div>
    <br>


    <div style="margin-top: 0px;">
        <div style="margin-bottom: 10px;">

        </div>
    </div>
    <br>
    <div class="table" style="margin-top: 15px;">
        <table class="tableLigne">
            <thead>
                <th style="width: 10px;background-color: #3232df;color: white;">Magasin</th>
                <th style="width: 10px;background-color: #3232df;color: white;">Agence </th>

            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->NomMagasin }}</td>
                        <td>{{ $item->NomF_Agence }}</td>
                    </tr>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
        <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimé par
        </div>
        <div align="right" style= "  ">{{ Auth::user()->name }}</div>
        <div align="right" style= "  ">{{ Carbon::now()->format('d/m/Y H:m:s') }}</div>
    </div>

    {{-- @endforeach --}}
    <style>
        .box1 {
            width: 50%;
            border: 1px solid rgb(130, 129, 129);
            background-color: #ffffff;
            margin: 10px;
            /* Marge entre les div */
            display: inline-block;
            /* Divs sur la même ligne */
            vertical-align: top;
            /* Alignement vertical vers le haut */
        }

        .box2 {

            background-color: #ffffff;
            margin: 10px;
            /* Marge entre les div */
            display: inline-block;
            /* Divs sur la même ligne */
            vertical-align: top;
            /* Alignement vertical vers le haut */

        }

        .box3 {
            width: 30%;
            background-color: #ffffff;

            display: inline-block;
            /* Divs sur la même ligne */
            vertical-align: top;
            /* Alignement vertical vers le haut */
        }

        .box4 {
            width: 50%;
            margin: 10px;
            /* Marge entre les div */

            display: inline-block;
            /* Divs sur la même ligne */
            vertical-align: top;
            /* Alignement vertical vers le haut */
        }

        .page-number:after {
            content: counter(page);
        }

        .total-pages:after {
            content: counter(pages);
        }

        .container {
            page-break-inside: avoid;
            /* Empêche les coupures de page à l'intérieur de cet élément */
        }

        .box1,
        .box2 {
            page-break-inside: avoid;
            /* Empêche les coupures de page à l'intérieur de ces éléments */
        }
    </style>

    <div class="container">
        <div class="container">
            <style>
                .right-align {
                    float: right;
                    margin-right: 50px;
                    /* Ajustez cette valeur selon vos besoins */
                }

                .tableInfox td {
                    vertical-align: top;
                    /* Assure l'alignement en haut des cellules */
                }
            </style>
        </div>
        <br><br>
    </div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>

        @if ($imageEntetePied != null)
            <!-- Contenu du pied de page ici -->
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
