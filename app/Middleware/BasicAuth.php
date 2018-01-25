<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;
use App\Databases\Result as DatabaseResult;
use App\Services\AuthenticateUserService;

class BasicAuth implements Middleware
{
    public static function run(Request $request)
    {
        $user = trim($request->getUser());
        $pass = trim($request->getPassword());

        $authorized = AuthenticateUserService::authenticate($user, $pass);

        if (!$authorized)
        {
            Response::send('unauthorized', 401);
        }
    }
}
