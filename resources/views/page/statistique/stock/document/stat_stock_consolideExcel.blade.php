@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="7"
                style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
<table>
    <tbody>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 20px; text-align: center;">STOCK CONSOLIDE AU
                {{ $dateFin }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="7" style="font-weight: bold; ">Stock consolide</th>
        </tr>
        <tr>
            <td style="font-weight: bold; ">PRODUIT</td>
            @if ($infoProduit == 'Tous')
                <td>Tous</td>
            @else
                <td>{{ $infoProduit['Designation'] }}</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold; ">MAGASIN</td>
            @if ($infoMagasin == 'Tous')
                <td>Tous</td>
            @else
                <td>{{ $infoMagasin['NomMagasin'] }}</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold; ">DATE</td>
            <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au
                {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
        </tr>
    </tbody>
</table>


<table>
    <thead>
        <tr>

            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Code</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Désignation</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Unité</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Magasin</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Quantité</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Prix</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Montant</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tableStatStockConsolideData as $row)
            @if (in_array($row['Reference'], ['Total pour la catégorie']))
                <tr>
                    <td colspan="6" style="font-weight: bold;">{{ $row['Reference'] }}</td>
                    <td style="text-align: right;">{{ $row['Designation'] }}</td>
                </tr>
            @else
                <tr>

                    <td width="100px">{{ $row['Reference'] }}</td>
                    <td width="300px" style="">{{ $row['Designation'] }}</td>
                    <td width="100px" style="">{{ $row['Unite'] }}</td>
                    <td width="100px" style="">{{ $row['Magasin'] }}</td>
                    <td width="200px" style="text-align: right;">{{ $row['Qte_stockee'] }}</td>
                    <td width="200px" style="text-align: right;">{{ $row['Prix_Achat_Net'] }}</td>
                    <td width="200px" style="text-align: right;">{{ $row['montant'] }}</td>
                </tr>
            @endif
        @endforeach
        <tr></tr>
        <tr>
            <th colspan="7" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold;">
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>
