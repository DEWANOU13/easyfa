@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif

<table>
    <tbody>
        <tr>

            <th colspan="5"  style="font-weight: bold; font-size: 20px; text-align: center;">MARGE PAR MOIS </th>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold">AGENCE</td>
            @if($infoAgence != null)
            <td style="font-weight: bold">{{$infoAgence['NomAgence']}}</td>

            @else
            <td style="font-weight: bold">Toutes les agences</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">PERIODE</td>
            <td>{{ \Carbon\Carbon::parse($dateDebut)->format('m/Y') }} à {{ \Carbon\Carbon::parse($dateFin)->format('m/Y') }}</td>
        </tr>

    </tbody>
</table>


<table>
    <thead>
        <tr>

            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Mois</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Vente</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Achat</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Marge</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Taux</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tableMargeParMoisData as $row)
        @if (in_array($row['Date'], [
            'TOTAL',

        ]))
        <tr>
            <td style="text-align: right; font-weight: bold;">{{ $row['Date'] }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $row['montantVente'] }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $row['montantAchat'] }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $row['marge'] }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $row['taux'] }}</td>
        </tr>
        @else
        <tr>

            <td width="200px">{{ $row['Date'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['montantVente'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['montantAchat'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['marge'] }}</td>
            <td width="100px" style="text-align: right;">{{ $row['taux'] }}</td>
        </tr>

        @endif
        @endforeach
        <tr></tr>
        <tr>
            <th colspan="5" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td  colspan="5" style="text-align: right; font-weight: bold;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
        </tr>
        <tr>
            <td  colspan="5" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>


