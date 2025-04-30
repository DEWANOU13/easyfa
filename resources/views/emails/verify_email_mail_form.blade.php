<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmez votre identité</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card-container {
            width: 90%;
            max-width: 800px;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }
        .card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .card h1 {
            color: #333333;
            margin-top: 0;
        }
        .card p {
            color: #555555;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .card hr {
            border: none;
            border-top: 1px solid #dddddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="card">
            <div class="card-body">  
                <img src="{{ asset('images/logo_easyfac.png') }}" height="200" width="300" alt="logo"
                style="margin: auto">  
                <h1>Code de vérification de l'adresse email</h1>
                <p>Bonjour, {{ $user->name }}</p>
                <p>Votre code de vérification de l'adresse email est:</p>
                <h1 style="{{background_color_1()}}"> <strong>{{ $code }}</strong></h1>
                <p>Merci d'utiliser l'application EASYFAC</p>
                <p>Cordialement,</p>
                <p>L'équipe EASYFAC</p>
                <hr>
            </div>
        copyright &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
