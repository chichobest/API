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

    public function enviarMensajePush($regs, $msg){
        define("GOOGLE_API_KEY", "AIzaSyAkNJ86_4GmtHTnz6PXN4vjd3ryaOpoc5U");
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $regs,
            'data' => array('message' => $msg),
            'delay_while_idle' => false,
        );
 
        $headers = array(
            'Authorization: key=AIzaSyAkNJ86_4GmtHTnz6PXN4vjd3ryaOpoc5U',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        echo $result;
    }
}
