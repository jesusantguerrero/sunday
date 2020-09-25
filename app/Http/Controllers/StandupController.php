<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Standup;

class StandupController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response)
    {
        $standup = new Standup();
        $standup->user_id =  $request->user()->id;
        $standup->team_id = $request->user()->current_team_id;
        $standup->date = $request->date;
        $standup->save();
        return $response->send($standup);
    }
}
