@if (session('success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: "{{ session('success') }}"
        });
    </script>
@elseif(session('error'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "error",
            title: "{{ session('error') }}"
        });
    </script>
@elseif(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: "warning",
                title: "Attention",
                text: "{!! html_entity_decode(session('warning')) !!}",
                //footer: '<a href="#">Why do I have this issue?</a>'
            });
        });
    </script>
@endif

@if (session('info'))
    <script>
        Swal.fire({
            title: "Attention",
            text: "{{ session('info')['message'] }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Oui, continuer",
            cancelButtonText: "Non, annuler"
        }).then((result) => {
            if (result.isConfirmed) {
                // Rediriger vers une route qui gère la modification
                window.location.href =
                    "{{ route('convertirEnAvoir', ['factureId' => session('info')['factureId']]) }}";
            } else {
                Swal.fire({
                    title: "Action annulée",
                    text: "La conversion a été annulée.",
                    icon: "error"
                });
            }
        });
    </script>
@endif
