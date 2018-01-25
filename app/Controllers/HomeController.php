<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;

class HomeController
{
    public function index(Request $request)
    {
        Response::view('index.html');
    }
}
