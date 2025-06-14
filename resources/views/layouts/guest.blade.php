<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bibliobit - @yield('title')</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    @vite(['resources/scss/sb-admin-2.scss', 'resources/js/app.js'])
</head>
<body class="bg-gradient-primary">
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
