<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class RoomStatus extends Model
{
    //
    protected $table = 'roomstatus';
    protected $connection = 'mysql';

    public function roomcategory(){
      return $this->hasMany('App\Model\Room\RoomByCategory', 'id', 'id');
    }

    public static function getAllOnGoingStatuses(){
    	$onGoingStatuses = RoomStatus::where('room_status', 'like', '%' .  'on-going' . '%')->get();

    	return response()->json([
    		'onGoingStatuses' => $onGoingStatuses
    	]);
    }
}
