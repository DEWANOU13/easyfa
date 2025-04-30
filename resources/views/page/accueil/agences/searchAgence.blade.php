@php
    $counter = 1;
@endphp

@forelse ($listeAgences as $agence)
    <tr class="clickable-row " data-url="{{ route('editAgence', $agence->id) }}">
        <th scope="row">{{ $counter }}</th>
        <td class="agence-nom">{{ $agence->NomAgence }}</td>
        <td class="text-end">
            @if ($agence->EnActivite == 1)
                <div class="form-check d-flex justify-content-end">
                    <div class="bg-primary  rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check text-white"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
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
    @php
        $counter++;
    @endphp


@empty

    <tr>
        <td colspan="13" class="text-center">
            <div id="" class="alert alert-info">
                Aucun résultat trouvé pour la recherche.
            </div>
        </td>
    </tr>
@endforelse
