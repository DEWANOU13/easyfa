@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="4"
                style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif

<table>
    <tr>
        <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES REGLEMENTS
           DU ({{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y') }}) AU ({{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y')  }})</td>
    </tr>
</table>


@if ($statut === '1')
    @foreach ($reglements as $reglement)
        <table>
            <tr>REFERENCE</tr>
            <tr>
                <td style="font-weight: bold;">Agence</td>
                <td>{{ $reglement->NomAgence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date</td>
                <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Numero&nbsp;Reglement&nbsp;:</td>
                <td>{{ $reglement->Reference_Reglement }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Vendeur</td>
                <td>{{ $reglement->name }}</td>
            </tr>
        </table>

        <table>
            <tr>CLIENT</tr>
            <tr>
                <td style="font-weight: bold;">Code&nbsp;client&nbsp;</td>
                <td>{{ $reglement->Code_client }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Client</td>
                <td>{{ $reglement->Denomination_sociale }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Téléphone&nbsp;:</td>
                <td>{{ $reglement->Telephone_mobile }}</td>
            </tr>

        </table>

        <table>
            <tr>
                <td colspan="1" style="font-weight: bold;">OBSERVATION:</td>
                <td colspan="3">{{ $reglement->Observations }}</td>
            </tr>
        </table>
        <table class="tableLigne">
            <tr>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">N° Facture</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Objet</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Mode règlement</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Montant réglé</th>
            </tr>
            <tbody>
                @php
                    $montantParNiveau = 0;
                    $montantTotal = 0;
                    $montantEnLettres = '';
                @endphp
                @foreach ($detail_reglements as $item)
                    @if ($item->Id_Reglement == $reglement->Id_Reglement)
                        <tr>
                            <td>{{ $item->Reference_facture }}</td>
                            <td>{{ $item->Objet_facture }}</td>
                            <td>{{ $item->Libelle_Operation }}</td>
                            <td>{{ $item->Montant_Regle }}</td>
                        </tr>
                        @php
                            $montantParNiveau += $item->Montant_Regle;
                            // $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                            // $montantEnLettres = ucfirst($formatter->format($montantParNiveau));
                        @endphp
                    @endif
                    @php
                        $montantTotal += $item->Montant_Regle;
                        $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($montantTotal));
                    @endphp
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                    <td style="font-weight: bold;">{{ $montantParNiveau }} </td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endif
@if ($statut === '2')
    @foreach ($reglements as $reglement)
        <table>
            <tr>REFERENCE</tr>
            <tr>
                <td style="font-weight: bold;">Agence</td>
                <td>{{ $reglement->NomAgence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date</td>
                <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Numero&nbsp;Reglement&nbsp;:</td>
                <td>{{ $reglement->Reference_Reglement }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Vendeur</td>
                <td>{{ $reglement->name }}</td>
            </tr>
        </table>

        <table>
            <tr>OPERATION</tr>
            <tr>
                <td style="font-weight: bold;"> Type &nbsp; Operation:</td>
                <td>{{ $reglement->Libelle_Operation }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="1" style="font-weight: bold;">OBSERVATION:</td>
                <td colspan="3">{{ $reglement->Observations }}</td>
            </tr>
        </table>
        <br>
        <table class="tableLigne">
            <tr>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">N° Facture</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Objet</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Client</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Montant réglé</th>
            </tr>
            <tbody>
                @php
                    $montantParNiveau = 0;
                    $montantTotal = 0;
                    $montantEnLettres = ''; // Initialisez la variable en dehors de la boucl
                @endphp
                @foreach ($detail_reglements as $item)
                    @if ($item->Id_Reglement == $reglement->Id_Reglement)
                        <tr>
                            <td>{{ $item->Reference_facture }}</td>
                            <td>{{ $item->Objet_facture }}</td>
                            <td>{{ $item->Denomination_sociale }}</td>
                            <td>{{ number_format($item->Montant_Regle, 0, ',', ' ') }}</td>
                        </tr>
                        @php
                            $montantParNiveau += $item->Montant_Regle;
                        @endphp
                    @endif
                    @php
                        $montantTotal += $item->Montant_Regle;
                        $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($montantTotal));
                    @endphp
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                    <td style="font-weight: bold;">{{ number_format($montantParNiveau, 0, ',', ' ') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endif
@if ($statut === '3')
    @foreach ($reglements as $reglement)
        <table>
            <tr>REFERENCE</tr>
            <tr>
                <td style="font-weight: bold;">Agence</td>
                <td>{{ $reglement->NomAgence }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date</td>
                <td>{{ \Carbon\Carbon::parse($reglement->Date_Reglement)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Numero&nbsp;Reglement&nbsp;:</td>
                <td>{{ $reglement->Reference_Reglement }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Vendeur</td>
                <td>{{ $reglement->name }}</td>
            </tr>
        </table>

        <table>
            <tr>OPERATION</tr>
            <tr>
                <td style="font-weight: bold;"> Type &nbsp; Operation:</td>
                <td>{{ $reglement->Libelle_Operation }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="1" style="font-weight: bold;">OBSERVATION:</td>
                <td colspan="3">{{ $reglement->Observations }}</td>
            </tr>
        </table>

        <table class="tableLigne">
            <tr>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">N° Facture</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Objet</th>
                <th width="300px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Montant réglé</th>
            </tr>
            <tbody>
                @php
                    $montantParNiveau = 0;
                    $montantTotal = 0;
                    $montantEnLettres = ''; // Initialisez la variable en dehors de la boucle
                @endphp
                @foreach ($detail_reglements as $item)
                    @if ($item->Id_Reglement == $reglement->Id_Reglement)
                        <tr>
                            <td>{{ $item->Reference_facture }}</td>
                            <td>{{ $item->Objet_facture }}</td>
                            {{-- <td>{{ $item->Denomination_sociale }}</td> --}}
                            <td>{{ number_format($item->Montant_Regle, 0, ',', ' ') }}</td>
                        </tr>
                        @php
                            $montantParNiveau += $item->Montant_Regle;
                            // $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                            // $montantEnLettres = ucfirst($formatter->format($montantParNiveau));
                        @endphp
                    @endif
                    @php
                        $montantTotal += $item->Montant_Regle;
                        $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($montantTotal));
                    @endphp
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold;">Total Réglé :</td>
                    <td style="font-weight: bold;">{{ number_format($montantParNiveau, 0, ',', ' ') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endif

<table>
    <tr>
        <td colspan="4">Arrêté le present réglement à la somme de @php
            echo $montantEnLettres . ' (' . number_format(intval($montantTotal), 0, ',', ' ') . ') FCFA';
        @endphp</td>
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
