<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Restriction_Model extends Model
{
    
    public static function LoginRestrictionWeb($data){

        $result = DB::connection('mysql')
        ->table('restriction_levels_web')
        ->select(
            DB::raw("COUNT(*) AS 'access_count'")
        )
        ->where('role_id', '=', $data->role_id)
        ->get();

        if($result[0]->access_count != 0){

            return true;

        }
        else{

            return false;

        }

    }

    public static function LoginRestrictionMobile($data){

        $result = DB::connection('mysql')
        ->table('restriction_levels_mobile')
        ->select(
            DB::raw("COUNT(*) AS 'access_count'")
        )
        ->where('role_id', '=', $data->role_id)
        ->get();

        if($result[0]->access_count != 0){

            return true;

        }
        else{

            return false;

        }

    }

}
