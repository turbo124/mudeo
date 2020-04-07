@extends('layouts.mudeo')

@section('head')
  <meta property="og:title" content="mudeo"></meta>
  <meta property="og:description" content="make music together"></meta>
  <meta property="og:image" content="https://mudeo.app/images/banner.jpg"></meta>
  <meta property="og:url" content="https://mudeo.app"></meta>
  <meta property="og:site_name" content="mudeo"></meta>

  <meta name="twitter:card" content="summary_large_image"></meta>
  <meta name="twitter:title" content="mudeo"></meta>
  <meta name="twitter:description" content="make music together"></meta>
  <meta name="twitter:image" content="https://mudeo.app/images/banner.jpg"></meta>
  <meta name="twitter:site" content="@mudeo_app"></meta>
  <meta name="twitter:creator" content="@hillelcoren"></meta>

  <meta name="msapplication-starturl" content="/">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="mudeo">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="apple-touch-icon" href="https://mudeo.app/images/icon.png">
  <link rel="manifest" href="manifest.json">
@endsection

@section('body')

  <center style="font-family:Tahoma,Geneva,sans-serif;font-size:28px;color:#888888;padding-top:100px">
    Loading...
  </center>

  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function () {
        navigator.serviceWorker.register('flutter_service_worker.js');
      });
    }
  </script>
  <script src="main.dart.js" type="application/javascript"></script>	

@endsection
