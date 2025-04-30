@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="6"
                style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
<table>
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES PRODUITS</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Type de produit
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Réference
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Désignation
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Catégorie
            </th>
            @if (emballageActiver())
                <th width="200px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Emballage
                </th>
                <th width="200px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Type Emballage
                </th>
            @endif
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Unité de comptage
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Date de création
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getProduits as $getProduit)
            <tr style="border: 3px solid black; ">
                <td style="border: 3px solid black;">{{ $getProduit->Type }}</td>
                <td style="border: 3px solid black; ">{{ $getProduit->Reference }}</td>
                <td style="border: 3px solid black; ">{{ $getProduit->Designation }}</td>
                <td style="border: 3px solid black; ">{{ $getProduit->categorieProduit->Libelle }}</td>
                @if (emballageActiver())
                    <td style="border: 3px solid black; ">{{ ($getProduit->emballage != null) ? $getProduit->emballage->Nom_emballage : ''  }}</td>
                    <td style="border: 3px solid black; ">{{ ($getProduit->typeEmballage != null) ? $getProduit->typeEmballage->Libelle : '' }}</td>
                @endif
                <td style="border: 3px solid black; ">{{ $getProduit->uniteDeComptage->Libelle }}</td>
                <td style="border: 3px solid black; text-align: right;">
                    {{ \Carbon\Carbon::parse($getProduit->created_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>

</table>

<table>
    <tr>
        <td colspan="6" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;text-align: right;">
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
