<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Http\Request;

abstract class Controller extends BaseController{

    use DispatchesJobs, ValidatesRequests;

    public function respuestaOK($datos, $codigo) {
		return response()->json(['data' => $datos], $codigo);
    }


    public function respuestaCount($datos, $codigo) {
        return response()->json(['count' => sizeof($datos), 'data' => $datos], $codigo);
    }

    public function respuestaError($mensaje, $codigo){
    	return response()->json(['error_description' => $mensaje], $codigo);
    }

    protected function buildFailedValidationResponse(Request $request, array $errors){
    	return $this->respuestaError($errors, 422);
    }
}
