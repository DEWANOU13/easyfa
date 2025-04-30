
@extends('layouts.master', ['title' => 'Categorie Client'])
@section('content')
    @include('layouts.partials.entete-page', [
        'infos1' => 'Categorie Client',
        'infos2' => 'Categorie Client',
        'infos3' => 'Liste',
    ])

@livewire('categorie-client.nouveau-categorie-client', ['listeCategorieClient' => $listeCategorieClient])

    <script>
        $(document).ready(function() {
            $('#categorieClientTable').on('click', '.clickable-row', function() {
                var ID = $(this).attr('data-id');

                $('#modifierLink').attr('wire:click.prevent', "editCategorieClient(" + ID + ")");

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
