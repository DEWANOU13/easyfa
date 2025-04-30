<table>
    <tr>
        @if ($fournisseur === 'Tous' && $magasin === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES ENTREES
                D'EMBALLAGE ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES ENTREES
                D'EMBALLAGE PAR LE FOURNISSEUR {{ $fournisseur }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES ENTREES
                D'EMBALLAGE DANS LE MAGASIN {{ $magasin->NomMagasin }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES
                ENTREES D'EMBALLAGE DU PRODUIT {{ $produit->Reference }} ENTRE LE
                ({{ $debut_periode }}) ET
                ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES
                ENTREES D'EMBALLAGE POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center; size: 18px; font-weight: bold;">LISTE DES
                ENTREES D'EMBALLAGE DU FOURNISSEUR {{ $fournisseur->DenominationSociale }} PAR LE
                MAGASIN
                {{ $magasin->NomMagasin }} ENTRE LE ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
            <td colspan="6" width="150px" style="text-align: center;">LISTE DES ENTREES D'EMBALLAGE PAR LE
                FOURNISSEUR {{ $fournisseur->DenominationSociale }} DU
                PRODUIT {{ $produit->Reference }} ENTRE LE ({{ $debut_periode }}) ET ({{ $fin_periode }})
            </td>
        @endif
        @if ($fournisseur !== 'Tous' && $magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
            <td width="150px" style="text-align: center;">LISTE DES ENTREES D'EMBALLAGE PAR LE FOURNISSEUR
                {{ $fournisseur->DenominationSociale }} POUR
                LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE ({{ $debut_periode }}) ET
                ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
            <td width="150px" style="text-align: center;">LISTE DES ENTREES D'EMBALLAGE DANS LE MAGASIN
                {{ $magasin->NomMagasin }} DU PRODUIT
                {{ $produit->Reference }} ENTRE LE ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
            <td width="150px" style="text-align: center;">LISTE DES ENTREES D'EMBALLAGE DANS LE MAGASIN
                {{ $magasin->NomMagasin }} POUR LA CATEGORIE
                {{ $categorie->Libelle }} ENTRE LE ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
        @if ($fournisseur === 'Tous' && $magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
            <td width="150px" style="text-align: center;">LISTE DES ENTREES D'EMBALLAGE DANS LE MAGASIN
                {{ $produit->Reference }} POUR LA CATEGORIE
                {{ $categorie->Libelle }} ENTRE LE ({{ $debut_periode }}) ET ({{ $fin_periode }})</td>
        @endif
    </tr>
</table>
@foreach ($getFournisseurEntreeProduits as $fournisseur_entree_produits)
    <table>
        <tr>
            <td style="font-weight: bold">FOURNISSEUR: </td>
            <td>{{ $fournisseur_entree_produits->Denomination_sociale }}</td>
        </tr>
    </table>

    @foreach ($getFournisseur as $entree_produit)
        @if ($fournisseur_entree_produits->Id_Fournisseur === $entree_produit->Id_Fournisseur)
            <table>
                <tr style="border: 3px solid black; ">
                    <td style="border: 3px solid black; ">Agence</td>
                    <td style="border: 3px solid black; ">Date</td>
                    <td style="border: 3px solid black; ">Réference</td>
                    <td style="border: 3px solid black; ">Vendeur</td>
                </tr>
                <tr style="border: 3px solid black; ">
                    <td style="border: 10px solid black; ">SIEGE</td>
                    <td style="border: 10px solid black; ">
                        {{ \Carbon\Carbon::parse($entree_produit->Date_Entree)->format('d/m/Y H:i') }}</td>
                    <td style="border: 10px solid black; ">{{ $entree_produit->Reference_Entree }}</td>
                    <td style="border: 10px solid black; ">{{ $entree_produit->name }}</td>
                </tr>
            </table>
        @endif
        @foreach ($getMagasin as $magasin_value)
            @if (
                $fournisseur_entree_produits->Id_Fournisseur === $entree_produit->Id_Fournisseur &&
                    $fournisseur_entree_produits->Id_Fournisseur === $magasin_value->Id_Fournisseur)
                <table>
                    <tr>
                        <td width="150px" style="text-align: right; font-weight:bold;">MAGASIN</td>
                        <td width="150px" style="text-align: right;">{{ $magasin_value->NomMagasin }}</td>
                    </tr>
                </table>
            @endif
            <table class="tableLigne">
                @if (
                    $fournisseur_entree_produits->Id_Fournisseur === $entree_produit->Id_Fournisseur &&
                        $fournisseur_entree_produits->Id_Fournisseur === $magasin_value->Id_Fournisseur)
                    @if (count($getEntree) > 0)
                        <thead>
                            <tr>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Reference</th>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Désignation</th>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Catégorie</th>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Qté</th>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Prix</th>
                                <th width="300px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Montant</th>
                            </tr>

                        </thead>
                    @endif
                @endif
                <tbody>
                    @foreach ($getEntree as $value)
                        @if (
                            $fournisseur_entree_produits->Id_Fournisseur === $entree_produit->Id_Fournisseur &&
                                $fournisseur_entree_produits->Id_Fournisseur === $magasin_value->Id_Fournisseur &&
                                $entree_produit->Id_Entree_Emballage === $value->Id_Entree_Emballage &&
                                $magasin_value->Id_Magasin === $value->Id_Magasin)
                            <tr>
                                <td width="150px" style="text-align: right;">{{ $value->Reference }}</td>
                                <td width="150px" style="text-align: right;">{{ $value->Designation }}</td>
                                <td width="150px" style="text-align: right;">{{ $value->Libelle }}</td>
                                <td width="150px" style="text-align: right;">{{ $value->Qte_Entree }}</td>
                                <td width="150px" style="text-align: right;">{{ $value->Prix_Achat_Net }}</td>
                                <td width="150px" style="text-align: right;">
                                    {{ $value->Qte_Entree * $value->Prix_Achat_Net }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>
        @endforeach
    @endforeach
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
