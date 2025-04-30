
<div style="display: flex; justify-content: center; align-items:center ">
    <div style="text-align: center; margin-bottom: 15px;" class="entente-bordereau">
        @if ($magasin === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS DU MAGASIN {{ $magasin->NomMagasin }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS {{ $produit->Reference }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS DU MAGASINS {{ $magasin->NomMagasin }} DU PRODUIT
                {{ $produit->Reference }}
                ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS DU PRODUIT {{ $produit->Reference }}
                POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
            <h4>LISTE DES SORTIES DE PRODUITS DU MAGASINS {{ $magasin->NomMagasin }}
                POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
                ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
    </div>
</div>


@foreach ($getMagasin as $getMagasin_sortir)
    <div class="">
        <h3>{{ $getMagasin_sortir->NomMagasin }}</h3>
    </div>

    @foreach ($getSortieProduit as $sortie_produit)
        @if (
            $getMagasin_sortir->Id_Magasin === $sortie_produit->Id_Magasin &&
                $sortie_produit->Id_Sortie_Produit === $getMagasin_sortir->Id_Sortie_Produit)
            <table>
                <tr>
                    <td>Date: {{ $sortie_produit->Date_Sortie }}</td>
                    <td>Reference: {{ $sortie_produit->Reference_Sortie }}</td>
                    <td>Enregistrer par: {{ $sortie_produit->name }}</td>
                </tr>
            </table>
        @endif
        <table>

                <thead>
                    <tr>

                        <th width="200px"
                            style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference
                        </th>
                        <th width="300px"
                            style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation
                        </th>
                        <th width="200px"
                            style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie
                        </th>
                        <th width="200px"
                            style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Sortie
                        </th>
                    </tr>
                </thead>


            @if ($getMagasin_sortir->Id_Magasin === $sortie_produit->Id_Magasin)
                @if (count($getSortie) > 0)
                @endif
            @endif
            <tbody>
                @foreach ($getSortie as $value)
                    @if (
                        $value->Id_Sortie_Produit === $sortie_produit->Id_Sortie_Produit &&
                            $value->Id_Sortie_Produit === $getMagasin_sortir->Id_Sortie_Produit)
                        <tr>
                            <td>{{ $value->Reference }}</td>
                            <td>{{ $value->Designation }}</td>
                            <td>{{ $value->Libelle }}</td>
                            <td>{{ $value->Qte_Sortie }}</td>
                        </tr>
                    @endif
                @endforeach
                <!-- Ajoutez ici plus de lignes avec des données -->
            </tbody>
        </table>
    @endforeach
@endforeach

<table>
    <tr>
        <th colspan="4" style="text-align: right; font-weight: bold;"> Imprimer le {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</th>
    </tr>
    <tr>
        <td  colspan="4" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
    </tr>
</table>
