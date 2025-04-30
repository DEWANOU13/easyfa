@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU DE SORTIE D'EMBALLAGE
        </td>
    </tr>
</table>
@foreach ($sortie_produits as $sortie_produit)
    <table>
        <tr>
            <td style="font-weight: bold;">Agence</td>
            <td>{{ $sortie_produit->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Date</td>
            <td>{{ \Carbon\Carbon::parse($sortie_produit->Date_Sortie)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">REFERENCES</td>
            <td>{{ $sortie_produit->Reference_Sortie }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Vendeur</td>
            <td>{{ $sortie_produit->name }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Reference</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Désignation</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Catégorie</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Qté_Sortie</th>
        </tr>
        <tbody>
            @foreach ($sortir_produits as $value)
                <tr>
                    <td>{{ $value->Reference }}</td>
                    <td>{{ $value->Designation }}</td>
                    <td>{{ $value->Libelle }}</td>
                    <td>{{ $value->Qte_Sortie }}</td>
                </tr>
            @endforeach
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
