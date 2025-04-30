@php
    inMaintenance();
@endphp
@extends('layouts.layout')

@section('content')
    @include('page.components.shared')

    <div class="container">
        <h4 class="text-primary mt-3 mb-3">Changement de mot de passe</h4>

        <form action="{{ route('changePassword') }}" method="post">
            @csrf
            <div class="row mb-3">
                <div class="col-md-12 form-group">
                    <label for="old_password" class="label-form">Mot de passe actuel :</label>
                    <input type="text" class="form-control" value="{{ old('old_password') }}" name="old_password" id="old_password" value="">
                    @error('old_password')
                        <div class="text-danger">
                            {{ $newMessage = str_replace('old password', 'Nouveau mot de passe', $message) }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 form-group">
                    <label for="new_password" class="label-form">Nouveau mot de passe :</label>
                    <input type="text" class="form-control" name="new_password" id="new_password" value="">
                    @error('new_password')
                        <div class="text-danger">
                            {{ $newMessage = str_replace('new password', 'Nouveau mot de passe', $message) }}
                        </div>
                    @enderror
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-md-12 form-group">
                    <label for="new_password_confirmation" class="label-form">Confirmer nouveau mot de passe</label>
                    <input type="text" class="form-control" name="new_password_confirmation"
                        id="new_password_confirmation" value="">
                    @error('new_password_confirmation')
                        <div class="text-danger">
                            {{ $newMessage = str_replace('new password confirmation', 'confirmer mot de passe', $message) }}
                        </div>
                    @enderror
                </div>
            </div>

            <button class="btn btn-primary mt-3">Valider</button>
        </form>
    </div>
@endsection
