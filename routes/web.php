<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StandupController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

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

// pages
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/', [BoardController::class, 'list']);
    Route::get('/dashboard', [BoardController::class, 'list'])->name('dashboard');
    Route::get('/boards/{id}', [BoardController::class, 'edit'])->name('boards');
    Route::get('/integrations', [ServiceController::class, 'index'])->name('integrations');
});

// resource route
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::apiResource('/items', ItemController::class);
    Route::apiResource('/stages', StageController::class);
    Route::apiResource('/standups', StandupController::class);
    Route::apiResource('/api/boards', BoardController::class);
    Route::post('/services/google', [ServiceController::class, 'google']);
    Route::get('/services/messages', [ServiceController::class, 'getMessages']);
});
