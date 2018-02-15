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
        $password   = $authorized['hashed_password'];

        if (!$authorized) {
            Response::send('unauthorized', 401);
        }

        // base64 encode the auth so it is not stored in plaintext as a cookie
        $auth = base64_encode("$username:$password");

        CookieManagerService::add('auth', $auth);

        $response = new Response();
        $response->headers->set('Location', '/');
        return $response;
    }

    public function register(Request $request)
    {
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        if (!$username || !$password) {
            return new Response('invalid user data', 400);
        }

        $db = new MongoClient('dockable', 'users');

        if ($db->exists(['username' => $username])) {
            return new Response('account already exists', 409);
        }

        $result = $db->create(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($result->err) {
            return new Response($result->err, 500);
        }

        $response = new Response();
        $response->headers->set('Location', '/login');
        return $response;
    }
}
