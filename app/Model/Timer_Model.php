<?php

namespace App\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Timer_Model extends Model
{

    public static function GetTime($id){

        $result = DB::connection('mysql')
        ->table('tblroom')
        ->select(
            DB::raw("TIMESTAMPDIFF(SECOND, updated_at, NOW()) AS 's'"),
            DB::raw("TIMESTAMPDIFF(HOUR, updated_at, NOW()) AS 'h'"),
            DB::raw("TIMESTAMPDIFF(DAY, updated_at, NOW()) AS 'd'")
        )
        ->where('id', '=', $id)
        ->get();

        return $result;

    }

}
