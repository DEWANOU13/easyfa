@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="11" style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>
    <tbody>
        <tr>

            <th colspan="11"  style="font-weight: bold; font-size: 20px; text-align: center;">SITUATION CLIENT </th>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold">CLIENT</td>
            @if($infoClient != null)
            <td style="font-weight: bold">{{$infoClient['Denomination_sociale']}}</td>

            @else
            <td style="font-weight: bold">Tous les clients</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">AGENCE</td>
            @if($infoAgence != null)
            <td style="font-weight: bold">{{$infoAgence['NomAgence']}}</td>

            @else
            <td style="font-weight: bold">Toutes les Agences</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>
            <td> {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
        </tr>

    </tbody>
</table>
 <table>
        <thead>
            <tr>
                <th width="200px"style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Type</th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Quantité</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Restitué</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Facturé</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Dette</th>


            </tr>
        </thead>
    <tbody>
        @foreach($tableSituationClientData as $row)
              @if (in_array($row['Libelle'], [
            'Ancienne dette','Dette récente'

        ]))
        <tr>
            <td colspan="4" style="font-weight: bold;">{{ $row['Libelle'] }}</td>
        </tr>
        @else
        <tr>

            <td width="100px">{{ $row['Libelle'] }}</td>
            <td width="300px" style="">{{ $row['total_Qte'] }}</td>
            <td width="100px" style="">{{ $row['total_restituee'] }}</td>
            <td width="100px" style="">{{ $row['total_facturee'] }}</td>
            <td width="300px" style="">{{ $row['restant'] }}</td>
        </tr>

        @endif
        @endforeach

        <tr>
            <th colspan="4" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td  colspan="4" style="text-align: right; font-weight: bold;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
        </tr>
        <tr>
            <td  colspan="4" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>


