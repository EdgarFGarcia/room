<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class RoomArea extends Model
{
    //
    protected $table = 'tblroomareas';
    protected $connection = 'mysql';

    public function roomcategory(){
      return $this->hasMany('App\Model\Room\RoomByCategory','id','id');
    }

    public static function getAllAreas(){
    	return $query = RoomArea::get(['id', 'room_area']);
    }
}
