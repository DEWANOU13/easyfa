@if ($reponse == 'exporter')
    @if ($texteEntetePied != null)
        <table>
            <tr>
                <td colspan="6"
                    style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">
                    {{ $texteEntetePied->entete }}</td>
            </tr>
        </table>
    @endif
    <table>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES PRIX</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Réference
                </th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Désignation
                </th>
                <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Catégorie Produit
                </th>
                <th width="200px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Agence
                </th>
                <th width="200px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Catégorie par client
                </th>
                <th width="200px"
                    style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                    Montant HT
                </th>
                {{-- <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Date de création
            </th> --}}
            </tr>
        </thead>
        <tbody>
            @if ($getGestionPrix)
                @foreach ($getGestionPrix as $getPrix)
                    <tr style="border: 3px solid black; ">
                        <td style="border: 3px solid black;">{{ $getPrix->Reference }}</td>
                        <td style="border: 3px solid black;">{{ $getPrix->Designation }}</td>
                        <td style="border: 3px solid black;">{{ $getPrix->Libelle_produit }}</td>
                        <td style="border: 3px solid black;">{{ $getPrix->NomAgence }}</td>
                        <td style="border: 3px solid black;">{{ $getPrix->Libelle_client }}</td>
                        <td style="border: 3px solid black;">{{ $getPrix->prix }}</td>
                    </tr>
                @endforeach
            @endif

        </tbody>

    </table>

    <table>
        <tr>
            <td colspan="6" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold;text-align: right;">
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

@endif

@if ($reponse == 'formatImportation')

    <table>
        <tr>
            <th style="font-weight: bold">Produit</th>
            <th style="font-weight: bold">Catégorie produit</th>
            <th style="font-weight: bold">Catégorie client</th>
            <th style="font-weight: bold">Agence</th>
            <th style="font-weight: bold">Prix</th>


        </tr>
        @foreach ($getGestionPrix as $item)
            <tr>
                <td width="250px">{{ $item->Designation }}</td>
                <td width="250px">{{ $item->Libelle_produit }}</td>
                <td width="200px">{{ $item->Libelle_client }}</td>
                <td width="250px">{{ $item->NomAgence }}</td>
                <td width="200px">{{ $item->prix }}</td>

            </tr>
        @endforeach
    </table>

@endif
