<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class TimeEntryController extends Controller
{
    public function index(Request $request) {
        return [];
    }

    public function list(Request $request) {
        $user = $request->user();

        return Inertia::render('TimeTracker', [
            'current' => TimeEntry::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'status' => 0
            ])->get(),
            'tracks' => TimeEntry::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'status' => 1
            ])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response, TimeEntry $entry)
    {
        $entryData = [
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ];
        $entryData = array_merge($entryData, $request->post());
        $resource = $entry->create($entryData);
        return $resource;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TimeEntry::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $stage = TimeEntry::find($id);
        $stage->update($request->post());
        return $stage;
    }

}
