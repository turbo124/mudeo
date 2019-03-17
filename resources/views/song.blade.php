@extends('layouts.mudeo')

	@section('body')

		<div class="container-fluid">
		    
		    <div class="d-flex justify-content-center">
		    	<h1>{{ $song->title }}</h1>
		    </div>

		    <div class="d-flex justify-content-center">

				  <video id='my-video' class='video-js' controls preload='auto' width='640' height='264'
				  poster='' data-setup='{}'>
				    <source src='{{ $video_url }}' type='video/mp4'>
				    <p class='vjs-no-js'>
				      To view this video please enable JavaScript, and consider upgrading to a web browser that
				      <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
				    </p>
				  </video>

			</div>

		    <div class="d-flex justify-content-center">
		    	<p>{{ $song->description }}</p>
		    </div>

		</div>

	@endsection
