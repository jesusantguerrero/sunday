<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Models\Board;
use App\Models\Item;
use App\Models\Link;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use App\Models\Standup;
use Carbon\Carbon;

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
            'todo' => ItemResource::collection(Item::getByCustomField(['status', 'todo'], $request->user())),
            'commitDate' => $commitDate,
            'committed' => ItemResource::collection(Item::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'commit_date' => $commitDate
            ])->with('stage')->get()),
            'links' => Link::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
            ])->get(),
            'agenda' => ItemResource::collection(Item::getByCustomField(['date', now()->format('Y-m-d')], $request->user())),
            'standup' => Standup::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'date' => now()->format('Y-m-d')
            ])->get()
        ]);
    }
    public function planner(Request $request)
    {
        $date = $request->query('date') ?? now()->format('Y-m-d');
        $date = new Carbon($date);
        $date->timezone = "America/Santo_Domingo";
        $formattedDate = $date->format('Y-m-d');

        return Inertia::render('Planner', [
            'scheduled' => ItemResource::collection(Item::getByCustomField(['date', $formattedDate], $request->user())),
            'date' => $date->toDateTimeString()
        ]);
    }

    public function notes(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date') ?? now()->format('Y-m-d');

        return Inertia::render('Notes', [
            'notebooks' =>  Board::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'board_type_id' => 2
            ])->get()->map(function ($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'stages' => $board->stages()->without('items')->get(),
                    'link' =>  URL::route('boards', $board),
                    'color' => $board->color,
                    'template'=> $board->boardTemplate,
                    'type' => $board->boardType
                ];
            }),
        ]);
    }

    public function okrs(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date') ?? now()->format('Y-m-d');

        return Inertia::render('Okrs', [
            'okrs' =>  Board::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'board_type_id' => 3
            ])->get()->map(function ($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'stages' => $board->stages()->without('items')->get(),
                    'link' =>  URL::route('boards', $board),
                    'color' => $board->color,
                    'template'=> $board->boardTemplate,
                    'type' => $board->boardType
                ];
            }),
        ]);
    }
    public function about(Request $request)
    {
        return Inertia::render('About');
    }
    public function help(Request $request)
    {
        return Inertia::render('Help');
    }

    public function blank(Request $request)
    {
        return Inertia::render('Blank');
    }
}
