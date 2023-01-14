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
        GoogleService::setTokens((object) $request->post('credentials'), $request->user());
        return redirect('/integrations');
    }

    public function listCalendars(Request $request, Response $response)
    {
       $calendars = GoogleService::listCalendars($request->user()->id);
       return $response->setContent($calendars->getItems());
    }
}
