@if ($reponse == 'exporter')
<!DOCTYPE html>
<html>
<head>
    <title>Export Vente Cumulée</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #dddddd;
            font-weight: bold;
        }
    </style>
</head>
@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <th colspan="5" style="text-align: center; font-weight: bold; font-size: 20px;">LISTE DES STOCKS EMBALLAGES</th>
    <tbody>
        <tr></tr>

        {{-- <tr>
            <td style="font-weight: bold">Client</td>
            @if($client == 'Tous')
            <td>{{ $client }} les clients</td>
            @endif
            @if($infoClient != null)
            <td>{{ $infoClient['Denomination_sociale'] }}</td>
            @endif
        </tr> --}}


    </tbody>
</table>
    <table>
        <thead >
            <tr style="padding: 8px; border: 1px solid black; ">
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Réference</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Magasin</th>
                <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qte en stock</th>
               {{--  <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Prix Achat</th> --}}

            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td  width="150px" style="text-align: right;">{{ $item->Reference }}</td>
                    <td  width="150px" style="text-align: right;">{{ $item->Designation }}</td>
                    <td  width="150px" style="text-align: right;">{{ $item->Libelle }}</td>
                    <td  width="150px" style="text-align: right;">{{ $item->NomMagasin }}</td>
                    <td  width="150px" style="text-align: right;">{{ $item->Qte_stockee}}</td>
                   {{--  <td  width="150px" style="text-align: right;">{{ $item->Prix_Achat_Net}}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="5" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <td colspan="5" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
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
</body>
</html>


@endif

@if ($reponse == 'formatImportation')
<table>
    <tr>
        <th style="font-weight: bold">reference_emballage</th>
        <th style="font-weight: bold">nom_emballge</th>
        <th style="font-weight: bold">categorie</th>
        <th style="font-weight: bold">magasin</th>
        <th style="font-weight: bold">quantite</th>
        <th style="font-weight: bold">prix_achat</th>


    </tr>
    @foreach($data as $item)
    <tr>
        <td  width="150px">{{ $item->Reference }}</td>
        <td  width="150px">{{ $item->Designation }}</td>
        <td  width="150px">{{ $item->Libelle }}</td>
        <td  width="150px">{{ $item->NomMagasin }}</td>
        <td  width="150px">{{ $item->Qte_stockee}}</td>
        <td  width="150px">{{ $item->Prix_Achat_Net}}</td>

    </tr>
    @endforeach
</table>

@endif

