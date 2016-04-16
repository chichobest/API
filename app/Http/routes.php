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
Route::put('/user/{id}/GCMupdate', 'UsersController@refreshGCM');
Route::get('/connect', 'UsersController@connect');

//Gestion de porras
Route::get('/porra/{id_porra}', 'PorrasController@show');
Route::post('/porra/{id_porra}/user/{id_user}','PorrasController@addUser');
Route::post('/porra/{id_porra}/partido/{id_partido}', 'PorrasController@addPartido');
Route::post('/porra/{id_porra}/init','PorrasController@initUsersPorra');
Route::put('/porra/{id_porra}', 'PorrasController@restartPartidosPorra');
Route::patch('/porra/{id_porra}', 'PorrasController@borrarPorra');
Route::patch('/porra/{id_porra}/user/{id_user}', 'PorrasController@removeUser');
Route::patch('/porra/{id_porra}/partido/{id_partido}', 'PorrasController@removePartido');

Route::get('/user/{id_user}/porra','UserPorrasController@indexMisPorras');
Route::get('/user/porra/{id_user}','UserPorrasController@indexPorrasUser');
Route::get('/user/porra/{id_user}/inprogress','UserPorrasController@indexPorrasEnCurso');
Route::get('/user/porra/{id_user}/finished','UserPorrasController@indexPorrasTerminadas');
Route::get('/porra/{id_porra}/user','UserPorrasController@indexUsersPorra');
Route::post('/porra/{id_propietario}', 'UserPorrasController@store');

//Gestion pronósticos
Route::get('/pronostico/user/{id_user}/porra/{id_porra}','PronosticosController@show');
Route::get('/pronostico/{id_porra}','PronosticosController@index');
Route::post('/pronostico', 'PronosticosController@store');
Route::put('/pronostico/{id_porra}/user/{id_user}', 'PronosticosController@update');

//Gestion de Amistad
Route::get('/user/{user_id}/user', 'AmistadController@indexAmigos');
Route::get('/user/{user_id}/user/all', 'AmistadController@indexAllAmigos');
Route::get('/user/user/{user_id}', 'AmistadController@indexPeticionesAmistad');
Route::post('/user/{user_id}/user/{friend_id}', 'AmistadController@enviarPeticionAmistad');
Route::put('/user/{user_id}/user/{friend_id}', 'AmistadController@aceptarAmistad');
Route::put('/user/{user_id}/user/{friend_id}/rechazar', 'AmistadController@rechazarAmistad');
Route::patch('/user/{user_id}/user/{friend_id}', 'AmistadController@eliminarPeticion');

Route::post('/GCMsend', 'UsersController@enviarMensaje');

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

//Gestión de fotografías
Route::post('/storage', 'StorageController@store');
Route::get('/storage/{archivo}', 'StorageController@show');
