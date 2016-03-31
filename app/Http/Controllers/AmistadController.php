<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class AmistadController extends Controller{

    public function __construct(){

        $this->middleware('oauth', ['only' => ['indexAmigos','indexAllAmigos','indexPeticionesAmistad','enviarPeticionAmistad','aceptarAmistad', 'rechazarAmistad','eliminarPeticion']]);
    }

    public function indexAllAmigos($id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede consultar esta lista de amigos", 401);
        }

        if ($usuario){
            $amigos = $usuario->getAmigos()->orderby('nick','asc')->get();
            if ($amigos){
                return $this->respuestaOK($amigos, 200);
            }
            return $this->respuestaError("Este usuario no tiene amigos asociados", 404);
        }
        return $this->respuestaError("No existe el usuario con ID $id", 404);
    }

    public function indexAmigos($id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede consultar esta lista de amigos", 401);
        }

        if ($usuario){
            $amigos = $usuario->getAmigos()->orderby('nick','asc')->wherePivot('aceptado', 1)->get();
            if ($amigos){
                return $this->respuestaOK($amigos, 200);
            }
            return $this->respuestaError("Este usuario no tiene amigos asociados", 404);
        }
        return $this->respuestaError("No existe el usuario con ID $id", 404);
    }


    public function indexPeticionesAmistad($id){
        $usuario = User::find($id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede consultar las peticiones de amistad", 401);
        }
        if ($usuario){
            $amigos = $usuario->getPeticionesAmistad()->wherePivot('aceptado', false)->get();
            if ($amigos){
                return $this->respuestaOK($amigos, 200);
            }
            return $this->respuestaError("Este usuario no tiene amigos asociados", 404);
        }
        return $this->respuestaError("No existe el usuario con ID $id", 404);
    }

    public function enviarPeticionAmistad($user_id, $friend_id){
        if ($user_id == $friend_id){
            return $this->respuestaError("Los usuarios no pueden ser el mismo", 400);
        }

        $owner_id = Authorizer::getResourceOwnerId();

        if ($user_id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede enviar esta peticion", 401);
        }

        $user = User::find($user_id);
        $friend = User::find($friend_id);
        if ($user && $friend){
            if($user->getAmigos->find($friend_id)){
                return $this->respuestaError("Ya existe relación entre $user_id y $friend_id", 409);
            }
            if ($user->getPeticionesAmistad->find($friend_id)){
                return $this->aceptarAmistad($user_id,$friend_id);
            }
            $user->getAmigos()->attach($friend);
            $this->enviarMensajePush($arrayRegs, "Has recibido una peticion de amistad de $user->nick");
            return $this->respuestaOK("Solicitud de amistad enviada correctamente", 200);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }

    public function aceptarAmistad($user_id, $friend_id){
        $user = User::find($user_id);
        $friend = User::find($friend_id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($user_id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede aceptar esta amistad", 401);
        }

        if ($user && $friend){
            $amistad = $friend->getAmigos()->find($user_id);
            if ($amistad){
                $friend->getAmigos()->updateExistingPivot($user_id, ['aceptado' => true]);
                $user->getAmigos()->attach($friend_id, ['aceptado' => true]);
                return $this->respuestaOK("Amistad aceptada", 200);     
            }
            return $this->respuestaError("No existe relación de amistad entre los usuarios", 404);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }

    public function rechazarAmistad($user_id, $friend_id){
        $user = User::find($user_id);
        $friend = User::find($friend_id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($user_id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede rechazar esta amistad", 401);
        }

        if ($user && $friend){
            $amistad = $friend->getAmigos()->find($user_id);
            if ($amistad){
                $friend->getAmigos()->updateExistingPivot($user_id, ['aceptado' => -1]);
                return $this->respuestaOK("Amistad rechazada", 200);    
            }
            return $this->respuestaError("No existe relación de amistad entre los usuarios", 404);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }

    public function eliminarPeticion($user_id, $friend_id){
        $user = User::find($user_id);
        $friend = User::find($friend_id);
        $owner_id = Authorizer::getResourceOwnerId();

        if ($user_id != $owner_id){
            return $this->respuestaError("El usuario conectado no puede aceptar esta amistad", 401);
        }
        if ($user && $friend){
            $amistad = $friend->getPeticionesAmistad()->find($user_id);
            if ($amistad){
                $user->getAmigos()->detach($friend_id);
                return $this->respuestaOK("Amistad borrada", 200);     
            }
            return $this->respuestaError("No existe relación de amistad entre los usuarios", 404);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }
}
