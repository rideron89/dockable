<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;

use App\Routing\Router;

class Application
{
    public $request;

    public function __construct()
    {
        include_once __DIR__ . '/../routes/web.php';

        $this->request = Request::createFromGlobals();
    }

    /**
    * Start running the application by passing along the request.
    */
    public function start()
    {
        Router::route($this->request);
    }
}
