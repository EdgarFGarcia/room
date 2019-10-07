<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Room\RoomByCategory as CAT;
use App\Model\Room\RoomArea as RA;
use App\Model\Room\RoomType as RT;
use App\Model\Room\RoomRatesHMS as Rate;
use App\Model\Room\RoomGroup as RG;
use App\Model\Room\RoomStatus as RS;
use App\Model\Auth\UserCheck as check;
use App\Model\Auth\UserLocale as UserLocale;
use App\Model\Status\Status as Status;
use App\Model\Room\MarketSourceHMS as MarketSource;
use App\Model\Room\VehicleType;
use App\Model\Room\CarMakeHMS as CarMake;
use App\Model\Room\AuditTrail;
use App\Model\Room\Nationality;
use App\Model\Room\GuestInfo as GI;
use App\Model\RMSLogs_Model as logs;
use App\Model\HMS\TblRoom as HMSTblRoom;
use App\Model\HMS\GuestInfo as HMSGuestInfo;
use App\Model\VCReserve\LocaleID as Locale;
use App\Model\VCReserve\Locale as ReservationBook;
use DB;
use Response;
use Auth;
use GuzzleHttp\Client;
use App\RoomRate_Model as RoomRate;
use Illuminate\Support\Str;
use App\Restriction_Model as Restriction;
use App\Model\Mobile\WorkLogs;
use App\Http\TranscationController;
use App\Model\Room\AccessLevel;
use Illuminate\Support\Collection;
use DataTables;

class ApiController extends Controller
{

    public $cleanid = 1;
    public $inspectedcleanid = 28;
    public $hotilifiedid = 27;
    
    //
    public function getrooms(Request $request){

      $roomArea = RA::get();
      $roomType = RT::get();

      $roomGroup = RG::select('group_name',
                    DB::raw("GROUP_CONCAT(room_type_id) AS roomtype"))
                    ->orderBy('id', 'ASC')
                    ->groupBy('group_name')
                    ->get();

      /*
      *bitbit nito lahat ng info
      */
      if($request->area_id!=null){

        if($request->search!=null){

          $query = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'tblroom.id',
              'tblroom.room_no', 
              'tblroom.room_area_id', 
              'tblroom.room_status_id', 
              'tblroom.room_type_id', 
              'tblroom.room_name', 
              'roomstatus.room_status', 
              'roomstatus.color', 
              'roomstatus.is_blink', 
              'roomstatus.is_timer', 
              'roomstatus.is_name',
              // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'userinfo'")
              DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'userinfo'"),
              'tblroom.from_room_status_id'
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')

            ->where('room_area_id', '=', $request->area_id)
            ->whereRaw("(room_no LIKE '%".$request->search."%' OR room_status LIKE '".$request->search."%')")
            ->orderBy('room_area_id', 'ASC')

            // ->orderBy('room_type_id', 'ASC')
            ->orderBy('room_no', 'ASC')
            ->get();

        }
        else{

          $query = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'tblroom.id',
              'tblroom.room_no', 
              'tblroom.room_area_id', 
              'tblroom.room_status_id', 
              'tblroom.room_type_id', 
              'tblroom.room_name', 
              'roomstatus.room_status', 
              'roomstatus.color', 
              'roomstatus.is_blink', 
              'roomstatus.is_timer', 
              'roomstatus.is_name',
              // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'userinfo'")
              DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'userinfo'"),
              'tblroom.from_room_status_id'
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')

            ->where('room_area_id', '=', $request->area_id)

            ->orderBy('room_area_id', 'ASC')
            ->orderBy('room_no', 'ASC')
            ->get();

        }
        

      }
      else{

        if($request->search!=null){

          $query = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'tblroom.id',
              'tblroom.room_no', 
              'tblroom.room_area_id', 
              'tblroom.room_status_id', 
              'tblroom.room_type_id', 
              'tblroom.room_name', 
              'roomstatus.room_status', 
              'roomstatus.color', 
              'roomstatus.is_blink', 
              'roomstatus.is_timer', 
              'roomstatus.is_name',
              // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'userinfo'")
              DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'userinfo'"),
              'tblroom.from_room_status_id'
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')

            ->whereRaw("(room_no LIKE '%".$request->search."%' OR room_status LIKE '".$request->search."%')")

            ->orderBy('room_area_id', 'ASC')
            ->orderBy('room_no', 'ASC')
            ->get();

        }
        else{

          $query = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'tblroom.id',
              'tblroom.room_no', 
              'tblroom.room_area_id', 
              'tblroom.room_status_id', 
              'tblroom.room_type_id', 
              'tblroom.room_name', 
              'roomstatus.room_status', 
              'roomstatus.color', 
              'roomstatus.is_blink', 
              'roomstatus.is_timer', 
              'roomstatus.is_name',
              // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'userinfo'")
              DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'userinfo'"),
              'tblroom.from_room_status_id'
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')

            ->orderBy('room_area_id', 'ASC')
            ->orderBy('room_no', 'ASC')
            ->get();

        }
        
      }
      //end of don't touch

      // return view('rooms.rooms', compact('query', 'roomArea', 'roomType', 'roomGroup', 'roomStatus'))->render();
      return view('rooms.rooms', compact('query', 'roomArea', 'roomType', 'roomGroup'))->render();

    }

    public function getareas(){

      $roomArea = RA::select('id', 'room_area')
                    ->get();

      return json_encode([
        "areas"=>$roomArea
      ]);

    }

    // get room instance via id let the left join begin
    public function getRoomInstance(Request $request){

      $getCurrentRoomStatus = CAT::where('id', '=', $request->id)
                            ->first();

      // $getNextStatus = DB::connection('mysql')
      // ->table('access_levels')
      // ->join('roomstatus')

      $getNextStatus = DB::connection('mysql')
      ->select(DB::raw("
        SELECT tblroomgroupstatus.available_status_id AS 'id', roomstatus.room_status  FROM access_levels 
        INNER JOIN tblroomgroupstatus ON tblroomgroupstatus.room_status_id='".$getCurrentRoomStatus->room_status_id."' AND tblroomgroupstatus.available_status_id=access_levels.allow_status_id
        INNER JOIN roomstatus ON roomstatus.id=tblroomgroupstatus.available_status_id
        WHERE access_levels.role_id='".$request->role_id."' AND access_levels.room_status_id='".$getCurrentRoomStatus->room_status_id."'
        "));

      // $getNextStatus = DB::connection('mysql')
      // ->select(DB::raw("
      //     SELECT roomstatus.id, roomstatus.room_status 
      //     FROM tblroomgroupstatus
      //     INNER JOIN roomstatus ON roomstatus.id=tblroomgroupstatus.available_status_id
      //     INNER JOIN access_levels ON access_levels.room_status_id='".$getCurrentRoomStatus->room_status_id."' AND access_levels.role_id='".$request->role_id."'
      //     WHERE tblroomgroupstatus.room_status_id='".$getCurrentRoomStatus->room_status_id."' AND roomstatus.id=access_levels.allow_status_id
      //     GROUP BY roomstatus.id
      // "));

      // $getNextStatus = AccessLevel::select(
      //             'access_levels.room_status_id',
      //             DB::raw("access_levels.allow_status_id AS 'id'"),
      //             'roomstatus.room_status'
      //         )
      //         ->where('role_id', $request->role_id)
      //         ->where('room_status_id', $getCurrentRoomStatus->room_status_id)
      //         ->join('roomstatus', 'access_levels.allow_status_id', '=', 'roomstatus.id')
      //         ->get();                      

      return Response::json(['getCurrentRoomStatus'=>$getCurrentRoomStatus, 'getNextStatus'=>$getNextStatus]);

    }

    public function GetCurrentRoomStatus(Request $request){

      $query = CAT::select(
        'tblroom.room_no',
        'a.room_status AS current_status',
        'b.room_status AS previous_status'
        )
        ->leftjoin('roomstatus as a', 'a.id', '=', 'tblroom.room_status_id')
        ->leftjoin('roomstatus as b', 'b.id', '=', 'tblroom.from_room_status_id')
        ->where('tblroom.id', '=', $request->id)->first();

        return json_encode([
          'roomInstance'=>$query
        ]);

    }

    public function getRoomStatus(Request $r){

    }

    public function loginWeb(Request $r){
      $query = UserLocale::select(DB::raw("count(*) as countUser"))
      ->where('username', $r->username)
      ->where('password', $r->password)
      ->first();

      if($query->countUser == 1){
        //existing proceed to login
        $user = UserLocale::select('*')
        ->where('username', $r->username)
        // ->where('password', $r->password)
        ->first();

        $checkConnection = UserLocale::checkConnection();
        if($checkConnection){
          $usercheck = check::getUserInfo($r->username);
            if(($user->role_id != $usercheck->role_id) || ($user->firstname != $usercheck->fname) || ($user->middlename != $usercheck->mname) || ($user->lastname != $usercheck->lname)
              || ($user->status != $usercheck->status) ){
              $updateUserRole = UserLocale::where('username', $r->username)
              ->update([
                'role_id' => $usercheck->role_id,
                'firstname' => $usercheck->fname,
                'middlename' => $usercheck->mname,
                'lastname' => $usercheck->lname,
                'status' => $usercheck->status
              ]);

              if($updateUserRole){
                  return $this->MobileLogin($r);
              }
            }
        }

        //check role_id is synced

        $access = Restriction::LoginRestrictionWeb($user);
        if($access){
          $locale = Locale::select('*')->first();

          //getroominfo
          $roomInfo = CAT::where('id', $r->roomId)->first();
          return Response::json([
            'whereAmI' => $locale,
            'roomInfo' => $roomInfo,
            'error_message' => 'Successful Login',
            'query' => $user,
            'access' => $access
          ]);
        }
        else{

          return Response::json([
            'message' => 'You dont have any access here.'
          ]);

        }
      }else{
        //does not exist copy to local database
        $queryToMainServer = check::where('username', $r->username)
        ->where('password', $r->password)->first();

        $user = UserLocale::select('*')
        ->where('username', $r->username)
        // ->where('password', $r->password)
        ->first();

        //if null
        if(is_null($user)){
          $insertUserToLocale = DB::connection('mysql')
          ->table('user')
          ->insert([
            'username' => $queryToMainServer->username,
            'password' => $queryToMainServer->password,
            'email' => $queryToMainServer->email,
            'role_id' => $queryToMainServer->role_id,
            'is_logged_in' => 0,
            'firstname' => $queryToMainServer->fname,
            'middlename' => $queryToMainServer->mname,
            'lastname' => $queryToMainServer->lname,
            'status' => $queryToMainServer->status,
            'created_at' => DB::raw("NOW()")
          ]);

          return $this->loginWeb($r);
        }

        // check if user has wrong password
        if($r->password != $user->password){
            return Response::json([
              'message' => 'Wrong Username Or Password'
            ]);
        }

        // check if user is still active and has role_id on Main Server
        if(empty($queryToMainServer)){
          return Response::json([
            'error_message' => 'User is in Inactive State'
          ]);
        }

        if(($queryToMainServer->status == 'Active') && ($queryToMainServer->position == "GUEST ATTENDANT") && ($queryToMainServer->role_id == 0)){
          $update = check::updateUserIfActiveWithNoRole($r->username, 10);
          return $this->loginWeb($r);
        }else if(($queryToMainServer->status == 'Active') && ($queryToMainServer->position == "ROOM ATTENDANT") && ($queryToMainServer->role_id == 0)){
          $update = check::updateUserIfActiveWithNoRole($r->username, 6);
          return $this->loginWeb($r);
        }else if( ($queryToMainServer->status == 'Active') && ($queryToMainServer->position == "DUTY MANAGER") && ($queryToMainServer->role_id == 0)){
          $update = check::updateUserIfActiveWithNoRole($r->username, 2);
          return $this->loginWeb($r);
        }else if( ($queryToMainServer->status == 'Active') && ($queryToMainServer->position == "MAINTENANCE MAN") && ($queryToMainServer->role_id == 0)){
          $update = check::updateUserIfActiveWithNoRole($r->username, 11);
          return $this->loginWeb($r);
        }


        //copy to locale database then return to itself note role_id should be existing on vc_employees on every user to properly work
        $insertUserToLocale = DB::connection('mysql')
        ->table('user')
        ->insert([
          'username' => $queryToMainServer->username,
          'password' => $queryToMainServer->password,
          'email' => $queryToMainServer->email,
          'role_id' => $queryToMainServer->role_id,
          'is_logged_in' => 0,
          'firstname' => $queryToMainServer->fname,
          'middlename' => $queryToMainServer->mname,
          'lastname' => $queryToMainServer->lname,
          'status' => $queryToMainServer->status,
          'created_at' => DB::raw("NOW()")
        ]);

        //check now if user exist on locale with first, middle, lastname and role_id
        $checkIfUserExistOnLocale = UserLocale::select(DB::raw("count(*) as userCheck"))->where('username', $r->username)->first();

        if($checkIfUserExistOnLocale->userCheck == 1){
          return $this->loginWeb($r);
        }

      }
    }

    public function checkIfRoomIsInUse($roomId, $userId){
      return $query = WorkLogs::checkRoomIfHasOnGoing($roomId, $userId);
    }

    public function getPrevStatus(Request $r){
      // return $r->all();
      $hms = [];
      $transFrom = "WEB";
      $query = CAT::where('id', '=', $r->roomId)->get();
      // dd($query);

      $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'getPrevStatus Web',
          'status' => $query[0]->from_room_status_id,
          'room_no' => $query[0]->room_no
        ]);

      if(count($query) > 0 ){
        $changeHMS = HMSTblRoom::changeHmsStatus($r->roomId, $query[0]->from_room_status_id);

        $logsCheck = logs::where('room_id', $r->roomId)
                    ->whereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->get();

        if(count($logsCheck) > 0){
          $rmsLogs = logs::updateRMSLogsAndWorkLogs($logsCheck[0]->id, $query[0]->from_room_status_id);

          return response()->json([
            'changeHMSStatus' => $changeHMS,
            'rmsLogs' => $rmsLogs,
            'message' => 'FROM TRUE'
          ], 200);
        }else{
          $rmsLogs = logs::insertLogsStart($r->roomId, $r->userInfo, $transFrom,  $query[0]->from_room_status_id);

          return response()->json([
            'changeHMSStatus' => $changeHMS,
            'rmsLogs' => $rmsLogs,
            'message' => 'FROM FALSE'
          ], 200);
        }

      }

      return json_encode([
          'success' => true,
          'message' => "Transcation Cancelled"
        ], 200);
      
    }

    public function changeStatus(Request $r){

      $rmsId;
      $transFrom = "WEB";

      if($r->toStatus ==  "0" || $r->toStatus == 0){
        return array(
          'message' => 'Please select on dropdown for next status'
        );
      }else{

      }

      if($r->toStatus == 13){
        $updateHMSRoomStat = HMSTblRoom::changeHmsStatus($r->roomId, 13);
      }else{
        //change hms status to tagboard
        $updateHMSRoomStat = HMSTblRoom::changeHmsStatus($r->roomId, $r->toStatus);

        //update room info of room

        $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'changeStatus Web',
          'status' => $r->toStatus,
          'room_no' => $r->roomId
        ]);

        $checkLogs = logs::select
        (
          'rms_logs.id as id',
          'rms_logs.e_status as e_status',
          'tbl_room_buddies.work_id as work_id'
        ) 
          ->join('tbl_room_buddies', 'rms_logs.id', '=', 'tbl_room_buddies.rms_logs_id')
          ->where('rms_logs.room_id', $r->roomId)
          ->whereNull('e_status')
          ->orderBy('rms_logs.id', 'DESC')
          ->get();

        if(count($checkLogs) > 0){
          $rmsLogs = logs::updateRMSLogsAndWorkLogs($r->toStatus, $checkLogs[0]->id);
          if(
            ($r->toStatus == 4) ||
            ($r->toStatus == 6) ||
            ($r->toStatus == 13) ||
            ($r->toStatus == 19) ||
            ($r->toStatus == 20) ||
            ($r->toStatus == 22) ||
            ($r->toStatus == 24) ||
            ($r->toStatus == 51) ||
            ($r->toStatus == 40)
            ){
              $insertUserInfo = logs::updateRoomInfo($r->userInfo, $checkLogs[0]->room_no);
              // $insertUserInfo = logs::updateRoomInfo($r->userInfo, $r->roomNo);
              // $updateUserInfoRoomBuddies = logs::updateRoomBuddiesInfo($r->userInfo, $r->roomNo, $checkLogs[0]->id, $transFrom, $checkLogs[0]->work_id);
            }
        }else{
          $rmsLogs = logs::insertLogsStart($r->roomId, $r->userInfo, $transFrom, $r->toStatus);
          if(
            ($r->toStatus == 4) ||
            ($r->toStatus == 6) ||
            ($r->toStatus == 13) ||
            ($r->toStatus == 19) ||
            ($r->toStatus == 20) ||
            ($r->toStatus == 22) ||
            ($r->toStatus == 24) ||
            ($r->toStatus == 51) ||
            ($r->toStatus == 40)
            ){
            $insertUserInfo = logs::updateRoomInfo($checkLogs[0]->room_no ,$r->userInfo);
          }
        }

        $checkStatus = CAT::where('id', '=', $r->roomId)
        ->limit(1)
        ->first();
        
        // end of room status update
        return Response::json([
          'checkStatus'=>$checkStatus, 
          'logs' => $rmsLogs, 
          // 'updateUserInfoRoomBuddies'=>$updateUserInfoRoomBuddies,
          'saveChanges'=>true
        ]);
      }
    }

    public function implementPrevStat(Request $r){

      $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'implementPrevStat Web'
        ]);
      
      // $changeStatus = CAT::find($r->roomId);
      $changeStatus = DB::connection('mysql')
      ->table('tblroom')
      ->where('id', '=', $r->roomId)
      ->get();

      // dd($changeStatus);

      $query = HMSTblRoom::where('RoomNo', '=', $r->roomNo)
                ->update([
                  'CRoom_Stat' => $changeStatus[0]->from_room_status_id,
                ]);

      $query = CAT::select('room_status_id', 'from_room_status_id', 'roomstatus.room_status', 'roomstatus.color')
              ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
              ->leftJoin('roomstatus as rs', 'tblroom.from_room_status_id', '=', 'rs.id')
              ->where('tblroom.id', '=', $r->roomId)
              ->get();

      return Response::json(['cancelledStatus'=>$changeStatus, 'query'=>$query]);
    }

    public function getRoomStatusAfterSave(Request $r){

      $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'getRoomStatusAfterSave Web',
        ]);

      $query = CAT::select(
                'room_status_id', 
                'tblroom.from_room_status_id', 
                'roomstatus.room_status', 
                'roomstatus.color', 
                'roomstatus.is_blink', 
                'roomstatus.is_timer', 
                'roomstatus.is_name',
                'tblroom.room_no as roomNo',  
                // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'userinfo'")
                DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'userinfo'")
              )
              ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
              ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')
              ->leftJoin('roomstatus as rs', 'tblroom.from_room_status_id', '=', 'rs.id')
              ->where('tblroom.id', '=', $r->roomId)
              ->get();
                      
      return Response::json(['getRooms'=>$query]);
      
    }

    public function getRoomTypeEtc(Request $r){

      $getRoomType = RT::where('id', '=', $r->roomType)->first();
      $getRates = Rate::where('RoomType', '=', $getRoomType['room_type'])
                      ->where('Activate', '=', 'TRUE')
                      ->get();
      $marketSource = MarketSource::get();
      $carMake = CarMake::get();
      $vehicleType = VehicleType::get();
      $nationality = Nationality::get();

      return response()->json(['getRoomType'=>$getRoomType, 'getRates'=>$getRates, 'marketSource'=>$marketSource
                        ,'carMake'=>$carMake, 'vehicleType'=>$vehicleType, 'nationality'=>$nationality]);

    }

    public function areaMobile(Request $r){
      $getAllRooms = DB::connection('mysql')
            ->table('tblroomareas')
            ->select('id', 'room_area')
            ->whereNull('deleted_at')
            ->get();

      return response()->json(['listOfAreas'=>$getAllRooms], 200);
    }

    public function listOfRoomsMobile(Request $r){

      $getAllRooms = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              DB::raw("TIMESTAMPDIFF(SECOND, tblroom.updated_at, NOW()) AS 'time_in_seconds'"),
              'tblroom.id as id', 
              'tblroom.room_no as room_no',
              'tblroom.room_type_id as room_type_id',
              'tblroom.room_hashed as room_hashed',
              'tblroom.room_type_id as room_type_id',
              'tblroom.from_userinfo as userinfo',
              'tblroom.room_area_id as roomAreaId',
              'tblroom.updated_at as updated_at',
              'tblroom.from_room_status_id as previous_room_status_id',
              'tblroomtype.room_type as room_type',
              'roomstatus.id as room_status_id', 
              'roomstatus.room_status as room_status',
              'roomstatus.color as color',
              'roomstatus.is_blink as is_blink',
              'roomstatus.is_timer as is_timer',
              'roomstatus.is_name as is_name',
              'roomstatus.is_buddy as is_buddy',
              // DB::RAW("IFNULL((SELECT buddies.emp_users FROM rms_logs LEFT JOIN (SELECT rms_logs_id, GROUP_CONCAT(CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'emp_users' FROM tbl_room_buddies LEFT JOIN user ON user.username=tbl_room_buddies.emp_id GROUP BY tbl_room_buddies.rms_logs_id) AS buddies ON buddies.rms_logs_id=rms_logs.id WHERE rms_logs.room_id=tblroom.id ORDER BY rms_logs.id DESC LIMIT 1), CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename)) AS 'name'")
              DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'name'")
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')
            ->leftjoin('tblroomtype', 'tblroom.room_type_id', '=', 'tblroomtype.id')
            ->orderBy('room_no', 'ASC')
            // ->orderBy('room_area_id', 'ASC')
            // ->orderBy('room_type_id', 'ASC')
            // ->orderBy('tblroom.room_status_id', 'ASC')
            ->get();

      return response()->json(['listOfRooms'=>$getAllRooms], 200);
      
    }


    public function checkin(Request $r){

        $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'checkin Web',
          'status' => 2,
          'room_no' => $r->roomNo
        ]);

        $query = HMSTblRoom::changeHmsStatus($r->roomId, 2);

        $getRoomInfo = CAT::where('id', $r->roomId)->first();
        $getRoomType = RT::where('id', $r->roomType)->first();
        $getRate = Rate::where('id', '=', $r->rates)->first();
        $marketSource = MarketSource::where('id', '=', $r->marketsource)->first();
        $nationality = Nationality::where('id', '=', $r->nationality)->first();
        $carMake = CarMake::where('id', '=', $r->car)->first();
        $vehicleType = VehicleType::where('id', '=', $r->vehicletype)->first();
        $user = UserLocale::where('username', '=', $r->userInfo)->first();

        $saveToRms = GI::insert([
          'RoomName' => $getRoomInfo->room_name,
          'RoomNo' => $getRoomInfo->room_no,
          'RoomType' => $getRoomType->room_type,
          'DateIn' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d')"),
          'TimeIn' => DB::raw("DATE_FORMAT(NOW(), '%h:%i:%s %p')"),
          'RateAmount' => $getRate->Amount,
          'RateDesc' => $getRate->RateDesc,
          'MarketSource' => is_null($r->marketsource) ? "" : $marketSource->MarketSource,
          'CarMake' => is_null($r->car) ? "" : $carMake->CarMake,
          'VehicleType' => is_null($r->vehicletype) ? "" : $vehicleType->name,
          'PlateNo' => is_null($r->platenumber) ? "" : $r->platenumber,
          'GSteward' => $user->firstname . " " . $user->lastname,
          'nationality' => $nationality == null ? "" : $nationality->name,
          'Remarks' => is_null($r->remarks) ? "" : $r->remarks,
          'user_id' => $r->userInfo,
          'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')")
        ]);

        $guestinfo = DB::connection('mysql')
        ->table('guestinfo')
        ->insert([
          'RoomName' => $getRoomInfo->room_name,
          'RoomNo' => $getRoomInfo->room_no,
          'RoomType' => $getRoomType->room_type,
          'DateIn' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d')"),
          'TimeIn' => DB::raw("DATE_FORMAT(NOW(), '%h:%i:%s %p')"),
          'RateAmount' => $getRate->Amount,
          'RateDesc' => $getRate->RateDesc,
          'MarketSource' => is_null($r->marketsource) ? "" : $marketSource->MarketSource,
          'CarMake' => is_null($r->car) ? "" : $carMake->CarMake,
          'VehicleType' => is_null($r->vehicletype) ? "" : $vehicleType->name,
          'PlateNo' => is_null($r->platenumber) ? "" : $r->platenumber,
          'GSteward' => $user->firstname . " " . $user->lastname,
          'nationality' => $nationality == null ? "" : $nationality->name,
          'Remarks' => is_null($r->remarks) ? "" : $r->remarks,
          'user_id' => $r->userInfo,
          'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')")
        ]);
        
        $validation = RoomRate::ValidateRoomRate($r);
        
        if($validation){
          RoomRate::SaveRoomRate($r);
        }
        else{
          RoomRate::UpdateRoomRate($r);
        }

        $logsCheck = logs::where('room_id', $r->roomId)
                    ->orWhereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->first();

        if(empty($logsCheck->e_status)){
          $rmsLogs = logs::updateRMSLogsAndWorkLogs($logsCheck->id, 2);
        }


      return response()->json([
        'checkin' => $saveToRms,
        'logsCheck' => $logsCheck
      ]);

    }

    public function getStatuses(Request $r){
      // $test = $r->all();
      $roomStatus = RS::get();
      return response()->json(['roomStatus'=>$roomStatus]);
    }

    public function reservationBooking(Request $r){

      $locale = Locale::get();

      $order_ref = Str::random(36);
      $query = ReservationBook::insert([
        'Room_Number' => $r->roomNo,
        'Entry_Date' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')"),
        'Reserve_Date' => $r->checkinDate,
        'First_Name' => $r->fnameReservation,
        'Last_Name' => $r->lnameReservation,
        'Room_Deposit' => $r->amount,
        'Currency'=> $r->deposit,
        'Locale_ID' => $locale[0]->local_id,
        'Reserved_By' => $r->userInfo,
        'Time' => $r->timeInReservation,
        'Contact_Number' => $r->pnumberReservation,
        'Reserved_Additional_Request' => $r->notesForReservation,
        'Order_Ref' => $order_ref
      ]);
    }

    public function roomInfoAll(){

      $roomInfo = CAT::select(
                    'tblroom.id as roomId', 
                    'tblroom.room_no as rooomNo',
                    'roomstatus.room_status as status',
                    DB::raw("COUNT(roomstatus.room_status) as status_count")
                    )
                    ->join('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
                    ->groupBy('tblroom.room_status_id')
                    ->get();

      return response()->json(['roomInfo'=>$roomInfo]);

    }


    public static function emitOnline($request, $locale_id = null) {
      if (!empty($request->from)) {
        $client = new Client([
            'base_uri' => 'http://192.168.1.23:6965/',
            'timeout'  => 2.0,
        ]);
        if (empty($locale_id)) {
            $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => "100"]]);
        } else {
            $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => $locale_id]]);
        }
      }
    }

    public function CheckIfOccupied(Request $request){

        $room = DB::connection('mysql')
        ->table('tblroom')
        ->select(
          'room_status_id'
        )
        ->where('id', '=', $request->romid)
        ->first();

        return json_encode([ 
          "status"=>$room->room_status_id
        ]);

    }

    // private function emitOnline($request, $locale_id) {

      // if (!empty($request->from)) {
      //   $client = new Client([
      //       'base_uri' => 'http://192.168.1.59:6969/',
      //       'timeout'  => 2.0,
      //   ]);
      //   if (empty($locale_id)) {
      //       $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => "100"]]);
      //   } else {
      //       $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => $locale_id]]);
      //   }
      //   // $client->request('GET', 'emitOnlineUsers');
      // } 
    // }

    public function testRooms(Request $r){
      $query = DB::connection('mysql')
      ->table('tblroom')
      ->select('*')->get();
      return response()->json([
        'rooms' => $query
      ]);
    }

    public function getLocaleInfo(){
      $test = array(
        array(
          'name' => 'VCPA',
          'description' => 'VICTORIA COURT PANORAMA',
          'ipAddress' => 'http://192.168.15.12/',
          'rmsTagboard' => 'http://192.168.15.12:6969',
          'rmsMobile' => 'http://192.168.15.12:6965'
        ),
        array(
          'name' => 'VCHI',
          'description' => 'VICTORIA COURT HILLCREST',
          'ipAddress' => 'http://192.168.13.12/',
          'rmsTagboard' => 'http://192.168.13.12:6969',
          'rmsMobile' => 'http://192.168.13.12:6965'
        ),
        array(
          'name' => 'VCSF',
          'description' => 'VICTORIA COURT SAN FERNANDO PAMPANGA',
          'ipAddress' => 'http://192.168.9.12/',
          'rmsTagboard' => 'http://192.168.9.12:6969',
          'rmsMobile' => 'http://192.168.9.12:6965'
        ),
        array(
          'name' => 'VCBA',
          'description' => 'VICTORIA COURT BALINTAWAK',
          'ipAddress' => 'http://192.168.3.12/',
          'rmsTagboard' => 'http://192.168.3.12:6969',
          'rmsMobile' => 'http://192.168.3.12:6965'
        ),
        array(
          'name' => 'VCNE',
          'description' => 'VICTORIA COURT NORTH EDSA',
          'ipAddress' => 'http://192.168.6.12/',
          'rmsTagboard' => 'http://192.168.6.12:6969',
          'rmsMobile' => 'http://192.168.6.12:6965'
        ),
        array(
          'name' => 'VCMA',
          'description' => 'VICTORIA COURT MALABON',
          'ipAddress' => 'http://192.168.10.12/',
          'rmsTagboard' => 'http://192.168.10.12:6969',
          'rmsMobile' => 'http://192.168.10.12:6965'
        ),
        array(
          'name' => 'VCLP',
          'description' => 'VICTORIA COURT LAS PINAS',
          'ipAddress' => 'http://192.168.5.12/',
          'rmsTagboard' => 'http://192.168.5.12:6969',
          'rmsMobile' => 'http://192.168.5.12:6965'
        ),
        array(
          'name' => 'TEST SERVER',
          'description' => 'TEST SERVER',
          'ipAddress' => 'http://192.168.1.8/',
          'rmsTagboard' => 'http://192.168.1.8:6969',
          'rmsMobile' => 'http://192.168.1.8:6965'
        ),
            array(
                'name' => 'VICKY',
                'description' => 'POGING BAGSIK',
                'ipAddress' => 'http://192.168.1.58/',
                'rmsTagboard' => 'http://192.168.1.8:6969',
                'rmsMobile' => 'http://192.168.1.8:6965'
            ),

      );

      return response()->json([
        'localeList' => $test
      ], 200);
    }


    public function testtime(){
      // return "test";
      return $test = DB::connection('mysql')
      ->table('historical_inspection as a')

      ->select(
        'c.room_no as room_no',
        'b.score as score',
        'a.created_at as created_at',
        'c.user_id as user_id',
        'd.firstname as fname',
        'd.lastname as lname'
      )

      ->leftJoin('tbl_score_inspection as b', 'a.batch_number', '=', 'b.batch_number')
      ->leftJoin('tbl_transactions as c', 'a.transaction_id', '=', 'c.id')
      ->leftjoin('user as d', 'c.user_id', '=', 'd.username')

      ->whereNotNull('b.score')
      ->where('a.process_code', 'HK')

      ->groupBy('a.batch_number')
      ->orderBy('a.created_at', 'DESC')

      ->get();

    }

    public function LoadBeloBadge(){

      $result = DB::connection('mysql')
      ->table('tblroom')
      ->select(
        DB::raw("COUNT(*) AS 'belo_count'")
      )
      ->whereRaw("belo_count>=(SELECT (automatic_belo_checkout * automatic_belo_percentage) FROM settings)")
      ->first();

      return json_encode([
        "belo_count"=>$result->belo_count
      ]);

    }

    public function LoadForBeloPercent(){

      $result = DB::connection('mysql')
      ->table('tblroom')
      ->select(
        'id',
        'room_no',
        DB::raw("CONCAT(ROUND((SELECT (belo_count / automatic_belo_checkout) * 100 FROM settings), 2), '%') AS 'percent'")
      )
      ->whereRaw("belo_count>=(SELECT (automatic_belo_checkout * automatic_belo_percentage) FROM settings)")
      ->get();

      $data = array();
      foreach($result as $val){

          $obj = new \stdClass;

          $obj->room = $val->room_no;
          $obj->percent = $val->percent;

          $data[] = $obj;

      }

      $info = new Collection($data);
      return Datatables::of($info)->make(true);


    }

    public function LoadRoomGRP(){

      $content = "";

      $result = DB::connection('mysql')
      ->table('tbl_room_balancer')
      ->select(
        DB::raw("REPLACE(tblroomtype.room_type, ' ', '') AS 'group_name'"),
        'tblroomtype.room_type'
      )
      ->join('tblroomtype', 'tblroomtype.id', '=', 'tbl_room_balancer.grproomid')
      ->orderBy('tbl_room_balancer.order')
      ->get();

      foreach($result as $val) {
        
        $content .= '
        <div class="col-md-4">
            <div id="' . $val->group_name . '" style="margin-left: 50px; margin-top: 50px;">
                <h2 style="color: white; margin-bottom: 15px; margin-top: 15px;">'. $val->room_type .'</h2>
                <h3 style="color: white;">LOADING...</h3>
            </div>
        </div>
        ';

      }

      return json_encode([
        "content"=>$content
      ]);

    }

    public function LoadBalancerBadge(){

      $balancercount = 0;

      //Get Room Type
      $roomtype = DB::connection('mysql')
      ->table('tbl_room_balancer')
      ->select(
        DB::raw("REPLACE(tblroomtype.room_type, ' ', '') AS 'group_name'"),
        DB::raw("tbl_room_balancer.grproomid AS 'room_type_id'"),
        'tbl_room_balancer.percentage'
      )
      ->join('tblroomtype', 'tblroomtype.id', '=', 'tbl_room_balancer.grproomid')
      ->get();

      foreach($roomtype as $rtype){

        $group = DB::connection('mysql')
        ->table('tblroom')
        ->select(
          DB::raw("COUNT(*) AS 'count'"),
          DB::raw("CEILING((COUNT(*) * ". $rtype->percentage .")) AS 'roomcount'"),
          DB::raw("
          (SELECT COUNT(*) FROM tblroom WHERE room_type_id=". $rtype->room_type_id ." AND (room_status_id=". $this->cleanid ." OR room_status_id=". $this->inspectedcleanid ." OR room_status_id=". $this->hotilifiedid .")) AS 'cleancount'
          ")
        )
        ->whereRaw("tblroom.room_type_id=". $rtype->room_type_id ."")
        ->get();

        foreach($group as $val){

          if($val->roomcount>$val->cleancount){

            $room = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'room_no',
              'updated_at'
            )
            ->whereRaw("room_type_id=". $rtype->room_type_id ."")
            ->whereRaw("room_status_id IN (3,16,18,21,26,31,53,55,56,25)")
            ->limit($val->roomcount)
            ->get();

            $balancercount += count($room);

          }

        }

      }

      if($balancercount!=0){

        return json_encode([
          "balancercount"=>$balancercount,
          "isblink"=>true
        ]);

      }
      else{

        return json_encode([
          "balancercount"=>$balancercount,
          "isblink"=>false
        ]);

      }


    }


    // public function testtime(){
    //   $test = DB::connection('mysql')
    //   ->select("SELECT * FROM `rms_logs` WHERE timestampdiff(second, s_datetime, e_dateTime) < 0");

    //   foreach($test as $data){
    //     $id = $data->id;

    //     $eDateTime = $data->e_dateTime;

    //     $update = DB::connection('mysql')
    //     ->select("UPDATE rms_logs set e_dateTime = ADDTIME('$eDateTime', '43200') WHERE id = '$id'");

    //   }
    // }
}