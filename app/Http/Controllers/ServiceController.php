<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleService;
use App\Models\AutomationService;
use App\Models\Integration;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Integrations', [
            "services" => AutomationService::all(),
            "integrations" => Integration::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id
            ])->with(['automations'])->get()
        ]);
    }

    public function google(Request $request)
    {
        return GoogleService::requestAccessToken((object) $request->post('credentials'), $request->user());
    }

    public function acceptOauth(Request $request)
    {
        if (auth()->hasUser()) {
            try {
                GoogleService::setTokens((object) $request->post('credentials'), $request->user());
                return redirect('/integrations');
            } catch (Exception $e) {
                return redirect('/integrations')->with('flash', [
                    'banner' => $e->getMessage(),
                ]);
            }
        } else {
        }
    }

    public function listCalendars(Integration $integration)
    {
        $calendars = GoogleService::listCalendars($integration->id);
        return response()->setContent($calendars->getItems());
    }
}
