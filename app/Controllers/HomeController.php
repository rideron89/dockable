<?php

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Databases\MongoClient;

class HomeController
{
    public function index(Request $request)
    {
        Response::view('index.html');
    }
}
