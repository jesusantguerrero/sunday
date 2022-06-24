<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoardResource;
use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Models\Board;
use App\Models\Item;
use App\Models\Link;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use App\Models\Standup;
use Carbon\Carbon;
use Insane\Treasurer\Models\Plan;
use Insane\Treasurer\Models\Subscription;

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
            'notebooks' =>  BoardResource::collection(Board::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'board_type_id' => 2
            ])->get()),
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

    public function billing(Request $request)
    {
        $user = $request->user();
        return Inertia::render('Billing', [
            "plans" => Plan::all(),
            "subscriptions" => Subscription::where([
                "user_id" => $user->id
            ])->get()->map( function ($sub) {
                return [
                    "name" => $sub->name,
                    "id" => $sub->id,
                    "status" => $sub->status,
                    "quantity" => $sub->quantity,
                    "agreement_id" => $sub->agreement_id,
                    "agreements" => $sub->agreements(),
                ];
            })
        ]);
    }

    public function payments(Request $request)
    {
        $user = $request->user();
        return Inertia::render('Payments', [
            "plans" => Plan::all(),
            "subscriptions" => Subscription::where([
                "user_id" => $user->id
            ])->get()->map( function ($sub) {
                return [
                    "name" => $sub->name,
                    "id" => $sub->id,
                    "status" => $sub->status,
                    "quantity" => $sub->quantity,
                    "agreement_id" => $sub->agreement_id,
                    "agreements" => $sub->agreements(),
                ];
            })
        ]);
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
