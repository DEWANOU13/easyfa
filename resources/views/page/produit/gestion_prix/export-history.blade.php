@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size:18px;">LISTE HISTORIQUES PRIX</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Date
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Désignation
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Agence
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Catégorie par client
            </th>
            <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Prix
            </th>
            {{-- <th width="200px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Date de création
            </th> --}}
        </tr>
    </thead>
    <tbody>
        @if ($getGestionPrixHistory)
            @foreach ($getGestionPrixHistory as $getPrixHistory)
                    <tr style="border: 3px solid black; ">
                        <td style="border: 3px solid black;">{{ $getPrixHistory->date_changement_prix}}</td>
                        <td style="border: 3px solid black;">{{ $getPrixHistory->Designation }}</td>
                        <td style="border: 3px solid black;">{{ $getPrixHistory->NomAgence }}</td>
                        <td style="border: 3px solid black;">{{ $getPrixHistory->Libelle }}</td>
                        <td style="border: 3px solid black;">{{ $getPrixHistory->prix }}</td>
                    </tr>
                @endforeach
        @endif

    </tbody>

</table>

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
        <td colspan="1" style="font-weight: bold; background-color: #3232df;font-weight: bold; text-align: center; color: white;">Editer par:</td>
    </tr>
    <tr>
        <td colspan="1" style="font-weight: bold;text-align: center;">EASYFAC</td>
    </tr>
</table>
