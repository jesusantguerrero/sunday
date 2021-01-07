<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Libraries\GoogleService;
use App\Models\Automation;

class AutomationController extends BaseController
{

    public function __construct()
    {
        $this->model = new Automation();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }

    public function run(int $automationId)
    {
            $automation = Automation::find($automationId);
                $service = $automation->recipe->name;
                GoogleService::$service($automation);
    }
}
