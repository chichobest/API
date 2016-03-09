<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Porra extends Model {

    public $timestamps = false;

    protected $fillable = [
        'id', 'nombre', 'apuesta', 'bote', 'vuelta', 'propietario', 'n_jugadores', 'fecha_inicio', 'fecha_fin' 
    ];

    public function getPropietario(){
        return $this->belongsTo('App\User');
    }

    public function getUsuarios(){
        return $this->belongsToMany('App\User');
    }

    public function getPronosticos(){
        return $this->hasMany('App\Pronostico');
    }
}
