@extends('layouts.mudeo')

@section('head')
  <title>{{ config('app.name') }} | {{ config('mudeo.tag_line') }}</title>
  <meta name="description" content="{{ config('mudeo.app_description') }}">

  <meta property="og:title" content="{{ config('app.name') }}"></meta>
  <meta property="og:description" content="{{ config('mudeo.tag_line') }}"></meta>
  <meta property="og:image" content="/images/banner.jpg"></meta>
  <meta property="og:url" content="{{ config('app.url') }}"></meta>

  <meta name="twitter:card" content="summary_large_image"></meta>
  <meta name="twitter:title" content="{{ config('app.name') }}"></meta>
  <meta name="twitter:description" content="{{ config('mudeo.tag_line') }}"></meta>
  <meta name="twitter:image" content="/images/banner.jpg"></meta>

  <meta name="msapplication-starturl" content="/">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="apple-touch-icon" href="/images/icon.png">
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
  <script src="main.dart.js?clear_cache=2" type="application/javascript"></script>

@endsection
