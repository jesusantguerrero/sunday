<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\StandupController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TimeEntryController;
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
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/', [BoardController::class, 'list']);
    Route::get('/dashboard', [BoardController::class, 'list'])->name('dashboard');
    Route::get('/boards/{id}', [BoardController::class, 'edit'])->name('boards');
    Route::get('/integrations', [ServiceController::class, 'index'])->name('integrations');
    Route::get('/tracker', [TimeEntryController::class, 'list'])->name('tracker');
});

// resource route
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::apiResource('/items', ItemController::class);
    Route::apiResource('/stages', StageController::class);
    Route::apiResource('/standups', StandupController::class);
    Route::apiResource('/api/boards', BoardController::class);
    Route::apiResource('/links', LinkController::class);
    Route::apiResource('/time-entries', TimeEntryController::class);
    Route::post('/services/google', [ServiceController::class, 'google']);
    Route::get('/services/messages', [ServiceController::class, 'getMessages']);
});
