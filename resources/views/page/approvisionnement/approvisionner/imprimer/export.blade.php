@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="5"
                style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif

<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">
            APPROVISIONNEMENT
        </td>
    </tr>
</table>



@foreach ($data_agences as $item)
    <table>
        <tr>
            <td colspan="5" style="font-weight: bold; font-style: italic;">INFO</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Nom Agence Destination&nbsp;:
            </td>
            <td>{{ $item->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
            </td>
            <td>{{ \Carbon\Carbon::parse($item->Date_Appro)->format('d/m/Y H:i')  }}</td>
        </tr>
    </table>
    <table class="tableLigne">
        <thead>
            <th style="width:150px; background-color: #3232df;font-weight: bold; color: white; ">Référence
            </th>
            <th style="width:150px; background-color: #3232df;font-weight: bold; color: white; ">Désignation
            </th>
            <th style="width:150px; background-color: #3232df;font-weight: bold; color: white; ">Catégorie
            </th>
            <th style="width:150px; background-color: #3232df;font-weight: bold; color: white; ">Qté</th>
        </thead>
        <tbody>
            @php
                $solde = 0;
            @endphp
            @foreach ($data_approvisionners as $value)
                @if ($item->Id_Agence_Destination == $value->Id_Agence_Destination)
                    <tr>
                        <td>{{ $value->Reference }}</td>
                        <td>{{ $value->Designation }}</td>
                        <td>{{ $value->Libelle }}</td>
                        <td>{{ $value->Qte_Approvisionnee }}</td>

                    </tr>
                    @php
                        $solde += $value->Qte_Approvisionnee;
                    @endphp
                @endif
            @endforeach
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Solde : </strong></td>
                    <td>{{ $solde }}</td>
                </tr>
            <!-- Ajoutez ici plus de lignes avec des données -->
        </tbody>
    </table>
@endforeach
<table>
    <tr>
        <td colspan="4" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="4" style="font-weight: bold;text-align: right;">
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
