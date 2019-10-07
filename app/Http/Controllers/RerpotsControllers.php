<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Logs\RMSLogs;
use App\Model\Room\RoomByCategory as Rooms;
use DB;
use Carbon\Carbon;
use App\Transaction;

class RerpotsControllers extends Controller
{
    //
    public function generateReports(Request $r){

    	$getRoomReportsByRoom = RMSLogs::getRoomReportsByRoom($r->roomNo);
    	$getRoomRemarks = RMSLogs::getRemarks($r->roomNo);

    	return response()->json([
    		'getRoomReportsByRoom' => $getRoomReportsByRoom,
    		'getRoomRemarks' => $getRoomRemarks
    	]);
    }

    public function lastRemarksOfRoomWeb(Request $r){
        $roomNo = Rooms::where('id', $r->roomId)->get();
        $query = Transaction::getLastRemarksOfRoom($roomNo[0]->room_no);
        
        return response()->json([
            'lastRemarksOfRoomWeb' => $query
        ], 200);


    }

    public function index(){
        return view('inspected');
    }

    // public static function getRemarks(Request $r){

        // $dateNow = Carbon::now()->format('Y-m-d');
        // $dateLastWeek = Carbon::now()->subDays(7)->format('Y-m-d');

        // $result = DB::connection('mysql')
        // ->select(DB::raw("
        //     SELECT links.roomno, links.userid, GROUP_CONCAT(links.area) AS 'areas', GROUP_CONCAT(links.component) AS 'components', GROUP_CONCAT(links.standard) AS 'standards', GROUP_CONCAT(links.rem) AS 'srem', links.name, links.dates
        //     FROM 
        //     (SELECT 
        //      a.room_no AS 'roomno',
        //      a.user_id AS 'userId', 
        //      b.id AS 'id',
        //      d.name AS 'area', 
        //      e.name AS 'component', 
        //      f.name AS 'standard', 
        //      g.name AS 'rem',
        //      a.other_remarks AS 'remarks', 
        //      CONCAT(b.lastname, ', ', b.firstname) as 'name', 
        //      a.other_remarks as 'notes', 
        //      a.created_at AS 'dates'
        //      FROM tbl_transactions a 
        //      LEFT JOIN user b ON a.user_id=b.username
        //      LEFT JOIN tbl_links c ON c.id=a.links_id
        //      LEFT JOIN tbl_areas d ON d.id=c.area_id 
        //      LEFT JOIN tbl_components e ON e.id=c.component_id 
        //      LEFT JOIN tbl_standards f ON f.id=c.standard_id 
        //      LEFT JOIN tbl_remarks g ON g.id=c.remarks_id 
        //      WHERE a.created_at BETWEEN '".$dateNow."' AND '".$dateLastWeek."') links
        //      GROUP BY links.dates
        // "));


        // return response()->json([
        //     'STL' => $result
        // ]);
    // }

    public static function getRemarksByDate(Request $r){

    	$result = DB::connection('mysql')
    	->select(DB::raw("
    		SELECT 
            
            -- links.roomno, COUNT(links.roomno) AS roomCount, links.userid, GROUP_CONCAT(links.area) AS 'areas', GROUP_CONCAT(links.component) AS 'components', GROUP_CONCAT(links.standard) AS 'standards', GROUP_CONCAT(links.rem) AS 'srem', links.name, links.dates

            links.roomno, links.userid, GROUP_CONCAT(links.area) AS 'areas', GROUP_CONCAT(links.component) AS 'components', GROUP_CONCAT(links.standard) AS 'standards', GROUP_CONCAT(links.rem) AS 'srem', links.name, links.dates

			FROM 
			(SELECT 
			 a.room_no AS 'roomno',
			 a.user_id AS 'userId', 
			 b.id AS 'id',
			 d.name AS 'area', 
			 e.name AS 'component', 
			 f.name AS 'standard', 
			 g.name AS 'rem', 
			 a.other_remarks AS 'remarks', 
			 CONCAT(b.lastname, ', ', b.firstname) as 'name', 
			 a.other_remarks as 'notes', 
			 a.created_at AS 'dates'
			 FROM tbl_transactions a 
			 LEFT JOIN user b ON a.user_id=b.username
			 LEFT JOIN tbl_links c ON c.id=a.links_id
			 LEFT JOIN tbl_areas d ON d.id=c.area_id 
			 LEFT JOIN tbl_components e ON e.id=c.component_id 
			 LEFT JOIN tbl_standards f ON f.id=c.standard_id 
			 LEFT JOIN tbl_remarks g ON g.id=c.remarks_id 
			 WHERE a.created_at BETWEEN 
                    '".$r->startDateHistory."' 
                    AND 
                    '".$r->endDateHistory."' 
                    AND 
                    a.user_id='".$r->userid."') links
			 GROUP BY links.dates
    	"));


    	return response()->json([
    		'byDate' => $result
    	]);
    }

    public function getInspectionCout(Request $r){
        // return $r->all();
        $getDmAndHm = DB::connection('mysql')
        ->table('user')
        ->where('role_id', 22)
        ->orWhere('role_id', 2)
        ->get();

        // return $workLogs = DB::connection('mysql')
        // ->table('work_logs')
        // ->where('work_id', 13)
        // ->where('status', 1)
        // ->where('user_id', 10139)
        // ->whereBetween('created_at', [$r->startDate, $r->endDate])
        // ->get()
        // ->count();

        $test = [];

        foreach($getDmAndHm as $out){

            $index = count( isset($test['getInspected']) ? $test['getInspected'] : []);

            $test['getInspected'][$index] = array(
                'username'              => $out->username,
                'name'                  => $out->lastname . ', ' . $out->firstname,
                'room inspected'        => DB::connection('mysql')
                                            ->table('work_logs')
                                            ->where('user_id', $out->username)
                                            // ->whereBetween('work_logs.created_at', [$r->startDate, $r->endDate])
                                            ->groupby('created_at')
                                            ->get()->count()
            );
        }

        return $test;

    }

}