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
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $client = new MongoClient('dockable', 'auth_tokens');
        $token = $client->find(['_id' => new ObjectId($auth['_id']['$oid'])]);

        // check for token not found
        if (!$token || $token->err) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        // check expration date
        if (time() >= $token->data[0]->expires_date) {
            return new Response(json_encode($token), Response::HTTP_UNAUTHORIZED);
        }

        return $request;
    }
}
