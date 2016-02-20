<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'nick', 'name', 'email', 'telefono', 'URL_image', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'created_at', 'updated_at'];

    public function getMisPorras(){
        return $this->hasMany('App\Porra', 'propietario');
    }

    public function getAmigos(){
        return $this->belongsToMany('App\User', 'amistades' ,'user_id', 'friend_id')->withPivot('aceptado');
    }

    public function getPeticionesAmistad(){
        return $this->belongsToMany('App\User', 'amistades' ,'friend_id', 'user_id')->withPivot('aceptado');
    }

    public function getPorras(){
        return $this->belongsToMany('App\Porra')->withPivot('pagado');
    }

    public function getPronosticos(){
        return $this->hasMany('App\Pronostico');
    }
}
