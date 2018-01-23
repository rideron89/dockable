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
            array($method)                      // methods
        );

        if (!self::$routes[$method])
        {
            self::$routes[$method] = new RouteCollection();
        }

        self::$routes[$method]->add($path, $route);
    }

    /**
    * Add a new route configuration for the GET method.
    *
    * @param string $path
    * @param string $controller
    */
    public static function get($path, $controller)
    {
        self::addRoute($path, $controller, 'GET');
    }

    /**
    * Add a new route configuration for the POST method.
    *
    * @param string $path
    * @param string $controller
    */
    public static function post($path, $controller)
    {
        self::addRoute($path, $controller, 'POST');
    }

    /**
    * Add a new route configuration for the PUT method.
    *
    * @param string $path
    * @param string $controller
    */
    public static function put($path, $controller)
    {
        self::addRoute($path, $controller, 'PUT');
    }

    /**
    * Add a new route configuration for the DELETE method.
    *
    * @var string $path
    * @var string $controller
    */
    public static function delete($path, $controller)
    {
        self::addRoute($path, $controller, 'DELETE');
    }

    /**
    * Generate a complete (or partial) set of route collections for a specific
    * end-point base.
    *
    * @param string $base
    * @param string $controller
    * @param string $param
    * @param array $methods [default: ['GET', 'POST', 'PUT', 'DELETE']]
    */
    public static function generateRoutes($base, $controller, $param, $methods = ['GET', 'POST', 'PUT', 'DELETE'])
    {
        if (in_array('GET', $methods))
        {
            if (!in_array('GET-SINGLE', $methods))
            {
                self::addRoute($base, "$controller@index", 'GET');
            }

            if (!in_array('GET-ALL', $methods))
            {
                self::addRoute("$base/{{$param}}", "$controller@read", 'GET');
            }
        }

        if (in_array('POST', $methods))
        {
            self::addRoute($base, "$controller@create", 'POST');
        }

        if (in_array('PUT', $methods))
        {
            self::addRoute("$base/{{$param}}", "$controller@update", 'PUT');
        }

        if (in_array('DELETE', $methods))
        {
            self::addRoute("$base/{{$param}}", "$controller@delete", 'DELETE');
        }
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

            // get route parameters
            $paramMatches = [];
            preg_match('/{(\S+)}/', $match['_route'], $paramMatches);

            // first item is the full match, so remove it
            array_shift($paramMatches);

            foreach ($paramMatches as $param)
            {
                $request->params[$param] = $match[$param];
            }
        }
        catch (ResourceNotFoundException $ex)
        {
            Response::send('page not found', 404);
        }

        if ($match['_controller'])
        {
            // expect _controller to look like 'ClassName@methodName'
            $split = explode('@', $match['_controller']);

            if (count($split) < 2)
            {
                Response::send('invalid controller setup for route "' . $request->path . '"', 400);
            }

            $controller = $split[0];
            $method     = $split[1];
            $className  = 'App\\Controllers\\' . $controller;

            $instance = new $className();
            $instance->$method($request, $params);
        }
    }
}
