@extends('layouts.mudeo')

@section('head')
    <title>{{ $song->user->handle . ' - ' . $song->title }} | mudeo</title>
    <meta name="description" content="{{ $song->description }}">

    <meta property="og:title" content="{{ $song->user->handle . ' - ' . $song->title }}">
    <meta property="og:description" content="{{ $song->description }}">
    <meta property="og:image" content="{{ $song->thumbnail_url }}">
    <meta property="og:url" content="{{ $song->url }}">
    <meta property="og:site_name" content="mudeo">

    <meta name="twitter:title" content="{{ $song->user->handle . ' - ' . $song->title }}">
    <meta name="twitter:description" content="{{ $song->description }}">
    <meta name="twitter:image" content="{{ $song->thumbnail_url }}">
    <meta name="twitter:card" content="player">
    <meta name="twitter:site" content="@mudeo_app">
    <meta name="twitter:image:alt" content="{{ $song->title }}">
    <meta name="twitter:player" content="{{ $song->video_url }}">
    <meta name="twitter:player:stream:content_type" content="video/mp4;" codecs="avc1.42E01E1, mp4a.40.2">
    <meta name="twitter:player:height" content="480">
    <meta name="twitter:player:width" content="640">

    <!--
    <link href="https://vjs.zencdn.net/7.7.4/video-js.css" rel="stylesheet">
    <script src='https://vjs.zencdn.net/7.7.4/video.js'></script>
    -->

@endsection

@section('body')

    <style>
        body {
            background-color: black;
            margin: 0;
        }

        #video {
            @if ($song->width > $song->height)
                width: 100vw;
                min-width: 100%;
                max-width: 99vw;
                max-height: 99vh;
            @else
                height: 100vh;
                min-height: 100%;
                max-height: 99vh;
                max-width: 99vw;
            @endif
        }

        #links {
            color: red;
            position: absolute;
            bottom: 10vh;
            right: 2vh;
            z-index: 1;
        }

        /*
        vjs-custom {
            height: 90%;
            height: -moz-available;
            height: -webkit-fill-available;
            height: fill-available;
        }
        */

    </style>

    <div id="links">
        <a href="https://mudeo.app" target="_blank" border="0" title="Try the app">
            <img src="/images/icon.png" style="border-radius: 50%; width: 5vh; padding-right: .7vh;"/>
        </a>
        <a href="https://www.youtube.com/channel/UCX5ONbOAOG3bYe3vTXrWgPA" target="_blank" border="0" title="YouTube">
            <img src="/images/youtube.png" style="border-radius: 50%; width: 5vh; padding-right: .7vh;"/>
        </a>
        <a href="https://twitter.com/mudeo_app" target="_blank" border="0" title="Twitter">
            <img src="/images/twitter.png" style="border-radius: 50%; width: 5vh;"/>
        </a>
    </div>

    <center>
        @if ($song->is_rendered)
            <video controls autoplay id="video">
                <source src="{{ $song->video_url }}" type="video/mp4">
            </video>
        @else
            <div style="color: white; padding-top: 180px; font-family:arial,sans-serif; font-size:22px">
                The video is processing, it should be ready in a few minutes
            </div>
        @endif
    </center>

    <!--
    <div class="container-fluid">
        <div class="d-flex justify-content-center">
            <video id='my-video' class='video-js vjs-default-skin vjs-big-play-centered vjs-custom'
                controls preload='auto' poster='' data-setup='{}'>
                <source src='{{ $song->video_url }}' type='video/mp4'>
                    <p class='vjs-no-js'>
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                    </p>
            </video>
        </div>
    </div>

    <script>
        videojs.addLanguage('en', {"The media could not be loaded, either because the server or network failed or because the format is not supported.": "The video is processing, it should be ready in a few minutes"});
    </script>
    -->

    <script>
        /* https://stackoverflow.com/q/39384154/497368 */
        /*
        function calcVH() {
            $('#video').innerHeight( $(this).innerHeight() );
        }
        $(window).on('DOMContentLoaded load resize orientationchange', function() {
            calcVH();
        });
        */

        function calcVH() {
          var vH = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
          document.getElementById("video").setAttribute("style", "height:" + vH + "px;");
        }
        calcVH();
        window.addEventListener('DOMContentLoaded', calcVH, true);
        window.addEventListener('load', calcVH, true);
        window.addEventListener('resize', calcVH, true);
        window.addEventListener('onorientationchange', calcVH, true);
    </script>

@endsection
