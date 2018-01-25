<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Services\AuthenticateUserService;

class AuthController
{
    public function index(Request $request)
    {
        Response::send('success');
    }

    public function login(Request $request)
    {
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        $authorized = AuthenticateUserService::authenticate($username, $password);

        if (!$authorized)
        {
            Response::send('unauthorized', 401);
        }

        Response::send("Hello, $username!");
    }
}
