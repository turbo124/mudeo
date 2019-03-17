@extends('layouts.mudeo')

	@section('body')

		<div class="container-fluid">
		    
		    <div class="d-flex justify-content-center">
		    	<h1>{{ $song->title }}</h1>
		    </div>

		    <div class="d-flex justify-content-center">

				<video id="player" playsinline controls>
					<source src="{{ $video_url }}" type="video/mp4" />
				</video>

			</div>

		    <div class="d-flex justify-content-center">
		    	<p>{{ $song->description }}</p>
		    </div>

		</div>

	@endsection
