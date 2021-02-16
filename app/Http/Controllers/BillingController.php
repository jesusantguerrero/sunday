<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Models\Board;
use App\Models\Item;
use App\Models\Link;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use App\Models\Standup;
use Carbon\Carbon;
use Insane\Treasurer\Models\Plan;
use Insane\Treasurer\Models\Subscription;
use Insane\Treasurer\PaypalServiceV2;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $name = $request->routeName ?? "Billing";

        return Inertia::render(ucfirst($name), [
            "plans" => Plan::all(),
            "subscriptions" => Subscription::where([
                "user_id" => $user->id
            ])->get(),
            "transactions" => function () use ($request) {
                return $request->user()->subscriptionTransactions();
            }
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function plans(PaypalServiceV2 $paypalService)
    {
        return Inertia::render('Plans', [
            "plans" => Plan::all(),
            "products" => $paypalService->getProducts()
        ]);
    }
}
