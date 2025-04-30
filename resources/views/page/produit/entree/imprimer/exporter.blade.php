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
        @if ($fournisseur === 'Tous' && $magasin === 'Toutes')
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES ENTREES
                ENTRE ({{ $debut_periode }}) AU ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin === 'Toutes')
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES ENTREES
                DU FOURNISSEUR {{ $fournisseur }} ENTRE LE
                ({{ $debut_periode }}) AU ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin !== 'Toutes')
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES ENTREES
                DANS L'AGENCE {{ $magasin->NomAgnece }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin !== 'Toutes')
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES
                ENTREES DU FourNISSeur DANS L'AGENCE {{ $magasin->NomAgnece }} ENTRE LE
                ({{ $debut_periode }}) AU
                ({{ $fin_periode }})</td>
        @endif
    </tr>
</table>
@foreach ($getFournisseur as $fournisseur)
    <table>
        <tr>
            <td style="font-weight: bold">FOURNISSEUR: </td>
            <td>{{ $fournisseur->DenominationSociale }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold">AGENCE</td>
            <td style="font-weight: bold">{{ $magasin->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>
            <td style="font-weight: bold">
                {{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y H:i') . ' Au ' . \Carbon\Carbon::parse($fin_periode)->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">ENREGISTRER PAR</td>
            <td style="font-weight: bold">{{ Auth::user()->name }}</td>
        </tr>
    </table>


    <table class="tableLigne">

            @if (count($getEntree) > 0)
                <thead>
                    <tr>
                        <th width="300px"
                            style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                            Reference Entrée</th>
                        <th width="300px" style="background-color: #3232df;font-weight: bold; color: white;">Agence
                        </th>
                        <th width="300px" style="background-color: #3232df;font-weight: bold; color: white;">Magasin
                        </th>
                        <th width="300px" style="background-color: #3232df;font-weight: bold; color: white;">
                            Quantite</th>
                        <th width="300px" style="background-color: #3232df;font-weight: bold; color: white;">
                            Prix</th>
                        <th width="300px" style="background-color: #3232df;font-weight: bold; color: white;">
                            Montant</th>
                    </tr>
                </thead>
            @endif
        <tbody>
            @php
                $t_quantity = 0;
                $t_prix = 0;
                $t_montant = 0;
            @endphp
            @foreach ($getEntree as $entree_produit)
                <tr>
                    <td>{{ $entree_produit->Reference_Entree }}</td>
                    <td>{{ $entree_produit->NomAgence }}</td>
                    <td>{{ $entree_produit->NomMagasin }}</td>
                    <td style=" text-align: right">{{ number_format($entree_produit->total_quantity, 0, ',', ' ') }}
                    </td>
                    <td style=" text-align: right">{{ number_format($entree_produit->total_prix, 0, ',', ' ') }}</td>
                    <td style=" text-align: right">
                        {{ number_format($entree_produit->total_prix * $entree_produit->total_quantity, 0, ',', ' ') }}
                    </td>
                </tr>

                @php
                    $t_quantity += $entree_produit->total_quantity;
                    $t_prix += $entree_produit->total_prix;
                    $t_montant += $entree_produit->total_prix * $entree_produit->total_quantity;
                @endphp
            @endforeach
            <tr>
                <td colspan="3"> TOTAL</td>
                <td style=" text-align: right">{{ number_format($t_quantity, 0, ',', ' ') }}</td>
                <td style=" text-align: right">{{ number_format($t_prix, 0, ',', ' ') }}</td>
                <td style=" text-align: right">{{ number_format($t_montant, 0, ',', ' ') }}</td>
            </tr>
            <!-- Ajoutez ici plus de lignes avec des données -->
        </tbody>
    </table>
@endforeach
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
