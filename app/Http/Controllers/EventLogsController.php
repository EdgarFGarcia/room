<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventLogs_Model as Event;
use App\Restriction_Model as Restriction;
use DataTables;
use Illuminate\Support\Collection;

class EventLogsController extends Controller
{
   
    function index(){

        return view('eventlogs');

    }

    function EventLogin(Request $request){

        $validateuser = Event::ValidateUser($request);

		if($validateuser){

			$userinfo = Event::EventLogin($request);

			// $access = Restriction::LoginRestrictionWeb($userinfo);

			return json_encode([
				"success"=>true,
				"userinfo"=>$userinfo
			]);

			// if($access){

			// 	return json_encode([
			// 		"success"=>true,
			// 		"userinfo"=>$userinfo
			// 	]);

			// }
			// else{

			// 	return json_encode([
			// 		"success"=>false,
			// 		"message"=>"You dont have any access"
			// 	]);

			// }

		}
		else{

			//Get User From VCITD
			$validate = Event::ValidateUserVCITD($request);

			if($validate){

				$userinfo = Event::GetUserInfo($request);

				return json_encode([
					"success"=>true,
					"userinfo"=>$userinfo
				]);

			}
			else{

				return json_encode([
					"success"=>false,
					"message"=>"Invalid Username Or Password"
				]);

			}

		}

    }

    function LoadReasons(){

        $reasons = Event::LoadReasons();

        return json_encode([
            "data"=>$reasons
        ]);

    }

    function SaveEventInformation(Request $request){

        Event::SaveEventInformation($request);

        return json_encode([
            "success"=>true,
            "message"=>"Event information has been save."
        ]);

    }

    function LoadEvents(){

        $events = Event::LoadEvents();

        $data = array();
        foreach($events as $val){

            $obj = new \stdClass;

            $obj->event = $val->event;
            $obj->remarks = $val->reason;
            $obj->createdat = $val->created_at;
			$obj->createdby = $val->user;
			$obj->color = $val->color;	
            $obj->panel = '';					

            $data[] = $obj;

        }

        $info = new Collection($data);
        return Datatables::of($info)->make(true);


	}
	
	function SaveEventReason(Request $request){

		$validation = Event::ValidateEventReason($request->reason);

		if($validation){

			return json_encode([
				"success"=>false,
				"message"=>"Reason information already exist."
			]);

		}
		else{

			Event::SaveEventReason($request->reason, $request->color);

			return json_encode([
				"success"=>true,
				"message"=>"Reason information has been save."
			]);

		}

	}

}
