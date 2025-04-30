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
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">
        @endif
    </div>
    <div class="titre" style="padding-top: 20px;">BORDEREAU APPROVISIONNEMENT</div>
    <br>
    @foreach ($approvisionnement as $approvisionnements)
    <div class="clearfix" style="margin-bottom: 20px">
        <div class="box0">
            <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                <legend class="float-none w-auto px-1" style="font-weight: bold;">INFOS</legend>
                <table class="tableInfo">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence Destination&nbsp;:
                            </td>
                            <td>{{ $approvisionnements->NomAgence }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
                            <td>{{ \Carbon\Carbon::parse($approvisionnements->Date_Appro)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                            Reférence&nbsp;:</td>
                            <td>{{ $approvisionnements->Reference_Approvisionnement }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Vendeur&nbsp;:
                            </td>
                            <td>{{ $approvisionnements->name }}</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        {{-- <div class="box0p">
            <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                <legend class="float-none w-auto px-1" style="font-weight: bold;">DESTINATION</legend>
                <table class="tableInfo">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                Agence&nbsp;:</td>
                            <td>{{ $transfert_produit->NomAgenceDestination }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Masagin&nbsp;:
                            </td>
                            <td>{{ $transfert_produit->NomMagasinDestination }}</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div> --}}
    </div>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        {{-- @foreach ($reglement as $item) --}}
        <div class="table">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Magasin</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Approvisionnée</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Réceptionnée</th>
                    <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reste_à_Receptionner</th>
                </thead>
                <tbody>
                    @foreach ($approvisionners as $value)
                        <tr>
                            <td>{{ $value->Reference }}</td>
                            <td>{{ $value->Designation }}</td>
                            <td>{{ $value->Libelle }}</td>
                            <td>{{ $value->NomMagasin }}</td>
                            <td>{{ $value->Qte_Approvisionnee }}</td>
                            <td>{{ $value->Qte_Receptionnee }}</td>
                            <td>{{ $value->Qte_Approvisionnee - $value->Qte_Receptionnee}}</td>
                        </tr>
                    @endforeach
                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>
            {{-- <div style="margin-top: 15px;">
                    Montant total restant dû: ({{ number_format($montantRestantDu, 0, ',', ' ') }}) francs CFA
                </div> --}}
        </div>
        {{-- @endforeach --}}
    @endforeach
    <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
    <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">SERVICE FACTURATION
    </div>
    <div align="right" style= "  ">{{ Auth::user()->name }}</div>
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
        {{-- <div>
            Arrêté le présent règlement à la somme de : {{ $montantEnLettres }}
            ({{ number_format($montantTotal, 0, ',', ' ') }}) francs CFA
        </div> --}}
        {{-- <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
        <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">SERVICE FACTURATION
        </div> --}}
        {{-- <div align="right" style= "  ">{{ $infoFacture->Nom_user }}</div> --}}

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
