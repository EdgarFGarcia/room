<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\Model\Auth\UserLocale;
use App\Model\Room\RoomByCategory as Room;
use App\InspectionLink as Link;

class Transaction extends Model
{
    protected $table = 'tbl_transactions';
    protected $connection = 'mysql';

    public static function getLastRemarksOfRoom($roomNo){
         $query = Transaction::
    		select(
                '*'
    		)
    		->where('tbl_transactions.room_no', $roomNo)
            ->orderBy('tbl_transactions.created_at', 'DESC')
            ->first();

        return empty($query) ? array() : Transaction::
            select(
                'tbl_transactions.room_no as room',
                'historical_inspection.id',
                'historical_inspection.is_done',
                'historical_inspection.is_approve',
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) as name"),
                'tbl_transactions.other_remarks as notes',
                DB::raw("IFNULL(tbl_areas.name, '') AS 'areaName'"),
                DB::raw("IFNULL(tbl_components.name, '') AS 'componentName'"),
                DB::raw("IFNULL(tbl_remarks.name, '') AS 'remarksName'"),
                'tbl_transactions.created_at'
            )
            ->join('user', 'tbl_transactions.user_id', '=', 'user.username')
            ->leftJoin('tbl_links as links', 'tbl_transactions.links_id', '=', 'links.id')

            ->leftJoin('tbl_areas', 'links.area_id', '=', 'tbl_areas.id')
            ->leftJoin('tbl_components', 'links.component_id', '=', 'tbl_components.id')
            // ->join('tbl_standards', 'links.standard_id', '=', 'tbl_standards.id')
            ->leftJoin('tbl_remarks', 'links.remarks_id', '=', 'tbl_remarks.id')
            ->leftJoin('historical_inspection', 'tbl_transactions.id', '=', 'historical_inspection.transaction_id')

            ->where('tbl_transactions.created_at', $query->created_at)
            ->get();
    }

    public static function checkRoomIfSTL($roomNo){
        return $check = Room::where('room_no', $roomNo)
            ->where('room_status_id', 5)
            ->orWhere('room_status_id', 35)
            ->orWhere('room_status_id', 37)
            ->orWhere('room_status_id', 38)
            ->first();

        if($check){
            return $query2 = Transaction::
                select(
                    'tbl_transactions.room_no as room',
                    DB::raw("CONCAT(user.firstname, ', ', user.lastname) as name"),
                    'tbl_transactions.other_remarks as notes',
                    'tbl_areas.name as areaName',
                    'tbl_components.name as componentName',
                    // 'tbl_standards.name as standardName',
                    // 'tbl_standards.description as standardDescription',
                    'tbl_remarks.name as remarksName'
                )
                ->join('user', 'tbl_transactions.user_id', '=', 'user.username')
                ->join('tbl_links as links', 'tbl_transactions.links_id', '=', 'links.id')

                ->join('tbl_areas', 'links.area_id', '=', 'tbl_areas.id')
                ->join('tbl_components', 'links.component_id', '=', 'tbl_components.id')
                // ->join('tbl_standards', 'links.standard_id', '=', 'tbl_standards.id')
                ->join('tbl_remarks', 'links.remarks_id', '=', 'tbl_remarks.id')

                ->where('tbl_transactions.room_no', $roomNo)
                ->groupBy('tbl_transactions.created_at')
                // ->orderBy('tbl_transactions.created_at', 'DESC')
                ->get();
        }else{
            return false;
        }
    }
}