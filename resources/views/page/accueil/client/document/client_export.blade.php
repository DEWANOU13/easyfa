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
                La liste des clients
            </th>
        </tr>

        <tr>
            <th></th>
        </tr>

        <tr>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Numero</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Code Client</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Denonmination Sociale</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Numero Ifu</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Adresse</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Numero de telephone</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Email</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($tableClientData as $row)
            <tr>
                <td style="text-align: right">{{ $row['numero'] }}</td>
                <td style="text-align: right">{{ $row['code_client'] }}</td>
                <td style="text-align: right">{{ $row['denomination_sociale'] }}</td>
                <td style="text-align: right">{{ $row['numero_ifu'] }}</td>
                <td style="text-align: right">{{ $row['adresse'] }}</td>
                <td style="text-align: right">{{ $row['telephone'] }}</td>
                <td style="text-align: right">{{ $row['email'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align: center">Aucune donnée disponible dans le tableau</td>
            </tr>
        @endforelse

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold">Auteur : {{ auth()->user()->name }}</td>
        </tr>

        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold">Date : {{ now()->format('d/m/Y H:m:s') }}</td>
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
