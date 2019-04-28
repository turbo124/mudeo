@extends('layouts.mudeo')

@section('body')
	<style>
		body {background-color:black}
	</style>

	<p></p>


	<center>
		<a href="https://mudeo.app" target="_blank" style="font-weight:100">DOWNLOAD THE APP</a><p/>
	</center>


	<div class="container-fluid">
		<div class="d-flex justify-content-center">
			<video id='my-video' class='video-js vjs-default-skin vjs-big-play-centered' autoplay controls preload='auto' poster='' data-setup='{}'>
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
