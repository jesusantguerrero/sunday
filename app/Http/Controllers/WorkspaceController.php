<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends BaseController
{

    public function __construct(Workspace $workspace)
    {
        $this->model = $workspace;
    }
}
