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
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image:alt" content="{{ $song->title }}">
    <meta name="twitter:player" content="{{ str_replace('.mp4', '_low_res.mp4', $song->video_url) }}">
@endsection

@section('body')
	<style>
		body {
            background-color:black
        }

        vjs-custom {
            height: 90%;
            height: -moz-available;          /* WebKit-based browsers will ignore this. */
            height: -webkit-fill-available;  /* Mozilla-based browsers will ignore this. */
            height: fill-available;
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
			<video id='my-video' class='video-js vjs-default-skin vjs-big-play-centered vjs-custom'
                autoplay controls preload='auto' poster='' data-setup='{}'>
				<source src='{{ $video_url }}' type='video/mp4'>
					<p class='vjs-no-js'>
						To view this video please enable JavaScript, and consider upgrading to a web browser that
						<a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
					</p>
			</video>
		</div>
	</div>

	<script>
		videojs.addLanguage('en', {"The media could not be loaded, either because the server or network failed or because the format is not supported.": "The video is still processing, please try again in a few minutes."});
		//var player = videojs('my-video');
		//player.fluid(true);
	</script>

@endsection
