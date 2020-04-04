<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $table = 'Clubs';
    protected $primaryKey = 'ClubCode';
    protected $fillable = ['ClubCode', 'ClubName'];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    public static $clubReportList = ['ERRORTYPE'=>"CLUB REPORT\r\n"];

    //merge code 18-20
    public function member(){
        return $this->hasMany('App\Club');
    }
}
