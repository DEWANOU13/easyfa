<!DOCTYPE html>
<html>

<head>
    <title>Caisse</title>
    <style>
        /* Ajoutez ici votre style CSS pour le PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 150px; /* Espace entre le haut de la page et le début du contenu */
            margin-bottom: 100px; /* Espace entre le bas de la page et le pied de page */
        }

        .tableListe {
            width: 100%;
            border-collapse: collapse;
        }

        .tableListe,
        th,
        .tableListe td {
            border: 1px solid rgb(178, 178, 178);
        }

        .tableListe th,
        .tableListe td {
            padding: 5px;
            text-align: left;
        }

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
        @if($imageEntetePied != null)

        <img src="{{ $imageEntetePied->entete }}" style="width: 100%; margin-top: 0px">

        @endif
    </div>

    <h2 style="text-align: center; text-transform: uppercase; text-decoration: underline; margin-top:100px">Point des mouvements de la caisse</h2>

  <div class="box0">
        <fieldset class="border p-3 rounded-3" style="border-radius: 5px;">
          <legend class="float-none w-auto px-1" style="font-weight: bold;">INFO</legend>
          <table class="tableInfo">
            <tbody>


                <tr>
                    <td style="font-weight: bold">AGENCE:</td>
                    <td>{{$infoCaisse->NomAgence}}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Caissier:</td>
                    <td>{{$infoCaisse->user_name}}</td>
                </tr>

              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;ouverture:</td>
                <td>{{ \Carbon\Carbon::parse($infoCaisse->date_ouverture)->format('d/m/Y') }}</td>
              </tr>
              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;fermeture:</td>
                @if($infoCaisse->date_fermeture != null)
                <td>{{ \Carbon\Carbon::parse($infoCaisse->date_fermeture)->format('d/m/Y') }}</td>
                @else
                <td>Encore ouverte</td>
                @endif
              </tr>
              <tr>
                <td style="font-weight: bold; font-style: italic; vertical-align: top;">Imprimé&nbsp;le:</td>
                <td>{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
              </tr>

            </tbody>
          </table>
        </fieldset>
    </div>

    <br>


    <table class="tableListe">
        <thead>
            <tr>

                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Date</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Ref</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Statut</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Type</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Montant</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">D/R</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Ref règlement</th>
                <th  style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Description</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_depense = 0;
                $total_recette = 0;
            @endphp
            @foreach ($detailCaisse as $detail)

                @if($detail->designation_depense != null)

                    @if($detail->statut != 'ANNULEE')
                        @php
                            $total_depense += $detail->montant;
                        @endphp
                    @endif
                @endif
                @if($detail->designation_recette != null)

                    @if($detail->statut != 'ANNULEE')
                        @php
                            $total_recette += $detail->montant;
                        @endphp
                    @endif
                @endif

            <tr>
                <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $detail->reference_operation }}</td>
                <td>
                    @if ($detail->statut == 'ANNULEE')
                    <span
                        class="status-fermee" style="color: red">ANNULEE</span>
                @endif
                @if ($detail->statut == 'EFFECTUEE')
                    <span
                        class="status-ouvert" style="color: green">EFFECTUEE</span>
                @endif
                </td>
                <td>{{ $detail->type }}</td>
                <td>{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                @if($detail->designation_recette != null)
                <td>Recette<br>{{ $detail->designation_recette }}</td>
                @elseif($detail->designation_depense != null)
                <td>Depense<br>{{ $detail->designation_depense }}</td>

                @else
                <td></td>

                @endif
                <td>{{ $detail->reference_reglement }}</td>
                <td>{{ $detail->description }}</td>


            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="tableListe">
        <thead>
            <tr>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Dépense Total</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Recette Total</th>
                <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Fond Actuel</th>
            </tr>

        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">{{ number_format($total_depense, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ number_format($total_recette, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ number_format($infoCaisse->fonds_actuel, 0, ',', ' ') }}</td>
            </tr>
        </tbody>

    </table>
    <div>
        <div align="right">
            <h4 style="font-style: italic">Imprimé le {{date('d/m/Y à H:i:s')}}</h4>
            <p>{{ Auth::user()->name }}</p>

        </div>

    </div>

    <div class="footer">
        <footer>
            Page <span class="page-number"></span>
        </footer>
        <!-- Contenu du pied de page ici -->
        @if($imageEntetePied != null)
        <img src="{{ $imageEntetePied->pied }}" style="width: 100%; ">

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
