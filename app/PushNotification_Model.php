<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class PushNotification_Model extends Model
{
    
    public static function GetGroupNotification($data){

        $result = DB::connection('mysql')
        ->table('notification_schedule_info')
        ->select(
            'id',
            'group_id'
        )
        ->where('sched_id', '=', $data->schedid)
        ->get();

        $data = [];

        foreach($result as $val){

            $data[] = $val->group_id;

        }

        return $data;

    }

    public static function GetDevices($decive_type){

        $result = DB::connection('mysql')
        ->select(DB::raw("
            SELECT *, (SELECT IF(COUNT(*)!=0, 'Block', 'Allow') FROM notification_blocklists WHERE role_id=device.role_id) AS 'notif'
            FROM (SELECT tbl_device.user_id, tbl_device.device_token, user.role_id, tbl_device.device_type
            FROM tbl_device
            INNER JOIN user ON user.username=tbl_device.user_id
            WHERE tbl_device.user_id!=0) AS device
        "));

        return $result;

    }

    public static function GetMembers($data){

        $result = DB::connection('mysql')
        ->table('notification_members')
        ->select(
            'id',
            'role_id'
        )
        ->whereIn('group_id', $data)
        ->get();

        return $result;

    }

    public static function GetOccupiedRate($data){

        $result = DB::connection('notif')
        ->table('local_occupied_rate')
        ->select(
            'occupied_rate'
        )
        ->where('local', '=', $data->local)
        ->first();

        return $result->occupied_rate;

    }

    public static function GetSTLRooms($data){

        $result = DB::connection('notif')
        ->table('local_stl')
        ->select(
            '*'
        )
        ->where('local', '=', $data->local)
        ->get();

        return $result;

    }

    public static function GetDirtyRooms($data){

        $result = DB::connection('notif')
        ->table('local_dirty')
        ->select(
            '*'
        )
        ->where('local', '=', $data->local)
        ->first();

        return $result;

    }

    public static function GetLocalInformation($local){

        $stl = DB::connection('notif')
        ->table('local_stl')
        ->select(
            'local',
            DB::raw("'STL' AS 'type'"),
            DB::raw("CONCAT('Room: ', room_no, ' - ', time_difference) AS 'data'")
        )
        ->where('local', '=', $local);

        $clean = DB::connection('notif')
        ->table('local_clean')
        ->select(
            'local',
            DB::raw("'Clean Count' AS 'type'"),
            DB::raw("clean_count AS 'data'")
        )
        ->where('local', '=', $local);

        $dirty = DB::connection('notif')
        ->table('local_dirty')
        ->select(
            'local',
            DB::raw("'Dirty Count' AS 'type'"),
            DB::raw("dirty_count AS 'data'")
        )
        ->where('local', '=', $local);

        $occupied = DB::connection('notif')
        ->table('local_occupied')
        ->select(
            'local',
            DB::raw("'Occupied Count' AS 'type'"),
            DB::raw("occupied_count AS 'data'")
        )
        ->where('local', '=', $local);

        $result = DB::connection('notif')
        ->table('local_occupied_rate')
        ->select(
            'local',
            DB::raw("'Occupancy Rate' AS 'type'"),
            DB::raw("occupied_rate AS 'data'")
        )
        ->where('local', '=', $local)
        ->union($occupied)
        ->union($dirty)
        ->union($clean)
        // ->union($stl)
        ->get();

        return $result;


    }

    public static function GetLocalShiftSalesInformation($local, $shift){

        if($shift=="SHIFT : 3"){

            $result = DB::connection('notif')
            ->table('local_sales_per_shift')
            ->select(
                'ShiftNo',
                'total',
                'local'
            )
            ->where('local', '=', $local)
            ->whereRaw("DatePro=DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)")
            ->get();

            return $result;

        }
        else{

            $result = DB::connection('notif')
            ->table('local_sales_per_shift')
            ->select(
                'ShiftNo',
                'total',
                'local'
            )
            ->where('local', '=', $local)
            ->whereRaw("DatePro=DATE_FORMAT(NOW(), '%Y-%m-%d')")
            ->get();

            return $result;

        }

    }

    public static function GetCompanyInfo(){

        $result = DB::connection('dataanalytics')
        ->table('company')
        ->select(
            'id',
            'companycode'
        )
        ->where('isactive', '=', 1)
        ->get();

        return $result;

    }

    public static function GetLocalTNOGInformation($companyid, $shift){

        if($shift=="SHIFT : 3"){

            $result = DB::connection('dataanalytics')
            ->select(DB::raw("
                SELECT
                'Standard' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Standard%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
                UNION
                SELECT
                'Deluxe' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Deluxe%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
                UNION
                SELECT
                'Suite' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Suite%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY)
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
            "));
    
            return $result;

        }
        else{

            $result = DB::connection('dataanalytics')
            ->select(DB::raw("
                SELECT
                'Standard' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Standard%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
                UNION
                SELECT
                'Deluxe' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Deluxe%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
                UNION
                SELECT
                'Suite' AS 'type',
                SUM(Case When tblcustomerinfos.RoomType LIKE '%Suite%' then 1 else 0 end) as 'count'
                FROM tblcustomerinfos
                INNER JOIN tbltransactionpostings ON tbltransactionpostings.ControlNo=tblcustomerinfos.ControlNo
                WHERE tbltransactionpostings.voidStat=''
                AND  tbltransactionpostings.xdate=DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND tblcustomerinfos.stat='C/OUT' AND tblcustomerinfos.companyid='". $companyid ."'
            "));
    
            return $result;

        }

    }

}
