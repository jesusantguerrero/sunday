<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Insane\Accounting\Models\Transaction;
use Insane\Accounting\Models\TransactionLine;
use Laravel\Jetstream\Jetstream;
use Ramsey\Uuid\Type\Integer;

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

        // Transactions
        Route::get('/transactions', function(Request $request) {
            return Jetstream::inertia()->render($request, "Accounting/Transactions/Transactions", [
                'transactions' => Transaction::all()
            ]);
        })->name('transactions');

        Route::get('/transactions/{id}', function(Request $request, int $id) {
            return Jetstream::inertia()->render($request, "Accounting/Transactions/Transactions", [
                'transaction' => Transaction::find($id)
            ]);
        })->name('transactions-edit');
    });
});

// resource route
Route::middleware(['auth:sanctum', 'verified', 'web'])->group(function() {
    Route::apiResource('/api/transactions', Transaction::class);
    Route::apiResource('/api/transaction-lines', TransactionLine::class);
});
