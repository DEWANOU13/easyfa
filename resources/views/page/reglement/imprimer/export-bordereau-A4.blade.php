@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">BORDEREAU DU REGLEMENT</td>
        </tr>
    </table>


    @foreach ($reglement as $item)
        <table>
            <tr>
                <td style="font-weight: bold;">Agence</td>
                <td>{{ $item->NomAgence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date</td>
                <td>{{ \Carbon\Carbon::parse($item->Date_Reglement)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Numero&nbsp;Reglement&nbsp;:</td>
                <td>{{ $item->Reference_Reglement }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Vendeur</td>
                <td>{{ $item->name }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="font-weight: bold;">Code&nbsp;client&nbsp;:</td>
                <td>{{ $item->Code_client }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Client&nbsp;:</td>
                <td>{{ $item->Denomination_sociale }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Téléphone&nbsp;:</td>
                <td>{{ $item->Telephone_mobile }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="1" style="font-weight: bold;">OBJET:</td>
                <td colspan="3">{{ $item->Objet_facture }}</td>
            </tr>
        </table>
        @foreach ($reglement as $item)
            <table>
                <tr>
                    <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">N°
                        Facture</th>
                    <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Objet
                    </th>
                    <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Mode
                        règlement</th>
                    <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Montant
                        réglé</th>
                </tr>
                <tbody>
                    @php
                        $montantParNiveau = 0;
                        $montantTotal = 0;
                        $montantEnLettres = '';
                        $montantRestantDu = 0;
                    @endphp

                    @foreach ($detail_reglements as $detail_reglement)
                        @if ($item->id === $detail_reglement->Id_Reglement)
                            <tr>
                                <td>{{ $detail_reglement->Reference_facture }}</td>
                                <td>{{ $detail_reglement->Objet_facture }}</td>
                                <td>{{ $detail_reglement->Libelle_Operation }}</td>
                                <td>{{ $detail_reglement->Montant_Regle }}</td>
                            </tr>
                        @endif
                        @php
                            $montantParNiveau += $detail_reglement->Montant_Regle;
                            $montantTotal += $detail_reglement->Montant_Regle;
                            $montantRestantDu = $detail_reglement->Net_a_payer - $detail_reglement->Montant_Regle;
                        @endphp
                    @endforeach
                    @php
                        $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($montantTotal));
                    @endphp
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                        <td style="font-weight: bold;">{{ number_format($montantParNiveau, 0, ',', ' ') }}
                        </td>
                    </tr>

                </tbody>
            </table>

            <table>
                <tr>
                    <td colspan="4">
                        Montant total restant dû: ({{ number_format($montantRestantDu, 0, ',', ' ') }}) francs CFA
                    </td>
                </tr>
            </table>
        @endforeach
    @endforeach

    <table>
        <tr>
            <td colspan="4">
                Arrêté le présent règlement à la somme de : {{ $montantEnLettres }}
                ({{ number_format($montantTotal, 0, ',', ' ') }})
                francs CFA
            </td>
        </tr>
    </table>

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
