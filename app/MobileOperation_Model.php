<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class MobileOperation_Model extends Model
{
    
    public static function SaveOccupiedAuditInspection($data){

        DB::connection('mysql')
        ->table('tbl_inspection_audit')
        ->insert([
            'roomid'=>$data->room_id,
            'username'=>$data->user_id,
            'mobile'=>$data->mobile,
            'starttime'=>$data->start_time,
            'endtime'=>$data->end_time,
            'created_at'=>DB::raw("NOW()")
        ]);


    }

    public static function GetRMSRoomInfo($roomid){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'room_no'
        )
        ->where('id', '=', $roomid)
        ->first();

        return $result;

    }

    public static function GetHMSRoomInfo($roomno){

        $result = DB::connection('hms')
        ->table('tblroom')
        ->select(
            'Stat'
        )
        ->where('RoomNo', '=', $roomno)
        ->first();

        return $result;

    }

    public static function CheckMobileVersion($mobileid){

        $result = DB::connection('mysql')
        ->table('mobile_versions')
        ->select(
            '*'
        )
        ->where('id', '=', $mobileid)
        ->first();

        return $result;

    }

    public static function UpdateMobileVersion($mobileid, $version, $versioncode){

        DB::connection('mysql')
        ->table('mobile_versions')
        ->where('id', '=', $mobileid)
        ->update([
            'version'=>$version,
            'versioncode'=>$versioncode
        ]);

    }

}
