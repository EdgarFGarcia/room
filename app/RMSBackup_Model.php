<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class RMSBackup_Model extends Model
{
    
    public static function ReplicateRMSData(){

        DB::transaction(function(){

            $access_level = DB::connection('mysql')
            ->table('rms_logs')
            ->select(
                'id'
            )
            ->where('isupload', '=', '0')
            ->get();

            $arrid = [];
            $arrinsert = [];
            $id = "";
        
            $query = "
                INSERT INTO access_levels
                (
                    id,
                    role_id,
                    room_status_id,
                    allow_status_id,
                    created_at,
                    updated_at,
                    deleted_at
                )

                VALUES 
            ";

            foreach($access_level as $val){

                $arrid[] = $val->id;
                $arrinsert[] = "('". $val->id ."')";

            }


            // dd($arrid);
            // dd(implode($arrinsert, ","));ßßß

            $query .= implode($arrinsert, ",");

            dd($query);
            // DB::connection('backup')
            // ->select(DB::raw("
            //     (


            //     ), 
            //     ()
            //     SELECT 
            //     id,
            //     role_id,
            //     room_status_id,
            //     allow_status_id,
            //     created_at,
            //     updated_at,
            //     deleted_at
            //     FROM rms.access_levels 
            //     WHERE isupload=0;
            // "));

        });

    }

}
