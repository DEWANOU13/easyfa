<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYFAC - Autorisation d'accès </title>
    <link rel="shortcut icon" href="{{ asset('images/logo_easyfac.png') }}">
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css ">
    <link href="{{ asset('dashboard/css/icons.css') }}" rel="stylesheet" type="text/css ">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet" type="text/css ">

    {{-- <link href="{{ asset('dashboard/css/jquery-ui.css') }}" rel="stylesheet" type="text/css "> --}}

    <style>
        .centered {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
    </style>
</head>

<body class="pb-0">
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div>
    @php
        $user = auth()->user();
    @endphp
    @if ($user->actif == 1)
        @if ($user->identity_verified == 1 || $user->email_verified_at != null)
            @if ($user->force_password_change == 0)
                @if (sessionsBdd()->count() > 0)
                    @php
                        session()->put('statusVerifyTemtation', 0);
                    @endphp
                    <div class="centered m-auto">
                        <div class="card card-body ">
                            <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                                alt="Bienvenu sur l'application EASYFAC" style="margin: auto">
                            <p style="color: blue;"> Vous avez déjà une connexion active. <strong>Voulez-vous
                                    déconnecter l'ancienne connexion ?</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @method('POST')
                                        @csrf
                                        <button class="btn btn-primary" style="width: 90%;" type="submit">Non</button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form method="GET" action="{{ route('identity.trigger.verification') }}">
                                        @method('GET')
                                        @csrf
                                        <button class="btn btn-danger" style="width: 90%;" type="submit">Oui</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @if (session()->get('site_id'))
                        <script>
                            setTimeout(function() {
                                window.location.href = '/accueil';
                            }, 0);
                        </script>
                    @else
                        @if (agences()->count() > 0)
                            @if ($user->agences->count() == 1)
                                @if ($user->agences->first()->agence->EnActivite == 1)
                                    @php
                                        session()->put('site_id', $user->agences->first()->agence_id);
                                    @endphp
                                    <script>
                                        setTimeout(function() {
                                            window.location.href = '/accueil';
                                        }, 0);
                                    </script>
                                @else
                                    <div class="centered m-auto">
                                        <div class="card card-body ">
                                            <img src="{{ asset('images/logo_easyfac.png') }}" height="80"
                                                width="300" alt="Bienvenu sur l'application EASYFAC"
                                                style="margin: auto">
                                            <p>Votre agence n'est pas en activité, Merci de contacter l'administrateur
                                            </p>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @method('POST')
                                                @csrf
                                                <button class="btn btn-primary" type="submit">Connectez-vous</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @elseif ($user->agences->count() > 1)
                                <script>
                                    setTimeout(function() {
                                        window.location.href = '/choixAgence';
                                    }, 0);
                                </script>
                            @else
                                <div class="centered m-auto">
                                    <div class="card card-body ">
                                        <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                                            alt="Bienvenu sur l'application EASYFAC" style="margin: auto">
                                        <p>Vous n'avez accès à aucune agence, Merci de contacter l'administrateur</p>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @method('POST')
                                            @csrf
                                            <button class="btn btn-primary" type="submit">Connectez-vous</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="centered m-auto">
                                <div class="card card-body ">
                                    <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                                        alt="Bienvenu sur l'application EASYFAC" style="margin: auto">
                                    <p>Pas d'agences disponible. Merçi de contacter l'administrateur</p>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @method('POST')
                                        @csrf
                                        <button class="btn btn-primary" type="submit">Se déconnecter</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            @else
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="card p-4 shadow-sm">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                                alt="Bienvenue sur l'application EASYFAC">
                        </div>
                        <p class="text-center mb-4">Changer votre mot de passe pour continuer.</p>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('first.password.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input id="password" class="form-control" type="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer mot de passe</label>
                                <input id="password_confirmation" class="form-control" type="password"
                                    name="password_confirmation" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Valider
                                </button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="{{ route('deconnexion') }}">Revenir en arrière</a>
                        </div>
                    </div>
                </div>

            @endif
        @else
            @php
                session()->put('statusVerifyTemtation', 0);
            @endphp
            <div class="centered m-auto">
                <div class="card card-body ">
                    <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                        alt="Bienvenu sur l'application EASYFAC" style="margin: auto">
                    <p>Votre adresse email n'est pas vérifiée depuis la création de votre compte.</p>
                    <form method="GET" action="{{ route('email.trigger.verification') }}">
                        @method('GET')
                        @csrf
                        <button class="btn btn-primary" type="submit">Vérifiez votre email ici</button>
                    </form>
                    <a href="{{ route('deconnexion') }}"> Revenir en arrière</a>
                </div>
            </div>
        @endif
    @else
        <div class="centered m-auto">
            <div class="card card-body ">
                <img src="{{ asset('images/logo_easyfac.png') }}" height="80" width="300"
                    alt="Bienvenu sur l'application EASYFAC" style="margin: auto">
                <p>Votre compte n'est pas actif. Merçi de contacter l'administrateur</p>
                <form method="POST" action="{{ route('logout') }}">
                    @method('POST')
                    @csrf
                    <button class="btn btn-primary" type="submit">Connectez-vous</button>
                </form>
            </div>
        </div>
    @endif

    <script src="{{ asset('dashboard/js/jquery.min.js ') }}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.bundle.min.js ') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.slimscroll.js ') }}"></script>
    {{-- <script src="{{ asset('dashboard/js/jquery.scrollTo.min.js ') }}"></script> --}}

    <!-- App js -->
    <script src="{{ asset('dashboard/js/app-drixo.js ') }}"></script>

    <!-- countdown js -->
    <script src="{{ asset('dashboard/plugins/countdown/jquery.countdown.min.js ') }}"></script>
    {{-- <script src="{{ asset('dashboard/pages/countdown.int.js ') }}"></script> --}}

</body>

</html>
