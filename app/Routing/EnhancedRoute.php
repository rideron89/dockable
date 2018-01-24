<?php

namespace App\Routing;

use Symfony\Component\Routing\Route;

class EnhancedRoute extends Route
{
    /**
    * Set the middleware for the route.
    *
    * @param array/string $middleware
    */
    public function middleware($middleware)
    {
        if ('string' === gettype($middleware))
        {
            $this->setDefault('middleware', [$middleware]);
        }
        else
        {
            $this->setDefault('middleware', $middleware);
        }
    }
}
