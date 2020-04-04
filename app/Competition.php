<?php

//merge-code 1-17
namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $table = 'competitions';

    protected $fillable =['name'];

    public function Competitions(){
        return $this->hasMany('App\Competition');
    }
}
