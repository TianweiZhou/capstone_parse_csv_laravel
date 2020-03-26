<?php

namespace App\Http\Controllers;

use App\Club;
use App\Imports\CludCsvImport;
use Illuminate\Http\Request;
use App\Athlete;
use App\Imports\AthleteCsvImport;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AthleteCsvImportController extends Controller
{
    public $AthleteCsvImportReportList = ['ERRORTYPE'=>"ATHLETE CSV IMPORT REPORT\r\n"];
//    function index(){
//        $data = Athlete::all();
//        return response()->json(['data'=> 'test'], 200);;
//    }

    //import the csv in to database
    public function csv_import(Request $request){
        try{
            $file = $this->getFile($request);
            if(is_null($file)){
                return response()->json(['response_message'=> 'File not uploaded'], 400);
            }else{
                if($this->validateFile($file)){
                    Excel::import(new CludCsvImport(), $file);
                    Excel::import(new AthleteCsvImport(), $file);
                    $this->put();
                    return response()->json(['response_message'=> 'Upload successfully'], 200);

                }else{
                    return response()->json(['response_message'=> 'Not a valid csv, xlsx or xls file'], 400);
                }
            }
        }catch(Exception $exception){
            $errMessage = $exception->getMessage();
            return response()->json(['response_message'=> $errMessage], 500);
        }
    }

    /**
     * Validate file
     * return true when file has xlsx, xls, csv extension
    */
    private function validateFile($file){
        $extension = File::extension($file->getClientOriginalName());
        if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
            return true;
        }else{
            return false;
        }
    }

    public function put() {
        $writeClubMessage = Club::$clubReportList;
        $writeAthleteMessage = Athlete::$athleteReportList;
        Storage::disk('local')->put("Club Report.txt", $writeClubMessage);
        Storage::disk('local')->put("Athlete Report.txt", $writeAthleteMessage);
    }

    public function getFile($request){
        $file = $request->file('file');
        return $file;
    }
}
