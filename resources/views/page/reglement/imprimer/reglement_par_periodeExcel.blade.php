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
            <h1 class="titre">LISTE DES REGLEMENTS SUR UNE PERIODE </h1>
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
    <tr>
        <td style="font-weight: bold; font-style: italic; vertical-align: top;">
            Enregistrer par &nbsp;:
        </td>
        <td>{{ Auth::user()->name }}</td>
    </tr>
</table>
 <table>
    <thead>
        <tr>

            <th width="50px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                #</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Date</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Statut opération</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Agence</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Montant réglé</th>
        </tr>
    </thead>
    <tbody>
        @php
        $nombreLignes = count($tableReglementParPeriodeData);
        @endphp

        @foreach($tableReglementParPeriodeData as $proforma)
        @if (in_array($proforma['count'], [
            'Total général'

        ]))
        <tr>
            <td colspan="6" style="font-weight: bold;">{{ $proforma['count'] }}</td>
            <td style="text-align: right;font-weight: bold;">{{ $proforma['Date_Reglement']  }}</td>
        </tr>

        @else

                <tr>
                    <td>{{$proforma['count']}}</td>
                    <td  class="bold">{{ $proforma['Date_Reglement'] }}</td>
                    <td >{{ $proforma['Reference_Reglement'] }}</td>
                    <td >{{ $proforma['Denomination_sociale'] }}</td>
                    <td >{{ $proforma['Statut_Operation'] }}</td>
                    <td >{{ $proforma['NomAgence'] }}</td>
                    <td style="text-align: right">{{ $proforma['Montant_Regle'] }}</td>
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
