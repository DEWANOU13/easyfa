@php
    notInMaintenance();
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Maintenance du site</title>
    <script src="{{ asset('jquery.min.js') }}"></script>
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ WEB_SITE_NAME }}/css/select2.min.css">
</head>

<body style="background-color: #007bffa1 !important">
    <div class="d-flex justify-content-center align-items-center rounded border p-3" style="height: 100vh; background-color: #007bffa1">
        <div class="col-md-4 col-md-offset-4 border border-none p-4 bg-white shadow" style="border-radius: 20px">
            <div class="text-center">
                <img src="../../images/logo_easyfac.jpg" style="max-width: 50%; max-height: 50%;" alt="logo">
            </div>
            <h5 class="text-center text-success">Le site est en maintenance</h5>
        </div>
    </div>
    <script src="{{ WEB_SITE_NAME }}/js/select2.min.js"></script>
</body>

</html>
