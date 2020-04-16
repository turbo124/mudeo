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
    <meta name="twitter:image:alt" content="{{ $song->title }}">
    <meta name="twitter:player" content="{{ $song->video_url }}">
    <meta name="twitter:player:stream:content_type" content="video/mp4;" codecs="avc1.42E01E1, mp4a.40.2">
    <meta name="twitter:player:height" content="480">
    <meta name="twitter:player:width" content="640">
@endsection

@section('body')
	<style>

        body {
            margin: 0;
        }

        iframe {
            display: block;
            background: #000;
            border: none;
            height: 100vh;
            width: 100vw;
        }

        #links {
            color: red;
            position: absolute;
            bottom: 60px;
            left: 20px;
        }

	</style>

    <div id="links" title="Try the app">
        <a href="https://mudeo.app" target="_blank" border="0">
            <img src="/images/icon.png" style="border-radius: 50%; width: 60px;"/>
        </a>
    </div>

    <iframe src="{{ $song->youTubeEmbedUrl() }}" frameborder="0"></iframe>

@endsection
