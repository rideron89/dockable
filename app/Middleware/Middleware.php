<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface Middleware
{
    /**
    * Performs the login behind the middleware.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    */
    public static function run(Request $request) : Request;
}
