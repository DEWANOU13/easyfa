<!-- Example content for user roles and permissions -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID Utilisateur</th>
                <th>Nom</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<!-- Modal for editing roles -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Modifier Rôle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRoleForm">
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role">

                        </select>
                    </div>
                    <input type="hidden" id="userId" name="user_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="saveRoleButton">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editRoleModal = document.getElementById('editRoleModal');
        editRoleModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-user-id');
            var userRole = button.getAttribute('data-user-role');

            var modalUserId = editRoleModal.querySelector('#userId');
            var modalRoleSelect = editRoleModal.querySelector('#role');

            modalUserId.value = userId;
            modalRoleSelect.value = userRole;
        });

        document.getElementById('saveRoleButton').addEventListener('click', function() {
            var form = document.getElementById('editRoleForm');
            var formData = new FormData(form);

            fetch('', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // Reload to update the list of users and their roles
            });
        });
    });
</script>
