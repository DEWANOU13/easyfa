@forelse ($actions1 as $action)
    <tr scope="row" for="affectationDroit_{{ $action->id }}" class="clickable-row">
        <td scope="col">{{ $action->nom_action }}</td>
        <td scope="col" class="text-end">
            <input type="checkbox"
                {{ isActionAffectedToUser($action->id, $user_id, $agence_id) }}
                id="affectationDroit_{{ $action->id }}" class="user-checkbox checkbox3"
                data-affectationDroitUser="{{ $action->id }}">
        </td>
    </tr>
@empty
    <tr>
        <td scope="col" colspan="4" class="text-center">
            Veuillez sélectionner un utilisateur et un module et soumettre
        </td>
    </tr>
@endforelse
