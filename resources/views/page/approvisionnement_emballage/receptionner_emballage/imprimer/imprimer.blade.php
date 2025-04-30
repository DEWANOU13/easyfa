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
        <!-- Contenu de l'en-tête ici -->
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">
        @endif
    </div>

    <section class="">

            <div class="titre" style="padding-top: 20px;">RECEPTION APPROVISIONNEMENT EMBALLAGE</div>

            <div class="">
                {{-- <h3>INVENTAIRE N° {{  }}</h3> --}}
            </div>

            @foreach ($data_magasins as $item)
            <div>
                <span style=" margin-top: 30px; font-style: italic; vertical-align: top; border:#333 solid 1px; padding: 5px; border-radius: 5px; padding-left: 10px; box-shadow: 3 5px 10px rgba(0, 0, 0, 0.1); background-color: #f2f2f2"><strong>&nbsp; Date:</strong> {{\Carbon\Carbon::parse($item->created_at )->format('d/m/Y H:i')}}</span>
            </div>
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <!-- Section du nom du magasin -->
                    <div  style="display: flex; align-items: center; justify-content: space-between; width: 100%; margin-top: 20px;">
                        <!-- Section du nom du magasin -->
                        <div >
                            <span style=" margin-top: 30px; font-style: italic; vertical-align: top; border:#333 solid 1px; padding: 5px; border-radius: 5px; padding-left: 10px; box-shadow: 3 5px 10px rgba(0, 0, 0, 0.1); background-color: #f2f2f2"><strong>&nbsp; Nom Magasin:</strong>       {{ $item->NomMagasin }}</span>
                        </div>
                        <!-- Section des informations 1er et 2e, alignées à droite -->
                        <div style="display: flex; align-items: center;">
                        </div>
                    </div>
                    <!-- Section des informations 1er et 2e, alignées à droite -->
                    <div style="display: flex; align-items: center;">

                    </div>
                </div>
                <table class="tableLigne">
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Référence
                            </th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Désignation
                            </th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Catégorie
                            </th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Qté</th>
                        </thead>
                    <tbody>
                        @php
                            $solde = 0;
                        @endphp
                        @foreach ($data_receptions as $value)
                            @if ($item->Id_Magasin == $value->Id_Magasin)
                                <tr>
                                    <td>{{ $value->Reference }}</td>
                                    <td>{{ $value->Designation }}</td>
                                    <td>{{ $value->Libelle }}</td>
                                    <td>{{ $value->Qte_Receptionnee }}</td>

                                </tr>
                                @php
                                    $solde  += $value->Qte_Receptionnee;
                                @endphp
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Solde : </strong></td>
                            <td>{{$solde}}</td>

                        </tr>

                        <!-- Ajoutez ici plus de lignes avec des données -->
                    </tbody>
                </table>
            @endforeach

        <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
        <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimer le
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}
        </div>
        <div align="right" style= "  ">{{ Auth::user()->name }}</div>
    </section>

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
