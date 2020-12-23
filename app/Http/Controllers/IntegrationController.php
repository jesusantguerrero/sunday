<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Integration;
use Illuminate\Http\Request;

class IntegrationController extends BaseController
{
    public function __construct()
    {
        $this->model = new Integration();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }
}
