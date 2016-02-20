<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pronostico extends Model {

    protected $fillable = ['id', 'id_partido', 'goles_local', 'goles_visitante'];

    protected $hidden = ['id', 'created_at', 'uptadet_at'];

    public function usuario(){
        return $this->belongsTo('App\Usuario');
    }

    public function porra(){
        return $this->belongsTo('App\Porra');
    }   
}