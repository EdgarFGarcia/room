<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspectionComponent extends Model
{
    //
    protected $table = 'tbl_components';
    protected $connection = 'mysql';

    public function link(){
    	return $this->hasMany('App\InspectionLink', 'component_id', 'id');
    }

    public function toArea(){
    	return $this->hasMany('App\EagleEye');
    }

    public function getRemarks(){
    	return $this->belongsTo('App\Remark', 'id', 'id');
    }
}
