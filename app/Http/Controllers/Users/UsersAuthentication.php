<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class UsersAuthentication extends Controller
{
    //
    public function index(){

        // return $query = DB::connection('mysql')
        // ->table('tblroom')
        // ->get();

      return view('tagboard');
    }

    public function login(Request $request){
      
    }

    public function push(){
    	return view('push');
    }

    public function group(){
    	return view('group');
    }
}
