<!-- Example content for activity tracking -->
<br>
<div class="card">
    <div class="card-header">En temps réel</div>
    <br>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table_activities_histories">
                <thead class="table-primary">
                    <tr>
                        <!-- <th>ID Utilisateur</th> -->
                        <th>Nom</th>
                        <th>Activité</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#table_activities_histories').DataTable({
            "pagingType": "simple_numbers",
            "lengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Tout"]
            ],
            "language": {
                "search": "Rechercher:",
                "lengthMenu": "Afficher _MENU_ entrées",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "paginate": {
                    "first": "Premier",
                    "last": "Dernier",
                    "next": "Suivant",
                    "previous": "Précédent"
                }
            }
        });

        function getActivityTraking() {
        $.ajax({
            url: '/administration/centre-de-contrôle/getActivity',
            method: 'GET',
            success: function(response) {
                console.log(response);
                table.clear();
                $.each(response, function(index, item) {
                    table.row.add([
                        item.name,
                        item.description,
                        item.heure,
                    ]).draw(false);
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
        }
        getActivityTraking();
        getActivityTraking(getOnlineUsers, 30000);
    });
</script>