@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="5"
                style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
@if (
    $reponse === 'fiche_stock' ||
        $reponse === 'fiche_comptage' ||
        $reponse === 'ecart_stock' ||
        $reponse === 'valorisation_ecart' ||
        $reponse === 'bilan_inventaire' ||
        $reponse === 'valorisation_detail_inventaire')

    <table>
        <tr>
            @if ($reponse === 'fiche_stock')
                <td colspan="5"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    FICHE DE STOCK</td>
            @endif

            @if ($reponse === 'fiche_comptage')
                <td colspan="5"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    FICHE DE COMPTAGE</td>
            @endif

            @if ($reponse === 'ecart_stock')
                <td colspan="6"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    FICHE ECART STOCK</td>
            @endif

            @if ($reponse === 'valorisation_ecart')
                <td colspan="6"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    VALORISATION DES ECARTS</td>
            @endif

            @if ($reponse === 'bilan_inventaire')
                <td colspan="10"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    BILAN INVENTAIRE</td>
            @endif

            @if ($reponse === 'valorisation_detail_inventaire')
                <td colspan="12"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    VALORISATION DETAILLEE INVENTAIRE</td>
            @endif
        </tr>
    </table>

    <table>

        <tr>
            <td style="font-weight: bold">AGENCE</td>
            <td style="font-weight: bold">{{ $inventaire->NomAgence }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">INVENTAIRE N°</td>
            <td style="font-weight: bold">{{ $inventaire->Reference_Inventaire }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">DATE</td>
            <td style="font-weight: bold">{{ $inventaire->Date_Inventaire }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">ENREGISTRER PAR</td>
            <td style="font-weight: bold">{{ $inventaire->name }}</td>
        </tr>
    </table>

    @foreach ($inventaire_magasin as $item)
        <div class="entete">
            <h3>{{ $item->NomMagasin }}</h3>
        </div>
        @if ($reponse === 'fiche_comptage')
            <table>
                <tr>
                    <td>1er</td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="border: 1px solid black; width: 150px; height: 20px; padding: 10px; margin-left: 5px;">
                    </td>
                </tr>
                <tr>
                    <td>2e</td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="border: 1px solid black; width: 150px; height: 20px; padding: 10px; margin-left: 5px;">
                    </td>
                </tr>
            </table>
        @endif
        <table class="table1">
            @if (count($getInventaire) > 0)
                <thead>

                    <thead>

                        <tr>
                            @if ($reponse === 'fiche_stock' || $reponse === 'fiche_comptage')
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Référence</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Désignation</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Catégorie</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Unite Comptage</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Qté</th>
                            @endif


                            @if ($reponse === 'bilan_inventaire' || $reponse === 'valorisation_detail_inventaire')
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Référence</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Désignation</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Catégorie</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Unite Comptage</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Magasin</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Qté Théorique</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Qté Comptée</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Ecart</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Justificatif</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Qté Justifiée</th>
                            @endif
                            @if ($reponse === 'valorisation_detail_inventaire')
                                <th width="200px" style="background-color: #3232df;font-weight: bold; color: white; ">
                                    P.A.U.Moy</th>
                                <th width="200px" style="background-color: #3232df;font-weight: bold; color: white; ">
                                    Montant</th>
                            @endif

                        </tr>
                    </thead>
            @endif

            <tbody>
                @php
                    $solde = 0;
                    $solde_prix = 0;
                    $solde_ecart = 0;
                    $solde_stock = 0;
                    $prix = 0;
                    $montant = 0;
                @endphp
                @foreach ($getInventaire as $value)
                    @if ($item->Id_Magasin == $value->Id_Magasin)
                        <tr>
                            @if (
                                $reponse === 'fiche_stock' ||
                                    $reponse === 'fiche_comptage' ||
                                    $reponse === 'bilan_inventaire' ||
                                    $reponse === 'valorisation_detail_inventaire')
                                <td>{{ $value->Reference }}</td>
                                <td>{{ $value->Designation }}</td>
                                <td>{{ $value->Libelle }}</td>
                            @endif
                            @if ($reponse == 'fiche_stock')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td>{{ number_format($value->Qte_Initiale, 0, ',', ' ') }}</td>
                                @php
                                    $solde_stock += $value->Qte_Initiale;
                                @endphp
                            @endif

                            @if ($reponse === 'fiche_comptage')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td></td>
                            @endif

                            @if ($reponse === 'bilan_inventaire' || $reponse === 'valorisation_detail_inventaire')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td>{{ $value->NomMagasin }}</td>
                                <td>{{ number_format($value->Qte_stockee, 0, ',', ' ') }}</td>
                                <td>{{ number_format($value->Qte_Comptee, 0, ',', ' ') }}</td>
                                <td>{{ number_format($value->Qte_Ecart, 0, ',', ' ') }}</td>
                                <td></td>
                                <td>{{ number_format($value->Qte_Ajustee, 0, ',', ' ') }}</td>
                            @endif

                            @if ($reponse === 'valorisation_detail_inventaire')
                                <td style="text-align: right;">{{ number_format($value->Prix_Achat_Net, 2, ',', ' ') }}
                                </td>
                                <td style="text-align: right;">
                                    {{ number_format($value->Prix_Achat_Net * $value->Qte_Comptee, 2, ',', ' ') }}
                            @endif
                        </tr>
                        @if ($reponse === 'fiche_stock')
                            @php
                                $solde += $value->Qte_stockee;
                            @endphp
                        @endif
                        @if ($reponse === 'bilan_inventaire')
                            @php
                                $solde_stock += $value->Qte_stockee;
                                $solde_prix += $value->Qte_Comptee;
                                $solde_ecart += $value->Qte_Ecart;
                                $solde += $value->Qte_Ajustee;
                                @endphp
                        @endif
                        @if ($reponse === 'valorisation_detail_inventaire')
                        @php
                                $solde_stock += $value->Qte_stockee;
                                $solde_prix += $value->Qte_Comptee;
                                $solde_ecart += $value->Qte_Ecart;
                                $solde += $value->Qte_Ajustee;
                                $prix += $value->Prix_Achat_Net;
                                $montant += $value->Prix_Achat_Net * $value->Qte_Comptee;
                            @endphp
                        @endif
                    @endif
                @endforeach
                @if ($reponse === 'fiche_stock')
                    <tr>
                        <td colspan="4" style="font-weight: bold; text-align: right;">Solde</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ $solde }}</td>
                    </tr>
                @endif
                @if ($reponse === 'bilan_inventaire')
                    <tr>
                        <td colspan="5" style="font-weight: bold; text-align: right;">Solde</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_stock, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_prix, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_ecart, 0, ',', ' ') }}</td>
                        <td></td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde, 0, ',', ' ') }}</td>
                    </tr>
                @endif
                @if ($reponse === 'valorisation_detail_inventaire')
                    <tr>
                        <td colspan="5" style="font-weight: bold; text-align: right;">Solde</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_stock, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_prix, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_ecart, 0, ',', ' ') }}</td>
                        <td></td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($prix, 2, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($montant, 2, ',', ' ') }}</td>
                    </tr>
                @endif

                {{-- @if ($reponse === 'valorisation_ecart')
                    <tr>
                        <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_ecart, 0, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde_prix, 2, ',', ' ') }}</td>
                        <td style="font-weight: bold; text-align: center; text-align: right">
                            {{ number_format($solde, 2, ',', ' ') }}</td>
                    </tr>
                @endif --}}

                <!-- Ajoutez ici plus de lignes avec des données -->
            </tbody>
        </table>

        @if ($reponse === 'ecart_stock' || $reponse === 'valorisation_ecart')
            <table class="table1">
                @if (count($getInventaire) > 0)
                    <thead>

                        <thead>
                            @if ($reponse === 'valorisation_ecart' || $reponse === 'ecart_stock')
                                <tr>
                                    <td colspan="6"><strong>Ecart Surplus</strong></td>
                                </tr>
                            @endif
                            <tr>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Référence</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Désignation</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Catégorie</th>

                                @if ($reponse === 'valorisation_ecart')
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Ecart</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Prix Achat</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Montant</th>
                                @endif
                                @if ($reponse === 'ecart_stock')
                                    {{-- <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Unite Comptage</th> --}}
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Qté fiche de stock</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Ecart</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Justificatif</th>
                                @endif
                            </tr>
                        </thead>
                @endif

                <tbody>
                    @php
                        $solde = 0;
                        $solde_prix = 0;
                        $solde_ecart = 0;
                        $solde_stock = 0;
                    @endphp
                    @foreach ($getInventaire as $value)
                        @if ($item->Id_Magasin == $value->Id_Magasin && $value->Qte_Ecart > 0)
                            <tr>
                                <td>{{ $value->Reference }}</td>
                                <td>{{ $value->Designation }}</td>
                                <td>{{ $value->Libelle }}</td>
                                @if ($reponse === 'ecart_stock')
                                    {{-- <td>{{ $value->Libelle_Comptage }}</td> --}}
                                    <td>{{ number_format($value->Qte_stockee, 0, ',', ' ') }}</td>
                                    @php
                                        $solde_stock += $value->Qte_stockee;
                                    @endphp
                                @endif

                                @if ($reponse === 'ecart_stock')
                                    <td>{{ number_format($value->Qte_Ecart, 0, ',', ' ') }}</td>
                                    <td></td>
                                    @php
                                        $solde_ecart += $value->Qte_Ecart;
                                    @endphp
                                @endif
                                @if ($reponse === 'valorisation_ecart')
                                    <td style="text-align: right;">
                                        {{ number_format($value->Qte_Ecart, 0, ',', ' ') }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($value->Prix_Achat_Net, 2, ',', ' ') }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($value->Prix_Achat_Net * $value->Qte_Ecart, 2, ',', ' ') }}
                                    </td>
                                @endif
                            </tr>
                            @if ($reponse === 'ecart_stock')
                                @php
                                    $solde += $value->Qte_Ecart;
                                @endphp
                            @endif
                            @if ($reponse === 'valorisation_ecart')
                                @php
                                    $solde += $value->Prix_Achat_Net * $value->Qte_Ecart;
                                    $solde_prix += $value->Prix_Achat_Net;
                                    $solde_ecart += $value->Qte_Ecart;
                                @endphp
                            @endif
                        @endif
                    @endforeach
                    {{-- @if ($reponse === 'fiche_stock')
                        <tr>
                            <td colspan="4" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 0, ',', ' ') }}</td>
                        </tr>
                    @endif --}}
                    @if ($reponse === 'ecart_stock')
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_stock, 0, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 0, ',', ' ') }}</td>
                        </tr>
                    @endif
                    @if ($reponse === 'valorisation_ecart')
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_ecart, 0, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_prix, 2, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 2, ',', ' ') }}</td>
                        </tr>
                    @endif

                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>
        @endif
        @if ($reponse === 'ecart_stock' || $reponse === 'valorisation_ecart')
            <table class="table1">
                @if (count($getInventaire) > 0)
                    <thead>

                        <thead>
                            @if ($reponse === 'valorisation_ecart' || $reponse === 'ecart_stock')
                                <tr>
                                    <td colspan="6"><strong>Ecart Manquant</strong></td>
                                </tr>
                            @endif
                            <tr>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Référence</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Désignation</th>
                                <th width="200px"
                                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                    Catégorie</th>

                                @if ($reponse === 'valorisation_ecart')
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Ecart</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Prix Achat</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Montant</th>
                                @endif
                                @if ($reponse === 'ecart_stock')
                                    {{-- <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Unite Comptage</th> --}}
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Qté fiche de stock</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Ecart</th>
                                    <th width="200px"
                                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                        Justificatif</th>
                                @endif
                            </tr>
                        </thead>
                @endif

                <tbody>
                    @php
                        $solde = 0;
                        $solde_prix = 0;
                        $solde_ecart = 0;
                        $solde_stock = 0;
                    @endphp
                    @foreach ($getInventaire as $value)
                        @if ($item->Id_Magasin == $value->Id_Magasin && $value->Qte_Ecart < 0)
                            <tr>
                                <td>{{ $value->Reference }}</td>
                                <td>{{ $value->Designation }}</td>
                                <td>{{ $value->Libelle }}</td>
                                @if ($reponse === 'ecart_stock')
                                    {{-- <td>{{ $value->Libelle_Comptage }}</td> --}}
                                    <td>{{ number_format($value->Qte_stockee, 0, ',', ' ') }}</td>
                                    @php
                                        $solde_stock += $value->Qte_stockee;
                                    @endphp
                                @endif

                                @if ($reponse === 'ecart_stock')
                                    <td>{{ number_format($value->Qte_Ecart, 0, ',', ' ') }}</td>
                                    <td></td>
                                    @php
                                        $solde_ecart += $value->Qte_Ecart;
                                    @endphp
                                @endif
                                @if ($reponse === 'valorisation_ecart')
                                    <td style="text-align: right;">{{ number_format($value->Qte_Ecart, 0, ',', ' ') }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ number_format($value->Prix_Achat_Net, 2, ',', ' ') }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($value->Prix_Achat_Net * $value->Qte_Ecart, 2, ',', ' ') }}
                                    </td>
                                @endif
                            </tr>
                            @if ($reponse === 'ecart_stock')
                                @php
                                    $solde += $value->Qte_Ecart;
                                @endphp
                            @endif
                            @if ($reponse === 'valorisation_ecart')
                                @php
                                    $solde += $value->Prix_Achat_Net * $value->Qte_Ecart;
                                    $solde_prix += $value->Prix_Achat_Net;
                                    $solde_ecart += $value->Qte_Ecart;
                                @endphp
                            @endif
                        @endif
                    @endforeach
                    {{-- @if ($reponse === 'fiche_stock')
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 0, ',', ' ') }}</td>
                        </tr>
                    @endif --}}
                    @if ($reponse === 'ecart_stock')
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_stock, 0, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 0, ',', ' ') }}</td>
                        </tr>
                    @endif
                    @if ($reponse === 'valorisation_ecart')
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Solde</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_ecart, 0, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde_prix, 2, ',', ' ') }}</td>
                            <td style="font-weight: bold; text-align: center; text-align: right">
                                {{ number_format($solde, 2, ',', ' ') }}</td>
                        </tr>
                    @endif

                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>
        @endif
    @endforeach
@endif
@if ($reponse === 'ecart_stock')
    <table>
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold;"> Imprimer le
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
        </tr>
    </table>
@endif
@if ($reponse === 'valorisation_ecart')
    <table>
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold;"> Imprimer le
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
        </tr>
    </table>
@endif

@if ($reponse === 'valorisation_detail_inventaire')
    <table>
        <tr>
            <th colspan="12" style="text-align: right; font-weight: bold;"> Imprimer le
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
        </tr>
        <tr>
            <td colspan="12" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
        </tr>
    </table>
@endif

@if ($reponse === 'fiche_stock' || $reponse === 'fiche_comptage')
    <table>
        <tr>
            <th colspan="5" style="text-align: right; font-weight: bold;"> Imprimer le
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
        </tr>
        <tr>
            <td colspan="5" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
        </tr>
    </table>
@endif

@if ($reponse === 'bilan_inventaire')
    <table>
        <tr>
            <th colspan="10" style="text-align: right; font-weight: bold;"> Imprimer le
                {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
        </tr>
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
        </tr>
    </table>
@endif
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
