<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Models\Board;
use App\Models\Item;
use App\Models\Link;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use App\Models\Standup;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $commitDate = $request->query('commit-date') ?? now()->format('Y-m-d');

        return Inertia::render('Dashboard', [
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

            'todo' => ItemResource::collection(Item::getByCustomField(['status', 'todo'], $request->user())),
            'commitDate' => $commitDate,
            'committed' => ItemResource::collection(Item::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'commit_date' => $commitDate
            ])->with('stage')->get()),
            'scheduled' => ItemResource::collection(Item::getByCustomField(['date', $commitDate], $request->user())),
            'links' => Link::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
            ])->get(),
            'standup' => Standup::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'date' => now()->format('Y-m-d')
            ])->get()
        ]);
    }
    public function planner(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date') ?? now()->format('Y-m-d');

        return Inertia::render('Planner', [
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

            'scheduled' => ItemResource::collection(Item::getByCustomField(['date', $date], $request->user()))
        ]);
    }
    public function blank(Request $request)
    {
        return Inertia::render('Blank');
    }
}
