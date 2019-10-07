<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HistoricalInspection_Model as Historical;
use DataTables;
use Illuminate\Support\Collection;

class HistoricalInspectionController extends Controller
{
    
    function index(){

        return view("historicalinspection");

    }

    function LoadHistoricalInspection(Request $request){

        $historicalinspection = Historical::LoadHistoricalInspection();

        return json_encode([
            "data"=>$historicalinspection
        ]);

    }

    function Login(Request $request){

        $validateuserrms = Historical::CheckUsernameRMS($request);

        if($validateuserrms){

            $userinfo = Historical::Login($request);

            if($userinfo->password==$request->password){

                return json_encode([
                    "success"=>true,
                    "message"=>"Login Successful.",
                    "userinfo"=>$userinfo->username,
                    "roleid"=>$userinfo->role_id
                ]);

            }
            else{

                return json_encode([
                    "success"=>false,
                    "message"=>"Invalid username or password."
                ]);

            }

        }
        else{


            $validateuservcitd = Historical::ValidateVCITDUser($request);

            if($validateuservcitd){


                $userinfovcitd = Historical::GetVCITDUserInfo($request);

                Historical::InsertUserRMS($userinfovcitd);

                $userinfo = Historical::Login($request);

                if($userinfo->password==$request->password){
    
                    return json_encode([
                        "success"=>true,
                        "message"=>"Login Successful.",
                        "userinfo"=>$userinfo->username,
                        "roleid"=>$userinfo->role_id
                    ]);
    
                }
                else{
    
                    return json_encode([
                        "success"=>false,
                        "message"=>"Invalid username or password."
                    ]);
    
                }

                

            }
            else{

                return json_encode([
                    "success"=>false,
                    "message"=>"Invalid username or password."
                ]);

            }


        }

    }

    function ApproveInformation(Request $request){

        Historical::ApproveInformation($request);

        return json_encode([
            "success"=>true,
            "message"=>"Historical information has been update."
        ]);

    }

    function DisapproveInformation(Request $request){

        Historical::SaveHistoricalAuditData($request);
        Historical::DisapproveInformation($request);

        return json_encode([
            "success"=>true,
            "message"=>"Historical information has been update."
        ]);

    }

    function LoadHistoricalInformation(Request $request){

        $information = Historical::LoadHistoricalInformation($request);

        return json_encode([
            "area"=>$information[0]->area,
            "component"=>$information[0]->component,
            "standard"=>$information[0]->standard,
            "remarks"=>$information[0]->remarks,
            "service_user_id"=>$information[0]->service_user_id,
            "service_date"=>$information[0]->service_date,
            "mlremarks"=>$information[0]->mlremarks,
            "validated_user_id"=>$information[0]->validated_user_id,
            "validated_date"=>$information[0]->validated_date,
            "validattion_notes"=>$information[0]->validattion_notes
        ]);

    }

    function LoadHistoricalRemarks(Request $request){

        $info = explode(',', $request->id);

        $historicalremarks = Historical::LoadHistoricalRemarks($info[0], $info[1]);

        return json_encode([
            "data"=>$historicalremarks
        ]);

    }

    function SaveRepairInformation(Request $request){

        Historical::SaveRepairInformation($request);

        return json_encode([
            "success"=>true,
            "message"=>"Historical information has been save."
        ]);

    }

    /*
    *   route: historical/historicalInspectionSearch'
    *   params: roomId (int) -> get instance per room
    */

    public function historicalSearchViaRoomId(Request $r){
        $query = Historical::historicalSearch($r);

        if($query){
            return response()->json([
                'message' => 'Success',
                'data' => $query
            ], 200);
        }else{
            return response()->json([
                'message' => 'Error',
                'data' => $query
            ], 500);
        }
    }


    /*
    *   route: historical/historicalInspectionMobile
    *   params: (no params if you want to get all records),  batchNumber (int) ->
    *           roomId (int) -> specific room historical inspection,
    *           batchNumber (int) -> if you want to update the record (approve or disapprove)
    */
    public function historicalMobile(Request $r){

        // $query = Historical::LoadHistoricalInspection();

        $query = Historical::historicalDataMobile();
        if(!empty($r->roomId))
        {
            // return "test";
            $perRoom = Historical::loadHistoricalPerRoom($r);
            if($query){

                return response()->json(
                    [
                        'message' => 'Success',
                        'historicalInspection' => $perRoom
                    ]
                );
            }
        }

        if(!empty($r->batchNumber))
        {
            $batchNumber = Historical::LoadHistoricalInspectionInfo($r->batchNumber);

            return response()->json(
                [
                    'message' => 'Success',
                    'roomBatch' => $batchNumber
                ]
            );
        }

        return response()->json(
            [
                'message' => 'Success',
                'historicalInspection' => $query
            ]
        );
    }

    /*
    *   route: historical/historicalInspectionMobileMM
    *   params: id (int) -> historical inspection id
    *           remarks (text) -> if any
    *           userinfo (int) -> maintenance username
    */
    public function historicalInspectionMobileMM(Request $r){
        $query = Historical::SaveHistoricalInspectionMobile($r->id, $r->remarks, $r->userinfo);

        if($query)
        {

            return response()->json(
                [
                    'message' => 'Success',
                    'saving' => $query
                ]
            );

        }

        else

        {

            return response()->json(
                [
                    'message' => 'Failed',
                    'saving' => array() 
                ]
            );
        }

    }

    /*
    *   route: historical/historicalInspectionMobileApprove
    *   params: isApprove => either 0 (false) || 1 (true)
    *           validatorId => validator username ex: DM, HM, TL username
    *           id => id of the historical_inspection
    *           validationNotes (text) => notes if any
    */
    public function historicalMobileValidation(Request $r){

        if($r->isApprove == 1)
        {
            // is_approve is approved
            $query = Historical::approveInspectionHistory($r);

            if(!empty($query)){
                return response()->json([
                    'message' => 'Success',
                    'data' => $query
                ]);
            }else{
                return response()->json([
                    'message' => 'Failed',
                    'data' => array()
                ]);
            }

            
        }
        else
        {
            // is_approve == 0 not approved
            $query = Historical::updateInspectionHistory($r);

            return response()->json([
                'message' => 'Success',
                'data' => $query
            ]);

        }

    }


    /*
    *   route: historical/historicalInspectionPerRoomAndArea
    *   params: none
    */
    public function historicalMobilePerRoomAndArea(Request $r){
       // return $r->all();    
        return $query = Historical::historicalPerRoomAndArea($r);
    }

    /*
    *   route: historical/historicalInspectionPerRoom
    *   params: none
    */
    public function perRoom(){
        return $query = Historical::getPerInstance();
    }

    /*
    *   route: historical/historicalInspectionByDM
    *   params: 'validated_user_id' => $data->userId,
    *        'validated_date' => DB::raw("NOW()"),
    *        'is_approve' => $data->isApprove(1 or 0),
    *        'validattion_notes' => $data->notes
    *        $data->id (historical Inspection ID, historyIdToReturnUpdate)
    */
    public function validateByDM(Request $r){
        // return $r->all();
        $query = Historical::updateAsDoneByDM($r);

        if($query){
            return response()->json([
                'message' => 'Success',
                'data' => $query
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => $query
            ]);
        }
    }

    public function infoForMM(){

        $query = Historical::getAllMMInfo();

        if($query){
            return response()->json([
                'message' => 'Success',
                'data' => $query
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => $query
            ]);
        }

    }

}
