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
                <td colspan="7"
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
        <table class="table1">
            @if (count($getInventaire) > 0)
                <thead>
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
                        @if ($reponse === 'fiche_stock' || $reponse === 'fiche_comptage')
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Unite Comptage</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Qté</th>
                        @endif
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
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Unite Comptage</th>
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

                        @if ($reponse === 'bilan_inventaire' || $reponse === 'valorisation_detail_inventaire')
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Unite Comptage</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Magasin</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Qte Initiale</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Ecart</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Qte Comptée</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Justificatif</th>
                            <th width="200px"
                                style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Qte Justifiée</th>
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
                @endphp
                @foreach ($getInventaire as $value)
                    @if ($item->Id_Magasin == $value->Id_Magasin)
                        <tr>
                            <td>{{ $value->Reference }}</td>
                            <td>{{ $value->Designation }}</td>
                            <td>{{ $value->Libelle }}</td>
                            @if ($reponse == 'fiche_stock' || $reponse === 'ecart_stock')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td>{{ $value->Qte_stockee }}</td>
                            @endif

                            @if ($reponse === 'fiche_comptage')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td></td>
                            @endif

                            @if ($reponse === 'ecart_stock')
                                <td>{{ $value->Qte_Ecart }}</td>
                                <td></td>
                            @endif
                            @if ($reponse === 'valorisation_ecart')
                                <td style="text-align: right;">{{ $value->Qte_Ecart }}</td>
                                <td style="text-align: right;">{{ $value->Prix_Achat_Net }}</td>
                                <td style="text-align: right;">{{ number_format($value->Prix_Achat_Net * $value->Qte_Ecart, 2, ',', ' ') }}
                                </td>
                            @endif
                            @if ($reponse === 'bilan_inventaire' || $reponse === 'valorisation_detail_inventaire')
                                <td>{{ $value->Libelle_Comptage }}</td>
                                <td>{{ $value->NomMagasin }}</td>
                                <td>{{ $value->Qte_stockee }}</td>
                                <td>{{ $value->Qte_Ecart }}</td>
                                <td>{{ $value->Qte_Comptee }}</td>
                                <td></td>
                                <td>{{ $value->Qte_Ajustee }}</td>
                            @endif

                            @if ($reponse === 'valorisation_detail_inventaire')
                                <td style="text-align: right;">{{ $value->Prix_Achat_Net }}</td>
                                <td style="text-align: right;">{{ number_format($value->Prix_Achat_Net * $value->Qte_Ecart, 2, ',', ' ') }}
                            @endif
                        </tr>
                        @if ($reponse === 'fiche_stock')
                            @php
                                $solde += $value->Qte_stockee;
                            @endphp
                        @endif
                        @if ($reponse === 'ecart_stock')
                            @php
                                $solde += $value->Qte_Ecart;
                            @endphp
                        @endif
                        @if ($reponse === 'valorisation_ecart')
                        @php
                            $solde += $value->Prix_Achat_Net * $value->Qte_Ecart;
                        @endphp
                    @endif
                    @endif
                @endforeach
                @if ($reponse === 'fiche_stock')
                    <tr>
                        <td colspan="4" style="font-weight: bold; text-align: right;">Solde</td>
                        <td  style="font-weight: bold; text-align: center; text-align: right">
                            {{ $solde }}</td>
                    </tr>
                @endif
                @if ($reponse === 'ecart_stock')
                    <tr>
                        <td colspan="5" style="font-weight: bold; text-align: right;">Solde</td>
                        <td  style="font-weight: bold; text-align: center; text-align: right">
                            {{ $solde }}</td>
                    </tr>
                @endif
                @if ($reponse === 'valorisation_ecart')
                <tr>
                    <td colspan="5" style="font-weight: bold; text-align: right;">Solde</td>
                    <td  style="font-weight: bold; text-align: center; text-align: right">
                        {{ $solde }}</td>
                </tr>
            @endif

                <!-- Ajoutez ici plus de lignes avec des données -->
            </tbody>
        </table>
    @endforeach
@endif
<table>
    <tr>
        <th colspan="5" style="text-align: right; font-weight: bold;"> Imprimer le
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</th>
    </tr>
    <tr>
        <td colspan="5" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
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
