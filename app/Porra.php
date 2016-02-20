<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Porra extends Model {

    protected $fillable = [
        'id', 'nombre', 'apuesta', 'bote', 'vuelta', 'propietario',
    ];

    protected $hidden = [
        'id', 'created_at', 'uptadet_at'
    ];

    public function getPropietario(){
        return $this->belongsTo('App\Usuario');
    }

    public function getUsuarios(){
        return $this->belongsToMany('App\Usuario');
    }

    public function getPronosticos(){
        return $this->hasMany('App\Pronostico');
    }
}
