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
            <h1 class="titre">RELEVE DES SORTIES </h1>
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
        @if($infoCategorie != null)
        <td style="font-weight: bold">{{ $infoCategorie['Libelle']}}</td>
        @else
        <td style="font-weight: bold">Tous les catégories produit</td>
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


            <th width="150px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Reference</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Designation</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Lundi</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Mardi</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Mercredi</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Jeudi</th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Vendredi</th>
                <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                    Samedi</th>
                    <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                        Dimanche</th>
                        <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                            Sortie Moy. Jour</th>
                            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                                Sortie Moy. Sem</th>
        </tr>
    </thead>
    <tbody>


        @foreach($tableReleverSortieData as $item)


                <tr>
                    <td>{{$item['Reference']}}</td>
                    <td  class="bold">{{ $item['Designation'] }}</td>
                    <td style="text-align: right">{{ $item['Lundi'] }}</td>
                    <td style="text-align: right">{{ $item['Mardi'] }}</td>
                    <td style="text-align: right">{{ $item['Mercredi'] }}</td>
                    <td style="text-align: right">{{ $item['Jeudi'] }}</td>
                    <td style="text-align: right">{{ $item['Vendredi'] }}</td>
                    <td style="text-align: right">{{ $item['Samedi'] }}</td>
                    <td style="text-align: right">{{ $item['Dimanche'] }}</td>
                    <td style="text-align: right">{{ $item['SortieMoyenneParJour'] }}</td>
                    <td style="text-align: right">{{ $item['SortieHebdo'] }}</td>
                </tr>


        @endforeach
    </tbody>
</table>


<table>
    <tr>
        <td colspan="11" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="11" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
    </tr>
    <tr>
        <td colspan="11" style="font-weight: bold;text-align: right;">Edité par Easyfac</td>
    </tr>
</table>
