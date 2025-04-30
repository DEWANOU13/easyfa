@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU D'ENTREE
        </td>
    </tr>
</table>



@foreach ($entree_produits as $entree_produit)
    <table>
        <tr>
            <td style="font-weight: bold;">Agence</td>
            <td>{{ $entree_produit->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Date</td>
            <td>{{ \Carbon\Carbon::parse($entree_produit->Date_Entree)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">REFERENCES</td>
            <td>{{ $entree_produit->Reference_Entree }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Vendeur</td>
            <td>{{ $entree_produit->name }}</td>
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
                Qté_Entree</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Prix Achat Net</th>
        </tr>
        <tbody>
            @foreach ($entrer_produits as $value)
                <tr>
                    <td>{{ $value->Reference }}</td>
                    <td>{{ $value->Designation }}</td>
                    <td>{{ $value->Libelle }}</td>
                    <td>{{ $value->Qte_Entree }}</td>
                    <td>{{ $value->Prix_Achat_Net }}</td>
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
        <td colspan="1" style="font-weight: bold; background-color: #3232df;font-weight: bold; text-align: center; color: white;">Editer par:</td>
    </tr>
    <tr>
        <td colspan="1" style="font-weight: bold;text-align: center;">EASYFAC</td>
    </tr>
</table>

