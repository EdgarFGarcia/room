<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    //
    protected $table = 'tblroomtype';
    protected $connection = 'mysql';

    public function roomcategory(){
      return $this->hasMany('App\Model\Room\RoomByCategory', 'id', 'id');
    }
}
