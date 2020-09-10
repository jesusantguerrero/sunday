<?php

use Illuminate\Support\Facades\Route;
use App\Models\Board;
use App\Models\Item;
use Illuminate\Http\Request;

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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard', [
        'boards' => Board::all()->map(function($board) {
            return [
                'name' => $board->name,
                'link' =>  URL::route('boards', $board),
            ];
        })
    ]);
})->name('dashboard');


Route::middleware(['auth:sanctum', 'verified'])->get('/boards/{id}', function ($id) {
    $board = Board::find($id);
    return Inertia\Inertia::render('Board', [
        'board' => [
            'name' => $board->name,
            'stages' => $board->stages
        ]
    ]);
})->name('boards');


Route::middleware(['auth:sanctum', 'verified'])->post('/items', function (Request $request) {
    $item = new Item();
    $item->assign($request->post());
    $item->save();
})->name('boards');
