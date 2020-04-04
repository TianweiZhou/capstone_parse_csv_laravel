<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;
use App\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function GetResult(){
        $result = Result::all();
        return $result;
    }
    public function PostResult(Request $request){
        $result = new Result();

        $result->CompId = request()->get('CompId');
        $result->EventId = request()->get('EventId');
        $result->AthleteId = request()->get('AthleteId');
        $result->Mark = request()->get('Mark');
        $result->Position = request()->get('Position');
        $result->Wind = request()->get('Wind');

        $result->save();

        return $result;
    }



}
