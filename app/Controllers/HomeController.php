<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Viewer;
use App\Databases\MongoClient;
use App\Services\AuthenticateUserService;

class HomeController
{
    public function index(Request $request)
    {
        $login = $request->cookies->get('login');

        if (!$login)
        {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $parts = explode(':', $login);
        $username = $parts[0];
        $password = $parts[1];

        if (!$username || !$password)
        {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $authorized = AuthenticateUserService::authenticate($username, $password);

        if (!$authorized)
        {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $response = new Response(Viewer::renderTwig('index.twig'));
        return $response;
    }

    public function login(Request $request)
    {
        $response = new Response(Viewer::renderTwig('login.twig'));
        return $response;
    }

    public function register(Request $request)
    {
        $response = new Response(Viewer::renderTwig('register.twig'));
        return $response;
    }
}
