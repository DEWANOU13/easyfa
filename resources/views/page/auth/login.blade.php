<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>GESTION FACTURATION</title>
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="stylesheet" href="css/bootstrap.bundle.min.css">
  <link rel="stylesheet" href="css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="css/main.cdn.min.css">
  <link rel="icon" href="images/logo_easyfac.jpg">
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0" style="background-color: #c6c6c6">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light shadow text-left py-5 px-4 px-sm-5">
              <div class="brand-logo text-center">
                <img src="../../images/logo_easyfac.jpg" alt="logo">
              </div>
              <form class="" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                  <input type="text" name="email" class="form-control form-control-lg" placeholder="Entrer votre login" value="{{ old('email') }}" required>
                  @error('email')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                  @enderror
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Entrer votre mot de passe" required>
                    <span class="input-group-text" id="clickTogglePassword" title="" data-toggle="tooltip">
                        <span class="border-0"><i class="bi-eye" id="togglePassword"></i></span>
                    </span>
                  </div>
                  @error('password')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                  @enderror
                </div>
                <div class="mt-3">
                  <button class="btn btn-block btn-lg font-weight-bold text-white auth-form-btn" style="background-color: #007bff">SE CONNECTER</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('jquery.min.js') }}"></script>
  <script src="js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function(){
        $("#clickTogglePassword").click(function(){

            var passwordField = $("#password");
            var passwordFieldType = passwordField.attr("type");

            var iconField = $("#togglePassword");
            var iconFieldType = iconField.attr("class");

            //Mot de passe masques ou afficher au click
            if(passwordFieldType === "password"){
                passwordField.attr("type", "text");
                $("#clickTogglePassword").attr("title", "Masquer le mot de passe");
            }else{
                passwordField.attr("type", "password");
                $("#clickTogglePassword").attr("title", "Afficher le mot de passe");
            }

            // Icone masquer ou afficher au click
            if (iconFieldType === "bi-eye") {
                iconField.attr("class", "bi-eye-slash");
            }else{
                iconField.attr("class", "bi-eye");
            }
        });
    });

    /* Infobulle personnalisé */
    $(function () {
        // Initialisez les infobulles
        $('[data-toggle="tooltip"]').tooltip({
            title: function () {
                return $(this).data('title');
            }
        });
    });
  </script>

</body>
</html>
