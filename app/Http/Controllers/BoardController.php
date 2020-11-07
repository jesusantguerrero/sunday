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

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response)
    {
        $board = new Board();
        $board->user_id =  $request->user()->id;
        $board->team_id = $request->user()->current_team_id;
        $board->name = $request->name;
        $board->save();
        $board->createMainStage();
        return $response->send($board);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $board = Board::find($id);
        $board->update($request->post());
        return $board;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $board = Board::find($id);
        $board->deleteStages();
        $board->delete();
    }

    public function edit(Request $request, int $id)
    {
        $user = $request->user();
        $board = Board::find($id);
        if (!$board) {
            return redirect('dashboard');
        }

        return Inertia::render('Board', [
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
                'filters' => $request->all('search', 'done'),
                'board' => [
                    'id' => $board->id,
                    'name' => $board->name,
                    'stages' => $board->stages->map(function ($stage) use($request) {
                        return [
                            'id' => $stage->id,
                            'board_id' => $stage->board_id,
                            'name' => $stage->name,
                            'fields' => $stage->fields,
                            'labels' => $stage->labels,
                            'items' => $stage->items()->filter($request->only('search', 'done'))->get()
                        ];
                    })
                ]
            ]);
    }

    public function list(Request $request)
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
            'committed' => Item::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'commit_date' => $commitDate
            ])->with('stage')->get(),
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
}
