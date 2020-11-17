<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
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
    // Dashboards
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/planner', [DashboardController::class, 'planner'])->name('planner');

    // Apps
    Route::get('/notes', [DashboardController::class, 'blank'])->name('blank');
    Route::get('/reports', [DashboardController::class, 'blank'])->name('blank');

    // footer
    Route::get('/help', [DashboardController::class, 'blank'])->name('blank');
    Route::get('/about', [DashboardController::class, 'blank'])->name('blank');
    Route::get('/settings', [DashboardController::class, 'blank'])->name('blank');

    // Boards
    Route::get('/boards/{id}', [BoardController::class, 'edit'])->name('boards');

    // Integration
    Route::get('/integrations', [ServiceController::class, 'index'])->name('integrations');

    // Tracker
    Route::get('/tracker', [TimeEntryController::class, 'list'])->name('tracker');
});

// resource route
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::apiResource('/items', ItemController::class);
    Route::apiResource('/stages', StageController::class);
    Route::apiResource('/standups', StandupController::class);
    Route::apiResource('/api/boards', BoardController::class);
    Route::apiResource('/api/fields', FieldController::class);
    Route::apiResource('/links', LinkController::class);
    Route::apiResource('/time-entries', TimeEntryController::class);
    Route::post('/services/google', [ServiceController::class, 'google']);
    Route::get('/services/messages', [ServiceController::class, 'getMessages']);
});
