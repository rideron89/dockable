<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;
use App\Databases\Result as DatabaseResult;

class BasicAuth implements Middleware
{
    public static function run(Request $request)
    {
        $user = trim($request->getUser());
        $pass = trim($request->getPassword());

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
