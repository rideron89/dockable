<?php

namespace App\Routing;

use App\Middleware\Middleware;
use App\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
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
    *
    * @return App\Routing\Route
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

        if (!self::$routes[$method]) {
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
    * @return App\Routing\Route
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
    * @return App\Routing\Route
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
    * @return App\Routing\Route
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
    * @return App\Routing\Route
    */
    public static function delete($path, $controller)
    {
        return self::addRoute($path, $controller, 'DELETE');
    }

    /**
    * Attempt to route the currect request.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    *
    * @return Symfony\Component\HttpFoundation\Response
    */
    public static function route(Request $request)
    {
        $context = new RequestContext('/', $request->getMethod());
        $matcher = new UrlMatcher(self::$routes[$request->getMethod()], $context);

        try {
            $match = $matcher->match($request->getPathInfo());
        } catch (ResourceNotFoundException $ex) {
            return new Response('route not found', 404);
        }

        // get route parameters
        $paramMatches = [];
        preg_match('/{(\S+)}/', $match['_route'], $paramMatches);

        // first item is the full match, so remove it
        array_shift($paramMatches);

        foreach ($paramMatches as $param) {
            $request->params[$param] = $match[$param];
        }

        if ($match['_controller']) {
            // expect _controller to look like 'ClassName@methodName'
            $split = explode('@', $match['_controller']);

            if (count($split) < 2) {
                return new Response('invalid controller setup for route "' . $request->getPathInfo() . '"', 501);
            }

            $controller = $split[0];
            $method     = $split[1];
            $className  = 'App\\Controllers\\' . $controller;

            // run the middleware if any is set
            if ($match['middleware']) {
                foreach ($match['middleware'] as $middleware) {
                    $middlewareClassname = 'App\\Middleware\\' . $middleware;

                    $request = $middlewareClassname::run($request);
                }
            }

            $instance = new $className();

            return $instance->$method($request);
        } else {
            return new Response('no controller setup', 501);
        }
    }
}
