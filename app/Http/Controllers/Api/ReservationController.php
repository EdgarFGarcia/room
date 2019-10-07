<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Model\VCReserve\LocaleID;
use App\Model\Room\RoomByCategory;
use App\Model\Room\RoomType;
use App\Model\VCReserve\ReservationType;
use App\Model\VCReserve\Categories;
use App\Reservation_Model as Reservation;
use App\Restriction_Model as Restriction;

use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
	
	function index($id=null){

		$room_no = RoomByCategory::select('room_no')->where('id', '=', $id)->get();

		return view('reservation')->with('id', $id)->with('room_no', $room_no[0]->room_no);

	}

    function getAreaAndDetails(Request $request){

    	$localeQuery = LocaleID::select('*')->first();
    	$getAllRooms = RoomByCategory::where('id', $request->roomId)->first();
    	$getRoomType = RoomType::where('id', $getAllRooms->room_type_id)->first();
    	$getType = ReservationType::get();
    	$categories = Categories::get();

    	return response()->json(['locale'=>$localeQuery, 'getAllRooms'=>$getAllRooms, 'getRoomType'=>$getRoomType, 'getType'=>$getType, 'categories'=>$categories]);
	}
	
	function LoadReservation(Request $request){

		$data = array();
		$reservation = Reservation::LoadReservation($request);

        foreach($reservation as $val){

            $data[] = array(
                'id' => $val->Reservation_ID,
                'title' => 'Room #' . $val->Room_Number . ': Reserved by `' . $val->Last_Name . ', ' . $val->First_Name . '`',
                'start' => $val->Reserve_Date . ' ' . $val->Time,
				'end' => $val->End_Date . ' ' . $val->Time,
				'color' => $val->color
            );

        }

        return json_encode($data);

	}

	function LoadReservationType(){

		$reservationtype = ReservationType::select('*')
		->where('active', '=', '1')
		->get();

		return json_encode([
			"data"=>$reservationtype
		]);

	}

	function LoadReservationCategory(){

		$reservationcategory = Categories::select('*')
		->where('active', '=', '1')
		->get();

		return json_encode([
			"data"=>$reservationcategory
		]);

	}

	function UpdateReservationInformation(Request $request){

		Reservation::UpdateReservationInformation($request);

        return json_encode([
            "success"=>true,
            "message"=>"Reservation information has been update."
        ]);

	}

	function ResLogin(Request $request){

		$validateuser = Reservation::ValidateUser($request);

		if($validateuser){

			$userinfo = Reservation::ResLogin($request);

			$access = Restriction::LoginRestriction($userinfo);

			if($access){

				return json_encode([
					"success"=>true,
					"userinfo"=>$userinfo
				]);

			}
			else{

				return json_encode([
					"success"=>false,
					"message"=>""
				]);

			}

		}
		else{

			//Get User From VCITD
			$validate = Reservation::ValidateUserVCITD($request);

			if($validate){

				$userinfo = Reservation::GetUserInfo($request);

				dd($userinfo);

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

	function LoadReservationRate(Request $request){

		$rates = Reservation::LoadReservationRate($request->id);

		return json_encode([
			"data"=>$rates
		]);

	}

	function AddReservation(Request $request){
		
		// 12-hour time to 24-hour time 
		$order_ref = $this->GenerateOrderRef();
		$request->timeInReservation = date("H:i:s", strtotime($request->timeInReservation));
		$rate = Reservation::GetReservationRate($request);
		$settings = Reservation::GetLocalID();
		
		Reservation::AddReservation($request, $rate, $settings, $order_ref);

		return json_encode([
			"success"=>true,
			"message"=>"Reservation information has been save."
		]);

	}

	function GenerateOrderRef($length = 32) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;

	}

}
