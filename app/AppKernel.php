<?php

namespace App;

use App\Routing\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppKernel
{
    public static function start()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');

        $request = Request::createFromGlobals();

        $response = Router::route($request);
        $response->send();
    }
}
