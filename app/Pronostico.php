<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pronostico extends Model {

    protected $fillable = ['id','partido_id', 'user_id', 'porra_id', 'goles_local', 'goles_visitante', 'cerrado'];

    protected $hidden = ['created_at', 'updated_at'];

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function porra(){
        return $this->belongsTo('App\Porra');
    }
}