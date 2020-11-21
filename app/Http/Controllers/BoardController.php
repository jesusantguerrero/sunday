<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Http\Resources\Automation as AutomationResource;
use Illuminate\Http\Request;
use App\Models\Board;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;

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
                'automations' => AutomationResource::collection(Automation::where([
                    'team_id' => $user->current_team_id,
                    'user_id' => $user->id,
                    'board_id' => $id
                ])->get()),
                'board' => [
                    'id' => $board->id,
                    'name' => $board->name,
                    'fields' => $board->fields,
                    'labels' => $board->labels,
                    'stages' => $board->stages->map(function ($stage) use($request) {
                        return [
                            'id' => $stage->id,
                            'board_id' => $stage->board_id,
                            'name' => $stage->name,
                            'items' => $stage->items()->filter($request->only('search', 'done'))->get()
                        ];
                    })
                ]
            ]);
    }
}
