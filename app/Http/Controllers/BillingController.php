<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Insane\Treasurer\Models\Plan;
use Insane\Treasurer\PaypalServiceV2;
use Insane\Treasurer\Models\Subscription;

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
                return $request->user()->currentTeam->subscriptionTransactions();
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
