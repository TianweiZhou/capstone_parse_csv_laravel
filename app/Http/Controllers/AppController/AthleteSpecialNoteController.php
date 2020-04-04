<?php

namespace App\Http\Controllers\AppController;

use App\Athlete;
use App\AthleteSpecialNote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AthleteSpecialNoteController extends Controller
{
    public function GetAthleteSpecialNotes(){
        $notes = AthleteSpecialNote::all();
        return $notes;
    }
    public function addNote(){
        try{
            // $nid = $_POST['nid'] ;
            $athleteid = $_POST['id'];
            $note = $_POST['Note'];

            if(isset($athleteid)) {
                $athlete = DB::table('Athletes')->where('AthleteId', $athleteid)->get();

                foreach ($athlete as $a) {
                    echo "<script type='text/javascript'>alert('$a->AthleteSpecialNoteId');</script>";

                    if (($a->AthleteSpecialNoteId) === null) {
                        $noteid = mt_rand();

                        //inserting into AthleteSpecialNote table with random id
                        DB::unprepared('SET IDENTITY_INSERT AthleteSpecialNotes ON');
                        DB::table('AthleteSpecialNotes')
                            ->insert(
                                [
                                    'AthleteSpecialNoteId' => $noteid,
                                    'Note' => $note,
                                ]
                            );
                        DB::unprepared('SET IDENTITY_INSERT AthleteSpecialNotes OFF');

                        //update in athlete table
                        DB::table('Athletes')
                            ->where('AthleteId', $athleteid)
                            ->update(
                                [
                                    'AthleteSpecialNoteId' => $noteid
                                ]
                            );
                        $message = 'note updated for the ' . $a->FirstName . ' athlete';

                        return response($message, 200)
                            ->header('Content-Type', 'text/plain');

                    } else {
                        $notes = DB::table('AthleteSpecialNotes')
                            ->where('AthleteSpecialNoteId', $a->AthleteSpecialNoteId)
                            ->update(
                                [
                                    'Note' => $note,
                                ]
                            );
                        $message = 'note updated for the ' . $a->FirstName . ' athlete';

                        return response($message, 200)
                            ->header('Content-Type', 'text/plain');
                    }
                }
            }

        }catch(Exception $e){
            $message = $e->getMessage();
            return response($message, 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function getNoteById(){
        try{
            if(isset($_GET['id'])){
                $athleteid = $_GET['id'];

                $notes["athleteId"] = DB::table('Athletes')
                    ->join('AthleteSpecialNotes','AthleteSpecialNotes.AthleteSpecialNoteId','=','Athletes.AthleteSpecialNoteId')
                    ->where('AthleteId', $athleteid)
                    ->select('AthleteSpecialNotes.AthleteSpecialNoteId','AthleteSpecialNotes.note')
                    ->get();
                return $notes;

            }else{
                return response('athlete id is mandatory field', 400)
                    ->header('Content-Type', 'text/plain');
            }
        }catch(Exception $e){
            $message =  $e->getMessage();
            return response($message, 500)
                ->header('Content-Type', 'text/plain');
        }
    }

}
