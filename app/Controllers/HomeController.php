<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;
use App\Services\AuthenticateUserService;

class HomeController
{
    public function index(Request $request)
    {
        $login = $request->cookies->get('login');

        if (!$login)
        {
            header('Location: /login');
            return;
        }

        $parts = explode(':', $login);
        $username = $parts[0];
        $password = $parts[1];

        if (!$username || !$password)
        {
            header('Location: /login');
            return;
        }

        $authorized = AuthenticateUserService::authenticate($username, $password);

        if (!$authorized)
        {
            header('Location: /login');
            return;
        }

        Response::view('index.html');
    }

    public function login(Request $request)
    {
        Response::view('login.html');
    }
}
