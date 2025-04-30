<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 60%;
            margin-top: 150px; /* Espace entre le haut de la page et le début du contenu */
            margin-bottom: 100px; /* Espace entre le bas de la page et le pied de page */
        }

    @page {
      margin-top: 0px;
      */
      /* Espace entre le haut de la page et le début du contenu */
      margin-bottom: 50px;
      /* Espace entre le bas de la page et le pied de page */
    }

    .header,
    .footer {
      position: fixed;
      width: 100%;
      text-align: center;
    }


    .table-container {
      margin: 20px;
      border: 1px solid #ccc;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    th,
    td {
      padding: 0px;
      text-align: left;
      border-bottom: 1px solid #ccc;
      font-size: 10px
    }

    th {
      background-color: #f2f2f2;
      padding: 3px;
    }

    /* Ajout de la bordure pour chaque td */
    td {
      border-right: 1px solid #ccc;
    }

    .titre {
      text-align: center;
    }

    .entete {
      font-size: 12px;
    }

    .sous-titre {
      font-size: 10px;
      font-weight: 900;
      font-style: italic;
      margin-bottom: 10px;

    }

    .montant-regle {
      font-size: 9px;
    }

    .total-row {
      font-weight: bold;
    }

    body::before {
      content: "EDITE PAR EASYFAC";
      position: fixed;
      top: 80%;
      left: -70px;
      /* Positionné à gauche */
      transform: translateY(-50%) rotate(-90deg);
      /* Rotation du texte */
      font-size: 8px;
      /* Ajustez la taille du texte selon vos besoins */
      color: rgb(0, 0, 0);
      /* Ajustez la couleur et l'opacité du filigrane */
      z-index: -1;
      pointer-events: none;
      /* Empêche les interactions avec le filigrane */
    }

    .box0 {
      width: 60%;
      border: 1px solid #a7a5a5;
      background-color: #ffffff;
      display: inline-block;
      /* Divs sur la même ligne */
      vertical-align: top;
      /* Alignement vertical vers le haut */
      font-size: 12px;
    }

    .box0p {
      float: right;
      width: 40%;
      background-color: #ffffff;
      display: inline-block;
      /* Divs sur la même ligne */
      vertical-align: top;
      /* Alignement vertical vers le haut */
      border: 1px solid #a7a5a5;
      font-size: 12px;
    }

    .r {
      margin: 5px;
    }

    span,
    strong {
      padding: 15px;
      padding-bottom: 5px;
    }

    span {}

    .t {
      margin: 5px;
    }

    .boite {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 0 50px;
      margin-top: 15px;
    }

    .entente-bordereau {
      padding: 10px;
      
      border-radius: 20px;
      box-shadow: 30 30 10 0px;
      margin-top: 50px;

      font-weight: 600;
      font-size: 14px;
    }

    .date-bordereau {
      margin-top: 50px;
      font-size: 12px;
    }

    .boite-niveau-un {
      border: 1px solid #CCC;
      border-radius: 10px;
      margin-bottom: 10px;
      max-width: 240px;
    }

    .boite-niveau-deux {
      border: 1px solid #CCC;
      border-radius: 10px;
      margin-bottom: 10px;
      max-width: 240px;
    }

    .date-bordereau {
      margin-top: 10px;
    }

    .entente-bordereau {
      font-weight: 600;
      margin-top: -20px;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 80%;
      margin-top: 150px;
      /* Marge supérieure pour commencer après l'en-tête */
      margin-bottom: 50px;
      /* Marge supérieure pour commencer après l'en-tête */
    }

    .titre {
      font-weight: bold;
      font-size: 25px;
      text-align: center;
    }

    /* CSS pour l'en-tête et le pied de page */
    @page {
      margin-top: 100px;
      /* Espace entre le haut de la page et le début de l'en-tête */
      margin-bottom: 50px;
      /* Espace entre le bas de la page et le pied de page */
    }

    .box0 {
      width: 60%;
      border: 1px solid #a7a5a5;
      background-color: #ffffff;
      display: inline-block;
      /* Divs sur la même ligne */
      vertical-align: top;
      /* Alignement vertical vers le haut */
      font-size: 12px;
    }

    .box0p {
      float: right;
      width: 40%;
      background-color: #ffffff;
      display: inline-block;
      /* Divs sur la même ligne */
      vertical-align: top;
      /* Alignement vertical vers le haut */
      border: 1px solid #a7a5a5;
      font-size: 12px;
    }

    .header,
    .footer {
      position: fixed;
      width: 100%;
      text-align: center;
    }

    body::before {
      content: "EDITE PAR EASYFAC";
      position: fixed;
      top: 80%;
      left: -70px;
      /* Positionné à gauche */
      transform: translateY(-50%) rotate(-90deg);
      /* Rotation du texte */
      font-size: 8px;
      /* Ajustez la taille du texte selon vos besoins */
      color: rgb(0, 0, 0);
      /* Ajustez la couleur et l'opacité du filigrane */
      z-index: -1;
      pointer-events: none;
      /* Empêche les interactions avec le filigrane */
    }

    .header {
      top: 0;
      height: 100px;
      z-index: 1000;
      /* Assurez-vous que l'en-tête apparaît au-dessus du contenu */
    }

    .footer {
      bottom: 0;
      height: 50px;
    }

    .table-container2 {
      width: 50px;
    }

    /* Style du tableau */
    .tableLigne {
      border-collapse: collapse;
      width: 1000px;
      border: 1px solid #ddd;
      margin: auto;
      /* Centrer le tableau horizontalement */
      margin-top: 20px;
      /* Marge supérieure pour l'espace entre l'en-tête et le tableau */
    }

    /* Style des cellules du tableau */
    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    /* Style de l'entête du tableau */
    .tableLigne th {
      background-color: #f2f2f2;
      color: #333;
    }

    .tableInfodgi {
      border-collapse: collapse;
      width: 100%;
    }

    .tableInfodgi th,
    .tableInfodgi td {
      padding: 5px;
      text-align: left;
    }

    .container {}

    .box1 {
      width: 60%;
      background-color: #ffffff;
      margin: 10px;
      display: inline-block;
      vertical-align: top;
    }

    .box2 {
      width: 30%;
      background-color: #ffffff;
      margin: 10px;
      display: inline-block;
      vertical-align: top;
    }

    .page-number:after {
      content: counter(page);
    }

    .total-pages:after {
      content: counter(pages);
    }

    .section {
      margin-top: -20px;
    }

    .entete {
      margin-top: -25px;
    }

    .span-titre {
      font-size: 14px;
    }

  </style>
</head>

<body>
  <div class="header">
    @if ($imageEntetePied != null)
    <img src="{{ $imageEntetePied->entete }}" style="width: 100%">
    @endif
    {{-- <img src="{{ public_path('imgPdf/entete.png') }}" style="width: 100%; margin-top: -50px; margin-bottom: 50px"> --}}
  </div>
  <section class="section">
    <div style="display: flex; justify-content: center; align-items:center ">
      <div style="text-align: center; margin-bottom: 15px;" class="entente-bordereau">
        @if ($magasin === 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE DU MAGASIN {{ $magasin->NomMagasin }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE {{ $produit->Reference }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE DU MAGASINS {{ $magasin->NomMagasin }} DU PRODUIT
          {{ $produit->Reference }}
          ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE DU PRODUIT {{ $produit->Reference }}
          POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES D'EMBALLAGE DU MAGASINS {{ $magasin->NomMagasin }}
          POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
      </div>
    </div>
    @foreach ($getMagasin as $getMagasin_sortir)
    <div class="entete">
      <h3>{{ $getMagasin_sortir->NomMagasin }}</h3>
    </div>
    @foreach ($getSortieProduit as $sortie_produit)
    @if (
    $getMagasin_sortir->Id_Magasin === $sortie_produit->Id_Magasin &&
    $sortie_produit->Id_Sortie_Emballage === $getMagasin_sortir->Id_Sortie_Emballage)
    <div class="sous-titre">
      <span class="span-titre">Date: </span><em>{{ $sortie_produit->Date_Sortie }}</em>
      <span class="span-titre">Reference: </span><em>{{ $sortie_produit->Reference_Sortie }}</em>
      <span class="span-titre">Enregistrer par: </span><em>{{ $sortie_produit->name }}</em>
    </div>
    @endif
    <div class="table">
      <table>
        @if ($getMagasin_sortir->Id_Magasin === $sortie_produit->Id_Magasin)
        @if (count($getSortie) > 0)
        <thead>
          <th>Reference</th>
          <th>Désignation</th>
          <th>Catégorie</th>
          <th>Qté_Sortie</th>
        </thead>
        @endif
        @endif
        <tbody>
          @foreach ($getSortie as $value)
          @if (
          $value->Id_Sortie_Emballage === $sortie_produit->Id_Sortie_Emballage &&
          $value->Id_Sortie_Emballage === $getMagasin_sortir->Id_Sortie_Emballage)
          <tr>
            <td>{{ $value->Reference }}</td>
            <td>{{ $value->Designation }}</td>
            <td>{{ $value->Libelle }}</td>
            <td>{{ $value->Qte_Sortie }}</td>
          </tr>
          @endif
          @endforeach
          <!-- Ajoutez ici plus de lignes avec des données -->
        </tbody>
      </table>
    </div>
    @endforeach
    @endforeach
    {{-- @endforeach --}}
    {{-- @endif --}}

  </section>

  <div class="footer">
    <footer>
      Page <span class="page-number"></span>
    </footer>
    @if ($imageEntetePied != null)
    <img src="{{ $imageEntetePied->pied }}" style="width: 100%">
    @endif
    {{-- <img src="{{ public_path('imgPdf/pied.png') }}" style="width: 100%"> --}}
  </div>

  <script type="text/php">
    if (isset($pdf)) {
            $pdf->page_script('
                if ($PAGE_COUNT > 1) {
                    $font = $fontMetrics->get_font("DejaVu Sans, sans-serif", "normal");
                    $size = 12;
                    $pageText = "Page " . $PAGE_NUM . " / " . $PAGE_COUNT;
                    $y = 820;
                    $x = 520;
                    $pdf->text($x, $y, $pageText, $font, $size);
                }
            ');
        }
    </script>
</body>

</html>
