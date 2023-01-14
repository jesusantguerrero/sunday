<?php

namespace App\Http\Controllers;

use App\Events\AutomationCompleted;
use App\Http\Controllers\Api\BaseController;
use App\Jobs\ProcessCalendar;
use App\Libraries\GoogleService;
use App\Models\Automation;
use Exception;

class AutomationController extends BaseController
{

    public function __construct()
    {
        $this->model = new Automation();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }

    public function run($automationId)
    {
        $automation = Automation::find($automationId);
        try {
            if ($automation) {
                $service = $automation->recipe->name;
                GoogleService::$service($automation->id, true);
                return ["done" => $automation];
            } else {
                $automations = Automation::where([
                    "automation_recipe_id" => 1
                ])->get();

                if (count($automations)) {
                    foreach ($automations as $automation) {
                        ProcessCalendar::dispatch($automation);
                    }
                }

                return $automations;
            }
        } catch (Exception $e) {
            if ($e->getMessage() == "Need authorize again") {
                return GoogleService::requestAccessToken($automation->toArray(), request()->user());
            }
        }
    }
}
