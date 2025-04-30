<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; width:150px;">reference_produit</th>
                <th style="font-weight: bold; width:150px;">nom_produit</th>
                <th style="font-weight: bold; width:150px;">Catégorie</th>
                <th style="font-weight: bold; width:150px;">magasin</th>
                <th style="font-weight: bold; width:150px;">quantite</th>
                <th style="font-weight: bold; width:150px;">prix_achat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produits as $produit)
            <tr>
                <td>{{ $produit->Reference }}</td>
                <td>{{ $produit->Designation }}</td>
                <td>{{ $produit->Libelle }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
