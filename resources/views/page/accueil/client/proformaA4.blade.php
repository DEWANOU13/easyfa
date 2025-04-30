<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   {{--  <link rel="stylesheet" href="{{ asset('bootstrap-5.3.3-dist/css/bootstrap.min.css') }}">

    <script src="{{ asset('jquery.min.js') }}"></script> --}}

    <title>Document</title>
    <style>
        /* Définir la police pour l'ensemble du document */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 80%;
            margin-top: 100px; /* Marge supérieure pour commencer après l'en-tête */
            margin-bottom: 50px;


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
        .table-container2 {
            width: 50px;
        }
        .titre{
            font-weight: bold;
            font-size: 25px;
            text-align: center;
        }
        /* Style du tableau */
        .tableLigne {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;

        }

        /* Style des cellules du tableau */
        .tableLigne th, .tableLigne td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        /* Style de l'entête du tableau */
       .tableLigne th {
            background-color: #f2f2f2;
            color: #333;
        }
          /* Style des lignes impaires */
          .tableLigne tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Style des lignes au survol */
      .tableLigne  tr:hover {
            background-color: #ddd;
        }

        .tableInfo{
            border-collapse: collapse;



        }
        .tableInfo th, .tableInfo td {
                   
            padding: 3px;

        }


        .tableInfodgi {
            border-collapse: collapse;
            

        }


                .tableInfodgi th, .tableInfodgi td {

        padding: 5px;
        text-align: left;
        }


    </style>
</head>
<body>

      <div class="header">
       @if($imageEntetePied != null)

       <img src="{{ 'easyfac/public/'.$imageEntetePied->entete }}" style="width: 100%; margin-top: -50px">
    @endif
    </div>

<div class="titre" style="padding-top: 20px;">FACTURE PROFORMA: {{$infoFacture->Reference_facture}}</div>


    <br><br>

        <div class="box0">
            <table class="tableInfo">
                <tbody>
                    <tr>
                        <td style="font-weight: bold">Agence :</td>
                        <td>{{$infoAgence->NomAgence}}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Date :</td>
                        <td>{{$infoFacture->Date_facture}}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Numero facture :</td>
                        <td>{{$infoFacture->Reference_facture}}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Id vendeur :</td>
                        <td>{{$infoFacture->user_id}}</td>

                    </tr>

                    <tr>
                        <td style="font-weight: bold">Vendeur :</td>
                        <td>{{$infoVendeur->name}}</td>

                    </tr>

                </tbody>
            </table>

        </div>
        <div class="box0p">
            <table class="tableInfo">
                <tbody>
                    <tr>
                        <td style="font-weight: bold">Code client :</td>
                        <td>{{$infoClient->Code_client}}</td>

                    </tr>
                    <tr>

                        <td style="font-weight: bold">Client :</td>
                        <td>{{$infoClient->Denomination_sociale}}</td>
                    </tr>
                    <tr>

                        <td style="font-weight: bold">Téléphone :</td>
                        <td>{{$infoClient->Telephone_mobile}}</td>
                    </tr>
                    <tr>

                        <td style="font-weight: bold">N° IFU client :</td>
                        @if ($infoClient->Numero_ifu != null)
                            <td>{{$infoClient->Numero_ifu}}</td>
                        @endif
                    </tr>
                    <tr>

                        <td style="font-weight: bold">Adresse client :</td>
                        @if ($infoClient->Adresse_client != null)
                            <td>{{$infoClient->Adresse_client}}</td>


                        @endif
                    </tr>

                </tbody>
            </table>
        </div>
    <br>
    <div style=" margin-top:20px">
        <div style="margin-bottom: 10px"><span style="font-weight: bold ; margin-bottom: 10px">OBJET:</span> {{$infoFacture->Objet_facture}}</div>
        <div style="margin-bottom: 10px"><span style="font-weight: bold ; margin-bottom: 10px">VALIDITE:</span> {{$infoFacture->Validite}}</div>
        @if ($infoFacture->Commentaire != null)
        <div style="margin-bottom: 10px"><span style="font-weight: bold">Commentaire:</span> {{$infoFacture->Commentaire}}</div>
        @endif
        @if ($infoFacture->Autres_infos != null)
        <div style="margin-bottom: 10px"><span style="font-weight: bold">Autres infos:</span> {{$infoFacture->Autres_infos}}</div>
        @endif
    </div>

    @if (isset($reference_ancienneFacture))

    @if ($reference_ancienneFacture != null)
    <table class="tableInfo" style="width: 50%">
        <tbody>
            <tr>
                <td style="font-weight: bold">Référence facture d'origine :</td>
                <td>{{$reference_ancienneFacture}}</td>

            </tr>
        </tbody>
    </table>

    @endif
    @endif


    <br>


<table class="tableLigne">
    <thead>
        <tr>
            <th>REFERENCE</th>
            <th>DESIGNATION </th>
            <th>QUANTITE</th>
            <th>PRIX UNIT TTC</th>
            <th>MONTANT TTC</th>
        </tr>
    </thead>
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
        $puNetTTC =$puHT_Net + ($puHT_Net * $ligne->valeur_taxe) / 100;
        $MontantNetHT = $puHT_Net * $ligne->Qte;
        $MontantNetTTC = $puNetTTC * $ligne->Qte;
        $totalTTC += $MontantNetTTC;
        if($ligne->Code_lettre == 'A'){
            $totalHTA += $MontantNetHT;

        }
        if($ligne->Code_lettre == 'B'){
            $totalHT_B += $MontantNetHT;
            $totalTVA_B += $MontantNetHT*0.18;

        }
        if($ligne->Code_lettre == 'C'){
            $totalHT_C += $MontantNetHT;
        }
        if($ligne->Code_lettre == 'D'){
            $totalHT_D += $MontantNetHT;
            $totalTVA_D += $MontantNetHT*0.18;
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
            <td>{{$ligne->Designation}} ({{$ligne->Code_lettre}})</td>
            <td>{{$ligne->Qte}}</td>
            <td>{{number_format(intval($puNetTTC), 0, ',', ' ')  }}</td>
            <td>{{number_format(intval($MontantNetTTC), 0, ',', ' ')}}</td>

        </tr>

        @empty

        @endforelse

    </tbody>
</table>
<style>
    .container {

    }
    .box0 {
        width: 50%;
        border:1px solid #a7a5a5;
        background-color: #ffffff;
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }
    .box0p {
        float: right;
        width: 40%;
        background-color: #ffffff;
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
        border:1px solid #a7a5a5;
    }

    .box1 {
        width: 50%;
        background-color: #ffffff;
        margin: 10px; /* Marge entre les div */
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }

    .box2 {
        width: 40%;
        background-color: #ffffff;
        margin: 10px; /* Marge entre les div */
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
       
    }
    .box3 {
        width: 20%;
        background-color: #ffffff;
        margin: 10px; /* Marge entre les div */
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }
    .box4 {
        width: 60%;
        margin: 10px; /* Marge entre les div */
       
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }
</style>

<div class="container">
    <div class="box1">

    </div>
    <div class="box2">
        <table class="tableInfodgi">
            <tbody>
                <tr>
                    <td width="50%" style="font-weight: bold; font-size: 12px">TOTAL TTC :</td>
                    <td>{{number_format($totalTTC + $aib, 0, ',', ' ')}}</td>
                </tr>
                @if ($totalHTA > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">EXONERES :</td>
                    <td>{{number_format($totalHTA, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_B > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">TOTAL HT [B]18% :</td>
                    <td>{{number_format($totalHT_B, 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">TOTAL TVA [B]18% :</td>
                    <td>{{ number_format( intval($totalTVA_B), 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if ($totalHT_C > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">TOTAL HT [C]0% :</td>
                    <td>{{number_format($totalHT_C, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_D > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">TOTAL HT [D]18% :</td>
                    <td>{{number_format($totalHT_D, 0, ',', ' ')}}</td>
                </tr>
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">TOTAL TVA [D]18% : </td>
                    <td>{{number_format( intval($totalTVA_D), 0, ',', ' ')}}</td>
                </tr>
                @endif
                @if ($totalHT_E > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">REGIME TPS[E] :</td>
                    <td>{{number_format($totalHT_E, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($totalHT_F > 0)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">RESERVES :</td>
                    <td>{{number_format($totalHT_F, 0, ',', ' ')}}</td>
                </tr>

                @endif
                @if ($infoFacture->Aib == 1)
                @if(strlen($infoFacture->Ifu_client) == 13)
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">Aib 1% :</td>
                    <td>{{number_format($aib, 0, ',', ' ')}}</td>
                </tr>
                @else
                <tr>
                    <td width="50%" style="font-weight: bold;font-size: 12px">Aib 5% :</td>
                    <td>{{number_format($aib, 0, ',', ' ')}}</td>
                </tr>
                @endif
                @endif
                <tr>
                    <td style="font-weight: bold;font-size: 12px">NBRE D'ARTICLES :</td>
                    <td>{{$nombreLignes}}</td>
                </tr>

            </tbody>
        </table>

    </div>
</div>

<br><br>
<div>
    Arrêtée la présente facture à la somme de : {{$montantEnLettres}} ({{number_format($totalTTC + $aib, 0, ',', ' ')}}) francs CFA
</div>
<div align="right" style= "font-weight: bold; margin-right: 80px">TITRE</div>
<div align="right" style= "font-weight: bold; margin-right: 80px; margin-top: 60px">NOM</div>
{{-- <div style="page-break-after: always;"></div>
 --}}







<div class="footer" >
    @if($imageEntetePied != null)

    <img src="{{ 'easyfac/public/'.$imageEntetePied->pied }}" style="width: 100%">
    @endif

</div>



</body>
</html>
