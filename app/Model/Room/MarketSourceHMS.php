<?php

namespace App\Model\Room;

use Illuminate\Database\Eloquent\Model;

class MarketSourceHMS extends Model
{
    //
    protected $table = 'marketsource';
    protected $connection = 'mysql';

    public static function getAllMarketSource(){
    	return $query = MarketSourceHMS::get([
    		'id',
    		'MarketSource'
    	]);
    }
}
