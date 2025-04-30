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
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU RECEPTION
            APPROVISIONNEMENT
        </td>
    </tr>
</table>



@foreach ($data_magasins as $item)
    <table>
        <tr>
            <td colspan="5" style="font-weight: bold; font-style: italic;">INFO</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Nom Magasin&nbsp;:
            </td>
            <td>{{ $item->NomMagasin }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:
            </td>
            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')  }}</td>
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
            @foreach ($data_receptions as $value)
                @if ($item->Id_Magasin == $value->Id_Magasin)
                    <tr>
                        <td>{{ $value->Reference }}</td>
                        <td>{{ $value->Designation }}</td>
                        <td>{{ $value->Libelle }}</td>
                        <td>{{ $value->Qte_Receptionnee }}</td>

                    </tr>
                    @php
                        $solde += $value->Qte_Receptionnee;
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
