<?php

namespace App\Http\Controllers\AppController;

use App\AthleteEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AthleteEventController extends Controller
{
    //no futher filter requires to filter eventnames.
    //this returns all the event names
    public function GetEventNames(){
        $eventNames = DB::table('Competitions')
            ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
            ->join('AthleteEvents','Results.EventId','=','AthleteEvents.EventId')
            ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId');
        if (isset($_GET['gender'])) {
            $eventNames->where('Athletes.AthleteGender', '=', $_GET['gender']); // F or M
        }if (isset($_GET['age'])){ // 20, 19, 18, 17,
            if($_GET['age'] == 'open'){ // open categories for > 20 year
                $eventNames->where(DB::raw('(DATEDIFF(YEAR,Athletes.DOB,Competitions.StartDate)+1)'),'>', $_GET['age']);
            }else{
                $eventNames->where(DB::raw('(DATEDIFF(YEAR,Athletes.DOB,Competitions.StartDate)+1)'),'=', $_GET['age']);
            }
        }if (isset($_GET['type'])){  // accepts o or i
            $eventNames->where('Competitions.Season','=', $_GET['type']);
        }if (isset($_GET['year'])){ // eg. 2019
            $eventNames->Where('Competitions.StartDate', 'like',  $_GET['year'] . '%');
        }
        $query = $eventNames->distinct()->get('AthleteEvents.EventName');

        return $query;

    }
}
