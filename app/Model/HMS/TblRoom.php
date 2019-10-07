<?php

namespace App\Model\HMS;

use Illuminate\Database\Eloquent\Model;
use App\Model\Room\RoomByCategory as Rooms;
use DB;


class TblRoom extends Model
{
    //
    protected $table = 'tblroom';
    protected $connection = 'hms';
    public $timestamps = false;

    protected $fillable = array('CRoom_Stat');

    public static function changeHmsStatus($roomId, $nextStatus){
 	    $roomDetails = Rooms::where('id', $roomId)
                            ->where('room_status_id', '!=', 2)
                            ->first();

 		if ($nextStatus == 1 || $nextStatus == 16 || $nextStatus == 27 || $nextStatus == 28) {
            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                'CRoom_Stat' => $nextStatus,
                'Stat' => 'CLEAN'
            ]);
        } else if($nextStatus == 2){

            // Edgar
            // if(count($clearOrdersIfAny) > 1){
            //     foreach($clearOrdersIfAny as $empty){
            //         $query = DB::connection('hms')
            //         ->table('tbl_postrans_kds')
            //         ->where('room_no', $roomDetails->room_no)
            //         ->
            //     }
            // }

            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                'CRoom_Stat' => $nextStatus,
                'Stat' => 'WELCOME'
            ]);

            //Occupied Time Adjustment
            DB::connection('mysql')
            ->table('rmsguestinfo')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                "TimeIn"=>DB::raw("TIME_FORMAT(NOW(), '%h:%i:%s %p')")
            ]);

            DB::connection('hms')
            ->table('tbl_postrans_kds')
            ->where('room_no', '".$roomDetails->room_no."')
            ->where('itemStat', '0')
            ->update([
                'itemStat' => '2'
            ]);

        } else if($nextStatus == 19){
            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                'CRoom_Stat' => $nextStatus,
                'Stat' => 'DIRTY'
            ]);
        } else if($nextStatus == 10){
            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                'CRoom_Stat' => 1,
                'Stat' => 'CLEAN'
            ]);
        } else if($nextStatus == 39 || $nextStatus == 40){
            if ($roomDetails->room_status_id == 31) {
                if($nextStatus == 40){
                    $updateRoomHMS = DB::connection('hms')
                    ->table('tblroom')
                    ->where('RoomNo', '=', $roomDetails->room_no)
                    ->update([
                        'CRoom_Stat' => $nextStatus,
                        'Stat' => 'DIRTY'
                    ]);
                } else {
                    $updateRoomHMS = 0;    
                }
            }  else {
                if($nextStatus == 39){
                    $updateRoomHMS = DB::connection('hms')
                    ->table('tblroom')
                    ->where('RoomNo', '=', $roomDetails->room_no)
                    ->update([
                        'CRoom_Stat' => $nextStatus,
                        'Stat' => 'OCCUPIED'
                    ]);
                } else if ($nextStatus == 40) {
                    $updateRoomHMS = DB::connection('hms')
                    ->table('tblroom')
                    ->where('RoomNo', '=', $roomDetails->room_no)
                    ->update([
                        'CRoom_Stat' => $nextStatus,
                        'Stat' => 'DIRTY'
                    ]);        
                } 
            }
        } else { 
            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $roomDetails->room_no)
            ->update([
                'CRoom_Stat' => $nextStatus,
                'Stat' => 'DIRTY'
            ]);
        }

        return $updateRoomHMS;
        // if($updateRoomHMS){
        //     return array('updateRoom' => $updateRoomHMS,
        //         'message' => 'Successful');
        //     // return response()->json([
                
        //     // ], 200);
        // }else{
        //     return array('updateRoom' => $updateRoomHMS,
        //         'message' => 'Successful');
        //     // return response()->json([
                
        //     // ], 403);
        // }
 	}

    
}