<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FindingType extends Model
{
    protected $table = 'tbl_findings_type';
    protected $connection = 'mysql';

    public function link(){
    	return $this->hasMany('App\InspectionLink');
    }
}
