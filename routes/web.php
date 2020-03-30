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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/terms', 'HomeController@terms')->name('terms');
Route::get('/privacy', 'HomeController@privacy')->name('privacy');
Route::get('/song/{hashed_id}', 'SongController@play')->name('play');
Route::get('/song/{hashed_id}/approve', 'SongController@approve')->name('approve');
Route::get('/song/{hashed_id}/upload', 'SongController@upload')->name('upload');
