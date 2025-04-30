@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="2" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES CATEGORIES PRODUITS</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Catégorie
            </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Date création
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getCategorieProduits as $getCategorieProduit)
        <tr style="border: 3px solid black; ">
            <td style="border: 3px solid black; ">{{ $getCategorieProduit->Libelle }}</td>
            <td style="border: 3px solid black; text-align: right;" >{{ \Carbon\Carbon::parse( $getCategorieProduit->created_at)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>

</table>

<table>
    <tr>
        <td colspan="2" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;text-align: right;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
    </tr>
</table>
<table>
    <tr>
        <td colspan="1" style="font-weight: bold; background-color: #3232df;font-weight: bold; text-align: center; color: white;">Editer par:</td>
    </tr>
    <tr>
        <td colspan="1" style="font-weight: bold;text-align: center;">EASYFAC</td>
    </tr>
</table>
