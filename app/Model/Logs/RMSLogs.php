<?php

namespace App\Model\Logs;

use Illuminate\Database\Eloquent\Model;

use App\Model\RoomByCategory as Rooms;
use App\Model\Logs\RoomBuddy;
use DB;
use Carbon\Carbon;

class RMSLogs extends Model
{
    //
    protected $table = 'rms_logs';
    protected $connection = 'mysql';

    public static function getRoomReportsByRoom($roomNo){
    	$roomBuddy = RoomBuddy::where('tbl_room_buddies.room_no', $roomNo)
    	->join('user', 'tbl_room_buddies.emp_id', '=', 'user.username')
    	->join('tblroom', 'tbl_room_buddies.room_no', '=', 'tblroom.room_name')
    	->get(
	    	[
	    		DB::raw("concat(user.firstname, ' ', user.lastname) as name"),
				'tbl_room_buddies.rms_logs_id as rmsId',
				'tbl_room_buddies.work_id as workId',
				'tbl_room_buddies.from as source',
				'tblroom.room_no as roomNo'
	    	]
    	);

    	$rmsLogs = [];
    	foreach($roomBuddy as $out){

    		$out['rmsLogs'] = RMSLogs::where('rms_logs.id', $out->rmsId)
    		->join('roomstatus as pStat', 'rms_logs.p_status', '=', 'pStat.id')
    		->join('roomstatus as sStat', 'rms_logs.s_status', '=', 'sStat.id')
    		->join('roomstatus as eStat', 'rms_logs.e_status', '=', 'eStat.id')

    		->get(
    			[
    				'pStat.id as pStatusId',
    				'pStat.room_status as pStatus',
    				'sStat.id as startStatId',
    				'sStat.room_status as sStat',
    				'eStat.id as endStatusId',
    				'eStat.room_status as eStat',
    				'rms_logs.created_at as logsCreated',
    				'rms_logs.updated_at as logsEnd'
    			]
    		);

    		$out['workLogs'] = DB::connection('mysql')
    		->table('work_logs')
    		->join('user', 'work_logs.user_id', '=', 'user.username')
    		->where('work_logs.id', $out->workId)
    		->get(
    			[
    				DB::raw("concat(user.firstname, ' ', user.lastname) as name"),
    				'work_logs.is_overriden as isOverridden',
    				'work_logs.created_at as timeStart',
    				'work_logs.updated_at as timeEnd',
    				'work_logs.status as isClosed'
    			]
    		);

    	}

    	return $roomBuddy;
    }

    public static function getRemarks($roomNo){
    	$remarks = [];

    	$dateNow = Carbon::now()->format('Y-m-d');

    	$getRoom = DB::connection('mysql')
    	->table('tblroom')
    	->where('room_no', $roomNo)->first();

    	$remarks['remarks'] = DB::connection('mysql')
    	->table('tbl_transactions')
    	->where('tbl_transactions.room_no', $roomNo)
    	->WhereNotNull('tbl_transactions.created_at')
    	->WhereNotNull('tbl_transactions.other_remarks')
    	->get();

    	return $remarks;
    }
}
