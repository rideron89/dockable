<?php

namespace App\Middleware;

use App\Request;

interface Middleware
{
    /**
    * Performs the login behind the middleware.
    *
    * @param App\Request $request
    */
    public static function run(Request $request);
}
