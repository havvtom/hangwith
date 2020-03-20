<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Events\UserConnected;

class ConnectController extends Controller
{
    public function update(User $user){

    	UserConnected::dispatch();
    }
}
