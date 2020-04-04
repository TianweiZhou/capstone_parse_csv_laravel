<?php

namespace App\Http\Controllers\AppController;

use App\Athlete;
use App\Club;
use App\Competition;
use App\Http\Controllers\Controller;
use App\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use function MongoDB\BSON\toJSON;
use function Sodium\add;

class ChainController extends Controller
{
    public function GetRanking(){
        $users = DB::table('Competitions')
            ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
            ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
            ->select('Athletes.AthleteId', 'Athletes.FirstName', 'Athletes.LastName', 'Athletes.ClubCode','Competitions.StartDate')
            ->get();

        return $users;
    }

    public function getResult(){

        $result = DB::table('Competitions')
            ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
            ->join('AthleteEvents','Results.EventId','=','AthleteEvents.EventId')
            ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
            ->join('Clubs', 'Athletes.ClubCode', '=', 'Clubs.ClubCode');

        if (isset($_GET['season'])){
            $result->where('Competitions.Season', $_GET['season']);
        }if (isset($_GET['gender'])) {
            $result->where('Athletes.AthleteGender', $_GET['gender']);
        }if (isset($_GET['division'])){
            if($_GET['division'] == 'R'){
                $result->whereIn('AthleteEvents.EventDivision', array('U12','U14','U16','U18','U20', 'Open'));
            }if($_GET['division'] == 'M'){
                $result->where('AthleteEvents.EventDivision','LIKE', '%Masters%');
            }if($_GET['division'] == 'A'){
                $result->where('AthleteEvents.EventDivision','Ambulatory');
            }if($_GET['division'] == 'W'){
                $result->where('AthleteEvents.EventDivision','Wheelchair');
            }if($_GET['division'] == 'P'){
                $result->where('AthleteEvents.EventDivision','Para');
            }
        }if (isset($_GET['ageGroup'])){
            $result->whereIn(DB::raw('(DATEDIFF(YEAR, Athletes.DOB, Competitions.StartDate)+1)') , array($_GET['ageGroup']));
        }if (isset($_GET['year'])){
            $result->Where(DB::raw('DATEPART(year, Competitions.StartDate)'),  $_GET['year'] );
        }if (isset($_GET['name'])){
            $result->Where('AthleteEvents.Eventname',  $_GET['name']);
        }

        $query = $result->select( 'Athletes.AthleteId', 'Athletes.FirstName', 'Athletes.LastName',
            'Clubs.ClubName','Competitions.CompName','Competitions.StartDate','Athletes.DOB',
            DB::raw("'u' + CAST(DATEDIFF(YEAR, DOB, StartDate)+1 as varchar) as age"),
            'AthleteEvents.EventRound','Results.Position','Results.Mark','Results.Wind',
            'Athletes.AthleteGender')->distinct()->get();

        return $query;
    }

    public function GetFilerValue(){

//        try{
//
//        }catch {
//
//        }
        $result1['division'] = DB::table('AthleteEvents')
            ->orWhere('EventDivision', 'not like', '%'.'Masters'. '%')
            ->distinct()
            ->get(['EventDivision']);
        $result2['year'] = DB::table('Competitions')->distinct()->get([DB::raw('YEAR(Startdate) as year')]);

        $finalResult[] = array_merge($result1, $result2);

        return $finalResult;
    }

    public function getDivision(){

        $result = DB::table('Athletes')
            ->join('Results', 'Results.AthleteId', '=', 'Athletes.AthleteId')
            ->join('AthleteEvents','Results.EventId','=','AthleteEvents.EventId');
        if(isset($_GET['age'])){
            $result->where(DB::raw('(DATEDIFF(YEAR, Athletes.DOB, GETDATE())) +1'),'=', $_GET['age'] );
        }

        $query = $result->distinct()->get('EventDivision');

        return $query;
    }

    public function getPersonalInfo(){

        if(isset($_GET['id'])) {
            try {
                $result = DB::table('Athletes')
                    ->leftJoin('AthleteSpecialNotes', 'Athletes.AthleteSpecialNoteId', '=', 'AthleteSpecialNotes.AthleteSpecialNoteId')
                    ->join('Clubs', 'Clubs.ClubCode', '=', 'Athletes.ClubCode')
                    ->where('Athletes.athleteid', $_GET['id'])
                    ->select('Athletes.athleteid', 'Athletes.ACNum', 'athletes.FirstName', 'athletes.LastName', 'athletes.dob',
                        'athletes.AthleteGender', 'athletes.ClubCode', 'Clubs.ClubName',
                        DB::raw("'U'+CAST(DATEDIFF(YY,DOB,GETDATE())+1 as VARCHAR) as ROLE"), 'athletes.Phone', 'athletes.Address',
                        DB::raw("ISNULL(NOte,'') AS Note"))
                    ->get();
                return $result;

            }catch(Exception $e){
                $message = $e->getMessage();
                return $message;
            }
        }else{
            return $error['error'] = 'no id found';
        }
    }
    public function getProgress(){
        $id = $_GET['id'];
        $eventPrograss['Events'] = $id;

        if(isset($id)){

            $eventid = DB::table('viewAthDetailsBySubRole')
                ->where('AthleteId', $id)
                ->distinct()
                ->get('EventName');

            foreach ($eventid as $eid){

                $eventNames['eventnames'] =  DB::table('viewAthDetailsBySubRole')
                    ->where('AthleteId', $id)
                    ->where('EventName', $eid->EventName)
                    ->where('mark','NOT LIKE', '%[A-Z]%')
                    ->select('EventName',  'Season'  )
                    ->distinct()
                    ->get();
                //->first(1);
                $eventYear = DB::table('viewAthDetailsBySubRole')
                    ->where('AthleteId', $id)
                    ->where('EventName', $eid->EventName)
                    ->distinct()
                    ->get('compyear as y');

                // $test['year'] = [];

                foreach ($eventYear as $year){
                    $event['year']  =  DB::table('viewAthDetailsBySubRole')
                        ->where('AthleteId', $id)
                        ->where('EventName', $eid->EventName)
                        ->where('Compyear', $year->y)
                        ->where('mark','NOT LIKE', '%[A-Z]%')
                        ->select('Compyear',
                            'mark',
                            'compname',
                            'EventRound',
                            'Position',
                            'wind',
                            'StartDate')
                        ->orderByDesc('mark')
                        ->distinct()
                        ->first(1);

                    // $testmerge =   array_merge($test, $event);
                    $fullEvent = array_merge($eventNames, $event);
                    array_push($eventPrograss, $fullEvent);
                }
            }
        }else{
            $eventPrograss = 'id is mandatory field';
        }
        return $eventPrograss;
    }

    public function getBest(){
        $id = $_GET['id'];
        $eventPrograss['Events'] = $id;
        if(isset($id)){
            $eventid = DB::table('viewAthDetailsBySubRole')
                ->where('AthleteId', $id)
                ->distinct()
                ->get('Season');

            foreach ($eventid as $eid){

                $events =  DB::table('viewAthDetailsBySubRole')
                    ->where('AthleteId', $id)
                    ->where('Season', $eid->Season)
                    ->distinct()
                    ->get('Eventname as name');

                foreach($events as $event){
                    $eventbySeason[$eid->Season] =  DB::table('viewAthDetailsBySubRole')
                        ->where('AthleteId', $id)
                        ->where('Season', $eid->Season)
                        ->where('EventName',  $event->name)
                        ->where('mark','NOT LIKE', '%[A-Z]%')
                        ->select('Eventname',
                            'mark',
                            'wind',
                            'compname',
                            'startdate',
                            'eventround',
                            'position',
                            'season')
                        ->orderByDesc('mark')
                        ->distinct()
                        ->paginate(1);
                }
            }
            array_push($eventPrograss, $eventbySeason);
        }else{
            $eventPrograss = 'id is mandatory field';
        }
        return $eventPrograss;
    }

    public function getReasultbyYear(){
        $eventPrograss['Events'] = $_GET['id'];
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            if (isset( $_GET['year'])){
                $yr = $_GET['year'];
//                $eventyear = DB::table('viewAthDetailsBySubRole')
//                    ->where('AthleteId', $id)
//                    ->select(DB::raw('DATEPART(year, StartDate) as year'))
//                    ->distinct()
//                    ->get();

//                foreach ($eventyear as $years){

                $event[$yr]  =  DB::table('viewAthDetailsBySubRole')
                    ->where('AthleteId', $id)
                    ->where('startdate','LIKE', $yr.'%')
                    ->select('Eventname',
                        'mark',
                        'wind',
                        'compname',
                        'startdate',
                        'eventround',
                        'position',
                        'season')
                    ->distinct()
                    ->get();
//                }
                array_push($eventPrograss, $event);
            }else{
                $eventPrograss = 'no year found';
            }
        }else{
            $eventPrograss = 'no id found';
        }
        return $eventPrograss;
    }

    public function oneBestResult(){
        $eventPrograss['Events'] = $_GET['id'];
        if(isset($_GET['id'])){
            $id = $_GET['id'];

            $eventyear = DB::table('viewAthDetailsBySubRole')
                ->where('AthleteId', $id)
                ->select(DB::raw('DATEPART(year, StartDate) as year'))
                ->distinct()
                ->get();

            foreach ($eventyear as $years){

                $event[$years->year]  =  DB::table('viewAthDetailsBySubRole')
                    ->where('AthleteId', $id)
                    ->where('startdate','LIKE', $years->year.'%')
                    ->where('mark','NOT LIKE', '%[A-Z]%')
                    //->take(1)
                    ->select('Eventname',
                        'mark',
                        'wind',
                        'compname',
                        'startdate',
                        'eventround',
                        'position',
                        'season')
                    ->orderByDesc('mark')
                    ->distinct()
                    ->paginate(1);
            }
            array_push($eventPrograss, $event);

        }else{
            $eventPrograss = 'no id found';
        }
        return $eventPrograss;
    }
}
