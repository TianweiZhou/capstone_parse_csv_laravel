<?php
//merge code 1-17
namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable =['name'];

    public function member(){
        return $this->hasMany('App\Result');
    }
}
