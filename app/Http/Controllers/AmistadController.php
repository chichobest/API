<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AmistadController extends Controller{

    public function indexAmigos($id){
        $usuario = User::find($id);
        if ($usuario){
            $amigos = $usuario->getAmigos;
            if ($amigos){
                return $this->respuestaOK($amigos, 200);
            }
            return $this->respuestaError("Este usuario no tiene amigos asociados", 404);
        }
        return $this->respuestaError("No existe el usuario con ID $id", 404);
    }

    public function indexPeticionesAmistad($id){
        $usuario = User::find($id);
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

        $user = User::find($user_id);
        $friend = User::find($friend_id);
        if ($user && $friend){
            if($user->getAmigos->find($friend_id)){
                return $this->respuestaError("Ya existe relación entre $user_id y $friend_id", 409);
            }
            $user->getAmigos()->attach($friend);
            return $this->respuestaOK("Solicitud de amistad enviada correctamente", 200);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }

    public function aceptarAmistad($user_id, $friend_id){
        $user = User::find($user_id);
        $friend = User::find($friend_id);

        if ($user && $friend){
            $amistad1 = $friend->getAmigos()->find($user_id);
            if ($amistad1){
                $friend->getAmigos()->updateExistingPivot($user_id, ['aceptado' => true]);
                $user->getAmigos()->attach($friend_id, ['aceptado' => true]);
                return $this->respuestaOK("Amistad aceptada", 200);     
            }
            return $this->respuestaError("No existe relación de amistad entre los usuarios", 404);
        }
        return $this->respuestaError("Alguno de los usuarios no existe", 404);
    }
}
