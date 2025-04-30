<!-- Example content for notifications -->
<div class="card">
    <div class="card-header">Notifications en Temps Réel</div>
    <div class="card-body">
        <ul class="list-group" id="notificationsList">

        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Example of real-time notifications using Pusher or a similar service
        Echo.channel('notifications')
            .listen('NotificationEvent', (e) => {
                const notificationsList = document.getElementById('notificationsList');
                const newNotification = document.createElement('li');
                newNotification.classList.add('list-group-item');
                newNotification.innerHTML = `${e.message} - <small>${e.created_at}</small>`;
                notificationsList.prepend(newNotification);
            });
    });
</script>
