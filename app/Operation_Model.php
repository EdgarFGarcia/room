<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Operation_Model extends Model
{
    
    public static function ValidateGuestInfo($roomid){

        $result = DB::connection('mysql')
        ->table('rmsguestinfo')
        ->select(
            DB::raw("COUNT(*) AS 'room_count'")
        )
        ->whereRaw("RoomNo=(SELECT room_no FROM tblroom WHERE id='".$roomid."')")
        ->first();

        if($result->room_count!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function DeleteGuestInfo($roomid){

        DB::connection('mysql')
        ->table('rmsguestinfo')
        ->whereRaw("RoomNo=(SELECT room_no FROM tblroom WHERE id='".$roomid."')")
        ->delete();
        
    }

    public static function SaveGuestInfo($data){

        DB::connection('mysql')
        ->table('rmsguestinfo')
        ->insert([
            "RoomName"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "RoomNo"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "RoomType"=>DB::raw("(SELECT RoomType FROM roomrates WHERE id='".$data->rates."')"),
            "DateIn"=>DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d')"),
            "TimeIn"=>DB::raw("DATE_FORMAT(NOW(), '%h:%i:%s %p')"),
            "RateAmount"=>DB::raw("(SELECT Amount FROM roomrates WHERE id='".$data->rates."')"),
            "RateDesc"=>DB::raw("(SELECT RateDesc FROM roomrates WHERE id='".$data->rates."')"),
            "nationality"=>DB::raw("(SELECT name FROM nationality WHERE id='".$data->nationality."')"),
            "MarketSource"=>DB::raw("(SELECT MarketSource FROM marketsource WHERE id='".$data->marketsource."')"),
            "CarMake"=>DB::raw("(SELECT CarMake FROM carmake WHERE id='".$data->car."')"),
            "VehicleType"=>DB::raw("(SELECT name FROM vehicle WHERE id='".$data->vehicletype."')"),
            "PlateNo"=>$data->platenumber,
            "GSteward"=>DB::raw("(SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) FROM user WHERE username='".$data->userInfo."')"),
            "Remarks"=>$data->remarks,
            "user_id"=>$data->userInfo,
            "created_at"=>DB::raw("NOW()")
        ]);

    }

    public static function CheckInHMS($roomid, $datetime){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'room_no'
        )
        ->where('id', '=', $roomid)
        ->first();

        DB::connection('hms')
        ->table('tblroom')
        ->where('RoomNo', '=', $result->room_no)
        ->update([
            "Stat"=>"WELCOME",
            "CRoom_Stat"=>"2"
        ]);

        DB::connection('mysql')
        ->table('tblroom')
        ->where('id', '=', $roomid)
        ->update([
            "updated_at"=>$datetime
        ]);

        DB::connection('hms')
        ->table('tbl_postrans_kds')
        ->where('room_no', '".$result->room_no."')
        ->where('itemStat', 0)
        ->update([
            'itemStat' => 2
        ]);

    }

    public static function ValidateRMSLogs($roomid){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->select(
            DB::raw("COUNT(*) AS  'count_rmslogs'")
        )
        ->whereNull('e_status')
        ->where('room_id', '=', $roomid)
        ->orderBy('id', 'DESC')
        ->first();

        if($result->count_rmslogs!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function SaveRMSLogs($data, $datetime, $where){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->insertGetId([
            "room_id"=>$data->roomId,
            "p_status"=>DB::raw("(SELECT from_room_status_id FROM tblroom WHERE id='".$data->roomId."')"),
            "p_dateTime"=>DB::raw("(SELECT updated_at FROM tblroom WHERE id='".$data->roomId."')"),
            "s_status"=>$data->toStatus,
            "s_emp_id"=>$data->userInfo,
            "s_from" => $where,
            "s_dateTime"=>$datetime,
            "created_at"=>$datetime
        ]);

        return $result;

    }

    public static function SaveRMSLogsCheckIn($data, $datetime, $where){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->insertGetId([
            "room_id"=>$data->roomId,
            "p_status"=>DB::raw("(SELECT from_room_status_id FROM tblroom WHERE id='".$data->roomId."')"),
            "p_dateTime"=>DB::raw("(SELECT updated_at FROM tblroom WHERE id='".$data->roomId."')"),
            "s_status"=>"2",
            's_from' => $where,
            "s_emp_id"=>$data->userInfo,
            "s_dateTime"=>$datetime,
            "created_at"=>$datetime
        ]);

        return $result;

    }

    public static function UpdateRMSLogs($data, $datetime, $where, $endstatus){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->select(
            'id'
        )
        ->whereNull('e_status')
        ->where('room_id', '=', $data->roomId)
        ->orderBy('id', 'DESC')
        ->first();

        if($endstatus!=0){

            DB::connection('mysql')
            ->table('rms_logs')
            ->where('id', '=', $result->id)
            ->update([
                "e_status"=>$endstatus,
                "e_emp_id"=>$data->userInfo,
                "e_from" => $where,
                "e_dateTime"=>$datetime,
                "updated_at"=>$datetime
            ]);

        }
        else{

            DB::connection('mysql')
            ->table('rms_logs')
            ->where('id', '=', $result->id)
            ->update([
                "e_status"=>$data->toStatus,
                "e_emp_id"=>$data->userInfo,
                "e_from" => $where,
                "e_dateTime"=>$datetime,
                "updated_at"=>$datetime
            ]);

        }

    }

    public static function UpdateRMSLogsCheckIn($data, $datetime, $where){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->select(
            'id'
        )
        ->whereNull('e_status')
        ->where('room_id', '=', $data->roomId)
        ->orderBy('id', 'DESC')
        ->first();

        DB::connection('mysql')
        ->table('rms_logs')
        ->where('id', '=', $result->id)
        ->update([
            "e_status"=>"2",
            'e_from' => $where,
            "e_emp_id"=>$data->userInfo,
            "e_dateTime"=>$datetime,
            "updated_at"=>$datetime
        ]);


    }

    public static function GetRoomType($roomid){

        $result = DB::connection('mysql')
        ->table('tblroomtype')
        ->select(
            "room_type"
        )
        ->whereRaw("id=(SELECT room_type_id FROM tblroom WHERE id='".$roomid."')")
        ->first();

        return $result;

    }

    public static function GetRates($roomtype){

        $result = DB::connection('mysql')
        ->table('roomrates')
        ->select(
            'id',
            DB::raw("CONCAT(RateDesc, ' - Price:', Amount) AS 'price'")
        )
        ->where('RoomType', '=', $roomtype)
        ->where('Activate', '=', 'True')
        ->get();

        return $result;

    }

    public static function GetMarketSource(){


        $result = DB::connection('mysql')
        ->table('marketsource')
        ->select(
            'id',
            'MarketSource'
        )
        ->orderBy('id')
        ->get();

        return $result;


    }

    public static function GetCarmake(){

        $result = DB::connection('mysql')
        ->table('carmake')
        ->select(
            'id',
            'CarMake'
        )
        ->get();

        return $result;

    }

    public static function GetVehicleType(){

        $result = DB::connection('mysql')
        ->table('vehicle')
        ->select(
            'id',
            'name'
        )
        ->get();

        return $result;


    }

    public static function GetNationality(){

        $result = DB::connection('mysql')
        ->table('nationality')
        ->select(
            'id',
            'name'
        )
        ->get();

        return $result;

    }

    public static function ChangeStatus($data, $datetime){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'room_no'
        )
        ->where('id', '=', $data->roomId)
        ->first();

        if($data->toStatus == 1 || $data->toStatus == 16 || $data->toStatus == 27 || $data->toStatus == 28){

            DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $result->room_no)
            ->update([
                "Stat"=>"CLEAN",
                "CRoom_Stat"=>$data->toStatus
            ]);

            DB::connection('mysql')
            ->table('tblroom')
            ->where('id', '=', $data->roomId)
            ->update([
                "updated_at"=>$datetime
            ]);

        }
        else{

            DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $result->room_no)
            ->update([
                "Stat"=>"DIRTY",
                "CRoom_Stat"=>$data->toStatus
            ]);

            DB::connection('mysql')
            ->table('tblroom')
            ->where('id', '=', $data->roomId)
            ->update([
                "updated_at"=>$datetime
            ]);


        }

    }

    public static function SaveWorkLogs($data){

        $result = DB::connection('mysql')
        ->table('work_logs')
        ->insertGetId([
            "user_id"=>$data->userInfo,
            "room_number"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "status"=>"0",
            "created_at"=>DB::raw("NOW()"),
            "work_id"=>$data->toStatus
        ]);

        return $result;

    }

    public static function UpdateWorkLogs($data){

        $result = DB::connection('mysql')
        ->table('tbl_room_buddies')
        ->select(
            'work_id'
        )
        ->whereRaw("room_no=(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')")
        ->orderBy('id', 'DESC')
        ->first();

        $result = DB::connection('mysql')
        ->table('work_logs')
        ->where('id', '=', $result->work_id)
        ->update([
            "status"=>"1",
            "updated_at"=>DB::raw("NOW()")
        ]);

    }

    public static function SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $data){

        $transfrom = "WEB";
        
        DB::connection('mysql')
        ->table('tbl_room_buddies')
        ->insert([
            "rms_logs_id"=>$lastinsertedrmslogsid,
            "emp_id"=>$data->userInfo,
            "room_no"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "from"=>$transfrom,
            "work_id"=>$lastinsertedworkid,
            "created_at"=>DB::raw("NOW()")
        ]);

    }

    public static function GetPreviousRoomStatus($roomid){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'from_room_status_id'
        )
        ->where('id', '=', $roomid)
        ->first();

        return $result->from_room_status_id;

    }

    public static function ChangePreviousStatusHMS($roomid, $previousstatus, $datetime){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'room_no'
        )
        ->where('id', '=', $roomid)
        ->first();

        if($previousstatus == 1 || $previousstatus == 16 || $previousstatus == 27 || $previousstatus == 28){

            DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $result->room_no)
            ->update([
                "CRoom_Stat"=>$previousstatus,
                'Stat' => 'CLEAN'
            ]);

            DB::connection('mysql')
            ->table('tblroom')
            ->where('id', '=', $roomid)
            ->update([
                "updated_at"=>$datetime
            ]);

        }
        else{

            DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $result->room_no)
            ->update([
                "CRoom_Stat"=>$previousstatus,
                'Stat' => 'DIRTY'
            ]);

            DB::connection('mysql')
            ->table('tblroom')
            ->where('id', '=', $roomid)
            ->update([
                "updated_at"=>$datetime
            ]);

        }


    }

    public static function SaveRMSLogsPrevious($roomid, $previousstatus, $datetime, $userinfo, $where){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->insertGetId([
            "room_id"=>$roomid,
            "p_status"=>DB::raw("(SELECT from_room_status_id FROM tblroom WHERE id='".$roomid."')"),
            "p_dateTime"=>DB::raw("(SELECT updated_at FROM tblroom WHERE id='".$roomid."')"),
            "s_status"=>$previousstatus,
            "s_emp_id"=>$userinfo,
            "s_from" => $where,
            "s_dateTime"=>$datetime,
            "created_at"=>$datetime
        ]);

        return $result;
    }

    public static function UpdateRMSLogsPrevious($roomid, $previousstatus, $datetime, $userinfo, $where){

        $result = DB::connection('mysql')
        ->table('rms_logs')
        ->select(
            'id'
        )
        ->whereNull('e_status')
        ->where('room_id', '=', $roomid)
        ->orderBy('id', 'DESC')
        ->first();

        DB::connection('mysql')
        ->table('rms_logs')
        ->where('id', '=', $result->id)
        ->update([
            "e_status"=>$previousstatus,
            "e_emp_id"=>$userinfo,
            "e_from" => $where,
            "e_dateTime"=>$datetime,
            "updated_at"=>$datetime
        ]);

    }

    public static function SaveWorkLogsPrevious($data, $previousstatus){

        $result = DB::connection('mysql')
        ->table('work_logs')
        ->insertGetId([
            "user_id"=>$data->userInfo,
            "room_number"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "status"=>"0",
            "created_at"=>DB::raw("NOW()"),
            "work_id"=>$previousstatus
        ]);

        return $result;

    }

    public static function UpdateWorkLogsPrevious($data, $datetime){

        $result = DB::connection('mysql')
        ->table('tbl_room_buddies')
        ->select(
            'work_id'
        )
        ->whereRaw("room_no=(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')")
        ->orderBy('id', 'DESC')
        ->first();


        $result = DB::connection('mysql')
        ->table('work_logs')
        ->where('id', '=', $result->work_id)
        ->update([
            "status"=>"1",
            "updated_at"=>$datetime
        ]);

    }

    public static function GetPreviousRoomStatusName($statusid){

        $result = DB::connection('mysql')
        ->table('roomstatus')
        ->select(
            'room_status'
        )
        ->where('id', '=', $statusid)
        ->first();

        return $result->room_status;

    }

    public static function UserCheckWorkLogs($data){

        $resultbuddies = DB::connection('mysql')
        ->table('tbl_room_buddies')
        ->select(
            'work_id'
        )
        ->whereRaw("room_no=(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')")
        ->orderBy('id', 'DESC')
        ->first();

        $resultworklogs = DB::connection('mysql')
        ->table('work_logs')
        ->select(
            'user_id'
        )
        ->where('id', '=', $resultbuddies->work_id)
        ->first();

        if($resultworklogs->user_id!=$data->userInfo){
            return true;
        }
        else{
            return false;
        }


    }

    public static function UpdateWorkLogsPreviousOverride($data, $datetime){

        $result = DB::connection('mysql')
        ->table('tbl_room_buddies')
        ->select(
            'work_id'
        )
        ->whereRaw("room_no=(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')")
        ->orderBy('id', 'DESC')
        ->first();


        $result = DB::connection('mysql')
        ->table('work_logs')
        ->where('id', '=', $result->work_id)
        ->update([
            "status"=>"1",
            "updated_at"=>$datetime,
            "is_overriden"=>"1"
        ]);

    }

    public static function SaveWorkLogsPreviousOverride($data, $previousstatus){

        $result = DB::connection('mysql')
        ->table('work_logs')
        ->insert([
            "user_id"=>$data->userInfo,
            "room_number"=>DB::raw("(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')"),
            "status"=>"1",
            "created_at"=>DB::raw("NOW()"),
            "updated_at"=>DB::raw("NOW()"),
            "work_id"=>$previousstatus
        ]);


    }

    public static function ValidateUserWorkLogs($data){

        $result = DB::connection('mysql')
        ->table('work_logs')
        ->select(
            'user_id'
        )
        ->whereRaw("room_number=(SELECT room_no FROM tblroom WHERE id='".$data->roomId."')")
        ->orderBy('id', 'DESC')
        ->first();

        if($result->user_id!=$data->userInfo){
            return true;
        }
        else{
            return false;
        }

    }

    public static function UpdateUserInfo($data){

        DB::connection('mysql')
        ->table('tblroom')
        ->where('id', '=', $data->roomId)
        ->update([
            "from_userinfo"=>$data->userInfo
        ]);

    }

    public static function GetUserRole($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            'role_id'
        )
        ->where('username', '=', $data->userInfo)
        ->first();

        return $result->role_id;

    }

    public static function GetAllowStatus($roleid){

        $result = DB::connection('mysql')
        ->table('access_levels')
        ->select(
            DB::raw("COUNT(DISTINCT allow_status_id) AS 'allow_count'")
        )
        ->where('role_id', '=', $roleid)
        ->where('allow_status_id', '=', '20')
        ->first();

        if($result->allow_count!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function ChangeStatusValidation($data){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'tblroom.room_no',
            DB::raw("COUNT(*) AS 'check_count'"),
            'roomstatus.room_status'
        )
        ->join('roomstatus', 'roomstatus.id', '=', 'tblroom.room_status_id')
        ->where('tblroom.from_userinfo', '=', $data->userInfo)
        ->where('tblroom.room_status_id', '=', $data->toStatus)
        ->first();

        if($result->check_count!=0){

            return array([
                "validation"=>true,
                "room_no"=>$result->room_no,
                "room_status"=>$result->room_status
            ]);

        }
        else{

            return array([
                "validation"=>false,
                "room_no"=>"",
                "room_status"=>""
            ]);

        }

    }

    public static function ValidateOngoingUser($data){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'tblroom.room_no',
            'tblroom.from_userinfo',
            DB::raw("CONCAT(user.lastname, ', ',  user.firstname, ' ', user.middlename) AS 'name'"),
            'roomstatus.room_status'
        )
        ->leftjoin('user', 'user.username', '=', 'tblroom.from_userinfo')
        ->leftjoin('roomstatus', 'roomstatus.id', '=', 'tblroom.room_status_id')
        ->where('tblroom.id', '=', $data->roomid)
        ->first();

        return $result;

    }

    public static function GetUserInformation($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            '*'
        )
        ->where('username', '=', $data->userinfo)
        ->first();

        return $result;

    }

    public static function GetVehicleId($data){

        $result = DB::connection('mysql')
        ->table('vehicle')
        ->select(
            '*'
        )
        ->where('name', '=', $data->marketsource)
        ->first();

        return $result->id;

    }

    public static function GetCarId($car){

        $result = DB::connection('mysql')
        ->table('carmake')
        ->select(
            '*'
        )
        ->where('CarMake', '=', $car)
        ->first();

        return $result->id;

    }

    public static function ValidateIsPest($roomid){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'is_pest'
        )
        ->where('id', '=', $roomid)
        ->first();

        return $result;

    }

    public static function GetRoomNo($roomid){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            'room_no',
            'room_area_id',
            'room_type_id'
        )
        ->where('id', '=', $roomid)
        ->first();

        return $result;

    }

    //Nerdvana Operation
    public static function CheckInPOSNerdvana($nationality, $roomno, $roomTypeId, $room_rate_id, $room_sale_price_id, $car, $vehicleType, $marketSource, $plateNumber, $userInfo, $adult, $child, $taxInfo, $roomAreaId, $currencyId, $currencyValue, $url){

        $data = [
            'room_no'               => $roomno,
            'room_type_id'          => '',
            'room_rate_id'          => '',
            'room_rate_price_id'    => '',
            'car_id'                => $car,
            'vehicle_id'            => $vehicleType,
            'guest_type_id'         => $marketSource,
            'customer'              => "To be filled",
            'plate_no'              => $plateNumber,
            'steward'               => $userInfo,
            'checkIn'               => '',
            'checkOut'              => '',
            'user_id'               => $userInfo,
            'pos_id'                => '',
            'adult'                 => $adult,
            'child'                 => $child,
            'tax'                   => $taxInfo,
            'room_area_id'          => $roomAreaId,
            'currency_id'           => $currencyId,
            'currency_value'        => $currencyValue,
            'platform'              => 0,
            'nationality_id'        => $nationality
        ];

        //No Curl
        // $data_string = json_encode($data);
        // file_get_contents($url.'booked', null, stream_context_create(
		//     array(
		//         'http' => array(
		//         'method' => 'POST',
		//         'header' => 'Content-Type: application/json'."\r\n".
        //                     'Content-Length: '.strlen($data_string)."\r\n",
        //                     'accept: */*'."\r\n",
        //                     'accept-language: en-US,en;q=0.8'."\r\n",
		//         'content' => $data_string,
		//         ),
		//     )
        // ));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'booked',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        curl_exec($curl);

    }

}
