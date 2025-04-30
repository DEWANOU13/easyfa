<!-- TableCategorie client -->
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
                <td colspan="4" style="background-color: #0d6efd; color: white; font-weight: bold; text-align: center; font-size: 18px; padding-top: 5px; padding-bottom: 5px;">
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
            <th colspan="4" style="font-weight: bold; text-align: center; font-size: 18px; padding-top: 5px; padding-bottom: 5px;">
                Liste des categories de client
            </th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>

        <tr>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Numero</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Categorie Client</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date d'enregistrement</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date de modification</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($tableCategorieClientData as $row)
            <tr>
                <td style="text-align: right">{{ $row['numero'] }}</td>
                <td style="text-align: right">{{ $row['categorie_client'] }}</td>
                <td style="text-align: right">{{ $row['enregistre_par'] }}</td>
                <td style="text-align: right">{{ $row['modifie_par'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center">Aucune donnée disponible dans le tableau</td>
            </tr>
        @endforelse

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold">Auteur : {{ auth()->user()->name }}</td>
        </tr>

        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold">Date : {{ now()->format('d/m/Y H:m:s') }}</td>
        </tr>

        @if ($entetePiedExcel != null)
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Contact {{ $entetePiedExcel['entete'].' ' }}: {{ $entetePiedExcel['pied'] }}</td>
            </tr>
        @endif

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="background-color: #0d6efd; border: 1px solid white; padding : 20px; font-size: 16px; text-align: center">Edité par</td>
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
