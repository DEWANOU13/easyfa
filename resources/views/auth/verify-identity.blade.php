@php
    inMaintenance();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('images/logo_easyfac.png') }}">
    <title>EASYFAC - Vérification d'identité</title>
    <script src="{{ asset('jquery.min.js') }}"></script>
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
</head>

<body style="background-color: #007bffa1 !important">
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; background-color: #007bffa1">
        <div class="col-md-4 col-md-offset-4 border p-4 bg-white shadow">
            <div class="text-center mt-4">
                <img src="../../images/logo_easyfac.jpg" style="max-width: 50%; max-height: 50%;" alt="logo">
            </div>
            @if (!session('statusVerify'))
            <form class="" action="{{ route('identity.verify') }}" method="POST">
                @csrf
                @if (session('status'))
                <h4 class="text-center text-primary">{{ session('status') }}</h4>
                @endif
                @if (session('noVerify'))
                <h4 class="text-center text-danger">{{ session('noVerify') }}</h4>
                @endif
                <div class="form-group">
                    <input type="text" name="code"  id="code" class="form-control form-control-lg" placeholder="Entrer le code reçu ici" value="{{ old('code') }}" required>
                    <!-- <input type="hidden" name="numberTemtation" value="1"> -->
                    <input type="hidden" name="verification_token" value="{{ $token }}">
                </div>
                <div class="mt-3">
                    <button class="btn btn-block btn-lg font-weight-bold text-white auth-form-btn" style="{{ background_color_1() }}">Valider</button>
                </div>
                <a href="{{route('deconnexion')}}"> Revenir en arrière</a>
            </form>
            @else
            @php
            session()->forget('verification_token');
            @endphp
            <h4 class="text-center text-primary">{{ session('statusVerify') }}</h4>
            <p style="text-align: center;">Vous serez redirigé dans un instant ...</p>
            <script>
                setTimeout(function() {
                    window.location.href = '/verif-access';
                }, 3000);
            </script>
            @endif
        </div>
    </div>

    <script src="{{ WEB_SITE_NAME }}/js/select2.min.js"></script>
    <script>
        // Initialiser Selectize sur les éléments de sélection avec la classe 'selectize'
        $(document).ready(function() {
            $('.js-single').select2();
        });
    </script>
</body>

</html>
