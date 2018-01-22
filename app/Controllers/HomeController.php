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

    public function test(Request $request)
    {
        $out = 'Test path called via "' . $request->base . '"!';

        Response::send($out);
    }

    public function load(Request $request)
    {
        $client = new MongoClient('dockable', 'sources');

        $sources = $client->find()->all();

        Response::send($sources);
    }
}
