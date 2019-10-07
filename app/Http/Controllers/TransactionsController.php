<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
//ROOM RMS
use App\Model\Room\RoomByCategory as Rooms;
use App\Model\Auth\UserLocale as check;
use App\AccessLevel as AccessLevel;
use App\LoginMobile as LoginMobile;
use App\InspectionComponent as InspectionComponent;
use App\Standard as Standard;
use App\InspectionLink;
use App\RmsRoom;
// use App\InspectionComponent as InspectionComponent;
// use App\Standard as Standard;
// use App\InspectionLink;
use App\HMSTableRoom;
use DB;
use GuzzleHttp\Client;
use App\Model\VCReserve\LocaleID as Locale;
use App\Model\RMSLogs_Model as logs;
use App\Model\Mobile\WorkLogs;
use App\Model\HMS\TblRoom;
use App\Model\HistoricalInspection;
use Carbon\Carbon;
use App\Jobs\Inspections\InspectionSave;


class TransactionsController extends Controller
{

    public function roomCheckChecklist(Request $request) {
        $wholeData = InspectionLink::select(
            'tbl_deduction.tbl_findings_type_id as finding_type',
            'tbl_links.id as links_id',
            'tbl_links.standard_id as standard_id',
            'tbl_standards.name as standard_name',
            'tbl_remarks.name as remark_name',
            'tbl_deduction.tbl_findings_type_id as finding_type')
            ->leftJoin('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')
            ->leftJoin('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->leftJoin('tbl_deduction', 'tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
            ->where('tbl_links.standard_id', '=', '3')
            ->where('area_id', '=', 0)
            ->where('component_id', '=', 0)
            ->get();

        $this->data['response'] = $wholeData;


        return response()->json($this->data, $this->statusCode);
    }

    public function resumeInspection(Request $request) {

        $this->rule = array(
            'user_id'=> 'required',
            'room_number'=> 'required'
        );

        $this->validate($request->all(), $this->rule);

        //Validation
        //Get Room Info
        // $roomninfo = Rooms::select(
        //     'from_userinfo'
        // )
        // ->where('room_no', '=', $request->room_number)
        // ->get();

        // //Get User Info
        // $userinfo = check::where('username', '=', $request->user_id)->first();
        
        // if($roomninfo[0]->from_userinfo!=$userinfo->username){

        //     if($userinfo->role_id!=2 && $userinfo->role_id!=22 && $userinfo->role_id!=59 && $userinfo->role_id!=27 && $userinfo->role_id!=57){

        //         $this->data['response'] = array(
        //             "next_steps" => array(),
        //             "last_rms_log_id" => "",
        //             "message" => "Bawal mang agaw wag gayahin si dione at gelo."
        //         );
        //         $this->statusCode = 403;
    
        //         $this->responseError("Bawal mang agaw wag gayahin si dione at gelo.");
        //         return response()->json($this->data, $this->statusCode);
    
        //     }

        // }

        //Begin Process
        if(empty($this->data['error']['error_message'])) {

            $hasPending = WorkLogs::checkIfHasPending($request->user_id);

            $user = check::where('username', '=', $request->user_id)->first();

            if (empty($user)) {
                $this->data['response'] = array(
                    "next_steps" =>array(),
                    "last_rms_log_id" => ""
                );
                $this->data['error']['error_message'] = "You have no access";
                $this->statusCode = 403;
                return response()->json($this->data, $this->statusCode);
            }

            $nextSteps = AccessLevel::where('role_id', '=', $user->role_id)
            ->where('room_status_id', '=', "13")
            ->join('roomstatus', 'allow_status_id', '=', 'roomstatus.id')
            ->get();

            $connection = DB::connection('mysql');
            $roomDetails = $connection->table('tblroom')
                ->select('id')
                ->where('room_no', '=', $request->room_number)->get();

            // if ($hasPending['hasPending']) {
            $rmsLogsRecord = logs::GetRMSLogsProfile($roomDetails[0]->id)[0];
            $this->data['response'] = array(
                "next_steps" =>$nextSteps,
                // "logsId" => $insertNew,
                "last_rms_log_id" => $rmsLogsRecord->id,
                "time" => $rmsLogsRecord->time
            );

            // $dateNow = DB::select(DB::raw("SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS 'time'"));
            // $dateNow = DB::raw("TIMESTAMPDIFF(SECOND, tblroom.updated_at, NOW()) AS 'time_in_seconds'"),


            // }
            // else {
            //     $this->data['response'] = array(
            //         "next_steps" =>array(),
            //         // "logsId" => array(),
            //         "last_rms_log_id" => ""
            //     );
            //     $this->data['error']['error_message'] = "Room is in use";
            //     $this->statusCode = 403;
            // }

        }

        return response()->json($this->data, $this->statusCode);

    }

    public function getMainNotifications(Request $request) {

        $this->rule = array(
            'locale_id'=> 'required|integer'
        );
        $this->validate($request->all(), $this->rule);

        if (!$this->data['error']) {
            $connection = DB::connection('inspections');
            $mainTable = $connection->table('broadcast_main')
            ->select(DB::raw('COUNT(broadcast_main.id) AS TOTAL'))->get();
            // $mainTable[0]->TOTAL
            $detailsTable = $connection->table('broadcast_details')
            ->select(DB::raw('count(id) as TOTAL'))
            ->where('locale_id','=',$request->locale_id)
            ->where('done_by', '!=', null)
            ->get();

            $this->data['response'] = array('inspections' => $mainTable[0]->TOTAL - $detailsTable[0]->TOTAL);

            // return $detailsTable;

        }

        return response()->json($this->data, $this->statusCode);
    }

    public function activityLogs(Request $request) {
        $connection = DB::connection('mysql');

        if (empty($request->user_id)) {

            $this->data['response'] =  $connection->table('work_logs')
                ->select(DB::raw("IFNULL(user.username, '') AS 'username'"),
                         DB::raw("IFNULL(user.firstname, '') AS 'firstname'"),
                         DB::raw("IFNULL(user.middlename, '') AS 'middlename'"),
                         DB::raw("IFNULL(user.lastname, '') AS 'lastname'"),
                         DB::raw("IFNULL(roles.role, '') AS 'role_description'"),
                         DB::raw("IFNULL(roomstatus.room_status, '') AS 'work_description'"),
                         DB::raw("IFNULL(work_logs.room_number, '') AS 'room_number'"),
                         DB::raw("IFNULL(work_logs.created_at, '') AS 'created_at'"),
                         DB::raw("IF(work_logs.status = 1, 'Completed', 'Pending') AS 'work_status'"))
                ->join('user', 'work_logs.user_id', '=', 'user.username')
                ->join('roomstatus', 'work_logs.work_id', '=', 'roomstatus.id')
                ->join('roles', 'roles.id', '=', 'user.role_id')
                ->orderBy('work_logs.status', 'asc' )
                ->orderBy('work_logs.created_at', 'desc')
                ->where('work_logs.created_at', '>=', \Carbon\Carbon::now()->startOfMonth())
                ->get();
        } else {
            $this->data['response'] =  $connection->table('work_logs')
                ->select(DB::raw("IFNULL(user.username, '') AS 'username'"),
                         DB::raw("IFNULL(user.firstname, '') AS 'firstname'"),
                         DB::raw("IFNULL(user.middlename, '') AS 'middlename'"),
                         DB::raw("IFNULL(user.lastname, '') AS 'lastname'"),
                         DB::raw("IFNULL(roles.role, '') AS 'role_description'"),
                         DB::raw("IFNULL(roomstatus.room_status, '') AS 'work_description'"),
                         DB::raw("IFNULL(work_logs.room_number, '') AS 'room_number'"),
                         DB::raw("IFNULL(work_logs.created_at, '') AS 'created_at'"),
                         DB::raw("IF(work_logs.status = 1, 'Completed', 'Pending') AS 'work_status'"))
                ->join('user', 'work_logs.user_id', '=', 'user.username')
                ->join('roomstatus', 'work_logs.work_id', '=', 'roomstatus.id')
                ->join('roles', 'roles.id', '=', 'user.role_id')
                ->where('work_logs.user_id', '=', $request->user_id)
                ->where('work_logs.created_at', '>=', \Carbon\Carbon::now()->startOfMonth())
                ->orderBy('work_logs.status', 'asc' )
                ->orderBy('work_logs.created_at', 'desc')
                ->get();
        }

        return response()->json($this->data, $this->statusCode);
    }

    public function startInspection(Request $request) {
        // return $request->all();
        $this->rule = array(
            'user_id'=> 'required',
            'room_number'=> 'required',
            'mobile'    => 'required',
            'workId'    => 'required',
        );
        $this->validate($request->all(), $this->rule);
        $user = check::where('username', '=', $request->user_id)->first();

        $val = RmsRoom::hasOnGoingWork($request->user_id);
        if ($val[0]['hasWork']) {
            $this->data['response'] = array(
                        'current_room_status'=> 0,
                        'message'=>'You are still working at room '. $val[0]['roomNumber'] .' , please finish it first',
                        'rmsLogs' => array(
                                        'lastRmsLogId' => 0,
                                        'lastWorkId' => 0,
                                        'lastRoomBuddies' => 0,
                                        'time' => 0
                                    ),
                        'updateUserInfo' => 0,
                        'next_steps' => AccessLevel::where('role_id', '=', $user->role_id)
                                        ->Where('room_status_id', '=', "13")
                                        ->join('roomstatus', 'allow_status_id', '=', 'roomstatus.id')
                                        ->get());
            $this->responseError('You are still working at room '. $val[0]['roomNumber'] .' , please finish it first');
            return response()->json($this->data, $this->statusCode);
        }

        if (empty($this->data['error']['error_message'])) {
            try {
                $connection = DB::connection('mysql');

                $getRoomId = Rooms::where('room_no', $request->room_number)->get();

                // $roomId, $userId, $mobile, $roomStatus, $workId
                // $connection->table('hms.tblroom')
                //     ->where('RoomNo' ,'=', $request->room_number)
                //     ->update(array('CRoom_Stat' => '13', 'Stat' => 'DIRTY')
                // );
                //TODO dito ako
                $changeStatusHMS = TblRoom::changeHmsStatus($getRoomId[0]->id, $request->workId);

                $user = check::where('username', '=', $request->user_id)->first();
                $nextSteps = AccessLevel::where('role_id', '=', $user->role_id)
                ->Where('room_status_id', '=', "13")
                ->join('roomstatus', 'allow_status_id', '=', 'roomstatus.id')
                ->get();

                $roomDetails = $connection->table('tblroom')
                ->select('room_status_id as current_room_status', 'id')
                ->where('room_no', '=', $request->room_number)->get();

                // check if rms_logs open
                $roomCheck = DB::connection('mysql')
                    ->table('rms_logs')
                    ->where('room_id', '=', $getRoomId[0]->id)
                    ->whereNull('e_status')
                    ->orderBy('id', 'DESC')
                    ->first();

                if($roomCheck){
                    $query2 = logs::updateRMSLogsAndWorkLogs($roomCheck->id, 7, $request->user_id, $request->mobile);
                    $query = logs::insertLogsStart($roomDetails[0]->id, $request->user_id, $request->mobile, $request->workId);
                }else{
                    $query = logs::insertLogsStart($roomDetails[0]->id, $request->user_id, $request->mobile, $request->workId);
                }

                $updateUserInfo = logs::updateUserInfo($request->room_number, $request->user_id);

                $locale = Locale::select('*')->first();
                // $this->emitUsersOnline($locale->local_id);
                if (count($roomDetails) > 0) {
                    $this->data['response'] = array(
                        'current_room_status'=> $roomDetails[0]->current_room_status,
                        'message'=>'Inspection successfully saved',
                        'rmsLogs' => $query,
                        'updateUserInfo' => $updateUserInfo,
                        'next_steps' => $nextSteps);
                }
            } catch (\Exception $e) {
                $this->data['error'] = array($e->getMessage());
                $this->statusCode = 403;
            }
        }

        return response()->json($this->data, $this->statusCode);

    }

    public function fetchOnlineUsers(Request $request) {
        $connection = DB::connection('mysql');
        $this->data['response'] =  $connection->table('user')
            ->select(DB::raw("IFNULL(user.username, '') AS 'username'"),
                    DB::raw("IFNULL(user.firstname, '') AS 'firstname'"),
                    DB::raw("IFNULL(user.middlename, '') AS 'middlename'"),
                    DB::raw("IFNULL(user.lastname, '') AS 'lastname'"),
                    DB::raw("IFNULL(work_logs.created_at, '') AS 'created_at'"),
                    DB::raw("IFNULL(work_logs.work_id, '') AS 'work_id'"),
                    DB::raw("IFNULL(work_logs.room_number, '') AS 'room_number'"),
                    DB::raw("IFNULL(roomstatus.room_status, '') AS 'status_description'"),
                    DB::raw("IFNULL(work_logs.room_number, '') AS 'room_number'"),
                    DB::raw("IFNULL(user.status, '') AS 'statusUser' "))
            ->where('is_logged_in', '=', '1')

            ->leftJoin('work_logs', function($join) use ($request){
                $join->on('user.username','=','work_logs.user_id')
                ->where('work_logs.status', '=', '0')
                ;
            })
            ->leftJoin('roomstatus', 'roomstatus.id', '=', 'work_logs.work_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json($this->data, $this->statusCode);

    }

    public function fetchAreas(Request $request) {
        $connection = DB::connection('mysql');
            $res = $connection->table('tbl_areas')
            ->select('*')
            ->get();
        if ($res) {
            $this->data['response'] = array('result' => $res);
        } else {
            $this->data['response'] = array();
        }
        return response()->json($this->data, $this->statusCode);
    }


    public function fetchBroadcastNotificationDetails(Request $request) {

        $this->rule = array(
            'broadcast_id'=> 'required|integer'
        );
        $this->validate($request->all(), $this->rule);

        if (empty($this->data['error']['error_message'])) {
            $connection = DB::connection('inspections');
            $res = $connection->table('locales')
            ->select(
                'locales.id as lid',
                'locales.Locale as locale',
                DB::raw("IFNULL(broadcast_main_id, '') AS 'broadcast_main_id'"),
                DB::raw("IFNULL(remarks, '') AS 'remarks'"),
                DB::raw("IFNULL(acknowledged_by, '') AS 'acknowledged_by'"),
                DB::raw("IFNULL(done_by, '') AS 'done_by'"),
                DB::raw("IFNULL(remarks, '') AS 'remarks'"),
                DB::raw("IFNULL(broadcast_details.created_at, '') AS 'created_at'"),
                DB::raw("IFNULL(broadcast_details.updated_at, '') AS 'updated_at'")
            )
            ->leftJoin('broadcast_details', function($join) use ($request){
                $join->on('locales.id','=','broadcast_details.locale_id')
                ->where('broadcast_details.broadcast_main_id', '=', $request->broadcast_id)
                ->orderBy('created_at', 'ASC');
            })
            ->get();
            if ($res) {
                $this->data['response'] = array('result' => $res);
            } else {
                $this->data['response'] = array();
            }
        }
        return response()->json($this->data, $this->statusCode);
    }



    public function fetchBroadcastNotifications(Request $request) {

        $this->rule = array(
            'locale_id' => 'required|integer'
        );
        $this->validate($request->all(), $this->rule);
        if (empty($this->data['error']['error_message'])) {

            $connection = DB::connection('inspections');
            $res = $connection->table('broadcast_main')
            ->select(
                'locales.Locale as locale',
                'broadcast_main.initiated_by_locale_id as locale_id',
                'broadcast_main.id',
                'broadcast_main.area_id',
                'broadcast_main.remarks',
                'broadcast_main.created_at',
                'broadcast_main.area_description',
                'broadcast_main.type',
                'broadcast_main.image',
                DB::raw("IFNULL(broadcast_details.remarks, '') AS 'done_remarks'"),
                DB::raw("IFNULL(broadcast_details.acknowledged_by, '') AS 'acknowledged_by'"),
                DB::raw("IFNULL(broadcast_details.done_by, '') AS 'done_by'"))
            ->join('locales', 'broadcast_main.initiated_by_locale_id', '=', 'locales.id')
            ->leftJoin('broadcast_details', function($join) use ($request){
                $join->on('broadcast_main.id','=','broadcast_details.broadcast_main_id')
                ->where('broadcast_details.locale_id', '=', $request->locale_id)
                ->orderBy('created_at', 'ASC');
            })
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

            if ($res) {
                $this->data['response'] = array('result' => $res);
            } else {
                $this->data['response'] = array();
            }
        }

        return response()->json($this->data, $this->statusCode);
    }


    public function markAsDoneBroadcastDetails(Request $request) {
        $this->rule = array(
            'broadcast_id' => 'required|integer',
            'user_id' => 'required|integer',
            'locale_id' => 'required|integer',
            'remarks' => 'required|string'
        );
        $this->validate($request->all(), $this->rule);
        if (empty($this->data['error']['error_message'])) {
             try {
                $connection = DB::connection('inspections');
                $res = $connection->table('broadcast_details')
                ->where('broadcast_main_id', $request->broadcast_id)
                ->where('locale_id', $request->locale_id);
                $selected = $res->select('*')->first();
                if ($selected) {
                    if ($selected->done_by) {
                        $this->data['response'] = array(array('message' => 'Response already marked as done'));
                    } else {
                        $res->update([
                            'remarks'=> $request->remarks,
                            'done_by'=> $request->user_id,
                            'updated_at'=> DB::raw("NOW()")
                        ]);
                    }
                    $this->emitBroadcastResponse($request->broadcast_id);
                    $this->data['response'] = array(array('message' => 'Response marked as done'));
                } else {
                    $this->responseError('Record not existing');
                    // $this->data['error'] = array('message'=>'Record not existing');
                    $this->statusCode = 403;
                }
            } catch (Exception $e) {
                $this->data['error'] = array($e->getMessage());
                $this->statusCode = 403;
            }
        }

        return response()->json($this->data, $this->statusCode);
    }



    public function acknowledgeBroadcast(Request $request) {

        $this->rule = array(
            'broadcast_id' => 'required|integer',
            'user_id' => 'required|integer',
            'locale_id' => 'required|integer'
        );
        $this->validate($request->all(), $this->rule);
        if (empty($this->data['error']['error_message'])) {
             try {
                $connection = DB::connection('inspections');
                $acknowledgedRecord = $connection->table('broadcast_details')
                ->where('broadcast_main_id', '=', $request->broadcast_id)
                ->where('locale_id', '=', $request->locale_id)->get();

                if (count($acknowledgedRecord) > 0) {
                    $this->data['response'] = array(array('message' => 'Broadcast already acknowledged'));
                } else {
                    $connection->table('broadcast_details')
                    ->insert(array(
                        'broadcast_main_id'=> $request->broadcast_id,
                        'acknowledged_by' => $request->user_id,
                        'locale_id' => $request->locale_id
                    ));
                    $this->emitBroadcastResponse($request->broadcast_id);
                    $this->data['response'] = array(array('message' => 'Broadcast Acknowledged'));
                }

            } catch (Exception $e) {
                $this->responseError($e->getMessage);
                // $this->data['error'] = array($e->getMessage());
                $this->statusCode = 403;
            }
        }

        return response()->json($this->data, $this->statusCode);
    }

    public function sendBroadcastInspection(Request $request) {

        $this->rule = array(
            'type' => 'required',
            'area_description' => 'required',
            // 'area_id' => 'required|string',
            'remarks' => 'required',
            'user_id' => 'required|integer',
            'locale_id' => 'required|integer'
        );
        $image = "";



        $this->validate($request->all(), $this->rule);
        if (empty($this->data['error']['error_message'])) {

            try {
                if (!empty($request->image)) {
                    $image = $this->uploadImage($request->image, $request->user_id);
                }
                $connection = DB::connection('inspections');
                $connection->table('broadcast_main')
                ->insert(array(
                    'type' => $request->type,
                    'area_description' => $request->area_description,
                    'area_id'=> str_replace("s-", "", $request->area_id),
                    'remarks'=> $request->remarks,
                    'initiated_by_user_id' => $request->user_id,
                    'initiated_by_locale_id' => $request->locale_id,
                    'image' => $image,
                    'status' => 1
                ));
                $this->emitCreatedBroadcast();
                $this->data['response'] = array(array('message' => 'Broadcast Inspection successful'));
            } catch (Exception $e) {
                $this->data['error'] = array($e->getMessage());
                $this->statusCode = 403;
            }
        }
        return response()->json($this->data, $this->statusCode);
    }

    private function emitCreatedBroadcast() {
        $client = new Client([
            'base_uri' => 'http://192.168.1.23:6965/',
            'timeout'  => 2.0,
        ]);
        $client->request('GET', 'emitBroadcastInspect');
    }

    private function emitBroadcastResponse($broadcast_id) {
        $client = new Client([
            'base_uri' => 'http://192.168.1.23:6965/',
            'timeout'  => 2.0,
        ]);
        $client->request('GET', 'emitBroadcastResponse', ['query' => ['broadcast_id' => $broadcast_id]]);
    }

    private function emitUsersOnline($locale_id = null) {
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

    public function saveAnyRemarks(Request $request) {
        //TODO save any remarks
        // return $request->all();
        // return json_decode($request->remarks);
        if(!empty($request->room_no) && !empty($request->standardId)){
            $this->data['response'] = "One at a time";
            return response()->json($this->data, $this->statusCode);
        }

        if(empty($request->room_no) && empty($request->standardId)){
            $this->data['response'] = "Magbigay ka naman ng value kahit 1 lang";
            return response()->json($this->data, $this->statusCode);
        }

        $userId = $request->user_id;
        $roomId = 0;

        $connection = DB::connection('mysql');

        $remarks = $this->fixRemarksData($request);

        DB::table('tbl_transactions')
        ->insert($remarks);

        $latestId = DB::table('tbl_transactions')
            ->select('id', 'created_at')->orderBy('id', 'DESC')->first();

        $ids = DB::connection('mysql')
        ->table('tbl_transactions')

        ->select(
            'tbl_transactions.id as id',
            'tbl_transactions.created_at as created_at'
        )

        // join
        ->leftJoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
        // end of join

        ->where('tbl_transactions.created_at', '=', $latestId->created_at)
        ->where('tbl_links.standard_id', '<>', 1)
        ->where('tbl_links.standard_id', '<>', 3)
        ->get();

        if(count($ids) > 0){
            $dt = new \DateTime($ids[0]->created_at);

            $dateFormat = $dt->format('H:i:s');
            $dateYearFormat = $dt->format('Y-m-d');

            $dateYearSpliced = str_replace("-", "", $dateYearFormat);
            $dateSpliced = str_replace(":", "", $dateFormat);

            $newFormat = $dateYearSpliced . $dateSpliced;

            $room_id = DB::connection('mysql')
            ->table('tblroom')
            ->where('room_no', $request->room_no)->first();

            $id = array();
            foreach($ids as $out){
                $id[] = $out->id;
                // return $id;
            }

            // return $id;
            // dd($id);
            if(!empty($room_id)) {
                $roomId = $room_id->id;
            }

            $fixedHistoricalData = $this->fixHistoricalData($request, $roomId, $newFormat, $id); 

            DB::connection('mysql')
            ->table('historical_inspection')
            ->insert(
                $fixedHistoricalData 
            ); 

        }
        

        // $this->emitForInspectionHistoricalo();

        $this->data['response'] = "Remarks saved";
        return response()->json($this->data, $this->statusCode);

    }

    private function emitForInspectionHistoricalo() {
        $url = $_SERVER['SERVER_ADDR'];
        $client = new Client([
            // 'base_uri' => 'http://'.$url.':6965/',
            'base_uri' => 'http://192.168.1.8:6969/',
            'timeout'  => 2.0,
        ]);
        $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => "100"]]);
        // if (empty($locale_id)) {
            
        // } else {
        //     $client->request('GET', 'emitOnlineUsers', ['query' => ['locale_id' => $locale_id]]);
        // }
        
    }

    public function saveInspection(Request $request){
        //TODO saveInspection
        /**
        pointsMaintenance
        pointsHouseKeeping
        **/
        $this->rule = array(
            'room_no' => 'required|integer',
            'user_id' => 'required|integer',
            'new_room_status' => 'required|integer',
            'rmsId' => 'required',
            'transFrom' => 'sometimes|string'
        );

        $this->validate($request->all(), $this->rule);

        if (empty($this->data['error']['error_message'])) {
            $remarks = $this->fixRemarksData($request);
            // return $this->isRoomExisting($request->room_no);
            if ($this->isRoomExisting($request->room_no)) {
                    // return "i entered here";
                    $userId = $request->user_id;

                    try {
                        $connection = DB::connection('hms');
                        $connection->transaction(function () use($connection, $request, $remarks) {
                            //TODO saveInspection

                            // $saveInspection = (new InspectionSave($connection, $request, $remarks));

                            $roomId = Rooms::where('room_no', $request->room_no)->get();

                            $updateRoomHMS = TblRoom::changeHmsStatus($roomId[0]->id, $request->new_room_status);

                            $logsCheck = logs::where('room_id', $roomId[0]->id)
                                ->whereNull('e_status')
                                ->orderBy('id', 'DESC')
                                ->first();

                            if(!empty($logsCheck)){
                              $rmsLogs = logs::updateRMSLogsAndWorkLogs($logsCheck->id, $request->new_room_status, $request->user_id, $request->transFrom);
                            }else{
                              $rmsLogs = logs::insertLogsStart($roomId[0]->id, $request->user_id, $request->transFrom,  $request->new_room_status);
                            }

                            //start here
                            $latestId = DB::table('tbl_transactions')
                            ->select('id')->orderBy('id', 'DESC')->first();

                            DB::table('tbl_transactions')
                            ->insert($remarks);

                            /**
                            house keeping instance save - start
                            **/

                            $forHouseKeeping = DB::connection('mysql')
                            ->table('tbl_transactions as a')
                            ->select(
                                'a.id as id',
                                'a.created_at as created_at'
                            )
                            ->join('tbl_links as b', 'a.links_id', '=', 'b.id')
                            ->where('a.id', '>', $latestId->id)
                            ->where('b.standard_id', '=', 1)
                            ->get();

                            if(count($forHouseKeeping) > 0){
                                $dt = new \DateTime($forHouseKeeping[0]->created_at);

                                $dateFormat = $dt->format('H:i:s');
                                $dateYearFormat = $dt->format('Y-m-d');

                                $dateYearSpliced = str_replace("-", "", $dateYearFormat);
                                $dateSpliced = str_replace(":", "", $dateFormat);

                                $newFormat = $dateYearSpliced . $dateSpliced;

                                $room_idHK = DB::connection('mysql')
                                ->table('tblroom')
                                ->where('room_no', $request->room_no)->first();

                                $idHk = array();
                                foreach($forHouseKeeping as $out){
                                    $idHk[] = $out->id;
                                }

                                if(!empty($room_idHK)) $roomId2 = $room_idHK->id;
                                // foreach($idHk as $insertHk){
                                $fixedHouseKeepingData = $this->fixHouseKeepingData($request, $roomId2, $newFormat, $idHk);
                                    DB::connection('mysql')
                                    ->table('historical_inspection')
                                    ->insert(
                                        $fixedHouseKeepingData
                                    );
                                // }

                            } else {

                                $data = DB::connection('mysql')
                                ->table('tbl_transactions')
                                ->select('*')
                                ->where('tbl_transactions.id', '=', $latestId->id)
                                ->get();

                                $dt = new \DateTime($data[0]->created_at);
                                $dateFormat = $dt->format('H:i:s');
                                $dateYearFormat = $dt->format('Y-m-d');

                                $dateYearSpliced = str_replace("-", "", $dateYearFormat);
                                $dateSpliced = str_replace(":", "", $dateFormat);

                                $newFormat = $dateYearSpliced . $dateSpliced;

                                DB::connection('mysql')
                                ->table("tbl_score_inspection")
                                ->insert([
                                    'batch_number' => $newFormat,
                                    'score' => $request->pointsHouseKeeping,
                                    'rating_id' => DB::raw("(SELECT id FROM tbl_score_rating WHERE score_from <= '".$request->pointsHouseKeeping."' AND score_to >= '".$request->pointsHouseKeeping."')"),
                                    'created_at' => DB::raw("NOW()"),
                                    'updated_at' => DB::raw("NOW()")
                                ]);
                            }

                            /**
                            house keeping instance save - end
                            **/

                            /**
                            maintenance instance save - start
                            */

                            $ids = DB::connection('mysql')
                            ->table('tbl_transactions')

                            ->select(
                                'tbl_transactions.id as id',
                                'tbl_transactions.created_at as created_at'
                            )

                            // join
                            ->join('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
                            // end of join

                            ->where('tbl_transactions.id', '>', $latestId->id)
                            ->where('tbl_links.standard_id', '<>', 1)
                            ->where('tbl_links.standard_id', '<>', 3)
                            ->get();

                            if(count($ids) > 0){
                                $dt = new \DateTime($ids[0]->created_at);

                                $dateFormat = $dt->format('H:i:s');
                                $dateYearFormat = $dt->format('Y-m-d');

                                $dateYearSpliced = str_replace("-", "", $dateYearFormat);
                                $dateSpliced = str_replace(":", "", $dateFormat);

                                $newFormat = $dateYearSpliced . $dateSpliced;

                                $room_id = DB::connection('mysql')
                                ->table('tblroom')
                                ->where('room_no', $request->room_no)->first();

                                $id = array();
                                foreach($ids as $out){
                                    $id[] = $out->id;
                                }
                                // return "I was here";
                                if(!empty($room_id)) {
                                    $roomId = $room_id->id;
                                }
                                $fixedHistoricalData = $this->fixHistoricalData($request, $roomId, $newFormat, $id);
                                    DB::connection('mysql')
                                    ->table('historical_inspection')
                                    ->insert(
                                        $fixedHistoricalData
                                    );
                                // foreach($id as $insert){

                                // }
                            /**
                            maintenance instance save - end
                            */
                            }

                            $closeRMS = logs::closeInspectionRMSLogs($request->rmsId, $request->new_room_status);

                        });
                        // $this->saveAnyRemarks($request);
                        $this->data['response'] = array('message'=>'Inspection successfully ended');
                    } catch (\Exception $e) {
                        $this->data['error'] = array($e->getMessage());
                        $this->statusCode = 403;
                    }
                }
            } else {
                $this->data['error']['error_message'] = "Room not existing";
                $this->statusCode = 403;
            }

        return response()->json($this->data, $this->statusCode);
    }

    public static function fixHouseKeepingData($request, $roomId2, $newFormat, $id){
        $remarks = array();
        foreach ($id as $value) {
            array_push($remarks, array(
                'user_id' => $request->user_id,
                'room_id' => empty($roomId2) ? "" : $roomId2,
                'batch_number' => $newFormat,
                'transaction_id' => $value,
                'standard_id' => $request->standardId,
                'process_code' => 'HK'
            ));
        }
        DB::connection('mysql')
        ->table("tbl_score_inspection")
        ->insert([
            'batch_number' => $newFormat,
            'score' => $request->pointsHouseKeeping,
            'rating_id' => DB::raw("(SELECT id FROM tbl_score_rating WHERE score_from <= '".$request->pointsHouseKeeping."' AND score_to >= '".$request->pointsHouseKeeping."')"),
            'created_at' => DB::raw("NOW()"),
            'updated_at' => DB::raw("NOW()")
        ]);

        return $remarks;
    }

    public static function fixHistoricalData($request, $roomId, $newFormat, $id) {

        $remarks = array();

        foreach ($id as $value) {
            array_push($remarks, array(
                'user_id' => $request->user_id,
                'room_id' => empty($roomId) ? "" : $roomId,
                'batch_number' => $newFormat,
                'transaction_id' => $value,
                'standard_id' => $request->standardId,
                'process_code' => 'MT'
            ));
        }

        DB::connection('mysql')
        ->table("tbl_score_inspection")
        ->insert([
            'batch_number' => $newFormat,
            'score' => $request->pointsMaintenance,
            'rating_id' => DB::raw("(SELECT id FROM tbl_score_rating WHERE score_from <= '".$request->pointsMaintenance."' AND score_to >= '".$request->pointsMaintenance."')"),
            'created_at' => DB::raw("NOW()"),
            'updated_at' => DB::raw("NOW()")
        ]);

        return $remarks;

    }


    public static function fixRemarksData($request) {
    	$remarks = array();

        $all_remarks = $request->remarks;
        if (!is_array($all_remarks))
            $all_remarks = json_decode($all_remarks, true);
        if (count($all_remarks) > 0)
        {
            foreach ($all_remarks as $key => $value)
            {
                $value['updated_at'] = DB::raw("NOW()");
                array_push($remarks, $value);
            }
        }
        else {
            array_push($remarks, array(
            'links_id'=> 0,
            'user_id' => $request->user_id,
            'room_no' => $request->room_no,
            'points' => "0",
            'updated_at' => DB::raw("NOW()")));
        }

		return $remarks;
    }

    private function isRoomExisting($room_no) {
    	$validRoomTemp = DB::connection('hms')
    		->table('tblroom')
    		->select(DB::raw("COUNT(*) AS 'room_count'"))
    		->where('RoomNo','=',$room_no)
    		->first()->room_count;
		return $validRoomTemp == 1;
    }

    public function changeRoomStatus(Request $request) {
    	$this->rule = array(
			'room_no' => 'required|integer',
			'new_room_status' => 'required|integer',
			'current_room_status' => 'required|integer'
		);
    	$this->validate($request->all(), $this->rule);
    	if (!$this->data['error']) {
    		if ($this->isRoomExisting($request->room_no)) {
                $connection = DB::connection('hms');
                        $connection->transaction(function () use($connection, $request) {
                            $connection->table('hms.tblroom')
                                ->where('RoomNo' ,'=', $request->room_no)
                                ->update(array('CRoom_Stat' => $request->new_room_status)
                            );
                        });
    			if ($this->isAllowedToChange($request->role_id, $request->current_room_status,  $request->new_room_status)) {
    				try {
			    		$connection = DB::connection('hms');
						$connection->transaction(function () use($connection, $request) {
							$connection->table('hms.tblroom')
								->where('RoomNo' ,'=', $request->room_no)
								->update(array('CRoom_Stat' => $request->new_room_status)
							);
						});
						$this->data['response'] = array('message'=>'Room status changed');
			    	} catch (Exception $e) {
						$this->data['error'] = array($e->getMessage());
						$this->statusCode = 403;
			    	}
    			} else {
    				$this->data['error'] = array("Room status for cannot be changed to this status");
					$this->statusCode = 403;
    			}

    		} else {
    			$this->data['error'] = array("Room not existing or not allowed for inspection");
				$this->statusCode = 403;
    		}
    	}
    	return response()->json($this->data, $this->statusCode);
    }

    public function cancel(Request $r){
        //TODO cancel
        // dd($r->all());
        $this->rule = array(
            'rmsId' => 'required',
        );
        $this->validate(
            $r->all(),
            $this->rule
        );
        if($this->data['error']['status'] == false){
            $cancelInspection = logs::cancelInspection($r->rmsId, $r->user_id, $r->mobile);

            $checkLogs = logs::where('id', $r->rmsId)->first();


            // $checkRooms = Rooms::where('id', $checkLogs->room_id)->first();

            // TODO dione hindot

            // $rooms = Rooms::changeHmsStatus();

            // if($checkRooms->from_room_status_id == 1){
            //     $updateRoomToClean = TblRoom::where('RoomNo', '=', $checkRooms->room_no)
            //     ->update([
            //         'Stat' => 'CLEAN',
            //         'CRoom_Stat' => 1
            //     ]);
            // }

            $this->data['response'] = array(
                'cancelInspection' => $cancelInspection,
                'message' => 'Cancellation Succssful'
            );
        }
        return response()->json($this->data, $this->statusCode);
    }

    public function logout(Request $request) {
    	$this->rule = array(
			'username' => 'required'
		);
    	$this->validate($request->all(), $this->rule);
    	if (empty($this->data['error']['error_message'])) {

            //OLD FROM 1.23
    		// $logincheck = LoginMobile::where('username', '=', $request->username)
            //                 ->select('*')->first();
                          
            //NEW FROM LOCAL
            $logincheck = DB::connection('mysql')
            ->table('user')
            ->select(
                '*'
            )
            ->where('username', $request->username)
            ->first();

        	if ($logincheck) {
        		$user = DB::connection('mysql')
	        			->table('user')
	        			->where('username', $request->username)
	        			->update(['is_logged_in' => 0]);
    			$this->data['response'] = "User logout successfully";
                $locale = Locale::select('*')->first();
				// $this->emitUsersOnline($locale->local_id);
        	} else {
        		$this->data['error'] = array("No user found");
				$this->statusCode = 403;
        	}

    	}
        return response()->json($this->data, $this->statusCode);
    }

    private function isAllowedToChange($role_id, $room_status, $new_status) {
		$response = AccessLevel::where('role_id', '=', $role_id)
			->Where('room_status_id', '=', $room_status)
			->Where('allow_status_id', '=', $new_status)->first();
    	return $response;
    }

    public function endStatus(Request $request) {
    	$this->rule = array(
			'role_id' => 'required|integer',
			'current_room_status' => 'required|integer'
		);
    	$this->validate($request->all(), $this->rule);
    	if (!$this->data['error']) {

    		$this->data['response'] = AccessLevel::where('role_id', '=', $request->role_id)
			->Where('room_status_id', '=', $request->current_room_status)
			->join('roomstatus', 'allow_status_id', '=', 'roomstatus.id')
			->get();
    	}
        return response()->json($this->data, $this->statusCode);
    }

    /*
    * inspection/lastRemarksOfRoom
    * @params
    * roomNo(int)
    */

    public function lastRemarksOfRoom(Request $r){

        $query = Transaction::getLastRemarksOfRoom($r->roomNo);

        if (count($query) > 0)
        {
            foreach ($query as $key => $value)
            {
                if ($value['areaName'] === '' || $value['componentName'] === '' || $value['remarksName'] === '')
                {
                    unset($query[$key]);
                }
            }
        }

        return response()->json([
                'lastRemarksOfRoom' => $query
            ], 200);


    }

    /*
    *   inspection/checkIfRoomSTL
    *   @params
    *   roomNo(int)
    *
    */

    public function checkIfRoomSTL(Request $r){

        $query = Transaction::checkRoomIfSTL($r->roomNo);
        if($query){
            return response()->json([
                'checkRoomIfSTL' => $query
            ], 200);
        }else{
            return response()->json([
                'checkRoomIfSTL' => array()
            ], 403);
        }

    }

    public function findChecklist(Request $request) {
        $this->rule = array(
        );
        $this->validate($request->all(), $this->rule);
        $filtered['area'] = array();

        $wholeData = InspectionLink::select(
            'tbl_links.finding_type_id as finding_type',
            'tbl_links.id as links_id',
            'tbl_links.standard_id as standard_id',
            'tbl_areas.name as area_name',
            'tbl_components.name as component_name',
            'tbl_standards.name as standard_name',
            'tbl_remarks.name as remark_name',
            'tbl_deduction.points as points',
            'tbl_areas.id as area_id')
            ->leftJoin('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->leftJoin('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->leftJoin('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')
            ->leftJoin('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            // ->leftJoin('tbl_deduction', 'tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
            ->leftjoin('tbl_deduction', function ($join) {
                $join->on('tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
                ->on('tbl_links.finding_type_id', '=', 'tbl_deduction.tbl_findings_type_id');
            })
            ->whereIn('tbl_links.standard_id', explode(",", $request->standard_ids))
            ->orderBy('tbl_links.area_id', 'ASC')
            ->orderBy('tbl_components.name', 'ASC')
            ->get();

        // return $wholeData;
        // ->where('tbl_deduction.tbl_findings_type_id', $request->finding_type)

        foreach ($wholeData as $value) {

            $areaIndex = array_search($value->area_name, array_column($filtered['area'], 'name'));
            if ($areaIndex === false) {
                $filtered['area'][] = array('id' => $value->area_id, 'name' => $value->area_name, 'component' => array());
                $areaIndex = count($filtered['area'])-1;
            }
            $componentIndex = array_search($value->component_name,
                            array_column($filtered['area'][$areaIndex]['component'], 'name'));
            if ($componentIndex === false) {
                if($value->standard_name == 'House Keeping') {
                    $filtered['area'][$areaIndex]['component'][] = array(
                        'olink_id' => $value->links_id,
                        'name' => $value->component_name, 'housekeeping' => array(), 'maintenance' => array());
                } else if($value->standard_name == 'Repairs & Maintenance') {
                    $filtered['area'][$areaIndex]['component'][] = array(
                        'olink_id' => $value->links_id,
                        'name' => $value->component_name, 'housekeeping' => array() ,'maintenance' => array());
                } else {
                    $filtered['area'][$areaIndex]['component'][] = array(
                        'olink_id' => $value->links_id,
                        'name' => $value->component_name, 'housekeeping' => array() ,'maintenance' => array());
                }
                $componentIndex = count($filtered['area'][$areaIndex]['component'])-1;
            }

            if ($value->standard_id != 1 || $value->standard_id != 2) {
                
                if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'])) {
                    $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'] = array();
                }
                $remarkIndex = array_search($value->remark_name,
                            array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'], 'name'));
                if ($remarkIndex === false) {
                       $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'][] = array('name' => $value->remark_name,
                                 'links_id' => $value->links_id,
                                 'points' => $value->points,
                                 'finding_type' => $value->finding_type);
                }


            } else {

                if ($value->standard_id == 1) {
                    if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'])) {
                        $filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'] = array();
                    }

                    $remarkIndex = array_search($value->links_id,
                                array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'], 'links_id'));
                    if ($remarkIndex === false) {
                           $filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'][] = array('name' => $value->remark_name,
                                     'links_id' => $value->links_id,
                                     'points' => $value->points,
                                     'finding_type' => $value->finding_type);
                    }
                }

                if ($value->standard_id == 2) {
                    if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'])) {
                        $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'] = array();
                    }
                    $remarkIndex = array_search($value->remark_name,
                                array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'], 'name'));
                    if ($remarkIndex === false) {
                           $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'][] = array('name' => $value->remark_name,
                                     'links_id' => $value->links_id,
                                     'points' => $value->points,
                                     'finding_type' => $value->finding_type);
                    }
                }
            }
        }
        $this->data['response'] = $filtered;
        return response()->json($this->data, $this->statusCode);

    }

    public function checklist(Request $request) {
        $this->rule = array(
        );
        $this->validate($request->all(), $this->rule);

        $filtered['area'] = array();

        $wholeData = InspectionLink::select(
            'tbl_links.finding_type_id as finding_type',
            'tbl_links.id as links_id',
            'tbl_links.standard_id as standard_id',
            'tbl_areas.name as area_name',
            'tbl_components.name as component_name',
            'tbl_standards.name as standard_name',
            'tbl_remarks.name as remark_name',
            'tbl_deduction.points as points',
            'tbl_areas.id as area_id')
            ->leftJoin('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->leftJoin('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->leftJoin('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')
            ->leftJoin('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            // ->leftJoin('tbl_deduction', 'tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
            ->leftjoin('tbl_deduction', function ($join) {
                $join->on('tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
                ->on('tbl_links.finding_type_id', '=', 'tbl_deduction.tbl_findings_type_id');
            })
            ->orWhere('tbl_links.standard_id', '=', '1')
            ->orWhere('tbl_links.standard_id', '=', '2')
            ->orderBy('tbl_links.area_id', 'ASC')
            ->orderBy('tbl_components.name', 'ASC')
            ->get();

        // return $wholeData;
        // ->where('tbl_deduction.tbl_findings_type_id', $request->finding_type)
        foreach ($wholeData as $value) {

            $areaIndex = array_search($value->area_name, array_column($filtered['area'], 'name'));
            if ($areaIndex === false) {
                $filtered['area'][] = array('id' => $value->area_id,'name' => $value->area_name, 'component' => array());
                $areaIndex = count($filtered['area'])-1;
            }
            $componentIndex = array_search($value->component_name,
                            array_column($filtered['area'][$areaIndex]['component'], 'name'));
            if ($componentIndex === false) {
                if($value->standard_name == 'House Keeping') {
                    $filtered['area'][$areaIndex]['component'][] = array(
                        'olink_id' => $value->links_id,
                        'name' => $value->component_name, 'housekeeping' => array(), 'maintenance' => array());
                } else if($value->standard_name == 'Repairs & Maintenance') {
                    $filtered['area'][$areaIndex]['component'][] = array(
                        'olink_id' => $value->links_id,
                        'name' => $value->component_name, 'housekeeping' => array() ,'maintenance' => array());
                }
                $componentIndex = count($filtered['area'][$areaIndex]['component'])-1;
            }

            if ($value->standard_id == 1) {
                if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'])) {
                    $filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'] = array();
                }

                $remarkIndex = array_search($value->links_id,
                            array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'], 'links_id'));
                if ($remarkIndex === false) {
                       $filtered['area'][$areaIndex]['component'][$componentIndex]['housekeeping'][] = array('name' => $value->remark_name,
                                 'links_id' => $value->links_id,
                                 'points' => $value->points,
                                 'finding_type' => $value->finding_type);
                }
            }

            if ($value->standard_id == 2) {
                if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'])) {
                    $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'] = array();
                }
                $remarkIndex = array_search($value->remark_name,
                            array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'], 'name'));
                if ($remarkIndex === false) {
                       $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'][] = array('name' => $value->remark_name,
                                 'links_id' => $value->links_id,
                                 'points' => $value->points,
                                 'finding_type' => $value->finding_type);
                }
            }


        }
        $this->data['response'] = $filtered;
        return response()->json($this->data, $this->statusCode);
    }

    public function startInspectFromNfc(Request $request) {
        $this->rule = array();
        $this->validate($request->all(), $this->rule);

        $this->rule = array(
            'room_number' => 'required',
            'role_id' => 'required'
        );

        $this->validate($request->all(), $this->rule);

        //add check if room number is existing
        if (empty($this->data['error']['error_message'])) {
            $isAllowed = false;
            $roomDetails = DB::connection('mysql')
            ->table('tblroom')
            ->select(
              'tblroom.id as id',
              'tblroom.room_no as room_no',
              'tblroom.from_room_status_id as previous_room_status_id',
              'tblroom.room_status_id as current_room_status_id',
              'tblroom.room_status_id',

              'tblroom.room_type_id as room_type_id',
              'tblroom.room_hashed as room_hashed',
              'tblroom.room_type_id as room_type_id',
              'tblroom.from_userinfo as userinfo',
              'tblroom.room_area_id as roomAreaId',
              'tblroom.updated_at as updated_at',
              'tblroomtype.room_type as room_type',
              'roomstatus.room_status as room_status',
              'roomstatus.color as color',
              'roomstatus.is_blink as is_blink',
              'roomstatus.is_timer as is_timer',
              'roomstatus.is_name as is_name',
              'roomstatus.is_buddy as is_buddy'
            )
            ->leftJoin('roomstatus', 'tblroom.room_status_id', '=', 'roomstatus.id')
            ->leftJoin('user', 'user.username', '=', 'tblroom.from_userinfo')
            ->leftjoin('tblroomtype', 'tblroom.room_type_id', '=', 'tblroomtype.id')
            ->where('room_no', '=', $request->room_number)
            ->orWhere('room_hashed', '=', $request->room_number)
            ->get();

            if (count($roomDetails) > 0) {
                $access = DB::connection('mysql')
                ->table('access_levels')
                ->where('role_id', '=', $request->role_id)
                ->where('room_status_id', '=', $roomDetails[0]->current_room_status_id)
                ->get();

                foreach($access as $value) {
                    if($value->allow_status_id == 13) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
            $this->data['response'] = array('is_allowed' => $isAllowed, 'room_details' => $roomDetails);
        }

        return response()->json($this->data, $this->statusCode);

    }



    public function areaChecklist(Request $request) {
        $this->rule = array(
            'standard_id' => 'required'
        );
        $this->validate($request->all(), $this->rule);

        $filtered['area'] = array();
        if (empty($this->data['error']['error_message'])) {
            $wholeData = InspectionLink::select(
            'tbl_deduction.tbl_findings_type_id as finding_type',
            'tbl_links.id as links_id',
            'tbl_links.standard_id as standard_id',
            'tbl_areas.name as area_name',
            'tbl_components.name as component_name',
            'tbl_standards.name as standard_name',
            'tbl_remarks.name as remark_name',
            'tbl_deduction.points as points')
            ->leftJoin('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->leftJoin('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->leftJoin('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')
            ->leftJoin('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->leftJoin('tbl_deduction', 'tbl_remarks.id', '=', 'tbl_deduction.remarks_id')
            ->orWhere('tbl_links.standard_id', '=', $request->standard_id)
            ->orderBy('tbl_links.area_id', 'ASC')
            ->get();
            // return $wholeData;
            foreach ($wholeData as $value) {
                $areaIndex = array_search($value->area_name, array_column($filtered['area'], 'name'));

                if ($areaIndex === false) {
                    $filtered['area'][] = array('name' => $value->area_name, 'component' => array());
                    $areaIndex = count($filtered['area'])-1;

                }

                $componentIndex = array_search($value->component_name,
                        array_column($filtered['area'][$areaIndex]['component'], 'name'));


                if ($componentIndex === false) {

                    $filtered['area'][$areaIndex]['component'][] = array(
                            'olink_id' => $value->links_id,
                            'name' => $value->component_name,
                            'maintenance' => array(),
                            'housekeeping' => array());

                    $componentIndex = count($filtered['area'][$areaIndex]['component'])-1;
                }

                if (empty($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'])) {
                        $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'] = array();
                }

                $remarkIndex = array_search($value->remark_name,
                                array_column($filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'], 'name'));
                if ($remarkIndex === false) {
                       $filtered['area'][$areaIndex]['component'][$componentIndex]['maintenance'][] = array('name' => $value->remark_name,
                                 'links_id' => $value->links_id,
                                 'points' => $value->points,
                                 'finding_type' => $value->finding_type);
                }
                $this->data['response'] = $filtered;
            }
        }








        return response()->json($this->data, $this->statusCode);
    }

    public function uploadImage($image, $user_id) {
        $date = new \DateTime();
        $date = $date->format('U');

        $url = "http://192.168.1.23/inspections/upload.php";
        $data['image'] = $image;
        $data['filename'] = $user_id . '-' . $date . '.png';
        $data_string = json_encode($data);

        $result = file_get_contents($url, null, stream_context_create(
            array(
                'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json'."\r\n".
                            'Content-Length: '.strlen($data_string)."\r\n",
                'content' => $data_string,
                ),
            )
        ));
        if (json_decode($result)->message == 'File saved') {
            return "http://192.168.1.23/inspections/images/". $data['filename'];
        } else {
            return "";
        }
        // echo json_decode($result)->message;


        // $file = base64_decode($request->image);
        // $folderName = 'public/images/';
        // $safeName = 'pekpek.png';
        // $destinationPath = public_path() . $folderName;

        // if (file_exists(public_path().'/images/'.$safeName)) {
        //     return "MERON NA";
        // } else {
        //     $success = file_put_contents(public_path().'/images/'.$safeName, $file);
        //     return "wala pa";
        // }
        // $this->data['response'] =  url('/').'/images/'.$safeName;
        // return response()->json($this->data, $this->statusCode);
    }

    public function testHasWork(Request $request) {
        dd(RmsRoom::hasOnGoingWork($request->user_id));
    }
}
