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
            font-size: 40%;
            margin: 0;
            padding: 0;
        }
        .table-container2 {
            width: 50px;
        }
        .titre{
            font-weight: bold;
            font-size: 7px;
            text-align: center;
        }
            .tableLigne {
            border-collapse: collapse;
            width: 100%;


        }
        .tableLigne th, .tableLigne td {
            border-bottom: 0.1px solid #828080;
            padding: 2px;

        }

       .tableLigne th {

            color: #333;
        }

        .tableLigne tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .tableLigne  tr:hover {
            background-color: #ddd;
        }

        .tableLigne2 {
            border-collapse: collapse;
            width: 100%;


        }
        .tableLigne2 th, .tableLigne2 td {
            border-bottom: 0.1px solid #828080;
            padding: 2px;

        }

        .tableInfo{
            border-collapse: collapse;
           /*  width: 100%; */
           /*  border: 1px solid #ddd; */

        }
        .tableInfo th, .tableInfo td {
        /*             border: 1px solid #ddd;*/

            text-align: left;
        }


        .tableInfodgi {
            border-collapse: collapse;


        }

        .tableInfodgi th, .tableInfodgi td {

        padding: 5px;
        text-align: left;
        }

                @page {
            size: A8;
            margin: 0 10px; /* Supprime les marges haut et bas */
        }

        body::before {
            content: "EDITE PAR EASYFAC";
            position: fixed;
            top: 80%;
            left: -60px; /* Positionné à gauche */
            transform: translateY(-50%) rotate(-90deg); /* Rotation du texte */
            font-size: 4px; /* Ajustez la taille du texte selon vos besoins */
            color: rgb(0, 0, 0); /* Ajustez la couleur et l'opacité du filigrane */
            z-index: -1;
            pointer-events: none; /* Empêche les interactions avec le filigrane */
        }
         .header {
            top: 10;
           /*  height: 20px; */
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
          border: 1px solid #000; /* Assurez-vous d'avoir une bordure visible */
        }

        .clearfix::after {
          content: "";
          display: table;
          clear: both;
        }
        .box1 {



    }

    .box2 {

        background-color: #ffffff;
        margin: 1px; /* Marge entre les div */
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */


    }
    .box3 {
        width: 30%;
        background-color: #ffffff;

        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }
    .box4 {
        width: 50%;
        margin: 10px; /* Marge entre les div */

        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }
    .page-number:after {
            content: counter(page);
        }
        .total-pages:after {
            content: counter(pages);
        }
    .container {
      page-break-inside: avoid; /* Empêche les coupures de page à l'intérieur de cet élément */
    }
    .box1, .box2 {
      page-break-inside: avoid; /* Empêche les coupures de page à l'intérieur de ces éléments */
    }
    </style>

</head>
<body>

      <div class="">
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%;" >
        @endif


    </div>
 <div class="titre">
    @if ($infoFacture->Code_type_facture == "FV")
        FACTURE VENTE
    @elseif ($infoFacture->Code_type_facture == "FA")
        FACTURE AVOIR
    @elseif ($infoFacture->Code_type_facture == "EV")
        FACTURE VENTE A L'EXPORTATION
    @endif
</div>

    <div class="" style="margin-bottom: 2px">

        <table class="" style="width: 100%">
          <tbody>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Numero&nbsp;facture&nbsp;:</td>
                <td align="right">{{$infoFacture->Reference_facture}}</td>
            </tr>
            <tr>
              <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:</td>
              <td align="right">{{$infoFacture->Nom_agence}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Adresse&nbsp;:</td>
                <td align="right">{{ $infoFacture->Adresse_agence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Téléphone&nbsp;:</td>
                <td align="right"> {{ $infoFacture->Telephone_agence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Vendeur&nbsp;:</td>
                <td align="right">{{$infoFacture->Nom_user}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Client&nbsp;:</td>
                <td align="right">{{$infoFacture->Nom_client}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">IFU&nbsp;Client&nbsp;:</td>
                <td align="right">{{$infoFacture->Ifu_client}}</td>
            </tr>



          </tbody>
        </table>
</div>
@if (isset($reference_ancienneFacture))

@if ($reference_ancienneFacture != null)

<div class="tableInfo" style="margin: 10px 0 10px 10px">
    <div ><span style="font-weight: bold">Facture d'origine :</span> {{$numero_ancienne_facture}}</div>
    <div ><span style="font-weight: bold">Référence facture d'origine :</span> {{$reference_ancienneFacture}}</div>
</div>
@endif
@endif

@php
$aibDeductible = $infoTotal->Aib_deductible;
@endphp


    <table class="tableLigne">

        <tbody>
            @php
            $totalTTC = 0;
            $nombreLignes = count($lignefacture);
            $totalHTA = 0;
            $totalHT_B = 0;
            $totalTVA_B = 0;
            $totalHT_C= 0;
            $totalHT_D = 0;
            $totalTVA_D = 0;
            $totalHT_E = 0;
            $totalHT_F = 0;
            $totalHT_Global = 0;


            @endphp
            @forelse ($lignefacture as $ligne )

            @php

            $puHT = $ligne->Prix_unitaire_HT;
            $puHT_Net = $puHT - $puHT * ($ligne->Taux_remise / 100);
            $puNetTTC =$ligne->Prix_revient;
        $MontantNetTTC = $puNetTTC * $ligne->total_qte;
        $MontantNetHT = round($MontantNetTTC / (1 + $ligne->valeur_taxe / 100));
        $totalTTC += $MontantNetTTC;
        if($ligne->Code_lettre == 'A'){
            $totalHTA += $MontantNetHT;

        }
        if($ligne->Code_lettre == 'B'){
            $totalHT_B += $MontantNetHT;
            $totalTVA_B += $MontantNetTTC - $MontantNetHT;

        }
        if($ligne->Code_lettre == 'C'){
            $totalHT_C += $MontantNetHT;
        }
        if($ligne->Code_lettre == 'D'){
            $totalHT_D += $MontantNetHT;
            $totalTVA_D += $MontantNetTTC - $MontantNetHT;
        }
            if($ligne->Code_lettre == 'E'){
                $totalHT_E += $MontantNetHT;
            }
            if($ligne->Code_lettre == 'F'){
                $totalHT_F += $MontantNetHT;
            }
            $totalHT_Global += $MontantNetHT;
                    if ($infoFacture->Aib == 1){

                        if(strlen($infoFacture->Ifu_client) == 13){

                            $aib = $totalHT_Global*0.01;
                        }else {
                            $aib = $totalHT_Global*0.05;
                        }
                    }else {
                        # code...
                        $aib = 0;
                    }


            @endphp
            <tr>
                @if ($ligne->is_emballage == 1)
                <td>{{$ligne->Nom_emballage}} => {{$ligne->Designation}} ({{$ligne->Code_lettre}})<br>{{$puNetTTC}} X {{$ligne->Qte}} </td>
                @else
                <td>{{$ligne->Designation}} ({{$ligne->Code_lettre}}) </br> ({{$puNetTTC}}) X {{$ligne->Qte}} </td>

                @endif
                {{-- <td>{{$ligne->Unite_Comptage}}</td>
                <td>{{number_format(intval($ligne->total_qte), 0, ',', ' ')}}</td>
                <td>{{number_format(intval($puNetTTC), 0, ',', ' ')  }}</td> --}}
                <td align="right">{{ number_format(intval($MontantNetTTC), 0, ',', ' ') }}</td>
            </tr>


            @empty

            @endforelse



        </tbody>
    </table>
    <div style="margin-bottom: 10px"></div>

    <div class="">
        <table class="tableLigne2" >
            <tbody>
                <tr>
                    <td  style="font-weight: bold; font-size: 5px">TOTAL&nbsp;TTC:</td>
                    <td align="right">{{number_format($totalTTC + $aib, 0, ',', ' ')}}</td>
                </tr>
                @if ($totalHTA > 0)
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">EXONERES:</td>
                    <td align="right">{{number_format($totalHTA, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_B > 0)
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">TOTAL&nbsp;HT&nbsp;[B]18%:</td>
                    <td align="right">{{number_format($totalHT_B, 0, ',', ' ') }} </td>
                </tr>
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">TOTAL&nbsp;TVA&nbsp;[B]18%:</td>
                    <td align="right">{{ number_format( intval($totalTVA_B), 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if ($totalHT_C > 0)
                <tr>
                    <td style="font-weight: bold;font-size: 5px">TOTAL&nbsp;HT&nbsp;[C]0%:</td>
                    <td align="right">{{number_format($totalHT_C, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_D > 0)
                <tr>
                    <td style="font-weight: bold;font-size: 5px">TOTAL&nbsp;HT&nbsp;[D]18%:</td>
                    <td align="right">{{number_format($totalHT_D, 0, ',', ' ')}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 5px">TOTAL&nbsp;TVA&nbsp;[D]18%: </td>
                    <td align="right">{{number_format( intval($totalTVA_D), 0, ',', ' ')}}</td>
                </tr>
                @endif
                @if ($totalHT_E > 0)
                <tr>
                    <td style="font-weight: bold;font-size: 5px">REGIME&nbsp;TPS[E]:</td>
                    <td align="right">{{number_format($totalHT_E, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_F > 0)
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">RESERVES:</td>
                    <td align="right">{{number_format($totalHT_F, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($infoFacture->Aib == 1)
                @if(strlen($infoFacture->Ifu_client) == 13)
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">Aib&nbsp;1%:</td>
                    <td align="right">{{number_format($aib, 0, ',', ' ')}}</td>
                </tr>
                @else
                <tr>
                    <td  style="font-weight: bold;font-size: 5px">Aib&nbsp;5%:</td>
                    <td align="right">{{number_format($aib, 0, ',', ' ')}}</td>
                </tr>
                @endif
                @endif
                @if (isset($infoTotal->Aib_deductible) && $infoTotal->Aib_deductible > 0)
                    <tr>
                         <td  style="font-weight: bold;font-size: 5px">Aib&nbsp;Déductible:</td>
                        <td  align="right">{{number_format($infoTotal->Aib_deductible, 0, ',', ' ')}}</td>
                    </tr>

                    @endif

                    <tr>
                        <td  style="font-weight: bold;font-size: 5px">Net&nbsp;à&nbsp;payer:</td>
                       <td  align="right">{{number_format($totalTTC + $aib - $aibDeductible, 0, ',', ' ')}}</td>
                   </tr>
                <tr>
                    <td style="font-weight: bold;font-size: 5px">NBRE&nbsp;D'ARTICLES&nbsp;:</td>
                    <td align="right">{{$nombreLignes}}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="container">

        <div class="container">

            <div class="">
                <div style="text-align: center;font-weight: bold">Code MECeF/DGI</div>
                <div style="text-align: center; font-weight: bold ;font-size: 4px">{{$infoFacture->Code_signature}}</div>

                    <div align="center" style="justify-align: center; margin-left: 40px">
                        <table class=""  >
                            <tbody>
                                <tr>
                                    <td style="font-weight: bold;">MECeF&nbsp;NIM:</td>
                                    <td >{{$infoFacture->Nim_machine}}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">MECeF&nbsp;Compteurs:</td>
                                    <td>{{$infoFacture->Compteur_type_facture}}/{{$infoFacture->Compteur_total}} {{$infoFacture->Code_type_facture}}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">MECeF&nbsp;Heure:</td>
                                    <td>{{ \Carbon\Carbon::parse($infoFacture->Date_signature)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="" style="text-align: center;" >
                        <img src="{{ $qrCode->getDataUri() }}" style="width: 40%" alt="QR Code">
                    </div>
            </div>
                <style>
                    .right-align {
                      float: right;
                      margin-right: 5px; /* Ajustez cette valeur selon vos besoins */
                    }
                    .tableInfox td {
                      vertical-align: top; /* Assure l'alignement en haut des cellules */
                    }
                  </style>

        </div>



    </div>
    <div style="font-style: italic; text-align: center;">
        Merci de votre visite.

    </div>
{{--     <div class="footer" >


        @if($imageEntetePied != null)
        <!-- Contenu du pied de page ici -->
        <img src="{{ $imageEntetePied->pied }}" style="width: 100%; margin-bottom: 10px ">
        @endif
    </div>
 --}}









</body>
</html>
