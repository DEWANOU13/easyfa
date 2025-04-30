
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        /* Définir la police pour l'ensemble du document */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 100px;
            /* Marge supérieure pour commencer après l'en-tête */
            margin-bottom: 50px;
        }

        .table-container2 {
            width: 50px;
        }

        .titre {
            font-weight: bold;
            font-size: 18px;
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
            /*             border: 1px solid #ddd;*/

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
    @if ($imageEntetePied != null)
    <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">
@endif

    <section class="section">
        <div style="display: flex; justify-content: center; align-items:center ; margin-bottom:15px;">
            <div style="text-align: center" class="entente-bordereau"></div>
                <div class="titre" style="padding-top: 20px;">BORDEREAU REGLEMENT DU {{ $debut_periode }} AU
                    {{ $fin_periode }}</div>
        </div>
        @if ($statut === '1')
            @foreach ($reglements as $reglement)
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
                                        <td>{{ $reglement->NomAgence }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Date&nbsp;:</td>
                                        <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Numero&nbsp;Reglement&nbsp;:</td>
                                        <td>{{ $reglement->Reference_Reglement }}</td>
                                    </tr>
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id
                                vendeur&nbsp;:
                            </td>
                            <td>{{ $infoFacture->user_id }}</td>
                        </tr> --}}
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Vendeur&nbsp;:
                                        </td>
                                        <td>{{ $reglement->name }}</td>

                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                    <div class="box0p">
                        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                            <legend class="float-none w-auto px-1" style="font-weight: bold;">CLIENT</legend>
                            <table class="tableInfo">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Code&nbsp;client&nbsp;:</td>
                                        <td>{{ $reglement->Code_client }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Client&nbsp;:
                                        </td>
                                        <td>{{ $reglement->Denomination_sociale }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic ; vertical-align: top">
                                            Téléphone&nbsp;:
                                        </td>
                                        <td>{{ $reglement->Telephone_mobile }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div style="margin-top: 0px;">
                    <div style="margin-bottom: 10px;">
                        <span style="font-weight: bold;">OBSERVATION : {{  $reglement->Observations }}</span>
                    </div>
                </div>
                <br>
                <div >
                    <table class="tableLigne">
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">N° Facture</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Objet</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Mode règlement</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white; ">Montant réglé</th>
                        </thead>
                        <tbody>
                            @php
                                $montantParNiveau = 0;
                                $montantTotal = 0;
                                $montantEnLettres = '';
                            @endphp
                            @foreach ($detail_reglements as $item)
                                @if ($item->Id_Reglement == $reglement->Id_Reglement)
                                    <tr>
                                        <td>{{ $item->Reference_facture }}</td>
                                        <td>{{ $item->Objet_facture }}</td>
                                        <td>{{ $item->Libelle_Operation }}</td>
                                        <td>{{ $item->Montant_Regle }}</td>
                                    </tr>
                                    @php
                                        $montantParNiveau += $item->Montant_Regle;
                                        // $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                                        // $montantEnLettres = ucfirst($formatter->format($montantParNiveau));
                                    @endphp
                                @endif
                                @php
                                    $montantTotal += $item->Montant_Regle;
                                    $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                                    $montantEnLettres = ucfirst($formatter->format($montantTotal));
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                                <td style="font-weight: bold;">{{ $montantParNiveau }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
        @if ($statut === '2')
            @foreach ($reglements as $reglement)
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
                                        {{-- <td>{{ $item->NomAgence }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Date&nbsp;:</td>
                                        <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Numero&nbsp;Reglement&nbsp;:</td>
                                        <td>{{ $reglement->Reference_Reglement }}</td>
                                    </tr>
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id
                                vendeur&nbsp;:
                            </td>
                            <td>{{ $infoFacture->user_id }}</td>
                        </tr> --}}
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Vendeur&nbsp;:
                                        </td>
                                        <td>{{ $reglement->name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                    <div class="box0p">
                        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                            <legend class="float-none w-auto px-1" style="font-weight: bold;">OPERATION</legend>
                            <table >
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Type &nbsp; Operation:
                                        </td>
                                        <td>{{ $reglement->Libelle_Operation }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td style="font-weight: bold; font-style: italic ; vertical-align: top">
                                            Téléphone&nbsp;:
                                        </td>
                                        <td>{{ $reglement->Telephone_mobile }}</td>
                                    </tr> --}}
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top">
                                N°&nbsp;IFU&nbsp;client&nbsp;:</td>
                            @if ($infoFacture->Ifu_client != null)
                                <td>{{ $infoFacture->Ifu_client }}</td>
                            @endif
                        </tr> --}}
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top">
                                Adresse&nbsp;client&nbsp;:</td>
                            @if ($infoClient->Adresse_client != null)
                                <td>{{ $infoClient->Adresse_client }}</td>
                            @endif
                        </tr> --}}
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div style="margin-top: 0px;">
                    <div style="margin-bottom: 10px;">
                        <span style="font-weight: bold;">OBSERVATION : {{  $reglement->Observations }}</span>
                    </div>
                </div>
                <br>

                <div >
                    <table class="tableLigne">
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">N° Facture</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">Objet</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">Client</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">Montant réglé</th>
                        </thead>
                        <tbody>
                            @php
                                $montantParNiveau = 0;
                                $montantTotal = 0;
                                $montantEnLettres = ''; // Initialisez la variable en dehors de la boucl
                            @endphp
                            @foreach ($detail_reglements as $item)
                                @if ($item->Id_Reglement == $reglement->Id_Reglement)
                                    <tr>
                                        <td>{{ $item->Reference_facture }}</td>
                                        <td>{{ $item->Objet_facture }}</td>
                                        <td>{{ $item->Denomination_sociale }}</td>
                                        <td>{{ number_format($item->Montant_Regle, 0, ',', ' ') }}</td>
                                    </tr>
                                    @php
                                        $montantParNiveau += $item->Montant_Regle;
                                    @endphp
                                @endif
                                @php
                                    $montantTotal += $item->Montant_Regle;
                                    $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                                    $montantEnLettres = ucfirst($formatter->format($montantTotal));
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                                <td style="font-weight: bold;">{{ number_format($montantParNiveau, 0, ',', ' ') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
        @if ($statut === '3')
            @foreach ($reglements as $reglement)
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
                                        {{-- <td>{{ $item->NomAgence }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Date&nbsp;:</td>
                                        <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Numero&nbsp;Reglement&nbsp;:</td>
                                        <td>{{ $reglement->Reference_Reglement }}</td>
                                    </tr>
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id
                                vendeur&nbsp;:
                            </td>
                            <td>{{ $infoFacture->user_id }}</td>
                        </tr> --}}
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Vendeur&nbsp;:
                                        </td>
                                        <td>{{ $reglement->name }}</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                    <div class="box0p">
                        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                            <legend class="float-none w-auto px-1" style="font-weight: bold;">OPERATION</legend>
                            <table class="tableInfo">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                                            Type &nbsp; Operation:
                                        </td>
                                        <td>{{ $reglement->Libelle_Operation }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td style="font-weight: bold; font-style: italic ; vertical-align: top">
                                            Téléphone&nbsp;:
                                        </td>
                                        <td>{{ $reglement->Telephone_mobile }}</td>
                                    </tr> --}}
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top">
                                N°&nbsp;IFU&nbsp;client&nbsp;:</td>
                            @if ($infoFacture->Ifu_client != null)
                                <td>{{ $infoFacture->Ifu_client }}</td>
                            @endif
                        </tr> --}}
                                    {{-- <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top">
                                Adresse&nbsp;client&nbsp;:</td>
                            @if ($infoClient->Adresse_client != null)
                                <td>{{ $infoClient->Adresse_client }}</td>
                            @endif
                        </tr> --}}
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div style="margin-top: 0px;">
                    <div style="margin-bottom: 10px;">
                        <span style="font-weight: bold;">OBSERVATION : {{  $reglement->Observations }}</span>
                    </div>
                </div>
                <br>
                <div >
                    <table class="tableLigne">
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">N° Facture</th>
                            <th style="background-color: #3232df;font-weight: bold; color: white;">Objet</th>
                            {{-- <th>Mode règlement</th> --}}
                            <th style="background-color: #3232df;font-weight: bold; color: white;">Montant réglé</th>
                        </thead>
                        <tbody>
                            @php
                                $montantParNiveau = 0;
                                $montantTotal = 0;
                                $montantEnLettres = ''; // Initialisez la variable en dehors de la boucle
                            @endphp
                            @foreach ($detail_reglements as $item)
                                @if ($item->Id_Reglement == $reglement->Id_Reglement)
                                    <tr>
                                        <td>{{ $item->Reference_facture }}</td>
                                        <td>{{ $item->Objet_facture }}</td>
                                        {{-- <td>{{ $item->Denomination_sociale }}</td> --}}
                                        <td>{{ number_format($item->Montant_Regle, 0, ',', ' ') }}</td>
                                    </tr>
                                    @php
                                        $montantParNiveau += $item->Montant_Regle;
                                        // $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                                        // $montantEnLettres = ucfirst($formatter->format($montantParNiveau));
                                    @endphp
                                @endif
                                @php
                                    $montantTotal += $item->Montant_Regle;
                                    $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                                    $montantEnLettres = ucfirst($formatter->format($montantTotal));
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="2" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                                <td style="font-weight: bold;">{{ number_format($montantParNiveau, 0, ',', ' ') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
        <div class="">
            <p>Arrêté le present réglement à la somme de @php
                echo $montantEnLettres .' ('.   number_format(intval($montantTotal), 0, ',', ' ').') FCFA' ;
            @endphp </p>
        </div>
        <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
        <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimer le {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}
        </div>
        <div align="right" style= "  ">{{ Auth::user()->name }}</div>


        @include('layouts.alert')
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
