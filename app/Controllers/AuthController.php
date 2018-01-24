<?php

namespace App\Controllers;

use App\Request;
use App\Response;

class AuthController
{
    public function index(Request $request)
    {
        Response::send('pass through');
    }
}
