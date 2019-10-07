<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Room\AccessLevel;
use App\Model\Room\RoomByCategory as Rooms;
use App\Model\Auth\UserLocale as check;
use App\Model\Auth\UserCheck;
use App\Restriction_Model as Restriction;
use App\Http\Controllers\Api\ApiController;
use App\Model\VCReserve\LocaleID as Locale;
use App\Model\Room\RoomStatus;
use App\PushNotification_Model as Push;

use Response;
use DB;

class MobileController extends Controller
{
    //
    /*
    * route => mobile/allowedStatus
    * room_number (int)
    *
    */
    public function __construct()
    {
        //Set Limit For Execution Time
        set_time_limit(0);
    }

    public function allowedStatus(Request $r){
    	$getCurrentRoomStatus = Rooms::where('room_no', $r->room_number)->first();
    	$user = check::select
    		(
    			'access_levels.room_status_id as current_status', 
    			'access_levels.allow_status_id as next_status', 
    			'currentstatus.room_status as current_status_desc',
    			'nextstatus.room_status as next_status_desc'
    		)

    			->where('user.username', $r->user_id)
    			->where('access_levels.room_status_id', $getCurrentRoomStatus->room_status_id)

    			->join('access_levels', 'user.role_id', '=', 'access_levels.role_id')
    			->join('roomstatus as currentstatus', 'access_levels.room_status_id', '=', 'currentstatus.id')
    			->join('roomstatus as nextstatus', 'access_levels.allow_status_id', '=', 'nextstatus.id')
    			->get();

    	return response()->json(['accesslevel'=>$user, 'getRoom'=>$getCurrentRoomStatus], 200);
    }

    /*
    * route => loginMobile
    * username (int)
    * password (int)
    */
    public function MobileLogin(Request $r){

        $this->rule = array(
            'username' => 'required',
            'password' => 'required'
            // 'device_type' => 'required',
            // 'device_token' => 'required'
        );

        $this->validate($r->all(), $this->rule);

        if($this->data['error']['status'] == false){

            $checkIfExistOnLocale = check::checkIfExist($r->username, $r->password);
            // dd($checkIfExistOnLocale);
            if($checkIfExistOnLocale['response'] == "userDoesNotExist"){
                // search on main server and check if active and has a valid role id
                $checkInMain = UserCheck::getUserInfo($r->username);
                $insertUserToLocale = check::insertUser($checkInMain);
                if($insertUserToLocale){
                    $this->MobileLogin($r);
                }

            }else if($checkIfExistOnLocale['response'] == "Proceed"){
                // proceed to login
                $info = $checkIfExistOnLocale['query'];

                $access = Restriction::LoginRestrictionMobile($info);

                if($access){

                    $locale = Locale::select('id', 'local_id', 'local_code', 'user_restricted')->first();

                    // $emit = ApiController::emitOnline($r, $locale->local_id);

                    $loggedIn = check::where('username', '=', $r->username)
                        ->update(['is_logged_in' => 1]);

                    $this->data['response'] = array(
                        'whereAmI'=>$locale, 
                        'error_message'=>'',
                        'query'=>$info, 
                        'access'=>$access,
                        'isLoggedIn'=>$loggedIn
                    );

                    return response()->json($this->data, $this->statusCode);

                }
                else{

                    $this->responseError(
                        'No Access'
                    );

                    return response()->json($this->data, $this->statusCode);

                }

            }
            else if($checkIfExistOnLocale['response'] == "InvalidUsernamePassword"){

                $this->responseError(
                    'Invalid Username Or Password'
                );

                return response()->json($this->data, $this->statusCode);

            }

            // OLD CODE
            // $query = check::
            // select(
            //     'user.username as id',
            //     DB::raw("CONCAT(user.firstname, ' ', user.lastname) as name"),
            //     'user.role_id as role_id',
            //     'roles.role as rolename',
            //     'user.email as email',
            //     'user.password as password',
            //     'user.firstname as firstname',
            //     'user.lastname as lastname',
            //     'user.middlename as middlename'
            // )
            // ->join('roles', 'user.role_id', '=', 'roles.id')
            // ->where('user.username', '=', $r->username)
            // // ->where('user.password', '=', $r->password)
            // ->first();

            // if(empty($query)){
            //     // return "test";
            //     $checkUserInMain = UserCheck::where('username', $r->username)
            //     ->where('password', $r->password)->first();
            //     // check if user is still active and has role_id on Main Server

            //     if(empty($checkUserInMain)){
            //         $this->responseError("User is Inactive");
            //         $this->data['response'] = array('dummy_key'=>'ignore');
            //         return response()->json($this->data, $this->statusCode);
            //     }

            //     if(($checkUserInMain->status == 'Active') && ($checkUserInMain->role_id == 0)){
            //         $this->data['response'] = array('dummy_key'=>'ignore');
            //         $this->responseError("User Has No Role ID Contact IT");
            //         return response()->json($this->data, $this->statusCode);
            //     }

            //     if(($checkUserInMain->status == 'Active') && ($checkUserInMain->role_id == "")){
            //         $this->data['response'] = array('dummy_key'=>'ignore');
            //         $this->responseError("Your Role ID is blank! Please Contact IT");
            //         return response()->json($this->data, $this->statusCode);
            //     }


            //     $firstname = trim($checkUserInMain->fname, " ");
            //     $middlename = trim($checkUserInMain->mname, " ");
            //     $lastname = trim($checkUserInMain->lname, " ");

            //     $insertUserToLocale = DB::connection('mysql')
            //     ->table('user')
            //     ->insert([
            //       'username' => $checkUserInMain->username,
            //       'password' => $checkUserInMain->password,
            //       'email' => $checkUserInMain->email,
            //       'role_id' => $checkUserInMain->role_id,
            //       'is_logged_in' => 1,
            //       'firstname' => $firstname,
            //       'middlename' => $middlename,
            //       'lastname' => $lastname,
            //       'status' => $checkUserInMain->status,
            //       'created_at' => DB::raw("NOW()")
            //     ]);

            //     $checkIfUserExistOnLocale = check::where('username', '=', $r->username)->first();

            //     if(!empty($checkIfUserExistOnLocale)){
            //         return $this->MobileLogin($r);
            //     }

            // }else if(!empty($query)){
            //     // return "not empty";
            //     //check if role_id is synced to vc_employees
            //     $checkConnection = check::checkConnection();

            //     if($checkConnection){
            //         $usercheck = UserCheck::getUserInfo($r->username);
            //         // if($usercheck->rold_id == 0){
            //         //     $this->responseError("User Has No Role ID Contact IT");
            //         //      return response()->json($this->data, $this->statusCode);
            //         // }
            //         if(($query->role_id != $usercheck->role_id) || ($query->firstname != $usercheck->fname) || ($query->lastname != $usercheck->lname) || ($query->middlename != $usercheck->mname)
            //             || ($query->status != $usercheck->status) ){
            //             $updateUserRoleLocally = check::where('username', $r->username)
            //             ->update([
            //                 'role_id' => $usercheck->role_id,
            //                 'firstname' => $usercheck->fname,
            //                 'middlename' => $usercheck->mname,
            //                 'lastname' => $usercheck->lname,
            //                 'status' => $usercheck->status
            //             ]);
            //             if($updateUserRoleLocally){
            //                 return $this->MobileLogin($r);
            //             }
            //         }
            //     }

            //     if($r->password != $query->password){
            //         $this->data['response'] = array('dummy_key'=>'ignore');
            //         $this->responseError("wrong username or password");
            //         return response()->json($this->data, $this->statusCode);
            //     }

            //     $access = Restriction::LoginRestrictionMobile($query);
            //     if($access){
            //         $checkIfUserExistOnLocal = check::where('username', $r->username)
            //         ->select('user.role_id as role_id', 'roles.role as role')
            //         ->join('roles', 'user.role_id', '=', 'roles.id')
            //         ->first();

            //         $locale = Locale::select('id', 'local_id', 'local_code', 'user_restricted')->first();

            //         // $deviceType = check::userIdToken($r->username);

            //         if($checkIfUserExistOnLocal){
            //             $emit = ApiController::emitOnline($r, $locale->local_id);

            //             $loggedIn = check::where('username', '=', $r->username)
            //                 ->update(['is_logged_in' => 1]);

            //             $this->data['response'] = array(
            //                 'whereAmI'=>$locale, 
            //                 'error_message'=>'',
            //                 'query'=>$query, 
            //                 'access'=>$access,
            //                 'isLoggedIn'=>$loggedIn
            //             );
            //         }else{
            //             if((empty($query->id)) 
            //                 && (empty($query->email)) 
            //                 && (empty($query->role_id))
            //                 && (empty($query->password))
            //                 && (empty($query->fname))
            //                 && (empty($query->mname))
            //                 && (empty($query->lname))

            //             ){
            //                 $this->data['response'] = array('dummy_key'=>'ignore');
            //                 $this->responseError("User does not exist");
            //                 return response()->json($this->data, $this->statusCode);
            //             }
            //         }
            //     }else{
            //         $this->responseError("You don't have an access here");
            //     }
            // }else{
            //     $this->responseError("wrong username or password");
            // }   

                  
        }

    }

    /*
    *   mobile/getAllGoingStatus
    *   get all "on-going status on database"
    */
    public function onGoingStatuses(Request $r){
        return $query = RoomStatus::getAllOnGoingStatuses();
    }

    public function roomStatusForMobile(){
        $roomStatusForMobile = Rooms::roomStatusesForReportsMobile();
        return response()->json([
            'roomStatuses' => $roomStatusForMobile,
        ]);
    }

    // public static function updateTblDevice(Request $r){
    //     $checkTable = check::userIdToken($r->username);
    // }

    public function addOrEditDevice(Request $r){

        check::deviceInsertInfoOrUpdate($r->deviceType, $r->deviceToken, $r->username);
        return json_encode([
            "success"=>true
        ]);

        
    }

    public function logoutZero(Request $r){

        check::setZero($r->username, $r->deviceToken);

    }

    //Push Notification Occupied Rate
    public function NotifOccupiedRate(Request $request){

        //Variables
        $message = "";
        $header;
        $ios = [];
        $android = [];

        $device = Push::GetDevices();
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);
        $occupiedrate = Push::GetOccupiedRate($request);

        // $localdata = Push::GetLocalInformation($request);

        // foreach($localdata as $val){

        //     $message = $message . $val->type . ": " .  $val->data . PHP_EOL;

        // }

        //Set
        $header = $request->local;
        $message = $occupiedrate . '%';

        foreach($device as $val){

            if($val->notif=="Allow"){

                if($val->device_type=="Android"){

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $android[] = $val->device_token;

                        }

                    }
            

                }
                else{ //IOS

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $ios[] = substr($val->device_token, 1, -1);

                        }

                    }

                }

            }

        }

        //Send
        $this->dionepush($message, $header, $android);
        $this->__push_notification($message, $ios);

    }

    public function NotifSTLRooms(Request $request){

        //Variables
        $message = "";
        $header;
        $ios = [];
        $android = [];

        $device = Push::GetDevices();
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);
        $stlrooms = Push::GetSTLRooms($request);

        foreach($stlrooms as $val){

            $message = $message . "Room #: " . $val->room_no . " Time Length: " . $val->time_difference . PHP_EOL;

        }

        //Set
        $header = "STL Rooms";

        foreach($device as $val){

            if($val->notif=="Allow"){

                if($val->device_type=="Android"){

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $android[] = $val->device_token;

                        }

                    }
            

                }
                else{ //IOS

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $ios[] = substr($val->device_token, 1, -1);

                        }

                    }

                }

            }

        }

        //Send
        $this->dionepush($message, $header, $android);
        $this->__push_notification($message, $ios);


    }

    public function NotifDirtyRooms(Request $request){

        //Variables
        $message = "";
        $header;
        $ios = [];
        $android = [];

        $device = Push::GetDevices();
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);
        $dirtyrooms = Push::GetDirtyRooms($request);

        //Set
        $header = "Dirty Room Count";
        $message = $dirtyrooms->dirty_count;

        foreach($device as $val){

            if($val->notif=="Allow"){

                if($val->device_type=="Android"){

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $android[] = $val->device_token;

                        }

                    }
            

                }
                else{ //IOS

                    foreach($push as $role){
                        
                        if($role->role_id==$val->role_id){

                            $ios[] = substr($val->device_token, 1, -1);

                        }

                    }

                }

            }

        }

        //Send
        $this->dionepush($message, $header, $android);
        $this->__push_notification($message, $ios);


    }

    public function AutopilotNotificationAndroid(Request $request){

        //Variables
        $message = "";
        $header = "";
        $android = [];
        $local = array("VCHI", "VCPA", "VCLP", "VCBA", "VCNE", "VCMA", "VCSF");

        $device = Push::GetDevices($request->devicetype);
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);

        foreach($device as $val){

            if($val->notif=="Allow"){

                foreach($push as $role){
                        
                    if($role->role_id==$val->role_id){

                        if($val->role_id==22 || $val->role_id==46 || $val->role_id==27){

                            $android[] = $val->device_token;
                                
                            foreach($local as $branch){

                                $localdata = Push::GetLocalInformation($branch);
                                $header = $branch;
                                $message = "";
                                foreach($localdata as $data){

                                    $message = $message . $data->type . ": " .  $data->data . PHP_EOL;

                                }

                                //Send
                                $this->dionepush($message, $header, $android);

                            }


                        }
                        else{

                            $android[] = $val->device_token;

                            $localdata = Push::GetLocalInformation($request->local);

                            $header = $request->local;
                            $message = "";

                            foreach($localdata as $data){

                                $message = $message . $data->type . ": " .  $data->data . PHP_EOL;

                            }

                            //Send
                            $this->dionepush($message, $header, $android);


                        }


                    }

                }

            }

        }

    }

    public function AutopilotNotificationIOS(Request $request){

        //Variables
        $message = "";
        $header = "";
        $ios = [];
        $local = array("VCHI", "VCPA", "VCLP", "VCBA", "VCNE", "VCMA", "VCSF");

        $device = Push::GetDevices($request->devicetype);
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);

        foreach($device as $val){

            if($val->notif=="Allow"){
            
                foreach($push as $role){
                        
                    if($role->role_id==$val->role_id){

                        if($val->role_id==22 || $val->role_id==46 || $val->role_id==27){

                            $ios[] = substr($val->device_token, 1, -1);

                            foreach($local as $branch){

                                $localdata = Push::GetLocalInformation($branch);
                                $header = $branch;
                                $message = "";
                                foreach($localdata as $data){

                                    $message = $message . $data->type . ": " .  $data->data . ", ";

                                }

                                //Send
                                $this->__push_notification(
                                    array(
                                        "title" 	=> $header,
                                        "subtitle" 	=> "",
                                        "body" 		=> $message
                                    ), 
                                    $ios
                                );

                            }


                        }
                        else{

                            $ios[] = substr($val->device_token, 1, -1);
                                
                            $localdata = Push::GetLocalInformation($request->local);

                            $header = $request->local;
                            $message = "";

                            foreach($localdata as $data){

                                $message = $message . $data->type . " - " .  $data->data . ", ";

                            }

                            //Send
                            $this->__push_notification(
                                array(
                                    "title" 	=> $header,
                                    "subtitle" 	=> "",
                                    "body" 		=> $message
                                ), 
                                $ios
                            );

                        }



                    }

                }


            }

        }


    }

    public function NotifAutopilotShiftAndroidIOS(Request $request){

        //Variables
        $message = "";
        $header = "";
        $android = [];
        // $local = array("VCHI", "VCPA", "VCLP", "VCBA", "VCNE", "VCMA", "VCSF");
        $local = Push::GetCompanyInfo();

        $device = Push::GetDevices($request->devicetype);
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);

        foreach($device as $val){

            if($val->notif=="Allow"){

                foreach($push as $role){
                        
                    if($role->role_id==$val->role_id){

                        if($val->role_id==22 || $val->role_id==46 || $val->role_id==27){

                            if($val->device_type=="Android"){
                                $android[] = $val->device_token;
                            }
                            else if($val->device_type=="ios"){
                                $ios[] = substr($val->device_token, 1, -1);
                            }
                                
                            foreach($local as $branch){

                                $localdata = Push::GetLocalShiftSalesInformation($branch->companycode, $request->description);
                                $header = $branch->companycode;
                                $message = "";
                                foreach($localdata as $data){

                                    if($val->device_type=="Android"){
                                        $message = $message . $data->ShiftNo . " - " .  number_format($data->total, 2) . PHP_EOL;
                                    }
                                    else if($val->device_type=="ios"){
                                        $message = $message . $data->ShiftNo . " - " .  number_format($data->total, 2) . ", ";
                                    }

                                }

                                $tnogdata = Push::GetLocalTNOGInformation($branch->id, $request->description);

                                foreach($tnogdata as $tnog){

                                    if($val->device_type=="Android"){
                                        $message = $message . $tnog->type . " - " . $tnog->count . PHP_EOL;
                                    }
                                    else if($val->device_type=="ios"){
                                        $message = $message . $tnog->type . " - " . $tnog->count . ", ";
                                    }

                                }

                                if($val->device_type=="Android"){

                                    //Send Android
                                    $this->dionepush($message, $header, $android);

                                }
                                else if($val->device_type=="ios"){

                                    //Send IOS
                                    $this->__push_notification(
                                        array(
                                            "title" 	=> $header,
                                            "subtitle" 	=> "",
                                            "body" 		=> $message
                                        ), 
                                        $ios
                                    );

                                }


                            }


                        }


                    }

                }

            }

        }


    }

    public function NotifAutopilotShiftIOS(Request $request){

        //Variables
        $message = "";
        $header = "";
        $ios = [];
        // $local = array("VCHI", "VCPA", "VCLP", "VCBA", "VCNE", "VCMA", "VCSF");
        $local = Push::GetCompanyInfo();

        $device = Push::GetDevices($request->devicetype);
        $group = Push::GetGroupNotification($request);
        $push = Push::GetMembers($group);

        foreach($device as $val){

            if($val->notif=="Allow"){
            
                foreach($push as $role){
                        
                    if($role->role_id==$val->role_id){

                        if($val->role_id==22 || $val->role_id==46 || $val->role_id==27){

                            $ios[] = substr($val->device_token, 1, -1);

                            foreach($local as $branch){

                                $localdata = Push::GetLocalShiftSalesInformation($branch->companycode, $request->description);
                                $header = $branch->companycode;
                                $message = "";
                                foreach($localdata as $data){

                                    $message = $message . $data->ShiftNo . " - " .  number_format($data->total, 2) . ", ";

                                }

                                $tnogdata = Push::GetLocalTNOGInformation($branch->id, $request->description);

                                foreach($tnogdata as $tnog){

                                    $message = $message . $tnog->type . " - " . $tnog->count . ", ";

                                }

                                //Send
                                $this->__push_notification(
                                    array(
                                        "title" 	=> $header,
                                        "subtitle" 	=> "",
                                        "body" 		=> $message
                                    ), 
                                    $ios
                                );

                            }


                        }

                    }

                }


            }

        }

    }

    public function preNotif(Request $r){

        $message = $r->message;
        $header = $r->header;

        $devicesToken = DB::connection('mysql')
        ->table('tbl_device')
        ->select('*')
        ->get();

        $ios = [];
        $android = [];

        foreach($devicesToken as $out){

            if ($out->device_type == "ios")
            {

                $ios[] = substr($out->device_token, 1, -1);
                
            }
            if($out->device_type == "Android"){

                $android[] = $out->device_token;

            }

        }

        $this->dionepush($message, $header, $android);
        $this->__push_notification($message, $ios);

    }

    public function dionepush($messages, $headers, $id){
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $fields = array (
                'registration_ids' =>
                       $id,
                'data' => array (
                        "header" => $headers,
                        "message" => $messages
                )
        );

        $fields = json_encode ( $fields );

        $headers = array (
                'Authorization: key=' . 'AAAAOUcB5w0:APA91bF2pI6TGvBRX8Q1kTLQIdLPi6CpBR8wL01NGEz6yjFbwO8t1tCmbWRf1EfbQBet_i9pObSxMct83YPloHlKnOb3lFTc-deGh7DfkzjlJ4bTvS3H2KNjnGiRiZv54SnvGjdbQFXi',
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $result = curl_exec ( $ch );
        echo $result;
        curl_close ( $ch );
    }

    //NEW IOS Push Notif
    /***
    * Function: template_user_info 
    * 
    * Parameter
    *   • $message_input
    *		- String message
    *   • $token_array
    *   	- Array contains: `token`  ex. "<xxxxxxx 9b0f527f xxxxxxxx 2727ed28 xxxxxxxx 4e693a61 xxxxxxx ac2f7dbb>"
    * 		Note:
    *		this removes the '<' and '>' to make the device token valid
    *		`$device_token = str_replace(">","",str_replace("<","",$device_token));`
    * ---
    */
    public function __push_notification($input, $token_array)
	{
		$push_config = array(

			"development" => array(
				"status"    => true,
				"cert" 		=> realpath('pushcert.pem'),
				"pass" 		=> '',
				"server" 	=> 'ssl://gateway.sandbox.push.apple.com:2195'
			),
			"production"  => array(
				"status"    => true,
				"cert" 		=> realpath('push.pem'),
				"pass" 		=> '',
				"server" 	=> 'ssl://gateway.push.apple.com:2195'
			)
		);

		foreach ($push_config as $key => $config) 
		{
			if ($config['status'] === true)
				$this->__exe_push_notification($input, $config, $token_array);
		}
	}

    private function __exe_push_notification($input, $config, $token_array)
	{
		$title 		= stripslashes($input['title']);
		$subtitle 	= stripslashes($input['subtitle']);
		$body 		= stripslashes($input['body']);

		$cert 	= $config['cert'];
		$pass 	= $config['pass'];
		$server = $config['server'];

		$payload = '{
	        "aps" :
	            {
	                "alert" : "'.$body.'",
	                "badge" : 1, 
	                "sound" : "bingbong.aiff"
	            }
	    }';

	    
    
        if (!empty($title))
        {
            $payload = '{
                "aps" :
                {
                    "alert" :
                    {
                        "title"     : "'.$title.'",
                        "subtitle"  : "'.$subtitle.'",
                        "body"      : "'.$body.'"
                    },
                    "mutable-content" : 1,
                    "badge" : 1
                }
            }';
        }

        // $p = count(unpack('C*', $payload));
        // $b = count(unpack('C*', $body));
        // dd($p . " " . $b);

	    $ctx = stream_context_create();
	    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert);
	    stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);

	    $fp = stream_socket_client($server, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	    if (!$fp) 
	    {
	        // echo "Failed to connect $err $errstr <br>";

	        return;
	    }
	    else 
	        // echo "Post notification sent<br>";

	    $dev_array = array();

	    $dev_array = $token_array;

	    foreach ($dev_array as $device_token) 
	    {
	    	$device_token = str_replace(">","",str_replace("<","",$device_token));

	        $msg =  chr(0) .
	                pack("n", 32) . 
	                pack('H*', str_replace(' ', '', $device_token)) . 
	                pack("n", strlen($payload)) . 
	                $payload;

	        // echo "sending message :" . $payload . "n";

	        fwrite($fp, $msg);
	    }
	    fclose($fp);
	}
    
    //=====================================================================================

    //OLD IOS Push Notif
    // public function __push_notification($message_input, $token_array) {
    //     $message = stripslashes($message_input);

    //     $passphrase = 'autopilot';

    //     // $cert = asset('push.pem');
    //     $cert = realpath(public_path('push.pem'));
    //     // dd($cert);
    //     $payload = '{
    //         "aps" :
    //             {
    //                 "alert" : "'.$message.'",
    //                 "badge" : 1, 
    //                 "sound" : "bingbong.aiff"
    //             }
    //     }';

    //     $ctx = stream_context_create();
    //     stream_context_set_option($ctx, 'ssl', 'local_cert', $cert);
    //     stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

    //     $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

    //     if (!$fp) 
    //     {
    //         // echo "Failed to connect $err $errstr <br>";

    //         return;
    //     }
    //     else 
    //         // echo "Post notification sent<br>";

    //     $dev_array = array();

    //     $dev_array = $token_array;

    //     foreach ($dev_array as $device_token) 
    //     {
    //         $msg =  chr(0) .
    //                 pack("n", 32) . 
    //                 pack('H*', str_replace(' ', '', $device_token)) . 
    //                 pack("n", strlen($payload)) . 
    //                 $payload;

    //         // echo "sending message :" . $payload . "n";

    //         fwrite($fp, $msg);
    //     }
    //     fclose($fp);
    // }
    //====================================================================================================



    public function test(Request $r){
        return $getAllForInspection = DB::connection('mysql')
        ->table('rms_logs')
        ->where('p_status', 7)
        ->get([
            'id as rmsId',
            'room_id as rmsRoomId',
        ]);

        
    }
}
