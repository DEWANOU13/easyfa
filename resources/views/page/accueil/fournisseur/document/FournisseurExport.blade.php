@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="7" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
<table>
    <tr>
        <td colspan="7" style="text-align: center; font-weight: bold; font-size:18px;">LISTE DES FOURNISSEURS </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
               Denomination Sociale
            </th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
               Adresse Fournisseur
            </th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
              Telephone Fixe
            </th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Telephone Mobile
            </th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Adresse mail
            </th>
            <th width="100px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
              Pays
            </th>
            <th width="300px" style="background-color: #3232df;font-weight: bold; text-align: center; color: white;">
                Numéro IFU
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getfournisseurs as $getfournisseur)
        <tr style="border: 3px solid black; ">
            <td style="border: 3px solid black; ">{{ $getfournisseur->DenominationSociale }}</td>
            <td style="border: 3px solid black; text-align: right;" >{{ $getfournisseur->AdresseFournisseur }}</td>
            <td style="border: 3px solid black; ">{{ $getfournisseur->TelephoneFixe }}</td>
            <td style="border: 3px solid black; text-align: right;" >{{ $getfournisseur->TelephoneMobile }}</td>
            <td style="border: 3px solid black; ">{{ $getfournisseur->AdresseMail }}</td>
            <td style="border: 3px solid black; text-align: right;" >{{ $getfournisseur->Pays }}</td>
            <td style="border: 3px solid black; ">{{ $getfournisseur->NumeroIfu }}</td>

        </tr>
        @endforeach
    </tbody>

</table>
<table>
    <tr>
        <td colspan="7" style="font-weight: bold;text-align: right;">Imprimé par {{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td colspan="7" style="font-weight: bold;text-align: right;">{{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</td>
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
