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
@if($texteEntetePied != null)
<table>
    <tr>
        <td style="font-weight: bold; font-size: 15px">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>
    <th colspan="5" style="text-align: center; font-weight: bold; font-size: 20px;">TITRE</th>
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
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; " scope="col">Raison sociale du client </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; " scope="col">Achat HT </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; " scope="col">TVA </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; " scope="col">AIB</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; " scope="col">Achat TTC</th>
        </tr>
    </thead>
    <tbody>
        {{-- @php
             $totalStock = 0;
        @endphp --}}
        @foreach ($data as $value)
        <tr>
            <td>{{ $value[0] }}</td>
            <td>{{ $value[1] }}</td>
            <td>{{ $value[2] }}</td>
            <td>{{ $value[3] }}</td>
            <td>{{ $value[4] }}</td>
        </tr>
        {{-- @php
            $totalStock += $value[7];
        @endphp --}}
    @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
    <tr>
        <tr>
            <td colspan="5" style="font-weight: bold;text-align: right;">
                Edité par EasyFac</td>
        </tr>
    </tr>
</table>
</body>

</html>
