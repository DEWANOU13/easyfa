<!-- Top Bar Start -->
<div class="topbar bg-white">
    <style>
        @media (max-width: 576px) {
            .none {
                display: none;
            }
        }
    </style>
    <div class="topbar-left	d-none d-lg-block">
        <div class="text-center ">
            <a href="/" class="navbar-brand"><img src="{{ asset('images/logo_easyfac.png') }}" height="60" width="210" alt="EASY FAC" class="logo"></a>
        </div>
    </div>

    <nav class="navbar-custom bg-white">
        <ul class="list-inline float-right mb-0">
            <li class="list-inline-item dropdown notification-list">
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="mdi mdi-bell-outline fs-4">
                    </i>
                    <span class=" bg-danger rounded-circle border px-2 absolute text-white fw-bold fs-6">{{notification_approvs()->count() + notification_achemis()->count() + notification_approv_emballages()->count() + notification_achemi_emballages()->count()}}</span>

                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated" style="width: 500px">
                    <h6 class="dropdown-item-text">Notifications</h6>
                    <div class="slimscroll notification-item-list px-2" id="notificationList">
                        <!-- Notifications will be loaded here dynamically -->
                        @if(notification_approvs()->count() > 0)
                        <div>
                            @foreach (notification_approvs() as $key => $value)
                            <div class="card card-body shadow-lg p-3 mb-3 bg-body rounded bg-Light">
                                <h6 class="text-primary">{{$value->Motif}}</h6>
                                <p>Vous venez d'être approvisionner par l'agence <strong>{{$value->NomAgenceSource}}</strong> Réference <strong>{{$value->Reference_Approv }}
                                </strong> Qté <strong>{{$value->Qte_Approvisionnee }}</strong>. <a href="{{route('reception_approvisionnement_emballage')}}">Cliquez ici pour receptionner</a> </p>
                                <p><a href="{{route('changeStatutnotificationApprov', $value->id)}}">Marquer comme lu</a></p>
                            </div>
                            @endforeach

                    </div>
                    @endif
                    @if(notification_approv_emballages()->count() > 0)
                    <div>
                        @foreach (notification_approv_emballages() as $key => $value)
                        <div class="card card-body shadow-lg p-3 mb-3 bg-body rounded bg-Light">
                            <h6 class="text-primary">{{$value->Motif}}</h6>
                            <p>Vous venez d'être approvisionner en emballage par l'agence <strong>{{$value->NomAgenceSource}}</strong> Réference <strong>{{$value->Reference_Approv }}
                            </strong> Qté <strong>{{$value->Qte_Approvisionnee }}</strong>. <a href="{{route('reception_approvisionnement')}}">Cliquez ici pour receptionner</a> </p>
                            <p><a href="{{route('changeStatutnotificationApprovEnb', $value->id)}}">Marquer comme lu</a></p>
                        </div>
                        @endforeach

                </div>
                @endif
                    @if(notification_achemis()->count() > 0)
                    <div>
                        @foreach (notification_achemis() as $key => $value)
                        <div class="card card-body shadow-lg p-3 mb-3 bg-body rounded bg-Light">
                            <h6 class="text-primary">{{$value->Motif}}</h6>
                            <p>Un acheminement vient d'etre effectuer par l'agence <strong>{{$value->NomAgenceSource}}</strong> Réference <strong>{{$value->Reference_achemis }}
                            </strong> Qté <strong>{{$value->Qte_acheminee }}</strong>. <a href="{{route('reception_acheminement')}}">Cliquez ici pour receptionner</a> </p>
                            <p><a href="{{route('changeStatutnotificationAchemi', $value->id)}}">Marquer comme lu</a></p>

                        </div>
                        @endforeach

                     </div>
                    @endif
                    @if(notification_achemi_emballages()->count() > 0)
                    <div>
                        @foreach (notification_achemi_emballages() as $key => $value)
                        <div class="card card-body shadow-lg p-3 mb-3 bg-body rounded bg-Light">
                            <h6 class="text-primary">{{$value->Motif}}</h6>
                            <p>Un acheminement en emballage vient d'etre effectuer par l'agence <strong>{{$value->NomAgenceSource}}</strong> Réference <strong>{{$value->Reference_achemis }}
                            </strong> Qté <strong>{{$value->Qte_acheminee }}</strong>. <a href="{{route('reception_acheminement_emballage')}}">Cliquez ici pour receptionner</a> </p>
                            <p><a href="{{route('changeStatutnotificationAchemiEmb', $value->id)}}">Marquer comme lu</a></p>

                        </div>
                        @endforeach

                     </div>
                    @endif
                    </div>

                    <a href="" class="dropdown-item text-center text-primary">Voir toutes <i class="fi-arrow-right"></i></a>
                </div>
            </li>


                <span class="font-medium text-base text-dark"> Agence {{ getAgenceById() }} | <span class="none">Bonjour, </span> {{ auth()->user()->name }}</span>
                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <div class="flex items-center px-1">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 mr-3">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="user" class="rounded-circle" style="border: solid;">
                        </div>
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown ">
                    <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profil</a>
                    <a class="dropdown-item" href="{{ route('user.preferences') }}">
                        <i class="fas fa-cog"></i>Préférences</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#staticChangeAgence" (click)="openModal()" href="#">
                        <i class="mdi mdi-swap-horizontal m-r-5 text-muted"></i>
                        <span>Changer Agence</span>
                    </a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#staticDeconnexion" (click)="openModal()" href="#">
                        <i class="mdi mdi-logout m-r-5 text-muted"></i>
                        <span>Se déconnecter</span>
                    </a>
                </div>
            </li>
        </ul>
        <ul class="list-inline menu-left mb-0">
            <li class="list-inline-item" style="{{ background_color_1() }}">
                <button type="button" class="button-menu-mobile open-left waves-effect">
                    <i class="ion-navicon" style="color:black;"></i>
                </button>
            </li>
        </ul>

        <div class="clearfix"></div>
    </nav>

    <!-- <nav class="navbar-custom bg-white">
        <ul class="list-inline float-right mb-0">
            <li class="list-inline-item dropdown notification-list">
                <span class="font-medium text-base text-dark"> Agence {{ getAgenceById() }} | <span class="none">Bonjour, </span> {{ auth()->user()->name }}</span>
                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <div class="flex items-center px-1">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 mr-3">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="user" class="rounded-circle" style="border: solid;">
                        </div>
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown ">
                    <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profil</a>
                    <a class="dropdown-item" href="{{ route('user.preferences') }}">
                        <i class="fas fa-cog"></i>Préférences</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#staticChangeAgence" (click)="openModal()" href="#">
                        <i class="mdi mdi-swap-horizontal m-r-5 text-muted"></i>
                        <span>Changer Agence</span>
                    </a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#staticDeconnexion" (click)="openModal()" href="#">
                        <i class="mdi mdi-logout m-r-5 text-muted"></i>
                        <span>Se déconnecter</span>
                    </a>
                </div>
            </li>
        </ul>
        <ul class="list-inline menu-left mb-0">
            <li class="list-inline-item" style="{{ background_color_1() }}">
                <button type="button" class="button-menu-mobile open-left waves-effect">
                    <i class="ion-navicon" style="color:black;"></i>
                </button>
            </li>
        </ul>

        <div class="clearfix"></div>

    </nav> -->

</div>
<!-- Top Bar End -->
<!-- Modal Deconnexion-->
<div class="modal fade" id="staticDeconnexion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticDeconnexion" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Déconnexion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment vous déconnecter
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('deconnexion') }}" method="GET">
                    @method('GET')
                    @csrf
                    <button class="btn btn-danger">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer d'agence -->
<div class="modal fade" id="staticChangeAgence" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Changer d'agences</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                Voici les agences auxquelles vous avez accès
                <form id="change-agence-form" action="{{ route('switchAgence') }}" method="POST">
                    @php
                    $agenceIds = \App\Models\AgenceUser::where('user_id', Auth::user()->id)->pluck('agence_id')->toArray();
                    $agences = \App\Models\Agence::whereIn('id', $agenceIds)->pluck('NomAgence', 'id');
                    @endphp
                    @csrf
                    <select name="agence" class="form-control">
                        @foreach ($agences as $id => $nom)
                        <option value="{{ $id }}">{{ $nom }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger" onclick="document.getElementById('change-agence-form').submit();">Changer</button>
            </div>
        </div>
    </div>
</div>
