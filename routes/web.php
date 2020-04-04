<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', 'AthleteCsvImportController@index');
//
//Route::post('/import', 'AthleteCsvImportController@csv_import')->name('import');

//merge code 21-75
Route::get('/', function () {
    return view('form');
});
//parameters are not in requirement for now.
Route::get('/api/eventNames/{gender?}/{age?}/{type?}/{year?}', 'AppController\AthleteEventController@GetEventNames');
//search by name | ACNum | id
Route::get('/api/athletes/{name?}', 'AppController\AthleteController@GetAthletes');
//select * from competition
Route::get('/api/competitions/{id?}', 'AppController\CompetitionController@GetFilterCompetitions');
// get all competitions
Route::get('/api/allCompetitions', 'AppController\CompetitionController@GetCompetitions');


//Route for Ranking page (filter Result)
Route::get('/api/ranking','AppController\ChainController@GetRanking');
//get all years
Route::get('/api/years', 'AppController\CompetitionController@getYears');
//ranking page
Route::get('/api/filteredResult/{season?}/{gender?}/{division?}/{ageGroup?}/{year?}/{name?}','AppController\ChainController@getResult');
//get divisions & years
Route::get('/api/filterdivyear','AppController\ChainController@GetFilerValue');
// select division where age = 19
Route::get('/api/getDivision/{age?}','AppController\ChainController@getDivision');
//get personal info
Route::get('/api/memberdetail/{id?}','AppController\ChainController@getPersonalInfo');
//get progress by id
Route::get('/api/progress/{id?}','AppController\ChainController@getProgress');
//
Route::get('/api/best/{id?}','AppController\ChainController@getBest');

Route::get('/api/getResult/{id?}/{year?}','AppController\ChainController@getReasultbyYear');
//best 1 result in each year
Route::get('/api/getTopResult/{id?}','AppController\ChainController@oneBestResult');
Route::get('/api/notes/{id?}','AppController\AthleteSpecialNoteController@getNoteById');

//add competition to competition table
Route::post('/api/competitions','AppController\CompetitionController@AddCompetitions');

Route::post('/api/notes','AppController\AthleteSpecialNoteController@addNote');

//update competition - id mandatory
Route::put('/api/competitions','AppController\CompetitionController@UpdateCompetitions');


Route::delete('/api/competitions','AppController\CompetitionController@DeleteCompetitions');


//Route::put($uri, $callback);
//Route::delete($uri, $callback);

//NOT USED APIs
Route::get('/api/results', 'AppController\ResultController@GetResult');
Route::get('/api/clubs', 'AppController\ClubController@GetClub');
Route::get('/api/specialNotes', 'AppController\AthleteSpecialNoteController@GetAthleteSpecialNotes');
Route::post('/results', 'AppController\ResultController@PostResult');
