<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

Route::get('/', function () {
    return view('welcome');
});
*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('index');
Route::get('/terms', 'HomeController@terms')->name('terms');
Route::get('/privacy', 'HomeController@privacy')->name('privacy');

Route::get('/song/{hashed_id}', 'SongController@play')->name('play');
Route::get('/song/{hashed_id}/approve', 'SongController@approve')->name('approve');
Route::get('/song/{hashed_id}/unapprove', 'SongController@unapprove')->name('unapprove');
Route::get('/song/{hashed_id}/feature', 'SongController@feature')->name('feature');
Route::get('/song/{hashed_id}/unfeature', 'SongController@feature')->name('unfeature');
Route::get('/song/{hashed_id}/publish', 'SongController@publish')->name('publish');
Route::get('/song/{hashed_id}/tweet', 'SongController@tweet')->name('tweet');
Route::get('/song/{hashed_id}/build', 'SongController@buildVideo');
