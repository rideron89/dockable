<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Services\AuthenticateUserService;
use App\Services\CookieManagerService;

class AuthController
{
    public function login(Request $request)
    {
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        $authorized = AuthenticateUserService::authenticate($username, $password);

        if (!$authorized)
        {
            Response::send('unauthorized', 401);
        }

        CookieManagerService::add('login', "$username:$password");

        Response::send("Hello, $username!");
    }

    public function logout(Request $request)
    {
        CookieManagerService::remove('login');

        Response::send('logged out');
    }
}
