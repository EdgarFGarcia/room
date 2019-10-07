<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
 	//
    protected $table = 'tbl_standards';
    protected $connection = 'mysql';

    public function link(){
    	return $this->hasMany('App\InspectionLink', 'standard_id', 'id');
    }
}
