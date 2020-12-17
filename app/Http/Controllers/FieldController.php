<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Field;
use Illuminate\Http\Response;

class FieldController extends BaseController
{
    public function __construct()
    {
        $this->model = new Field();
    }
}
