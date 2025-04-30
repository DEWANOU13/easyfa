<br>
<div class="card">
    <div class="card-header">En temps réel</div>
    <br>
    <div class="table-responsive">
        <table class="table" id="table_online_users">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <!-- <th>Heure de Connexion</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Les lignes seront ajoutées dynamiquement -->
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialisation de la DataTable une seule fois
        var table = $('#table_online_users').DataTable({
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

        function getOnlineUsers() {
            $.ajax({
                url: '/administration/centre-de-contrôle/getOnlineUsers',
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    table.clear();
                    $.each(response, function(index, item) {
                        table.row.add([
                            item.id,
                            item.name,
                            item.email,
                            '<button class="btn btn-sm btn-danger btn-disconnect" data-id="' + item.id + '" title="Déconnecter cet utilisateur">' +
                            '<i class="fas fa-sign-out-alt"></i></button>' +
                            ' <button class="btn btn-sm btn-warning btn-force-password" data-id="' + item.id + '" title="Forcer le changement de mot de passe">' +
                            '<i class="fas fa-lock"></i></button>'
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // Fonction pour déconnecter l'utilisateur
        function disconnectUser(userId) {
            $.ajax({
                url: '/administration/centre-de-contrôle/disconnectUser/' + userId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Utilisateur déconnecté avec succès.');
                    getOnlineUsers();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // Fonction pour forcer le changement de mot de passe
        function forcePasswordChange(userId) {
            $.ajax({
                url: '/administration/centre-de-contrôle/forcePasswordChange/' + userId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Changement de mot de passe forcé avec succès.');
                    getOnlineUsers();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // Gestion des clics sur les boutons d'action
        $('#table_online_users').on('click', '.btn-disconnect', function() {
            var userId = $(this).data('id');
            if (confirm('Êtes-vous sûr de vouloir déconnecter cet utilisateur ?')) {
                disconnectUser(userId);
            }
        });

        $('#table_online_users').on('click', '.btn-force-password', function() {
            var userId = $(this).data('id');
            console.log(userId);
            if (confirm('Êtes-vous sûr de vouloir forcer le changement de mot de passe pour cet utilisateur ?')) {
                forcePasswordChange(userId);
            }
        });

        // Appel initial pour récupérer les utilisateurs en ligne
        getOnlineUsers();

        // Mettre à jour la liste des utilisateurs toutes les 5 secondes
        setInterval(getOnlineUsers, 5000);
    });
</script>

<!-- Assurez-vous que les scripts jQuery et DataTables sont inclus -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">