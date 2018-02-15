<?php

namespace App;

use App\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function __construct()
    {
        $request = Request::createFromGlobals();

        $response = Router::route($request);
        $response->send();
    }
}
