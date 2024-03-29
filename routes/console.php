<?php

use App\Libraries\GoogleService;
use App\Models\Checklist;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Insane\Treasurer\PaypalServiceV2;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('daily:service {service} {automationId}', function ($service, $automationId) {
    GoogleService::$service($automationId);
})->purpose('call services');
