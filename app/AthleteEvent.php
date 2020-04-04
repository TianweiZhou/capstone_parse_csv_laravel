<?php
//merge code 1-16
namespace App;

use Illuminate\Database\Eloquent\Model;

class AthleteEvent extends Model
{
    protected $table = 'AthleteEvents';

    protected $fillable =['name'];

    public function Event(){
        return $this->hasMany('App\AthleteEvent');
    }
}
