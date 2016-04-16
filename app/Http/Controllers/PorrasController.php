<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Pronostico;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

use App\Porra;

class PorrasController extends Controller{

    public function __construct(){
        $this->middleware('oauth', ['only' => ['show','borrarPorra','addUser','removeUser','addPartido','removePartido','initUsersPorra','restartPartidosPorra','update','destroy']]);
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

    public function addUser(Request $request, $id_porra, $id_usuario){
        $porra = Porra::find($id_porra);
        $partidos = json_decode($request->get('partidos'));

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
                foreach ($partidos as $partido) {
                    $campos['partido_id'] = $partido->id;
                    $campos['user_id'] = $id_usuario;
                    $campos['porra_id'] = $id_porra;                
                    $campos['goles_local'] = 0;
                    $campos['goles_visitante'] = 0;
                    $campos['cerrado'] = 0;
                    $pronostico = Pronostico::create($campos);
                }
                $porra->n_jugadores = $porra->n_jugadores + 1;
                $porra->save();
                return $this->respuestaOK("Usuario añadido correctamente a la porra $id_porra", 200);               
            }
            return $this->respuestaError("No existe el usuario $id_usuario", 404);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function removeUser ($id_porra, $id_usuario) {
        $porra = Porra::find($id_porra);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($porra){
            $usuario = User::find($id_usuario);
            if ($usuario){
                if ($porra->propietario != $owner_id){
                    if ($id_usuario != $owner_id)
                        return $this->respuestaError("El usuario conectado no puede modificar esta porra", 401);
                }
                if ($porra->getUsuarios()->find($id_usuario)){
                    $porra->getUsuarios()->detach($id_usuario);
                    $usuario->getPronosticos()->where('porra_id','=', $porra->id)->delete();
                    $porra->n_jugadores = $porra->n_jugadores - 1;
                    $porra->save();
                    return $this->respuestaOK("El usuario $usuario->nick se ha eliminado correctamente", 200);  
                }
                return $this->respuestaOK("El usuario no se ha podido eliminar", 404);               
            }
            return $this->respuestaError("No existe el usuario", 404);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function addPartido (Request $request, $id_porra, $id_partido){
        $porra = Porra::find($id_porra);
        $owner_id = Authorizer::getResourceOwnerId();
        $users = json_decode($request->get('users'));

        if ($porra){
            if ($porra->propietario != $owner_id){
                return $this->respuestaError("El usuario conectado no puede modificar esta porra", 401);
            }
            $partido = $porra->getPronosticos()->where('partido_id', '=', $id_partido)->get();
            if (sizeof($partido)!=0) {
                return $this->respuestaError("El partido ya existe en esta porra", 409);
            }
            foreach ($users as $user) {
                $usuario = User::find($user->id);
                if ($usuario){
                    $campos['partido_id'] = $id_partido;
                    $campos['user_id'] = $user->id;
                    $campos['porra_id'] = $id_porra;
                    $campos['cerrado'] = 0;
                    $pronostico = Pronostico::create($campos);
                }
            }
            return $this->respuestaOK("Partido añadido correctamente", 200);               
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function removePartido (Request $request, $id_porra, $id_partido){
        $porra = Porra::find($id_porra);
        $owner_id = Authorizer::getResourceOwnerId();       
        $users = json_decode($request->get('users'));

        if ($porra){            
            if ($porra->propietario != $owner_id){
                return $this->respuestaError("El usuario conectado no puede modificar esta porra", 401);
            }
            foreach ($users as $user) {
                $usuario = User::find($user->id);
                $usuario->getPronosticos()->where('partido_id', '=', $id_partido)->delete();
            }
            return $this->respuestaOK("Partido eliminado correctamente", 200);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }

    public function restartPartidosPorra (Request $request, $id_porra){
        $porra = Porra::find($id_porra);
        $owner_id = Authorizer::getResourceOwnerId();       
        $users = json_decode($request->get('users'));
        $partidos = json_decode($request->get('partidos'));

        $arrayRegs = array();
        if ($porra){            
            if ($porra->propietario != $owner_id){
                return $this->respuestaError("El usuario conectado no puede modificar esta porra", 401);
            }

            $porra->getPronosticos()->delete();
            $porra->fecha_inicio = $request->get('fecha_inicio');
            $porra->fecha_fin = $request->get('fecha_fin');
            $porra->bote = $request->get('bote');
            $porra->vuelta = $porra->vuelta+1;
            $porra->save();
            foreach ($users as $user) {
                $usuario = User::find($user->id);
                foreach ($partidos as $partido) {
                    if (!$usuario || !$porra){
                        return $this->respuestaError("Error al crear los pronosticos");
                    }
                    $campos['partido_id'] = $partido->id;
                    $campos['user_id'] = $user->id;
                    $campos['porra_id'] = $id_porra;
                    $campos['cerrado'] = 0;
                    $pronostico = Pronostico::create($campos);
                }
                if ($usuario->id!=$owner_id)
                    array_push($arrayRegs, $usuario->GCMregister);
                $porra->getUsuarios()->updateExistingPivot($user->id, ['pagado' => 0]);
            }

            $this->enviarMensajePush($arrayRegs, "Has sido añadido a la porra $porra->nombre", "porra");
            return $this->respuestaOK("Porra reiniciada correctamente", 200);
        }
        return $this->respuestaError("No existe la porra $id_porra", 404);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function borrarPorra($id) {
        $porra = Porra::find($id);

        $owner_id = Authorizer::getResourceOwnerId();

        if ($porra->propietario != $owner_id){
            return $this->respuestaError("El usuario conectado no puede borrar esta porra", 401);
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
