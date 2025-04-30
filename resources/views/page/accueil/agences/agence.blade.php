
@extends('layouts.master', ['title' => 'Agence'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Agence',
        'infos2' => 'Agence',
        'infos3' => 'Liste',
    ])

@livewire('agence.nouveau-agence', ['listeAgences' => $listeAgences])

    <script>
        $(document).ready(function() {
            $('#magasinsTable').on('click', '.clickable-row', function() {
                var ID = $(this).attr('data-id');
                $('#modifierLink').attr('wire:click.prevent', "editAgence(" + ID + ")");

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.clickable-row').on('click', function() {
                // Supprimez la classe 'selected' de toutes les lignes
                $('.clickable-row').removeClass('selected');
                // Ajoutez la classe 'selected' à la ligne cliquée
                $(this).addClass('selected');
            });
        });
    </script>

    <style>
        .selected>td {
            background-color: rgb(29, 9, 101);
            /* Ou la background_color_1 de votre choix */
            color: white;
            /* background_color_1 du texte sur fond bleu */
        }
    </style>

    @include('layouts.alert')
@endSection
