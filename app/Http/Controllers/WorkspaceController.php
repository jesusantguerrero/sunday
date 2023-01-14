<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class WorkspaceController extends BaseController
{

    public function __construct(Workspace $workspace)
    {
        $this->model = $workspace;
    }

    public function switchWorkspace(Request $request) {
        $workspaceId = $request->post('workspace_id');
        $workspace = Workspace::findOrFail($workspaceId);

        if (!$request->user()->switchWorkspace($workspace)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }
}
