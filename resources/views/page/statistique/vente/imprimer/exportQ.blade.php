@if ($texteEntetePied != null)
    <table>
        <tr>
            <td colspan="6"
                style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                {{ $texteEntetePied->entete }}</td>
        </tr>
    </table>
@endif
@if ($reponse === 'list_cumule_by_client')
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">LISTES DES VENTES CUMULEES
                PAR CLIENT</td>
        </tr>
    </table>
    <table>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Raison sociale du client</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                VenteHT</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">TVA
            </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                VentesTTC</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['raisonSociale'] }}</td>
                    <td>{{ $item['ventesHT'] }}</td>
                    <td>{{ $item['tva'] }}</td>
                    <td>{{ $item['ventesTTC'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'list_cumule_by_day')
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">LISTES DES VENTES CUMULEES
                PAR JOUR</td>
        </tr>
    </table>
    <table>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Période</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                TotalHT</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                TVA</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                TotalTTC</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['periode'] }}</td>
                    <td>{{ $item['totalHT'] }}</td>
                    <td>{{ $item['tva'] }}</td>
                    <td>{{ $item['totalTTC'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'list_cumule_by_category')
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">LISTES DES VENTES PAR
                QUANTITE CUMULEES
                PAR CATEGORIE</td>
        </tr>
    </table>
    <table>
        <tr>
            <td style=" font-weight:bold">Catégorie</td>
            @if ($dataEntete['categorie'] !== 'Toutes')
                <td >{{ $dataEntete['categorie']['Libelle'] }}</td>
                @else
                <td>{{ $dataEntete['categorie'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Agence</td>
            @if ($dataEntete['agence'] !== 'Toutes')
                <td>{{ $dataEntete['agence']['NomAgence'] }}</td>
                @else
                <td>{{ $dataEntete['agence'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Client</td>
            @if ($dataEntete['client'] !== 'Tous')
                <td>{{ $dataEntete['client']['Denomination_sociale'] }}</td>
                @else
                <td>{{ $dataEntete['client'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Date</td>
                <td>{{ $dataEntete['dateDebut']. ' Au ' .$dataEntete['dateFin'] }}</td>
        </tr>
        <tr>
            <td style=" font-weight:bold">Enregistrer par </td>
                <td>{{ Auth::user()->name}}</td>
        </tr>
    </table>
    <table>
        <tr>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Libelle Catégorie
            </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Quantité</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Agence</th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Magasin</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['libelle'] }}</td>
                    <td>{{ $item['client'] }}</td>
                    <td>{{ $item['quantite'] }}</td>
                    <td>{{ $item['agence'] }}</td>
                    <td>{{ $item['magasin'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'list_cumule_by_product')
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">LISTES DES VENTES CUMULEES
                PAR PRODUIT</td>
        </tr>
    </table>
    <table>
        <tr>
            <td style=" font-weight:bold">Produit</td>
            @if ($dataEntete['produit'] !== 'Tous')
                <td >{{ $dataEntete['produit']['Designation'] }}</td>
                @else
                <td>{{ $dataEntete['produit'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Agence</td>
            @if ($dataEntete['agence'] !== 'Toutes')
                <td>{{ $dataEntete['agence']['NomAgence'] }}</td>
                @else
                <td>{{ $dataEntete['agence'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Client</td>
            @if ($dataEntete['client'] !== 'Tous')
                <td>{{ $dataEntete['client']['Denomination_sociale'] }}</td>
                @else
                <td>{{ $dataEntete['client'] }}</td>
            @endif
        </tr>
        <tr>
            <td style=" font-weight:bold">Date</td>
                <td>{{ $dataEntete['dateDebut']. ' Au ' .$dataEntete['dateFin'] }}</td>
        </tr>
        <tr>
            <td style=" font-weight:bold">Enregistrer par </td>
                <td>{{ Auth::user()->name}}</td>
        </tr>
    </table>
    <table>
        <tr>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Code
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Désignation produit
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Client</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Quantite</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Agence</th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">
                Magasin</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['reference'] }}</td>
                    <td>{{ $item['designation'] }}</td>
                    <td>{{ $item['client'] }}</td>
                    <td>{{ $item['quantite'] }}</td>
                    <td>{{ $item['agence'] }}</td>
                    <td>{{ $item['magasin'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'list_cumule_by_agence')
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; font-size:18px;">LISTES DES VENTES CUMULEES
                PAR AGENCE</td>
        </tr>
    </table>
    <table>
        <tr>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Désignation agence
            </th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Quantite</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">VentesTTC</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['libelle'] }}</td>
                    <td>{{ $item['quantite'] }}</td>
                    <td>{{ $item['venteHT'] }}</td>
                    <td>{{ $item['tva'] }}</td>
                    <td>{{ $item['ventesTTC'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'journal_vente')
    <table>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">JOURNAL DES VENTES</td>
        </tr>
    </table>
    <table class="tableLigne">
        <tr>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Date
            </th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference Facture</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">AIB</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">VenteTTC</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['ref_facture'] }}</td>
                    <td>{{ $item['venteHT'] }}</td>
                    <td>{{ $item['tva'] }}</td>
                    <td>{{ $item['aib'] }}</td>
                    <td>{{ $item['ventesTTC'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'list_facture_avoir')
    <table>
        <tr>
            <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES FACTURES AVOIRS
            </td>
        </tr>
    </table>
    <table class="tableLigne">
        <tr>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">Date Facture Avoir
            </th>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">Reference Facture
                Origine</th>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">Reference Facture</th>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">VenteHT</th>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">TVA</th>
            <th style="width:200px; background-color: #3232df;font-weight: bold; color: white; ">VenteTTC</th>
        </tr>
        <tbody>
            @foreach ($dataPrint as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['reference_ancienneFacture'] }}</td>
                    <td>{{ $item['ref_facture'] }}</td>
                    <td>{{ $item['venteHT'] }}</td>
                    <td>{{ $item['tva'] }}</td>
                    <td>{{ $item['ventesTTC'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($reponse === 'sale_by_user')
    <table>
        <tr>
            <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">STATISTIQUE EFFECTUER PAR
                UTILISATEUR</td>
        </tr>
    </table>
    <table class="tableLigne">
        <tr>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Date Debut
            </th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Date Fin</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Reference</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Client</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Vente</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Avoir</th>
            <th style="background-color: #3232df;font-weight: bold; color: white; ">Agence</th>
        </tr>
        <tbody>
            @foreach ($data['dataPrint'] as $item)
                <tr>
                    <td>{{ $item['date_debut'] }}</td>
                    <td>{{ $item['date_fin'] }}</td>
                    <td>{{ $item['reference'] }}</td>
                    <td>{{ $item['client'] }}</td>
                    <td>{{ $item['vente'] }}</td>
                    <td>{{ $item['avoir'] }}</td>
                    <td>{{ $item['agence'] }}</td>
                    {{-- <td>{{ $item['ventesTTC'] }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<table>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold;text-align: right;">
            {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</td>
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
