<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class HistoricalInspection extends Model
{
    //
    protected $table = "historical_inspection";
    protected $connection = "mysql";

    public static function initiateSave($id, $newFormat, $userId, $room_id){
        if()
    	$info = array();
    	return $save = array_push($info, array(
    		'user_id' => $userId,
    		'room_id' => $room_id,
    		'batch_number' => $newFormat,
    		'transaction_id' => $id
    	));

    	return $saveInfo = DB::table('historical_inspection')->insert($save);
    }
}
