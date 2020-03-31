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
    <meta name="twitter:site" content="mudeo_app">
    <meta name="twitter:image:alt" content="{{ $song->title }}">
    <meta name="twitter:player" content="{{ $song->youTubeUrl() }}">
@endsection

@section('body')
	<style>
		body {
            background-color:black
        }
        iframe{
            width: 100vw;
            height: calc(100vw/1.83);
        }
	</style>

	<p></p>

	<center>
		<a href="https://mudeo.app" target="_blank" style="font-weight:100">DOWNLOAD THE APP</a>

        &nbsp;&nbsp; <span style="color:white">•</span> &nbsp;&nbsp;

        <a href="https://twitter.com/mudeo_app" target="_blank" style="font-weight:100">FOLLOW US ON TWITTER</a>
	</center>

    <p></p>

	<div class="container-fluid">
		<div class="d-flex justify-content-center">
            <iframe allowfullscreen
                src="https://www.youtube.com/embed/{{ $song->youtube_id }}?autoplay=1&modestbranding=1&rel=0"
                frameborder="0"></iframe>
		</div>
	</div>

@endsection
