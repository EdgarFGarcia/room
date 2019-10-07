<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginMobile extends Model
{
    protected $table = 'vc_employees';
    protected $connection = 'vcitd';
}
