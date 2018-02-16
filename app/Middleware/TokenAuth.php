<?php

namespace App\Middleware;

use App\Databases\MongoClient;
use App\Middleware\Middleware;
use App\Services\CookieManagerService;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth implements Middleware
{
    public static function run(Request $request)
    {
        $auth = CookieManagerService::get('auth');
        $auth = base64_decode(trim($auth));
        $auth = json_decode($auth, true);

        if (!$auth) {
            $response = new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
            $response->send();
            die();
        }

        $client = new MongoClient('dockable', 'auth_tokens');
        $token = $client->find(['_id' => new ObjectId($auth['id'])]);

        // check for token not found
        if (!$token || $token->err) {
            $response = new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
            $response->send();
            die();
        }

        // check expration date
        if (time() >= $token->data[0]['expires_date']) {
            $response = new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
            $response->send();
            die();
        }

        return $request;
    }
}
