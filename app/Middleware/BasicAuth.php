<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Services\AuthenticateUserService;

class BasicAuth implements Middleware
{
    /**
    * Try to authenticate the user.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    *
    * @return Symfony\Component\HttpFoundation\Request
    */
    public static function run(Request $request) : Request
    {
        $user = trim($request->getUser());
        $pass = trim($request->getPassword());

        $result = AuthenticateUserService::authenticate($user, $pass);

        if (!$result['id'])
        {
            $response = new Response('unauthorized', 401);
            $response->send();
        }

        $request->attributes->set('user_id', $result['id']);

        return $request;
    }
}
