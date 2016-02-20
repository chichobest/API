<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::resource('user', 'UsersController');
Route::resource('porra', 'PorrasController');
Route::resource('pronostico', 'PronosticosController');
Route::resource('user.user', 'AmistadController');
Route::resource('user.porra', 'UserPorrasController');
