<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EagleEye extends Model
{
    //
    protected $table = 'tbl_areas';
    protected $connection = 'mysql';

    public function link(){
    	return $this->hasMany('App\InspectionLink');
    }

    public function getComponent(){
    	return $this->belongsTo('App\InspectionComponent', 'id', 'id');
    }

    public function getRemarks(){
    	return $this->belongsTo('App\Remark', 'id', 'id');
    }
}
