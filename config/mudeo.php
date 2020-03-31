<?php
return [
	'app_name' => 'Mudeo',
	'api_version' => '0.1',
	'api_secret' => env('API_SECRET',''),
	'asset_url' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
	'app_url' =>env('APP_URL',''),
	'app_environment' => env('APP_ENV',''),
	'publish_secret' => env('PUBLISH_SECRET',''),
];
