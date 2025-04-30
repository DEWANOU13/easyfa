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
            margin-top: 100px; /* Marge supérieure pour commencer après l'en-tête */
            margin-bottom: 50px;
        }
        .table-container2 {
            width: 50px;
        }
        .titre{
            font-weight: bold;
            font-size: 25px;
            text-align: center;
        }
            .tableLigne {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;

        }
        .tableLigne th, .tableLigne td {
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
        .tableLigne  tr:hover {
            background-color: #ddd;
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
            /* margin-top: 0px; */ /* Espace entre le haut de la page et le début du contenu */
            /* margin-bottom: 50px; */ /* Espace entre le bas de la page et le pied de page */
        }
        .header, .footer {
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
          border: 1px solid #000; /* Assurez-vous d'avoir une bordure visible */
        }

        .clearfix::after {
          content: "";
          display: table;
          clear: both;
        }
    </style>

</head>
<body>

      <div class="header">
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px" >
        @endif


    </div>

@if ($infoFacture->Code_type_facture == "FV")
<div class="titre" style="padding-top: 20px;">FACTURE VENTE</div>

@endif
@if ($infoFacture->Code_type_facture == "FA")
<div class="titre" style="padding-top: 20px;">FACTURE AVOIR</div>
@endif
@if ($infoFacture->Code_type_facture == "EV")
<div class="titre" style="padding-top: 20px;">FACTURE VENTE A L'EXPORTATION</div>

@endif
    <br>
    <div class="clearfix" style="margin-bottom: 20px">
        <div class="box0">
          <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
            <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
            <table class="tableInfo">
              <tbody>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:</td>
                  <td>{{$infoFacture->Nom_agence}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-style: italic; vertical-align: top;">Adresse&nbsp;:</td>
                    <td>{{ $infoFacture->Adresse_agence }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-style: italic; vertical-align: top;">Téléphone&nbsp;:</td>
                    <td>{{ $infoFacture->Telephone_agence }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
                  <td>{{ \Carbon\Carbon::parse($infoFacture->Date_facture)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Numero&nbsp;facture&nbsp;:</td>
                  <td>{{$infoFacture->Reference_facture}}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Id vendeur&nbsp;:</td>
                  <td>{{$infoFacture->user_id}}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Vendeur&nbsp;:</td>
                  <td>{{$infoFacture->Nom_user}}</td>

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
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;" >Code&nbsp;client&nbsp;:</td>
                  <td>{{$infoFacture->Code_client}}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top;">Client&nbsp;:</td>
                  <td>{{$infoFacture->Nom_client}}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic ; vertical-align: top">Téléphone&nbsp;:</td>
                  <td>{{$infoFacture->Telephone_client}}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top">N°&nbsp;IFU&nbsp;client&nbsp;:</td>
                  @if ($infoFacture->Ifu_client != null)
                            <td>{{$infoFacture->Ifu_client}}</td>
                        @endif
                </tr>
                <tr>
                  <td style="font-weight: bold; font-style: italic; vertical-align: top">Adresse&nbsp;client&nbsp;:</td>
                  @if ($infoClient->Adresse_client != null)
                            <td>{{$infoClient->Adresse_client}}</td>
                        @endif
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
      </div>
      <div style="margin-top: 0px;">
        <div style="margin-bottom: 10px;">
          <span style="font-weight: bold;">OBJET:</span> {{$infoFacture->Objet_facture}}
        </div>
        @if ($infoFacture->Commentaire != null)
        <div style="margin-bottom: 10px;">
          <span style="font-weight: bold;">Commentaire:</span> {{$infoFacture->Commentaire}}
        </div>
        @endif
        @if ($infoFacture->Autres_infos != null)
        <div style="margin-bottom: 10px;">
          <span style="font-weight: bold;">Autres infos:</span> {{$infoFacture->Autres_infos}}
        </div>
        @endif
      </div>



    @if (isset($reference_ancienneFacture))

    @if ($reference_ancienneFacture != null)

    <div class="tableInfo" style="margin: 10px 0 10px 10px">
        <div ><span style="font-weight: bold">Facture d'origine :</span> {{$numero_ancienne_facture}}</div>
        <div ><span style="font-weight: bold">Référence facture d'origine :</span> {{$reference_ancienneFacture}}</div>
    </div>
    @endif
    @endif


    <br>


<table class="tableLigne">
    <thead>
        <tr>
            <th width="5%">REF.</th>
            <th width="40%">DESIGNATION </th>
            <th width="10%">UNITE </th>
            <th width="10%">QTE</th>
            <th width="15%">PU TTC</th>
            <th width="20%">MONTANT&nbsp;TTC</th>
        </tr>
    </thead>
    <tbody>
        @php
        $aibDeductible = $infoTotal->Aib_deductible;
    @endphp
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
            <td>{{$ligne->Reference}}</td>
            @if ($ligne->is_emballage == 1)
            <td>{{$ligne->Nom_emballage}} => {{$ligne->Designation}} ({{$ligne->Code_lettre}})</td>
            @else
            <td>{{$ligne->Designation}} ({{$ligne->Code_lettre}})</td>
            @endif
            <td>{{$ligne->Unite_Comptage}}</td>
            <td>{{number_format(intval($ligne->total_qte), 0, ',', ' ')}}</td>
            <td>{{number_format(intval($puNetTTC), 0, ',', ' ')  }}</td>
            <td>{{number_format(intval($MontantNetTTC), 0, ',', ' ') }}</td>

        </tr>


        @empty

        @endforelse



    </tbody>
</table>
<style>


    .box1 {
        width: 50%;
        border:1px solid rgb(130, 129, 129);
        background-color: #ffffff;
        margin: 10px; /* Marge entre les div */
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */

    }

    .box2 {

        background-color: #ffffff;
        margin: 10px; /* Marge entre les div */
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

<div class="container">
    <div class="container">
        <div class="box1">
            <div style="text-align: center;font-weight: bold">Code MECeF/DGI</div>
            <div style="text-align: center; font-weight: bold ;font-size: 12px">{{$infoFacture->Code_signature}}</div>
            <div>
                <div class="box3" >
                    <img src="{{ $qrCode->getDataUri() }}" style="width: 100%" alt="QR Code">
                </div>
                <div class="box4" >
                    <table class="">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold ;font-size: 10px">MECeF&nbsp;NIM:</td>
                                <td>{{$infoFacture->Nim_machine}}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold ;font-size: 10px">MECeF&nbsp;Compteurs:</td>
                                <td>{{$infoFacture->Compteur_type_facture}}/{{$infoFacture->Compteur_total}} {{$infoFacture->Code_type_facture}}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold ;font-size: 10px">MECeF&nbsp;Heure:</td>
                                <td>{{ \Carbon\Carbon::parse($infoFacture->Date_signature)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            <style>
                .right-align {
                  float: right;
                  margin-right: 50px; /* Ajustez cette valeur selon vos besoins */
                }
                .tableInfox td {
                  vertical-align: top; /* Assure l'alignement en haut des cellules */
                }
              </style>
        <div class="box2 right-align">
            <table class="tableInfox" align="right">
                <tbody>
                    <tr>
                        <td  style="font-weight: bold; font-size: 10px">TOTAL&nbsp;TTC:</td>
                        <td>{{number_format($totalTTC + $aib, 0, ',', ' ')}}</td>
                    </tr>
                    @if ($totalHTA > 0)
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">EXONERES:</td>
                        <td>{{number_format($totalHTA, 0, ',', ' ')}}</td>
                    </tr>

                    @endif
                    @if ($totalHT_B > 0)
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">TOTAL&nbsp;HT&nbsp;[B]18%:</td>
                        <td>{{number_format($totalHT_B, 0, ',', ' ') }} </td>
                    </tr>
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">TOTAL&nbsp;TVA&nbsp;[B]18%:</td>
                        <td>{{ number_format( intval($totalTVA_B), 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                    @if ($totalHT_C > 0)
                    <tr>
                        <td style="font-weight: bold;font-size: 10px">TOTAL&nbsp;HT&nbsp;[C]0%:</td>
                        <td>{{number_format($totalHT_C, 0, ',', ' ')}}</td>
                    </tr>

                    @endif
                    @if ($totalHT_D > 0)
                    <tr>
                        <td style="font-weight: bold;font-size: 10px">TOTAL&nbsp;HT&nbsp;[D]18%:</td>
                        <td>{{number_format($totalHT_D, 0, ',', ' ')}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;font-size: 10px">TOTAL&nbsp;TVA&nbsp;[D]18%: </td>
                        <td>{{number_format( intval($totalTVA_D), 0, ',', ' ')}}</td>
                    </tr>
                    @endif
                    @if ($totalHT_E > 0)
                    <tr>
                        <td style="font-weight: bold;font-size: 10px">REGIME&nbsp;TPS[E]:</td>
                        <td>{{number_format($totalHT_E, 0, ',', ' ')}}</td>
                    </tr>

                    @endif
                    @if ($totalHT_F > 0)
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">RESERVES:</td>
                        <td>{{number_format($totalHT_F, 0, ',', ' ')}}</td>
                    </tr>

                    @endif
                    @if ($infoFacture->Aib == 1)
                    @if(strlen($infoFacture->Ifu_client) == 13)
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">Aib&nbsp;1%:</td>
                        <td>{{number_format($aib, 0, ',', ' ')}}</td>
                    </tr>
                    @else
                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">Aib&nbsp;5%:</td>
                        <td>{{number_format($aib, 0, ',', ' ')}}</td>
                    </tr>
                    @endif


                    @endif
                    @if (isset($infoTotal->Aib_deductible) && $infoTotal->Aib_deductible > 0)
                    <tr>
                         <td  style="font-weight: bold;font-size: 10px">Aib&nbsp;Déductible:</td>
                        <td>{{number_format($infoTotal->Aib_deductible, 0, ',', ' ')}}</td>
                    </tr>

                    @endif

                    <tr>
                        <td  style="font-weight: bold;font-size: 10px">Net&nbsp;à&nbsp;payer:</td>
                       <td>{{number_format($totalTTC + $aib - $aibDeductible, 0, ',', ' ')}}</td>
                   </tr>

                    <tr>
                        <td style="font-weight: bold;font-size: 10px">NBRE&nbsp;D'ARTICLES&nbsp;:</td>
                        <td>{{$nombreLignes}}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    <br><br>
    <div>

        Arrêtée la présente facture à la somme de : {{$montantEnLettres}} ({{number_format($totalTTC + $aib - $aibDeductible, 0, ',', ' ')}}) francs CFA
    </div>
    <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
    <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">{{$signataire->titre_signataire_facture}}</div>
    <br>
    <br>
    <br>
    <div align="right" style= "  ">{{$signataire->nom_signataire}}</div>


</div>



<div class="footer" >
    <footer>
        Page <span class="page-number"></span>
    </footer>

    @if($imageEntetePied != null)
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
