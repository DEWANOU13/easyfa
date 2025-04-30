<!DOCTYPE html>
<html>

<head>
    <title>Export Vente Cumulée</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #dddddd;
            font-weight: bold;
        }
    </style>
</head>
<table>
    <th colspan="9" style="text-align: center; font-weight: bold; font-size: 20px;">HISTORIQUE DES STOCKS</th>
    <tbody>
        <tr></tr>

        {{-- <tr>
            <td style="font-weight: bold">Client</td>
            @if ($client == 'Tous')
            <td>{{ $client }} les clients</td>
            @endif
            @if ($infoClient != null)
            <td>{{ $infoClient['Denomination_sociale'] }}</td>
            @endif
        </tr> --}}


    </tbody>
</table>
<table>
    <thead>
        <tr style="padding: 8px; border: 1px solid black; ">
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Emballage</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Agence</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Magasin</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Référence</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Motif</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Entrée</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Sortie</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Solde</th>
        </tr>
    </thead>
    <tbody>
        @php
             $totalStock = 0;
        @endphp
        @foreach ($data as $value)
            <tr>
                <td width="150px" style="text-align: right;">{{ $value[0] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[1] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[2] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[3] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[4] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[8] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[5] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[6] }}</td>
                <td width="150px" style="text-align: right;">{{ $value[7] }}</td>
            </tr>
            @php
                $totalStock += $value[7];
            @endphp
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="9" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="9" style="font-weight: bold;text-align: right;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
</table>
</body>

</html>
