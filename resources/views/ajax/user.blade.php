@forelse ($users as $user)
    <tr>
        <td>{{ $user->email }}</td>
        <td>{{ $user->name }}</td>
        <td>
            <div class="form-check">
                <input class="form-check-input" disabled
                    style="{{ $user->actif == '1' ? 'background-color: blue;' : '' }}" type="checkbox" value=""
                    {{ $user->actif == '1' ? 'Checked' : '' }}>
            </div>
        </td>
        <td>
            <div class="d-flex gap-2 w-100 justify-content-end">
                <a href="{{ route('user.edit', $user) }}" class="btn btn-primary" title="editer" data-toggle="tooltip">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="15"
                        height="15" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan = "4" class="text-center fw-bold">Aucun utilisateur trouvé</td>
    </tr>
@endforelse
