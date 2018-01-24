<?php

namespace App\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

use App\Request;
use App\Response;
use App\Middleware\Middleware;

class Router
{
    /**
    * Array of Symfony\Component\Routing\RouteCollection objects to hold all
    * possible routes, separated by HTTP method.
    *
    * @var Symfony\Component\Routing\RouteCollection
    */
    private static $routes = [];

    /**
    * Add a new route configuration for a specific PATH and METHOD to the list
    * of routes.
    *
    * @param string $path
    * @param string $controller
    * @param string $method [default: 'GET']
    *
    * @return App\Routing\EnhancedRoute
    */
    public static function addRoute($path, $controller, $method = 'GET')
    {
        $route = new EnhancedRoute(
            $path,                              // path
            array(                              // default values
                '_controller' => $controller,
            ),
            array(),                            // requirements
            array(),                            // options
            '',                                 // host
            array(),                            // schemes
            array($method)                      // methods
        );

        if (!self::$routes[$method])
        {
            self::$routes[$method] = new RouteCollection();
        }

        self::$routes[$method]->add($path, $route);

        return $route;
    }

    /**
    * Add a new route configuration for the GET method.
    *
    * @param string $path
    * @param string $controller
    *
    * @return App\Routing\EnhancedRoute
    */
    public static function get($path, $controller)
    {
        return self::addRoute($path, $controller, 'GET');
    }

    /**
    * Add a new route configuration for the POST method.
    *
    * @param string $path
    * @param string $controller
    *
    * @return App\Routing\EnhancedRoute
    */
    public static function post($path, $controller)
    {
        return self::addRoute($path, $controller, 'POST');
    }

    /**
    * Add a new route configuration for the PUT method.
    *
    * @param string $path
    * @param string $controller
    *
    * @return App\Routing\EnhancedRoute
    */
    public static function put($path, $controller)
    {
        return self::addRoute($path, $controller, 'PUT');
    }

    /**
    * Add a new route configuration for the DELETE method.
    *
    * @var string $path
    * @var string $controller
    *
    * @return App\Routing\EnhancedRoute
    */
    public static function delete($path, $controller)
    {
        return self::addRoute($path, $controller, 'DELETE');
    }

    /**
    * Attempt to route the currect request.
    *
    * @param App\Request $request
    */
    public static function route(Request $request)
    {
        $context = new RequestContext('/', $request->method);
        $matcher = new UrlMatcher(self::$routes[$request->method], $context);

        try
        {
            $match = $matcher->match($request->path);
        }
        catch (ResourceNotFoundException $ex)
        {
            Response::send('route not found', 404);
        }

        // get route parameters
        $paramMatches = [];
        preg_match('/{(\S+)}/', $match['_route'], $paramMatches);

        // first item is the full match, so remove it
        array_shift($paramMatches);

        foreach ($paramMatches as $param)
        {
            $request->params[$param] = $match[$param];
        }

        if ($match['_controller'])
        {
            // expect _controller to look like 'ClassName@methodName'
            $split = explode('@', $match['_controller']);

            if (count($split) < 2)
            {
                Response::send('invalid controller setup for route "' . $request->path . '"', 501);
            }

            $controller = $split[0];
            $method     = $split[1];
            $className  = 'App\\Controllers\\' . $controller;

            // run the middleware if any is set
            if ($match['middleware'])
            {
                foreach ($match['middleware'] as $middleware)
                {
                    $middlewareClassname = 'App\\Middleware\\' . $middleware;

                    $middlewareClassname::run($request);
                }
            }

            $instance = new $className();
            $instance->$method($request);
        }
        else
        {
            Response::send('no controller setup', 501);
        }
    }
}
