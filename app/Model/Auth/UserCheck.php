<?php

namespace App\Model\Auth;

use Illuminate\Database\Eloquent\Model;

class UserCheck extends Model
{
    //
    protected $table = 'vc_employees';
    protected $connection = 'vcitd';

    /*
	* params
	* username(int), roleId(int)
	*
    */
    public static function updateUserIfActiveWithNoRole($username, $roleId){
    	return $updateUser = UserCheck::where('username', $username)
    	->update([
    		'role_id' => $roleId
    	]);
    }

    public static function getUserInfo($username){
        return $getUser = UserCheck::where('username', $username)
                    ->where('status', '!=', 'Inactive')
                    ->whereNotNull('role_id')
                    ->first();
    }


}
