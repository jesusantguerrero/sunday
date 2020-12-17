<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Stage;
use Illuminate\Http\Response;

class StageController extends BaseController
{

    public function __construct()
    {
        $this->model = new Stage();
        $this->searchable = ['name'];
        $this->validationRules = [];
    }
}
