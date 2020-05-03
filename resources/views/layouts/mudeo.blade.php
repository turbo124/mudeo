<!DOCTYPE html>
<html lang="en" data-flavor="{{ config('mudeo.is_dance') ? 'dance' : 'mudeo' }}">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('mudeo.analytics_id') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ config('mudeo.analytics_id') }}');
    </script>

    <meta charset="UTF-8">
    <meta content="IE=Edge" http-equiv="X-UA-Compatible">
    <link rel="shortcut icon" type="image/png" href="/images/icon.png"/>

    <meta property="og:site_name" content="{{ config('app.name') }}"></meta>
    <meta name="twitter:site" content="{{ config('mudeo.twitter_handle') }}"></meta>
    <meta name="twitter:creator" content="@hillelcoren"></meta>

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="twitter:app:name:iphone" content="{{ config('app.name') }}">
    <meta name="twitter:app:id:iphone" content="{{ config('mudeo.app_id_ios') }}">
    <meta name="twitter:app:url:iphone" content="https://itunes.apple.com/us/app/mudeo/{{ config('mudeo.app_id_ios') }}?mt=8">
    <meta name="twitter:app:name:googleplay" content="{{ config('app.name') }}">
    <meta name="twitter:app:id:googleplay" content="{{ config('mudeo.app_id_android') }}"/>

    @yield('head')
</head>

<body id="app-container">
    @section('body')
    @yield('body')

    @include('footer')
    @yield('footer')
</body>
</html>
