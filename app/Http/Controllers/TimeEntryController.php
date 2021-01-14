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
