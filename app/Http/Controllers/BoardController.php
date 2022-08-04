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
        $data = $request->post();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->current_team_id;
        $data['workspace_id'] = $request->user()->current_workspace_id;
        $board = Board::create($data);
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

        $boardData = [
            'id' => $board->id,
            'name' => $board->name,
            'fields' => $board->fields,
            'labels' => $board->labels,
            'stages' => $board->stages->map(function ($stage) use($request) {
                return [
                    'id' => $stage->id,
                    'board_id' => $stage->board_id,
                    'name' => $stage->name,
                    'items' => $stage->items()->filter($request->only('search', 'done', 'sort'))->get()
                ];
        })];

        return Inertia::render('Board', [
                'filters' => $request->all('search', 'done'),
                'automations' => AutomationResource::collection(Automation::where([
                    'team_id' => $user->current_team_id,
                    'user_id' => $user->id,
                    'board_id' => $id
                ])->get()),
                'board' => $boardData,
                'boards' => Board::where([
                    'team_id' => $user->current_team_id,
                    'user_id' => $user->id,
                    'board_type_id' => $board->board_type_id,
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
}
