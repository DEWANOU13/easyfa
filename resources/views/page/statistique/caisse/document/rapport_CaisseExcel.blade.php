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

            <th colspan="11"  style="font-weight: bold; font-size: 20px; text-align: center;">RAPPORT DE VENTE </th>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold">Utilisateur</td>
            @if($infoUser != null)
            <td style="font-weight: bold">{{$infoUser['name']}}</td>

            @else
            <td style="font-weight: bold">Tous les utilisateurs</td>
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
            <td> {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
        </tr>

    </tbody>
</table>

<table>
        <thead>
            <tr>
                <th width="200px"style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Date</th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Ref</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Statut</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Type</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Montant</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    D/R</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Ref règlement</th>

            </tr>
        </thead>
    <tbody>
        @foreach($tableRapportCaisseData as $row)
              @if (in_array($row['created_at'], [
            'Total dépense',

        ]))
        <tr>
            <td colspan="" style="font-weight: bold;">{{ $row['created_at'] }}</td>
            <td style="text-align: right;">{{ $row['reference_operation'] }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $row['type'] }}</td>
            <td style="text-align: right;">{{ $row['statut'] }}</td>
        </tr>
        @else
        <tr>

            <td width="100px">{{ $row['created_at'] }}</td>
            <td width="300px" style="">{{ $row['reference_operation'] }}</td>
            <td width="100px" style="">{{ $row['type'] }}</td>
            <td width="100px" style="">{{ $row['statut'] }}</td>
            <td width="100px" style="text-align: right;">{{ $row['montant'] }}</td>
            <td width="300px" style="">{{ $row['designation_recette'] }}</td>
            <td width="100px" style="">{{ $row['reference_reglement'] }}</td>
        </tr>

        @endif
        @endforeach

        <tr>
            <th colspan="7" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td  colspan="7" style="text-align: right; font-weight: bold;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
        </tr>
        <tr>
            <td  colspan="7" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>


