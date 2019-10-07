<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class RoomMileage_Model extends Model
{
    
    public static function GetRoomTimeInTimeOut($room_no){

        $result = DB::connection('hms')
        ->table('tblcustomerinfo')
        ->select(
            'DateIn',
            'TimeIn',
            'DateOut',
            'TimeOut'
        )
        ->where('RoomNo', '=', $room_no)
        ->orderBy('id', 'DESC')
        ->first();

        return $result;

    }

    public static function GetMileageHours($timein, $timeout){

        $result = DB::connection('mysql')
        ->select(DB::raw("
            SELECT HOUR(TIMEDIFF('". $timeout ."', '". $timein ."')) AS `hours` 
        "));

        return $result;

    }

    
}
