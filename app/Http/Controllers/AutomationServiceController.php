<?php

namespace App\Http\Controllers;

use App\Models\AutomationService;


class AutomationServiceController extends Controller
{
    public function index() {
        return AutomationService::all();
    }
}
