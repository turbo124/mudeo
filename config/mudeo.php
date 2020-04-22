<?php

return [
	'api_version' => '0.1',
	'api_secret' => env('API_SECRET',''),
	'asset_url' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
	'app_url' =>env('APP_URL',''),
	'app_environment' => env('APP_ENV',''),
	'publish_secret' => env('PUBLISH_SECRET',''),
	'analytics_id' => env('ANALYTICS_ID', ''),
	'app_id_ios' => env('APP_ID_IOS', 'id1459106474'),
	'app_id_android' => env('APP_ID_ANDROID', 'app.mudeo.mudeo'),
	'twitter_handle' => env('TWITTER_HANDLE', '@mudeo_app'),
	'contact_email' => env('CONTACT_EMAIL', 'contact@mudeo.app'),
	'youtube_channel' => env('YOUTUBE_CHANNEL', 'UCX5ONbOAOG3bYe3vTXrWgPA'),
	'tag_line' => env('TAG_LINE', 'a collaborative music video app'),
	'app_description' => env('APP_DESCRIPTION', 'The app enables you to easily collaborate on multi-track music videos. One artist can start a song and then any other artist can edit it to add their own tracks. It\'s sort of a mashup between TikTok, Acapella and GitHub.'),
];
