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

Route::post('auth', 'AuthController@passwordAuth');
Route::post('reset_password', 'AuthController@resetPassword');
Route::post('user/create', 'UserAccountController@create');

//Route::group(['middleware' => ['auth:api']], function () {
Route::group(['middleware' => ['api']], function () {

	Route::resource('songs', 'SongController'); // name = (songs. index / create / show / update / destroy / edit
	Route::resource('tracks', 'TrackController'); // name = (tracks. index / create / show / update / destroy / edit
	Route::resource('track_comments', 'TrackCommentController'); // name = (track_comments. index / create / show / update / destroy / edit
	Route::resource('song_comments', 'SongCommentController'); // name = (track_comments. index / create / show / update / destroy / edit
	Route::resource('users', 'UserController'); // name = (users. index / create / show / update / destroy / edit

});