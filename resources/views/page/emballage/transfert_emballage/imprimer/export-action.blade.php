@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU TRANSFERT
        </td>
    </tr>
</table>
@foreach ($transfert_produits as $transfert_produit)
    <table>
        <tr>
            <td colspan="4" style="font-weight: bold; font-style: italic;">SOURCE</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:
            </td>
            <td>{{ $transfert_produit->NomAgenceSource }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Date&nbsp;:</td>
            <td>{{ \Carbon\Carbon::parse($transfert_produit->Date_Transfert)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                Reglement&nbsp;:</td>
            <td>{{ $transfert_produit->Reference_Transfert }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">
                Magasin&nbsp;:</td>
            <td>{{ $transfert_produit->NomMagasinSource }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-style: italic; vertical-align: top;">Vendeur&nbsp;:
            </td>
            <td>{{ $transfert_produit->name }}</td>
        </tr>
    </table>

    <table>
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
    </table>

    <table>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Transferée</th>
        </tr>
        <tbody>
            @foreach ($transferers as $value)
                <tr>
                    <td>{{ $value->Reference }}</td>
                    <td>{{ $value->Designation }}</td>
                    <td>{{ $value->Libelle }}</td>
                    <td>{{ $value->Qte_transferee }}</td>
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
