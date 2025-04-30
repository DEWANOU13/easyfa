<table id="droitGroupe" class="datatable table table-striped table-bordered dt-responsive nowrap"
    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>Droit</th>
            <th>Autorisation</th>
        </tr>
    </thead>
    <tbody id="groupeActionDynamiqueTable">
        @forelse ($actions as $action)
            <tr scope="row" for="affectationDroit_{{ $action->id }}" class="clickable-row">
                <td scope="col">{{ $action->nom_action }}</td>
                <td scope="col" class="text-end">
                    <input type="checkbox" {{ isActionAffectedToGroupe($action->id, $groupe_id) }}
                        id="affectationDroit_{{ $action->id }}" class="user-checkbox checkbox2"
                        data-affectationDroit="{{ $action->id }}">
                </td>
            </tr>
        @empty
            <tr>
                <td scope="col" colspan="4" class="text-center">
                    Veuillez sélectionner un groupe et un module et soumettre
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


<script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>
{{-- <script src="{{ asset('dashboard/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script> --}}
{{-- <script src="{{ asset('dashboard/pages/datatables.init.js') }}"></script> --}}
