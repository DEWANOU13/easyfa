@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="6"
                style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif

<table>
    <tr>

        <th colspan="12" style="font-weight: bold; font-size: 20px; text-align: center;">STOCK DU MAGASIN</th>
    </tr>
</table>

<table>
    <tbody>
        <tr></tr>
        <tr>
            <th colspan="6" style="font-weight: bold; ">INFO</th>
        </tr>

        <tr>
            <td style="font-weight: bold; ">MAGASIN</td>
            @if ($infoMagasin != null)
                <td>{{ $infoMagasin['NomMagasin'] }}</td>
            @else
                <td>Tous les magasins</td>
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
    <tr>

        <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Code
        </th>
        <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Désignation</th>
        <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Unité
        </th>
        <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Catégorie</th>
        <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Magasin</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Entrée</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Sortie</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Facture_FV</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Facture_FA</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Transfert</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
            Facture_IN</th>
        <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">SOLDE
        </th>
    </tr>
    <tbody>
        @foreach ($tableStatMagasinData as $row)
            @if ($row['reference'] == 'VALEUR TOTAL DU STOCK')
                <tr>
                    <td colspan="5">{{ $row['reference'] }}</td>
                    <td>{{ $row['designation'] }}</td>
                    <td>{{ $row['unite'] }}</td>
                    <td>{{ $row['categorie'] }}</td>
                    <td>{{ $row['magasin'] }}</td>
                    <td>{{ $row['entree'] }}</td>
                    <td>{{ $row['sortie'] }}</td>
                    <td>{{ $row['facture_fv'] }}</td>
                    <td>{{ $row['facture_fa'] }}</td>
                    <td>{{ $row['transfert'] }}</td>
                    <td>{{ $row['facture_in'] }}</td>
                </tr>
            @else
                <tr>
                    <td>{{ $row['reference'] }}</td>
                    <td>{{ $row['designation'] }}</td>
                    <td>{{ $row['unite'] }}</td>
                    <td>{{ $row['categorie'] }}</td>
                    <td>{{ $row['magasin'] }}</td>
                    <td>{{ $row['entree'] }}</td>
                    <td>{{ $row['sortie'] }}</td>
                    <td>{{ $row['facture_fv'] }}</td>
                    <td>{{ $row['facture_fa'] }}</td>
                    <td>{{ $row['transfert'] }}</td>
                    <td>{{ $row['facture_in'] }}</td>
                    <td>{{ $row['solde'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

<table>
    <tr>
        <th colspan="12" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
    </tr>
    <tr>
        <td colspan="12" style="text-align: right; font-weight: bold;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
    <tr>
        <td colspan="12" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
    </tr>
</table>
