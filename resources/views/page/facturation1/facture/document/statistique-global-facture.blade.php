@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white; ">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>
    <th colspan="5" style="text-align: center; font-weight: bold; font-size: 20px;">STATISTIQUE GLOBAL</th>
    <tbody>
        <tr></tr>

        <tr>
            <td style="font-weight: bold">Agence</td>
            @if($infoClient != null)
            <td>{{ $infoAgence['NomAgence'] }}</td>
            @endif
        </tr>

        <tr>
            <td style="font-weight: bold">CLIENT</td>
            @if($client == 'Tous')
            <td>{{ $client }} les clients</td>
            @endif
            @if($infoClient != null)
            <td>{{ $infoClient['Denomination_sociale'] }}</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>

            <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>


        </tr>


    </tbody>
</table>

<table>
    <thead>
        <tr>

            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Vente (FV)</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Avoir (FA)</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Exportation (EV)</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Avoir d'exportation (EA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tableStatGlobalData as $row)
            <tr>

                <td width="300px">{{ $row['category'] }}</td>
                <td width="100px" style="text-align: right;">{{ $row['FV'] }}</td>
                <td width="100px" style="text-align: right;">{{ $row['FA'] }}</td>
                <td width="200px" style="text-align: right;">{{ $row['EV'] }}</td>
                <td width="200px" style="text-align: right;">{{ $row['EA'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Table des différences -->
<table>
    <thead>
        <tr>

            <th   style="background-color: #00cb51;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
            <th colspan="2" style="background-color: #00cb51;font-weight: bold; text-align: center; color: white; ">Montant à l'intérieur (FV - FA)</th>
            <th colspan="" style="background-color: #00cb51;font-weight: bold; text-align: center; color: white; ">Montant à l'extérieur (EV - EA)</th>
            <th style="background-color: #00cb51;font-weight: bold; text-align: center; color: white; ">Montant total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($diffTableData as $row)
            <tr>

                <td >{{ $row['category'] }}</td>
                <td colspan="2" style="text-align: right;">{{ $row['montantInterieur'] }}</td>
                <td colspan="" style="text-align: right;">{{ $row['montantExterieur'] }}</td>
                <td style="text-align: right;">{{ $row['montantTotal'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
    </tr>
    <tr>
        <td colspan="5" style="text-align: right;font-weight: bold">Edité par EasyFac</td>
    </tr>
</table>
