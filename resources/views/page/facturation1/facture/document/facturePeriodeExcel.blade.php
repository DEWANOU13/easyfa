@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="9" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white; ">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>
    <tr>

        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 20px">
            <h1 class="titre">LISTE DES FACTURES</h1>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold">AGENCE</td>
        @if($infoAgence != null)
        <td colspan="5" style="font-weight: bold">{{ $infoAgence['NomAgence']}}</td>
        @else
        <td colspan="5" style="font-weight: bold">Toutes les agences</td>
        @endif

    </tr>
    <tr>
        <td style="font-weight: bold">CLIENT</td>
        @if($infoClient != null)
        <td colspan="5" style="font-weight: bold">{{ $infoClient['Denomination_sociale']}}</td>
        @else
        <td colspan="5" style="font-weight: bold">Tous les clients</td>
        @endif

    </tr>
    <tr>
        <td style="font-weight: bold">DATE</td>
        <td>Du {{$dateDebut}} au {{$dateFin}}</td>
    </tr>
</table>


    <table class="tableLigne">
        <thead>
            <tr>

                <th width="50px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    #</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                        Date</th>
                <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    N°</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Client</th>

                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Aib</th>
                <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    AIB à déduire</th>
                <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Total TVA</th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Montant HT</th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Net à payer</th>
            </tr>
        </thead>
        <tbody>
            @php
                $montantTotal = 0;
            @endphp
            @foreach($tableFactureParPeriodeData as $row)

            @if (in_array($row['count'], [
                'EN COURS','PAYEE','INVALIDEE','ANNULEE','NORMALISEE','EN COURS DE REGLEMENT','SOLDE',

            ]))
            <tr>
                <td colspan="9" style="font-weight: bold; background-color: #9191f7;">{{ $row['count'] }}</td>
            </tr>

            @elseif (in_array($row['count'], [
                'Total',

            ]))
            <tr>
                <td colspan="8" style="font-weight: bold;">{{ $row['count'] }}</td>
                <td style="text-align: right;">{{ $row['Date_facture']  }}</td>
            </tr>
            <tr>
                <td  colspan="9"></td>
            </tr>
            @elseif (in_array($row['count'], [
                'Aucune facture pour ce statut'

            ]))
            <tr>
                <td colspan="9" style="">{{ $row['count'] }}</td>
            </tr>
            <tr>
                <td  colspan="9"></td>
            </tr>
            @elseif (in_array($row['count'], [
                'Total général'

            ]))
            <tr>
                <td colspan="7" style="font-weight: bold;">{{ $row['count'] }}</td>
                <td style="text-align: right;">{{ $row['Date_facture']  }}</td>
                <td style="text-align: right;">{{ $row['Reference_facture']  }}</td>
            </tr>

            @else

            <tr>
                <td>{{ $row['count']  }}</td>
                <td>{{ $row['Date_facture']  }}</td>
                <td>{{$row['Reference_facture']}}</td>
                <td>{{$row['Denomination_sociale']}}</td>

                <td>{{$row['Aib']}}</td>
                <td>{{$row['Aib_deductible']}}</td>
                <td style="text-align: right;">{{$row['TotalGlobalTVA']}}</td>
                <td style="text-align: right;">{{$row['TotalGlobalHT']}}</td>
                <td style="text-align: right;">{{$row['Net_a_payer']}}</td>
            </tr>
            @endif
            @endforeach


        </tbody>
    </table>


<table>
    <tr>
        <td colspan="9" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="9" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: right;font-weight: bold">Edité par EasyFac</td>
    </tr>
</table>

