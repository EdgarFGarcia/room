<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use DB;

class OnlineReservation extends Model
{
    // DB = > vcreserve

	public static function getRoomNo($data){
		return $query = DB::connection('mysql')
		->table('tblroom')
		->select('room_no')
		->where('id', $data)
		->get();
	}

    public static function getReservationFromVCReserve($roomNo, $date, $localeID){
    	return $query = DB::connection('vcreserve')
    	->table('room_reservation_details')
    	->select('*')
    	->where('Locale_ID', '=', $localeID)
    	->where('Room_Number', $roomNo)
    	->whereRaw("DATE_FORMAT(Reserve_Date, '%Y-%m')='$date'")
    	->get();
    }
}
