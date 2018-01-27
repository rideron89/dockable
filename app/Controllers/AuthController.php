<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Databases\MongoClient;
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

    public function register(Request $request)
    {
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        if (!$username || !$password)
        {
            return new Response('invalid user data', 400);
        }

        $db = new MongoClient('dockable', 'users');

        if ($db->exists(['username' => $username]))
        {
            return new Response('account already exists', 409);
        }

        $result = $db->create(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($result->err)
        {
            return new Response($result->err, 500);
        }

        $response = new Response();
        $response->headers->set('Location', '/login');
        return $response;
    }

    public function logout(Request $request)
    {
        CookieManagerService::remove('login');

        Response::send('logged out');
    }
}
