<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class AccessLevel extends Model
{
	
    protected $table = 'access_levels';
    protected $connection = 'mysql';

}
