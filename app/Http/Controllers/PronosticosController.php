<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pronostico;
use App\User;
use App\Porra;
use DB;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class PronosticosController extends Controller{


    public function __construct(){
        $this->middleware('oauth', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id_porra){
        $porra = Porra::find($id_porra);
        if ($porra){
            $partidos = DB::select('select distinct partido_id from pronosticos where porra_id = ?', [$id_porra]);
            $pronosticos = $porra->getPronosticos->groupBy('user_id');
            $users = $porra->getUsuarios; 

            return response()->json(['partidos' => $partidos , 'usuarios' => $users, 'user_pronosticos' => $pronosticos], 200);
        }
    }

    public function indexAll($id_porra){
        $porra = Porra::distinct()->select('partido_id')->groupBy('user_id')->get();
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
    public function store(Request $request){
        $users = json_decode($request->get('users'));
        $partidos = json_decode($request->get('partidos'));

        $owner_id = Authorizer::getResourceOwnerId();
        $porra = Porra::find($request->get('id_porra'));

        if ($porra->propietario != $owner_id){
            return $this->respuestaError("El usuario conectado no puede modificar esta porra, sólo el propietario puede", 401);
        }

        foreach ($users as $user) {
            foreach ($partidos as $partido) {
                $usuario = User::find($user->id);
                if (!$usuario || !$porra){
                    return $this->respuestaError("Error al crear los pronosticos");
                }
                $campos['partido_id'] = $partido->id;
                $campos['user_id'] = $user->id;
                $campos['porra_id'] = $request->get('id_porra');
                $pronostico = Pronostico::create($campos);
            }
        }
        foreach ($partidos as $partido) {
            $campos['partido_id'] = $partido->id;
            $campos['user_id'] = $owner_id;
            $campos['porra_id'] = $request->get('id_porra');                
            $campos['goles_local'] = 0;
            $campos['goles_visitante'] = 0;
            $pronostico = Pronostico::create($campos);
        }
        return $this->respuestaOK($porra->getPronosticos, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_user, $id_porra){
        $user = User::find($id_user);

        $pronosticos = $user->getPronosticos()->where('porra_id', $id_porra)->get();

        return $this->respuestaOK($pronosticos, 200);
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
}
