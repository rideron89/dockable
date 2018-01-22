<?php

namespace App;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

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
    */
    public static function addRoute($path, $controller, $method = 'GET')
    {
        $route = new Route(
            $path,                              // path
            array(                              // default values
                '_controller' => $controller,
            ),
            array(),                            // requirements
            array(),                            // options
            '',                                 // host
            array(),                            // schemes
            array($method)                        // methods
        );

        if (!self::$routes[$method])
        {
            self::$routes[$method] = new RouteCollection();
        }

        self::$routes[$method]->add($path, $route);
    }

    /**
    * Add a new route configuration for a specific PATH to the GET collection.
    *
    * @param string $path
    * @param string $controller
    */
    public static function get($path, $controller)
    {
        self::addRoute($path, $controller, 'GET');
    }

    /**
    * Add a new route configuration for a specific PATH to the POST collection.
    *
    * @param string $path
    * @param string $controller
    */
    public static function post($path, $controller)
    {
        self::addRoute($path, $controller, 'POST');
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
            $parameters = $matcher->match($request->path);
        }
        catch (ResourceNotFoundException $ex)
        {
            Response::send('page not found', 404);
        }

        if ($parameters['_controller'])
        {
            // expect _controller to look like 'ClassName@methodName'
            $split = explode('@', $parameters['_controller']);

            if (count($split) < 2)
            {
                Response::send('invalid controller setup for route "' . $request->path . '"', 400);
            }

            $controller = $split[0];
            $method     = $split[1];
            $className  = 'App\\Controllers\\' . $controller;

            $instance = new $className();
            $instance->$method($request);
        }
    }
}
