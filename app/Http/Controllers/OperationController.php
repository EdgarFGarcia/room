<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operation_Model as Operation;
use Carbon\Carbon;

class OperationController extends Controller
{
    
    private $cancelstatus = 53; //Cancelled
    private $overridestatus = 54; //Override

    private $where = "WEB";
    private $url = "http://192.168.1.23/pos/api/vc/ma/";

    public function __construct()
    {

        date_default_timezone_set('Asia/Manila');

    }

    function CheckIn(Request $request){

        $datetime = Carbon::now();

        //Variables
        $lastinsertedrmslogsid;
        $lastinsertedworkid;

        //Validate RMS Guest Info
        $validationguestinfo = Operation::ValidateGuestInfo($request->roomId);
        
        //Insert RMS Guest Info
        if($validationguestinfo){

            Operation::DeleteGuestInfo($request->roomId);
            Operation::SaveGuestInfo($request);

        }
        else{

            Operation::SaveGuestInfo($request);
            
        }

        //RMS Logs
        $validationrmslogs = Operation::ValidateRMSLogs($request->roomId);
        if($validationrmslogs){

            Operation::UpdateRMSLogsCheckIn($request, $datetime, $this->where);
            Operation::UpdateWorkLogs($request);

        }
        else{

            $lastinsertedrmslogsid = Operation::SaveRMSLogsCheckIn($request, $datetime, $this->where);
            $lastinsertedworkid = Operation::SaveWorkLogs($request);

        }

        //Update RMS tblroom Userinfo
        Operation::UpdateUserInfo($request);

        //POSNerdvana Check-In 
        // $roominfo = Operation::GetRoomNo($request->roomId);
        // $adult = 2;
        // $child = 0;
        // $currency_id = "PHP";
        // $currency_value = 1;
        // $room_sale_price_id = "";
        // $tax_info = "0.12";
        // Operation::CheckInPOSNerdvana($request->nationality, $roominfo->room_no, $roominfo->room_type_id, $request->rates, $room_sale_price_id, $request->car, $request->vehicletype, $request->marketsource, $request->platenumber, $request->userInfo, $adult, $child, $tax_info, $roominfo->room_area_id, $currency_id, $currency_value, $this->url);
        //8================================================================================================D

        //Change Status HMS (Check-In)
        Operation::CheckInHMS($request->roomId, $datetime);

        return json_encode([
            "success"=>true
        ]);


    }

    function OnNegoChange(Request $request){
        
        $datetime = Carbon::now();

        //Variables
        $lastinsertedrmslogsid;
        $lastinsertedworkid;

        //RMS Logs
        $validationrmslogs = Operation::ValidateRMSLogs($request->roomId);
        if($validationrmslogs){
        
            Operation::UpdateRMSLogs($request, $datetime, $this->where, 0);
            Operation::UpdateWorkLogs($request);
                
        
        }
        else{
        
           $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
           $lastinsertedworkid = Operation::SaveWorkLogs($request);

           Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);
        
        }

        //Update RMS tblroom Userinfo
        Operation::UpdateUserInfo($request);

        //Change Status HMS
        Operation::ChangeStatus($request, $datetime);

        return json_encode([
            "success"=>true
        ]);

    }

    function GetCheckInInformation(Request $request){

        $roomtype = Operation::GetRoomType($request->roomId);

        $rates = Operation::GetRates($roomtype->room_type);
        $marketsource =  Operation::GetMarketSource();
        $carmake = Operation::GetCarmake();
        $vehicletype = Operation::GetVehicleType();
        $nationality = Operation::GetNationality();

        return json_encode([
            "rates"=>$rates,
            "marketsource"=>$marketsource,
            "carmake"=>$carmake,
            "vehicletype"=>$vehicletype,
            "nationality"=>$nationality 
        ]);
        

    }

    function CancelOnNego(Request $request){
        
        $datetime = Carbon::now();

        //Variables
        $lastinsertedrmslogsid;
        $lastinsertedworkid;

        $previousstatus = Operation::GetPreviousRoomStatus($request->roomId);

        //RMS Logs
        $validationrmslogs = Operation::ValidateRMSLogs($request->roomId);
        if($validationrmslogs){
        
            
            Operation::UpdateRMSLogsPrevious($request->roomId, $this->cancelstatus, $datetime, $request->userInfo, $this->where);

            $validateuserworklogs = Operation::ValidateUserWorkLogs($request);
            if($validateuserworklogs){ //Cancelled
                Operation::UpdateWorkLogsPreviousOverride($request, $datetime);
                Operation::SaveWorkLogsPreviousOverride($request, $previousstatus);
            }
            else{ //Normal Procedure
                Operation::UpdateWorkLogsPrevious($request, $datetime);
            }
            
        
        }
        else{
        
           $lastinsertedrmslogsid = Operation::SaveRMSLogsPrevious($request->roomId, $this->cancelstatus, $datetime, $request->userInfo, $this->where);
           $lastinsertedworkid = Operation::SaveWorkLogsPrevious($request, $this->cancelstatus);

           Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);
        
        }

        //Update RMS tblroom Userinfo
        Operation::UpdateUserInfo($request);

        //Change HMS Status
        Operation::ChangePreviousStatusHMS($request->roomId, $previousstatus, $datetime);

        return json_encode([
            "success"=>true
        ]);

    }

    function ChangeStatus(Request $request){

        $datetime = Carbon::now();

        //Variables
        $lastinsertedrmslogsid;
        $lastinsertedworkid;

        //RMS Logs
        $validationrmslogs = Operation::ValidateRMSLogs($request->roomId);
        if($validationrmslogs){

            if($request->toStatus==13){ //For Inspection

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 7);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus==6){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 21);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus==19){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 56);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus == 22 || $request->toStatus == 37 || $request->toStatus == 38){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 5);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus==24){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 14);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus==51){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 50);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else if($request->toStatus==64){

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 63);
                Operation::UpdateWorkLogs($request);

                $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
                $lastinsertedworkid = Operation::SaveWorkLogs($request);

                Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

            }
            else{

                Operation::UpdateRMSLogs($request, $datetime, $this->where, 0);
                Operation::UpdateWorkLogs($request);

            }

                              
        }
        else{

            $lastinsertedrmslogsid = Operation::SaveRMSLogs($request, $datetime, $this->where);
            $lastinsertedworkid = Operation::SaveWorkLogs($request);
 
            Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);

        }

        //Update RMS tblroom Userinfo
        Operation::UpdateUserInfo($request);

        //Change HMS Status
        Operation::ChangeStatus($request, $datetime);

        return json_encode([
            "success"=>true
        ]);


    }

    function CancelOnGoings(Request $request){

        $datetime = Carbon::now();

        //Variables
        $lastinsertedrmslogsid;
        $lastinsertedworkid;

        $previousstatus = Operation::GetPreviousRoomStatus($request->roomId);
        $currentstatus = Operation::GetPreviousRoomStatusName($request->currentRoomStatus);

        //RMS Logs
        $validationrmslogs = Operation::ValidateRMSLogs($request->roomId);
        if($validationrmslogs){
        
            
            Operation::UpdateRMSLogsPrevious($request->roomId, $this->cancelstatus, $datetime, $request->userInfo, $this->where);

            $validateuserworklogs = Operation::ValidateUserWorkLogs($request);
            if($validateuserworklogs){ //Cancelled
                Operation::UpdateWorkLogsPreviousOverride($request, $datetime);
                Operation::SaveWorkLogsPreviousOverride($request, $previousstatus);
            }
            else{ //Normal Procedure
                Operation::UpdateWorkLogsPrevious($request, $datetime);
            }

        }
        else{
        
           $lastinsertedrmslogsid = Operation::SaveRMSLogsPrevious($request->roomId, $this->cancelstatus, $datetime, $request->userInfo, $this->where);
           $lastinsertedworkid = Operation::SaveWorkLogsPrevious($request, $this->cancelstatus);

           Operation::SaveWorkBuddies($lastinsertedrmslogsid, $lastinsertedworkid, $request);
        
        }

        //Update RMS tblroom Userinfo
        Operation::UpdateUserInfo($request);

        //Change HMS Status
        Operation::ChangePreviousStatusHMS($request->roomId, $previousstatus, $datetime);

        return json_encode([
            "success"=>true,
            "message"=>$currentstatus . " has been cancel."
        ]);


    }

    function CheckUserRole(Request $request){


        $roleid = Operation::GetUserRole($request);

        $allow = Operation::GetAllowStatus($roleid);

        return json_encode([
            'allow'=>$allow
        ]);

    }

    function ChangeStatusValidation(Request $request){

        if($request->toStatus==6 || $request->toStatus==13 || $request->toStatus==19){

            $val = Operation::ChangeStatusValidation($request);

            if($val[0]["validation"]){

                return json_encode([
                    "success"=>false,
                    "message"=>"You have a " . $val[0]["room_status"] . " in room ". $val[0]["room_no"] . "."
                ]);

            }
            else{

                return json_encode([
                    "success"=>true,
                    "message"=>""
                ]);

            }

        }
        else{
            
            return json_encode([
                "success"=>true,
                "message"=>""
            ]);
            
        }

    }

    function ValidateOngoingUser(Request $request){

        //Get Role ID
        $user = Operation::GetUserInformation($request);

        if($user->role_id==2 || $user->role_id==59){

            return json_encode([
                "success"=>true,
                "message"=>"",
                "bypass"=>1
            ]);

        }

        if($request->roomstatus==6 || $request->roomstatus==13 || $request->roomstatus==19){

            $val = Operation::ValidateOngoingUser($request);

            if($val->from_userinfo==$request->userinfo){

                return json_encode([
                    "success"=>true,
                    "message"=>"",
                    "bypass"=>0
                ]);

            }
            else{
               
                return json_encode([
                    "success"=>false,
                    "message"=>"This room is already in " . $val->room_status . " by " . $val->name . ".",
                    "bypass"=>0
                ]);

            }

        }
        else{
            
            return json_encode([
                "success"=>true,
                "message"=>"",
                "bypass"=>0
            ]);
            
        }


    }

    function GetVehicleId(Request $request){

        $vehicleid = Operation::GetVehicleId($request);
        $carid = Operation::GetCarId("None");

        return json_encode([
            "vehicleid"=>$vehicleid,
            "carid"=>$carid
        ]);

    }

    function ValidateIsPest(Request $request){

        $roominfo = Operation::ValidateIsPest($request->roomId);

        if($roominfo->is_pest==1){
            
            return json_encode([
                "success"=>false,
                "message"=>"This room is under recovery."
            ]);

        }
        else{
            
            return json_encode([
                "success"=>true
            ]);

        }

    }


}
