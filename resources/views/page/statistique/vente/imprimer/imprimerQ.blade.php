<!DOCTYPE html>
<html lang="fr">

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
        @if ($data['imageEntetePied'] !== null)
            <img src="{{ $data['imageEntetePied']['entete'] }}" style="width: 100%; margin-top: -50px">
        @endif
    </div>
    @if ($data['reponse'] === 'list_cumule_by_client')
        <div class="titre" style="padding-top: 20px;">LISTES DES VENTES CUMULEES PAR CLIENT</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Raison sociale du client
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VentesTTC</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['raisonSociale'] }}</td>
                            <td>{{ $item['ventesHT'] }}</td>
                            <td>{{ $item['tva'] }}</td>
                            <td>{{ $item['ventesTTC'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'list_cumule_by_day')
        <div class="titre" style="padding-top: 20px;">LISTES DES VENTES CUMULEES PAR JOUR</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Période
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TotalHT</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TotalTTC</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['periode'] }}</td>
                            <td>{{ $item['totalHT'] }}</td>
                            <td>{{ $item['tva'] }}</td>
                            <td>{{ $item['totalTTC'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'list_cumule_by_category')

        <div class="titre" style="padding-top: 20px;">LISTES DES VENTES PAR QUANTITE CUMULEE PAR CATEGORIE</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <div class="clearfix" style="margin-bottom: 20px">
            <div class="box0">
                <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                    <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
                    <table class="tableInfo">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Agence&nbsp;:
                                </td>
                                @if ($data['dataEntete']['agence'] != 'Toutes')
                                    <td>{{ $data['dataEntete']['agence']->NomAgence }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['agence'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Catégorie&nbsp;:
                                </td>
                                @if ($data['dataEntete']['categorie'] != 'Toutes')
                                    <td>{{ $data['dataEntete']['categorie']->Libelle }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['categorie'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Client&nbsp;:
                                </td>
                                @if ($data['dataEntete']['client'] != 'Tous')
                                    <td>{{ $data['dataEntete']['client']->Denomination_sociale }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['client'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
                                </td>
                                <td>{{ $data['dataEntete']['dateDebut'] . ' Au ' . $data['dataEntete']['dateFin'] }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Référence&nbsp;:</td>
                                <td>{{ $inventaire->Reference_Inventaire }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id
                                    vendeur&nbsp;:
                                </td>
                                <td>{{ $infoFacture->user_id }}</td>
                            </tr> --}}
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Enregistrer
                                    par&nbsp;:
                                </td>
                                <td>{{ Auth::user()->name }}</td>

                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>

        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Libelle Catégorie
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Client</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Quantité</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Agence</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Magasin</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['libelle'] }}</td>
                            <td>{{ $item['client'] }}</td>
                            <td>{{ $item['quantite'] }}</td>
                            <td>{{ $item['agence'] }}</td>
                            <td>{{ $item['magasin'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'list_cumule_by_product')
        <div class="titre" style="padding-top: 20px;">LISTES DES VENTES CUMULEES PAR PRODUIT</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <div class="clearfix" style="margin-bottom: 20px">
            <div class="box0">
                <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                    <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
                    <table class="tableInfo">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Agence&nbsp;:
                                </td>
                                @if ($data['dataEntete']['agence'] != 'Toutes')
                                    <td>{{ $data['dataEntete']['agence']->NomAgence }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['agence'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Produit&nbsp;:
                                </td>
                                @if ($data['dataEntete']['produit'] != 'Toutes')
                                    <td>{{ $data['dataEntete']['produit']->Designation }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['produit'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Client&nbsp;:
                                </td>
                                @if ($data['dataEntete']['client'] != 'Tous')
                                    <td>{{ $data['dataEntete']['client']->Denomination_sociale }}</td>
                                @else
                                    <td>{{ $data['dataEntete']['client'] }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
                                </td>
                                <td>{{ $data['dataEntete']['dateDebut'] . ' Au ' . $data['dataEntete']['dateFin'] }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                    Référence&nbsp;:</td>
                                <td>{{ $inventaire->Reference_Inventaire }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id
                                    vendeur&nbsp;:
                                </td>
                                <td>{{ $infoFacture->user_id }}</td>
                            </tr> --}}
                            <tr>
                                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Enregistrer
                                    par&nbsp;:
                                </td>
                                <td>{{ Auth::user()->name }}</td>

                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>

        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Code
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Désignation produit
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Client</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Quantité</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Agence</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Magasin</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['reference'] }}</td>
                            <td>{{ $item['designation'] }}</td>
                            <td>{{ $item['client'] }}</td>
                            <td>{{ $item['quantite'] }}</td>
                            <td>{{ $item['agence'] }}</td>
                            <td>{{ $item['magasin'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'list_cumule_by_agence')
        <div class="titre" style="padding-top: 20px;">LISTES DES VENTES CUMULEES PAR AGENCE</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Désignation agence
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Quantite</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VentesTTC</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['libelle'] }}</td>
                            <td>{{ $item['quantite'] }}</td>
                            <td>{{ $item['venteHT'] }}</td>
                            <td>{{ $item['tva'] }}</td>
                            <td>{{ $item['ventesTTC'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'journal_vente')
        <div class="titre" style="padding-top: 20px;">JOURNAL DES VENTES</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Date
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference Facture</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">AIB</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteTTC</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['ref_facture'] }}</td>
                            <td>{{ $item['venteHT'] }}</td>
                            <td>{{ $item['tva'] }}</td>
                            <td>{{ $item['aib'] }}</td>
                            <td>{{ $item['ventesTTC'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'list_facture_avoir')
        <div class="titre" style="padding-top: 20px;">LISTE DES FACTURES AVOIRS</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Date Facture Avoir
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference Facture Origine
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference Facture</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteTTC</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['reference_ancienneFacture'] }}</td>
                            <td>{{ $item['ref_facture'] }}</td>
                            <td>{{ $item['venteHT'] }}</td>
                            <td>{{ $item['tva'] }}</td>
                            <td>{{ $item['ventesTTC'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($data['reponse'] === 'sale_by_user')
        <div class="titre" style="padding-top: 20px;">Statistique Par Utilisateur</div>
        <br>
        <div style="margin-top: 0px;">
            <div style="margin-bottom: 10px;">
                {{-- <span style="font-weight: bold;">OBJET: {{ $item->Objet_facture }}</span> --}}
            </div>
        </div>
        <br>
        <div class="table" style="margin-top: 15px;">
            <table class="tableLigne">
                <thead>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Date Debut
                    </th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Date Fin</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Client</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Vente</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Avoir</th>
                    <th style="background-color: #3232df;font-weight: bold; color: white; ">Agence</th>
                </thead>
                <tbody>
                    @foreach ($data['dataPrint'] as $item)
                        <tr>
                            <td>{{ $item['date_debut'] }}</td>
                            <td>{{ $item['date_fin'] }}</td>
                            <td>{{ $item['reference'] }}</td>
                            <td>{{ $item['client'] }}</td>
                            <td>{{ $item['vente'] }}</td>
                            <td>{{ $item['avoir'] }}</td>
                            <td>{{ $item['agence'] }}</td>
                            {{-- <td>{{ $item['ventesTTC'] }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

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
        <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
        <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimer le
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}
        </div>
        <div align="right" style= "  ">{{ Auth::user()->name }}</div>

    </div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>

        @if ($data['imageEntetePied'] != null)
            <!-- Contenu du pied de page ici -->
            <img src="{{ $data['imageEntetePied']['pied'] }}" style="width: 100%">
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
