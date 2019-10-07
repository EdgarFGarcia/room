<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class RmsRoom extends Model
{
    public static function hasOnGoingWork($userId) {
        
    	$hasWork = false;
        $roomNumber = "0";

        
    	//6 == on going gc
    	//13 == on going inspection
    	//19 == on going rc
    	//20 == on going nego
    	//22 == on going rectification
    	//24 == on going maintenance
    	//40 == room check
    	//51 == on going jor
		$unallowedOngoingIds = array(6, 13, 19);
    	$room = DB::connection('mysql')->table('tblroom');
    	$record = $room->select('*')
    		->where('from_userinfo', '=', $userId)
    		->whereIn('room_status_id', $unallowedOngoingIds)
    		->get();
		if (count($record) > 0) {
            $hasWork = true;
            $roomNumber = $record[0]->room_no;
		}

        $response = array(['hasWork' => $hasWork, 'roomNumber' => $roomNumber]);


    	return $response;
    }
}
