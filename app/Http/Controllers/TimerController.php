<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\Timer_Model as Timer;

class TimerController extends Controller
{
    
    function GetTime(Request $request){

        $time = Timer::GetTime($request->id);

        return json_encode([
            "sec"=>$time[0]->s,
            "hour"=>$time[0]->h,
            "day"=>$time[0]->d
        ]);

    }

}
