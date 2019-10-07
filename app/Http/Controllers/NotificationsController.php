<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationBlocklist;
use App\NotificationGroup;
use App\NotificationMember;
use DB;
class NotificationsController extends Controller
{
    public function addRoleBlocklist(Request $request) {

    	$this->rule = array('role_id'=> 'required');

        $this->validate($request->all(), $this->rule);
        if (empty($this->data['error']['error_message'])) {
        	$record = NotificationBlocklist::select('*')->where('role_id', '=', $request->role_id)->get();
	    	if (count($record) < 1) {
	    		$isInserted = NotificationBlocklist::insert(array('role_id' => $request->role_id));
	    		if ($isInserted) {
	    			$this->data['response'] = "Role successfully added to blocklist";
	    		}

	    	} else {
	    		$this->responseError("Role already exists");
	    	}
        }
    	return response()->json($this->data, $this->statusCode);
    }

    public function removeRoleFromBlocklist(Request $request) {
    	$this->rule = array('id'=> 'required');

        $this->validate($request->all(), $this->rule);

        if (empty($this->data['error']['error_message'])) {
        	$record = NotificationBlocklist::where('id', '=', $request->id);
        	if (count($record->get()) > 0) {
        		if ($record->delete()) {
        			$this->data['response'] = "Role successfully removed from blocklist";
        		}
        	} else {
        		$this->responseError("Record not existing");
        	}
        }
        return response()->json($this->data, $this->statusCode);
    }

    public function addGroup(Request $request) {
    	$this->rule = array('name'=> 'required');

        $this->validate($request->all(), $this->rule);

        if (empty($this->data['error']['error_message'])) {

        	$record = NotificationGroup::select('*')->where('name', '=', $request->name)->get();

	    	if (count($record) < 1) {

	    		$isInserted = NotificationGroup::insert(array('name' => $request->name));
	    		if ($isInserted) {
	    			$this->data['response'] = "Group successfully added";
	    		}

	    	} else {
	    		$this->responseError("Group already exists");
	    	}
        }
        return response()->json($this->data, $this->statusCode);
    }

    public function deleteGroup(Request $request) {
    	$this->rule = array('id'=> 'required');

        $this->validate($request->all(), $this->rule);

        if (empty($this->data['error']['error_message'])) {
        	$groupRecord = NotificationGroup::where('id', '=', $request->id);
        	if (count($groupRecord->get()) > 0) {

    			NotificationMember::where('group_id', '=', $request->id)->delete();
    			NotificationGroup::where('id', '=', $request->id)->delete();

				$this->data['response'] = "Member and group successfully deleted";
        		
        	} else {
        		$this->responseError("Record not existing");
        	}
        }
        return response()->json($this->data, $this->statusCode);
    }


    public function addMember(Request $request) {
    	$this->rule = array(
    		'users'=> 'required');

        $this->validate($request->all(), $this->rule);

        $user = $request->users;

        if (empty($this->data['error']['error_message'])) {
        	$data = $this->fixMemberData($request->users);
        	
        	$isInserted = NotificationMember::insert($data);

        	if (count($data) > 0) {
        	 	$this->data['response'] = "Member successfully added to group";
        	} else {
				$this->responseError("Data is empty or already exist");
        	}
        }

        return response()->json($this->data, $this->statusCode);
    }

    function fixMemberData($users) {
    	$remarks = array();
        if (!is_array($users))
            $users = json_decode($users, true);
        if (count($users) > 0)
        {
            foreach ($users as $key => $value)
            {
            	$record = NotificationMember::select('*')
		    		->where('group_id', '=', $value['group_id'])
		    		->where('role_id', '=', $value['role_id'])->get();

            	$filtered_array = array_filter($remarks, function($val) use($value){
              		return ($val['group_id']== $value['group_id'] and $val['role_id']==$value['role_id']);
          		});

		    	if (count($filtered_array) < 1 && count($record) < 1) {
		    		array_push($remarks, $value);	
		    	}
                
            }
        }
        return $remarks;
    }

    public function deleteMembers(Request $request) {

    	$this->rule = array(
    		'users'=> 'required');

        $this->validate($request->all(), $this->rule);

        $user = $request->users;

        if (empty($this->data['error']['error_message'])) {
    		$isDeleted = DB::connection('mysql')
    		->table('notification_members')
			->where('group_id', $request->group_id)
			->whereIn('role_id', $this->fixUsersData($request->users))
    		->delete();

    		if ($isDeleted) {
    			$this->data['response'] = "Member successfully added to group";
    		} else {
    			$this->responseError("Delete fail");
    		}
        }


    	 

		return response()->json($this->data, $this->statusCode);
    }

    function fixUsersData($list) {
    	$users = array();
        if (!is_array($list))
            $temp = json_decode($list, true);
        if (count($temp) > 0)
        {
            foreach ($temp as $key => $value)
            {
            	array_push($users, $value['role_id']);
                
            }
        }
        return $users;
    }

}
