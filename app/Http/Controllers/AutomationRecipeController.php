<?php

namespace App\Http\Controllers;

use App\Models\AutomationRecipe;


class AutomationRecipeController extends Controller
{
    public function index() {
        return AutomationRecipe::all();
    }
}
