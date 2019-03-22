<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['api_secret_check']], function () {

	Route::post('auth', 'AuthController@passwordAuth');
	Route::post('oauth', 'AuthController@oauthLogin');
	Route::post('reset_password', 'AuthController@resetPassword');
	Route::post('user/create', 'UserAccountController@create');
	Route::post('user/check_handle', 'UserAccountController@check_handle');
});


Route::group(['middleware' => ['api_secret_check','token_auth']], function () {

	Route::resource('songs', 'SongController'); // name = (songs. index / create / show / update / destroy / edit
	Route::resource('song_likes', 'SongLikeController'); // name = (songs. index / create / show / update / destroy / edit
	Route::resource('videos', 'VideoController'); // name = (songs. index / create / show / update / destroy / edit
	Route::resource('song_comments', 'SongCommentController'); // name = (track_comments. index / create / show / update / destroy / edit
	Route::resource('users', 'UserController'); // name = (users. index / create / show / update / destroy / edit
	Route::get('user', 'AuthController@current_user');

	Route::post('user/profile_image', 'UserController@storeProfileImage');
	Route::post('user/header_image', 'UserController@storeBackgroundImage');
});