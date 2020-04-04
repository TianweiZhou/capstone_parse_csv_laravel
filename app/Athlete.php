<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $table = 'Athletes';
    protected $primaryKey = 'AthleteId';
    protected $fillable = ['ACNum', 'FirstName', 'LastName', 'DOB', 'AthleteGender', 'ClubCode', 'Address', 'City', 'Phone', 'AthleteEmail', 'AthleteSpecialNoteId', 'ClubAffiliationSince'];
    public $timestamps = false;
    public $incrementing = true;
    public static $athleteReportList = ['ERRORTYPE'=>"ATHLETE REPORT;\r\n"];

    //merge code 16 - 23
    public function getAthlete(){
        return $this->hasMany('App\Athlete');
    }

    public function PostAthlete(){

    }
}
