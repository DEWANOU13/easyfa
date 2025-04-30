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
    <style>
        /* CSS pour l'en-tête et le pied de page */
        @page {
            margin-top: 0;
            margin-bottom: 10px;
        }


        .header,
        .footer {
            position: fixed;
            width: 100%;
            text-align: center;
        }

        .header {
            top: 0;
            height: 100px;
        }

        .footer {
            bottom: 0;
            height: 50px;
        }
    </style>
</head>

<body>
    <section class="section">


        <div class="header">
            <!-- Contenu de l'en-tête ici -->
            @if ($imageEntetePied != null)
                <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">
            @endif
        </div>

        {{-- @if ($statut == '1') --}}
        <div class="titre" style="padding-top: 50px;">
                LISTE DES RECEPTIONS ACHEMINEMENT  PAR PERIODE
        </div>
        <div class="box0">
            <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
                <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
                <table class="">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date debut&nbsp;:
                            </td>
                            <td>{{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y: H:i') }}
                            </td>

                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date fin&nbsp;:
                            </td>
                            <td>{{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y: H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:
                            </td>
                            @if($infoAgence != null)
                            <td>{{$infoAgence['NomAgence']}}</td>
                            @elseif($infoAgence == null)
                            <td>Toutes les agences</td>
                            @endif
                        </tr>



                    </tbody>
                </table>
            </fieldset>
        </div>
        <br>

           <div class="table">
                <!-- Placer la section <thead> en haut du fichier PDF -->
                <div class="table" style="margin-bottom: 15px;">
                    <table class="tableLigne table1">
                        @php
                            $showHeader = false; // Initialisez la variable à false
                        @endphp
                        <thead>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Date</th>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Reference</th>
                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Agence</th>

                            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Utilisateur</th>

                        </thead>
                        <tbody>
                            @foreach ($getAcheminements as $value)

                            <tr>
                                        <td> {{ \Carbon\Carbon::parse($value->Date_Reception )->format('d/m/Y: H:i') }}</td>
                                        <td>{{ $value->Reference_Reception }}</td>
                                        <td>{{ $value->NomAgence }}</td>
                                        <td>{{ $value->name }}</td>
                                    </tr>

                            @endforeach
                            <!-- Ajoutez ici plus de lignes avec des données -->
                        </tbody>
                    </table>
                </div>
            </div>

    </section>
    <br>

    <div style="text-align: right; font-weight: bold;"> Imprimer le
        {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</div>
    <div style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</div>
    </table>
    <div class="footer">
        @if ($imageEntetePied != null)
            <img src="{{ $imageEntetePied->pied }}" style="width: 100%">
        @endif
    </div>
</body>

</html>
