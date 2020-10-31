<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;
use Inertia\Inertia;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Auth/Login');
    }
}
