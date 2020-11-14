<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;

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
            ])->get(),
            'boards' => Board::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id
            ])->get()->map(function ($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'link' =>  URL::route('boards', $board),
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TimeEntry $entry)
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
        $entry = TimeEntry::find($id);
        $entry->update($request->post());
        return $entry;
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entry = TimeEntry::find($id);
        $entry->delete();
        return $entry;
    }

}
