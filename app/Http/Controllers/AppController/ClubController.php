<?php

namespace App\Http\Controllers\AppController;

use App\Club;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function GetClub(){
        $clubs = Club::all();
        return $clubs;
    }
}
