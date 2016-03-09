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

//Gestión de Usuarios
Route::resource('user', 'UsersController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
Route::get('/connect', 'UsersController@connect');

//Gestion de porras
Route::resource('porra', 'PorrasController', ['only' => ['show', 'update', 'destroy']]);
Route::post('/porra/{id_porra}/user/{id_user}','PorrasController@addUser');
Route::post('/porra/{id_porra}/init','PorrasController@initUsersPorra');

Route::post('/porra/{id_propietario}', 'UserPorrasController@store');
//Route::resource('pronostico', 'PronosticosController');
//Route::resource('user.porra', 'UserPorrasController');

Route::get('/user/{id_user}/porra','UserPorrasController@indexMisPorras');
Route::get('/user/porra/{id_user}','UserPorrasController@indexPorrasUser');
Route::get('porra/{id_porra}/user','UserPorrasController@indexUsersPorra');


Route::get('/pronostico/user/{id_user}/porra/{id_porra}','PronosticosController@show');
Route::get('/pronostico/{id_porra}','PronosticosController@index');
Route::post('/pronostico', 'PronosticosController@store');

//Gestion de Amistad
Route::get('/user/{user_id}/user', 'AmistadController@indexAmigos');
Route::get('/user/{user_id}/user/all', 'AmistadController@indexAllAmigos');
Route::get('/user/user/{user_id}', 'AmistadController@indexPeticionesAmistad');
Route::post('/user/{user_id}/user/{friend_id}', 'AmistadController@enviarPeticionAmistad');
Route::put('/user/{user_id}/user/{friend_id}', 'AmistadController@aceptarAmistad');

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

//Gestión de fotografías
Route::post('/storage', 'StorageController@store');
Route::get('/storage/{archivo}', 'StorageController@show');
