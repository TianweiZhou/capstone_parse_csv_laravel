<?php
//merge code all AppController
namespace App\Http\Controllers\AppController;

use App\Athlete;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AthleteController extends Controller
{
    public function GetAthletes(){
        // $members = 'No Athlete Found';

        if (isset($_GET['name'])){
            if (($_GET['name']) !== '') {
                $members['athletes'] =DB::table('Competitions')
                    ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
                    ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
                    ->join('Clubs', 'Athletes.ClubCode', '=','Clubs.ClubCode')
                    ->orWhere('FirstName', 'LIKE', '%' . $_GET['name'] . '%')
                    ->orWhere('LastName', 'LIKE', '%' . $_GET['name'] . '%')
                    ->select('Athletes.AthleteId','Athletes.ACNum','Athletes.FirstName','Athletes.LastName','Athletes.DOB',
                        'Athletes.AthleteGender','Clubs.ClubCode','Clubs.ClubName',
                        DB::raw("CONCAT('U', (DATEDIFF(YEAR,Athletes.DOB,Competitions.StartDate)+1)) as Role"))
                    ->orderBy('FirstName', 'asc')
                    ->orderBy('LastName', 'asc')
                    ->get();
            }
            else{
                $members['error'] = 'member name is empty';
            }
        }
        if (isset($_GET['ACNum'])){
            if(is_numeric($_GET['ACNum'])){
                $members['athletes'] = DB::table('Competitions')
                    ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
                    ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
                    ->join('Clubs', 'Athletes.ClubCode', '=','Clubs.ClubCode')
                    ->orWhere('Athletes.ACNum', 'LIKE', '%'.$_GET['ACNum'] . '%')
                    ->select('Athletes.AthleteId','Athletes.ACNum','Athletes.FirstName','Athletes.LastName','Athletes.DOB',
                        'Athletes.AthleteGender','Clubs.ClubCode','Clubs.ClubName',
                        DB::raw("CONCAT('U', (DATEDIFF(YEAR,Athletes.DOB,Competitions.StartDate)+1)) as Role"))
                    ->orderBy('ACNum', 'asc')
                    ->get();
            }
            elseif (($_GET['ACNum']) == ''){
                $members['error'] = 'AC Number can not be empty';
            }else{
                $members['error'] = 'AC Number is not valid';
            }
        }
        if (!isset($_GET['name'])&& !isset($_GET['ACNum'])){
            $members['athletes'] = DB::table('Competitions')
                ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
                ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
                ->join('Clubs', 'Athletes.ClubCode', '=','Clubs.ClubCode')
                ->select('Athletes.AthleteId','Athletes.ACNum','Athletes.FirstName','Athletes.LastName','Athletes.DOB',
                    'Athletes.AthleteGender','Clubs.ClubCode','Clubs.ClubName',
                    DB::raw("CONCAT('U', (DATEDIFF(YEAR,Athletes.DOB,Competitions.StartDate)+1)) as Role"))
                ->orderBy('ACNum', 'asc')
                ->get();
        }
        if (isset($_GET['id'])){
            $members['athletes'] = DB::table('Competitions')
                ->join('Results', 'Results.CompId', '=', 'Competitions.CompId')
                ->join('AthleteEvents','Results.EventId','=','AthleteEvents.EventId')
                ->join('Athletes', 'Athletes.AthleteId', '=', 'Results.AthleteId')
                ->join('Clubs', 'Athletes.ClubCode', '=', 'Clubs.ClubCode')
                ->where('Athletes.AthleteId', '=', $_GET['id'])
                ->get();
        }

        return $members;
    }

}
