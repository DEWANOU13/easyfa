@if($texteEntetePied != null)
<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 15px; background-color: #3232df; text-align: center; color: white;">{{$texteEntetePied->entete}}</td>
    </tr>
</table>
@endif
    <table>
        <tr >
            <td colspan="5">
                LISTE DES ACHEMINEMENTS EMBALLAGE SUR UNE PERIODE
            </td>
        </tr>

    </table>

            <legend class="float-none w-auto px-1" style="font-weight: bold;">REFERENCES</legend>
            <table class="">
                <tbody>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">Date debut&nbsp;:
                        </td>
                        <td colspan="4">{{ \Carbon\Carbon::parse($debut_periode)->format('d/m/Y: H:i') }}
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2"  style="font-weight: bold; font-style: italic; vertical-align: top;">Date fin&nbsp;:
                        </td>
                        <td colspan="4">{{ \Carbon\Carbon::parse($fin_periode)->format('d/m/Y: H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td  colspan="2" style="font-weight: bold; font-style: italic; vertical-align: top;">Agence&nbsp;:
                        </td>
                        @if($infoAgence != null)
                        <td colspan="4">{{$infoAgence['NomAgence']}}</td>
                        @elseif($infoAgence == null)
                        <td colspan="4">Toutes les agences</td>
                        @endif
                    </tr>

                </tbody>
            </table>
    <br>
        <!-- Placer la section <thead> en haut du fichier PDF -->
            <table class="table1">

                <thead>
                    <tr>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Date</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Reference</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; "> Agence</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Agence destination</th>
                        <th  width="200px"
                        style="background-color: #3232df;font-weight: bold; text-align: center; color: white; ">Utilisateur</th>
                    </tr>

                </thead>
                <tbody>
                    @foreach ($getAcheminements as $value)

                            <tr>

                                <td> {{ \Carbon\Carbon::parse($value->Date_acheminement )->format('d/m/Y: H:i') }}</td>
                                <td>{{ $value->Reference_acheminement }}</td>
                                <td>{{ $value->NomAgence }}</td>
                                <td>{{ $value->agence_destination }}</td>
                                <td>{{ $value->name }}</td>
                            </tr>
                    @endforeach
                    <!-- Ajoutez ici plus de lignes avec des données -->
                </tbody>
            </table>

<table>
    <tr>
        <th colspan="4" style="text-align: right; font-weight: bold;"> Imprimer le {{\Carbon\Carbon::now()->format('d/m/Y à H:i:s')}}</th>
    </tr>
    <tr>
        <td  colspan="4" style="text-align: right; font-weight: bold;">{{ Auth::user()->name }}</td>
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
