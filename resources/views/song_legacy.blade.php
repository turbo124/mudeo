@extends('layouts.mudeo')

@section('head')
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
    <meta name="twitter:creator" content="@hillelcoren"></meta>
    <meta name="twitter:image:alt" content="{{ $song->title }}">
    <meta name="twitter:player" content="{{ $song->video_url }}">
    <meta name="twitter:player:height" content="480">
    <meta name="twitter:player:width" content="640">

    <link href="https://vjs.zencdn.net/7.7.4/video-js.css" rel="stylesheet">
    <script src='https://vjs.zencdn.net/7.7.4/video.js'></script>
@endsection

@section('body')

    <style>
        body {
            background-color: black;
            margin: 0;
        }

        #video {
            height: 100vh;
            min-height: 100%;
        }

        #links {
            color: red;
            position: absolute;
            top: 20px;
            left: 20px;
        }

        vjs-custom {
            height: 90%;
            height: -moz-available;          /* WebKit-based browsers will ignore this. */
            height: -webkit-fill-available;  /* Mozilla-based browsers will ignore this. */
            height: fill-available;
        }
    </style>

    <div id="links" title="Try the app">
        <a href="https://mudeo.app" target="_blank" border="0">
            <img src="/images/icon.png" style="border-radius: 50%; width: 50px;"/>
        </a>
    </div>

    <center>
    <video controls autoplay id="video">
        <source src="{{ $song->video_url }}" type="video/mp4">
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
        -->
    </video>
    </center>

    <script>
        videojs.addLanguage('en', {"The media could not be loaded, either because the server or network failed or because the format is not supported.": "The video is still processing, please try again in a few minutes."});
    </script>

@endsection
