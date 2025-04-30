@php
    inMaintenance();
@endphp
<x-guest-layout>
  <x-authentication-card>
    <x-slot name="logo">
      <x-authentication-card-logo />
    </x-slot>

    <div class="mb-4 text-sm text-gray-600">
      {{ __('Vous avez oublié votre mot de passe ? aucun problème. Faites-nous savoir votre adresse e-mail et nous vous en informerons par e-mail un lien de réinitialisation de mot de passe qui vous permettra d’en choisir une nouvelle.') }}
    </div>

    @if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
      {{ session('status') }}
    </div>
    @endif

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="block">
        <x-label for="email" value="{{ __('Email') }}" />
        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <x-button>
          {{ __('Lien de réinitialisation du mot de passe de l’e-mail') }}
        </x-button>
      </div>
    </form>
  </x-authentication-card>
</x-guest-layout>
