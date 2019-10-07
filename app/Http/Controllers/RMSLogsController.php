<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\RMSLogs_Model as Logs;
use App\Model\Room\RoomByCategory as Room;
use App\Model\Room\RoomInventory;
use App\Model\VCReserve\Locale as RoomReservationDetails;
use App\Model\VCReserve\LocaleID;
use App\Model\Logs\RMSLogs;
use App\Model\Room\RoomStatus;
use App\Model\Auth\UserCheck;
use App\Model\Auth\UserLocale;
use Illuminate\Support\Collection;
use DataTables;
use Carbon\Carbon;
use App\Http\Controllers\RerpotsControllers;
// use App\Model\Room\RoomType as RoomType;
use DB;

use App\Model\OnlineReservation;

class RMSLogsController extends Controller
{
    
    public function __construct()
    {

        date_default_timezone_set('Asia/Manila');

    }
    
    function SaveRMSLogs(Request $request){

		$startvalidation = Logs::ValidateStartLogs($request->roomId);

		if($startvalidation[0]->check!=0){

			Logs::where('id', '=', $startvalidation[0]->id)
			->update([
				's_emp_id'=>$request->userInfo,
				's_trans_from'=>$request->trans_from
			]);

        }
        else{

            $endvalidation = Logs::ValidateEndtLogs($request->roomId);

            if($endvalidation[0]->check!=0){
            
                Logs::where('id', '=', $endvalidation[0]->id)
                ->update([
                    'e_emp_id'=>$request->userInfo,
                    'e_trans_from'=>$request->trans_from
                ]);
            
            }

        }

       	
    }

    public function getRoomDetails(Request $r){

      $query = Logs::where('room_id', '=', $r->roomId)->first();
      $roomDetail = Room::select('*')
      				->join('tblroomtype', 'tblroom.room_type_id', '=', 'tblroomtype.id')
      				->where('tblroom.id', '=', $r->roomId)
      				->first();

	  return response()->json(['logs'=>$query, 'roomDetail'=>$roomDetail]);
	  
    }

    public function roomRemarks(Request $r){
    	return $query = RerpotsControllers::getRemarks($r);
    }

    public function roomInventory(Request $r){

    	$inventory = RoomInventory::where('room_id', '=', $r->roomId)->get();

    	$data = array();
    	foreach($inventory as $out){
    		$obj = new \stdClass;
    		$obj->brand = $out->brand;
    		$obj->brand_description = $out->brand_description;
    		$obj->quantity = $out->quantity;
    		$data[] = $obj;
    	}
    	$info = new Collection($data);

    	// return response()->json(['inventory'=>$inventory]);
		return DataTables::of($info)->make(true);
		
    }


    public function getVCLocale(Request $r){
        // return "hello world";
    	// return $r->all();
        $getRoomNo = OnlineReservation::getRoomNo($r->roomId);

        $roomNo = $getRoomNo[0]->room_no;
        $date = Carbon::now()->format('Y-m');
        $localeID = LocaleID::select('*')->first();

        $getReservationFromVCReserve = OnlineReservation::getReservationFromVCReserve($roomNo, $date, $localeID->local_id);

    	// $query = LocaleID::select('*')->first();
    	// $getReservedDetails = RoomReservationDetails::select('*')
    	// 							->where('Locale_ID', '=', $query->local_id)
    	// 							->whereRaw("DATE_FORMAT(Reserve_Date, '%Y-%m')='$id'")
    	// 							->where('Room_Number', '=', $r->roomNo)
    	// 							->get();
    	// return response()->json(['reserveOnThisRoom'=>$getReservedDetails, 'Locale'=>$query, 'id'=>$id]);

        $data = array();
        foreach($getReservationFromVCReserve as $out){
            $obj = new \stdClass;
            $obj->date = $out->Reserve_Date;
            $obj->time = $out->Time;
            $obj->hours = $out->Hours;
            $obj->notes = $out->Reserved_Notes;
            $data[] = $obj;
        }
        $info = new Collection($data);
        return DataTables::of($info)->make(true);
    }

    public function currentLogs(Request $r){

        // $test = $r->all();
        // dd($test);
        $getInstanceRoom = Room::where('id', $r->roomId)->first();

        $getCurrentStatus = RoomStatus::where('id', $getInstanceRoom->room_status_id)->first();

        // $loggedBy = DB::connection('mysql')
        // ->table('tbl_room_buddies')
        // ->where('room_no', $getInstanceRoom->room_no)
        // ->latest()->first();

        $loggedBy = DB::connection('mysql')
        ->table('tblroom')
        ->where('room_no', $getInstanceRoom->room_no)
        ->latest()->first();

        $getUserLoggedBy = UserCheck::where('username', $loggedBy->from_userinfo)
                            ->select('fname', 'lname', 'mname')
                            ->first();

        return response()->json(['getInstanceRoom'=>$getInstanceRoom, 'getCurrentStatus'=>$getCurrentStatus, 'getUserLoggedBy'=>$getUserLoggedBy, 'source'=>$loggedBy]);

    }

    public function todayLogs(Request $r){
        // return "hello";
        $now = Carbon::now()->format('Y-m-d');

        $getCurrentRoom = Room::where('id', '=', $r->roomId)->first();

        $todayLogs = DB::connection('mysql')
        ->table('rms_logs as logs')

        ->select(
            'tbl_room_buddies.from as from',
            DB::raw("COALESCE(logs.s_from, tbl_room_buddies.from) as sfrom"),
            DB::raw("COALESCE(logs.e_from, tbl_room_buddies.from) as efrom"),
            DB::raw("IFNULL(logs.s_status, '') as start_status"),
            DB::raw("IFNULL(logs.e_status, '') as end_status"),
            'tbl_room_buddies.emp_id as userId',
            DB::raw("IFNULL(logs.s_datetime, '') as start_datetime"),
            DB::raw("IFNULL(logs.e_datetime, '') as end_datetime"),
            DB::raw("IFNULL(s_stat.room_status, '') as start_status_text"),
            DB::raw("IFNULL(e_stat.room_status, '') as end_status_text"),
            // DB::raw("COALESCE(u.firstname, posUsera.fname, tblNull.firstname, '') as fname"),
            // DB::raw("COALESCE(u.lastname, posUsera.lname, tblNull.lastname, '') as lname"),
            // DB::raw("COALESCE(b.firstname, posUserb.fname, tblNull.firstname, '') as endfname"),
            // DB::raw("COALESCE(b.lastname, posUserb.lname, tblNull.lastname, '') as endlname")

            DB::raw("COALESCE(u.firstname, posUsera.fname, '') as fname"),
            DB::raw("COALESCE(u.lastname, posUsera.lname, '') as lname"),
            DB::raw("COALESCE(b.firstname, posUserb.fname, '') as endfname"),
            DB::raw("COALESCE(b.lastname, posUserb.lname, '') as endlname")

        )

        ->leftjoin('tbl_room_buddies', 'logs.id', '=', 'tbl_room_buddies.rms_logs_id')
        ->leftjoin('roomstatus as s_stat', 'logs.s_status', '=', 's_stat.id')
        ->leftjoin('roomstatus as e_stat', 'logs.e_status', '=', 'e_stat.id')
        ->leftjoin('user as u', 'logs.s_emp_id', '=', 'u.username')
        ->leftjoin('user as b', 'logs.e_emp_id', '=', 'b.username')
        ->leftjoin('vc_employees as posUsera', 'logs.s_emp_id', '=', 'posUsera.username')
        ->leftjoin('vc_employees as posUserb', 'logs.e_emp_id', '=', 'posUserb.username')
        // ->leftjoin('user as tblNull', 'tbl_room_buddies.emp_id', '=', 'tblNull.username')
        // ->leftjoin('rms_logs as logs', 'tbl_room_buddies.rms_logs_id', '=', 'logs.id')
        // ->leftjoin('roomstatus as s_stat', 'logs.s_status', '=', 's_stat.id')
        // ->leftjoin('roomstatus as e_stat', 'logs.e_status', '=', 'e_stat.id')
        // ->leftjoin('user as u', 'logs.s_emp_id', '=', 'u.username')
        // ->leftjoin('user as b', 'logs.e_emp_id', '=', 'b.username')

        ->where('room_no', $getCurrentRoom->room_no)
        ->whereRaw("DATE_FORMAT(logs.s_datetime, '%Y-%m-%d')='".$now."'")

        ->orderBy('logs.id', 'DESC')
        ->get();

        return response()->json(['todayLogs'=>$todayLogs]);
		// return response()->json(['reserveOnThisRoom'=>$getReservedDetails, 'Locale'=>$query, 'id'=>$id]);
		
    }

    public function getHistoryStatus(Request $r){
        // return $r->all();

        $now = Carbon::now()->format('Y-m-d');

        $getCurrentRoom = Room::where('id', '=', $r->roomId)->first();

        $start = Carbon::parse($r->startDateHistory)->startOfDay();
        $end = Carbon::parse($r->endDateHistory)->endOfDay();

        if($r->statusHistory == "all"){
            $logs = DB::connection('mysql')
            ->table('rms_logs as logs')
            ->select(
                DB::raw("IFNULL(logs.s_datetime, '') as startDate"),
                DB::raw("IFNULL(logs.e_dateTime, '') as endDate"),
                // 'tbl_room_buddies.from as source',
                DB::raw("COALESCE(tbl_room_buddies.from, logs.s_from) as sSource"),
                DB::raw("COALESCE(tbl_room_buddies.from, logs.e_from) as eSource"),
                'tbl_room_buddies.room_no as room_no',

                DB::raw("COALESCE(user.firstname, posUsera.fname, tblNull.fname, '') as firstname"),
                DB::raw("COALESCE(user.lastname, posUsera.lname, tblNull.lname, '') as lastname"),
                DB::raw("COALESCE(end.firstname, posUserb.fname, tblNull.fname, '') as efirstname"),
                DB::raw("COALESCE(end.lastname, posUserb.lname, tblNull.lname, '') as elastname"),

                // DB::raw("COALESCE(user.firstname, tblNull.firstname) as firstname"),
                // DB::raw("COALESCE(user.lastname, tblNull.lastname) as lastname"),
                // DB::raw("COALESCE(end.firstname, tblNull.firstname) as efirstname"),
                // DB::raw("COALESCE(end.lastname, tblNull.lastname) as elastname"),

                DB::raw("IFNULL(s_stat.room_status, '') as startStat"),
                DB::raw("IFNULL(e_stat.room_status, '') as endStat")
            )

            ->leftjoin('tbl_room_buddies', 'logs.id', '=', 'tbl_room_buddies.rms_logs_id')
            ->leftjoin('roomstatus as s_stat', 'logs.s_status', '=', 's_stat.id')
            ->leftjoin('roomstatus as e_stat', 'logs.e_status', '=', 'e_stat.id')
            // ->leftjoin('user', 'logs.s_emp_id', '=', 'user.username')
            // ->leftjoin('user as end', 'logs.e_emp_id', '=', 'end.username')
            ->leftjoin('user', 'logs.s_emp_id', '=', 'user.username')
            ->leftjoin('user as end', 'logs.e_emp_id', '=', 'end.username')
            ->leftjoin('vc_employees as posUsera', 'logs.s_emp_id', '=', 'posUsera.username')
            ->leftjoin('vc_employees as posUserb', 'logs.e_emp_id', '=', 'posUserb.username')
            ->leftjoin('vc_employees as tblNull', 'tbl_room_buddies.emp_id', '=', 'tblNull.username')

            ->where('logs.room_id', $r->roomId)

            ->where('logs.s_datetime', '>=', $start)
            ->where('logs.s_datetime', '<=', $end)

            ->orderBy('logs.id', 'DESC')

            ->get();

            return response()->json(['historyLogs'=>$logs]);

        }else{
            $start = Carbon::parse($r->startDateHistory)->startOfDay();
            $end = Carbon::parse($r->endDateHistory)->endOfDay();

            $logs = DB::connection('mysql')
            ->table('rms_logs as logs')

            ->select(

                DB::raw("IFNULL(logs.s_datetime, '') as startDate"),
                DB::raw("IFNULL(logs.e_dateTime, '') as endDate"),
                // 'tbl_room_buddies.from as source',
                DB::raw("COALESCE(tbl_room_buddies.from, logs.s_from) as sSource"),
                DB::raw("COALESCE(tbl_room_buddies.from, logs.e_from) as eSource"),
                'tbl_room_buddies.room_no as room_no',

                DB::raw("COALESCE(user.firstname, posUsera.fname, tblNull.fname, '') as firstname"),
                DB::raw("COALESCE(user.lastname, posUsera.lname, tblNull.lname, '') as lastname"),
                DB::raw("COALESCE(end.firstname, posUserb.fname, tblNull.fname, '') as efirstname"),
                DB::raw("COALESCE(end.lastname, posUserb.lname, tblNull.lname, '') as elastname"),

                // DB::raw("COALESCE(user.firstname, tblNull.firstname) as firstname"),
                // DB::raw("COALESCE(user.lastname, tblNull.lastname) as lastname"),
                // DB::raw("COALESCE(end.firstname, tblNull.firstname) as efirstname"),
                // DB::raw("COALESCE(end.lastname, tblNull.lastname) as elastname"),

                DB::raw("IFNULL(s_stat.room_status, '') as startStat"),
                DB::raw("IFNULL(e_stat.room_status, '') as endStat")

                // 'logs.s_status as startStatus',
                // 'logs.s_datetime as startDate',
                // 'logs.e_status as endStatus',
                // 'logs.e_dateTime as endDate',
                // 'tbl_room_buddies.from as source',
                // 'tbl_room_buddies.room_no as room_no',

                // // DB::raw("COALESCE(user.firstname, posUsera.fname, tblNull.firstname, '') as firstname"),
                // // DB::raw("COALESCE(user.lastname, posUsera.lname, tblNull.lastname, '') as lastname"),
                // // DB::raw("COALESCE(end.firstname, posUserb.fname, tblNull.firstname, '') as efirstname"),
                // // DB::raw("COALESCE(end.lastname, posUserb.lname, tblNull.lastname, '') as elastname"),


                // DB::raw("COALESCE(user.firstname, posUsera.fname, tblNull.firstname) as firstname"),
                // DB::raw("COALESCE(user.lastname, posUsera.lname, tblNull.lastname) as lastname"),
                // DB::raw("COALESCE(end.firstname, posUserb.fname, tblNull.firstname) as efirstname"),
                // DB::raw("COALESCE(end.lastname, posUserb.lname, tblNull.lastname) as elastname"),

                // 's_stat.room_status as startStat',
                // 'e_stat.room_status as endStat'
            )

            ->leftjoin('tbl_room_buddies', 'logs.id', '=', 'tbl_room_buddies.rms_logs_id')
            ->leftjoin('roomstatus as s_stat', 'logs.s_status', '=', 's_stat.id')
            ->leftjoin('roomstatus as e_stat', 'logs.e_status', '=', 'e_stat.id')
            // ->leftjoin('user', 'logs.s_emp_id', '=', 'user.username')
            // ->leftjoin('user as end', 'logs.e_emp_id', '=', 'end.username')
            ->leftjoin('user', 'logs.s_emp_id', '=', 'user.username')
            ->leftjoin('user as end', 'logs.e_emp_id', '=', 'end.username')
            ->leftjoin('vc_employees as posUsera', 'logs.s_emp_id', '=', 'posUsera.username')
            ->leftjoin('vc_employees as posUserb', 'logs.e_emp_id', '=', 'posUserb.username')
            ->leftjoin('vc_employees as tblNull', 'tbl_room_buddies.emp_id', '=', 'tblNull.username')

            ->where('logs.room_id', $r->roomId)
            ->where('s_status', '=', $r->statusHistory)
            ->where('logs.s_datetime', '>=', $start)
            ->where('logs.s_datetime', '<=', $end)

            ->orderBy('logs.id', 'DESC')

            ->get();

            return response()->json(['historyLogs'=>$logs]);
        }
        
    }
}