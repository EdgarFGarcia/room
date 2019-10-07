<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RMSBackup_Model AS Backup;

class RMSBackupController extends Controller
{
    
    public function __construct(){

        ini_set('memory_limit','512M');
        ini_set('max_execution_time', 300);

    }

    function ReplicateRMSData(){

        Backup::ReplicateRMSData();

        return json_encode([
            "success"=>true
        ]);

    }

}
