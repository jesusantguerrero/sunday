<?php

use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;

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


Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    Route::group(["middleware" => ['auth', 'verified'] ], function() {
        // Dashboards
        Route::get('/accounts', function(Request $request) {
            return Jetstream::inertia()->render($request,"Accounting/Accounts");
        })->name('accounts');

        Route::get('/transactions', function(Request $request) {
            return Jetstream::inertia()->render($request, "Accounting/Transactions/Transactions", [
                'transactions' => Transaction::all()
            ]);
        })->name('transactions');
    });
});

// resource route
Route::middleware(['auth:sanctum', 'verified', 'web'])->group(function() {
    Route::apiResource('/api/transactions', Transaction::class);
    Route::apiResource('/api/transaction-lines', TransactionLine::class);
});
