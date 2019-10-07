<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class RoomByCategory extends Model
{
    //
    protected $table = 'tblroom';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';

    public function roomArea(){
      //first parameter yung model kung san coconnect, 2nd parameter id within the table
      // (tblroom), 3rd parameter id na pupuntahan (tblroomarea)
      return $this->belongsTo('App\Model\Room\RoomArea', 'id', 'id');
    }

    public function roomType(){
      return $this->belongsTo('App\Model\Room\RoomType', 'id', 'id');
    }

    public function roomStatus(){
      return $this->belongsTo('App\Model\Room\RoomStatus', 'id', 'id');
    }

    // public static function checkIfOnNego($roomId){
    //   $checkIfNego = RoomByCategory::where('id', $roomId)->first();
    //   if($checkIfNego->room_status_id == 20){
        
    //   }
    // }

  public static function roomInfo($roomNo){
    $query = RoomByCategory::where('room_no', $roomNo)->first();
    if($query){
        return response()->json(['roomDetails'=>$query], 200);
    }else{
        return response()->json(['roomDetails'=>array()], 403);
    }
  }

  public static function getRoomById($roomId){
    return $query = RoomByCategory::where('id', $roomId)->first();
  }

  public static function isRoomClean($roomId){
    $query = RoomByCategory::where('id', $roomId)
                ->where('room_status_id', 1)
                ->first();
    if($query){
      return true;
    }else{
      return false;
    }
  }

  public static function checkRoomStatus($roomId){

    return $query = DB::table('tblroom')->where('id', $roomId)->first();

  }

  public static function roomStatusesForReportsMobile(){

     $query = DB::connection('mysql')
          ->table('roomstatus')
          ->select(
            'id',
            'room_status',
            DB::raw("IFNULL((
              SELECT COUNT(*) FROM tblroom where room_status_id = roomstatus.id
              GROUP BY room_status_id
            ), 0) AS 'count' "),
            DB::raw("IFNULL((
              SELECT ( COUNT(*) / ((SELECT COUNT(*) FROM tblroom) - (SELECT COUNT(*) FROM tblroom WHERE (room_status_id=35 OR room_status_id=5 OR room_status_id=37 OR room_status_id=38)))) FROM tblroom where room_status_id = roomstatus.id
              GROUP BY room_status_id
            ), 0) AS 'percentage' ")
          )
          ->get();

    return array(
      // "total_rooms"=> RoomByCategory::getAllRooms()[0]->roomStatus,
      "total_rooms" => RoomByCategory::getAllRooms()[0]->rooms,
      "graph_list" => RoomByCategory::dashboardData($query),
      "defectives" => array(

      ),
    );
    // status_count / (all rooms - defective) = percentage 
    $defectiveRooms = RoomByCategory::minusAllDefectiveRooms();
    $getAllRoom = RoomByCategory::getAllRooms();
  }

  public static function dashboardData($data)
  {
    $array = array();

    foreach ($data as $key => $value) 
    {
      if ($value->id < 4) 
        $array[] = $value;

      else if ($value->id == 5 || $value->id == 35 || $value->id == 37 || $value->id == 38)
      {
        if (empty($array[3]))
        {
          $array[] = array(
            "id"          => count($array) + 1,
            "room_status" => "Defective",
            "count"       => 0,
            "percentage"  => 0.0
          );
        }
        $array[3]["count"]      = $array[3]["count"] + $value->count;
        $array[3]["percentage"] = $array[3]["percentage"]+ $value->percentage;
      }
    } 

    return $array;
  }

  public static function getAllRooms(){
    return $query = RoomByCategory::
                select(
                  DB::RAW("COUNT('*') as rooms")
                )
                ->get();
  }

  public static function getAllCleanRoom(){
    return $query = RoomByCategory::where('room_status_id', '=', 1)->get();
  }

  public static function minusAllDefectiveRooms(){
    return $query = RoomByCategory::
                    select(
                      DB::RAW("COUNT('*') as roomDefective")
                    )
                    ->where('room_status_id', '=', 35)
                    ->orWhere('room_status_id', '=', 5)
                    ->orWhere('room_status_id', '=', 37)
                    ->orWhere('room_status_id', '=', 38)->get();
  }

  public static function totalRoomWithDefective(){
    $allRooms = RoomByCategory::getAllRooms();
    $allDefective = RoomByCategory::minusAllDefectiveRooms();

    $allroomsCount = $allRooms[0]->rooms;

    $minusAllDefectiveRooms = $allDefective[0]->roomDefective;

    return $allRooms = $allroomsCount - $minusAllDefectiveRooms;
  }

  public static function updateWorkLogsUserId($rmsId, $userId){
        $workLogs = DB::connection('mysql')
        ->table('work_logs')
        ->update([
            'rms_logs_id' => $rmsId,
            'emp_id' => $userId,
            'created_at' => DB::raw("NOW()") 
        ]);

    }
}

// status_count / (all rooms - defective) = percentage 