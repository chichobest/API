<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Porra;
use Carbon\Carbon;

class UserPorrasController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexMisPorras($id) {
        $usuario = User::find($id);

        if ($usuario){
            $porras = $usuario->getMisPorras;
            return $this->respuestaOK($porras, 200);        
        }   
        return $this->respuestaError("No existe el usuario", 404);

    }

    public function indexPorrasUser($id) {
        $usuario = User::find($id);

        if ($usuario){
            $porras = $usuario->getPorras;
            return $this->respuestaOK($porras, 200);        
        }   
        return $this->respuestaError("No existe el usuario", 404);

    }

    public function indexPorrasTerminadas($id_user){
        $usuario = User::find($id_user);
        date_default_timezone_set('Europe/Madrid');

        if ($usuario){
            $porras = $usuario->getPorras()->where('fecha_fin', '<=', Carbon::now())->get();
            return $this->respuestaOK($porras, 200);   
        }   
        return $this->respuestaError("No existe el usuario", 404);
    }

    public function indexPorrasEnCurso($id_user){
        $usuario = User::find($id_user);
        date_default_timezone_set('Europe/Madrid');

        if ($usuario){
            $porras = $usuario->getPorras()->where('fecha_inicio', '<=', Carbon::now())->where('fecha_fin', '>=', Carbon::now())->get();
            return $this->respuestaOK($porras, 200);   
        }   
        return $this->respuestaError("No existe el usuario", 404);
    }

    public function indexUsersPorra($id){
        $porra = Porra::find($id);
        if ($porra){
            $usuarios = $porra->getUsuarios;
            return $this->respuestaOK($usuarios, 200);
        }
        return $this->respuestaError("No existe el usuario", 404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id_propietario) {
        $usuario = User::find($id_propietario);

        if ($usuario){
            $this->validation($request);

            $campos = $request->all();
            $campos['bote'] = 0;
            $campos['vuelta'] = 1;
            $campos['propietario'] = $id_propietario;

            $porra = Porra::create($campos);
            $porra->getUsuarios()->attach($id_propietario);

            return $this->respuestaOK($porra, 200);
        }
        return $this->respuestaError('El usuario no existe', 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function validation ($request){
        $reglas =
        [
            'nombre' => 'required',
            'apuesta' => 'required|numeric',
        ];

        $this->validate($request, $reglas);
    }
}
