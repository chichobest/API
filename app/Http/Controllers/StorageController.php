<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StorageController extends Controller{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
 
       $file = $request->file('profile_picture');
       $nombre = $file->getClientOriginalName();
 
       \Storage::disk('local')->put($nombre, \File::get($file));
 
       return $this->respuestaOK("Archivo guardado", 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($archivo){
        $url = storage_path('/app/'.$archivo);
        if (\Storage::exists($archivo)) {
            return response()->download($url);
        }
        $this->respuestaError("Archivo no encontrado", 404);
    }
}
