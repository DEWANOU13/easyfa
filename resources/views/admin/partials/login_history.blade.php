<!-- Example content for login history -->
<br>
<div class="card">
    <div class="card-header">En temps réel</div>
    <br>
    <div class="table-responsive">
        <table class="table" id="table_login_histories">
            <thead class="table-primary">
                <tr>
                    <th>ID Utilisateur</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Heure de Connexion</th>
                    <th>Heure de Déconnexion</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#table_login_histories').DataTable({
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

        function getLoginHistories() {
        $.ajax({
            url: '/administration/centre-de-contrôle/getLoginHistory',
            method: 'GET',
            success: function(response) {
                console.log(response);
                table.clear();
                $.each(response, function(index, item) {
                    table.row.add([
                        item.user_id,
                        item.name,
                        item.email,
                        item.login_at,
                        item.logout_at,
                    ]).draw(false);
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
        }
        getLoginHistories();
        setInterval(getLoginHistories, 30000);
    });
</script>