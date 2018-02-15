<?php

namespace App;

use App\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public static function start()
    {
        $request = Request::createFromGlobals();

        $response = Router::route($request);
        $response->send();
    }
}
