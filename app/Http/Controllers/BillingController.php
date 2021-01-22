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
            ])->get()->map( function ($sub) {
                return [
                    "name" => $sub->name,
                    "id" => $sub->id,
                    "status" => $sub->status,
                    "quantity" => $sub->quantity,
                    "agreement_id" => $sub->agreement_id,
                    "agreements" => [],
                ];
            })
        ]);

    }
}
