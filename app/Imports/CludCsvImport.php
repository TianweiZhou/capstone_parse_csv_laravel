<?php

namespace App\Imports;

use App\Club;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Arr;

class CludCsvImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        $todayDateTime = Carbon::now('America/Toronto');
        Club::$clubReportList = Arr::add(Club::$clubReportList,
            'DATETIME', "DATE TIME--$todayDateTime\r\n");
        if($this->isUniqueClubCode($row['code'])) {
            return new Club([
                'ClubCode' => $row['code'],
                'ClubName' => $row['club']
            ]);
        }else{
            Club::$clubReportList = Arr::add(
                Club::$clubReportList,
                'DUPLICATE', 'DUPLICATE--Try to insert ' . $row['code'] . ', ' .$row['club']
                . ' failure because of duplicate club data.'."\r\n");
        }
    }

    /**
     * Check unique club code
     * @return true when club code is unique
     * @return false when club code is duplicate or null
     */
    private function isUniqueClubCode($clubCode){
        if(!is_null($clubCode)) {
            $result = Club::where('ClubCode', $clubCode)->get();
            if(count($result) > 0){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
}
