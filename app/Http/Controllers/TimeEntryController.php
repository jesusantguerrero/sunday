<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Board;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;

class TimeEntryController extends BaseController
{
    public function __construct()
    {
        $this->model = new TimeEntry();
        $this->searchable = ['name', 'description'];
        $this->validationRules = [];
    }

    public function index(Request $request) {
        return [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateLocal($request);
        $data = $request->post();
        $data['user_id'] = (int) $request->user()->id;
        $data['team_id'] = (int) $request->user()->current_team_id;

        TimeEntry::stopRunningEntries($data['user_id'], $data['team_id'], $data['start']);

        $resource = $this->model::create($data);
        return [
            "message" => $this->createdMessage,
            "data" => $resource
        ];
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
            ])->orderBy('start', 'DESC')->get()
        ]);
    }

}
