<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Model\Room\RoomByCategory as Room;
use App\Model\HMS\TblRoom;

class RMSLogs_Model extends Model
{
    
    protected $table = "rms_logs";
	protected $connection = "mysql";
    
    public static function GetRoomProfile($id){

    	$result = DB::connection('mysql')
    	->table('tblroom')
    	->select(
    		'from_room_status_id',
    		'updated_at'
    	)
    	->where('id', '=', $id)
    	->get();

    	return $result;

	}
	
	public static function GetRMSLogsProfile($room_id){

		$result = DB::connection('mysql')
		->table('rms_logs')
		->select(
			'id',
			DB::raw("TIMESTAMPDIFF(SECOND, rms_logs.created_at, NOW()) AS 'time'")
		)
		->where('room_id', '=', $room_id)
		->orderBy('id', 'DESC')
		->limit(1)
		->get();

		return $result;

	}

	public static function ValidateStartLogs($room_id){

		$result = DB::connection('mysql')
    	->table('rms_logs')
    	->select(
			DB::raw("COUNT(*) AS 'check'"),
			'id'
    	)
		->where('room_id', '=', $room_id)
		->where('s_emp_id', '=', '0')
		->orderBy('id', 'DESC')
		->limit(1)
    	->get();

		return $result;

	}

	public static function ValidateEndtLogs($room_id){

		$result = DB::connection('mysql')
    	->table('rms_logs')
    	->select(
			DB::raw("COUNT(*) AS 'check'"),
			'id'
    	)
		->where('room_id', '=', $room_id)
		->where('e_emp_id', '=', '0')
		->orderBy('id', 'DESC')
		->limit(1)
    	->get();

		return $result;

	}

	//mobile rms logs
	public static function insertLogsStart($roomId, $userId, $mobile, $status){
		//TODO dito ako lubid
		// $getRoomInfo = Room::checkRoomStatus($roomId);
		// dd("lubid");

		$getRoomInfo = Room::where('id', $roomId)->first();
		// dd($getRoomInfo);

		$prevStatus = $getRoomInfo->from_room_status_id;
		$p_DateTime = $getRoomInfo->updated_at;
		$s_room_stat = $getRoomInfo->room_status_id;
		$room_no = $getRoomInfo->room_no;

		$getPreviousTime = DB::connection('mysql')
		->table('rms_logs')
		->select('*')
		->where('room_id', $roomId)
		->orderBy('id', 'DESC')
		->first();

		// $prevTime = $getPreviousTime->e_dateTime;

		$tansactionRMSLogs = DB::transaction(function() use ($roomId, $getRoomInfo, $prevStatus,
			$p_DateTime, $s_room_stat, $room_no, $userId, $mobile, $status){

			$dateNow = DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')");

			//insert to rms_logs
			$rmsLogsInsert = DB::connection('mysql')
			->table('rms_logs')
			->insertGetId([
				'room_id' => $roomId,
				'p_status' => $s_room_stat,
				'p_dateTime' => DB::raw("(SELECT updated_at FROM tblroom WHERE id='".$roomId."')"),
				's_status' => $status,
				's_from' => $mobile,
				's_emp_id' => $userId,
				's_datetime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

			$lastInsertedIdRmsLogs = $rmsLogsInsert;

			//insert work logs
			$workLogs = DB::connection('mysql')
			->table('work_logs')
			->insertGetId([
				'user_id' => $userId,
				'room_number' => $room_no,
				'status' => 0,
				'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'work_id' => $status
			]);

			$lastWorkLogId = $workLogs;

			//insert to tbl_room_buddies
			$roomBuddies = DB::connection('mysql')
			->table('tbl_room_buddies')
			->insertGetId([
				'rms_logs_id' => $lastInsertedIdRmsLogs,
				'emp_id' => $userId,
				'room_no' => $room_no,
				'from' => $mobile,
				'work_id' => $lastWorkLogId,
				'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

			$lastInsertedRoomBuddies = $roomBuddies;

			return array(
				'lastRmsLogId' => $lastInsertedIdRmsLogs,
				'lastWorkId' => $lastWorkLogId,
				'lastRoomBuddies' => $lastInsertedRoomBuddies,
				'time' => 0
			);
		});

		return $tansactionRMSLogs;
	}

	public static function updateRoomInfo($userId, $roomNo){
		$query = Room::where('room_no', $roomNo)
			->update([
				'from_userinfo' => $userId
			]);
	}

	public static function updateRoomBuddiesInfo($userId, $roomNo, $rmsId, $from, $work_id){
		$query = DB::connection('mysql')
		->table('tbl_room_buddies')
		->insert([
			'rms_logs_id' => $rmsId,
			'emp_id' => $userId,
			'room_no' => $roomNo,
			'from' => $from,
			'work_id' => $work_id
		]);

		return $query;
	}

	public static function updateRoomInfoRoomBuddies($rmsId, $workLogsId, $userId, $roomNo, $where){

		$tblWorkBuddiesUserInfo = DB::connection('mysql')
		->table('tbl_room_buddies')
		->where('rms_logs_id', $rmsId)
		->insert([
			'emp_id' => $userId,
			'room_no' => $roomNo,
			'from' => $where,
			'work_id' => $workLogsId,
			'created_at' => DB::raw("NOW()") 
		]);
		
	}

	public static function updateRMSLogsAndWorkLogs($rmsId, $toStatus, $userId, $mobile){

		$generalId = DB::connection('mysql')
		->table('tbl_room_buddies')
		->select('*')
		->where('rms_logs_id', $rmsId)
		->first();

		$query = RMSLogs_Model::where('id', $rmsId)
		->update([
			'e_status' => $toStatus,
			'e_emp_id' => $userId,
			'e_from' => $mobile,
			'e_dateTime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
			'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
		]);

		$updateWorkLogs = DB::connection('mysql')
		->table('work_logs')
		->where('id', $generalId->work_id)
		->update([
			'status' => 1,
			'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
		]);

		return array(
			'rmsLogs' => $query,
			'workLogs' => $updateWorkLogs
			// 'updateHMSTblRoom' => $updateHMSTblRoom
		);

	}

	public static function validateWorkBuddies($rmsId, $userId){
		
		$query = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)
			->where('emp_id', $userId)
			->select(DB::raw("COUNT(*) as user_count "))->get();

		if($query[0]->user_count == 0){
			return true;
		}else{
			return false;
		}

	}

	public static function workBuddies($rmsId, $userId, $mobile){
		$workBuddies = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)->first();

		$insertBuddies = DB::connection('mysql')
			->table('tbl_room_buddies')
			->insert([
				'rms_logs_id' => $workBuddies->rms_logs_id,
				'emp_id' => $userId,
				'room_no' => $workBuddies->room_no,
				'from' => $mobile,
				'work_id' => $workBuddies->work_id
			]);
		
		if($insertBuddies){
			return array(
				'insertBuddies' => $insertBuddies
			);
		}else{
			return array(
				'insertBuddies' => array()
			);
		}

	}

	public static function closeRmsLogsCheckIn($rmsId, $endStatus, $userId, $mobile){
		$query = RMSLogs_Model::where('id', $rmsId)
			->update([
				'e_status' => $endStatus,
				'e_emp_id' => $userId,
				'e_from' => $mobile,
				'e_dateTime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);
	}

	public static function previousStatus($rmsId, $userId, $mobile){
		// dd($rmsId);
		// TODO previous status
		$generalId = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)
			->orderBy('id', 'DESC')
			->first();

		$getRoomInfo = DB::connection('mysql')
			->table('tblroom')
			->where('room_no', $generalId->room_no)
			->first();

		// $updateHMSTblRoom = DB::connection('hms')
		// 	->table('tblroom')
		// 	->where('RoomNo', $generalId->room_no)
		// 	->update([
		// 		'CRoom_Stat' => $getRoomInfo->from_room_status_id
		// 	]);

		$updateHMSTblRoom = TblRoom::changeHmsStatus($getRoomInfo->id, $getRoomInfo->from_room_status_id);

		// $updateHMSTblRoom = TblRoom::changeHmsStatus($getRoomInfo->id, 53);

		$workLogsClose = DB::connection('mysql')
			->table('work_logs')
			->where('id', $generalId->work_id)
			->update([
				'status' => 1,
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

		$rmsLogClose = RMSLogs_Model::where('id', $rmsId)
			->update([
				'e_status' => 53,
				'e_emp_id' => $userId,
				'e_from' => $mobile,
				'e_dateTime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

		$rmsLogs = RMSLogs_Model::where('id', $rmsId)
		->first();

		$updateRoomInfo = DB::connection('mysql')
		->table('tblroom')
		->where('room_no', $generalId->room_no)
		->update([
			'updated_at' => $rmsLogs->e_dateTime
		]);

		return array(
			'updateHMSTblRoom' => $updateHMSTblRoom,
			'workLogsClose' => $workLogsClose,
			'rmsLogClose' => $rmsLogClose
		);
	}

	public static function closeInspectionRMSLogs($rmsId, $endStatus){
		// $rmslogs = RMSLogs_Model::where('id', $rmsId)->first();
		$generalId = DB::connection('mysql')
				->table('tbl_room_buddies')
				->where('rms_logs_id', $rmsId)->first();

		// dd($generalId);

		$workLogsId = $generalId->work_id;

		$query = RMSLogs_Model::where('id', $rmsId)
			->update([
				'e_status' => $endStatus,
				'e_dateTime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

		$closeWorkLogs = DB::connection('mysql')
					->table('work_logs')
					->where('id', $workLogsId)
					->update([
						'status' => 1,
						'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
					]);

		// dd($query, $closeWorkLogs);
	}

	public static function updateUserInfo($roomNumber, $user_id){
		return $query = Room::where('room_no', $roomNumber)
				->update([
					'from_userinfo' => $user_id
				]);
	}

	public static function cancelInspection($rmsId, $userid, $mobile){
		// return $rmsId;
		$query = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)
			->orderBy('id', 'DESC')
			->first();

		$rmsRoomId = RMSLogs_Model::where('id', $rmsId)->get();

		$workLogsId = $query->work_id;

		$prevStatRoom = Room::where('room_no', $query->room_no)->get();

		//dione hindot

		$requeryRoom = Room::where('room_no', $query->room_no)->first();
		// dd($requeryRoom);
		$changeRMSLOGS = RMSLogs_Model::where('id', $rmsId)
			->update([
				// 'e_status' => $requeryRoom->from_room_status_id,
				'e_status' => 53,
				'e_from' => $mobile,
				'e_emp_id' => $userid,
				'e_dateTime' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
			]);

		//TODO change HMS Status
		$changeRoomStatus = TblRoom::changeHmsStatus($rmsRoomId[0]->room_id, $prevStatRoom[0]->from_room_status_id);
		// $changeRoomStatus = TblRoom::where('RoomNo', $query->room_no)
		// 		->update([
		// 			'CRoom_Stat' => $prevStatRoom->from_room_status_id
		// 		]);

		$updateWorkLogs = DB::connection('mysql')
			->table('work_logs')
			->where('id', $workLogsId)
			->update([
				'status' => 1,
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);

		return array(
			'query' => $query,
			'changeRoomStatus' => 1,
			'updateWorkLogs' => 1,
			'changeRMSLOGS' => 1
		);
	}

	public static function closeWorkLogs($rmsId){
		$query = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)->first();

		$workLogsId = $query->work_id;

		$updateWorkLogs = DB::connection('mysql')
			->table('work_logs')
			->where('id', $workLogsId)
			->update([
				'status' => 1,
				'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')")
			]);
	}

	public static function closeWorkLogsNotOrigAuthor($rmsId, $userId){
		
		$query = DB::connection('mysql')
			->table('tbl_room_buddies')
			->where('rms_logs_id', $rmsId)->first();

		$workLogsId = $query->work_id;


		if($query->emp_id != $userId){
			$updateWorkLogs = DB::connection('mysql')
				->table('work_logs')
				->where('id', $workLogsId)
				->update([
					'status' => 1,
					'updated_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')"),
					'is_overriden' => 1
				]);

			if($updateWorkLogs){
				return true;
			}else{
				return false;
			}
		}
		
	}

	public static function cancelWaitingGuest($roomNo){

		return $cancelWaiting = DB::connection('mysql')
		->table('rmsguestinfo')
		->where('RoomNo', $roomNo)
		// ->orderBy('id', 'DESC')
		->delete();

	}

}