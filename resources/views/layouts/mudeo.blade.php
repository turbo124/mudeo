<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>mudeo</title>
    <meta content="IE=Edge" http-equiv="X-UA-Compatible">
    <meta name="description" content="A new Flutter project.">
    <link rel="shortcut icon" type="image/png" href="https://mudeo.app/images/icon.png"/>
    
    @yield('head')
</head>

<body>
    @section('body')
    @yield('body')

    @include('footer')
    @yield('footer')
</body>
</html>
