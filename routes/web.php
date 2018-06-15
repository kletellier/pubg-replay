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
*/

Route::get('/', 'Home@index'); 
Route::post('/player', 'Home@player');
Route::get('/test', 'Home@test');
Route::get('/itemicon/{id}','Image@itemicon');
Route::get('/maplowres/{id}','Image@maplowres');
Route::get('/json/path/{id}/{shards}/{user}','Json@path');
Route::get('/replay/{id}/{shards}/{user}','Home@replay');
