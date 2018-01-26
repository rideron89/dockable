<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\ViewResponse;
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
            $response->send();
        }

        $parts = explode(':', $login);
        $username = $parts[0];
        $password = $parts[1];

        if (!$username || !$password)
        {
            $response = new Response();
            $response->headers->set('Location', '/login');
            $response->send();
        }

        $authorized = AuthenticateUserService::authenticate($username, $password);

        if (!$authorized)
        {
            $response = new Response();
            $response->headers->set('Location', '/login');
            $response->send();
        }

        $response = new ViewResponse();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/html');
        $response->send('index.html');
    }

    public function login(Request $request)
    {
        $response = new ViewResponse();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/html');
        $response->send('login.html');
    }
}
