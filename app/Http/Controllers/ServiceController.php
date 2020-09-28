<?php

namespace App\Http\Controllers;

use App\Libraries\GmailService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index() {

        return Inertia::render('Integrations');
    }

    public function google(Request $request)
    {
       return GmailService::setTokens($request, $request->user()->id);
    }

    public function getMessages(Request $request)
    {
       return GmailService::getMessages($request->user()->id);
    }
}
