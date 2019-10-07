<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspectionLink extends Model
{
    //
	protected $table = 'tbl_links';
	protected $connection = 'mysql';

	public function area(){
		return $this->hasOne('App\EagleEye', 'id', 'area_id');
	}

	public function component(){
		return $this->hasOne('App\InspectionComponent', 'id', 'component_id');
	}

	public function getTblStandard(){
		return $this->belongsTo('App\Standard', 'id', 'id');
	}

	public function remarks(){
		return $this->belongsTo('App\Remark', 'remarks_id', 'id');
	}

	public function getFindingType(){
		return $this->belongsTo('App\FindingType', 'id', 'id');
	}
}
