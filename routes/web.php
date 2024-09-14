<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StandupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\NeatlancerController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\AutomationRecipeController;
use App\Http\Controllers\AutomationServiceController;

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

// Automation Services
Route::get('/services/accept-oauth', [ServiceController::class, 'acceptOauth']);
Route::get('/neatlancer', [NeatlancerController::class, 'connect']);
Route::get('/oauth/accept', [NeatlancerController::class, 'accept']);

Route::middleware(['auth:sanctum', 'verified', 'inertia'])->group(function() {
    // Dashboards
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/planner', [DashboardController::class, 'planner'])->name('planner');
    Route::put('/current-workspace', [WorkspaceController::class, 'switchWorkspace']);

    // Apps
    Route::get('/notes', [DashboardController::class, 'notes'])->name('notes');
    Route::get('/okrs', [DashboardController::class, 'okrs'])->name('okrs');
    Route::get('/reports', [DashboardController::class, 'blank'])->name('reports');

    // footer
    Route::get('/help', [DashboardController::class, 'help'])->name('help');
    Route::get('/about', [DashboardController::class, 'about'])->name('about');
    Route::get('/settings', [DashboardController::class, 'blank'])->name('settings');

    // Boards
    Route::get('/boards/{id}', [BoardController::class, 'edit'])->name('boards');

    // Integration
    Route::get('/integrations', [ServiceController::class, 'index'])->name('integrations');
    Route::post('/services/google', [ServiceController::class, 'google']);

    // Tracker
    Route::get('/tracker', [TimeEntryController::class, 'list'])->name('tracker');

    // Billing
    Route::get('/user/billing', [BillingController::class, 'index'])->name('billing');
    Route::get('/user/billing/{routeName}', [BillingController::class, 'index'])->name('billing.details');
    Route::get('/admin/plans', [BillingController::class, 'plans'])->name('billing.plans');
});

// resource route
Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::apiResource('/api/boards', BoardController::class);
    Route::apiResource('/api/fields', FieldController::class);
    Route::apiResource('/api/stages', StageController::class);
    Route::apiResource('/items', ItemController::class);
    Route::get('/api/items/todos', [ItemController::class, 'getTodos']);
    Route::post('/api/items/bulk/delete', [ItemController::class, 'bulkDelete']);
    Route::apiResource('/standups', StandupController::class);

    // Automations
    Route::apiResource('/api/integrations', IntegrationController::class);
    Route::apiResource('/api/automations', AutomationController::class);
    Route::post('/api/automations/{id}/run', [AutomationController::class, 'run']);
    Route::apiResource('/api/automation-services', AutomationServiceController::class);
    Route::apiResource('/api/automation-recipes', AutomationRecipeController::class);

    Route::get('/services/messages', [ServiceController::class, 'getMessages']);
    Route::get('/api/calendars', [ServiceController::class, 'listCalendars']);

    // Links
    Route::apiResource('/links', LinkController::class);

    // Time entries
    Route::apiResource('/api/time-entries', TimeEntryController::class);
    Route::post('/api/time-entries/bulk/delete', [TimeEntryController::class, 'bulkDelete']);
    Route::apiResource('/api/settings', SettingController::class);

    // Workspaces
    Route::apiResource('/api/workspaces', WorkspaceController::class);
});
