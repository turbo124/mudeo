<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('ninja.analytics_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ config('ninja.analytics_id') }}');
    </script>

    <meta charset="UTF-8">
    <title>mudeo</title>
    <meta content="IE=Edge" http-equiv="X-UA-Compatible">
    <meta name="description" content="A new Flutter project.">
    <link rel="shortcut icon" type="image/png" href="https://mudeo.app/images/icon.png"/>

    <meta name="twitter:app:name:iphone" content="mudeo">
    <meta name="twitter:app:id:iphone" content="id1459106474">
    <meta name="twitter:app:url:iphone" content="https://itunes.apple.com/us/app/mudeo/id1459106474?mt=8">
    <meta name="twitter:app:name:googleplay" content="mudeo">
    <meta name="twitter:app:id:googleplay" content="app.mudeo.mudeo"/>

    @yield('head')
</head>

<body>
    @section('body')
    @yield('body')

    @include('footer')
    @yield('footer')
</body>
</html>
