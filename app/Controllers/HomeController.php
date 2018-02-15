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

        // redirect to /login page if no credentials are given
        if (!$login) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        // parse the login credentials, stored as username:password
        $parts = explode(':', $login);
        $username = $parts[0];
        $password = $parts[1];

        // redirect to /login page if the credentials are invalid
        if (!$username || !$password) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $userId = AuthenticateUserService::authenticate($username, $password);

        // redirect to the /login page if the credentials are invalid
        if (!$userId) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $response = new Response(Viewer::renderTwig('index.twig'));
        return $response;
    }

    public function clients(Request $request)
    {
        $login = $request->cookies->get('login');

        // redirect to /login page if no credentials are given
        if (!$login) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        // parse the login credentials, stored as username:password
        $parts = explode(':', $login);
        $username = $parts[0];
        $password = $parts[1];

        // redirect to /login page if the credentials are invalid
        if (!$username || !$password) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $userId = AuthenticateUserService::authenticate($username, $password);

        // redirect to the /login page if the credentials are invalid
        if (!$userId) {
            $response = new Response();
            $response->headers->set('Location', '/login');
            return $response;
        }

        $db = new MongoClient('dockable', 'clients');
        $clients = $db->find(['user_id' => $userId['id']]);

        $response = new Response(Viewer::renderTwig('clients.twig', ['clients' => $clients->data]));
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
