@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU RECEPTION ACHEMINEMENT EMBALLAGE
        </td>
    </tr>
</table>
@foreach ($reception as $reception)
    <table>
        <tr>
            <td colspan="7" style="font-weight: bold; font-style: italic;">INFO</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence Destination&nbsp;:
            </td>
            <td>{{ $reception->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
            <td>{{ \Carbon\Carbon::parse($reception->Date_Reception)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                Reférence&nbsp;:</td>
            <td>{{ $reception->Reference_Reception }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Vendeur&nbsp;:
            </td>
            <td>{{ $reception->name }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="4" style="font-weight: bold; font-style: italic;">DESTINATION</td>
            <td>{{$reception->Observations}}</td>
        </tr>

    </table>

    <table>
        <tr>
            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; width: 100px; ">Reference</th>
            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;  width: 200px; ">Désignation</th>
            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;  width: 150px; ">Catégorie</th>
            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;  width: 200px; ">Magasin</th>
            <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white;  width: 200px; ">Qté_Réceptionnée</th>
        </tr>
        <tbody>
            @foreach ($recetionner as $value)
                <tr>
                    <td>{{ $value->Reference }}</td>
                    <td>{{ $value->Designation }}</td>
                    <td>{{ $value->Libelle }}</td>
                    <td>{{ $value->NomMagasin }}</td>
                    <td>{{ $value->Qte_Receptionnee }}</td>
                </tr>
            @endforeach
            <!-- Ajoutez ici plus de lignes avec des données -->
        </tbody>
    </table>
@endforeach

<table>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
</table>
<table>
    <tr>
        <td colspan="1"
            style="font-weight: bold; background-color: #3232df;font-weight: bold; text-align: center; color: white;">
            Editer par:</td>
    </tr>
    <tr>
        <td colspan="1" style="font-weight: bold;text-align: center;">EASYFAC</td>
    </tr>
</table>
