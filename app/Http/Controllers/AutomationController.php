<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Automation;

class AutomationController extends BaseController
{

    public function __construct()
    {
        $this->model = new Automation();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }
}
