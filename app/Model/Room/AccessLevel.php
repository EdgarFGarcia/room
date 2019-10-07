<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AccessLevel extends Model
{
    //
    protected $table = 'access_levels';
    protected $connection = 'mysql';

    public static function checkaccesslevel($roleid){
    	// return "hello";

    	$arrayLang = array();

    	$query = AccessLevel::select
    			(
    				'access_levels.role_id as roleid',
    				'a.id as current_id', 
    				'a.room_status as current_status',
    				'b.id as allowed_id',
    				'b.room_status as allowed_status',
                    'b.has_checklist as has_checklist',
                    'b.checklist_ids as checklist_ids'
    			)
    			->where('role_id', $roleid)
    			->join('roomstatus as a', 'access_levels.room_status_id', '=', 'a.id')
    			->join('roomstatus as b', 'access_levels.allow_status_id', '=', 'b.id')
    			->get();


//test

    	// foreach($query as $out){
    	foreach($query as $out){
    		$status = array_search($out->current_id, array_column($arrayLang, 'current_id'));
    		if($status === false){
    			$arrayLang[] = array('current_id'=>$out->current_id,
    							'current_status'=>$out->current_status,
    							'next'=>array([
    								'status_id'=>$out->allowed_id,
    								'allowed_status'=>$out->allowed_status,
                                    'has_checklist'=>$out->has_checklist,
                                    'checklist_ids' => $out->checklist_ids == null ? "" : $out->checklist_ids
    							]));
    		}else{
    			$nextStatus = array_search($out->allowed_id, array_column($arrayLang[$status]['next'], 'status_id'));
    			if($nextStatus === false){
    				$arrayLang[$status]['next'][] = array(
    					'status_id'=>$out->allowed_id,
    					'allowed_status'=>$out->allowed_status,
                        'has_checklist'=>$out->has_checklist,
                        'checklist_ids' => $out->checklist_ids == null ? "" : $out->checklist_ids
    				);
    			}else{
    				$arrayLang[$status]['next'][$nextStatus] = array(
    					'status_id'=>$out->allowed_id,
    					'allowed_status'=>$out->allowed_status,
                        'has_checklist'=>$out->has_checklist,
                        'checklist_ids' => $out->checklist_ids == null ? "" : $out->checklist_ids
    				);
    			}
    		}
    		// $arrayLang[] = $out->current_id;
    		
    	}

    	return response()->json(['accesslevel'=>$arrayLang], 200);
    }
}
