<!-- Table client -->
<table>
    <thead>

        @if ($entetePiedExcel != null)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <td colspan="7" style="background-color: #0d6efd; color: white; font-weight: bold; text-align: center; font-size: 18px; padding-top: 5px; padding-bottom: 5px;">
                    {{ $entetePiedExcel['entete'] }}
                </td>
            </tr>
        @endif

        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>

        <tr>
            <th colspan="7" style="font-weight: bold; text-align: center; font-size: 18px; font-family: Bodoni MT; padding-top: 5px; padding-bottom: 5px;">
                Etat de Compte
            </th>
        </tr>

        <tr>
            <th></th>
        </tr>

        <tr>
            <th width="175px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Operation</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Justificatif</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Debit</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Credit</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Solde</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tableClientCompteData as $row)
            <tr>
                <td style="text-align: right">{{ $row['date'] }}</td>
                <td style="text-align: right">{{ $row['client'] }}</td>
                <td style="text-align: right">{{ $row['operation'] }}</td>
                <td style="text-align: right">{{ $row['justificatif'] }}</td>
                <td style="text-align: right">{{ $row['debit'] }}</td>
                <td style="text-align: right">{{ $row['credit'] }}</td>
                <td style="text-align: right">{{ $row['compte'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold">Auteur : {{ auth()->user()->name }}</td>
        </tr>

        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold">Date : {{ now() }}</td>
        </tr>

        @if ($entetePiedExcel != null)
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">Contact {{ $entetePiedExcel['entete'].' ' }}: {{ $entetePiedExcel['pied'] }}</td>
            </tr>
        @endif

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="background-color: #0d6efd; font-weight: bold; border: 1px solid white; padding : 20px; font-size: 16px; text-align: center">Edité par</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="font-weight: bold; text-align: center">EASYFAC</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
