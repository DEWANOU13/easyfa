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
        margin-top: 100px;
        margin-bottom: 50px; /* Marge supérieure pour commencer après l'en-tête */
    }

    .titre {
        font-weight: bold;
        font-size: 20px;
        text-align: center;
    }

    /* CSS pour l'en-tête et le pied de page */
    @page {

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
        left: -70px; /* Positionné à gauche */
        transform: translateY(-50%) rotate(-90deg); /* Rotation du texte */
        font-size: 8px; /* Ajustez la taille du texte selon vos besoins */
        color: rgb(0, 0, 0); /* Ajustez la couleur et l'opacité du filigrane */
        z-index: -1;
        pointer-events: none; /* Empêche les interactions avec le filigrane */
    }

    .header {
        top: 0;
        height: 100px;
        z-index: 1000; /* Assurez-vous que l'en-tête apparaît au-dessus du contenu */
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
        width: 100%;

        border: 1px solid #ddd;
        margin: auto; /* Centrer le tableau horizontalement */
        margin-top: 20px; /* Marge supérieure pour l'espace entre l'en-tête et le tableau */
    }

    /* Style des cellules du tableau */
    .tableLigne th,
    .tableLigne td {
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
        <h4>LISTE DES SORTIES DE PRODUITS ENTRE ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS DU MAGASIN {{ $magasin->NomMagasin }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS {{ $produit->Reference }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit !== 'Tous' && $categorie === 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS DU MAGASINS {{ $magasin->NomMagasin }} DU PRODUIT
          {{ $produit->Reference }}
          ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin === 'Tous' && $produit !== 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS DU PRODUIT {{ $produit->Reference }}
          POUR LA CATEGORIE {{ $categorie->Libelle }} ENTRE LE
          ({{ $debut_periode }}) ET ({{ $fin_periode }})</h4>
        @endif
        @if ($magasin !== 'Tous' && $produit === 'Tous' && $categorie !== 'Toutes')
        <h4>LISTE DES SORTIES DE PRODUITS DU MAGASINS {{ $magasin->NomMagasin }}
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
    $sortie_produit->Id_Sortie_Produit === $getMagasin_sortir->Id_Sortie_Produit)
    <div class="sous-titre">
      <span class="span-titre">Date: </span><em>{{ $sortie_produit->Date_Sortie }}</em>
      <span class="span-titre">Reference: </span><em>{{ $sortie_produit->Reference_Sortie }}</em>
      <span class="span-titre">Enregistrer par: </span><em>{{ $sortie_produit->name }}</em>
    </div>
    @endif
    <div class="table">
      <table class="tableLigne">
        @if ($getMagasin_sortir->Id_Magasin === $sortie_produit->Id_Magasin)
        @if (count($getSortie) > 0)
        <thead>
          <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
          <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Désignation</th>
          <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Catégorie</th>
          <th style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Qté_Sortie</th>
        </thead>
        @endif
        @endif
        <tbody>
          @foreach ($getSortie as $value)
          @if (
          $value->Id_Sortie_Produit === $sortie_produit->Id_Sortie_Produit &&
          $value->Id_Sortie_Produit === $getMagasin_sortir->Id_Sortie_Produit)
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
    <div align="right" style= "font-weight: bold; margin-right: 80px"></div>
    <div align="right" style= "font-weight: bold;  margin-top: 60px; font-style: italic">Imprimer le {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}
    </div>
    <div align="right" style= "  ">{{ Auth::user()->name }}</div>

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
