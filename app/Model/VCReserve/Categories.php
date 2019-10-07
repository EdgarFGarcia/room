<?php

namespace App\Model\VCReserve;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    //
    protected $table = 'reservation_categories';
    protected $connection = 'vcreserve';
}
