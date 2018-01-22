<?php

namespace App\Controllers;

use App\Request;
use App\Response;

class HomeController
{
    public function index(Request $request)
    {
        Response::view('index.html');
    }

    public function test(Request $request)
    {
        $out = 'Test path called via "' . $request->base . '"!';

        Response::send($out);
    }
}
