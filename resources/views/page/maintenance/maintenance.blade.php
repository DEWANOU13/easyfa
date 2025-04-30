@extends('layouts.master', ['title' => 'Maintenance'])

@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'MAINTENANCE',
        'infos2' => 'MAINTENANCE',
        'infos3' => 'EJECTION',
    ])

    <section style="margin-bottom: 140px;">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="my-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row mb-3">
            <div class="d-flex justify-content-between">
                <h5 class="text-primary">Maintenance du site (Notifier aux utilisateurs la mise en maintenace du site)</h5>

                <button type="button" class="btn  btn-success" data-bs-toggle="modal"cstyle="text-align: right"
                    title="Introduire tous les utilsateurs après la maintenance"
                    data-toggle="tooltip" data-bs-target="#staticBackdropEnlever">Remettre le site en marche</button>
            </div>
        </div>

        {{-- Modal pour mettre le site en main --}}
        <div class="modal fade" id="staticBackdropEnlever" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                            Demande de confirmation
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment remettre le site en marche ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        <form action="{{ route('endMaintenance') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn text-white" style="{{ background_color_2() }}"
                                data-bs-dismiss="modal"> Oui
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('storeInMaintenance') }}" method="post" class=" align-items-center gap-4">
                        @csrf
                        <span>
                            <label for="libelle" class="label-form">Nombre de temps pour la mise en maintenance du
                                site&nbsp;</label>
                        </span>

                        <div class="d-flex gap-5">
                            <div class="d-flex gap-3">
                                <div class="form-group">
                                    <label for="myInputJour">Jour(s)</label>
                                    <input type="number" value="0" id="myInputJour" style="width: 75px" class="form-control"
                                        name="jour_maintenance">
                                </div>
                                <div class="form-group">
                                    <label for="myInputHeure">Heure(s)</label>
                                    <input type="number" value="0" id="myInputHeure" style="width: 75px" class="form-control"
                                        name="heure_maintenance">
                                </div>
                                <div class="form-group">
                                    <label for="myInputMinute">Minute(s)</label>
                                    <input type="number" value="0" id="myInputMinute" style="width: 75px"
                                        class="form-control" name="minute_maintenance">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn  btn-primary" data-bs-toggle="modal"
                                    class="btn btn-success" style="text-align: right; margin-top: 30px"
                                    title="Mettre le site en maintenance" data-toggle="tooltip"
                                    data-bs-target="#staticBackdropAjouter">Mis en maintenance</button>
                            </div>
                        </div>

                        {{-- Modal pour mettre le site en maintenance --}}
                        <div class="modal fade" id="staticBackdropAjouter" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                            Demande de confirmation
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment mettre le site en maintenance dans <strong
                                            id="displayInput"></strong> ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Non</button>
                                        <button type="submit" class="btn text-white" style="{{ background_color_2() }}"
                                            data-bs-dismiss="modal"> Oui
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.alert')
@endSection
