<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleService;
use App\Models\AutomationService;
use App\Models\Integration;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index() {

        return Inertia::render('Integrations', [
            "services" => AutomationService::all(),
            "integrations" => Integration::all()
        ]);
    }

    public function google(Request $request)
    {
       return GoogleService::setTokens((object) $request->post('credentials'), $request->user()->id);
    }

    public function listCalendars(Request $request, Response $response)
    {
       $calendars = GoogleService::listCalendars($request->user()->id);
       return $response->setContent($calendars->getItems());
    }
}
