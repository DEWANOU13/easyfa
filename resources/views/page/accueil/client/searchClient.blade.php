@forelse ($listeClient as $client)
    <tr class="clickable-row " data-url="{{ route('editClient', $client->id) }}">
        <td></td>
        <th scope="row">{{ $client->Code_client }}</th>
        <td>{{ $client->Denomination_sociale }}</td>
        <td>{{ $client->CategorieClient }}</td>
        <td>{{ $client->Pays }}</td>
        <td>{{ $client->Numero_ifu }}</td>
        <td>{{ $client->Adresse_client }}</td>
        <td>{{ $client->Telephone_fixe }}</td>
        <td>{{ $client->Telephone_mobile }}</td>
        <td>{{ $client->Adresse_mail }}</td>
        <td>{{ $client->created_at }}</td>
        <td>{{ $client->updated_at }}</td>
        <td>
            @if ($client->Statut_client == 1)
                <div class="form-check d-flex justify-content-end">
                    <div class="bg-primary  rounded">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-check text-white"
                            width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>

                </div>
            @else
                <div class="form-check d-flex justify-content-end">
                    <div class="btn btn-primary bg-light  rounded"></div>
                </div>
            @endif
        </td>

    </tr>

@empty

<tr>
    <td colspan="13" class="text-center">
        <div id="" class="alert alert-info" >
            Aucun résultat trouvé pour la recherche.
        </div>
    </td>
</tr>

@endforelse
