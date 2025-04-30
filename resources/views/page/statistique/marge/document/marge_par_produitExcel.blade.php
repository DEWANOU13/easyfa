@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="7" style="font-weight: bold; font-size: 15px;background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>
    <tbody>
        <tr>

            <th colspan="7"  style="font-weight: bold; font-size: 20px; text-align: center;">MARGE PAR PRODUIT </th>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold">AGENCE</td>
            @if($infoAgence != null)
            <td style="font-weight: bold">{{$infoAgence['NomAgence']}}</td>

            @else
            <td style="font-weight: bold">Toutes les agences</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">PRODUIT</td>
            @if($infoProduit != null)
            <td style="font-weight: bold">{{$infoProduit['Designation']}}</td>

            @else
            <td style="font-weight: bold">Tous les produits</td>
            @endif
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>
            <td> {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
        </tr>
    </tbody>
</table>


<table>
    <thead>
        <tr>

            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Code</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Quantité</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Vente</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Achat</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Marge</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Taux</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tableMargeParProduitData as $row)
        @if (in_array($row['Reference'], [
            'Total pour la catégorie',

        ]))
        <tr>
            <td colspan="3" style="font-weight: bold;">{{ $row['Reference'] }}</td>
            <td style="text-align: right;">{{ $row['Designation'] }}</td>
            <td style="text-align: right;">{{ $row['Quantite'] }}</td>
            <td style="text-align: right;">{{ $row['montantVente'] }}</td>
            <td style="text-align: right;">{{ $row['montantAchat'] }}</td>
        </tr>
        @else
        <tr>

            <td width="100px">{{ $row['Reference'] }}</td>
            <td width="300px" style="">{{ $row['Designation'] }}</td>
            <td width="100px" style="">{{ $row['Quantite'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['montantVente'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['montantAchat'] }}</td>
            <td width="200px" style="text-align: right;">{{ $row['marge'] }}</td>
            <td width="100px" style="text-align: right;">{{ $row['taux'] }}</td>
        </tr>

        @endif
        @endforeach
        <tr></tr>
        <tr>
            <th colspan="7" style="text-align: right; font-weight: bold;">Imprimé par {{ Auth::user()->name }}</th>
        </tr>
        <tr>
            <td  colspan="7" style="text-align: right; font-weight: bold;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
        </tr>
        <tr>
            <td  colspan="7" style="text-align: right; font-weight: bold;">Edité par ESAYFAC</td>
        </tr>
    </tbody>
</table>


