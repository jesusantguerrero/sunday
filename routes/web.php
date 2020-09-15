<?php

use App\Actions\Sunday\CreateTask;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Models\Board;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// pages
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Dashboard', [
            'boards' => Board::all()->map(function($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'link' =>  URL::route('boards', $board),
                ];
            }),
            'todo' => Item::getByCustomField('status', 'todo')->toArray()
        ]);
    })->name('dashboard');

    Route::get('/boards/{id}', function ($id) {
        $board = Board::find($id);
        return Inertia\Inertia::render('Board', [
            'boards' => Board::all()->map(function($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'link' =>  URL::route('boards', $board),
                ];
            }),
            'board' => [
                'name' => $board->name,
                'stages' => $board->stages
            ]
        ]);
    })->name('boards');
});

// resource route
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::apiResource('/items', ItemController::class);
    Route::apiResource('/api/boards', BoardController::class);
});
