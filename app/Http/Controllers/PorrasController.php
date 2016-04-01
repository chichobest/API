<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

use App\Porra;

class PorrasController extends Controller{

    public function __construct(){
        $this->middleware('oauth', ['only' => ['show','addUser','initUsersPorra', 'update','destroy']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $porra = Porra::find($id);
        return $this->respuestaOK($porra, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $porra = Porra::find($id);

        $owner_id = Authorizer::getResourceOwnerId();

        if ($porra->propietario != $owner_id){
            return $this->respuestaError("El usuario conectado no puede modificar esta porra, sólo el propietario puede", 401);
        }

        if ($porra){
            $this->validation($request);

            $porra->nombre = $request->get('nombre');
            $porra->apuesta = $request->get('apuesta');
            $porra->bote = $request->get('bote');
            $porra->vuelta = $request->get('vuelta');
        }
    }

    public function initUsersPorra(Request $request, $id_porra){
        $porra = Porra::find($id_porra);
        $users = json_decode($request->get('users'));        
        $owner_id = Authorizer::getResourceOwnerId();

        $arrayRegs = array();
        if ($porra){
            if ($porra->propietario != $owner_id){
                    return $this->respuestaError("El usuario conectado no puede modificar esta porra, sólo el propietario puede", 401);
            }          
            foreach ($users as $user) {
                $usuario = User::find($user->id);
                if (!$usuario){
                    return $this->respuestaError("No existe el usuario $user->id", 404);
                }
                if (!$porra->getUsuarios()->find($user->id)){
                    $porra->getUsuarios()->attach($user->id);
                    array_push($arrayRegs, $usuario->GCMregister);
                }
            }
            $this->enviarMensajePush($arrayRegs, "Has sido añadido en la porra $porra->nombre", "porra");
            return $this->respuestaOK($porra->getUsuarios, 200);   
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function addUser($id_porra, $id_usuario){
        $porra = Porra::find($id_porra);

        $owner_id = Authorizer::getResourceOwnerId();

        if ($porra){
            if ($porra->propietario != $owner_id){
                return $this->respuestaError("El usuario conectado no puede modificar esta porra, sólo el propietario puede", 401);
            }
            $usuario = User::find($id_usuario);
            if ($usuario){
                if ($porra->getUsuarios()->find($id_usuario)){
                    return $this->respuestaError("El usuario $id_usuario ya pertenece a esta porra", 409);  
                }
                $porra->getUsuarios()->attach($id_usuario);
                return $this->respuestaOK("Usuario añadido correctamente a la porra $id_porra", 200);               
            }
            return $this->respuestaError("No existe el usuario $id_usuario", 404);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $porra = Porra::find($id);

        $owner_id = Authorizer::getResourceOwnerId();
        $propietario_id = Porra::getPropietario()->id();

        if ($id != $owner_id){
            return $propietario_id->respuestaError("El usuario conectado no puede modificar esta porra, sólo el propietario puede", 401);
        }

        if ($porra){
            $porra->getPronosticos()->delete();
            $porra->getUsuarios()->detach();
            $porra->delete();
            return $this->respuestaOK("Porra eliminada correctamente", 200);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function validation ($request){
        $reglas =
        [
            'nombre'=> 'required',
            'apuesta'=> 'required|numeric',
            'bote'=> 'required|numeric',
            'vuelta'=> 'required|numeric',
        ];

        $this->validate($request, $reglas);
    }
}
