@extends('layouts.master', ['title' => 'Notifications'])
@section('content')
@include('layouts.partials.entete-page', [
'infos1' => 'Mes messages',
'infos2' => 'Mon espace',
'infos3' => 'Message',
])

<div class="container">
    <h2>Notifications</h2>
    <ul class="list-group">
        @foreach ($notifications as $notification)
            <li class="list-group-item">
                {{ $notification->data['message'] }} - <small>{{ $notification->created_at }}</small>
                @if ($notification->read_at)
                    <span class="badge badge-success">Lu</span>
                @else
                    <a href="#" class="btn btn-sm btn-primary mark-as-read" data-notification-id="{{ $notification->id }}">Marquer comme lu</a>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $notifications->links() }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-notification-id');

                fetch('{{ route('admin.notifications.markAsRead') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ notification_id: notificationId })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    this.closest('li').remove();
                });
            });
        });
    });
</script>









@include('layouts.alert')
@endSection