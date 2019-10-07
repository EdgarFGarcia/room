<?php

namespace App\Model\Mobile;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Model\Room\RoomByCategory;
use App\Model\Room\RoomStatus;

class WorkLogs extends Model
{
    //
    protected $table = 'work_logs';
    protected $connection = 'mysql';
    public $timestamps = false;
 	
 	public static function updateWorkLogs($workId, $user_id, $room_no){
 		$updateWLogs = WorkLogs::where('work_id', '=', $workId)
 			->where('user_id', '=', $user_id)
 			->where('room_number', '=', $room_no)
 			->where('status', '=', 0)
 			->whereNULL('updated_at')
 			->update([
 				'status' => 1,
 				'updated_at' => DB::raw("NOW()")
 			]);
        // dd($updateWLogs);
        if($updateWLogs){
            return response()->json([
                'status' => $updateWLogs
            ], 200);
        }else{
            return response()->json([
                'status' => $updateWLogs
            ], 403);
        }
 	}

 	public static function insertWorkLogs($user_id, $roomNo, $workId){
    	$insert = WorkLogs::insert([
    		'user_id' => $user_id,
    		'room_number' => $roomNo,
    		'status' => 0,
    		'created_at' => DB::raw("NOW()"),
    		'work_id' => $workId
    	]);
    	return response()->json(['workLogsInsert'=>$insert]);
    }

    public static function checkIfHasPending($user_id){
        $out = [];
    	$check = WorkLogs::select(
                    'work_logs.status as status',
                    'work_logs.created_at as startDate',
                    'tblroom.room_no as room_no',
                    'roomstatus.room_status as status'
                    )
                    ->join('tblroom', 'work_logs.room_number', '=', 'work_logs.room_number')
                    ->join('roomstatus', 'work_logs.work_id', '=', 'roomstatus.id')
                    ->where('work_logs.user_id', $user_id)
    				->where('work_logs.status', 0)
    				->whereNull('work_logs.updated_at')
    				->orderBy('work_logs.id', 'DESC')
    				->limit(1)
                    ->first();
        if($check){
            return array(
                'check'=>$check, 
                'hasPending'=>true
            );
        }else{
            return array(
                'check'=>$check, 
                'hasPending'=>false
            );
        }
    }

    public static function checkRoomIfHasOnGoing($roomId){
        // dd($roomId, $userinfo);
        return $query = WorkLogs::select('*')
                    ->whereRaw("room_number=(SELECT room_no FROM tblroom WHERE id='". $roomId ."')")
                    ->where('status', 0)
                    ->whereNull('updated_at')
                    ->first();
    }   
}