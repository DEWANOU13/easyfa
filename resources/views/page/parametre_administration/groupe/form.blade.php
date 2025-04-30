@extends('layouts.master', ['title' => $groupe->exists ? 'Modifier Groupe' : 'Creer Groupe'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Groupe',
        'infos2' => 'Groupe',
        'infos3' => $groupe->exists ? 'Modification' : 'Nouveau',
    ])

    <section>
        <div class="row">
            <!-- end col -->

            <div class="col-md-8 offset-md-2 mb-5">
                <div class="d-flex flex-row-reverse bd-highlight">
                    <div class="dropdown mb-2">
                        <a href="{{ route('groupe.index') }}" class="btn text-white" style="{{ background_color_1() }}">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="card m-b-30">
                    <div class="card-header" style="{{ background_color_2() }}">
                        <h4 class="mt-2 text-dark">
                            @if ($groupe->exists)
                                Modification de {{ $groupe->nom_groupe }}
                            @else
                                Enregistrement d'un groupe
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form class="" id="myForm" method="POST"
                            action='{{ route($groupe->exists ? 'groupe.update' : 'groupe.store', $groupe) }}'>
                            @csrf
                            @method($groupe->exists ? 'put' : 'post')
                            <div class="mb-3">
                                <label for="nom_groupe" class="form-label fw-bold">Groupe<span
                                        class="text-danger position-absolute" style="line-height: 1;">*</span>
                                    <input type="text" class="form-control" name="nom_groupe"
                                        value="{{ $groupe->exists ? $groupe->nom_groupe : old('nom_groupe') }}"
                                        id="nom_groupe" aria-label="file example" required>
                                    @if ($errors->has('nom_groupe'))
                                        <div class="text-danger">
                                            {{ $errors->first('nom_groupe') }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">Le nom est obligatoire</div>
                                    @endif
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea name="description" id="description" rows="6" class="form-control" aria-label="file example">{{ $groupe->exists ? $groupe->description : old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <div class="text-danger">
                                        {{ $errors->first('description') }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">La description est obligatoire</div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                                    data-bs-target="#modalGroupe">{{ $groupe->exists ? 'Sauvegarder' : 'Enregistrer' }}</button>
                            </div>

                            {{-- Modal enregistrement ou suvegarde --}}
                            <div class="modal fade" id="modalGroupe" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">Voulez-vous
                                            {{ $groupe->exists ? 'sauvegarder' : 'enregistrer' }} le groupe ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Annuler</button>
                                            <button class="btn btn-primary" type="submit">{{ $groupe->exists ? 'Sauvegarder' : 'Enregistrer' }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div> <!-- end col -->
        </div> <!-- end row -->

    </section>

    @include('layouts.alert')
@endSection
