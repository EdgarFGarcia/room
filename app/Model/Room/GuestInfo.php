<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class GuestInfo extends Model
{
    //
    protected $table = 'rmsguestinfo';
    protected $connection = 'mysql';

    // public static function insertToGuestInfo(){
    // 	$result = DB::connection('mysql')
	   //  	->table('rmsguestinfo')
	   //  	->select('*')->get();
    // }
}
