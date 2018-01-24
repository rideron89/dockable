<?php

namespace App\Middleware;

use App\Request;
use App\Response;
use App\Databases\MongoClient;
use App\Databases\Result as DatabaseResult;

class BasicAuth implements Middleware
{
    public static function run(Request $request)
    {
        $user = trim($request->originalRequest['PHP_AUTH_USER']);
        $pass = trim($request->originalRequest['PHP_AUTH_PW']);

        if (!$user || !$pass)
        {
            Response::send('unauthorized', 401);
        }

        $client = new MongoClient('dockable', 'users');
        $results = $client->find(['username' => $user]);

        if (password_verify($pass, $results->documents[0]['password']) === false)
        {
            Response::send('unauthorized', 401);
        }
    }
}
