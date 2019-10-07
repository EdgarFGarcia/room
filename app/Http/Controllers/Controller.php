<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use App\Model\RMSLogs_Model as logs;
use App\Model\Mobile\WorkLogs;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data = array('response'=> array(),'error' => array(
        'status' => false,
        'error_message' => ''
    ));
    protected $statusCode = 200;
    protected $rule = array();
    protected $test = array();

    protected function responseError($messages){
        $this->data['error']['error_message'] = $messages;
        $this->data['error']['status'] = !empty($messages);
    }
    

    protected function validate($request, $rule) {
    	$validator = Validator::make($request, $rule);
    	if ($validator->fails()) {
    		// $this->data['error']['error_message'] = $validator->messages()->all()[0];s
    		$this->statusCode = 400;
            $this->responseError($validator->messages()->all()[0]);
    	} 
    }

    protected function insertLOGS($roomId, $user_id, $from){
      $query = logs::insertLogsStart($roomId, $user_id, $from);
    }

    protected function checkIfLogsExistWorkLogs($roomId, $userInfo, $toStatus, $roomNo){
        // dd($roomId, $userInfo, $toStatus, $roomNo);
        $checkWorkLogs = WorkLogs::checkRoomIfHasOnGoing($roomId);
        dd($checkWorkLogs);
        if(!is_null($checkWorkLogs)){
          $updateWorkLogs = WorkLogs::updateWorkLogs($toStatus, $userInfo, $roomNo);
          // $saveToWorkLogs = WorkLogs::insertWorkLogs($r->userInfo, $r->roomNo, $r->toStatus);
            return array(
                'saveWorkLogs' => $checkWorkLogs,
                'updateWorkLogs' => $updateWorkLogs
            );
        }else{
            return array(
                'saveWorkLogs' => array(),
                'updateWorkLogs' => array()
            );
        }
        
        // else{
        //   $saveToWorkLogs = WorkLogs::insertWorkLogs($r->userInfo, $r->roomNo, $r->toStatus);
        // }
    }
    
}
