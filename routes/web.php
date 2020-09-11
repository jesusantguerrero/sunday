<?php

use App\Actions\Sunday\CreateTask;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Models\Board;
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
                    'name' => $board->name,
                    'link' =>  URL::route('boards', $board),
                ];
            })
        ]);
    })->name('dashboard');

    Route::get('/boards/{id}', function ($id) {
        $board = Board::find($id);
        return Inertia\Inertia::render('Board', [
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
});
