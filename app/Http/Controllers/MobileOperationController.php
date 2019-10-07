<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MobileOperation_Model as Mobile;

class MobileOperationController extends Controller
{
    
    public function __construct()
    {

        date_default_timezone_set('Asia/Manila');

    }

    //Params
    // user_id
    // room_id
    // mobile
    // start_time
    // end_time
    function SaveOccupiedAuditInspection(Request $request){

        $val = $request->mobile=="ios" ? 1 : 1000;

        //Convert Long To Date Time
        $start = $request->start_time / $val;
        $end = $request->end_time / $val;

        $starttime = date("Y-m-d H:i:s", $start);
        $endtime = date("Y-m-d H:i:s", $end);

        $request->start_time = $starttime;
        $request->end_time = $endtime;

        Mobile::SaveOccupiedAuditInspection($request);


    }

    //Params
    // room_id
    function ValidateOccupiedRoom(Request $request){

        $rmsroom = Mobile::GetRMSRoomInfo($request->room_id);
        $hmsroom = Mobile::GetHMSRoomInfo($rmsroom->room_no);

        if($hmsroom->Stat=="OCCUPIED"){

            return response()->json([
                "success"=>true,
                "message"=>"Room is in use"
            ], 200);

        }
        else{

            return response()->json([
                "success"=>false,
                "message"=>""
            ], 200);

        }

    }

    //Params
    // mobile_id
    // version
    // version_code
    function CheckMobileVersion(Request $request){

        $mobile = Mobile::CheckMobileVersion($request->mobile_id);

        if($request->version_code>$mobile->versioncode){ //Update Version

            Mobile::UpdateMobileVersion($request->mobile_id, $request->version, $request->version_code);

            return response()->json([
                "success"=>true
            ], 200);

        }
        else{

            if($request->version_code==$mobile->versioncode){
                
                return response()->json([
                    "success"=>true
                ], 200);

            }
            else{

                return response()->json([
                    "success"=>false
                ], 200);

            }
            
        }



    }


}
