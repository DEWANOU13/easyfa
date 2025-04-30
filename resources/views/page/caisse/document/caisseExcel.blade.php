@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="11"
                style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
<table>
    <tbody>
        <tr>

            <th colspan="8" style="font-weight: bold; font-size: 20px; text-align: center;">RAPPORT DE VENTE </th>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold">AGENCE:</td>
            <td>{{ $infoCaisse->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">Caissier:</td>
            <td>{{ $infoCaisse->user_name }}</td>
        </tr>

        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;ouverture:</td>
            <td>{{ \Carbon\Carbon::parse($infoCaisse->date_ouverture)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;fermeture:</td>
            @if ($infoCaisse->date_fermeture != null)
                <td>{{ \Carbon\Carbon::parse($infoCaisse->date_fermeture)->format('d/m/Y') }}</td>
            @else
                <td>Encore ouverte</td>
            @endif
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
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                D/R</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Ref règlement</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Description</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_depense = 0;
            $total_recette = 0;
        @endphp
        @foreach ($detailCaisse as $detail)
            @if ($detail->designation_depense != null)
                @if ($detail->statut != 'ANNULEE')
                    @php
                        $total_depense += $detail->montant;
                    @endphp
                @endif
            @endif
            @if ($detail->designation_recette != null)
                @if ($detail->statut != 'ANNULEE')
                    @php
                        $total_recette += $detail->montant;
                    @endphp
                @endif
            @endif

            <tr>
                <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $detail->reference_operation }}</td>
                <td>
                    @if ($detail->statut == 'ANNULEE')
                        <span class="status-fermee" style="color: red">ANNULEE</span>
                    @endif
                    @if ($detail->statut == 'EFFECTUEE')
                        <span class="status-ouvert" style="color: green">EFFECTUEE</span>
                    @endif
                </td>
                <td>{{ $detail->type }}</td>
                <td>{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                @if ($detail->designation_recette != null)
                    <td>Recette<br>{{ $detail->designation_recette }}</td>
                @elseif($detail->designation_depense != null)
                    <td>Depense<br>{{ $detail->designation_depense }}</td>
                @else
                    <td></td>
                @endif
                <td>{{ $detail->reference_reglement }}</td>
                <td>{{ $detail->description }}</td>


            </tr>
        @endforeach
    </tbody>
</table>
<table class="tableListe">
    <thead>
        <tr>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Dépense Total</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Recette Total</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Fond Actuel</th>
        </tr>

    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">{{ number_format($total_depense, 0, ',', ' ') }}</td>
            <td style="text-align: center;">{{ number_format($total_recette, 0, ',', ' ') }}</td>
            <td style="text-align: center;">{{ number_format($infoCaisse->fonds_actuel, 0, ',', ' ') }}</td>
        </tr>
    </tbody>

</table>
<table>
    <tr>
        <th colspan="11" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
    </tr>
    <tr>
        <td colspan="11" style="text-align: right; font-weight: bold;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
    <tr>
        <td colspan="11" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
    </tr>
</table>
