<?php

namespace App;

class Application
{
    public $request;

    public function __construct()
    {
        include_once __DIR__ . '/../routes/web.php';

        $this->request = new Request();
    }

    /**
    * Start running the application by passing along the request.
    */
    public function start()
    {
        Router::route($this->request);
    }
}
