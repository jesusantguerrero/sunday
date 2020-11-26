<?php

namespace App\Http\Controllers;

use App\Libraries\GoogleService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index() {

        return Inertia::render('Integrations');
    }

    public function google(Request $request)
    {
       return GoogleService::setTokens($request, $request->user()->id);
    }

    public function listCalendars(Request $request, Response $response)
    {
       $calendars = GoogleService::listCalendars($request->user()->id);
       return $response->setContent($calendars->getItems());
    }
}
