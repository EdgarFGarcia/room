<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Room\RoomArea;
use App\Model\Room\MarketSourceHMS;
use DB;
use Illuminate\Support\Collection;
use DataTables;

class TurnAwayController extends Controller
{
    //
    public function index(){

        return view('turnaway');
        
    }

    public function getAllLocale(Request $r){

    	$query = RoomArea::getAllAreas();

    	return response()->json([
    		'locales' => $query
        ]);
        
    }

    public function getAllGuest(Request $r){
    	$query = MarketSourceHMS::getAllMarketSource();

    	return response()->json([
    		'marketSource' => $query
    	]);
    }

    public function getAllReason(Request $r){

    	$query = DB::connection('mysql')
    	->table('tbl_turn_away_reasons')
    	->get(['id', 'reason']);

    	return response()->json([
    		'reasons' => $query
        ]);
        
    }

    public function getAllRecords(){

        $query = DB::connection('mysql')
        ->table('tbl_turn_away')
        ->select(
            'tbl_turn_away.created_at as created_at',
            'tblroomareas.room_area as room_area',
            'marketsource.MarketSource as MarketSource',
            'tbl_turn_away_reasons.reason as reason',
            'tbl_turn_away.platenumber as platenumber',
            'tbl_turn_away.user as user',
            'tbl_turn_away.notes as notes',
            DB::raw("CONCAT(user.firstname, ' ', user.lastname) as name")
        )
        ->join('tblroomareas', 'tbl_turn_away.locale', '=', 'tblroomareas.id')
        ->join('marketsource', 'tbl_turn_away.typeofguest', '=', 'marketsource.id')
        ->join('tbl_turn_away_reasons', 'tbl_turn_away.reasons', '=', 'tbl_turn_away_reasons.id')
        ->join('user', 'tbl_turn_away.user', '=', 'user.username')
        ->get();

        $data = array();
        foreach($query as $out){
            $obj = new \stdClass;
            $obj->locale = $out->room_area;
            $obj->typeofguest = $out->MarketSource;
            $obj->reason = $out->reason;
            $obj->note = $out->notes;
            $obj->platenumber = $out->platenumber;
            $obj->created_at = $out->created_at;
            $obj->user = $out->name;
            $data[] = $obj;
        }
        $info = new Collection($data);
        return DataTables::of($info)->make(true);

    }

    public function insertTurnAway(Request $r){

        $checkUser = DB::connection('mysql')
        ->table('user')
        ->where('username', $r->username)->first();

        if($checkUser!=null){

            if($r->password == $checkUser->password){

                $query = DB::connection('mysql')
                ->table('tbl_turn_away')
                ->insert([
                    'locale' => $r->locale,
                    'typeofguest' => $r->marketSource,
                    'platenumber' => $r->plateNumber,
                    'reasons' => $r->reasons,
                    'user' => $r->username,
                    'notes' => $r->notes
                ]);
    
                return json_encode([
                    "success"=>true,
                    'message' => 'Turn away information has been save.'
                ]);
    
            }else{
    
                return json_encode([
                    "success"=>false,
                    'message' => 'Wrong username or password.'
                ]);

            }

        }
        else {

            return json_encode([
                "success"=>false,
                "message"=>"User does not exist."
            ]);

        }
       
    }
}
