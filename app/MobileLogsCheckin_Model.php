<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class MobileLogsCheckin_Model extends Model
{
    
    public static function DeleteGuestInformation($roomno){

        DB::connection('mysql')
        ->table('rmsguestinfo')
        ->where('RoomNo' , '=', $roomno)
        ->delete();

    }

    public static function GetHMSRoomStatus($roomno){

        $result = DB::connection('hms')
        ->table('tblroom')
        ->select(
            '*'
        )
        ->where('RoomNo', '=', $roomno)
        ->first();

        return $result;

    }
    
}
