<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Status\Status as NextStatuses;
use App\Model\Room\RoomStatus as RS;
use App\Model\Room\RoomByCategory as Rooms;
use App\Model\Logs\RMSLogs as Logs;
use App\Model\Room\RoomType as RT;
use App\Model\Room\RoomRatesHMS as Rate;
use App\Model\Room\MarketSourceHMS as MarketSource;
use App\Model\Room\CarMakeHMS as CarMake;
use App\Model\Room\VehicleType;
use App\Model\Room\Nationality;
use App\Model\Mobile\WorkLogs;
use App\Model\Room\GuestInfo;
use App\Model\Auth\UserCheck as check;
use App\Model\Room\AccessLevel;
use App\Model\HMS\TblRoom;
use App\Model\RMSLogs_Model as RMSLogs;
use App\Model\Auth\UserLocale;
use App\RmsRoom;
use DB;
use App\MobileLogsCheckin_Model as MobileLogs;
use App\Operation_Model as Operation;

class MobileLogsCheckin extends Controller
{
    //
    protected $mobile;
    private $url = "http://192.168.1.23/pos/api/vc/ma/";

	public function checkStatusesAfterLogin(Request $r){
        return $access = AccessLevel::checkaccesslevel($r->roleid);
	}

    /*
    * get all room status
    *
    */

    public function getAllRoomStatus(Request $r){
        $query = DB::connection('mysql')
        ->table('roomstatus')
        ->get();

        return response()->json([
            'statuses' => $query
        ]);
    }

    /*
    * roomInformation
    * required params
    * mobile/tagboard/roomInfo
    * roomNo(int) 
    *
    */

    public function roomInformation(Request $r){
        $query = Rooms::roomInfo($r->roomNo);
    }

    /*
    * on negotiation of room
    * required params are the following
    * mobile/tagboard/onNego
    * getAllInfo 0 or 1
    * work_id (int)
    * user_id (int)
    * mobile (string)
    * roomId (int)
    * status (int)
    * this this method / function will automatically call 
    * $this->guestInfoForm() and $this->checkin()
    */

    public function OnNego(Request $r){
        // return $access;
        // dd($r->all());
        $this->rule = array(
            'getAllInfo' => 'required',
            'work_id' => 'required',
            'user_id' => 'required',
            'mobile' => 'required',
            'roomId' => 'required',
            'status' => 'required',
        );

        $this->validate($r->all(), $this->rule);

        if($this->data['error']['status'] == false){
            $checkRooms = Rooms::where('id', $r->roomId)->get();

            $this->mobile = $r->mobile;

            //Checking
            if($checkRooms[0]->room_status_id==2){

                $this->responseError(
                    'Room Is In Use'
                );
                
                return response()->json($this->data, $this->statusCode);

            }

            // check if rooom is on nego
            if($checkRooms[0]->room_status_id == 20 || $checkRooms[0]->room_status_id == 32){
                // return "room is on nego or dirty with waiting guest";
                $iCameFrom = DB::connection('mysql')
                    ->table('where_i_came')
                    ->insert([
                      'function_name' => 'onNego from 20 and 32 Mobile',
                      'status' => $r->status,
                      'room_no' => $checkRooms[0]->room_no
                    ]);

                if($r->getAllInfo == 1){

                    $onNegoHMS = TblRoom::where('RoomNo', $checkRooms[0]->room_no)
                                ->where('CRoom_Stat', '=', 20)
                                ->where('Stat', '=', 'DIRTY')
                                ->first();

                    $getRmsId = RMSLogs::
                        where('room_id', $r->roomId)
                        ->whereNull('e_status')
                        ->orderBy('id', 'DESC')
                        ->get();

                    if(count($getRmsId) != 0){
                        // $updatedata = RMSLogs::updateRMSLogsAndWorkLogs($getRmsId[0]->id, $r->status, $r->user_id, $r->mobile);
                        // $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->status);
                        $getRmsId = array(
                            'lastRmsLogId' => $getRmsId[0]->id,
                            'lastWorkId' => 0,
                            'lastRoomBuddies' => 0,
                            'time' => 0
                        );

                    }else{
                        $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $checkRooms[0]->room_status_id);
                    }

                    // return $getRmsId['lastRmsLogId'];

                    if($r->status == 19){

                        $updateHMS = TblRoom::where('RoomNo', $checkRooms[0]->room_no)
                            ->update([
                                'CRoom_Stat' => 4,
                                'Stat' => 'DIRTY'
                            ]);

                    }

                    if($r->status == 41){
                        $updateHMS = TblRoom::where('RoomNo', $checkRooms[0]->room_no)
                            ->update([
                                'CRoom_Stat' => 20,
                                'Stat' => 'DIRTY'
                            ]);
                    }

                    $pepe = $this->guestInfoForm($r);
                    $pepe['rmsLogs'] = array('lastRmsLogId' => $getRmsId['lastRmsLogId'], 'lastWorkId' => '', 'lastRoomBuddies'=> '');
                    $this->data['response'] = $pepe;


                } else {
                    $this->responseError(
                        'Room Is In Use'
                    );
                }

            }else{

                if($r->getAllInfo == 1){
                    $onNegoHMS = TblRoom::where('RoomNo', $checkRooms[0]->room_no)
                                ->where('CRoom_Stat', '=', 20)
                                ->where('Stat', '=', 'DIRTY')
                                ->first();

                    $getRmsId = RMSLogs::
                        where('room_id', $r->roomId)
                        ->whereNull('e_status')
                        ->orderBy('id', 'DESC')
                        ->get();

                    if(count($getRmsId) > 0){
                        $updatedata = RMSLogs::updateRMSLogsAndWorkLogs($getRmsId->id, $r->status, $r->user_id, $r->mobile);
                        // $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->status);
                    }else{
                        $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->status);
                    }

                    $pepe = $this->guestInfoForm($r);
                    $pepe['rmsLogs'] = array('lastRmsLogId' => $getRmsId['lastRmsLogId'], 'lastWorkId' => '', 'lastRoomBuddies'=> '');
                    $this->data['response'] = $pepe;
                    
                }else{


                    // return $r->all();
                    $getRmsId = RMSLogs::
                    where('room_id', $r->roomId)
                    ->whereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->get();

                    // return $getRmsId;

                    // if(count($getRmsId) > 0){
                    //     $updatedata = RMSLogs::updateRMSLogsAndWorkLogs($getRmsId[0]->id, 57, $r->user_id, $r->mobile);
                    //     // $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->status);
                    // } else {
                    //     $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                    // }

                    // return $getRmsId;

                    if($r->status == 19){
                        if(count($getRmsId) > 0){
                            $updatedata = RMSLogs::updateRMSLogsAndWorkLogs($getRmsId[0]->id, 58, $r->user_id, $r->mobile);
                            $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                        } else {
                            $getRmsId = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                        }
                        $updateRoomStatus = TblRoom::changeHmsStatus($r->roomId, $r->status);
                        $pepe = $this->guestInfoForm($r);
                        $pepe['rmsLogs'] = array('lastRmsLogId' => $getRmsId['lastRmsLogId'], 'lastWorkId' => '', 'lastRoomBuddies'=> '');
                        $this->data['response'] = $pepe;
                    }else{
                        // $worklogs = WorkLogs::insertWorkLogs($r->user_id, $checkRooms->room_no, $r->work_id);
                        // prepare for the form

                        //Checking
                        if($checkRooms[0]->room_status_id==2){

                            $this->responseError(
                                'Room Is In Use'
                            );
                            
                            return response()->json($this->data, $this->statusCode);

                        }

                        $getRoomType = RT::where('id', '=', $checkRooms[0]->room_type_id)->first();
                        $getRates = Rate::where('RoomType', '=', $getRoomType['room_type'])
                                    ->where('Activate', '=', 'TRUE')
                                    ->get();

                        $marketSource = MarketSource::get();
                        $carMake = CarMake::get();
                        $vehicleType = VehicleType::get();
                        $nationality = Nationality::get();

                        $onNegoHMS = TblRoom::where('RoomNo', $checkRooms[0]->room_no)->update([
                            'CRoom_Stat' => $r->status == 1 ? 20 : 32,
                            'Stat' => 'DIRTY'
                        ]);

                        // $rmsLogs = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                        if(count($getRmsId) > 0){
                            $updatedata = RMSLogs::updateRMSLogsAndWorkLogs($getRmsId[0]->id, 58, $r->user_id, $r->mobile);
                            $rmsLogs = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                        } else {
                            $rmsLogs = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $r->work_id);
                        }

                        $insertData = $this->data['response'] = array(
                            'onNegoHMS' => $onNegoHMS, 
                            // 'workLogs'=>$worklogs, 
                            'getRoomType'=>$getRoomType, 
                            'getRates'=>$getRates, 
                            'marketSource'=>$marketSource,
                            'carMake'=>$carMake, 
                            'vehicleType'=>$vehicleType, 
                            'nationality'=>$nationality,
                            'rmsLogs' => $rmsLogs
                        );

                        return response()->json($this->data, $this->statusCode); 
                    }
                    
                }
                // return "room is not on nego or dirty with waiting guest";
            }
        }
    	return response()->json($this->data, $this->statusCode);

    }

    public function guestInfoForm(Request $r) {
        $getRoomInfo = Rooms::where('id', $r->roomId)->first();
                $marketsource = MarketSource::get();
                $carmake = Carmake::get();
                $vehicleType = VehicleType::get();
                $nationality = Nationality::get();
                $getRoomType = RT::where('id', '=', $getRoomInfo->room_type_id)->first();
                $getRates = Rate::where('RoomType', '=', $getRoomType->room_type)->get();
                return array('getRoomType'=>$getRoomType, 
                    'getRates'=>$getRates, 
                    'marketSource'=>$marketsource,
                    'carMake'=>$carmake, 
                    'vehicleType'=>$vehicleType, 
                    'nationality'=>$nationality);
                // return response()->json([
                    
                //     ], 200);
    }

    public function checkin(Request $r){
        $getRoomInfo = Rooms::where('id', $r->roomId)->first();
        if($getRoomInfo->room_status_id == 20){

            $hmsroom = MobileLogs::GetHMSRoomStatus($getRoomInfo->room_no);

            if($hmsroom->CRoom_Stat==2){

                return response()->json(['checkInFromClean'=>$getRoomInfo, 'hmsChange'=>1, 'updateLogs' => 1, 'closeRMS'=>1]);

            }

            $iCameFrom = DB::connection('mysql')
            ->table('where_i_came')
            ->insert([
              'function_name' => 'checkin from on nego Mobile',
              'status' => 20,
              'room_no' => $r->roomId
            ]);
            
            //Delete Guest Information (GELO)
            MobileLogs::DeleteGuestInformation($getRoomInfo->room_no);

            $this->pahirapNaCheckIn($r, $getRoomInfo);

            //Update HMS Room Status
            $hmsChange = TblRoom::where('RoomName', $getRoomInfo->room_no)
                    ->update([
                        'Stat' => 'WELCOME',
                        'CRoom_Stat' => 2
                    ]);

            $updateLogs = WorkLogs::updateWorkLogs($r->work_id, $r->user_id, $getRoomInfo->room_no);

            // end rms logs
            $endRMS = RMSLogs::closeRmsLogsCheckIn($r->rms_id, 2, $r->user_id, $r->mobile);

            // end workLogs
            $closeWorkLogs = RMSLogs::closeWorkLogs($r->rms_id);

            return response()->json(['checkInFromClean'=>$getRoomInfo, 'hmsChange'=>$hmsChange, 'updateLogs' => $closeWorkLogs, 'closeRMS'=>$endRMS]);

        }else if($getRoomInfo->room_status_id == 32){

            $iCameFrom = DB::connection('mysql')
            ->table('where_i_came')
            ->insert([
              'function_name' => 'checkin on-going from dirty with waiting guest Mobile',
              'status' => 32,
              'room_no' => $r->roomId
            ]);

            $this->pahirapNaCheckIn($r, $getRoomInfo);

            $endRMS = RMSLogs::closeRmsLogsCheckIn($r->rms_id, 32, $r->user_id, $r->mobile);

            $closeWorkLogs = RMSLogs::closeWorkLogs($r->rms_id);

            return response()->json(['checkInFromDirtyWithWaitingGuest'=>$getRoomInfo, 'closeRMS'=>$endRMS, 'closeWorkLogs' => $closeWorkLogs]);

        }else if($getRoomInfo->room_status_id == 19){
            // return "hello";
            $iCameFrom = DB::connection('mysql')
            ->table('where_i_came')
            ->insert([
              'function_name' => 'checkin on-going from rc with waiting guest Mobile',
              'status' => 19,
              'room_no' => $r->roomId
            ]);

            $updateRoomHMS = DB::connection('hms')
            ->table('tblroom')
            ->where('RoomNo', '=', $getRoomInfo->room_no)
            ->update([
                'CRoom_Stat' => 4,
                'Stat' => 'DIRTY'
            ]);

            $this->pahirapNaCheckIn($r, $getRoomInfo);

            // $endRMS = RMSLogs::closeRmsLogsCheckIn($r->rms_id, 19, $r->user_id);
            $endRMS = RMSLogs::closeRmsLogsCheckIn($r->rms_id, 4, $r->user_id, $r->mobile);

            $closeWorkLogs = RMSLogs::closeWorkLogs($r->rms_id);

            return response()->json(['checkInFromOnGoingRCWithWaiting'=>$getRoomInfo, 'closeRMS'=>$endRMS, 'closeWorkLogs' => $closeWorkLogs]);

        }else if($getRoomInfo->room_status_id == 2){

            $iCameFrom = DB::connection('mysql')
            ->table('where_i_came')
            ->insert([
              'function_name' => 'checkin on-going from occupied waiting guest Mobile',
              'status' => 2,
              'room_no' => $r->roomId
            ]);

            //Delete Guest Information (GELO)
            MobileLogs::DeleteGuestInformation($getRoomInfo->room_no);

            $this->pahirapNaCheckIn($r, $getRoomInfo);

            $endRMS = RMSLogs::closeRmsLogsCheckIn($r->rms_id, 2, $r->user_id, $r->mobile);

            $closeWorkLogs = RMSLogs::closeWorkLogs($r->rms_id);

            return response()->json(['checkInFromRegularClean'=>$getRoomInfo, 'closeRMS'=>$endRMS, 'closeWorkLogs' => $closeWorkLogs]);
        }	
    }


    public function pahirapNaCheckIn(Request $r, $getRoomInfo){

    	$getRoomType = RT::where('id', $r->roomType)->first();
    	$getRate = Rate::where('id', '=', $r->rates)->first();
    	$marketSource = MarketSource::where('id', '=', $r->marketsource)->first();
    	$carMake = CarMake::where('id', '=', $r->car)->first();
    	$vehicleType = VehicleType::where('id', '=', $r->vehicletype)->first();
        $nationality = Nationality::where('id', '=', $r->nationality)->first();
    	$user = check::where('username', '=', $r->user_id)->first();

    	$saveToRms = GuestInfo::insert([
          'RoomName' => $getRoomInfo->room_name,
          'RoomNo' => $getRoomInfo->room_no,
          'RoomType' => $getRoomType->room_type,
          'DateIn' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d')"),
          'nationality' => $nationality == null ? "" : $nationality->name,
          'TimeIn' => DB::raw("DATE_FORMAT(NOW(), '%h:%i:%s %p')"),
          'RateAmount' => $getRate->Amount,
          'RateDesc' => $getRate->RateDesc,
          'MarketSource' => $marketSource->MarketSource,
          'CarMake' => $carMake == null ? "" : $carMake->CarMake,
          'VehicleType' => $vehicleType == null ? "" : $vehicleType->name,
          'PlateNo' => empty($r->platenumber) ? "" : $r->platenumber,
          'GSteward' => $user->fname . " " . $user->lname,
          'Remarks' => empty($r->remarks) ? "" : $r->remarks,
          'user_id' => $r->user_id,
          'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')")
        ]);

        $guestinfo = DB::connection('mysql')
        ->table('guestinfo')
        ->insert([
          'RoomName' => $getRoomInfo->room_name,
          'RoomNo' => $getRoomInfo->room_no,
          'RoomType' => $getRoomType->room_type,
          'DateIn' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d')"),
          'nationality' => $nationality == null ? "" : $nationality->name,
          'TimeIn' => DB::raw("DATE_FORMAT(NOW(), '%h:%i:%s %p')"),
          'RateAmount' => $getRate->Amount,
          'RateDesc' => $getRate->RateDesc,
          'MarketSource' => $marketSource->MarketSource,
          'CarMake' => $carMake == null ? "" : $carMake->CarMake,
          'VehicleType' => $vehicleType == null ? "" : $vehicleType->name,
          'PlateNo' => empty($r->platenumber) ? "" : $r->platenumber,
          'GSteward' => $user->fname . " " . $user->lname,
          'Remarks' => empty($r->remarks) ? "" : $r->remarks,
          'user_id' => $r->user_id,
          'created_at' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')")
        ]);

        //POSNerdvana Check-In
        // $roominfo = Operation::GetRoomNo($r->roomId);
        // $adult = 2;
        // $child = 0;
        // $currency_id = "PHP";
        // $currency_value = 1;
        // $room_sale_price_id = "";
        // $tax_info = "0.12";
        // Operation::CheckInPOSNerdvana($r->nationality, $roominfo->room_no, $roominfo->room_type_id, $r->rates, $room_sale_price_id, $r->car, $r->vehicletype, $r->marketsource, $r->platenumber, $r->user_id, $adult, $child, $tax_info, $roominfo->room_area_id, $currency_id, $currency_value, $this->url);
        //8================================================================================================D

    }

    /*
    * change status of rooms in general form
    * special conditions if room is clean to on nego, dirty to dirty with waiting guest
    * regular clean with regular clean with waiting guest
    * required params are the following
    * mobile/tagboard/changeStatus
    * room_id
    * status
    *
    */
    public function changeStatus(Request $r){
        // return "hello";
        // return $r->all();

        if($r->status==6 || $r->status==13 || $r->status==19){

            $val = RmsRoom::hasOnGoingWork($r->user_id);
            if ($val[0]['hasWork']) {
                return response()->json([
                    'changeStatus'=>0,
                    'rmsLogs'=>array(
                                'lastRmsLogId' => 0,
                                'lastWorkId' => 0,
                                'lastRoomBuddies' => 0,
                                'time' => 0
                            ), 
                    'onlineUsers'=>array(),
                    'message' => 'You are still working at room '. $val[0]['roomNumber'] .' , please finish it first'
                ], 200);
            }

        }

        $checkRoom = Rooms::where('id', $r->room_id)->get();

        $iCameFrom = DB::connection('mysql')
        ->table('where_i_came')
        ->insert([
          'function_name' => 'changeStatus Mobile',
          'status' => $r->status,
          'room_no' => $checkRoom[0]->room_no
        ]);

        if($r->status == 11){
            $deleteRmsGuest = RMSLogs::cancelWaitingGuest($checkRoom[0]->room_no);
            $updateRoomHMS = TblRoom::changeHmsStatus($return->room_id, 19);

            if($updateRoomHMS == 1){
                sleep(3);
                $updateFromRoomStatus = DB::connection('mysql')
                ->table('tblroom')
                ->where('id', $r->room_id)
                ->update([
                    'from_room_status_id' => 3
                ]);
            }

            // return response()->json(['changeStatus'=>$updateRoomHMS, 'rmsLogs'=>$rmsLogs, 'onlineUsers'=>array(), 'deleteMoMukhaMo'=>$deleteRmsGuest], 200);
        }else{

            $updateRoomHMS = TblRoom::changeHmsStatus($r->room_id, $r->status);

        }

        $checkLogs = RMSLogs::where('room_id', $r->room_id)
                    ->whereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->get();

        if(count($checkLogs) > 0){
            if ($r->status == 6) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 21, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 19) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 56, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 22 || $r->status == 37 || $r->status == 38) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 5, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 24) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 14, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 51) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 50, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 72) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 71, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else if ($r->status == 75) {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 74, $r->user_id, $r->mobile);
                $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
            } else {
                $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, $r->status, $r->user_id, $r->mobile);
                $rmsLogs = array(
                                'lastRmsLogId' => 0,
                                'lastWorkId' => 0,
                                'lastRoomBuddies' => 0,
                                'time' => 0
                            );
            }
            
        }else{
          $rmsLogs = RMSLogs::insertLogsStart($r->room_id, $r->user_id, $r->mobile, $r->status);
        }

        $insertUserInfo = RMSLogs::updateRoomInfo($r->user_id, $checkRoom[0]->room_no);

        // if(($r->status == 4) ||
        //     ($r->status == 6) ||
        //     ($r->status == 13 ) ||
        //     ($r->status == 19) ||
        //     ($r->status == 20) ||
        //     ($r->status == 22) ||
        //     ($r->status == 24)){
        // }

        $getRoomStatusWithBuddies = DB::connection('mysql')
            ->table('roomstatus')
            ->where('id', $r->status)
            ->first();

        if(($r->status == 21) || ($r->status == 6)){
            
            $updateTblRoomRMS = DB::connection('mysql')
                ->table('tblroom')
                ->where('id', $r->room_id)
                ->update([
                    'last_general_cleaning' => DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d %h:%i:%s')")
                ]);
        }

        $getAllUser = array();

        if (isset($getRoomStatusWithBuddies->is_buddy))
        {
            if($getRoomStatusWithBuddies->is_buddy == 1){
                $getAllUser = UserLocale::where('is_logged_in', '=', 1)
                ->select('id',  
                    'username',
                    DB::raw("concat(firstname, ' ', lastname) as name") 
                )
                ->where('username', '!=', $r->user_id)
                ->get();
            }
        }

        return response()->json(['changeStatus'=>$updateRoomHMS, 'rmsLogs'=>$rmsLogs, 'onlineUsers'=>$getAllUser, 'message' => ''], 200);
    }

    /*
    * method for adding buddies when room is in a state of "needed a clean"
    * @params
    * $r->rmsId (int) 
    * $r->user_id (int)
    * $r->mobile (string)
    */

    public function addBuddies(Request $r){
        // check if rmsId exist on rmslogs table
        $isInsertedBuddy = RMSLogs::validateWorkBuddies($r->rmsId, $r->user_id);
        if($isInsertedBuddy){
            // insert room buddies record via rms_logs to tbl_room_buddies
            return $insert = RMSLogs::workBuddies($r->rmsId, $r->user_id, $r->mobile);
        }else{
            return response()->json([
                'insertBuddies' => false
            ], 403);
        }
    }

    /*
    *   mobile/tagboard/cancel
    *   roomId (int)
    *   for inserting rmsLogsRecord
    *   userId (int)
    *
    */

    public function cancel(Request $r){
        // return $r->all();

        $iCameFrom = DB::connection('mysql')
            ->table('where_i_came')
            ->insert([
              'function_name' => 'cancel Mobile'
            ]);

    	$getRoom = Rooms::where('id', $r->roomId)->get();

        $checkIfRoomHasRoomCheck = RMSLogs::where('id', $getRoom[0]->room_no)
            ->where('e_status', '=', 40)
            ->first();
        if($checkIfRoomHasRoomCheck){
            $insertData = rmsLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, $status);
            if(!empty($insertData)){
                $closeLogsAndRevertHms = RMSLogs::previousStatus($insertData['lastRmsLogId']->rmsId, $r->user_id, $r->mobile);
            }
        }

        if($getRoom[0]->from_room_status_id == 1){
            $cleanRoomHMS = TblRoom::where('RoomName', $getRoom[0]->room_no)
                        ->update([
                            'Stat' => 'CLEAN'
                        ]);
        }
        $closeLogsAndRevertHms = RMSLogs::previousStatus($r->rmsId, $r->user_id, $r->mobile);

        $insertUserInfo = RMSLogs::updateRoomInfo($r->userId, $getRoom[0]->room_no);

    	return response()->json(['workLogs' => $closeLogsAndRevertHms, 'message'=>'changed', 'success'=>true], 200);

    }

    public function getAllOngoingResumeStatus(Request $r) {

        // $resumeAndOnGoingStatuses = RS::select("id")
        //          // ->where('room_status', 'like', '%resume%')
        //          ->orWhere('room_status', 'like', '%on-going%')
        //          ->orWhere('room_status', 'like', '%on going%')
        //          ->orWhere('room_status', 'like', '%on going%')->get();

        // $ids = array();
        // foreach ($resumeAndOnGoingStatuses as $key => $value) {

        //     $id = $value->id;

        //     if(!in_array($id, $ids)){

        //         $query = "(access_levels.room_status_id='". $id ."' AND roomstatus.room_status LIKE '%resume%')";

        //         if ($id == 19) 
        //         {
        //             $query .= " OR (access_levels.room_status_id='". $id ."' AND roomstatus.id = 20)";
        //         }

        //         if (isset($r->role_id)) {

        //             $query .= " AND (access_levels.role_id = ".$r->role_id.")";
        //         }

        //         $ids[] = $id;
        //         $resumeStatuses = AccessLevel::select(
        //             DB::raw("DISTINCT roomstatus.room_status AS 'room_status',
        //                     roomstatus.id")
        //         )
        //         ->join('roomstatus', function($join) use ($id, $query){
        //             $join->on('access_levels.allow_status_id', '=', 'roomstatus.id')
        //             ->whereRaw($query);
        //         })
        //         ->get();


        //         $resumeAndOnGoingStatuses[$key]['next'] = $resumeStatuses;

        //     }
        //     else continue;
        // }
        
        //GELO

        // $resumeAndOnGoingStatuses = AccessLevel::GetOngoingStatus($r->role_id);


        $resumeAndOnGoingStatuses = DB::connection('mysql')
        ->select("
                SELECT 
                access_levels.room_status_id,
                a.room_status,
                b.allow_id,
                b.allow_status
                FROM access_levels
                INNER JOIN roomstatus a ON a.id=access_levels.room_status_id
                INNER JOIN
                (
                    SELECT
                    access_levels.room_status_id,
                    GROUP_CONCAT(roomstatus.id) AS 'allow_id',
                    GROUP_CONCAT(roomstatus.room_status) AS 'allow_status'
                    FROM access_levels
                    INNER JOIN roomstatus ON roomstatus.id=access_levels.allow_status_id
                    WHERE role_id='". $r->role_id ."'
                    AND (roomstatus.room_status LIKE '%resume%' OR roomstatus.room_status LIKE '%nego%') 
                    GROUP BY role_id, room_status_id    
                ) b ON b.room_status_id=access_levels.room_status_id
                WHERE role_id='". $r->role_id ."' AND (a.room_status LIKE '%on-going%' OR a.room_status LIKE '%on going%' OR a.room_status LIKE '%ongoing%') GROUP BY role_id, room_status_id
        ");

        
        $linis = array();

        foreach ($resumeAndOnGoingStatuses as $key => $value) 
        {


            $ids = explode(",", $value->allow_id);
            $statuses = explode(",", $value->allow_status);

            // $status = array();

            $linis[] = array("id" => $value->room_status_id);

            foreach ($ids as $k => $v) {

                 $linis[count($linis) - 1]["next"][] = array(
                    "id" => $v,
                    "room_status" => $statuses[$k]
                 );
            }

            // $resumeAndOnGoingStatuses->{$key}["malinis"] = $linis;
        }

        return response()->json([
            'status' => $linis
        ]);

    }

    public function getAllResumeStatus(Request $r) {
        $resumeStatuses = RS::select("*")
                 ->where('room_status', 'like', '%resume%')->get();

        return response()->json([
                'status' => $resumeStatuses
            ]);
    }

    /*
    * mobile/tagboard/getRoomCurrentStatus
    * check all current room status then update mv for android and/or ios
    * required params
    * roomId (int)
    */

    public function checkRoomStatusForUpdate(Request $r){

        if ($r->exists('allStatus')) {

        $resumeAndOnGoingStatuses = RS::select("id")
                 ->where('room_status', 'not like', '%resume%')
                 ->where('room_status', 'not like', '%on-going%')
                 ->where('room_status', 'not like', '%on going%')
                 ->where('room_status', 'not like', '%on going%')->get();


            $getCurrentRoomStatus = Rooms::select('room_status_id')
            ->where('id', $r->roomId)
            ->first();

            $getNextStep = AccessLevel::select(
                            'access_levels.room_status_id',
                            'access_levels.allow_status_id',
                            'roomstatus.room_status'
                        )
                        ->where('role_id', $r->roleId)
                        ->whereIn('allow_status_id', $resumeAndOnGoingStatuses)
                        ->where('room_status_id', $getCurrentRoomStatus->room_status_id)
                        ->join('roomstatus', 'access_levels.allow_status_id', '=', 'roomstatus.id')
                        ->get();

            return response()->json([
                // 'currentStatus' => $getCurrentRoomStatus,
                'nextStep' => $getNextStep
            ]);
        } else {
            $getCurrentRoomStatus = Rooms::select('room_status_id')
            ->where('id', $r->roomId)
            ->first();

            $getNextStep = AccessLevel::select(
                            'access_levels.room_status_id',
                            'access_levels.allow_status_id',
                            'roomstatus.room_status'
                        )
                        ->where('role_id', $r->roleId)
                        ->where('room_status_id', $getCurrentRoomStatus->room_status_id)
                        ->join('roomstatus', 'access_levels.allow_status_id', '=', 'roomstatus.id')
                        ->get();

            return response()->json([
                // 'currentStatus' => $getCurrentRoomStatus,
                'nextStep' => $getNextStep
            ]);
        }

        

    }

    /*
    *   mobile/tagboard/cancelRoomCheck
    *   @params
    *   roomId (int)
    *   status (int) const 31
    *
    */
    public function cancelRoomCheck(Request $r){
        // return $r->all();

        $userId = "10907";
        $mobile = "Autopilot";

        $getRoomInfo = Rooms::getRoomById($r->roomId);

        $checkLogs = RMSLogs::where('room_id', $r->roomId)
                    ->whereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->get();

        if (!empty($r->user_id)) {
            $userId = $r->user_id;
            $mobile = $r->mobile;
        }

        if(count($checkLogs) > 0){
            $rmsUpdate = RMSLogs::updateRMSLogsAndWorkLogs($checkLogs[0]->id, 53, $r->user_id, $r->mobile);
        }else{
            $rmsLogs = RMSLogs::insertLogsStart($r->roomId, $r->user_id, $r->mobile, 53);
        }

        $insertUserInfo = RMSLogs::updateRoomInfo($r->user_id, $getRoomInfo->room_no);

        $updateHMS = TblRoom::where('RoomNo', $getRoomInfo->room_no)
            ->update([
                'CRoom_Stat' => "6969"
            ]);

        return response()->json([
            'updateHMS' => $updateHMS,
        ]);
    }


    public function getBuddies(Request $request) {

        $this->rule = array(
            'user_id' => 'required'
        );

        $this->validate($request->all(), $this->rule);

        if($this->data['error']['status'] == false){
            $this->data['response'] = UserLocale::where('is_logged_in', '=', 1)
                        ->select('id',  
                            'username',
                            DB::raw("concat(firstname, ' ', lastname) as name") 
                        )
                        ->where('username', '!=', $request->user_id)
                        ->get();
        }

        return response()->json($this->data, $this->statusCode);
                  
    }

}