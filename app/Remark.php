<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    //
    protected $table = 'tbl_remarks';
    protected $connection = 'mysql';

    public function link(){
    	return $this->hasMany('App\InspectionLink');
    }

    // public function toArea(){
    // 	return $this->hasMany('App\EagleEye');
    // }

    public function toComponent(){
    	return $this->hasMany('App\InspectionComponent');
    }
}
