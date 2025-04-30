<!-- Example content for login history -->
<br>
<div class="card">
    <div class="card-header">En temps réel</div>
    <br>
    <div class="table-responsive">
        <table class="table" id="table_historique_produit">
            <thead class="table-primary">
                <tr>
                    <th>Désignation</th>
                    <th>Date de création</th>
                    <th>Enregister par</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#table_historique_produit').DataTable({
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
                url: '/administration/centre-de-contrôle/getHistoriqueProduit',
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    table.clear();
                    $.each(response, function(index, item) {
                        table.row.add([
                            item.Designation,
                            formatDate(item.created_at),
                            item.name,
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        }
        getLoginHistories();
        setInterval(getLoginHistories, 30000);
    });
</script>
