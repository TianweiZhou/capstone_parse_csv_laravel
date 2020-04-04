<?php

namespace App\Http\Controllers\AppController;

use App\Competition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class CompetitionController extends Controller
{
    public function GetFilterCompetitions(){

        if (isset($_GET['id'])){
            try{
                $competition  = DB::table('Competitions')
                    ->where('CompId', $_GET['id'])
                    ->get();
                if($competition == null){
                    $competition = 'no competition found';
                }
            }catch(Exception $e){
                $message = $e->getMessage();
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        }
        if(isset($_GET['name'])){
            $competition['competitions'] = DB::table('Competitions')
                ->where('CompName','LIKE', '%'.$_GET['name'].'%' )
                ->select('CompId', 'CompName', 'CompSubName', 'StartDate', 'EndDate', 'Facility', 'Location',
                    'CompType', DB::raw('DATEDIFF(DAY, StartDate,EndDate) as Eventdays'), 'CompSubType', 'Season')
                ->get();
        }
        if(isset($_GET['subname'])){
            $competition['competitions'] = DB::table('Competitions')
                ->where('CompSubName','LIKE', '%'.$_GET['subname'].'%' )
                ->select('CompId', 'CompName', 'CompSubName', 'StartDate', 'EndDate', 'Facility', 'Location',
                    'CompType', DB::raw('DATEDIFF(DAY, StartDate,EndDate) as Eventdays'), 'CompSubType', 'Season')
                ->get();
        }
        if(isset($_GET['meettype'])){
            $competition['competitions'] = DB::table('Competitions')
                ->where('CompSubType',$_GET['meettype'] )
                ->select('CompId', 'CompName', 'CompSubName', 'StartDate', 'EndDate', 'Facility', 'Location',
                    'CompType', DB::raw('DATEDIFF(DAY, StartDate,EndDate) as Eventdays'), 'CompSubType', 'Season')
                ->get();
        }


        return $competition;
    }

    public function GetCompetitions(){
        try{
            $competition['competitions'] = DB::table('Competitions')
                ->select('CompId', 'CompName', 'CompSubName', 'StartDate', 'EndDate', 'Facility', 'Location',
                    'CompType', DB::raw('DATEDIFF(DAY, StartDate,EndDate) as Eventdays'), 'CompSubType', 'Season')
                ->get();
            return $competition;
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function getYears(){
        $years['years'] = DB::table('Competitions')
            ->select(DB::raw('DATEPART(year, StartDate) as CompetitionYear'))
            ->distinct()
            ->get();
        return $years;
    }

    public function getOneCompetitionByID($id){

    }
    public function AddCompetitions(){
        $cname = $_POST['Name'];
        $subname = '';
        $location = '';
        $meetType = '';
        $meetSubType = '';
        $season = '';
        $startDay   = '';
        $endDay = '';

        if (!empty($cname)){
            if (isset($_POST['Subname'])){
                $subname = $_POST['Subname'];
            }
            if (isset($_POST['Facility'])){
                $facility = $_POST['Facility'];
            }
            if (isset($_POST['Location'])){
                $location = $_POST['Location'];
            }
            if (isset($_POST['MeetType'])){
                $meetType = $_POST['MeetType'];
            }
            if (isset($_POST['MeetSubType'])){
                $meetSubType = $_POST['MeetSubType'];
            }
            if (isset($_POST['Season'])){
                $season = $_POST['Season'];
            }
            if (isset($_POST['StartDate'])){
                $startDay = $_POST['StartDate'];
            }
            if (isset($_POST['EndDate'])){
                $endDay = $_POST['EndDate'];
            }

            try{
                DB::table('Competitions')
                    ->insert(
                        [
                            'CompName' => $_POST['Name'],
                            'CompSubName' => $subname,
                            'StartDate' => $startDay,
                            'EndDate' => $endDay,
                            'Facility' => $facility,
                            'Location' => $location,
                            'CompType' => $meetType,
                            'CompSubType' => $meetSubType,
                            'Season' => $season
                        ]
                    );
                $message = 'Record Added Successfully';

                return response($message, 200)
                    ->header('Content-Type', 'text/plain');
            }catch (\Exception   $e){
                $message = "the Competition name is already exist in database";

                return response($message, 400)
                    ->header('Content-Type', 'text/plain');
            }
        }else {
            //send responce code 500
            $message = 'Name is required field';

            return response($message, 400)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function UpdateCompetitions($id){
        $cname = $_POST['Name'];
        $subname = '';
        $location = '';
        $meetType = '';
        $meetSubType = '';
        $season = '';
        $startDay   = '';
        $endDay = '';

        if (!empty($cname)){
            if (isset($_POST['SubName'])){
                $subname = $_POST['SubName'];
            }
            if (isset($_POST['Facility'])){
                $facility = $_POST['Facility'];
            }
            if (isset($_POST['Location'])){
                $location = $_POST['Location'];
            }
            if (isset($_POST['MeetType'])){
                $meetType = $_POST['MeetType'];
            }
            if (isset($_POST['MeetSubType'])){
                $meetSubType = $_POST['MeetSubType'];
            }
            if (isset($_POST['Season'])){
                $season = $_POST['Season'];
            }
            if (isset($_POST['StartDate'])){
                $startDay = $_POST['StartDate'];
            }
            if (isset($_POST['EndDate'])){
                $endDay = $_POST['EndDate'];
            }

            try{
                DB::table('Competitions')
                    ->where('CompId', $_POST['id'])
                    ->update(
                        [
                            'CompName' => $_POST['Name'],
                            'CompSubName' => $subname,
                            'StartDate' => $startDay,
                            'EndDate' => $endDay,
                            'Facility' => $facility,
                            'Location' => $location,
                            'CompType' => $meetType,
                            'CompSubType' => $meetSubType,
                            'Season' => $season
                        ]
                    );
                $message = 'Record updated Successfully';
                return response($message, 200)
                    ->header('Content-Type', 'text/plain');
            }catch (Exception   $e){
                $message = "the Competition name is already exist in database";
                return response($message, 400)
                    ->header('Content-Type', 'text/plain');
            }
        }else {
            $message = 'Name is required field';
            return response($message, 400)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function DeleteCompetitions(){

        try{
            DB::table('Competitions')
                ->where('CompId', $_POST['id'])
                ->delete();

            $message = "Competition deleted successfully";
            return response($message, 200)
                ->header('Content-Type', 'text/plain');
        }catch (Exception $e){
            $message = $e->getMessage();
            return response($message, 400)
                ->header('Content-Type', 'text/plain');
        }

    }

}
