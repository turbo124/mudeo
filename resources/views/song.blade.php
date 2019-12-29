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
@endsection

@section('body')
	<style>
		body {background-color:black}


        /* https://stackoverflow.com/a/47039499/497368
           Make the video relative, instead of absolute, so that
           the parent container will size based on the video. Also,
           note the max-height rule. Note the line-height 0 is to prevent
           a small artifact on the bottom of the video.
         */
        .video-js.vjs-fluid,
        .video-js.vjs-16-9,
        .video-js.vjs-4-3,
        video.video-js,
        video.vjs-tech, {
          max-height: calc(100vh - 64px);
          position: relative !important;
          width: 100%;
          height: auto !important;
          max-width: 100% !important;
          padding-top: 0 !important;
          line-height: 0;
        }

        /* Fix the control bar due to us resetting the line-height on the video-js */
        .vjs-control-bar {
          line-height: 1;
        }


	</style>

	<p></p>


	<center>
		<a href="https://mudeo.app" target="_blank" style="font-weight:100">DOWNLOAD THE APP</a><p/>
	</center>

	<div class="container-fluid">
		<div class="d-flex justify-content-center">
			<video id='my-video' class='video-js vjs-default-skin vjs-big-play-centered'
                autoplay controls preload='auto' poster='' data-setup='{}' height='100%'>
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
		var player = videojs('my-video');
		player.fluid(true);
	</script>

@endsection
