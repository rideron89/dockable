<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;

class HomeController
{
    public function index(Request $request)
    {
        if ($request->user && $request->password)
        {
            Response::view('index.html');
        }
        else
        {
            Response::view('login.html');
        }
    }
}
