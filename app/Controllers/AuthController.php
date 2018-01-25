<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;

class AuthController
{
    public function index(Request $request)
    {
        Response::send('pass through');
    }
}
