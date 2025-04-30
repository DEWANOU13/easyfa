@extends('layouts.layout', ['title' => $user->exists ? 'Modifier Utilisateur' : 'Création Utilisateur'])
@section('content')
    <style>
        .entete_tableau {
            background-color: #6104ed;
            color: white;
        }
    </style>
    <section>
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="my-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <div class="row d-flex mt-4">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Accueil</li>
                    <li class="breadcrumb-item active"><a href="{{ route('user.index') }}">Utilisateur</a></li>
                    @if ($user->exists)
                        <li class="breadcrumb-item active"><a href="{{ route('user.edit', $user) }}"><span class="badge bg-primary">Modification utilisateur</span></a></li>
                    @else
                        <li class="breadcrumb-item active"><a href="{{ route('user.create') }}"><span class="badge bg-primary">Nouveau utilisateur</span></a></li>
                    @endif
                </ol>
            </nav>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary">{{ $user->exists ? 'Editer ' . $user->nom_utilisateur : 'Créer un utilisateur' }}</h4><br>
            <a href="{{ route('user.index') }}" class="btn btn-primary">Retour
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-backspace-filled" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M20 5a2 2 0 0 1 1.995 1.85l.005 .15v10a2 2 0 0 1 -1.85 1.995l-.15 .005h-11a1 1 0 0 1 -.608 -.206l-.1 -.087l-5.037 -5.04c-.809 -.904 -.847 -2.25 -.083 -3.23l.12 -.144l5 -5a1 1 0 0 1 .577 -.284l.131 -.009h11zm-7.489 4.14a1 1 0 0 0 -1.301 1.473l.083 .094l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.403 1.403l.094 -.083l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.403 -1.403l-.083 -.094l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.403 -1.403l-.094 .083l-1.293 1.292l-1.293 -1.292l-.094 -.083l-.102 -.07z"
                        stroke-width="0" fill="currentColor" />
                </svg>
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <form class="was-validated" method="POST" id="myForm" action='{{ route($user->exists ? 'user.update' : 'user.store', $user) }}'>
                    @csrf
                    @method($user->exists ? 'put' : 'post')
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Utilisateur(ou email)</label>
                        <input type="text" class="form-control" name="email" value="{{ $user->exists ? $user->email : old('email') }}" id="email" aria-label="file example" required>
                        @if ($errors->has('email'))
                            <div class="text-danger">
                                {{ $errors->first('email') }}
                            </div>
                        @else
                            <div class="invalid-feedback">Le nom d'utlisateur est obligatoire</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nom</label>
                        <input type="text" class="form-control" name="name" value="{{ $user->exists ? $user->name : old('name') }}" id="name" aria-label="file example" required>
                        @if ($errors->has('name'))
                            <div class="text-danger">
                                {{ $errors->first('name') }}
                            </div>
                        @else
                            <div class="invalid-feedback">Le nom est obligatoire</div>
                        @endif
                    </div>
                    @if(!$user->exists)
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control" aria-label="file example" required>
                            @if ($errors->has('password'))
                                <div class="text-danger">
                                    {{ $errors->first('password') }}
                                </div>
                            @else
                                <div class="invalid-feedback">Le mot de passe est obligatoire</div>
                            @endif
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="actif" class="form-label fw-bold">Actif ?</label>
                        <select class="form-select" name="actif" required aria-label="select example">
                            <option value="1" {{ ($user->exists && $user->actif == '1') ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ ($user->exists && $user->actif == '0') ? 'selected' : '' }}>Non</option>
                        </select>
                        @if ($errors->has('actif'))
                            <div class="text-danger">
                                {{ $errors->first('actif') }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalUser">{{ $user->exists ? 'Sauvegarder' : 'Enregistrer' }}</button>
                    </div>

                    {{-- Modal enregistrement ou suvegarde --}}
                    <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">Voulez-vous {{ $user->exists ? 'sauvegarder' : 'enregistrer' }} l'entête et le pied de page format A4
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button class="btn btn-primary" type="submit">Sauvegarder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endSection
