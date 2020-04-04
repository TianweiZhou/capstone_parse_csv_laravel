<?php
//merge code 1-16
namespace App;

use Illuminate\Database\Eloquent\Model;

class AthleteSpecialNote extends Model
{
    protected $table = 'AthleteSpecialNotes';

    protected $fillable =['name'];

    public function member(){
        return $this->hasMany('App\AthleteSpecialNote');
    }
}
