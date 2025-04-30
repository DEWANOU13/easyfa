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
            <h1 class="titre">SATATISTIQUES DETAILLE  DES FACTURES</h1>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold">AGENCE</td>
        @if($infoAgence != null)
        <td  style="font-weight: bold">{{ $infoAgence['NomAgence'] }}</td>
        @else
        <td  style="font-weight: bold">Toutes les agences</td>
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
        <td style="font-weight: bold">TAXE</td>
        @if($taxe != 'Tous')
        <td colspan="5" style="font-weight: bold">{{ $taxe}}</td>
        @else
        <td colspan="5" style="font-weight: bold">Toutes les taxes</td>
        @endif
    </tr>
    <tr>
        <td style="font-weight: bold">DATE</td>
        <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>

            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Taxe</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">N°
                facture</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Objet</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Vente (FV)</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Avoir (FA)</th>
        </tr>
    </thead>
     <tbody>
        @foreach($tableStatDetailleData as $row)
            @if(in_array($row['taxe'], ['Total TVA Taxable 18%', 'Total TVA régime d\'exception 18%', 'Total AIb facturé', 'Total AIb deductible']))
                <tr>
                    <td colspan="5" class="bold">{{ $row['taxe'] }}</td>
                    <td style="text-align: right;">{{ $row['client'] }}</td>
                    <td style="text-align: right;">{{ $row['date'] }}</td>
                    <td >{{ $row['nfacture'] }}</td>
                    <td >{{ $row['objet'] }}</td>
                    <td >{{ number_format($row['FV'])  }}</td>
                    <td >{{ number_format($row['FA']) }}</td>
                </tr>
                @elseif(in_array($row['taxe'], [
                    'TVA Taxable 18%',
                    'TVA régime d\'exception 18%',
                    'AIb facturé',
                    'AIb deductible',
                ]))
                 <tr>
                    <td colspan="7" style="font-weight: bold;">{{ $row['taxe'] }}</td>

                </tr>
                @else
                 <tr>
                    <td  class="bold">{{ $row['taxe'] }}</td>
                    <td >{{ $row['client'] }}</td>
                    <td >{{ $row['date'] }}</td>
                    <td >{{ $row['nfacture'] }}</td>
                    <td >{{ $row['objet'] }}</td>
                    <td style="text-align: right;" >{{ $row['FV']}}</td>
                    <td style="text-align: right;">{{ $row['FA']}}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="7" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="7" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
    </tr>
    <tr>
        <td colspan="7" style="text-align: right;font-weight: bold">Edité par EasyFac</td>
    </tr>
</table>
