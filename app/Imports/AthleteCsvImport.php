<?php

namespace App\Imports;

use App\Athlete;
use App\AthletesHistory;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AthleteCsvImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return Model|null
     * @throws Exception
     */
    public function model(array $row)
    {
        $todayDateTime = Carbon::now('America/Toronto');
        Athlete::$athleteReportList = Arr::add(Athlete::$athleteReportList,
            'DATETIME', "DATE TIME--$todayDateTime\r\n");
        //check mandatory fields and sign values
        if(!is_null($row['athletics_canada']) && !is_null($row['joined'])){
            $ACNum = $row['athletics_canada'];
            $ClubAffiliationSince = $this->transformDate($row['joined']);

            //sign and validate the values for rest of column
            if(!is_null($row['first_name'])){
                $FirstName = $row['first_name'];
            }else{
                $FirstName = null;
            }

            if(!is_null($row['last_name'])){
                $LastName = $row['last_name'];
            }else{
                $LastName = null;
            }

            if(!is_null($row['date_of_birth'])){
                $DOB = $this->transformDate($row['date_of_birth']);
            }else{
                $DOB = null;
            }

            if(!is_null($row['sex'])){
                $Gender = $row['sex'];
            }else{
                $Gender = null;
            }

            if(!is_null($row['code'])){
                $ClubCode = $row['code'];
            }else{
                $ClubCode = null;
            }

            if(!is_null($row['address_1'])){
                $Address = $row['address_1'];
            }else{
                $Address = null;
            }

            if(!is_null($row['city'])){
                $City= $row['city'];
            }else{
                $City = null;
            }

            if(!is_null($row['phone'])){
                $Phone = $row['phone'];
            }else{
                $Phone = null;
            }

            if(!is_null($row['email'])){
                $Email = $row['email'];
            }else{
                $Email = null;
            }


            $columnNameArray = ['ACNum','FirstName', 'LastName', 'DOB', 'AthleteGender', 'ClubCode', 'Address',
                'City', 'Phone', 'AthleteEmail', 'ClubAffiliationSince'];
            $newValueArray = [$ACNum, $FirstName, $LastName, $DOB, $Gender, $ClubCode, $Address,
                $City, $Phone, $Email, $ClubAffiliationSince];

            //check duplicate ac number
            $athleteFindByACNum = $this->findAthleteByACNum($ACNum);
            if(count($athleteFindByACNum) === 0){

                //check duplicate fname & lname & dob
                $athleteFindByFLNameDOB = $this->findAthleteByFnameLnameDOB($FirstName, $LastName, $DOB);
                if(count($athleteFindByFLNameDOB) === 0){

                    return $this->createAthlete($ACNum, $FirstName, $LastName, $DOB, $Gender, $ClubCode, $Address, $City, $Phone, $Email, $ClubAffiliationSince);

                }elseif (count($athleteFindByFLNameDOB) === 1){

                    //have same record with fname, lname, dob
                    $update = false;
                    for ($index = 0; $index < 11; $index++){
                        $result = $this->checkUpdateAthlete($athleteFindByFLNameDOB[0], $columnNameArray[$index], $newValueArray[$index]);
                        if($result){

                            Athlete::$athleteReportList = Arr::add(Athlete::$athleteReportList,
                                'UPDATE', "UPDATE--Update $columnNameArray[$index] of athlete $FirstName, $LastName $DOB to $newValueArray[$index]\r\n");
                            $update = true;
                        }
                    }
                    if(!$update){
                        Athlete::$athleteReportList = Arr::add(
                            Athlete::$athleteReportList,
                            'ALERT', "ALERT--Try to insert athlete $FirstName, $LastName $DOB failure because may have duplicate athlete saved in database.\r\n");
                    }
                }else{
                    Athlete::$athleteReportList = Arr::add(
                        Athlete::$athleteReportList,
                        'ALERT', "ALERT--Try to insert or update athlete $FirstName, $LastName $DOB failure because have duplicate athletes have same name and birthday saved in database.\r\n");
                }
            }else{
                //have same record with ac number
                $update = false;
                for ($index = 0; $index < 11; $index++){
                    $result = $this->checkUpdateAthlete($athleteFindByACNum[0], $columnNameArray[$index], $newValueArray[$index]);
                    if($result){
                        Athlete::$athleteReportList = Arr::add(Athlete::$athleteReportList,
                            'UPDATE', "UPDATE--Update $columnNameArray[$index] of athlete $ACNum to $newValueArray[$index].\r\n");
                        $update = true;
                    }
                }
                if(!$update){
                    Athlete::$athleteReportList = Arr::add(Athlete::$athleteReportList,
                        'DUPLICATE', "DUPLICATE--Try to insert athlete $ACNum failure because of duplicate athlete data.\r\n");
                }
            }
        }else{
            if(is_null($row['first_name']) || is_null($row['last_name'])){
                Athlete::$athleteReportList = Arr::add(
                    Athlete::$athleteReportList,
                    'ERROR', 'Try to insert athlete failure because of lost required data.');
            }else{
                Athlete::$athleteReportList = Arr::add(
                    Athlete::$athleteReportList,
                    'ERROR', 'ERROR--Try to insert athlete '.$row['first_name'] . ', '
                    . $row['last_name'] . ' failure because of empty AC number or club join time.'."\r\n");
            }
        }

    }

    /**
     * Create Athlete
     * @param $ACNum
     * @param $FirstName
     * @param $LastName
     * @param $DOB
     * @param $Gender
     * @param $ClubCode
     * @param $Address
     * @param $City
     * @param $Phone
     * @param $Email
     * @param $ClubAffiliationSince
     * @return Athlete
     */
    private function createAthlete($ACNum, $FirstName, $LastName, $DOB, $Gender, $ClubCode, $Address, $City, $Phone, $Email, $ClubAffiliationSince){
        return new Athlete([
            'ACNum' => $ACNum,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'DOB' => $DOB,
            'AthleteGender' => $Gender,
            'ClubCode' => $ClubCode,
            'Address' => $Address,
            'City' => $City,
            'Phone' => $Phone,
            'Email' => $Email,
            'ClubAffiliationSince' => $ClubAffiliationSince
        ]);
    }

    /**
     * Transform a date value into a datetime.
     *
     * @param $value
     * @param string $format
     * @return DateTime
     * @throws Exception
     */
    public function transformDate($value, $format = 'Y-m-d')
    {
        return Date::excelToDateTimeObject($value)->format($format);
    }

    /**
     * Find athlete by ac number
     * @param $ACNum
     * @return Athlete when find matched athlete
     */
    private function findAthleteByACNum($ACNum){
        if(!is_null($ACNum)) {
            return Athlete::where('ACNum', (string)$ACNum)
                ->get();
        }else{
            return null;
        }
    }

    /**
     * Find athlete by first name, last name and dob
     * @param $Fname
     * @param $Lname
     * @param $DOB
     * @return Athlete when find matched athlete
     */
    private function findAthleteByFnameLnameDOB($Fname, $Lname, $DOB){
        if(!is_null($Fname) && !is_null($Lname) && !is_null($DOB)){
            return Athlete::where('FirstName', $Fname)
                ->where('LastName', $Lname)
                ->where('DOB', $DOB)
                ->get();
        }else{
            return null;
        }
    }

    /**
     * Check and update athlete
     * Insert athlete history table when club code changed
     * @param $athlete
     * @param $columnName
     * @param $newValue
     * @return bool
     */
    private function checkUpdateAthlete($athlete, $columnName, $newValue){
        if(!is_null($newValue)){
            if($athlete->$columnName == $newValue){
                return false;
            }else{
                //if club changed save in club history db
                if($columnName === 'ClubCode'){
                    $this->insertAtheletHistroy($athlete);
                }
                $athlete->$columnName = $newValue;
                $athlete -> save();
                return true;
            }
        }else{
            return false;
        }
    }

    /**
     * insert athlete history table when club code changed
     * @param $athlete
     */
    private function insertAtheletHistroy($athlete){
        if(!is_null($athlete)){
            if(!is_null($athlete->ClubCode)){
                $history = new AthletesHistory;
                $history->AthleteId = $athlete->AthleteId;
                $history->ClubCodeHistory = $athlete->ClubCode;
                $history->ClubAffiliationSinceHistory = $athlete->ClubAffiliationSince;
                $history->save();
            }
        }
    }
}
