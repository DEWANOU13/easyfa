@php
    inMaintenance();
@endphp

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Choix de l'agence</title>
  <script src="{{ asset('jquery.min.js') }}"></script>
  <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ WEB_SITE_NAME }}/css/select2.min.css">
</head>

<body style="background-color: #007bffa1 !important">
  <div class="d-flex justify-content-center align-items-center" style="height: 100vh; background-color: #007bffa1">
    <div class="col-md-4 col-md-offset-4 border border-none p-4 bg-white shadow" style="">
      <div class="text-center mt-4">
        <img src="../../images/logo_easyfac.jpg" style="max-width: 50%; max-height: 50%;" alt="logo">
      </div>
      <form action="{{ route('choixAgenceLogin') }}" method="POST">
        @csrf
        <h3 class="text-center text-primary">Choisissez une agence pour vous connecter</h3>
        <div class="form-group">
          <select name="agence" class="custom-select" id="agence" required>
            <option value="">Choisissez une agence</option>
            @foreach ($agences as $k => $v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
          {!! $errors->first('agence', '<p class="error">:message</p>') !!}
        </div>
        <button type="submit" class="btn float-right text-white" style="{{background_color_1()}}">Continuer</button>
      </form>
      <form action="{{ route('logout') }}" method="POST">
        @method('POST')
        @csrf
        <button class="btn btn-danger">Retour</button>
      </form>
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
