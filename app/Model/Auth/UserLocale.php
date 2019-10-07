<?php

namespace App\Model\Auth;

use Illuminate\Database\Eloquent\Model;
use DB;
use Hash;

class UserLocale extends Model
{
    //
    protected $table = 'user';
    protected $connection = 'mysql';

    public static function checkIfExist($username, $password){

        $query = UserLocale::where('username', $username)
        ->select(
            'user.username as id',
            DB::raw("CONCAT(user.firstname, ' ', user.lastname) as name"),
            'user.role_id as role_id',
            'roles.role as rolename',
            'user.email as email',
            'user.password as password',
            'user.firstname as firstname',
            'user.lastname as lastname',
            'user.middlename as middlename'
        )
        ->join('roles', 'user.role_id', '=', 'roles.id')
        ->first();

        if($query){

            if($query->password!=$password){

                return array(
                    'response'  => 'InvalidUsernamePassword',
                    'query'     => array()
                );

            }
            else{

                return array(
                    'response'  => 'Proceed',
                    'query'     => $query
                );

            }

        }else{

            return array(
                'response'  => "userDoesNotExist",
                'query'     => array()
            );
            
        }
    }

    public static function insertUser($data){
        return $insertUser = UserLocale::insert([
            'username'          => $data->username,
            'email'             => $data->email,
            'password'          => $data->password,
            'role_id'           => $data->role_id,
            'is_logged_in'      => 1,
            'firstname'         => $data->fname,
            'middlename'        => $data->mname,
            'lastname'          => $data->lname,
            'status'            => $data->status,
            'created_at'        => DB::raw("NOW()"),
            'updated_at'        => DB::raw("NOW()")
        ]);
    }

    public static function UpdateUserinfo($data){

        DB::connection('mysql')
        ->table('user')
        ->where('username', '=', $data->username)
        ->update([
            "password"=>$data->password,
            "role_id"=>$data->role_id,
            "lastname"=>$data->lname,
            "firstname"=>$data->fname,
            "middlename"=>$data->mname,
            "updated_at"=>DB::raw("NOW()")
        ]);

    }

    public static function userIdToken($userId){
        $query = DB::connection('mysql')
        ->table('tbl_device')
        ->where('user_id', $userId)
        ->first();

        if($query){
            DB::connection('mysql')
            ->table('tbl_device')
            ->insert([
                'user_id' => $userId,
                'created_at' => DB::raw("NOW()")
            ]);
        }
    }


    public static function deviceInsertInfoOrUpdate($deviceType, $deviceToken, $userId){

        $ifExist = DB::connection('mysql')
        ->table('tbl_device')
        ->select(
            DB::raw("COUNT(*) as 'device_count'")
        )
        ->where('device_token', '=', $deviceToken)
        ->first();

        if($ifExist->device_count != 0){

            // update
            DB::connection('mysql')
            ->table('tbl_device')
            ->where('device_token', '=', $deviceToken)
            ->update([
                'user_id' => $userId,
                'updated_at' => DB::raw("NOW()")
            ]);

        }
        else{

            // insert
            DB::connection('mysql')
            ->table('tbl_device')
            ->insert([
                'user_id' => $userId,
                'device_type' => $deviceType,
                'device_token' => $deviceToken,
                'created_at' => DB::raw("NOW()")
            ]);

        }

    }

    public static function setZero($userId, $deviceToken){
        $query = DB::connection('mysql')
        ->table('tbl_device')
        ->where('device_token', '=', $deviceToken)
        ->orWhere('user_id', $userId)
        ->update([
            'user_id' => 0,
            'updated_at' => DB::raw("NOW()")
        ]);
    }

    public static function checkConnection(){
        try{
            DB::connection('vcitd')->getPdo();
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}