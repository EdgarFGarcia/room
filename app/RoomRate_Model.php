<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class RoomRate_Model extends Model
{
    
    public static function SaveRoomRate($data){

        DB::connection('mysql')
        ->table('roomrates_count')
        ->insert([
            "room_id"=>$data->roomId,
            "rate_id"=>$data->rates,
            "rate_count"=>"1",
            "created_at"=>DB::raw("NOW()")
        ]);

    }

    public static function ValidateRoomRate($data){

        $result = DB::connection('mysql')
        ->table('roomrates_count')
        ->select(
            DB::raw("COUNT(*) AS 'rate_count'")
        )
        ->where('room_id', '=', $data->roomId)
        ->where('rate_id', '=', $data->rates)
        ->get();

        if($result[0]->rate_count!="0"){

            return false;

        }
        else{

            return true;

        }

    }

    public static function UpdateRoomRate($data){

        DB::connection('mysql')
        ->table('roomrates_count')
        ->where('room_id', '=', $data->roomId)
        ->where('rate_id', '=', $data->rates)
        ->update([
            "rate_count"=>DB::raw("rate_count + 1"),
            "updated_at"=>DB::raw("NOW()")
        ]);

    }

}
