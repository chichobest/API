<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class UsersController extends Controller {

    public function __construct(){

        $this->middleware('oauth', ['only' => ['connect','show','update','destroy']]);
    }

    public function connect(){
        return $this->respuestaOK("Connection OK", 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $usuarios = User::all();
        return $this->respuestaOK($usuarios, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede consultar estos datos", 401);
        }

        if ($usuario){
            return $this->respuestaOK($usuario, 200);
        }
        return $this->respuestaError("El usuario no existe", 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validation($request);

        if (User::where('nick', '=', $request->get('nick'))->first()) {
            return $this->respuestaError('El nick ya está en uso', 409);
        }
        $request->input('password', bcrypt($request->get('password')));
        User::create([
                'nick' => $request->get('nick'),
                'password' => bcrypt($request->get('password')),
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'telefono' => $request->get('telefono'),
                'URL_image' => $request->get('URL_image')
            ]);
        return $this->respuestaOK('El usuario se ha creado correctamente', 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede modificar estos datos", 401);
        }

        if ($usuario){
            $this->validation($request);

            $usuario->nick = $request->get('nick');
            $usuario->password = bcrypt($request->get('password'));
            $usuario->name = $request->get('name');
            $usuario->email = $request->get('email');
            $usuario->telefono = $request->get('telefono');
            $usuario->URL_image = $request->get('URL_image');
            
            $usuario->save();
            return $this->respuestaOK("Usuario $usuario->id editado correctamente", 202);
        }
        return $this->respuestaError("El no corresponde a ningún usuario", 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede borrar estos datos", 401);
        }

        if ($usuario){
            if (sizeof($usuario->getMisPorras) > 0){
                return $this->respuestaError('El usuario tiene porras asociadas. Se deben eliminar antes', 409);
            }
            $usuario->getPorras()->detach();
            $usuario->getAmigos()->detach();
            $usuario->delete();
            return $this->respuestaOK("Usuario $usuario->id eliminado", 202);
        }

        return $this->respuestaError("El id no corresponde a ningún usuario", 404);
    }

    public function validation ($request){
        $reglas =
        [
            'nick' => 'required',
            'password' => 'required',
            'name' => 'required',
            'email' => 'required',
            'telefono' => 'required|numeric',
            'URL_image' => 'required',
        ];

        $this->validate($request, $reglas);
    }
}
