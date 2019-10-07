<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RoomMileage_Model as RoomMileage;

class RoomMileageController extends Controller
{
    
    function RoomMileage(Request $request){

        $roommileageinfo = RoomMileage::GetRoomTimeInTimeOut($request->room_no);

        //Format To 24 Hours
        $time_in_24_hour_format = date("H:i:s", strtotime($roommileageinfo->TimeIn));
        $time_out_24_hour_format = date("H:i:s", strtotime($roommileageinfo->TimeOut));

        $timein = $roommileageinfo->DateIn . " " . $time_in_24_hour_format;
        $timeout = $roommileageinfo->DateOut . " " . $time_out_24_hour_format;

        //Get Hours
        $hours = RoomMileage::GetMileageHours($timein, $timeout);

        return json_encode([
            "hours"=>$hours[0]->hours
        ]);

    }

}
