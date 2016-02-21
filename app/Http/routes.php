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

Route::resource('user', 'UsersController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
//Route::resource('porra', 'PorrasController');
//Route::resource('pronostico', 'PronosticosController');
//Route::resource('user.porra', 'UserPorrasController');

Route::get('/user/{user_id}/user', 'AmistadController@indexAmigos');
Route::get('/user/user/{user_id}', 'AmistadController@indexPeticionesAmistad');
Route::post('/user/{user_id}/user/{friend_id}', 'AmistadController@enviarPeticionAmistad');
Route::put('/user/{user_id}/user/{friend_id}', 'AmistadController@aceptarAmistad');

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});
Route::patch('/self', 'UsersController@self');
