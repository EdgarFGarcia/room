<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class HistoricalInspection_Model extends Model
{
    
    public static function LoadHistoricalInspection(){

        // SELECT 
        // IF(a.room_id!=0,  CONCAT(tblroom.id, ',', 'Room'), CONCAT(tbl_standards.id, ',', 'Area'))AS 'id',
        // IF(a.room_id!=0,  tblroom.room_no, tbl_standards.name) AS 'room_no',
		// COUNT(*) AS 'data_count'
        // FROM historical_inspection a
        // LEFT JOIN tblroom ON tblroom.id=a.room_id
        // LEFT JOIN tbl_standards ON tbl_standards.id=a.standard_id
        // LEFT JOIN user ON user.username=a.user_id 
        // WHERE a.process_code='MT' AND (a.is_done='0' OR a.is_approve='0')
        // GROUP BY id

        $result = DB::connection('mysql')
        ->table('historical_inspection as a')
        ->select(
            DB::raw("IF(a.room_id!=0,  CONCAT(tblroom.id, ',', 'Room'), CONCAT(tbl_standards.id, ',', 'Area')) AS 'id'"),
            DB::raw("IF(a.room_id!=0,  tblroom.room_no, tbl_standards.name) AS 'room_no'"),
            DB::raw("COUNT(*) AS 'data_count'")
        )
        ->leftjoin('tblroom', 'tblroom.id', '=', 'a.room_id')
        ->leftjoin('tbl_standards', 'tbl_standards.id', '=', 'a.standard_id')
        ->leftjoin('user', 'user.username', '=', 'a.user_id')
        ->whereRaw("a.process_code='MT' AND (a.is_done='0' OR a.is_approve='0')")
        ->groupBy('id')
        ->get();

        return $result;

    }

    public static function CheckUsernameRMS($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->first();

        if($result->user_count!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function Login($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            '*'
        )
        ->where('username', '=', $data->username)
        ->first();

        return $result;

    }

    public static function ValidateVCITDUser($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->first();

        if($result->user_count!=0){

            return true;

        }
        else{

            return false;

        }

    }

    public static function GetVCITDUserInfo($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
            '*'
        )
        ->where('username', '=', $data->username)
        ->first();

        return $result;

    }

    public static function InsertUserRMS($data){

        DB::connection('mysql')
        ->table('user')
        ->insert([
            "username"=>$data->username,
            "email"=>$data->email,
            "password"=>$data->password,
            "role_id"=>$data->role_id,
            "is_logged_in"=>"0",
            "firstname"=>$data->fname,
            "middlename"=>$data->mname,
            "lastname"=>$data->lname,
            "created_at"=>DB::raw("NOW()")
        ]);

    }

    public static function ApproveInformation($data){

        $dataid = explode(',', $data->id);
        
        DB::connection('mysql')
        ->table('historical_inspection')
        ->whereIn('id', $dataid)
        ->update([
            "validated_user_id"=>$data->userinfo,
            "validated_date"=>DB::raw("NOW()"),
            "is_approve"=>"1",
            "updated_at"=>DB::raw("NOW()")
        ]);

    }

    public static function DisapproveInformation($data){

        $dataid = explode(',', $data->id);
        
        DB::connection('mysql')
        ->table('historical_inspection')
        ->whereIn('id', $dataid)
        ->update([
            "service_user_id"=>"",
            "service_date"=>"",
            "is_done"=>"0",
        ]);

    }

    public static function loadHistoricalPerRoom($data){

        $query = DB::connection('mysql')
        ->table('historical_inspection')
        ->where('room_id', $data->roomId)->get();

        return $query;

    }

    public static function approveInspectionHistory($data){

        $approve = DB::connection('mysql')
        ->table('historical_inspection')
        ->where('id', $data->id)
        ->update([
            'validated_user_id' => $data->validatorId,
            'validated_date' => DB::raw("NOW()"),
            'is_approve' => $data->isApprove,
            'validattion_notes' => $data->validationNotes
        ]);

        if($approve){
            $getRecordIfDone = DB::connection('mysql')
            ->table('historical_inspection')
            ->where('id', $data->id)
            ->orderBy('id', 'DESC')
            ->first();

            $checkIfExist = DB::connection('mysql')
            ->table('historical_inspection_history')
            ->where('transaction_id', $getRecordIfDone->transaction_id)
            ->first();

            if(!$checkIfExist){
                $saveToFinishInspection = DB::connection('mysql')
                ->table('historical_inspection_history')
                ->insert([
                    'user_id' => $getRecordIfDone->user_id,
                    'room_id' => $getRecordIfDone->room_id,
                    'standard_id' => $getRecordIfDone->standard_id,
                    'batch_number' => $getRecordIfDone->batch_number,
                    'transaction_id' => $getRecordIfDone->transaction_id,
                    'remarks' => $getRecordIfDone->remarks,
                    'service_user_id' => $getRecordIfDone->service_user_id,
                    'service_date' => $getRecordIfDone->service_date,
                    'validated_user_id' => $getRecordIfDone->validated_user_id,
                    'validated_date' => $getRecordIfDone->validated_date,
                    'is_done' => $getRecordIfDone->is_done,
                    'is_approve' => $getRecordIfDone->is_approve,
                    'validattion_notes' => $getRecordIfDone->validattion_notes,
                    'created_at' => DB::raw("NOW()"),
                    'updated_at' => DB::raw("NOW()"),
                    'historical_inspection_id' => $getRecordIfDone->id
                ]);

                return $saveToFinishInspection;
            }
            
        }

    }

    public static function updateAsDoneByDM($data){
        // return $data;

        $update = DB::connection('mysql')
        ->table('historical_inspection as a')
        ->where('a.id', $data->id)
        ->update([
            'validated_user_id' => $data->validated_user_id,
            'validated_date' => DB::raw("NOW()"),
            'is_approve' => $data->is_approve,
            'validattion_notes' => $data->validattion_notes
        ]);

        if($update){
            $saveToFinishInspection;
            $getRecordIfDone = DB::connection('mysql')
            ->table('historical_inspection')
            ->where('id', $data->id)
            ->orderBy('id', 'DESC')
            ->first();

            $checkIfExist = DB::connection('mysql')
            ->table('historical_inspection_history')
            ->where('transaction_id', $getRecordIfDone->transaction_id)
            ->first();

            // return $getRecordIfDone->is_approve;


            if(!$checkIfExist){
                $saveToFinishInspection = DB::connection('mysql')
                ->table('historical_inspection_history')
                ->insert([
                    'user_id' => $getRecordIfDone->user_id,
                    'room_id' => $getRecordIfDone->room_id,
                    // 'standard_id' => $getRecordIfDone->standard_id,
                    'standard_id' => is_null($getRecordIfDone->standard_id ? $getRecordIfDone->standard_id : 0),
                    'batch_number' => $getRecordIfDone->batch_number,
                    'transaction_id' => $getRecordIfDone->transaction_id,
                    'remarks' => $getRecordIfDone->remarks,
                    'service_user_id' => $getRecordIfDone->service_user_id,
                    'service_date' => $getRecordIfDone->service_date,
                    'validated_user_id' => $getRecordIfDone->validated_user_id,
                    'validated_date' => $getRecordIfDone->validated_date,
                    'is_done' => $getRecordIfDone->is_done,
                    'is_approve' => $getRecordIfDone->is_approve,
                    'validattion_notes' => $getRecordIfDone->validattion_notes,
                    'created_at' => DB::raw("NOW()"),
                    'updated_at' => DB::raw("NOW()"),
                    'historical_inspection_id' => $getRecordIfDone->id
                ]);

                
            }

            if($getRecordIfDone->is_approve == 0){
                $updateWhenDisapprove = DB::connection('mysql')
                ->table('historical_inspection')
                ->where('id', $data->id)
                ->update([
                    'validated_user_id' => NULL,
                    'validated_date' => NULL,
                    'is_approve' => 0,
                    'is_done' => 0,
                    'service_user_id' => NULL,
                    'service_date' => NULL,
                    'is_approve' => NULL,
                    'validattion_notes' => NULL,
                    'created_at' => DB::raw("NOW()")
                ]);
            }

            return 1;
        }
    }

    public static function updateInspectionHistory($data){
        $update = DB::connection('mysql')
        ->table('historical_inspection as a')
        // ->join('tbl_transactions as b', 'a.transaction_id', '=', 'b.id')
        // ->leftjoin('tbl_links as c', 'b.link_id', '=', 'c.id')
        // ->join('tbl_remarks as d', 'c.remarks_id', '=', 'd.id')

        ->where('a.id', $data->id)
        ->update([
            'validated_user_id' => $data->validatorId,
            'validated_date' => DB::raw("NOW()"),
            'is_approve' => $data->isApprove,
            'validattion_notes' => $data->validationNotes
        ]);

        // return $update;

    }

    public static function historicalDataMobile() {
        $result = DB::connection('mysql')
        ->table('historical_inspection as a')
        ->select(
            DB::raw("IFNULL(tblroom.room_no, 
            (SELECT tbl_standards.name FROM tbl_transactions LEFT JOIN tbl_standards ON tbl_standards.id=tbl_transactions.room_no WHERE tbl_transactions.id IN (GROUP_CONCAT(a.transaction_id)) GROUP BY room_no)
            ) AS 'room_no'"),
            DB::raw("CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'name'"),
            'a.created_at',
            DB::raw("(SELECT IF((SELECT COUNT(*) FROM historical_inspection b WHERE b.batch_number=a.batch_number)=COUNT(is_done), 'Finish', 'Not Finish') FROM historical_inspection c WHERE c.batch_number=a.batch_number AND is_done='1') AS 'checking'")
        )
        ->leftjoin('tblroom', 'tblroom.id', '=', 'a.room_id')
        ->join('user', 'user.username', '=', 'a.user_id')
        ->groupBy('a.room_id')
        ->get();

        return $result;
    }

    public static function historicalSearch($data){

        // if(isset($data->roleId))
        // {  
        //     if (!in_array($data->roleId, [1, 2, 3]))
        //         return array();
        // }
        

        return DB::connection('mysql')
        ->table('historical_inspection')

        ->select(
            DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
            'historical_inspection.batch_number as batchNumber',
            DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
            'tbl_links.id as linkIdToReturn',
            'tbl_areas.name as areaName',
            'tbl_components.name as componentName',
            'tbl_remarks.name as remarksName',
            'tbl_transactions.id as transactionId'
        )

        ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
        ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
        ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
        ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
        ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
        ->join('user', 'historical_inspection.user_id', '=', 'user.username')

        ->where('historical_inspection.room_id', $data->roomId)
        ->where('historical_inspection.process_code', '=', 'MT')
        ->where('is_done', 0)

        ->groupBy('tbl_links.id')

        ->get();

    }

    public static function historicalPerRoomAndArea($data){

        if(isset($data->roleId))
        {  
            if (!in_array($data->roleId, [1, 2, 3]))
                return response()->json([
                    'rooms' => array(),
                    'areas' => array()
                ]);
        }

        $getRooms = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',
                DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),
                'tblroom.room_no as roomNo'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tblroom', 'historical_inspection.room_id', '=', 'tblroom.id')

            ->where('historical_inspection.room_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 0)
            ->where('is_approve', 0)

            ->groupBy('historical_inspection.room_id')
            // ->groupBy('tbl_links.id')

            ->get();

        $getPerArea = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',
                DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),
                'tbl_standards.name as areaName'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')

            ->where('historical_inspection.standard_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 0)
            ->where('is_approve', 0)

            ->groupBy('historical_inspection.standard_id')
            // ->groupBy('tbl_links.id')

            // ->union($getRooms)

            ->get();

        return response()->json([
            'rooms' => $getRooms,
            'areas' => $getPerArea
        ]); 
    }

    public static function getAllMMInfo(){

        $getRooms = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',
                DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),
                'tblroom.room_no as roomNo'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tblroom', 'historical_inspection.room_id', '=', 'tblroom.id')

            ->where('historical_inspection.room_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 0)

            ->groupBy('historical_inspection.room_id')

            ->get();

        $getPerArea = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',
                DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),
                'tbl_standards.name as areaName'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')

            ->where('historical_inspection.standard_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 0)

            ->groupBy('historical_inspection.standard_id')

            ->get();

        return response()->json([
            'rooms' => $getRooms,
            'areas' => $getPerArea
        ]); 
        
    }

    public static function getPerInstance(){

        $getRooms = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',
                // DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                // DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                // DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),

                DB::raw("tbl_areas.name AS areaName"),
                DB::raw("tbl_components.name AS componentName"),
                DB::raw("tbl_remarks.name AS remarksName"),

                'tblroom.room_no as roomNo'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tblroom', 'historical_inspection.room_id', '=', 'tblroom.id')

            ->where('historical_inspection.room_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 1)
            ->where('is_approve', 0)

            // ->groupBy('historical_inspection.room_id')
            ->groupBy('tbl_links.id')
            // ->groupBy('tbl_links.id')

            ->get();

        $getPerArea = DB::connection('mysql')
            ->table('historical_inspection')

            ->select(
                DB::raw("GROUP_CONCAT(historical_inspection.id) AS 'historyIdToReturnUpdate'"),
                DB::raw("CONCAT(user.firstname, ', ', user.lastname) AS whoFiled"),
                'tbl_links.id as linkIdToReturn',

                // DB::raw("GROUP_CONCAT(DISTINCT tbl_areas.name) AS areaName"),
                // DB::raw("GROUP_CONCAT(DISTINCT tbl_components.name) AS componentName"),
                // DB::raw("GROUP_CONCAT(DISTINCT tbl_remarks.name) AS remarksName"),

                DB::raw("tbl_areas.name AS areaName"),
                DB::raw("tbl_components.name AS componentName"),
                DB::raw("tbl_remarks.name AS remarksName"),

                'tbl_standards.name as areaName'
            )

            ->join('tbl_transactions', 'historical_inspection.transaction_id', '=', 'tbl_transactions.id')
            ->leftjoin('tbl_links', 'tbl_transactions.links_id', '=', 'tbl_links.id')
            ->join('tbl_areas', 'tbl_links.area_id', '=', 'tbl_areas.id')
            ->join('tbl_components', 'tbl_links.component_id', '=', 'tbl_components.id')
            ->join('tbl_remarks', 'tbl_links.remarks_id', '=', 'tbl_remarks.id')
            ->join('user', 'historical_inspection.user_id', '=', 'user.username')
            ->join('tbl_standards', 'tbl_links.standard_id', '=', 'tbl_standards.id')

            ->where('historical_inspection.standard_id', '<>', 0)
            ->where('historical_inspection.process_code', '=', 'MT')
            ->where('is_done', 1)
            ->where('is_approve', 0)

            ->groupBy('tbl_links.id')
            // ->groupBy('tbl_links.id')

            // ->union($getRooms)

            ->get();

        return response()->json([
            'rooms' => $getRooms,
            'areas' => $getPerArea
        ]); 

    }

    public static function SaveHistoricalAuditData($data){

        DB::connection('mysql')
        ->select(DB::raw("
            INSERT INTO historical_inspection_history 
            (
                user_id,
                room_id,
                standard_id,
                batch_number,
                transaction_id,
                remarks,
                service_user_id,
                service_date,
                validated_user_id,
                validated_date,
                is_done,
                is_approve,
                created_at,
                updated_at,
                historical_inspection_id
            )  
            
            SELECT 
                user_id,
                room_id,
                standard_id,
                batch_number,
                transaction_id,
                remarks,
                service_user_id,
                service_date,
                ".$data->userinfo.",
                NOW(),
                1,
                0,
                created_at,
                NOW(),
                id
            FROM historical_inspection WHERE id IN (".$data->id.")
            
        "));

    }

    public static function SaveRepairInformation($data){

        $dataid = explode(',', $data->id);

        DB::connection('mysql')
        ->table('historical_inspection')
        ->whereIn('id', $dataid)
        ->update([
            "remarks"=>$data->remarks,
            "service_user_id"=>$data->userinfo,
            "service_date"=>DB::raw("NOW()"),
            "is_done"=>"1"
        ]);

    }

    public static function LoadHistoricalInformation($data){

        $result = DB::connection('mysql')
        ->select(DB::raw("
            SELECT
            GROUP_CONCAT(inspections.id) AS 'id',
            inspections.area,
            inspections.component,
            inspections.standard,
            inspections.remarks,
            inspections.status,
            inspections.service_user_id,
            inspections.service_date,
            inspections.mlremarks,
            inspections.validated_user_id,
            inspections.validated_date,
            inspections.validattion_notes
            FROM (
                SELECT
                historical_inspection.id,  
                tbl_areas.name AS 'area', 
                tbl_components.name AS 'component', 
                tbl_standards.name AS 'standard', 
                IF(tbl_transactions.other_remarks IS NULL OR tbl_transactions.other_remarks='', tbl_remarks.name, tbl_transactions.other_remarks) AS 'remarks',
                IF(historical_inspection.is_done = 0, 'For Repair', IF(historical_inspection.is_done = 1 AND historical_inspection.is_approve = 0, 'For Validation', '') ) AS 'status',
                CONCAT(a.lastname, ', ', a.firstname, ' ', a.middlename) AS 'service_user_id',
                DATE_FORMAT(service_date, '%Y-%m-%d') AS 'service_date',
                historical_inspection.remarks AS 'mlremarks',
                CONCAT(b.lastname, ', ', b.firstname, ' ', b.middlename) AS 'validated_user_id',
                DATE_FORMAT(validated_date, '%Y-%m-%d') AS 'validated_date',
                historical_inspection.validattion_notes
                FROM historical_inspection 
                LEFT JOIN tbl_transactions ON tbl_transactions.id = historical_inspection.transaction_id
                LEFT JOIN user a ON a.username = historical_inspection.service_user_id 
                LEFT JOIN user b ON b.username = historical_inspection.validated_user_id 
                LEFT JOIN tbl_links ON tbl_links.id = tbl_transactions.links_id 
                LEFT JOIN tbl_areas ON tbl_areas.id = tbl_links.area_id 
                LEFT JOIN tbl_components ON tbl_components.id = tbl_links.component_id 
                LEFT JOIN tbl_standards ON tbl_standards.id = tbl_links.standard_id 
                LEFT JOIN tbl_remarks ON tbl_remarks.id = tbl_links.remarks_id 
                WHERE historical_inspection.process_code = 'MT' AND (historical_inspection.is_done = 0 OR historical_inspection.is_approve = 0)
            ) inspections
            WHERE inspections.id = '". $data->id ."'
            GROUP BY inspections.area, inspections.component, inspections.standard, inspections.remarks
            ORDER BY inspections.area DESC 
        "));

        return $result;

    }

    public static function LoadHistoricalRemarks($id, $type){

        if($type=="Room"){

            $result = DB::connection('mysql')
            ->select(DB::raw("
                SELECT
                GROUP_CONCAT(inspections.id) AS 'id',
                inspections.area,
                inspections.component,
                inspections.standard,
                inspections.remarks,
                inspections.status,
                inspections.inspector,
                DATE_FORMAT(MAX(inspections.created_at), '%Y-%m-%d') AS 'created_at'
                FROM (
                    SELECT
                    historical_inspection.id,  
                    tbl_areas.name AS 'area', 
                    tbl_components.name AS 'component', 
                    tbl_standards.name AS 'standard', 
                    IF(tbl_transactions.other_remarks IS NULL OR tbl_transactions.other_remarks='', tbl_remarks.name, tbl_transactions.other_remarks) AS 'remarks',
                    IF(historical_inspection.is_done = 0, IF(tbl_findings_type.name LIKE '%Maintenance%', 'For Maintenance', 'For Housework'), IF(historical_inspection.is_done = 1 AND historical_inspection.is_approve = 0, 'For Validation', '') ) AS 'status',
                    CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'inspector',
                    historical_inspection.created_at
                    FROM historical_inspection 
                    LEFT JOIN tbl_transactions ON tbl_transactions.id = historical_inspection.transaction_id 
                    LEFT JOIN user ON user.username = historical_inspection.user_id 
                    LEFT JOIN tbl_links ON tbl_links.id = tbl_transactions.links_id 
                    LEFT JOIN tbl_areas ON tbl_areas.id = tbl_links.area_id 
                    LEFT JOIN tbl_components ON tbl_components.id = tbl_links.component_id 
                    LEFT JOIN tbl_standards ON tbl_standards.id = tbl_links.standard_id 
                    LEFT JOIN tbl_remarks ON tbl_remarks.id = tbl_links.remarks_id
                    LEFT JOIN tbl_findings_type ON tbl_findings_type.id=tbl_links.finding_type_id
                    WHERE historical_inspection.room_id = '".$id."' AND  historical_inspection.process_code = 'MT' AND (historical_inspection.is_done = 0 OR historical_inspection.is_approve = 0)
                ) inspections
                GROUP BY inspections.area, inspections.component, inspections.standard, inspections.remarks
                ORDER BY inspections.id ASC 
            "));

            return $result;

        }
        else{

            $result = DB::connection('mysql')
            ->select(DB::raw("
                SELECT
                GROUP_CONCAT(inspections.id) AS 'id',
                inspections.area,
                inspections.component,
                inspections.standard,
                inspections.remarks,
                inspections.status,
                inspections.inspector,
                DATE_FORMAT(MAX(inspections.created_at), '%Y-%m-%d') AS 'created_at'
                FROM (
                    SELECT
                    historical_inspection.id, 
                    tbl_areas.name AS 'area', 
                    tbl_components.name AS 'component', 
                    tbl_standards.name AS 'standard', 
                    IF(tbl_transactions.other_remarks IS NULL OR tbl_transactions.other_remarks='', tbl_remarks.name, tbl_transactions.other_remarks) AS 'remarks',
                    IF(historical_inspection.is_done = 0, IF(tbl_findings_type.name LIKE '%Maintenance%', 'For Maintenance', 'For Housework'), IF(historical_inspection.is_done = 1 AND historical_inspection.is_approve = 0, 'For Validation', '') ) AS 'status',
                    CONCAT(user.lastname, ', ', user.firstname, ' ', user.middlename) AS 'inspector',
                    historical_inspection.created_at  
                    FROM historical_inspection 
                    LEFT JOIN tbl_transactions ON tbl_transactions.id = historical_inspection.transaction_id 
                    LEFT JOIN user ON user.username = historical_inspection.user_id 
                    LEFT JOIN tbl_links ON tbl_links.id = tbl_transactions.links_id 
                    LEFT JOIN tbl_areas ON tbl_areas.id = tbl_links.area_id 
                    LEFT JOIN tbl_components ON tbl_components.id = tbl_links.component_id 
                    LEFT JOIN tbl_standards ON tbl_standards.id = tbl_links.standard_id 
                    LEFT JOIN tbl_remarks ON tbl_remarks.id = tbl_links.remarks_id
                    LEFT JOIN tbl_findings_type ON tbl_findings_type.id=tbl_links.finding_type_id 
                    WHERE historical_inspection.standard_id = '".$id."' AND historical_inspection.process_code = 'MT' AND (historical_inspection.is_done = 0 OR historical_inspection.is_approve = 0)
                ) inspections 
                GROUP BY inspections.area, inspections.component, inspections.standard, inspections.remarks
                ORDER BY inspections.id ASC
            "));
    
            return $result;

        }

    }

    //wag burahin
    public static function SaveHistoricalInspectionMobile($id, $remarks, $userinfo){
        $dataId = explode(',', $id);
        $query = DB::connection('mysql')
        ->table('historical_inspection')
        ->whereIn('id', $dataId)
        ->update([
            "remarks"=>$remarks,
            "service_user_id"=>$userinfo,
            "service_date"=>DB::raw("NOW()"),
            "is_done"=>"1"
        ]);

        return $query;

    }

}
