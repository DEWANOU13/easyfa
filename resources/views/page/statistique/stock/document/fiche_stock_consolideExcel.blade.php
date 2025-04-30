@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="8"
                style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
<table>
    <tbody>
        <tr>

            <th colspan="8" style="font-weight: bold; font-size: 20px; text-align: center;">FICHE STOCK CONSOLIDE</th>
        </tr>
        <tr>
            <td style="font-weight: bold">PRODUIT</td>

            @if ($infoProduit == 'Tous')
                <td>Tous les produits</td>
            @else
                <td>{{ $infoProduit['Designation'] }}</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">MAGASIN</td>
            @if ($infoMagasin != 'Tous')
                <td>{{ $infoMagasin['NomMagasin'] }}</td>
            @else
                <td>Tous les magasins</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>
            <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au
                {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>

        </tr>
    </tbody>
</table>


<table>
    <thead>
        <tr>

            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Réference</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Type opération</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Magasin</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Entrée</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Sortie</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Stock</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Observation</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tableFicheStockConsolideData as $row)
            @if ($row['Date'] == 'Total')
                <tr>
                    <td colspan="4">{{ $row['Date'] }}</td>
                    <td colspan="" style="text-align: right;">{{ $row['Justificatif'] }}</td>
                    <td colspan="" style="text-align: right;">{{ $row['type_operation'] }}</td>
                    <td colspan="" style="text-align: right;">{{ $row['NomMagasin'] }}</td>
                    <td colspan="" style="text-align: right;"></td>
                </tr>
            @else
                <tr>
                    <td width="100px">{{ $row['Date'] }}</td>
                    <td width="200px" style="">{{ $row['Justificatif'] }}</td>
                    <td width="200px" style="">{{ $row['type_operation'] }}</td>
                    <td width="200px" style="">{{ $row['NomMagasin'] }}</td>
                    <td width="100px" style="text-align: right;">{{ $row['entree'] }}</td>
                    <td width="100px" style="text-align: right;">{{ $row['sortie'] }}</td>
                    <td width="100px" style="text-align: right;">{{ $row['stock'] }}</td>
                    <td width="300px" style="text-align: right;">{{ $row['Motif'] }}</td>
                </tr>
            @endif
        @endforeach
        <tr></tr>
        <tr>
            <th colspan="8" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right; font-weight: bold;">
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>
