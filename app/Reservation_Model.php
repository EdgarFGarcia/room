<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Reservation_Model extends Model
{
    
    public static function LoadReservation($data){

        $result = DB::connection('vcreserve')
        ->table('room_reservation_details')
        ->select(
           'room_reservation_details.Reservation_ID',
           'room_reservation_details.Room_Number',
           'room_reservation_details.Last_Name',
           'room_reservation_details.First_Name',
           'room_reservation_details.Reserve_Date',
           'room_reservation_details.End_Date',
           'room_reservation_details.Time',
           'reservation_categories.color'
        )
        ->join('reservation_categories', 'reservation_categories.id', '=', 'room_reservation_details.Res_Cat')
        ->where('Room_Number', '=', $data->room_no)
        ->get();

        return $result;

    }

    public static function UpdateReservationInformation($data){

        DB::connection('vcreserve')
        ->table('room_reservation_details')
        ->where('Reservation_ID', '=', $data->id)
        ->update([
            "Reserve_Date"=>$data->from,
            "End_Date"=>$data->to
        ]);

    }

    public static function ValidateUser($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->get();

        if($result[0]->user_count=="1"){
            return true;
        }   
        else{
            return false;
        } 

    }

    public static function ResLogin($data){

        $result = DB::connection('mysql')
        ->table('user')
        ->select(
           'username',
           'firstname',
           'middlename',
           'lastname',
           'role_id'
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->first();

        return $result;

    }

    public static function LoadReservationRate($id){

        $result = DB::connection('mysql')
        ->table('roomrates')
        ->select(
            'id', 
            'RateDesc' 
        )
        ->whereRaw("RoomType=(SELECT tblroomtype.room_type FROM tblroom INNER JOIN tblroomtype ON tblroomtype.id=tblroom.room_type_id WHERE tblroom.id='".$id."')")
        ->get();

        return $result;

    }

    public static function AddReservation($data, $rate, $settings, $order_ref){

        DB::connection('vcreserve')
        ->table('room_reservation_details')
        ->insert([
            "Room_Number"=>$data->roomNo,
            "Entry_Date"=>DB::raw("NOW()"),
            "Reserve_Date"=>$data->checkinDateFrom,
            "End_Date"=>$data->checkinDateTo,
            "First_Name"=>$data->fnameReservation,
            "Last_Name"=>$data->lnameReservation,
            "Days"=>DB::raw("DATEDIFF('".$data->checkinDateTo."', '".$data->checkinDateFrom."')"),
            "Room_Price"=>$rate->Amount,
            "Room_Deposit"=>$data->amount,
            "Currency"=>$data->deposit,
            "Hours"=>$rate->MaxHr,
            "Rtype"=>$data->roomtypesReservation,
            "Time"=>$data->timeInReservation,
            "Reserved_By"=>$data->userInfo,
            "Locale_ID"=>$settings->local_id,
            "Email_Add"=>$data->emailReservation,
            "Contact_Number"=>$data->pnumberReservation,
            // "Reserved_Additional_Request"=>$data->,
            "Reserved_Notes"=>$data->notesForReservation,
            "Room_Type"=>$rate->RoomType,
            // "Room_Additional_Net_Price"=>$data->,
            "Order_Ref"=>$order_ref,
            "Res_Cat"=>$data->reserveCategories
        ]);

    }

    public static function GetReservationRate($data){

        $result = DB::connection('mysql')
        ->table('roomrates')
        ->select(
            'Amount',
            'MaxHr',
            'RoomType'
        )
        ->where('ID', '=', $data->reservationRate)
        ->first();

        return $result;

    }

    public static function GetLocalID(){

        $result = DB::connection('mysql')
        ->table('settings')
        ->select(
            'local_id'
        )
        ->first();

        return $result;

    }
    
    public static function ValidateUserVCITD($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
            DB::raw("COUNT(*) AS 'user_count'")
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->first();

        if($result->user_count!=0){
            return true;
        }
        else{
            return false;
        }

    }

    public static function GetUserInfo($data){

        $result = DB::connection('vcitd')
        ->table('vc_employees')
        ->select(
           'username',
           'fname',
           'lname',
           'mname',
           'role_id'
        )
        ->where('username', '=', $data->username)
        ->where('password', '=', $data->password)
        ->get();

        return $result;

    }

}
