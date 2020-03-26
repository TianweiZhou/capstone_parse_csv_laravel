<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AthletesHistory extends Model
{
    protected $table = 'AthletesHistory';
    protected $fillable = ['AthleteId', 'ClubCodeHistory', 'ClubAffiliationSinceHistory'];
    public $timestamps = false;
    public static $athleteHistoryMessageList = ['ERRORTYPE'=>"ATHLETE HISTORY MESSAGE\r\n"];
}
