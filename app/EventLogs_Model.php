<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class EventLogs_Model extends Model
{
    
    public static function ValidateUser($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->get();

        if($result[0]->user_count=="1"){
            return true;
        }   
        else{
            return false;
        } 

    }

    public static function EventLogin($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
           'username',
           'firstname',
           'middlename',
           'lastname',
           'role_id'
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->first();

        return $result;

    }

    public static function ValidateUserVCITD($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->first();

        if($result->user_count!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function GetUserInfo($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
           'username',
           'fname',
           'lname',
           'mname',
           'role_id'
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->get();

        return $result;

    }

    public static function LoadReasons(){

        $result = DB::connection('mysql')
        ->table('events_reasons')
        ->select(
            'id',
            'reason'
        )
        ->get();

        return $result;

    }

    public static function SaveEventInformation($data){

        DB::connection('mysql')
        ->table('events')
        ->insert([
            "username"=>$data->userid,
            "event_id"=>$data->reasonid,
            "reason"=>$data->reason,
            "created_at"=>DB::raw("NOW()")
        ]);

    }

    public static function LoadEvents(){

        $result = DB::connection('mysql')
        ->table('events')
        ->select(
            'events.id',
            'events_reasons.reason as event',
            DB::raw("CONCAT(user.lastname, ', ', user.firstname) AS 'user'"),
            'events.reason',
            DB::raw("DATE_FORMAT(events.created_at, '%Y-%m-%d') AS 'created_at'"),
            'events_reasons.color'   
        )
        ->join('events_reasons', 'events_reasons.id', '=', 'events.event_id')
        ->join('user', 'user.username', '=', 'events.username')
        ->get();

        return $result;

    }

    public static function ValidateEventReason($reason){

        $result = DB::connection('mysql')
        ->table('events_reasons')
        ->select(
            DB::raw("COUNT(*) AS 'reasoncount'")
        )
        ->where('reason', '=', $reason)
        ->first();

        if($result->reasoncount!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function SaveEventReason($reason, $color){

        DB::connection('mysql')
        ->table('events_reasons')
        ->insert([
            "reason"=>$reason,
            "color"=>$color,
            "created_at"=>DB::raw("NOW()")
        ]);

    }

}
