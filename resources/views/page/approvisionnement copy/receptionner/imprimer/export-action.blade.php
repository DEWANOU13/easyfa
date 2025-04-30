@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU APPROVISIONNEMENT
        </td>
    </tr>
</table>
@foreach ($receptions as $reception)
    <table>
        <tr>
            <td colspan="5" style="font-weight: bold; font-style: italic;">INFO</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:
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

    {{-- <table>
        <tr>
            <td colspan="4" style="font-weight: bold; font-style: italic;">DESTINATION</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                Agence&nbsp;:</td>
            <td>{{ $transfert_produit->NomAgenceDestination }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Masagin&nbsp;:
            </td>
            <td>{{ $transfert_produit->NomMagasinDestination }}</td>
        </tr>
    </table> --}}

    <table>
        <tr>
            <th style="width: 100px; background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
            <th style="width: 100px; background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
            <th style="width: 100px; background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
            <th style="width: 100px; background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Magasin</th>
            <th style="width: 100px; background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Réceptionnée</th>
        </tr>
        <tbody>
            @foreach ($ligne_receptions as $value)
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
