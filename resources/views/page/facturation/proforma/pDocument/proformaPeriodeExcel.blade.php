@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="8" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;  font-size: 15px">{{$texteEntetePied->entete}}</td>
    </tr>
</table>

@endif
<table>

    <tr>

        <td colspan="8" style="text-align: center; font-weight: bold; font-size: 20px">
            <h1 class="titre">LISTE DES PROFORMAS SUR UNE PERIODE </h1>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold">AGENCE</td>
        @if($infoAgence != null)
        <td style="font-weight: bold">{{ $infoAgence['NomAgence']}}</td>
        @else
        <td style="font-weight: bold">Toutes les agences</td>
        @endif

    </tr>
    <tr>
        <td style="font-weight: bold">CLIENT</td>
        @if($infoClient != null)
        <td style="font-weight: bold">{{ $infoClient['Denomination_sociale']}}</td>
        @else
        <td style="font-weight: bold">Tous les clients</td>
        @endif

    </tr>
    <tr>
        <td style="font-weight: bold">Date</td>
        <td>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</td>
    </tr>
</table>
 <table>
    <thead>
        <tr>

            <th width="50px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                #</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    N°</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Validité</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Aib</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                AIB à déduire</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Net à payer</th>
        </tr>
    </thead>
    <tbody>
        @php
        $nombreLignes = count($tableFactureParPeriodeData);
        @endphp

        @foreach($tableFactureParPeriodeData as $proforma)
        @if (in_array($proforma['count'], [
            'Total général'

        ]))
        <tr>
            <td colspan="6" style="font-weight: bold;">{{ $proforma['count'] }}</td>
            <td style="text-align: right;font-weight: bold;">{{ $proforma['Date_facture']  }}</td>
            <td style="text-align: right;font-weight: bold;">{{ $proforma['Reference_facture']  }}</td>
        </tr>

        @else

                <tr>
                    <td>{{$proforma['count']}}</td>
                    <td  class="bold">{{ $proforma['Reference_facture'] }}</td>
                    <td >{{ $proforma['Date_facture'] }}</td>
                    <td >{{ $proforma['Denomination_sociale'] }}</td>
                    <td >{{ $proforma['Validite'] }}</td>
                    <td >{{ $proforma['Aib'] }}</td>
                    <td >{{ $proforma['Aib_deductible'] }}</td>
                    <td style="text-align: right">{{ $proforma['Net_a_payer'] }}</td>
                </tr>
            @endif

        @endforeach
    </tbody>
</table>


<table>
    <tr>
        <td colspan="8" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold;text-align: right;">Edité par Easyfac</td>
    </tr>
</table>
