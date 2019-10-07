<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class RoomGroup extends Model
{

    protected $table = "tbl_grp_room_type";
    protected $connection = "mysql";
    protected $primaryKey = "id";

}
