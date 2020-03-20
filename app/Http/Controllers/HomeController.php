<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function authenticate(Request $request){

        $socketId = $request->socket_id;
        $channelName = $request->channel_name;

        $pusher = new Pusher('6e5bc172c442bd95cee1', '64bf7effc8772b7e7dc6', '963151', [
            'cluster' => 'mt1',
            'encrypted' => true
        ]);

        $presence_data = ['name' => Auth()->user()->name];
        $key = $pusher->presence_auth($channelName, $socketId, Auth()->user()->id, $presence_data);

        return response($key);
    }
}
