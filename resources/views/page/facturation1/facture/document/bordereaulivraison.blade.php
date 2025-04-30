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
            font-family: Arial, sans-serif; /* Utilisez la police Arial par défaut */
            font-size: 65%;
            margin-top: 100px;
            margin-bottom: 50px;
        }
        /* Ajoutez vos autres styles CSS ici */
        /* ... */
    </style>
</head>
<body>
    <style>
        .table-container2 {
            width: 50px;
        }
        .titre{
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }
    </style>
     <style>
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
            width: 100%;
            border: 1px solid #ddd;

        }

        .tableInfo th, .tableInfo td {
        /*             border: 1px solid #ddd;*/
            padding: 8px;
            text-align: left;
        }


        .tableInfodgi {
            border-collapse: collapse;
            width: 100%;

        }

        .tableInfodgi th, .tableInfodgi td {

            padding: 5px;
            text-align: left;
        }



    </style>
    <style>
        /* CSS pour l'en-tête et le pied de page */
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
      <div class="header">
        @if($imageEntetePied != null)
        <!-- Contenu de l'en-tête ici -->
        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: -50px" >
        @endif

    </div>

<div class="titre" style="padding-top: 20px;">BORDEREAU DE LIVRAISON </div>
<br>
<div class="clearfix" style="margin-bottom: 10px">
    <div class="box0">
      <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
        <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
        <table class="">
          <tbody>
            <tr>
              <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
              <td>{{$Date_facture}}</td>
            </tr>
            <tr>
              <td style="font-weight: bold; font-style: italic; vertical-align: top;">Numero&nbsp;BL&nbsp;:</td>
              <td>{{$num_BL}}</td>
            </tr>
            <tr>
              <td style="font-weight: bold; font-style: italic; vertical-align: top;">Numero&nbsp;facture&nbsp;:</td>
              <td>{{$infoFacture->Reference_facture}}</td>
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
        <table class="">
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
           {{--  <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top">Adresse&nbsp;client&nbsp;:</td>
                @if ($infoClient->Adresse_client != null)
                          <td>{{$infoClient->Adresse_client}}</td>
                      @endif
              </tr> --}}
          </tbody>
        </table>
      </fieldset>
    </div>
  </div>
  @if($infoFacture->Statut_facture == 'ANNULEE')
  <div style="color: red" >La facture de ce bordereau est annulée . Références de l'annulation : {{$factureAvoir->Reference_facture}}</div>
  @endif


    <br>
    <br>


<table class="tableLigne">
    <thead>
        <tr>
            <th>REF</th>
            <th>DESIGNATION </th>
            <th>QTE</th>
            <th>UNITE</th>
            <th>OBSERVATION</th>
        </tr>
    </thead>
    <tbody>

        @forelse ($lignefacture as $ligne )



        <tr>
            <td width ="50px">{{$ligne->Reference}}</td>
            <td>{{$ligne->Designation}} </td>
            <td width ="10px">{{$ligne->total_qte}}</td>
            <td width ="10px">{{$ligne->Unite_Comptage}}</td>
            <td></td>

        </tr>

        @empty

        @endforelse

    </tbody>
</table>
<style>
    .container {

    }

    .box1 {
        width: 20%;
        background-color: #ffffff;
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }

    .box2 {
        width: 60%;
        background-color: #ffffff;
        display: inline-block; /* Divs sur la même ligne */
        vertical-align: top; /* Alignement vertical vers le haut */
    }

</style>


<br><br>
<div>
    <div align="left" class="box1" style= "font-weight: bold; text-decoration:underline;margin-left: 80px;">Visa du client</div>
<div align="right" class="box2" style= "font-weight: bold;text-decoration:underline;">Visa du fournisseur</div>

</div>




<div class="footer" >
    <!-- Contenu du pied de page ici -->
    @if($imageEntetePied != null)

    <img src="{{ $imageEntetePied->pied }}" style="width: 100%">
    @endif
</div>



</body>
</html>
